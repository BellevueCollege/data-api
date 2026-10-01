<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

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
        parent::setUp();

        $this->applySqliteDatabasePaths();
        $this->assertTestDatabaseConnectionsUseSqlite();
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
        if (! app()->environment('testing')) {
            return;
        }

        foreach (self::SQLITE_CONNECTIONS as $connectionName) {
            $driver = config("database.connections.{$connectionName}.driver");

            if ($driver !== 'sqlite') {
                $this->fail(
                    "Refusing to run tests: connection [{$connectionName}] driver is [{$driver}], expected sqlite."
                );
            }
        }
    }
}
