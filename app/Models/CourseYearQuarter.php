<?php namespace App\Models;

use App\Models\Section;
use Illuminate\Database\Eloquent\Model;
use DB;

class CourseYearQuarter extends Model
{
	/**
    * We can't use the name Class since it's a reserved word, so CourseYearQuarter to 
    * represent a class (course offered in a year quarter) it is. This is NOT the same 
    * as a section. 
    * Relationship: A Course is offered in a YearQuarter (CourseYearQuarter), those year quarter offerings have Sections.
    **/
     protected $table = 'vw_Class';
     protected $connection = 'ods';
     protected $primaryKey = null;
     public $timestamps = false;
	 
     
     public function course() {
         //scope relationship to retrieve only course relevant to YearQuarter of this CourseYearQuarter
		 return $this->belongsTo('App\Models\Course', 'CourseID', 'CourseID')->activeasofyearquarter($this->YearQuarterID);
	 }
     
     /** 
     * Section is a child model of CourseYearQuarter
     * Because of the data organization/definitions, we can't use standard relationship definitions (see course) 
     * so I made this accessor version instead. It will return the expected collection of child Section objects.
     **/
     public function getSectionsAttribute() {

         //Sections are children of CourseYearQuarter
         $sections = Section::where('YearQuarterID', '=', $this->YearQuarterID)
            ->where('Department', '=', $this->Department)
            ->where('CourseNumber', '=', $this->CourseNumber)
            ->get();

         return $sections;
     }
     
     /**
     * Accessor that wraps logic around which course title should be returned
     **/
     public function getTitleAttribute() {
         $title = $this->course->title;

         if ( is_null($title) ) {
             //if parent course title is null, default to CourseYearQuarter title
             $title = $this->CourseTitle;
        }  
        return $title;
     }
}
?>