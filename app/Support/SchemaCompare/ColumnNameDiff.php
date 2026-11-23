<?php

namespace App\Support\SchemaCompare;

use Tests\Support\WarehouseColumn;

/**
 * Compares expected warehouse columns (names + SQL Server types) to live catalog metadata.
 */
class ColumnNameDiff
{
    public function __construct(
        private readonly SqlServerTypeNormalizer $typeNormalizer = new SqlServerTypeNormalizer(),
    ) {}

    /**
     * @param  list<string>  $testColumns
     * @param  list<string>  $serverColumns
     * @param  list<string>  $ignoredTestColumns
     * @return list<string>
     */
    public function missingOnServer(
        array $testColumns,
        array $serverColumns,
        array $ignoredTestColumns = [],
    ): array {
        $serverLower = collect($serverColumns)
            ->map(fn (string $name): string => strtolower($name))
            ->all();

        $ignoredLower = collect($ignoredTestColumns)
            ->map(fn (string $name): string => strtolower($name))
            ->all();

        return collect($testColumns)
            ->reject(fn (string $name): bool => in_array(strtolower($name), $ignoredLower, true))
            ->reject(fn (string $name): bool => in_array(strtolower($name), $serverLower, true))
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @param  list<WarehouseColumn>  $expectedColumns
     * @param  array<string, string>  $serverTypesByName  column name => Laravel getColumns() type
     * @return list<array{column: string, expected: string, actual: string}>
     */
    public function typeMismatches(array $expectedColumns, array $serverTypesByName): array
    {
        $serverByLower = [];

        foreach ($serverTypesByName as $name => $type) {
            $serverByLower[strtolower($name)] = [
                'name' => $name,
                'type' => $type,
            ];
        }

        $mismatches = [];

        foreach ($expectedColumns as $expectedColumn) {
            if ($expectedColumn->sqliteOnly || $expectedColumn->sqlServer === null) {
                continue;
            }

            $key = strtolower($expectedColumn->name);

            if (! array_key_exists($key, $serverByLower)) {
                continue;
            }

            $actualType = $serverByLower[$key]['type'];

            if ($this->typeNormalizer->equals($expectedColumn->sqlServer, $actualType)) {
                continue;
            }

            $mismatches[] = [
                'column' => $expectedColumn->name,
                'expected' => $this->typeNormalizer->canonicalize($expectedColumn->sqlServer),
                'actual' => $this->typeNormalizer->displayWarehouseType($actualType),
            ];
        }

        usort(
            $mismatches,
            fn (array $left, array $right): int => strcasecmp($left['column'], $right['column'])
        );

        return $mismatches;
    }
}
