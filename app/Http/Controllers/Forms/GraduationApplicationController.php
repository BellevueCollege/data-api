<?php

namespace App\Http\Controllers\Forms;

use App\Models\Forms\GraduationApplication;
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
            'sid'           => 'required|numeric|max:999999999',
            'received'      => 'date',
            'quarter'       => 'string|max:4',
            'program'       => 'string|max:100',
            'program_code'  => 'string|max:6',
            'concentration' => 'string|max:50',
            'diploma_name'  => 'string|max:100',
            'entry_id'      => 'string|max:50',
            'is_update'     => 'boolean',
        ]);

        /**
         * Look for pipe (|) separated program code at end of program string
         */
        $program_code;
        if ($request->input('program_code'))
        {
            $program_code = $request->input('program_code');
        }
        else
        {
            preg_match('/\|\s*\K\S+/', $request->input('program'), $program_code);
            $program_code = $program_code[0] ?? null;
        }

        return GraduationApplication::createRecord(
            $request->input('sid', null),
            $request->input('received', null),
            $request->input('quarter', null),
            $program_code ?? null,
            $request->input('concentration', null),
            $request->input('diploma_name', null),
            $request->input('entry_id', null),
            $request->input('is_update', false)
        );
    }
}
