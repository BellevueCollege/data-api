<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\CourseResource;
use App\Models\Course;
use Tests\DatabaseTestCase;
use Tests\Support\AssertsResourceContract;
use Tests\Support\WarehouseFixtures;

/**
 * JSON shape of CourseResource matches the course API contract, including active descriptions.
 */
class CourseResourceTest extends DatabaseTestCase
{
    use AssertsResourceContract;

    protected function setUp(): void
    {
        parent::setUp();

        // Matches WarehouseFixtures quarter windows used for active description logic.
        $this->travelTo('2024-03-15 12:00:00');
        WarehouseFixtures::seedCurrentYearQuarter();
    }

    public function test_serializes_course_contract_without_active_description(): void
    {
        WarehouseFixtures::seedTransferInCourse();

        $course = Course::query()->where('PSCourseID', 'PS-ACCT-TR')->firstOrFail();

        $this->assertResourceContract(new CourseResource($course), [
            'title' => '',
            'subject' => 'ACCT',
            'courseNumber' => '199',
            'courseId' => 'ACCT 199',
            'ctcCourseId' => 'PS-ACCT-TR',
            'description' => null,
            'note' => null,
            'credits' => 1,
            'isVariableCredits' => false,
            'isCommonCourse' => false,
        ]);
    }

    public function test_serializes_course_contract_with_active_description(): void
    {
        WarehouseFixtures::seedCourseAcct101();

        $course = Course::query()->where('PSCourseID', 'PS-ACCT-101')->firstOrFail();

        $this->assertResourceContract(new CourseResource($course), [
            'title' => '',
            'subject' => 'ACCT',
            'courseNumber' => '101',
            'courseId' => 'ACCT 101',
            'ctcCourseId' => 'PS-ACCT-101',
            'description' => 'Introduction to accounting.',
            'note' => null,
            'credits' => 5,
            'isVariableCredits' => false,
            'isCommonCourse' => false,
        ]);
    }
}
