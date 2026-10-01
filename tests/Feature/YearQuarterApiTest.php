<?php

namespace Tests\Feature;

use Tests\ApiFeatureTestCase;
use Tests\Support\WarehouseFixtures;

/**
 * Year-quarter metadata and registration visibility on the public API.
 */
class YearQuarterApiTest extends ApiFeatureTestCase
{
    public function test_returns_year_quarter_by_id(): void
    {
        WarehouseFixtures::seedCurrentYearQuarter();

        $this->get('/api/v1/quarter/'.WarehouseFixtures::CURRENT_YEAR_QUARTER_ID)
            ->assertOk()
            ->assertJsonFragment([
                'quarter' => WarehouseFixtures::CURRENT_YEAR_QUARTER_ID,
                'title' => 'SPRING 2024',
            ]);
    }

    public function test_returns_year_quarter_by_strm_format(): void
    {
        WarehouseFixtures::seedCurrentYearQuarter();

        $this->get('/api/v1/quarter/'.WarehouseFixtures::CURRENT_STRM.'?format=strm')
            ->assertOk()
            ->assertJsonFragment([
                'strm' => WarehouseFixtures::CURRENT_STRM,
            ]);
    }

    public function test_returns_empty_json_object_for_unknown_year_quarter(): void
    {
        $this->get('/api/v1/quarter/xysdf')
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_returns_current_year_quarter(): void
    {
        WarehouseFixtures::seedCurrentYearQuarter();

        $this->get('/api/v1/quarter/current')
            ->assertOk()
            ->assertJsonFragment([
                'quarter' => WarehouseFixtures::CURRENT_YEAR_QUARTER_ID,
            ]);
    }

    public function test_returns_viewable_year_quarters(): void
    {
        WarehouseFixtures::seedViewableYearQuarterWithRegistration();

        $this->get('/api/v1/quarters')
            ->assertOk()
            ->assertJsonFragment([
                'quarter' => WarehouseFixtures::CURRENT_YEAR_QUARTER_ID,
            ]);
    }
}
