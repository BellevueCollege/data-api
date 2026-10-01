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

        $this->get('/api/v1/subjects?filter=active-credit')
            ->assertOk()
            ->assertJsonFragment(['subject' => 'ACCT']);
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

        DB::connection('ods')->table('vw_Class')->insert([
            'YearQuarterID' => WarehouseFixtures::CURRENT_YEAR_QUARTER_ID,
            'STRM' => WarehouseFixtures::CURRENT_STRM,
            'Department' => 'ACCT',
            'CourseNumber' => '101',
            'CourseID' => 'ACCT 101',
        ]);

        $this->get('/api/v1/subjects/'.WarehouseFixtures::CURRENT_YEAR_QUARTER_ID)
            ->assertOk()
            ->assertJsonFragment(['subject' => 'ACCT']);
    }

    public function test_returns_empty_json_object_for_unknown_year_quarter_on_subjects(): void
    {
        $this->get('/api/v1/subjects/xyzsdf')
            ->assertOk()
            ->assertExactJson(['subjects' => []]);
    }
}
