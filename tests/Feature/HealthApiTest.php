<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\ApiFeatureTestCase;

/**
 * GET /api/v1/health reflects whether the ODS connection is reachable.
 */
class HealthApiTest extends ApiFeatureTestCase
{
    public function test_returns_ok_when_ods_connection_is_available(): void
    {
        $response = $this->get('/api/v1/health')
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);

        $this->assertNotNull($response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function test_returns_503_when_ods_probe_fails(): void
    {
        Log::spy();

        $connection = \Mockery::mock();
        $connection->shouldReceive('select')
            ->with('select 1')
            ->andThrow(new \RuntimeException('database unavailable'));

        DB::shouldReceive('connection')
            ->with('ods')
            ->andReturn($connection);

        $response = $this->get('/api/v1/health')
            ->assertStatus(503)
            ->assertExactJson(['status' => 'error']);

        $this->assertNotNull($response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
        $this->assertStringNotContainsString('database unavailable', $response->getContent());

        Log::shouldHaveReceived('warning')
            ->once()
            ->with(
                'Health check database probe failed',
                [
                    'connection' => 'ods',
                    'message' => 'database unavailable',
                ]
            );
    }

    public function test_health_route_uses_probe_throttle_instead_of_api_group_throttle(): void
    {
        $route = app('router')->getRoutes()->match(
            Request::create('/api/v1/health', 'GET')
        );

        $this->assertContains('throttle:600,1', $route->gatherMiddleware());
        $this->assertContains('throttle:180,1', $route->excludedMiddleware());
    }
}
