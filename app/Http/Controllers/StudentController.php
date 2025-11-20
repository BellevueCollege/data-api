<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Http\Controllers\ApiController;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use App\Http\Transformers\StudentTransformer;
use App\Http\Serializers\CustomDataArraySerializer;

class StudentController extends ApiController {

    /**
     * Get a student by username
     * 
     * @param string $username Student username
     * 
     * @return \Illuminate\Http\JsonResponse
    **/
    public function getStudentByUsername($username)
    {

        $stu = Student::where('NTUserName', '=', $username)->first();

        $data = $stu;

        //handle gracefully if null
        if ( !is_null($stu) ) {
            $item = new Item($stu, new StudentTransformer);

            $fractal = new Manager;
            $fractal->setSerializer(new CustomDataArraySerializer);

            $data = $fractal->createData($item)->toArray();
        }

        return $this->respond($data);
    }

}
