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
            'note' => null,
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
}
