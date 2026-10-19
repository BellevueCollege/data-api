<?php

namespace Tests\Support;

use Illuminate\Database\Schema\Blueprint;
use InvalidArgumentException;
use RuntimeException;

/**
 * Warehouse column used in SQLite tests, annotated with the expected SQL Server type.
 *
 * The sqlServer definition drives both schema:compare and the SQLite Blueprint length/precision.
 */
final readonly class WarehouseColumn
{
    public function __construct(
        public string $name,
        public ?string $sqlServer = null,
        public bool $nullable = true,
        public bool $primary = false,
        public bool $sqliteOnly = false,
    ) {
        if (! $this->sqliteOnly && ($this->sqlServer === null || $this->sqlServer === '')) {
            throw new InvalidArgumentException(
                "Warehouse column [{$this->name}] requires a sqlServer type unless sqliteOnly is true."
            );
        }
    }

    /**
     * Add this column to a SQLite blueprint, deriving length/precision from sqlServer when present.
     */
    public function applyTo(Blueprint $table): void
    {
        if ($this->sqliteOnly) {
            $table->increments($this->name);

            return;
        }

        $parsed = $this->parseSqlServerType((string) $this->sqlServer);
        $column = match ($parsed['base']) {
            'nvarchar', 'varchar', 'nchar', 'char' => $this->applyStringColumn($table, $parsed),
            'text', 'ntext' => $table->text($this->name),
            'decimal', 'numeric' => $table->decimal(
                $this->name,
                $parsed['precision'] ?? 8,
                $parsed['scale'] ?? 2
            ),
            'bit' => $table->boolean($this->name),
            'date' => $table->date($this->name),
            'datetime', 'datetime2', 'smalldatetime', 'datetimeoffset' => $table->dateTime($this->name),
            'time' => $table->time($this->name),
            'int', 'integer' => $table->integer($this->name),
            'bigint' => $table->bigInteger($this->name),
            'smallint' => $table->smallInteger($this->name),
            'tinyint' => $table->tinyInteger($this->name),
            'float', 'real' => $table->float($this->name),
            default => throw new RuntimeException(
                "Unsupported sqlServer type [{$this->sqlServer}] for column [{$this->name}]."
            ),
        };

        if ($this->primary) {
            $column->primary();
        } elseif ($this->nullable) {
            $column->nullable();
        }
    }

    /**
     * @param  array{base: string, length: int|null, precision: int|null, scale: int|null, max: bool}  $parsed
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    private function applyStringColumn(Blueprint $table, array $parsed)
    {
        if ($parsed['max']) {
            return $table->text($this->name);
        }

        return $table->string($this->name, $parsed['length'] ?? 255);
    }

    /**
     * @return array{base: string, length: int|null, precision: int|null, scale: int|null, max: bool}
     */
    private function parseSqlServerType(string $type): array
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
            return [
                'base' => $matches[1],
                'length' => (int) $matches[2],
                'precision' => (int) $matches[2],
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

        throw new InvalidArgumentException("Cannot parse sqlServer type [{$type}].");
    }
}
