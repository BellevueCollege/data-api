<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
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

    public function test_returns_422_when_format_is_invalid_for_class_offering(): void
    {
        WarehouseFixtures::seedClassOfferingAbe53();

        $this->getJson('/api/v1/classes/'.WarehouseFixtures::CURRENT_YEAR_QUARTER_ID.'/ABE/53?format=bad')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['format']);
    }

    public function test_returns_422_when_format_is_invalid_for_classes_by_subject(): void
    {
        WarehouseFixtures::seedClassOfferingAbe53();

        $this->getJson('/api/v1/classes/'.WarehouseFixtures::CURRENT_YEAR_QUARTER_ID.'/ABE?format=bad')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['format']);
    }

    public function test_formats_section_days_and_trims_schedule(): void
    {
        WarehouseFixtures::seedCurrentYearQuarter();

        DB::connection('ods')->table('vw_Course')->insert([
            'PSCourseID' => 'PS-DAY-1',
            'CourseID' => 'DAY 101',
            'CourseSubject' => 'DAY',
            'CatalogNumber' => '101',
            'CourseTitle' => 'Days Course',
            'CourseTitle2' => '',
            'EffectiveYearQuarterEnd' => null,
            'Credits' => 3,
            'VariableCredits' => 0,
        ]);

        DB::connection('ods')->table('vw_Class')->insert([
            'PSClassID' => 'PS-CLASS-DAY',
            'ClassID' => '2001',
            'YearQuarterID' => WarehouseFixtures::CURRENT_YEAR_QUARTER_ID,
            'STRM' => WarehouseFixtures::CURRENT_STRM,
            'Department' => 'DAY',
            'CourseNumber' => '101',
            'CourseID' => 'DAY 101',
            'CourseTitle' => 'Days Course',
            'Section' => '01',
            'ItemNumber' => '1',
            'ClassNumber' => '2001',
            'InstructorName' => 'Day Teacher',
            'StartDate' => '2024-03-01 00:00:00',
            'EndDate' => '2024-06-01 00:00:00',
            'DayID' => 'MRU ',
            'Room' => 'R1',
            'RoomDescr' => 'Room 1',
            'StartTime' => '2024-03-01 09:00:00',
            'EndTime' => '2024-03-01 10:00:00',
        ]);

        $response = $this->get('/api/v1/classes/'.WarehouseFixtures::CURRENT_YEAR_QUARTER_ID.'/DAY/101')
            ->assertOk()
            ->assertJsonPath('classes.sections.data.0.days', 'MThSu');

        $schedule = $response->json('classes.sections.data.0.schedule');
        $this->assertSame('9:00am-10:00am', $schedule);
        $this->assertSame($schedule, trim($schedule));
    }

    public function test_section_schedule_is_empty_when_end_time_is_missing(): void
    {
        WarehouseFixtures::seedCurrentYearQuarter();

        DB::connection('ods')->table('vw_Course')->insert([
            'PSCourseID' => 'PS-TIME-1',
            'CourseID' => 'TIM 101',
            'CourseSubject' => 'TIM',
            'CatalogNumber' => '101',
            'CourseTitle' => 'Time Course',
            'CourseTitle2' => '',
            'EffectiveYearQuarterEnd' => null,
            'Credits' => 3,
            'VariableCredits' => 0,
        ]);

        DB::connection('ods')->table('vw_Class')->insert([
            'PSClassID' => 'PS-CLASS-TIME',
            'ClassID' => '2002',
            'YearQuarterID' => WarehouseFixtures::CURRENT_YEAR_QUARTER_ID,
            'STRM' => WarehouseFixtures::CURRENT_STRM,
            'Department' => 'TIM',
            'CourseNumber' => '101',
            'CourseID' => 'TIM 101',
            'CourseTitle' => 'Time Course',
            'Section' => '01',
            'ItemNumber' => '1',
            'ClassNumber' => '2002',
            'InstructorName' => 'Time Teacher',
            'StartDate' => '2024-03-01 00:00:00',
            'EndDate' => '2024-06-01 00:00:00',
            'DayID' => 'MW',
            'Room' => 'R1',
            'RoomDescr' => 'Room 1',
            'StartTime' => '2024-03-01 09:00:00',
            'EndTime' => null,
        ]);

        $this->get('/api/v1/classes/'.WarehouseFixtures::CURRENT_YEAR_QUARTER_ID.'/TIM/101')
            ->assertOk()
            ->assertJsonPath('classes.sections.data.0.schedule', '');
    }

    public function test_section_schedule_is_empty_when_start_time_is_missing(): void
    {
        WarehouseFixtures::seedCurrentYearQuarter();

        DB::connection('ods')->table('vw_Course')->insert([
            'PSCourseID' => 'PS-TIME-2',
            'CourseID' => 'TIM 102',
            'CourseSubject' => 'TIM',
            'CatalogNumber' => '102',
            'CourseTitle' => 'Time Course 2',
            'CourseTitle2' => '',
            'EffectiveYearQuarterEnd' => null,
            'Credits' => 3,
            'VariableCredits' => 0,
        ]);

        DB::connection('ods')->table('vw_Class')->insert([
            'PSClassID' => 'PS-CLASS-TIME-2',
            'ClassID' => '2003',
            'YearQuarterID' => WarehouseFixtures::CURRENT_YEAR_QUARTER_ID,
            'STRM' => WarehouseFixtures::CURRENT_STRM,
            'Department' => 'TIM',
            'CourseNumber' => '102',
            'CourseID' => 'TIM 102',
            'CourseTitle' => 'Time Course 2',
            'Section' => '01',
            'ItemNumber' => '1',
            'ClassNumber' => '2003',
            'InstructorName' => 'Time Teacher',
            'StartDate' => '2024-03-01 00:00:00',
            'EndDate' => '2024-06-01 00:00:00',
            'DayID' => 'MW',
            'Room' => 'R1',
            'RoomDescr' => 'Room 1',
            'StartTime' => null,
            'EndTime' => '2024-03-01 10:00:00',
        ]);

        $this->get('/api/v1/classes/'.WarehouseFixtures::CURRENT_YEAR_QUARTER_ID.'/TIM/102')
            ->assertOk()
            ->assertJsonPath('classes.sections.data.0.schedule', '');
    }

    public function test_trims_subject_and_course_number(): void
    {
        WarehouseFixtures::seedCurrentYearQuarter();

        DB::connection('ods')->table('vw_Course')->insert([
            'PSCourseID' => 'PS-NOTE-1',
            'CourseID' => 'ABC 101',
            'CourseSubject' => 'ABC',
            'CatalogNumber' => '101',
            'CourseTitle' => 'Note Course',
            'CourseTitle2' => '',
            'EffectiveYearQuarterEnd' => null,
            'Credits' => 3,
            'VariableCredits' => 0,
        ]);

        DB::connection('ods')->table('vw_Class')->insert([
            'PSClassID' => 'PS-CLASS-NOTE',
            'ClassID' => '3001',
            'YearQuarterID' => WarehouseFixtures::CURRENT_YEAR_QUARTER_ID,
            'STRM' => WarehouseFixtures::CURRENT_STRM,
            'Department' => 'ABC  ',
            'CourseNumber' => ' 101 ',
            'CourseID' => 'ABC 101',
            'CourseTitle' => 'Note Course',
            'Section' => '01',
            'ItemNumber' => '1',
            'ClassNumber' => '3001',
            'InstructorName' => 'Note Teacher',
            'StartDate' => '2024-03-01 00:00:00',
            'EndDate' => '2024-06-01 00:00:00',
            'DayID' => 'MW',
            'Room' => 'R1',
            'RoomDescr' => 'Room 1',
            'StartTime' => '2024-03-01 09:00:00',
            'EndTime' => '2024-03-01 10:00:00',
        ]);

        $this->get('/api/v1/classes/'.WarehouseFixtures::CURRENT_YEAR_QUARTER_ID.'/ABC%20%20/%20101%20')
            ->assertOk()
            ->assertJsonFragment([
                'subject' => 'ABC',
                'courseNumber' => '101',
            ]);
    }
}
