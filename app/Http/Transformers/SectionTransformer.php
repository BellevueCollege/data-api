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
         * Get asssociated day
         */
        $days = $sec->day()->first();
        if (isset($day->Title)) {
            $days = $day->Title;
        } else {
            $days = "";
        }
        //dd($days);

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
        ];
    }
}
