<?php

namespace App\Http\Controllers;

use App\Models\CourseYearQuarter;
use App\Http\Resources\CourseYearQuarterResource;
use App\Http\Resources\CourseYearQuarterCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class CourseYearQuarterController extends ApiController {

    const WRAPPER = "classes";

    /**
    * Return a CourseYearQuarter based on a YearQuarterID, subject, and course number.
    * 
    * @param string $yqrid YearQuarterID
    * @param string $subjectid SubjectID
    * @param string $coursenum CourseNumber
    * @param \Illuminate\Http\Request $request
    * 
    * @return CourseYearQuarterResource | \Illuminate\Http\JsonResponse
    **/
    public function getCourseYearQuarter($yqrid, $subjectid, $coursenum, Request $request)
    {
        $validated = $request->validate([
            'format' => 'sometimes|string|in:yrq,strm',
        ]);
        try {
            $selector = $request->input('format') === 'strm' ? 'STRM' : 'YearQuarterID';
            $cyq = CourseYearQuarter::where($selector, '=', $yqrid)
                ->where('Department', '=', $subjectid)
                ->where('CourseNumber', '=', $coursenum)
                ->firstOrFail();
            
            return new CourseYearQuarterResource($cyq);
        } catch (\Exception $e) {
            return response()->json(['classes' => []], 404);
        }
    }

    /**
    * Return CourseYearQuarters based on a given YearQuarterID and subject.
    * 
    * @param string $yqrid YearQuarterID
    * @param string $subjectid SubjectID
    * @param \Illuminate\Http\Request $request
    * 
    * @return CourseYearQuarterCollection | \Illuminate\Http\JsonResponse
    **/
    public function getCourseYearQuartersBySubject($yqrid, $subjectid, Request $request)
    {
        $validated = $request->validate([
            'format' => 'sometimes|string|in:yrq,strm',
        ]);
        $selector = $request->input('format') === 'strm' ? 'STRM' : 'YearQuarterID';
        try {
            $cyqs = CourseYearQuarter::where($selector, '=', $yqrid)
                ->where('Department', '=', $subjectid)
                ->distinctCourses()
                ->orderBy('CourseNumber', 'asc')
                ->get();
            return new CourseYearQuarterCollection($cyqs);
        } catch (\Exception $e) {
            return response()->json(['classes' => []], 404);
        }
    }
}
