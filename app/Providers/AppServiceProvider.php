<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkSpace;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       
        
        $adminRole = Role::firstOrCreate([
            'name' => 'admin'
        ]);

        $defaultWorkSpace = WorkSpace::firstOrCreate([
            'name' => 'default'
        ]);

        $adminUser = User::updateOrCreate(
            ['email' => 'admin@dms.zm'],
            [
            'name' => 'admin',
            'email' => 'admin@dms.zm',
            'password' => Hash::make('password')
        ]);

        if(!$adminUser->roles()->where('name', 'admin')->exists()) {
            $adminUser->roles()->attach($adminRole->id);
        }

        if(!$adminUser->workspaces()->where('name', 'default')->exists()) {
            $adminUser->workspaces()->attach($defaultWorkSpace->id);
        }

        try {
            $user_id = $adminUser->id;
            $workspace_id = $defaultWorkSpace->id;
            DB::beginTransaction();
    
            DB::table('user_workspace')
                ->where('user_id', $user_id)
                ->update(['is_default' => false]);
    
            DB::table('user_workspace')
                ->where('user_id', $user_id)
                ->where('workspace_id', $workspace_id)
                ->update(['is_default' => true]);
            DB::commit();    
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("AppServiceProvider:: " . $th->getMessage() . "on line " . $th->getLine());
        }
    }
}
