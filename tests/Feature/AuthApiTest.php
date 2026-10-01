<?php

namespace Tests\Feature;

use Tests\ApiFeatureTestCase;

/**
 * Client-credential login on public and internal hosts.
 *
 * Internal login is served only on the internal API domain; the same path on the public host returns 404.
 */
class AuthApiTest extends ApiFeatureTestCase
{
    public function test_public_login_returns_token_for_valid_client(): void
    {
        $client = $this->createApiClient();

        $this->postJson('/api/v1/auth/login', [
            'clientid' => $client->clientid,
            'clientkey' => self::DEFAULT_CLIENT_KEY,
        ])
            ->assertOk()
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }

    public function test_public_login_returns_401_for_invalid_client_key(): void
    {
        $client = $this->createApiClient();

        $this->postJson('/api/v1/auth/login', [
            'clientid' => $client->clientid,
            'clientkey' => 'not-the-right-key',
        ])
            ->assertUnauthorized();
    }

    public function test_public_login_returns_422_when_credentials_are_missing(): void
    {
        $this->postJson('/api/v1/auth/login', [])
            ->assertStatus(422);
    }

    public function test_internal_login_returns_token_on_internal_domain(): void
    {
        $client = $this->createApiClient();

        $this->postJson($this->internalApiUrl('/api/v1/internal/auth/login'), [
            'clientid' => $client->clientid,
            'clientkey' => self::DEFAULT_CLIENT_KEY,
        ])
            ->assertOk()
            ->assertJsonStructure(['access_token']);
    }

    public function test_internal_login_is_not_available_on_public_host(): void
    {
        $client = $this->createApiClient();

        $this->postJson('/api/v1/internal/auth/login', [
            'clientid' => $client->clientid,
            'clientkey' => self::DEFAULT_CLIENT_KEY,
        ])
            ->assertNotFound();
    }
}
