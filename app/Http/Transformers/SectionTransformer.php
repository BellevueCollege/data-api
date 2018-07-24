<?php namespace App\Http\Transformers;

use App\Models\Section;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class SectionTransformer extends TransformerAbstract {

    /**
    * Fractal transformer that defines how data for a section 
    * should be output in the API.
    **/

    public function transform(Section $sec)
    {
        return [
            'crn'       	=> $sec->ClassID,
            'courseSection' => $sec->Section,
			'instructor'	=> $sec->InstructorName,
            'beginDate'     => $sec->getFormattedDate($sec->StartDate),
            'endDate'       => $sec->getFormattedDate($sec->EndDate),
			//'building'		=> $sec->Building,
			'room'			=> $sec->location,
            'schedule'      => trim($sec->schedule),
        ];
    }
	
}