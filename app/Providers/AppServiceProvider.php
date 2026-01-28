<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use App\Models\Client;
use Illuminate\Support\Facades\URL;
use Dedoc\Scramble\Scramble;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Fix URL generation for subdomain + subfolder deployments
        $subdir = trim(parse_url(config('app.url'), PHP_URL_PATH) ?? '', '/');

        if ($subdir !== '') {
            // Use current request host, or APP_URL for CLI
            URL::useOrigin(
                app()->runningInConsole() 
                    ? preg_replace('#(/.*)?$#', '', config('app.url'))
                    : request()->getSchemeAndHttpHost()
            );

            // Prefix all generated paths with subdirectory
            URL::formatPathUsing(fn($path) => 
                str_starts_with($path = '/' . ltrim($path, '/'), "/$subdir/") || $path === "/$subdir"
                    ? $path 
                    : ($path === '/' ? "/$subdir" : "/$subdir$path")
            );
        }
        // Fix Scramble's auto-generated internal domain servers
        if (class_exists(Scramble::class)) {
            $subdir = trim(parse_url(config('app.url'), PHP_URL_PATH) ?? '', '/');
            $internalDomain = config('dataapi.api_internal_domain');
            
            if ($subdir && $internalDomain) {
                Scramble::afterOpenApiGenerated(function ($openApi) use ($subdir, $internalDomain) {
                    $badUrl  = "https://{$internalDomain}/api";
                    $goodUrl = "https://{$internalDomain}/{$subdir}/api";
                    
                    foreach ($openApi->paths as $pathItem) {
                        if (isset($pathItem->servers)) {
                            foreach ($pathItem->servers as $server) {
                                if ($server->url === $badUrl) {
                                    $server->url = $goodUrl;
                                }
                            }
                        }
                    }
                    return $openApi;
                });
            }
        }

        /**
         * Define Gates for each Permission
         */
        foreach (config('permissions') as $permissionName => $permissionDescription) {
            Gate::define($permissionName, function (Client $client) use ($permissionName) {
                return $client->hasPermission($permissionName);
            });
        }

        /**
         * Set up Azure AD Socialite
         */
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('azure', \SocialiteProviders\Azure\Provider::class);
        });
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
