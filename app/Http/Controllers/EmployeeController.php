<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Resources\EmployeeResource;

use App\Models\EmployeeDirectory;
use App\Http\Resources\EmployeeDirectoryDetailResource;
use App\Http\Resources\EmployeeDirectorySummaryCollection;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

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
            // If username contains @, search UserPrincipal
            if (strpos($username, '@') !== false) {
                $emp = Employee::where('UserPrincipalName', '=', $username)->where('EmployeeStatusCode', '=', 'A')->firstOrFail();
            } else {
                $emp = Employee::where('ADUserName', '=', $username)->where('EmployeeStatusCode', '=', 'A')->firstOrFail();
            }
            return new EmployeeResource($emp);
        } catch (\Exception $e) {
            return response()->json(new stdClass());
        }
        
    }


    /**
    * Get Employee from Directory by Username
    * 
    * Pass in the employee's username (e.g. "john.doe") as a parameter, and the employee's data will be returned.
    * **Note**: This endpoint is authenticated, but available on the public API server.
    * 
    * @param \Illuminate\Http\Request $request
    * @param string $username Employee username
    * 
    * @return EmployeeDirectoryDetailResource | stdClass
    **/
    public function getDirectoryEmployeeByUsername(Request $request, $username)
    {
        try {
            $emp = EmployeeDirectory::where('ADAccountName', '=', $username)->firstOrFail();
            return new EmployeeDirectoryDetailResource($emp);
        } catch (\Exception $e) {
            return response()->json(new stdClass());
        }
    }

    /**
    * Get all Employee usernames from the Directory
    * 
    * **Note**: This endpoint is authenticated, but available on the public API server.
    * 
    * @param \Illuminate\Http\Request $request
    * 
    * @return EmployeeDirectorySummaryCollection | stdClass
    **/
    public function getDirectoryEmployees()
    {
        try {
            $emps = EmployeeDirectory::whereNotNull('ADAccountName')->get();
            return new EmployeeDirectorySummaryCollection($emps);
        } catch (\Exception $e) {
            return response()->json(new stdClass());
        }
    }

    /**
    * Search for Directory Employees by DisplayName using substring search
    * 
    * Pass in the employee's partial display name (e.g. "john") as a parameter, and matching employee data will be returned.
    * 
    * **Note**: This endpoint is authenticated, but available on the public API server.
    * 
    * @param \Illuminate\Http\Request $request
    * @param string $username Employee username
    * 
    * @return EmployeeDirectorySummaryCollection | stdClass
    **/
    public function getDirectoryEmployeeDisplayNameSubstringSearch(Request $request, $username)
    {
        try {
            $emps = EmployeeDirectory::whereNotNull('ADAccountName')->where('DisplayName','like','%'.$username.'%')->get();
            return new EmployeeDirectorySummaryCollection($emps);
        } catch (\Exception $e) {
            return response()->json(new stdClass());
        }
    }
}
