<?php namespace App\Http\Transformers;

use App\Models\ClassSchedule;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class ClassScheduleTransformer extends TransformerAbstract
{

    /**
    * Fractal transformer that defines how data for a class schedule
    * should be output in the API.
    **/

    public function transform(ClassSchedule $sch)
    {
        /**
         * Get asssociated days
         */
        $days = trim($sch->DayID);
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
            'room'          => $sch->location,
            'roomDescription'     => $sch->RoomDescr,
            'instructor'    => $sch->InstructorName,
            'beginDate'     => $sch->getFormattedDate($sch->StartDate),
            'endDate'       => $sch->getFormattedDate($sch->EndDate),
            'schedule'      => trim($sch->schedule),
        ];
    }
}
