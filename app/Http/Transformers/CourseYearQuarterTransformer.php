<?php namespace App\Http\Transformers;

use App\Models\CourseYearQuarter;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class CourseYearQuarterTransformer extends TransformerAbstract {

    /**
    * Fractal transformer for a CourseYearQuarter. Defines how 
    * CourseYearQuarter data will be output in the API.
    **/
    
     /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'sections'
    ];

    public function transform(CourseYearQuarter $cyq)
    {
        //filter to get the active course description
        $all_desc = $cyq->course->coursedescriptions();        
        $cd_desc = null;
        if ( !is_null($all_desc) ) 
        {
            $cd_active = $all_desc->activedescription($cyq->YearQuarterID)->first();
            $cd_desc = $cd_active->Description;
        }
        
        return [
            'title'             => $cyq->title,
            'subject'           => trim($cyq->Department),
            'courseNumber' 	    => trim($cyq->CourseNumber),
            'description'       => utf8_encode($cd_desc),
            'note'              => $cyq->course->note,
            'credits'           => $cyq->course->Credits,
            'quarter'           => $cyq->YearQuarterID,
            'isVariableCredits'  => (bool)$cyq->course->VariableCredits,
            'isCommonCourse'    => $cyq->course->isCommonCourse,
        ];
    }
    
    /**
     * Include sections for class
     *
     * @return League\Fractal\Resource\Collection
     */
    public function includeSections(CourseYearQuarter $cyq)
    {
        $sections = $cyq->getSectionsAttribute();

        if ( $sections->count() == 0) {
            //return null resource
            return $this->null();
        }
        //set resource key as false so data isn't "double wrapped" with a section identifier
        $sections_transformed = $this->collection($sections, new SectionTransformer, false);

        return $sections_transformed;
    }
	
}