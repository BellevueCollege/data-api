<?php

namespace App\Http\Controllers;
use App\Models\Student;
use App\Http\Controllers\ApiController;
use App\Http\Resources\StudentResource;
use stdClass;

class StudentController extends ApiController {

    /**
     * Get a student by username
     * 
     * Pass in the student's username (e.g. "john.doe") as a parameter, and the student's data will be returned.
     
     * **Note**: This endpoint is authenticated, and only available on the internal API server.
     * 
     * @param string $username Student username
     * 
     * @return StudentResource | stdClass
    **/
    public function getStudentByUsername($username)
    {
        try {
            $stu = Student::where('NTUserName', '=', $username)->firstOrFail();
            return new StudentResource($stu);
        } catch (\Exception $e) {
            return response()->json(new stdClass());
        }
    }

}
