<?php

namespace App\Http\Controllers\Forms;

use App\Models\Forms\GraduationApplication;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class GraduationApplicationController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/forms/evaluations/graduation-application",
     *      summary="Graduation Application Data",
     *      @OA\Parameter(
     *         name="sid",
     *         in="query",
     *         description="Student ID Number",
     *         required=false,
     *         explode=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Email Address",
     *         required=false,
     *         explode=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="received",
     *         in="query",
     *         description="Received Date",
     *         required=false,
     *         explode=false,
     *         @OA\Schema(
     *             type="date",
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="quarter",
     *         in="query",
     *         description="Quarter Code (B901)",
     *         required=false,
     *         explode=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="program",
     *         in="query",
     *         description="Program name with pipe-separated program code (Program Name | 5AA)",
     *         required=false,
     *         explode=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="program_code",
     *         in="query",
     *         description="Program code only (5AA)",
     *         required=false,
     *         explode=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="concentration",
     *         in="query",
     *         description="Concentration (not used, but will write to DB)",
     *         required=false,
     *         explode=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="diploma_name",
     *         in="query",
     *         description="Student's name as printed on diploma",
     *         required=false,
     *         explode=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="requirements_year",
     *         in="query",
     *         description="Requirements year (2019-2020)",
     *         required=false,
     *         explode=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="entry_id",
     *         in="query",
     *         description="Entry ID from source system",
     *         required=false,
     *         explode=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="is_update",
     *         in="query",
     *         description="Is this an update of an existing record? (Still records as a new record)",
     *         required=false,
     *         explode=false,
     *         @OA\Schema(
     *             type="boolean",
     *         )
     *      ),
     *      security={
     *           {"JWTAuth": {}}
     *      },
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       security={
     *           { "basicAuth": {"write:true"} }
     *       }
     *     )
     *
     * Records Graduation Application and returns nothing
     */
    public function post(Request $request)
    {
        /**
         * Validate Request
         */
        $this->validate($request, [
            'sid'               => 'numeric|max:999999999',
            'email'             => 'string|max:50',
            'received'          => 'date',
            'quarter'           => 'string|max:4',
            'program'           => 'string|max:100',
            'program_code'      => 'string|max:10',
            'concentration'     => 'string|max:50',
            'diploma_name'      => 'string|max:100',
            'requirements_year' => 'string|max:20',
            'entry_id'          => 'string|max:50',
            'is_update'         => 'boolean',
        ]);
        /**
         * Look for pipe (|) separated program code at end of program string
         */
        if ($request->input('program_code'))
        {
            $program_code = $request->input('program_code');
        }
        else
        {
            preg_match('/\|\s*\K\S+/', $request->input('program'), $program_code);
            $program_code = $program_code[0] ?? null;
        }

        /**
         * Pull SID based on email if none is provided
         */
        if ($request->input('email') && !$request->input('sid'))
        {
            $stu = Student::where('Email', '=', $request->input('email', null))->first();
            $sid = $stu->SID;
        }
        else
        {
            $sid = $request->input('sid');
        }

        /**
         * Send to model
         */
        return GraduationApplication::createRecord(
            $sid,
            $request->input('email', null), // doesn't write
            $request->input('received', null),
            $request->input('quarter', null),
            $program_code ?? null,
            $request->input('concentration', null),
            $request->input('diploma_name', null),
            $request->input('requirements_year', null),
            $request->input('entry_id', null),
            $request->input('is_update', false)
        );
    }
}
