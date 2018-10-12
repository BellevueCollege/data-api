<?php namespace App\Http\Transformers;

use App\Models\Course;
use App\Models\YearQuarter;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class CourseTransformer extends TransformerAbstract {

    /**
    * Fractal transformer for a Course. Defines how 
    * Course data will be output in the API.
    **/
    
    public function transform(Course $course)
    {
        //filter to get the active course description
        $all_desc = $course->coursedescriptions();
        $cd_desc = null;
        if ( !is_null($all_desc) ) 
        {
            $cd_active = $all_desc->activedescription()->first();
            $cd_desc = $cd_active->Description;
        }
        
        $subject = null;
        $course_num = null;
        $pieces = preg_split('/[\s]+/', $course->CourseID);
        //dd($pieces);
        if ( count($pieces) == 1 ) {
            $subject = trim(substr(trim($pieces[0]), 0, 5));
            $course_num = substr(trim($pieces[0]), 5);
        } elseif ( count($pieces) > 1 ) {
            $subject = $pieces[0];
            $course_num = $pieces[1];
        }
        
        return [
            'title'             => $course->title,
            'subject'           => $subject,
            'courseNumber' 	    => $course_num,
            'courseId'          => $course->CourseID,
            'description'       => utf8_encode($cd_desc),
            'note'              => $course->note,
            'credits'           => $course->Credits,
            'isVariableCredits'  => (bool)$course->VariableCredits,
            'isCommonCourse'    => $course->isCommonCourse,
        ];
    }
	
}