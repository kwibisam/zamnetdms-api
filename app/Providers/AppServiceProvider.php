<?php
namespace App\Providers;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
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
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/reset-password?token=$token&email={$notifiable->getEmailForPasswordReset()}";
        });

        VerifyEmail::createUrlUsing(function (object $notifiable) {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return config('app.frontend_url')."/verify-email"."?url=".$verificationUrl;
        });


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
        } catch (\Throwable $th) {
            //throw $th;
            Log::error("AppServiceProvider::boot() " . $th->getMessage());
        }
    }
}
