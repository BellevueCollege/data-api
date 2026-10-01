<?php

namespace Tests\Unit\Models;

use App\Models\Course;
use Tests\TestCase;

/**
 * Course title and common-course accessors on in-memory models.
 */
class CourseAccessorTest extends TestCase
{
    public function test_title_uses_course_title2_when_set(): void
    {
        $course = new Course();
        $course->forceFill([
            'CourseTitle' => 'Primary',
            'CourseTitle2' => 'Secondary',
        ]);

        $this->assertSame('Secondary', $course->title);
    }

    public function test_title_falls_back_to_course_title_when_course_title2_is_null(): void
    {
        $course = new Course();
        $course->forceFill([
            'CourseTitle' => 'Primary',
            'CourseTitle2' => null,
        ]);

        $this->assertSame('Primary', $course->title);
    }

    public function test_is_common_course_is_true_when_course_id_contains_common_char(): void
    {
        $course = new Course();
        $course->forceFill(['CourseID' => 'ENGL&101']);

        $this->assertTrue($course->isCommonCourse);
    }

    public function test_is_common_course_is_false_when_course_id_does_not_contain_common_char(): void
    {
        $course = new Course();
        $course->forceFill(['CourseID' => 'ENGL 101']);

        $this->assertFalse($course->isCommonCourse);
    }
}
