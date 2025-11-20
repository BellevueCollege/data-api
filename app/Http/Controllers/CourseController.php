<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Exceptions\MissingParameterException;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use App\Http\Transformers\CourseTransformer;
use App\Http\Serializers\CustomDataArraySerializer;
use DB;

class CourseController extends ApiController{

    const WRAPPER = "courses";

    /**
     * Get all courses.
     * 
     * Inactive: No active route exists, should not be used
     *
     * @return \Illuminate\Http\JsonResponse
     **/
    public function index(){

        $courses  = Course::all();
        return $this->respond($courses);

    }

    /**
     * Get a course based on a given CourseID.
     * 
     * @param int $courseid
     * 
     * @return \Illuminate\Http\JsonResponse
    **/
    public function getCourse($courseid)
    {

        $course  = Course::where('CourseID', '=', $courseid)->active()->first();

        $data = $course;
        if ( !is_null($course) ) {
            $item = new Item($course, new CourseTransformer, "course");

            $fractal = new Manager;
            $fractal->setSerializer(new CustomDataArraySerializer);
            $data = $fractal->createData($item)->toArray();
        }
        return $this->respond($data);
    }

    /**
     * Get a course based on a given subject and course number.
     * 
     * @param string $subject
     * @param string $coursenum
     * 
     * @return \Illuminate\Http\JsonResponse
    **/
    public function getCourseBySubjectAndNumber($subject, $coursenum){

        $course  = Course::where('CourseSubject', '=', $subject)
                    ->where('CatalogNumber', '=', $coursenum)
                    ->active()
                    ->first();

        $data = $course;
        if ( !is_null($course) ) {
            $item = new Item($course, new CourseTransformer, "course");

            $fractal = new Manager;
            $fractal->setSerializer(new CustomDataArraySerializer);
            $data = $fractal->createData($item)->toArray();
        }
        return $this->respond($data);
    }

    /**
     * Get active courses based on a given subject.
     * 
     * @param string $subject
     * 
     * @return \Illuminate\Http\JsonResponse
    **/
    public function getCoursesBySubject($subject)
    {

        $courses = Course::where('CourseSubject', '=', $subject)
            ->where(function ($query) {
                $query->where('CourseTitle2', '<>', 'Transfer In Course')
                ->where('CourseTitle', '<>', 'Transferred-In Course');
            })
            ->orderBy('CourseId', 'asc')
            ->active()
            ->get();

        $data = $courses;
        if ( !is_null($courses) ) {
            $collection = new Collection($courses, new CourseTransformer, self::WRAPPER);

            $fractal = new Manager;
            $fractal->setSerializer(new CustomDataArraySerializer);
            $data = $fractal->createData($collection)->toArray();
        }
        return $this->respond($data);
    }

    /**
     * Get multiple courses based on course numbers passed via courses[] query parameter
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
    **/
    public function getMultipleCourses(Request $request){

        $course_input = $request->input('courses');
        if ( !is_array($course_input) ) {
            //be nice and don't fail non-array input for the case there's a single value
            $course_input[] = $course_input;
        }
        if ( !empty($course_input) && count($course_input) > 0 ) {
            //valid courses parameter so get courses
            //strip whitespace so we can do db comparison
            $courses_stripped = $course_input;
            array_walk($courses_stripped, array($this, 'stripWhitespace'));
            $placeholder = implode(', ', array_fill(0, count($courses_stripped), '?'));

            $courses = Course::whereRaw("REPLACE(CourseID, ' ', '') IN ($placeholder)", $courses_stripped)->active()->get();

            $data = $courses;
            if ( !is_null($courses) ) {
                $collection = new Collection($courses, new CourseTransformer, self::WRAPPER);

                // define serializer
                $fractal = new Manager;
                $fractal->setSerializer(new CustomDataArraySerializer);
                $data = $fractal->createData($collection)->toArray();
            }

            return $this->respond($data);
        } else {
            throw new MissingParameterException("Invalid courses[] parameter provided.");
        }

    }

    /**
    * Function to strip whitespace via regex
    * 
    * @param string $value
    * @param string $key
    * 
    * @return void
    **/
    private function stripWhitespace(&$value, $key){
        $value = preg_replace('/\s+/', '', $value);
    }

}

