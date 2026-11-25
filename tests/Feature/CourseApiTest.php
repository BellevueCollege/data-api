<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\ApiFeatureTestCase;
use Tests\Support\WarehouseFixtures;

/**
 * Public course lookup and catalog listing backed by warehouse fixtures.
 */
class CourseApiTest extends ApiFeatureTestCase
{
    public function test_returns_course_by_course_id(): void
    {
        WarehouseFixtures::seedActiveCreditCatalog();

        $this->get('/api/v1/course/ACCT%20101')
            ->assertOk()
            ->assertJsonFragment([
                'subject' => 'ACCT',
                'courseNumber' => '101',
            ]);
    }

    public function test_returns_404_with_empty_courses_when_course_id_is_unknown(): void
    {
        WarehouseFixtures::seedActiveCreditCatalog();

        $this->get('/api/v1/course/XYZ%20100')
            ->assertNotFound()
            ->assertExactJson(['courses' => []]);
    }

    public function test_returns_course_by_subject_and_number(): void
    {
        WarehouseFixtures::seedActiveCreditCatalog();

        $this->get('/api/v1/course/ACCT/101')
            ->assertOk()
            ->assertJsonFragment([
                'subject' => 'ACCT',
                'courseNumber' => '101',
            ]);
    }

    public function test_returns_404_with_empty_courses_for_unknown_subject_and_number(): void
    {
        WarehouseFixtures::seedActiveCreditCatalog();

        $this->get('/api/v1/course/XYZ/100')
            ->assertNotFound()
            ->assertExactJson(['courses' => []]);
    }

    public function test_returns_courses_by_subject_excluding_transfer_in_row(): void
    {
        WarehouseFixtures::seedActiveCreditCatalog();
        WarehouseFixtures::seedTransferInCourse();

        $response = $this->get('/api/v1/courses/ACCT');

        $response->assertOk()
            ->assertJsonFragment(['courseId' => 'ACCT 101'])
            ->assertJsonMissing(['courseId' => 'ACCT 199']);
    }

    public function test_returns_multiple_courses_from_query_string(): void
    {
        WarehouseFixtures::seedActiveCreditCatalog();

        DB::connection('ods')->table('vw_Course')->insert([
            'PSCourseID' => 'PS-BTS-293',
            'CourseID' => 'BTS 293',
            'CourseSubject' => 'BTS',
            'CatalogNumber' => '293',
            'CourseTitle' => 'BTS Course',
            'CourseTitle2' => null,
            'EffectiveYearQuarterEnd' => null,
            'Credits' => 2,
            'VariableCredits' => 0,
        ]);

        $this->get('/api/v1/courses/multiple?courses[]=ACCT%20101&courses[]=BTS%20293')
            ->assertOk()
            ->assertJsonFragment(['courseId' => 'ACCT 101'])
            ->assertJsonFragment(['courseId' => 'BTS 293']);
    }

    public function test_returns_422_when_courses_parameter_is_missing(): void
    {
        $this->getJson('/api/v1/courses/multiple')
            ->assertStatus(422);
    }

    public function test_returns_only_active_courses_for_subject(): void
    {
        WarehouseFixtures::seedCurrentYearQuarter();
        WarehouseFixtures::seedSubjectAcct();

        DB::connection('ods')->table('vw_Course')->insert([
            [
                'PSCourseID' => 'PS-ACCT-100',
                'CourseID' => 'ACCT 100',
                'CourseSubject' => 'ACCT',
                'CatalogNumber' => '100',
                'CourseTitle' => 'Ended Accounting',
                'CourseTitle2' => '',
                'EffectiveYearQuarterEnd' => 'B012',
                'Credits' => 5,
                'VariableCredits' => 0,
            ],
            [
                'PSCourseID' => 'PS-ACCT-101',
                'CourseID' => 'ACCT 101',
                'CourseSubject' => 'ACCT',
                'CatalogNumber' => '101',
                'CourseTitle' => 'Current Accounting',
                'CourseTitle2' => '',
                'EffectiveYearQuarterEnd' => 'Z999',
                'Credits' => 5,
                'VariableCredits' => 0,
            ],
            [
                'PSCourseID' => 'PS-ACCT-102',
                'CourseID' => 'ACCT 102',
                'CourseSubject' => 'ACCT',
                'CatalogNumber' => '102',
                'CourseTitle' => 'Open-Ended Accounting',
                'CourseTitle2' => '',
                'EffectiveYearQuarterEnd' => null,
                'Credits' => 5,
                'VariableCredits' => 0,
            ],
        ]);

        $response = $this->get('/api/v1/courses/ACCT')
            ->assertOk()
            ->assertJsonFragment(['courseId' => 'ACCT 101'])
            ->assertJsonFragment(['courseId' => 'ACCT 102']);

        $courses = $response->json('courses');
        if (is_array($courses) && isset($courses['data'])) {
            $courses = $courses['data'];
        }

        $courseIds = collect(is_array($courses) ? $courses : [])
            ->pluck('courseId')
            ->filter()
            ->values()
            ->all();

        $this->assertContains('ACCT 101', $courseIds);
        $this->assertContains('ACCT 102', $courseIds);
        $this->assertNotContains('ACCT 100', $courseIds);
    }

    public function test_normalizes_active_course_description_whitespace(): void
    {
        WarehouseFixtures::seedCurrentYearQuarter();
        WarehouseFixtures::seedSubjectAcct();

        DB::connection('ods')->table('vw_Course')->insert([
            'PSCourseID' => 'PS-ACCT-101',
            'CourseID' => 'ACCT 101',
            'CourseSubject' => 'ACCT',
            'CatalogNumber' => '101',
            'CourseTitle' => 'Intro Accounting',
            'CourseTitle2' => '',
            'EffectiveYearQuarterEnd' => null,
            'Credits' => 5,
            'VariableCredits' => 0,
        ]);

        DB::connection('ods')->table('vw_CourseDescription')->insert([
            [
                'CourseID' => 'ACCT 101',
                'Description' => '  Current   text  ',
                'EffectiveYearQuarterBegin' => 'B012',
            ],
            [
                'CourseID' => 'ACCT 101',
                'Description' => 'Future description',
                'EffectiveYearQuarterBegin' => 'Z999',
            ],
        ]);

        $this->get('/api/v1/course/ACCT%20101')
            ->assertOk()
            ->assertJsonPath('course.description', 'Current text');
    }
}
