<?php

namespace App\Http\Controllers\Forms;

use App\Models\Forms\TransferCreditEvaluation;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransferCreditEvaluationController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/forms/evaluations/transfer-credit-evaluation",
     *      summary="Transfer Credit Evaluation Data",
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
     *         name="military",
     *         in="query",
     *         description="Student military designation",
     *         required=false,
     *         explode=false,
     *         @OA\Schema(
     *             type="boolean",
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="international_transcript",
     *         in="query",
     *         description="Student has an international transcript",
     *         required=false,
     *         explode=false,
     *         @OA\Schema(
     *             type="boolean",
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
     *      security={
     *           {"JWTAuth": {}}
     *      },
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       security={
     *           { "basicAuth": {} }
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
            'sid'                      => 'numeric|max:999999999',
            'email'                    => 'string|max:50',
            'received'                 => 'date',
            'program'                  => 'string|max:100',
            'program_code'             => 'string|max:10',
            'military'                 => 'boolean',
            'international_transcript' => 'boolean',
            'entry_id'                 => 'string|max:50',
        ]);

        /**
         * Look for pipe (|) separated program code at end of program string
         */
        if ($request->input('program_code')) {
            $program_code = $request->input('program_code');
        } else {
            preg_match('/\|\s*\K\S+/', $request->input('program'), $program_code);
            $program_code = $program_code[0] ?? null;
        }

        /**
         * Pull SID based on email if none is provided
         */
        if ($request->input('email') && !$request->input('sid')) {
            $stu = Student::where('Email', '=', $request->input('email', null))->first();
            $sid = $stu->SID;
        } else {
            $sid = $request->input('sid');
        }

        /**
         * Send to model
         */
        return TransferCreditEvaluation::createRecord(
            $sid,
            $request->input('received', null),
            $program_code ?? null,
            $request->input('military', null),
            $request->input('international_transcript', null),
            $request->input('entry_id', null),
        );
    }
}
