<?php

namespace Tests\Unit\Models;

use App\Models\Course;
use App\Models\CourseYearQuarter;
use Tests\TestCase;

/**
 * CourseYearQuarter title accessor fallbacks when related course data is missing.
 */
class CourseYearQuarterTest extends TestCase
{
    public function test_title_uses_related_course_title_when_present(): void
    {
        $course = new Course();
        $course->forceFill([
            'CourseTitle' => 'From Parent',
            'CourseTitle2' => null,
        ]);

        $offering = new CourseYearQuarter();
        $offering->forceFill(['CourseTitle' => 'Offering Title']);
        $offering->setRelation('course', $course);

        $this->assertSame('From Parent', $offering->title);
    }

    public function test_title_falls_back_to_offering_course_title_when_parent_title_is_null(): void
    {
        $course = new Course();
        $course->forceFill([
            'CourseTitle' => null,
            'CourseTitle2' => null,
        ]);

        $offering = new CourseYearQuarter();
        $offering->forceFill(['CourseTitle' => 'Offering Title']);
        $offering->setRelation('course', $course);

        $this->assertSame('Offering Title', $offering->title);
    }
}
