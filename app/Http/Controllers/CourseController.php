<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Http\Resources\CourseResource;
use App\Http\Resources\CourseCollection;
use Illuminate\Http\Request;
use App\Exceptions\MissingParameterException;

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
     * Get Course by CourseID
     * 
     * Course ID is a combination of the Course Subject and Catalog Number, space separated
     * 
     * @param int $courseid
     * 
     * @return CourseResource | \Illuminate\Http\JsonResponse
    **/
    public function getCourse($courseid, Request $request)
    {
        try {
            $course  = Course::where('CourseID', '=', $courseid)->active()->firstOrFail();
            return new CourseResource($course);
        } catch (\Exception $e) {
            return response()->json(['courses' => []], 404);
        }
    }

    /**
     * Get Course by Subject and Catalog Number
     * 
     * @param string $subject
     * @param string $coursenum
     * 
     * @return CourseResource | \Illuminate\Http\JsonResponse
    **/
    public function getCourseBySubjectAndNumber($subject, $coursenum){
        try {
            $course  = Course::where('CourseSubject', '=', $subject)
                        ->where('CatalogNumber', '=', $coursenum)
                        ->active()
                        ->firstOrFail();
            return new CourseResource($course);
        } catch (\Exception $e) {
            return response()->json(['courses' => []], 404);
        }
    }

    /**
     * List Active Courses by Subject
     * 
     * @param string $subject
     * 
     * @return CourseCollection | \Illuminate\Http\JsonResponse
    **/
    public function getCoursesBySubject($subject)
    {
        try {
            $courses = Course::where('CourseSubject', '=', $subject)
                ->notTransferIn()
                ->active()
                ->orderBy('CourseId', 'asc')
                ->get();

            return new CourseCollection($courses);
        } catch (\Exception $e) {
            return response()->json(['courses' => []], 404);
        }
    }

    /**
     * Get Multiple Courses by CourseID
     * 
     * Courses are passed as an array via the courses[] query parameter
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return CourseCollection | \Illuminate\Http\JsonResponse
    **/
    public function getMultipleCourses(Request $request)
    {
        $validated = $request->validate([
            'courses' => 'required|array',
        ]);
        try {
            $course_input = $request->input('courses');

            if ( !empty($course_input) && count($course_input) > 0 ) {
                //valid courses parameter so get courses
                //strip whitespace so we can do db comparison
                $courses_stripped = $course_input;
                array_walk($courses_stripped, array($this, 'stripWhitespace'));
                $placeholder = implode(', ', array_fill(0, count($courses_stripped), '?'));

                $courses = Course::whereRaw("REPLACE(CourseID, ' ', '') IN ($placeholder)", $courses_stripped)->active()->get();
                return new CourseCollection($courses);
            } else {
                throw new MissingParameterException("Invalid courses[] parameter provided.");
            }
        } catch (\Exception $e) {
            return response()->json(['courses' => []], 404);
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