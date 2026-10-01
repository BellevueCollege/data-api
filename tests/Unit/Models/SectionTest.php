<?php

namespace Tests\Unit\Models;

use App\Models\Section;
use Tests\TestCase;

/**
 * Section location and schedule presentation for online versus classroom offerings.
 */
class SectionTest extends TestCase
{
    public function test_online_section_returns_null_location_and_online_schedule(): void
    {
        $section = new Section();
        $section->forceFill([
            'Section' => 'OAS',
            'Room' => 'R100',
            'StartTime' => '2024-03-01 09:00:00',
            'EndTime' => '2024-03-01 10:00:00',
        ]);

        $this->assertNull($section->location);
        $this->assertSame('Online', $section->schedule);
    }

    public function test_classroom_section_returns_trimmed_room_and_time_range(): void
    {
        $section = new Section();
        $section->forceFill([
            'Section' => '01',
            'Room' => '  R100  ',
            'StartTime' => '2024-03-01 09:00:00',
            'EndTime' => '2024-03-01 10:30:00',
        ]);

        $this->assertSame('R100', $section->location);
        $this->assertSame('9:00am-10:30am', $section->schedule);
    }
}
