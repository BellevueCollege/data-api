<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\ApiFeatureTestCase;
use Tests\Support\WarehouseFixtures;

/**
 * Public class schedule rows for a PeopleSoft class identifier.
 */
class ClassScheduleApiTest extends ApiFeatureTestCase
{
    public function test_returns_schedules_for_class_id(): void
    {
        WarehouseFixtures::seedClassSchedule();

        $this->get('/api/v1/schedules/PS-SCHED-1')
            ->assertOk()
            ->assertJsonFragment([
                'instructor' => 'Schedule Instructor',
            ]);
    }

    public function test_returns_empty_class_schedules_for_unknown_class_id(): void
    {
        $this->get('/api/v1/schedules/unknown-class-id')
            ->assertOk()
            ->assertJson(['classSchedules' => []]);
    }

    public function test_formats_days_and_trims_schedule(): void
    {
        DB::connection('ods')->table('vw_ClassSchedule')->insert([
            'PSClassID' => 'PS-SCHED-DAYS',
            'DayID' => 'MRU ',
            'Room' => 'R202',
            'RoomDescr' => 'Room 202',
            'InstructorName' => 'Schedule Instructor',
            'StartDate' => '2024-03-01 00:00:00',
            'EndDate' => '2024-06-01 00:00:00',
            'StartTime' => '2024-03-01 13:00:00',
            'EndTime' => '2024-03-01 14:30:00',
        ]);

        $response = $this->get('/api/v1/schedules/PS-SCHED-DAYS')
            ->assertOk()
            ->assertJsonPath('classSchedules.0.days', 'MThSu');

        $schedule = $response->json('classSchedules.0.schedule');
        $this->assertSame('1:00pm-2:30pm', $schedule);
        $this->assertSame($schedule, trim($schedule));
    }
}
