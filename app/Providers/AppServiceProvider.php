<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Client;
use App\Models\Permission;

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
        // $permissions = Permission::all();
        // if ($permissions->isNotEmpty()) {
        //     foreach ($permissions as $permission) {
        //         Gate::define($permission->name, function (Clent $client) use ($permission) {
        //             return $client->hasPermission($permission);
        //         });
        //     }
        // }
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
