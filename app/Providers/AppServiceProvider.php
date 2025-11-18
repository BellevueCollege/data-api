<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Define Gates for each Permission
         */
        foreach (config('permissions') as $permissionName => $permissionDescription) {
            Gate::define($permissionName, function (Client $client) use ($permissionName) {
                return $client->hasPermission($permissionName);
            });
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
