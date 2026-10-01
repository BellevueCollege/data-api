<?php

namespace Tests\Unit\Models;

use App\Models\CourseYearQuarter;
use Illuminate\Support\Facades\DB;
use Tests\DatabaseTestCase;
use Tests\Support\WarehouseFixtures;

/**
 * distinct_courses scope returns one row per course identity in a term.
 */
class CourseYearQuarterDistinctCoursesTest extends DatabaseTestCase
{
    public function test_distinct_courses_returns_one_row_per_course_identity(): void
    {
        DB::connection('ods')->table('vw_Class')->insert([
            [
                'YearQuarterID' => WarehouseFixtures::CURRENT_YEAR_QUARTER_ID,
                'STRM' => WarehouseFixtures::CURRENT_STRM,
                'Department' => 'ABE',
                'CourseNumber' => '53',
                'CourseID' => 'ABE 53',
                'Section' => '01',
            ],
            [
                'YearQuarterID' => WarehouseFixtures::CURRENT_YEAR_QUARTER_ID,
                'STRM' => WarehouseFixtures::CURRENT_STRM,
                'Department' => 'ABE',
                'CourseNumber' => '53',
                'CourseID' => 'ABE 53',
                'Section' => '02',
            ],
        ]);

        $rows = CourseYearQuarter::distinctCourses()->get();

        $this->assertCount(1, $rows);
    }
}
