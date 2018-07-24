<?php
  
namespace App\Http\Controllers;
  
use App\Models\Employee;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
//use App\Exceptions\MissingParameterException;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use App\Http\Transformers\EmployeeTransformer;
//use App\Http\Serializers\CustomDataArraySerializer;
use DB;
  
class EmployeeController extends ApiController{
  
    /**
     Get an employee by username 
     Status: active
    **/
    public function getEmployeeByUsername($username){
  
        $emp = Employee::where('ADUserName', '=', $username)->where('EmployeeStatusCode','=','A')->first();
        
        $data = $emp;
        //handle gracefully if null
        if( !is_null($emp) ) {
            $item = new Item($emp, new EmployeeTransformer);

            $fractal = new Manager;
        
            //$fractal->setSerializer(new CustomDataArraySerializer);
            $data = $fractal->createData($item)->toArray();
        }
        
        return $this->respond($data);
    }
 
}
?>