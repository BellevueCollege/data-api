<?php

namespace App\Console\Commands;

use App\Support\SchemaCompare\ColumnNameDiff;
use App\Support\SchemaCompare\TestSchemaMaterializer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\Support\SqliteWarehouseSchema;
use Tests\Support\WarehouseColumn;

/**
 * Dev-only check: test warehouse columns (names + SQL Server types/lengths) still match the warehouse.
 *
 * Requires SCHEMA_COMPARE_ALLOWED_HOSTS and APP_ENV=local. Does not dump full warehouse schemas.
 */
class SchemaCompareCommand extends Command
{
    protected $signature = 'schema:compare
                            {--connection=* : Limit check to one or more connections}';

    protected $description = 'Verify test-schema columns and SQL Server types still match the warehouse (dev only)';

    public function handle(
        TestSchemaMaterializer $materializer,
        ColumnNameDiff $columnNameDiff,
    ): int {
        $this->assertCanRun();

        $connections = $this->resolveConnections();
        $this->line('Checking hosts: '.implode(', ', $this->hostsFor($connections)));

        $materializer->rememberConnections();

        $failures = [];

        try {
            $failures = array_merge(
                $failures,
                $this->compareWarehouseColumns($columnNameDiff, $connections)
            );

            if (in_array('da', $connections, true)) {
                $failures = array_merge(
                    $failures,
                    $this->compareDaColumnNames($materializer, $columnNameDiff)
                );
            }
        } finally {
            $materializer->restoreConnections();
        }

        return $this->reportFailures($failures);
    }

    /**
     * @param  list<string>  $connections
     * @return list<array{connection: string, object: string, missing: list<string>, type_drift: list<array{column: string, expected: string, actual: string}>}>
     */
    private function compareWarehouseColumns(ColumnNameDiff $columnNameDiff, array $connections): array
    {
        $expectedByConnection = SqliteWarehouseSchema::expectedWarehouseColumns();
        $failures = [];

        foreach ($connections as $connection) {
            if ($connection === 'da' || ! isset($expectedByConnection[$connection])) {
                continue;
            }

            foreach ($expectedByConnection[$connection] as $object => $expectedColumns) {
                $serverColumns = Schema::connection($connection)->getColumns($object);
                $serverNames = collect($serverColumns)
                    ->pluck('name')
                    ->map(fn (mixed $name): string => (string) $name)
                    ->all();
                $serverTypesByName = collect($serverColumns)
                    ->mapWithKeys(fn (array $column): array => [
                        (string) $column['name'] => (string) $column['type'],
                    ])
                    ->all();

                $expectedNames = array_map(
                    fn (WarehouseColumn $column): string => $column->name,
                    $expectedColumns
                );

                $missing = $columnNameDiff->missingOnServer($expectedNames, $serverNames);
                $typeDrift = $columnNameDiff->typeMismatches($expectedColumns, $serverTypesByName);

                if ($missing === [] && $typeDrift === []) {
                    continue;
                }

                $failures[] = [
                    'connection' => $connection,
                    'object' => $object,
                    'missing' => $missing,
                    'type_drift' => $typeDrift,
                ];
            }
        }

        return $failures;
    }

    /**
     * @return list<array{connection: string, object: string, missing: list<string>, type_drift: list<array{column: string, expected: string, actual: string}>}>
     */
    private function compareDaColumnNames(
        TestSchemaMaterializer $materializer,
        ColumnNameDiff $columnNameDiff,
    ): array {
        $objects = config('schema-compare.da_objects', []);
        $serverNamesByObject = [];

        foreach ($objects as $object) {
            $serverNamesByObject[$object] = collect(Schema::connection('da')->getColumns($object))
                ->pluck('name')
                ->map(fn (mixed $name): string => (string) $name)
                ->all();
        }

        $databasePath = null;
        $failures = [];

        try {
            $databasePath = $materializer->materialize();

            foreach ($objects as $object) {
                $testNames = collect(Schema::connection('da')->getColumns($object))
                    ->pluck('name')
                    ->map(fn (mixed $name): string => (string) $name)
                    ->all();

                $missing = $columnNameDiff->missingOnServer($testNames, $serverNamesByObject[$object] ?? []);

                if ($missing === []) {
                    continue;
                }

                $failures[] = [
                    'connection' => 'da',
                    'object' => $object,
                    'missing' => $missing,
                    'type_drift' => [],
                ];
            }
        } finally {
            if ($databasePath !== null) {
                $materializer->remove($databasePath);
            }
        }

        return $failures;
    }

    /**
     * @param  list<array{connection: string, object: string, missing: list<string>, type_drift: list<array{column: string, expected: string, actual: string}>}>  $failures
     */
    private function reportFailures(array $failures): int
    {
        if ($failures === []) {
            $this->info('All annotated warehouse columns match SQL Server (names and types).');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->error('schema:compare failed.');
        $this->line('Test schema annotations disagree with SQL Server for allowlisted objects.');
        $this->line('Extra warehouse columns are ignored.');
        $this->newLine();

        foreach ($failures as $failure) {
            $this->line('  Connection: '.$failure['connection']);
            $this->line('  Object:     '.$failure['object']);

            if ($failure['missing'] !== []) {
                $this->line('  Missing:   '.implode(', ', $failure['missing']));
            } else {
                $this->line('  Missing:   (none)');
            }

            if ($failure['type_drift'] !== []) {
                $this->line('  Type drift:');

                foreach ($failure['type_drift'] as $drift) {
                    $this->line(sprintf(
                        '    %s: expected %s, warehouse has %s',
                        $drift['column'],
                        $drift['expected'],
                        $drift['actual']
                    ));
                }
            }

            $this->newLine();
        }

        $this->line('What to do:');
        $this->line('  - Update sqlServer: on WarehouseColumn in tests/Support/SqliteWarehouseSchema.php');
        $this->line('    (that also updates the SQLite declared length/precision on rebuild).');
        $this->line('  - Or remove obsolete columns from the test schema / fixtures.');
        $this->line('  - Or restore the column/type on the warehouse view.');
        $this->line('  - SQLite-only columns (sqliteOnly: true) are not checked against SQL Server.');

        return self::FAILURE;
    }

    private function assertCanRun(): void
    {
        if (! app()->environment('local')) {
            throw new RuntimeException('schema:compare is only available when APP_ENV=local.');
        }

        if ($this->isContinuousIntegration()) {
            throw new RuntimeException('schema:compare is not allowed in CI.');
        }

        if (! class_exists(SqliteWarehouseSchema::class)) {
            throw new RuntimeException(
                'Tests\\Support\\SqliteWarehouseSchema is not available. Install dev dependencies (composer install).'
            );
        }

        $allowedHosts = config('schema-compare.allowed_hosts', []);

        if ($allowedHosts === []) {
            throw new RuntimeException(
                'Set SCHEMA_COMPARE_ALLOWED_HOSTS to a non-empty comma-separated host list before running schema:compare.'
            );
        }

        foreach ($this->resolveConnections() as $connectionName) {
            $host = (string) config("database.connections.{$connectionName}.host");

            if ($host === '') {
                throw new RuntimeException("Connection [{$connectionName}] has no host configured.");
            }

            if (! in_array($host, $allowedHosts, true)) {
                throw new RuntimeException(
                    "Connection [{$connectionName}] host [{$host}] is not in SCHEMA_COMPARE_ALLOWED_HOSTS."
                );
            }
        }
    }

    private function isContinuousIntegration(): bool
    {
        foreach (['CI', 'TF_BUILD', 'GITHUB_ACTIONS', 'BUILD_BUILDID'] as $variable) {
            $value = getenv($variable);

            if ($value !== false && $value !== '' && $value !== 'false' && $value !== '0') {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    private function resolveConnections(): array
    {
        $allowed = config('schema-compare.compare_connections', []);
        $requested = $this->option('connection');

        if ($requested === [] || $requested === null) {
            return $allowed;
        }

        foreach ($requested as $connection) {
            if (! in_array($connection, $allowed, true)) {
                $this->fail("Connection [{$connection}] is not in the schema compare allowlist.");
            }
        }

        return array_values($requested);
    }

    /**
     * @param  list<string>  $connections
     * @return list<string>
     */
    private function hostsFor(array $connections): array
    {
        return collect($connections)
            ->map(fn (string $name): string => (string) config("database.connections.{$name}.host"))
            ->unique()
            ->values()
            ->all();
    }
}
