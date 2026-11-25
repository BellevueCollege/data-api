<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Azure\Provider as AzureProvider;
use Tests\TestCase;

/**
 * Lightweight checks that console, web, and Socialite wiring remain registered.
 */
class AppWiringSmokeTest extends TestCase
{
    public function test_named_admin_index_route_is_registered(): void
    {
        $this->assertTrue(Route::has('admin.index'));
    }

    public function test_root_web_route_is_reachable(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_schema_compare_command_is_registered(): void
    {
        $this->assertArrayHasKey('schema:compare', Artisan::all());
    }

    public function test_azure_socialite_driver_resolves_to_provider(): void
    {
        config([
            'services.azure.client_id' => 'test-client-id',
            'services.azure.client_secret' => 'test-client-secret',
            'services.azure.redirect' => 'http://localhost/admin/login/callback',
            'services.azure.tenant' => 'test-tenant',
        ]);

        $driver = Socialite::driver('azure');

        $this->assertInstanceOf(AzureProvider::class, $driver);
    }
}
