<?php

namespace Tests\Unit\Models;

use App\Models\Course;
use Tests\DatabaseTestCase;
use Tests\Support\WarehouseFixtures;

/**
 * Course query scopes for the active catalog and transfer-in exclusion.
 */
class CourseTest extends DatabaseTestCase
{
    public function test_active_returns_no_rows_when_no_current_year_quarter_exists(): void
    {
        $this->travelTo('2024-03-15');

        WarehouseFixtures::seedCourseAcct101();

        $this->assertCount(0, Course::active()->get());
    }

    public function test_not_transfer_in_excludes_transfer_titles(): void
    {
        WarehouseFixtures::seedCourseAcct101();
        WarehouseFixtures::seedTransferInCourse();

        $courseIds = Course::notTransferIn()->pluck('CourseID')->all();

        $this->assertSame(['ACCT 101'], $courseIds);
    }

    public function test_active_as_of_year_quarter_includes_course_with_null_end_quarter(): void
    {
        WarehouseFixtures::seedCourseAcct101();

        $courses = Course::activeAsOfYearQuarter(WarehouseFixtures::CURRENT_YEAR_QUARTER_ID)->get();

        $this->assertCount(1, $courses);
        $this->assertSame('ACCT 101', $courses->first()->CourseID);
    }

    public function test_active_returns_courses_when_current_year_quarter_exists(): void
    {
        $this->travelTo('2024-03-15');
        WarehouseFixtures::seedCurrentYearQuarter();
        WarehouseFixtures::seedCourseAcct101();

        $courses = Course::active()->get();

        $this->assertCount(1, $courses);
        $this->assertSame('ACCT 101', $courses->first()->CourseID);
    }
}
