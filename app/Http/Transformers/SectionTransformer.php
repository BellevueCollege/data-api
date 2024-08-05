<?php namespace App\Http\Transformers;

use App\Models\Section;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class SectionTransformer extends TransformerAbstract
{

    /**
    * Fractal transformer that defines how data for a section
    * should be output in the API.
    **/

    public function transform(Section $sec)
    {
        /**
         * Get asssociated days
         */
        $days = trim($sec->DayID);
        if ($days == "ARR")
            $days = "To be arranged";
        elseif ($days == "DALY")
            $days = "Daily";
        elseif ($days != "") {
            $days = str_replace("U","Su",$days);
            $days = str_replace("R","Th",$days);
        }

        return [
            'id'            => $sec->ClassID,
            'section'       => $sec->Section,
            'itemNumber'    => $sec->ItemNumber,
            'classNumber'   => $sec->ClassNumber,
            'instructor'    => $sec->InstructorName,
            'beginDate'     => $sec->getFormattedDate($sec->StartDate),
            'endDate'       => $sec->getFormattedDate($sec->EndDate),
            'room'          => $sec->location,
            'days'          => $days,
            'schedule'      => trim($sec->schedule),
            'roomDescription' => $sec->RoomDescr,
        ];
    }
}
