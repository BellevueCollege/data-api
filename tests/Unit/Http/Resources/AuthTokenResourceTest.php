<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\AuthTokenResource;
use Tests\Support\ResourceTestCase;

/**
 * JSON shape of AuthTokenResource matches the login API contract.
 */
class AuthTokenResourceTest extends ResourceTestCase
{
    public function test_serializes_auth_token_contract(): void
    {
        $this->assertResourceContract(new AuthTokenResource([
            'access_token' => 'test-access-token',
            'expires_in' => 3600,
        ]), [
            'access_token' => 'test-access-token',
            'token_type' => 'bearer',
            'expires_in' => 3600,
        ]);
    }
}
