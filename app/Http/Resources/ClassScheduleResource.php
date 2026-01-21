<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $days = trim($this->DayID);
        if ($days == "ARR")
            $days = "To be arranged";
        elseif ($days == "DALY")
            $days = "Daily";
        elseif ($days != "") {
            $days = str_replace("U","Su",$days);
            $days = str_replace("R","Th",$days);
        }
        return [
            'days'          => $days,
            'room'          => $this->location,
            'roomDescription'     => $this->RoomDescr,
            'instructor'    => $this->InstructorName,
            'beginDate'     => $this->getFormattedDate($this->StartDate),
            'endDate'       => $this->getFormattedDate($this->EndDate),
            'schedule'      => trim($this->schedule),
        ];
    }
}
