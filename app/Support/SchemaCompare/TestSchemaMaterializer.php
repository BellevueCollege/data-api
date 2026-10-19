<?php

namespace App\Support\SchemaCompare;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\Support\SqliteWarehouseSchema;

/**
 * Builds the same SQLite schema used in tests on a disposable file.
 */
class TestSchemaMaterializer
{
    /** @var array<string, array<string, mixed>> */
    private array $savedConnections = [];

    /**
     * Keep the current connection settings so warehouse reads are not lost after SQLite materialization.
     */
    public function rememberConnections(): void
    {
        $this->savedConnections = [];

        foreach (config('schema-compare.sqlite_connections', []) as $connectionName) {
            $this->savedConnections[$connectionName] = config("database.connections.{$connectionName}");
        }
    }

    public function restoreConnections(): void
    {
        foreach ($this->savedConnections as $connectionName => $connectionConfig) {
            config(["database.connections.{$connectionName}" => $connectionConfig]);
            DB::purge($connectionName);
        }
    }

    /**
     * @return string Absolute path to the temporary SQLite database file.
     */
    public function materialize(): string
    {
        if (! class_exists(SqliteWarehouseSchema::class)) {
            throw new RuntimeException(
                'Tests\\Support\\SqliteWarehouseSchema is not available. Install dev dependencies (composer install).'
            );
        }

        $databasePath = $this->createTempDatabasePath();
        $connectionNames = config('schema-compare.sqlite_connections', []);

        $this->applySqliteToConnections($connectionNames, $databasePath);
        $this->assertSqliteMaterialization($databasePath, $connectionNames);
        $this->purgeConnections($connectionNames);

        Artisan::call('migrate', ['--force' => true]);
        SqliteWarehouseSchema::rebuild();

        return $databasePath;
    }

    public function remove(string $databasePath): void
    {
        if (is_file($databasePath)) {
            unlink($databasePath);
        }
    }

    private function createTempDatabasePath(): string
    {
        $directory = storage_path('framework/schema-compare');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        return $directory.DIRECTORY_SEPARATOR.'materialize-'.uniqid('', true).'.sqlite';
    }

    /**
     * @param  list<string>  $connectionNames
     */
    private function applySqliteToConnections(array $connectionNames, string $databasePath): void
    {
        foreach ($connectionNames as $connectionName) {
            config([
                "database.connections.{$connectionName}.driver" => 'sqlite',
                "database.connections.{$connectionName}.database" => $databasePath,
            ]);
        }
    }

    /**
     * @param  list<string>  $connectionNames
     */
    private function assertSqliteMaterialization(string $databasePath, array $connectionNames): void
    {
        $tempDirectory = realpath(dirname($databasePath));
        $storageFramework = realpath(storage_path('framework'));

        if ($tempDirectory === false) {
            throw new RuntimeException('Could not resolve temp database directory.');
        }

        $allowedRoots = array_filter([
            $storageFramework,
            realpath(sys_get_temp_dir()),
        ]);

        $isUnderAllowedRoot = false;

        foreach ($allowedRoots as $root) {
            if ($root !== false && str_starts_with($tempDirectory, $root)) {
                $isUnderAllowedRoot = true;
                break;
            }
        }

        if (! $isUnderAllowedRoot) {
            throw new RuntimeException(
                'Refusing to materialize test schema: database path must be under storage/framework or the system temp directory.'
            );
        }

        foreach ($connectionNames as $connectionName) {
            $driver = config("database.connections.{$connectionName}.driver");

            if ($driver !== 'sqlite') {
                throw new RuntimeException(
                    "Refusing to materialize test schema: connection [{$connectionName}] driver is [{$driver}], expected sqlite."
                );
            }

            $configuredPath = (string) config("database.connections.{$connectionName}.database");

            if ($configuredPath !== $databasePath) {
                throw new RuntimeException(
                    "Refusing to materialize test schema: connection [{$connectionName}] database path mismatch."
                );
            }
        }
    }

    /**
     * @param  list<string>  $connectionNames
     */
    private function purgeConnections(array $connectionNames): void
    {
        foreach ($connectionNames as $connectionName) {
            DB::purge($connectionName);
        }
    }
}
