<?php

namespace Tests\Unit\Models;

use App\Models\CourseDescription;
use Tests\DatabaseTestCase;
use Tests\Support\WarehouseFixtures;
use Illuminate\Support\Facades\DB;

/**
 * Active course description selection across effective year-quarters.
 */
class CourseDescriptionTest extends DatabaseTestCase
{
    public function test_active_description_skips_blank_description_and_returns_latest_begin_quarter(): void
    {
        $this->travelTo('2024-03-15');
        WarehouseFixtures::seedCurrentYearQuarter();

        DB::connection('ods')->table('vw_CourseDescription')->insert([
            ['CourseID' => 'ACCT 101', 'Description' => ' ', 'EffectiveYearQuarterBegin' => 'B500'],
            ['CourseID' => 'ACCT 101', 'Description' => 'Older text', 'EffectiveYearQuarterBegin' => 'B510'],
            ['CourseID' => 'ACCT 101', 'Description' => 'Newer text', 'EffectiveYearQuarterBegin' => 'C100'],
        ]);

        $description = CourseDescription::query()
            ->where('CourseID', 'ACCT 101')
            ->activeDescription('C124')
            ->first();

        $this->assertNotNull($description);
        $this->assertSame('Newer text', $description->Description);
    }
}
