<?php

namespace Tests;

use Tests\Support\AuthenticatesApiClients;

/**
 * HTTP API tests with API clients, warehouse fixtures, and a fixed clock for quarter windows.
 */
abstract class ApiFeatureTestCase extends DatabaseTestCase
{
    use AuthenticatesApiClients;

    protected function setUp(): void
    {
        parent::setUp();

        // Aligns with WarehouseFixtures registration and class-day dates.
        $this->travelTo('2024-03-15 12:00:00');
    }
}
