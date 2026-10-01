<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\ClassScheduleResource;
use App\Models\ClassSchedule;
use Tests\Support\ResourceTestCase;

/**
 * JSON shape of ClassScheduleResource matches the schedules API contract.
 */
class ClassScheduleResourceTest extends ResourceTestCase
{
    public function test_serializes_class_schedule_contract(): void
    {
        $schedule = new ClassSchedule();
        $schedule->forceFill([
            'DayID' => 'MW',
            'Room' => ' R202 ',
            'RoomDescr' => 'Room 202',
            'InstructorName' => 'Schedule Instructor',
            'StartDate' => '2024-03-01 00:00:00',
            'EndDate' => '2024-06-01 00:00:00',
            'StartTime' => '2024-03-01 13:00:00',
            'EndTime' => '2024-03-01 14:30:00',
        ]);

        $this->assertResourceContract(new ClassScheduleResource($schedule), [
            'days' => 'MW',
            'room' => 'R202',
            'roomDescription' => 'Room 202',
            'instructor' => 'Schedule Instructor',
            'beginDate' => '03-01-2024',
            'endDate' => '06-01-2024',
            'schedule' => '1:00pm-2:30pm',
        ]);
    }
}
