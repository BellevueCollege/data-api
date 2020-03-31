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

class EmployeeController extends ApiController
{

    /**
     * Get an employee by username
     * Status: active
     *
     * @OA\Get(
     *      path="/api/v1/internal/employee/{username}",
     *      operationId="getEmployeeByUsername",
     *      tags={"Employees", "Internal"},
     *      summary="Get employee information",
     *      description="Returns employee data",
     *      @OA\Parameter(
     *          name="username",
     *          description="Employee Username",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Employee")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *     security={
     *         {"jwtAuth": {"read:true"}}
     *     }
     * )
     */
    public function getEmployeeByUsername(Request $request, $username)
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
     * Get an employee by username from the directory
     * Status: active
     *
     * @OA\Get(
     *      path="/api/v1/directory/employee/{username}",
     *      operationId="getDirectoryEmployeeByUsername",
     *      tags={"Employees", "Directory"},
     *      summary="Get employee directory information",
     *      description="Returns employee directory data",
     *      @OA\Parameter(
     *          name="username",
     *          description="Employee Username",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/DirectoryEmployee")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *     security={
     *         {"jwtAuth": {"read:true"}}
     *     }
     * )
     */
    public function getDirectoryEmployeeByUsername(Request $request, $username)
    {
        $emp = Employee::where('ADUserName', '=', $username)->where('EmployeeStatusCode', '=', 'A')->first();

        $data = $emp;
        //handle gracefully if null
        if (! is_null($emp)) {
            $item = new Item($emp, new EmployeeTransformer);
            $fractal = new Manager;
            $data = $fractal->createData($item)->toArray();
        }

        return $this->respond($data);
    }


    /**
     * Get a list of all directory employee usernames
     * Status: active
     *
     * @OA\Get(
     *      path="/api/v1/directory/employees",
     *      operationId="getDirectoryEmployees",
     *      tags={"Employees", "Directory"},
     *      summary="Get directory employee usernames",
     *      description="Returns a list of usernames of employees in the directory",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="employees",
     *                  description="List of employees",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(
     *                          property="username",
     *                          type="string",
     *                          description="Employee username",
     *                      ),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *     security={
     *         {"jwtAuth": {"read:true"}}
     *     }
     * )
     */
    public function getDirectoryEmployees()
    {
        $emps = EmployeeDirectory::whereNotNull('ADAccountName')->get();
        $collection = new Collection($emps, new EmployeesTransformer, 'employees');
        $fractal = new Manager;
        $fractal->setSerializer(new CustomDataArraySerializer);
        $data = $fractal->createData($collection)->toArray();
        return $this->respond($data);
    }
}
