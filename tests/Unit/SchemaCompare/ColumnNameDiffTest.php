<?php

namespace Tests\Unit\SchemaCompare;

use App\Support\SchemaCompare\ColumnNameDiff;
use PHPUnit\Framework\TestCase;
use Tests\Support\WarehouseColumn;

class ColumnNameDiffTest extends TestCase
{
    private ColumnNameDiff $diff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->diff = new ColumnNameDiff();
    }

    public function test_reports_test_columns_missing_on_the_server(): void
    {
        $missing = $this->diff->missingOnServer(
            ['PSCourseID', 'NewWarehouseColumn', 'CourseID'],
            ['PSCourseID', 'CourseID'],
        );

        $this->assertSame(['NewWarehouseColumn'], $missing);
    }

    public function test_sorts_missing_columns_alphabetically(): void
    {
        $missing = $this->diff->missingOnServer(
            ['Zed', 'Able'],
            ['Other'],
        );

        $this->assertSame(['Able', 'Zed'], $missing);
    }

    public function test_ignores_sqlite_only_columns(): void
    {
        $missing = $this->diff->missingOnServer(
            ['id', 'CourseID', 'Description'],
            ['CourseID', 'Description'],
            ['id'],
        );

        $this->assertSame([], $missing);
    }

    public function test_ignores_sqlite_only_columns_case_insensitively(): void
    {
        $missingFromUpperIgnored = $this->diff->missingOnServer(
            ['id', 'CourseID'],
            ['CourseID'],
            ['ID'],
        );
        $missingFromUpperTestColumn = $this->diff->missingOnServer(
            ['ID', 'CourseID'],
            ['CourseID'],
            ['id'],
        );

        $this->assertSame([], $missingFromUpperIgnored);
        $this->assertSame([], $missingFromUpperTestColumn);
    }

    public function test_compares_names_without_regard_to_case(): void
    {
        $missing = $this->diff->missingOnServer(
            ['DayID', 'Title'],
            ['dayid', 'title'],
        );

        $this->assertSame([], $missing);
    }

    public function test_matches_mixed_case_server_column_names(): void
    {
        $missing = $this->diff->missingOnServer(
            ['dayid', 'title'],
            ['DayID', 'Title'],
        );

        $this->assertSame([], $missing);
    }

    public function test_reports_type_and_length_mismatches(): void
    {
        $mismatches = $this->diff->typeMismatches(
            [
                new WarehouseColumn('SUBJECT', sqlServer: 'nvarchar(255)'),
                new WarehouseColumn('Credits', sqlServer: 'decimal(8,2)'),
            ],
            [
                'SUBJECT' => 'nvarchar(60)',
                'Credits' => 'decimal(8,2)',
            ],
        );

        $this->assertCount(1, $mismatches);
        $this->assertSame('SUBJECT', $mismatches[0]['column']);
        $this->assertSame('nvarchar(255)', $mismatches[0]['expected']);
        $this->assertSame('nvarchar(30)', $mismatches[0]['actual']);
    }

    public function test_type_mismatches_omit_matching_columns(): void
    {
        $mismatches = $this->diff->typeMismatches(
            [
                new WarehouseColumn('SUBJECT', sqlServer: 'nvarchar(30)'),
                new WarehouseColumn('Credits', sqlServer: 'decimal(8,2)'),
            ],
            [
                'SUBJECT' => 'nvarchar(60)',
                'Credits' => 'decimal(8,2)',
            ],
        );

        $this->assertSame([], $mismatches);
    }

    public function test_reports_multiple_type_mismatches_sorted_by_column_name(): void
    {
        $mismatches = $this->diff->typeMismatches(
            [
                new WarehouseColumn('Zebra', sqlServer: 'varchar(10)'),
                new WarehouseColumn('Alpha', sqlServer: 'varchar(10)'),
            ],
            [
                'Zebra' => 'varchar(99)',
                'Alpha' => 'varchar(99)',
            ],
        );

        $this->assertCount(2, $mismatches);
        $this->assertSame(['Alpha', 'Zebra'], array_column($mismatches, 'column'));
        $this->assertSame('varchar(10)', $mismatches[0]['expected']);
        $this->assertSame('varchar(99)', $mismatches[0]['actual']);
    }

    public function test_type_mismatches_skip_missing_columns(): void
    {
        $mismatches = $this->diff->typeMismatches(
            [new WarehouseColumn('Gone', sqlServer: 'nvarchar(50)')],
            [],
        );

        $this->assertSame([], $mismatches);
    }

    public function test_type_mismatches_skip_sqlite_only_columns(): void
    {
        $mismatches = $this->diff->typeMismatches(
            [new WarehouseColumn('id', sqlServer: 'nvarchar(10)', sqliteOnly: true)],
            ['id' => 'nvarchar(99)'],
        );

        $this->assertSame([], $mismatches);
    }

    public function test_keeps_checking_after_a_sqlite_only_column(): void
    {
        $mismatches = $this->diff->typeMismatches(
            [
                new WarehouseColumn('id', sqliteOnly: true),
                new WarehouseColumn('Zebra', sqlServer: 'varchar(10)'),
            ],
            [
                'Zebra' => 'varchar(99)',
            ],
        );

        $this->assertCount(1, $mismatches);
        $this->assertSame('Zebra', $mismatches[0]['column']);
    }

    public function test_keeps_checking_after_a_missing_server_column(): void
    {
        $mismatches = $this->diff->typeMismatches(
            [
                new WarehouseColumn('Gone', sqlServer: 'varchar(10)'),
                new WarehouseColumn('Zebra', sqlServer: 'varchar(10)'),
            ],
            [
                'Zebra' => 'varchar(99)',
            ],
        );

        $this->assertCount(1, $mismatches);
        $this->assertSame('Zebra', $mismatches[0]['column']);
    }

    public function test_keeps_checking_after_a_matching_column(): void
    {
        $mismatches = $this->diff->typeMismatches(
            [
                new WarehouseColumn('Alpha', sqlServer: 'varchar(10)'),
                new WarehouseColumn('Zebra', sqlServer: 'varchar(10)'),
            ],
            [
                'Alpha' => 'varchar(10)',
                'Zebra' => 'varchar(99)',
            ],
        );

        $this->assertCount(1, $mismatches);
        $this->assertSame('Zebra', $mismatches[0]['column']);
    }
}
