<?php
namespace App\Providers;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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

        try {
            if (!Schema::hasTable('roles') ||
                !Schema::hasTable('workspaces') ||
                !Schema::hasTable('users') ||
                !Schema::hasTable('departments') ||
                !Schema::hasTable('user_workspace')) { return;}
        
        if(!User::where('email', 'admin@dms.zm')->first()) {
            Log::info("AppServiceProvider: no default user. Creating one...");
            $adminUser = new User();
            $adminUser->email = "admin@dms.zm";
            $adminUser->name = "admin";
            $adminUser->password = Hash::make('password');

            $defaultDept = Department::Create(['name' => 'default']);
            $adminUser->department_id = $defaultDept->id;

            $adminUser->save();

            $adminRole = Role::Create(['name' => 'admin']);
            $adminUser->roles()->attach($adminRole->id);
        }

        // $adminUser = User::firstOrCreate(
        //     ['email' => 'admin@dms.zm'],
        //     [
        //     'name' => 'admin',
        //     'email' => 'admin@dms.zm',
        //     'department_id' => $defaultDept->id,
        //     'password' => Hash::make('password')
        // ]);

        } catch (\Throwable $th) {
            //throw $th;
            Log::error("AppServiceProvider::boot() " . $th->getMessage());
        }
    }
}
