<?php

namespace App\Http\Controllers\Forms;

use App\Models\Forms\TransferCreditEvaluation;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransferCreditEvaluationController extends Controller
{
    //
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
