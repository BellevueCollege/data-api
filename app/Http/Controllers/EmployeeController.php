<?php

namespace App\Http\Controllers;

use App\Models\Employee;
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

class EmployeeController extends ApiController{

    /**
     * Get an employee by username
     * Status: active
    **/
    public function getEmployeeByUsername(Request $request, $username ){

        /**
         * Pull the 'type' peram to determine data format to return
         */

        $type = $request->input('type');

        /**
         * If type === directory return Directory type info, otherwise do standard
         */
        if ( 'directory' === $type )
        {
            $emp = EmployeeDirectory::where('ADAccountName', '=', $username)->first();
        } else {
            $emp = Employee::where('ADUserName', '=', $username)->where('EmployeeStatusCode','=','A')->first();
        }

        $data = $emp;
        //handle gracefully if null
        if ( ! is_null($emp) ) {
            if ( 'directory' === $type ) {
                $item = new Item($emp, new EmployeeDirectoryTransformer);
                $fractal = new Manager;
                $data = $fractal->createData($item)->toArray();
            } else {
                $item = new Item($emp, new EmployeeTransformer);
                $fractal = new Manager;
                $data = $fractal->createData($item)->toArray();
            }
        }

        return $this->respond($data);
    }

    /**
     * Get All Employees, Only Returning AD Username
     */
    public function getEmployees() {
        $emps = EmployeeDirectory::whereNotNull('ADAccountName')->get();
        $collection = new Collection($emps, new EmployeesTransformer, 'employees');
        $fractal = new Manager;
        $fractal->setSerializer(new CustomDataArraySerializer);
        $data = $fractal->createData($collection)->toArray();
        return $this->respond($data);
    }

}

