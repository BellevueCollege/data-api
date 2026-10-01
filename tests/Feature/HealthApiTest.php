<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\ApiFeatureTestCase;

/**
 * GET /api/v1/health reflects whether the ODS connection is reachable.
 */
class HealthApiTest extends ApiFeatureTestCase
{
    public function test_returns_ok_when_ods_connection_is_available(): void
    {
        $this->get('/api/v1/health')
            ->assertOk()
            ->assertJson(['status' => 'ok']);
    }

    public function test_returns_503_when_ods_probe_fails(): void
    {
        $connection = \Mockery::mock();
        $connection->shouldReceive('select')
            ->with('select 1')
            ->andThrow(new \RuntimeException('database unavailable'));

        DB::shouldReceive('connection')
            ->with('ods')
            ->andReturn($connection);

        $this->get('/api/v1/health')
            ->assertStatus(503)
            ->assertJson(['status' => 'error']);
    }
}
