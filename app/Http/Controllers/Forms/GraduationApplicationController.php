<?php

namespace App\Http\Controllers\Forms;

use App\Models\Forms\GraduationApplication;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GraduationApplicationController extends Controller
{
    //
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
