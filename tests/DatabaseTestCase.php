<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\SqliteWarehouseSchema;

/**
 * Tests that need Laravel migrations plus warehouse-shaped tables on the SQLite connections.
 */
abstract class DatabaseTestCase extends TestCase
{
    use RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        // RefreshDatabase only migrates the default connection; warehouse views are not migrations.
        SqliteWarehouseSchema::rebuild();
    }
}
