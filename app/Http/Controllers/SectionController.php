<?php
  
namespace App\Http\Controllers;
  
use App\Models\Section;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
  
  
class SectionController extends ApiController{

    
    /*public function index(){
  
        $classes  = Section::all();
        return $this->respond($classes);
 
    }*/
  
    /**
    * Return sections based on a given YearQuarterID and department/subject
    **/
    /*public function getSections($yqrid, $dept){
  
        $sections = Section::where('YearQuarterID', '=', $yqrid)
			->where('Department', '=', $dept)
			->get();
  
        return $this->respond($sections);
    }*/
	
    /**
    * Return sections based on given YearQuarterID, department/subject, and course number.
    * Status: inactive
    * No route, not yet serialized
    **/
	public function getSections($yqrid, $dept, $coursenum){
  
        $sections = Section::where('YearQuarterID', '=', $yqrid)
			->where('Department', '=', $dept)
			->where('CourseNumber', '=', $coursenum)
			->get();
  
        return $this->respond($sections);
    }
}
?>