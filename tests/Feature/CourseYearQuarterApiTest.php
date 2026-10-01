<?php

namespace Tests\Feature;

use Tests\ApiFeatureTestCase;
use Tests\Support\WarehouseFixtures;

/**
 * Class offerings for a year-quarter, subject, and catalog number.
 */
class CourseYearQuarterApiTest extends ApiFeatureTestCase
{
    public function test_returns_class_offering_by_year_quarter_subject_and_number(): void
    {
        WarehouseFixtures::seedClassOfferingAbe53();

        $this->get('/api/v1/classes/'.WarehouseFixtures::CURRENT_YEAR_QUARTER_ID.'/ABE/53')
            ->assertOk()
            ->assertJsonFragment([
                'subject' => 'ABE',
                'courseNumber' => '53',
            ]);
    }

    public function test_returns_class_offering_by_strm_format(): void
    {
        WarehouseFixtures::seedClassOfferingAbe53();

        $this->get('/api/v1/classes/'.WarehouseFixtures::CURRENT_STRM.'/ABE/53?format=strm')
            ->assertOk()
            ->assertJsonFragment([
                'strm' => WarehouseFixtures::CURRENT_STRM,
            ]);
    }

    public function test_returns_404_with_empty_classes_for_unknown_offering(): void
    {
        WarehouseFixtures::seedClassOfferingAbe53();

        $this->get('/api/v1/classes/xysdf/ADFIT/020')
            ->assertNotFound()
            ->assertExactJson(['classes' => []]);
    }

    public function test_returns_classes_by_subject_for_term(): void
    {
        WarehouseFixtures::seedClassOfferingAbe53();

        $this->get('/api/v1/classes/'.WarehouseFixtures::CURRENT_YEAR_QUARTER_ID.'/ABE')
            ->assertOk()
            ->assertJsonFragment(['subject' => 'ABE']);
    }

    public function test_returns_empty_classes_collection_when_no_offerings_match_subject(): void
    {
        WarehouseFixtures::seedCurrentYearQuarter();

        $this->get('/api/v1/classes/'.WarehouseFixtures::CURRENT_YEAR_QUARTER_ID.'/XYZ')
            ->assertOk()
            ->assertJson(['classes' => []]);
    }
}
