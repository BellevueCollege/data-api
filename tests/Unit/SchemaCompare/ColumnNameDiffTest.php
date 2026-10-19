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

    public function test_ignores_sqlite_only_columns(): void
    {
        $missing = $this->diff->missingOnServer(
            ['id', 'CourseID', 'Description'],
            ['CourseID', 'Description'],
            ['id'],
        );

        $this->assertSame([], $missing);
    }

    public function test_compares_names_without_regard_to_case(): void
    {
        $missing = $this->diff->missingOnServer(
            ['DayID', 'Title'],
            ['dayid', 'title'],
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

    public function test_type_mismatches_skip_missing_columns(): void
    {
        $mismatches = $this->diff->typeMismatches(
            [new WarehouseColumn('Gone', sqlServer: 'nvarchar(50)')],
            [],
        );

        $this->assertSame([], $mismatches);
    }
}
