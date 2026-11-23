<?php

namespace Tests\Unit\Models;

use App\Models\ClassSchedule;
use Tests\TestCase;

/**
 * ClassSchedule accessors for location, schedule text, and formatted dates.
 */
class ClassScheduleTest extends TestCase
{
    public function test_location_returns_trimmed_room(): void
    {
        $schedule = new ClassSchedule();
        $schedule->forceFill(['Room' => '  B201  ']);

        $this->assertSame('B201', $schedule->location);
    }

    public function test_schedule_is_empty_when_start_or_end_time_is_missing(): void
    {
        $schedule = new ClassSchedule();
        $schedule->forceFill([
            'StartTime' => null,
            'EndTime' => '2024-03-01 10:00:00',
        ]);

        $this->assertSame('', $schedule->schedule);
    }

    public function test_schedule_formats_time_range_when_start_and_end_are_set(): void
    {
        $schedule = new ClassSchedule();
        $schedule->forceFill([
            'StartTime' => '2024-03-01 09:00:00',
            'EndTime' => '2024-03-01 10:30:00',
        ]);

        $this->assertSame('9:00am-10:30am', $schedule->schedule);
    }

    public function test_get_formatted_date_returns_empty_string_for_null(): void
    {
        $schedule = new ClassSchedule();

        $this->assertSame('', $schedule->getFormattedDate(null));
    }

    public function test_get_formatted_date_returns_m_d_y_format(): void
    {
        $schedule = new ClassSchedule();

        $this->assertSame('03-15-2024', $schedule->getFormattedDate('2024-03-15'));
    }
}
