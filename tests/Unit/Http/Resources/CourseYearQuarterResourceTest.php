<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\CourseYearQuarterResource;
use App\Models\CourseYearQuarter;
use Illuminate\Support\Facades\DB;
use Tests\DatabaseTestCase;
use Tests\Support\AssertsResourceContract;
use Tests\Support\WarehouseFixtures;

/**
 * JSON shape of CourseYearQuarterResource matches the class offering API contract.
 */
class CourseYearQuarterResourceTest extends DatabaseTestCase
{
    use AssertsResourceContract;

    protected function setUp(): void
    {
        parent::setUp();

        // Matches WarehouseFixtures class offering dates for the current quarter.
        $this->travelTo('2024-03-15 12:00:00');
    }

    public function test_serializes_class_offering_contract(): void
    {
        WarehouseFixtures::seedClassOfferingAbe53();

        DB::connection('ods')->table('vw_CourseDescription')->insert([
            'CourseID' => 'ABE 53',
            'Description' => 'ABE literacy overview.',
            'EffectiveYearQuarterBegin' => 'B500',
        ]);

        $offering = CourseYearQuarter::query()
            ->where('YearQuarterID', WarehouseFixtures::CURRENT_YEAR_QUARTER_ID)
            ->where('Department', 'ABE')
            ->where('CourseNumber', '53')
            ->firstOrFail();

        $offering->load('sections');

        $this->assertResourceContract(new CourseYearQuarterResource($offering), [
            'title' => '',
            'subject' => 'ABE',
            'courseNumber' => '53',
            'description' => 'ABE literacy overview.',
            'note' => null,
            'credits' => 3,
            'quarter' => WarehouseFixtures::CURRENT_YEAR_QUARTER_ID,
            'strm' => WarehouseFixtures::CURRENT_STRM,
            'isVariableCredits' => false,
            'isCommonCourse' => false,
            'sections' => [
                'data' => [
                    [
                        'id' => '1001',
                        'section' => '01',
                        'itemNumber' => '1',
                        'classNumber' => '1001',
                        'instructor' => 'Test Instructor',
                        'beginDate' => '03-01-2024',
                        'endDate' => '06-01-2024',
                        'room' => 'R101',
                        'days' => 'MW',
                        'schedule' => '9:00am-10:30am',
                        'roomDescription' => 'Room 101',
                    ],
                ],
            ],
        ]);
    }
}
