<?php
  
namespace App\Http\Controllers;
  
use App\Models\Student;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
//use App\Exceptions\MissingParameterException;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use App\Http\Transformers\StudentTransformer;
use App\Http\Serializers\CustomDataArraySerializer;
use DB;
  
class StudentController extends ApiController{
  
    /**
     Get a student by username 
     Status: active
    **/
    public function getStudentByUsername($username){
  
        $stu = Student::with('blocks','blocks.reason')->where('NTUserName', '=', $username)->first();
        
        $data = $stu;

        //handle gracefully if null
        if( !is_null($stu) ) {
            $item = new Item($stu, new StudentTransformer);

            $fractal = new Manager;
            $fractal->setSerializer(new CustomDataArraySerializer);

            $data = $fractal->createData($item)->toArray();
        }
        
        return $this->respond($data);
    }
 
}
?>