<?php namespace App\Http\Transformers;

use App\Models\Course;
use App\Models\YearQuarter;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;
use Illuminate\Support\Facades\Log;

class CourseTransformer extends TransformerAbstract
{

    /**
    * Fractal transformer for a Course. Defines how
    * Course data will be output in the API.
    **/

    public function transform(Course $course)
    {
        //filter to get the active course description
        $all_desc = $course->coursedescriptions();
        $cd_desc = null;

        if (!is_null($all_desc)) {
            $cd_active = $all_desc->activedescription()->first();

            if (!empty($cd_active)) {
                $cd_desc = utf8_encode($cd_active->Description);
            }
        }


        return [
            'title'             => $course->title,
            'subject'           => $course->CourseSubject,
            'courseNumber'      => $course->CatalogNumber,
            'courseId'          => $course->CourseID,
            'ctcCourseId'       => $course->PSCourseID,
            'description'       => $cd_desc,
            'note'              => $course->note,
            'credits'           => $course->Credits,
            'isVariableCredits'  => (bool)$course->VariableCredits,
            'isCommonCourse'    => $course->isCommonCourse,
        ];
    }
}
