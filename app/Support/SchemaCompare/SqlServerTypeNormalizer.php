<?php

namespace App\Support\SchemaCompare;

/**
 * Normalizes SQL Server type strings for comparison, including nvarchar byte→char length.
 */
class SqlServerTypeNormalizer
{
    /**
     * Canonical lowercase form used for equality checks, e.g. nvarchar(50), decimal(8,2).
     */
    public function canonicalize(string $type, bool $lengthIsBytesForNationalStrings = false): string
    {
        $parsed = $this->parse($type);

        if ($parsed['max']) {
            return $parsed['base'].'(max)';
        }

        if (in_array($parsed['base'], ['decimal', 'numeric'], true) && $parsed['precision'] !== null) {
            $scale = $parsed['scale'] ?? 0;

            return $parsed['base'].'('.$parsed['precision'].','.$scale.')';
        }

        if (in_array($parsed['base'], ['nvarchar', 'nchar'], true) && $parsed['length'] !== null) {
            $length = $lengthIsBytesForNationalStrings
                ? intdiv($parsed['length'], 2)
                : $parsed['length'];

            return $parsed['base'].'('.$length.')';
        }

        if (in_array($parsed['base'], ['varchar', 'char', 'varbinary', 'binary'], true) && $parsed['length'] !== null) {
            return $parsed['base'].'('.$parsed['length'].')';
        }

        if (in_array($parsed['base'], ['datetime2', 'datetimeoffset', 'time', 'float'], true) && $parsed['precision'] !== null) {
            return $parsed['base'].'('.$parsed['precision'].')';
        }

        return $parsed['base'];
    }

    public function equals(string $expected, string $actualFromLaravelGetColumns): bool
    {
        $expectedCanonical = $this->canonicalize($expected, lengthIsBytesForNationalStrings: false);
        $actualCanonical = $this->canonicalize($actualFromLaravelGetColumns, lengthIsBytesForNationalStrings: true);

        return $expectedCanonical === $actualCanonical;
    }

    /**
     * Format a Laravel getColumns() type for display after byte→char normalization.
     */
    public function displayWarehouseType(string $typeFromLaravelGetColumns): string
    {
        return $this->canonicalize($typeFromLaravelGetColumns, lengthIsBytesForNationalStrings: true);
    }

    /**
     * @return array{base: string, length: int|null, precision: int|null, scale: int|null, max: bool}
     */
    public function parse(string $type): array
    {
        $normalized = strtolower(preg_replace('/\s+/', '', $type) ?? $type);

        if (preg_match('/^([a-z0-9]+)\(max\)$/', $normalized, $matches) === 1) {
            return [
                'base' => $matches[1],
                'length' => null,
                'precision' => null,
                'scale' => null,
                'max' => true,
            ];
        }

        if (preg_match('/^([a-z0-9]+)\((\d+),(\d+)\)$/', $normalized, $matches) === 1) {
            return [
                'base' => $matches[1],
                'length' => null,
                'precision' => (int) $matches[2],
                'scale' => (int) $matches[3],
                'max' => false,
            ];
        }

        if (preg_match('/^([a-z0-9]+)\((\d+)\)$/', $normalized, $matches) === 1) {
            $value = (int) $matches[2];

            return [
                'base' => $matches[1],
                'length' => $value,
                'precision' => $value,
                'scale' => null,
                'max' => false,
            ];
        }

        if (preg_match('/^([a-z0-9]+)$/', $normalized, $matches) === 1) {
            return [
                'base' => $matches[1],
                'length' => null,
                'precision' => null,
                'scale' => null,
                'max' => false,
            ];
        }

        return [
            'base' => $normalized,
            'length' => null,
            'precision' => null,
            'scale' => null,
            'max' => false,
        ];
    }
}
