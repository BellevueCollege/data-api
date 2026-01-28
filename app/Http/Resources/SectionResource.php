<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * Get asssociated days
         */
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
            'id'            => $this->ClassID,
            'section'       => $this->Section,
            'itemNumber'    => $this->ItemNumber,
            'classNumber'   => $this->ClassNumber,
            'instructor'    => $this->InstructorName,
            'beginDate'     => $this->getFormattedDate($this->StartDate),
            'endDate'       => $this->getFormattedDate($this->EndDate),
            'room'          => $this->location,
            'days'          => $days,
            'schedule'      => trim($this->schedule),
            'roomDescription' => $this->RoomDescr,
        ];
    }
}
