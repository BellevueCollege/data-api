<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class SectionController extends ApiController{

    /**
    * Return sections based on given YearQuarterID, department/subject, and course number.
    * Status: inactive
    * No route, not yet serialized
    **/
    public function getSections($yqrid, $dept, $coursenum, Request $request){
        if ( $request->input('format') === 'strm') {
            $sections = Section::where('STRM', '=', $yqrid)
                ->where('Department', '=', $dept)
                ->where('CourseNumber', '=', $coursenum)
                ->get();
        } else {
            $sections = Section::where('YearQuarterID', '=', $yqrid)
                ->where('Department', '=', $dept)
                ->where('CourseNumber', '=', $coursenum)
                ->get();
        }


        return $this->respond($sections);
    }
}
