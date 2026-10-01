<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\ParallelTesting;

/**
 * Forces every application database connection onto one SQLite file and refuses to run
 * when any connection is not SQLite, so tests cannot touch a real warehouse.
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /** @var list<string> */
    private const SQLITE_CONNECTIONS = [
        'default',
        'da',
        'ods',
        'pciforms',
        'evalforms',
        'empdirectory',
        'copilot',
    ];

    protected function setUp(): void
    {
        // Reject a non-SQLite driver before setup opens connections via RefreshDatabase
        // or the parallel-testing database switch.
        if (! $this->withoutBootingFramework()) {
            $this->bootApplicationBeforeDatabaseGuard();
            $this->assertTestDatabaseConnectionsUseSqlite();
            $this->applySqliteDatabasePaths();
            $this->purgeDatabaseConnections();
            ParallelTesting::callSetUpTestCaseCallbacks($this);
        }

        parent::setUp();
    }

    /**
     * Boot the application before the database guard so the guard can read connection config.
     *
     * Parent setup skips creating the application when one already exists.
     */
    protected function bootApplicationBeforeDatabaseGuard(): void
    {
        if ($this->app) {
            return;
        }

        $this->refreshApplication();
    }

    protected function applySqliteDatabasePaths(): void
    {
        $databasePath = $this->testingDatabasePath();

        if (! file_exists($databasePath)) {
            touch($databasePath);
        }

        foreach (self::SQLITE_CONNECTIONS as $connectionName) {
            config([
                "database.connections.{$connectionName}.database" => $databasePath,
            ]);
        }
    }

    protected function testingDatabasePath(): string
    {
        return dirname(__DIR__).'/database/testing.sqlite';
    }

    protected function assertTestDatabaseConnectionsUseSqlite(): void
    {
        foreach (self::SQLITE_CONNECTIONS as $connectionName) {
            $driver = config("database.connections.{$connectionName}.driver");

            if ($driver !== 'sqlite') {
                $this->fail(
                    "Refusing to run tests: connection [{$connectionName}] driver is [{$driver}], expected sqlite."
                );
            }
        }
    }

    protected function purgeDatabaseConnections(): void
    {
        foreach (self::SQLITE_CONNECTIONS as $connectionName) {
            DB::purge($connectionName);
        }
    }
}
