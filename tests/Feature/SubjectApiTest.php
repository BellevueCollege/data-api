<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\ApiFeatureTestCase;
use Tests\Support\WarehouseFixtures;

/**
 * Subject index and detail from the ODS subject table.
 */
class SubjectApiTest extends ApiFeatureTestCase
{
    public function test_returns_subject_index(): void
    {
        WarehouseFixtures::seedSubjectAcct();

        $this->get('/api/v1/subjects')
            ->assertOk()
            ->assertJsonFragment([
                'subject' => 'ACCT',
            ]);
    }

    public function test_returns_subjects_with_active_credit_filter(): void
    {
        WarehouseFixtures::seedActiveCreditCatalog();

        DB::connection('ods')->table('vw_PS_CS_SubjectTable')->insert([
            'SUBJECT' => 'INAC',
            'DESCR' => 'Inactive Only',
            'DESCRFORMAL' => 'Inactive Only Department',
            'DESCRSHORT' => 'INAC',
            'EFFDT' => '2020-01-01',
            'EFF_STATUS' => 'A',
        ]);
        DB::connection('ods')->table('vw_Course')->insert([
            'PSCourseID' => 'PS-INAC-101',
            'CourseID' => 'INAC 101',
            'CourseSubject' => 'INAC',
            'CatalogNumber' => '101',
            'CourseTitle' => 'Ended Course',
            'CourseTitle2' => '',
            'EffectiveYearQuarterEnd' => 'B012',
            'Credits' => 3,
            'VariableCredits' => 0,
        ]);

        DB::connection('ods')->table('vw_PS_CS_SubjectTable')->insert([
            'SUBJECT' => 'TRNS',
            'DESCR' => 'Transfer Only',
            'DESCRFORMAL' => 'Transfer Only Department',
            'DESCRSHORT' => 'TRNS',
            'EFFDT' => '2020-01-01',
            'EFF_STATUS' => 'A',
        ]);
        DB::connection('ods')->table('vw_Course')->insert([
            'PSCourseID' => 'PS-TRNS-199',
            'CourseID' => 'TRNS 199',
            'CourseSubject' => 'TRNS',
            'CatalogNumber' => '199',
            'CourseTitle' => 'Transferred-In Course',
            'CourseTitle2' => '',
            'EffectiveYearQuarterEnd' => null,
            'Credits' => 1,
            'VariableCredits' => 0,
        ]);

        $response = $this->get('/api/v1/subjects?filter=active-credit')
            ->assertOk()
            ->assertJsonFragment(['subject' => 'ACCT']);

        $subjects = $this->subjectCodesFromResponse($response);

        $this->assertSame(['ACCT'], $subjects);
        $this->assertNotContains('INAC', $subjects);
        $this->assertNotContains('TRNS', $subjects);
    }

    public function test_returns_422_when_format_is_invalid_for_subjects_by_term(): void
    {
        WarehouseFixtures::seedCurrentYearQuarter();

        $this->getJson('/api/v1/subjects/'.WarehouseFixtures::CURRENT_YEAR_QUARTER_ID.'?format=bad')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['format']);
    }

    public function test_returns_subject_by_slug(): void
    {
        WarehouseFixtures::seedSubjectEngr();

        $this->get('/api/v1/subject/ENGR')
            ->assertOk()
            ->assertJsonFragment([
                'subject' => 'ENGR',
                'name' => 'Engineering',
            ]);
    }

    public function test_returns_empty_json_object_for_unknown_subject(): void
    {
        $this->get('/api/v1/subject/xyzsdf')
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_returns_subjects_for_year_quarter(): void
    {
        WarehouseFixtures::seedCurrentYearQuarter();
        WarehouseFixtures::seedSubjectAcct();
        WarehouseFixtures::seedSubjectEngr();

        DB::connection('ods')->table('vw_YearQuarter')->insert([
            'YearQuarterID' => 'B012',
            'STRM' => '2123',
            'Title' => 'FALL 2012',
            'FirstClassDay' => '2012-09-01 00:00:00',
            'LastClassDay' => '2012-12-15 00:00:00.000',
        ]);

        DB::connection('ods')->table('vw_Class')->insert([
            'YearQuarterID' => WarehouseFixtures::CURRENT_YEAR_QUARTER_ID,
            'STRM' => WarehouseFixtures::CURRENT_STRM,
            'Department' => 'ACCT',
            'CourseNumber' => '101',
            'CourseID' => 'ACCT 101',
        ]);
        DB::connection('ods')->table('vw_Class')->insert([
            'YearQuarterID' => 'B012',
            'STRM' => '2123',
            'Department' => 'ENGR',
            'CourseNumber' => '101',
            'CourseID' => 'ENGR 101',
        ]);

        $response = $this->get('/api/v1/subjects/'.WarehouseFixtures::CURRENT_YEAR_QUARTER_ID)
            ->assertOk()
            ->assertJsonFragment(['subject' => 'ACCT']);

        $subjects = $this->subjectCodesFromResponse($response);

        $this->assertSame(['ACCT'], $subjects);
        $this->assertNotContains('ENGR', $subjects);
    }

    public function test_returns_empty_json_object_for_unknown_year_quarter_on_subjects(): void
    {
        $this->get('/api/v1/subjects/xyzsdf')
            ->assertOk()
            ->assertExactJson(['subjects' => []]);
    }

    /**
     * @return list<string>
     */
    private function subjectCodesFromResponse(\Illuminate\Testing\TestResponse $response): array
    {
        $subjects = $response->json('subjects');

        if (is_array($subjects) && array_is_list($subjects) === false && isset($subjects['data'])) {
            $subjects = $subjects['data'];
        }

        if (is_array($subjects) && isset($subjects['subjects'])) {
            $subjects = $subjects['subjects'];
        }

        return collect(is_array($subjects) ? $subjects : [])
            ->pluck('subject')
            ->filter()
            ->values()
            ->all();
    }
}
