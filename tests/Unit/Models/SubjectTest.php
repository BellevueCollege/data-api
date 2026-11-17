<?php

namespace Tests\Unit\Models;

use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Tests\DatabaseTestCase;
use Tests\Support\WarehouseFixtures;

/**
 * Subject effective-dating and active-in-term query scopes.
 */
class SubjectTest extends DatabaseTestCase
{
    public function test_latest_effective_hides_future_effdt_row(): void
    {
        $this->travelTo('2024-03-15');

        DB::connection('ods')->table('vw_PS_CS_SubjectTable')->insert([
            [
                'SUBJECT' => 'HIST',
                'DESCR' => 'History Current',
                'DESCRFORMAL' => 'History',
                'DESCRSHORT' => 'HIST',
                'EFFDT' => '2020-01-01',
                'EFF_STATUS' => 'A',
            ],
            [
                'SUBJECT' => 'HIST',
                'DESCR' => 'History Future',
                'DESCRFORMAL' => 'History',
                'DESCRSHORT' => 'HIST',
                'EFFDT' => '2030-01-01',
                'EFF_STATUS' => 'A',
            ],
        ]);

        $subjects = Subject::where('SUBJECT', 'HIST')->get();

        $this->assertCount(1, $subjects);
        $this->assertSame('History Current', $subjects->first()->DESCR);
    }

    public function test_active_in_term_filters_by_year_quarter_id_by_default(): void
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

        $subjects = Subject::activeInTerm(WarehouseFixtures::CURRENT_YEAR_QUARTER_ID)->get();

        $this->assertCount(1, $subjects);
        $this->assertSame('ACCT', $subjects->first()->SUBJECT);
    }

    public function test_active_in_term_filters_by_strm_when_format_is_strm(): void
    {
        WarehouseFixtures::seedCurrentYearQuarter();
        WarehouseFixtures::seedSubjectEngr();

        DB::connection('ods')->table('vw_Class')->insert([
            'YearQuarterID' => WarehouseFixtures::CURRENT_YEAR_QUARTER_ID,
            'STRM' => WarehouseFixtures::CURRENT_STRM,
            'Department' => 'ENGR',
            'CourseNumber' => '101',
            'CourseID' => 'ENGR 101',
        ]);

        $subjects = Subject::activeInTerm(WarehouseFixtures::CURRENT_STRM, 'strm')->get();

        $this->assertCount(1, $subjects);
        $this->assertSame('ENGR', $subjects->first()->SUBJECT);
    }
}
