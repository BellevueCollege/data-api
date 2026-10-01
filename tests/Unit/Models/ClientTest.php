<?php

namespace Tests\Unit\Models;

use App\Models\Client;
use Tests\TestCase;

/**
 * API client permission checks and JWT identifier contract.
 */
class ClientTest extends TestCase
{
    public function test_has_permission_returns_true_when_permission_is_present(): void
    {
        $client = new Client();
        $client->forceFill(['permissions' => ['read_student_data']]);

        $this->assertTrue($client->hasPermission('read_student_data'));
    }

    public function test_has_permission_returns_false_when_permission_is_missing(): void
    {
        $client = new Client();
        $client->forceFill(['permissions' => ['read_student_data']]);

        $this->assertFalse($client->hasPermission('read_employee_data'));
    }

    public function test_has_permission_returns_false_when_permissions_is_null(): void
    {
        $client = new Client();
        $client->forceFill(['permissions' => null]);

        $this->assertFalse($client->hasPermission('read_student_data'));
    }

    public function test_get_jwt_identifier_returns_primary_key(): void
    {
        $client = new Client();
        $client->setAttribute('id', 42);

        $this->assertSame(42, $client->getJWTIdentifier());
    }

    public function test_get_jwt_custom_claims_returns_empty_array(): void
    {
        $client = new Client();

        $this->assertSame([], $client->getJWTCustomClaims());
    }
}
