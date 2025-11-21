<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\SubjectCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use stdClass;

class SubjectController extends ApiController
{

    /**
    * List all Subjects
    *
    * @param Request $request
    
    * @return SubjectCollection | stdClass
    **/
    public function index(Request $request)
    {
        try {
            if ($request->input('filter') === 'active-credit') {
                $subjects = Subject::
                    where('DESCR', 'not like', 'CE%')
                    ->where('DESCR', 'not like', '% (Inactive)')
                    ->orderBy('SUBJECT', 'ASC')
                    ->get();
            } else {
                $subjects = Subject::
                orderBy('SUBJECT', 'ASC')
                ->get();
            }

            return new SubjectCollection($subjects);
        } catch (\Exception $e) {
            return response()->json(new stdClass());
        }
    }

    /**
    * Return Subject by Slug
    *
    * @param string $slug Subject slug
    *
    * @response array{subject: array{subject: string, name: string}}
    *
    * @return \Illuminate\Http\JsonResponse
    **/
    public function getSubject($slug)
    {
        try {
            $subject  = Subject::where('SUBJECT', '=', $slug)->firstOrFail();
            // We're wrapping in a subject array to match the response format
            // Normal wrapping methods don't work because of double 'subject'
            // in both the wrapper and the resource.
            // This also requires the response to be manually listed in the
            // docblock to ensure documentation is correct.
            return response()->json([
                'subject' => new SubjectResource($subject)
            ]);
        } catch (\Exception $e) {
            return response()->json(new stdClass());
        }
    }

    /**
    * List Subjects in a YRQ
    *
    * @param string $yqr YearQuarterID
    *
    * @return SubjectCollection | stdClass
    **/
    public function getSubjectsByYearQuarter($yqr)
    {
        try {
            $subjects = DB::connection('ods')
                ->table('vw_PSSubject')
                ->join('vw_Class', 'vw_PSSubject.SUBJECT', '=', 'vw_Class.Department')
                ->where('vw_Class.YearQuarterID', '=', $yqr)
                ->select('vw_PSSubject.SUBJECT', 'vw_PSSubject.DESCR as DESCR')
                ->groupBy('vw_PSSubject.SUBJECT', 'vw_PSSubject.DESCR')
                ->orderBy('vw_PSSubject.SUBJECT', 'asc')
                ->get();

            return new SubjectCollection($subjects);
        } catch (\Exception $e) {
            return response()->json(new stdClass());
        }
    }
}
