<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Resources\EmployeeResource;
use App\Models\EmployeeDirectory;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
//use App\Exceptions\MissingParameterException;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use App\Http\Transformers\EmployeeTransformer;
use App\Http\Transformers\EmployeesTransformer;
use App\Http\Transformers\EmployeeDirectoryTransformer;
use App\Http\Serializers\CustomDataArraySerializer;
//use App\Http\Serializers\CustomDataArraySerializer;
use DB;

class EmployeeController extends ApiController
{
    /**
    * Get Employee by Username
    * 
    * @param \Illuminate\Http\Request $request
    * @param string $username Employee username
    * 
    * @return EmployeeResource | stdClass
    **/
    public function getEmployeeByUsername(Request $request, $username)
    {
        try{
            $emp = Employee::where('ADUserName', '=', $username)->where('EmployeeStatusCode', '=', 'A')->firstOrFail();
            return new EmployeeResource($emp);
        } catch (\Exception $e) {
            return response()->json(new stdClass());
        }
        
    }

    * 
    * @param \Illuminate\Http\Request $request
    * @param string $username Employee username
    * 
    * @return \Illuminate\Http\JsonResponse
    **/
    public function getDirectoryEmployeeByUsername(Request $request, $username)
    {

        $emp = EmployeeDirectory::where('ADAccountName', '=', $username)->first();


        $data = $emp;
        //handle gracefully if null
        if (! is_null($emp)) {
            $item = new Item($emp, new EmployeeDirectoryTransformer);
            $fractal = new Manager;
            $data = $fractal->createData($item)->toArray();
        }

        return $this->respond($data);
    }

    /**
    * Function to get a list of all directory employee usernames
    * 
    * @param \Illuminate\Http\Request $request
    * 
    * @return \Illuminate\Http\JsonResponse
    **/
    public function getDirectoryEmployees()
    {
        $emps = EmployeeDirectory::whereNotNull('ADAccountName')->get();
        $collection = new Collection($emps, new EmployeesTransformer, 'employees');
        $fractal = new Manager;
        $fractal->setSerializer(new CustomDataArraySerializer);
        $data = $fractal->createData($collection)->toArray();
        return $this->respond($data);
    }

    /**
    * Function to get a list of all directory employee usernames using substring search on DisplayName
    * 
    * @param \Illuminate\Http\Request $request
    * @param string $username Employee username
    * 
    * @return \Illuminate\Http\JsonResponse
    **/
    public function getDirectoryEmployeeDisplayNameSubstringSearch(Request $request, $username)
    {
        $emps = EmployeeDirectory::whereNotNull('ADAccountName')->where('DisplayName','like','%'.$username.'%')->get();
        $collection = new Collection($emps, new EmployeesTransformer, 'employees');
        $fractal = new Manager;
        $fractal->setSerializer(new CustomDataArraySerializer);
        $data = $fractal->createData($collection)->toArray();
        return $this->respond($data);
    }
}
