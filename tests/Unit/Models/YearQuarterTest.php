<?php

namespace Tests\Unit\Models;

use App\Models\YearQuarter;
use Illuminate\Support\Facades\DB;
use Tests\DatabaseTestCase;

/**
 * YearQuarter current term selection based on class-day dates.
 */
class YearQuarterTest extends DatabaseTestCase
{
    public function test_current_picks_earliest_strm_with_last_class_day_on_or_after_today(): void
    {
        $this->travelTo('2024-03-15 12:00:00');

        DB::connection('ods')->table('vw_YearQuarter')->insert([
            [
                'YearQuarterID' => 'C125',
                'STRM' => '2245',
                'Title' => 'FALL 2024',
                'FirstClassDay' => '2024-09-01 00:00:00',
                'LastClassDay' => '2024-12-15 00:00:00',
            ],
            [
                'YearQuarterID' => 'C124',
                'STRM' => '2241',
                'Title' => 'SPRING 2024',
                'FirstClassDay' => '2024-01-08 00:00:00',
                'LastClassDay' => '2024-12-15 00:00:00',
            ],
            [
                'YearQuarterID' => 'SKIP',
                'STRM' => '9999',
                'Title' => 'MAX TERM',
                'FirstClassDay' => '2024-01-08 00:00:00',
                'LastClassDay' => '2025-12-15 00:00:00',
            ],
        ]);

        $current = YearQuarter::current()->first();

        $this->assertNotNull($current);
        $this->assertSame('2241', $current->STRM);
    }
}
