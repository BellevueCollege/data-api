<?php

namespace Tests\Unit;

use Tests\TestCase;

class HealthControllerTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetHealth()
    {
        $response = $this->get('/api/v1/health')
            ->assertJson(['status' => 'ok']);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
