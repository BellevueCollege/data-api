<?php

namespace Tests\Support;

use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

/**
 * Creates API clients and bearer tokens for HTTP feature tests.
 */
trait AuthenticatesApiClients
{
    /** Plaintext password used when creating test API clients. */
    public const string DEFAULT_CLIENT_KEY = '9b1d89ea-02bf-11e7-93ae-92361f002671';

    /**
     * @param  list<string>  $permissions
     */
    protected function createApiClient(array $permissions = []): Client
    {
        $client = new Client();
        $client->forceFill([
            'clientname' => 'Test API Client',
            'clientid' => (string) Str::uuid(),
            'clienturl' => 'https://example.test',
            'password' => Hash::make(self::DEFAULT_CLIENT_KEY),
            'permissions' => $permissions,
        ]);
        $client->save();

        return $client;
    }

    /**
     * @param  list<string>  $permissions
     */
    protected function obtainBearerToken(array $permissions = []): string
    {
        $client = $this->createApiClient($permissions);

        $response = $this->postJson('/api/v1/auth/login', [
            'clientid' => $client->clientid,
            'clientkey' => self::DEFAULT_CLIENT_KEY,
        ]);

        $response->assertOk();

        return (string) $response->json('access_token');
    }

    /**
     * Internal routes are registered only on the configured internal API host.
     */
    protected function internalApiUrl(string $path): string
    {
        $domain = config('dataapi.api_internal_domain');

        return 'https://'.$domain.$path;
    }

    /**
     * @param  list<string>  $permissions
     */
    protected function withBearerToken(array $permissions = []): array
    {
        return [
            'Authorization' => 'Bearer '.$this->obtainBearerToken($permissions),
        ];
    }

    protected function assertEmptyJsonObject(TestResponse $response): void
    {
        $response->assertOk();
        $this->assertSame([], $response->json());
    }
}
