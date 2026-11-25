<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
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

    public function test_returns_ended_and_upcoming_within_lookahead_but_not_beyond(): void
    {
        $this->freezeTime();

        DB::connection('ods')->table('vw_YearQuarter')->insert([
            [
                'YearQuarterID' => 'B901',
                'STRM' => '1901',
                'Title' => 'ENDED QUARTER',
                'FirstClassDay' => '2023-01-01 00:00:00',
                'LastClassDay' => '2024-03-01 00:00:00.000',
            ],
            [
                'YearQuarterID' => 'C901',
                'STRM' => '2901',
                'Title' => 'WITHIN LOOKAHEAD',
                'FirstClassDay' => '2024-06-01 00:00:00',
                'LastClassDay' => '2024-08-15 00:00:00.000',
            ],
            [
                'YearQuarterID' => 'C902',
                'STRM' => '2902',
                'Title' => 'BEYOND LOOKAHEAD',
                'FirstClassDay' => '2024-09-01 00:00:00',
                'LastClassDay' => '2024-12-15 00:00:00.000',
            ],
        ]);

        DB::connection('ods')->table('vw_WebRegistrationSetting')->insert([
            [
                'STRM' => '1901',
                'FirstRegistrationDate' => '2022-12-01 00:00:00',
                'LastRegistrationDate' => '2023-01-15 00:00:00',
            ],
            [
                'STRM' => '2901',
                'FirstRegistrationDate' => '2024-03-20 00:00:00',
                'LastRegistrationDate' => '2024-06-01 00:00:00',
            ],
            [
                'STRM' => '2902',
                'FirstRegistrationDate' => '2024-04-15 00:00:00',
                'LastRegistrationDate' => '2024-09-01 00:00:00',
            ],
        ]);

        $response = $this->get('/api/v1/quarters')
            ->assertOk()
            ->assertJsonFragment(['quarter' => 'B901'])
            ->assertJsonFragment(['quarter' => 'C901']);

        $quartersPayload = $response->json('quarters');
        if (is_array($quartersPayload) && isset($quartersPayload['data'])) {
            $quartersPayload = $quartersPayload['data'];
        }

        $quarters = collect(is_array($quartersPayload) ? $quartersPayload : [])
            ->pluck('quarter')
            ->filter()
            ->values()
            ->all();

        $this->assertContains('B901', $quarters);
        $this->assertContains('C901', $quarters);
        $this->assertNotContains('C902', $quarters);
    }
}
