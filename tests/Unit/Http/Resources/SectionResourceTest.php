<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\SectionResource;
use App\Models\Section;
use Tests\Support\ResourceTestCase;

/**
 * JSON shape of SectionResource matches the section payload used in class offerings.
 */
class SectionResourceTest extends ResourceTestCase
{
    public function test_serializes_section_contract(): void
    {
        $section = new Section();
        $section->forceFill([
            'ClassID' => '1001',
            'Section' => '01',
            'ItemNumber' => '1',
            'ClassNumber' => '1001',
            'InstructorName' => 'Test Instructor',
            'StartDate' => '2024-03-01 00:00:00',
            'EndDate' => '2024-06-01 00:00:00',
            'Room' => '  R101  ',
            'DayID' => 'MW',
            'StartTime' => '2024-03-01 09:00:00',
            'EndTime' => '2024-03-01 10:30:00',
            'RoomDescr' => 'Room 101',
        ]);

        $this->assertResourceContract(new SectionResource($section), [
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
        ]);
    }
}
