<?php

namespace App\Providers;

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
        // Define dynamic gates based on static $rolePermissions map
        foreach (\App\Models\User::$rolePermissions as $role => $permissions) {
            foreach ($permissions as $permission) {
                \Illuminate\Support\Facades\Gate::define($permission, function (\App\Models\User $user) use ($permission) {
                    return $user->hasPermission($permission);
                });
            }
        }

        // Global bypass for super_admin
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });
    }
}
