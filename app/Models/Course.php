<?php namespace App\Models;
  
use Illuminate\Database\Eloquent\Model;
use DB;

class Course extends Model
{
     protected $table = 'vw_Course';
     protected $connection = 'ods';
     protected $primaryKey = 'CourseKey';
     public $timestamps = false;
	 
     /**
     * In Eloquent relationships, you can also define the inverse of the relationship, in this case, the parent(s).
     * But, since I don't yet see a reason to access the CourseYearQuarters parent objects this way, I've commented out 
     * to reduce the amount of eager loading done.
     */
     /*public function courseyearquarters()
     {
         return $this->hasMany('App\Models\CourseYearQuarter', 'CourseID', 'CourseID');
     }*/
        
     /**
     * Define course descriptions child relationship
     */
     public function coursedescriptions()
     {
         return $this->hasMany('App\Models\CourseDescription', 'CourseID', 'CourseID');
     }

     /**
     * Course descriptions accessor
     **/
     public function getCourseDescriptionsAttribute() {
         return $this->coursedescriptions();
     }
     
     /** 
     * Footnote is a child model of Course
     * Because of the data organization, we can't use standard relationship definitions (see coursedescriptions) 
     * so I made this accessor version instead. It will return the expected collection of Footnote objects.
     **/
     public function getFootnotes()
     {
         $ids = array();
         if ( !is_null($this->FootnoteID1) ) {
             $ids[] = $this->FootnoteID1;
         }
         if ( !is_null($this->FootnoteID2) ) {
             $ids[] = $this->FootnoteID2;
         }
         $footnotes = Footnote::whereIn('FootnoteID', $ids)->get();

         return $footnotes;
     }
     
     /**
     * Accessor for title attribute
     * - Includes fall-through logic for using correct title
     **/
     public function getTitleAttribute() {
         $title = null;
         if ( !is_null($this->CourseTitle2) ) {
             $title = $this->CourseTitle2;
         } else {
             $title = $this->CourseTitle;
         }
         
         return $title;
     }
     
     /** 
     * Accessor for footnotes 
     **/
     public function getFootnotesAttribute() {
         return $this->getFootnotes();
     }
     
     /**
     * Get notes for course back in aggregated fashion
     **/
     public function getNoteAttribute() {
         $notes = "";
         
         $the_footnotes = $this->footnotes;

         //if footnotes, loop through them and aggregate to single string
         if ( !empty($the_footnotes) && $the_footnotes->count() > 0 ) {

             foreach ( $the_footnotes as $note ) { 
                 $notes = $notes . " " . $note->FootnoteText;
             }
             $notes = trim($notes);
         }
         
         if ( empty($notes) ) return null;
         else return $notes;
     }
     
     /**
     * Get if course is a common course
     **/
     public function getIsCommonCourseAttribute() {
         if ( strpos($this->CourseID, config('dataapi.common_course_char')) === false ) {
             return false;
         } else {
             return true;
         }
     }
     
     /** 
     * Scope to retrieve singular active course object as of current YearQuarter 
     **/
     public function scopeActive($query) {
         $yqr = YearQuarter::current()->first();

         return $this->scopeActiveAsOfYearQuarter($query, $yqr->YearQuarterID);
         /*return $query->where('EffectiveYearQuarterBegin', '<=', $yqr->YearQuarterID)
            ->where('EffectiveYearQuarterEnd', '>=', $yqr->YearQuarterID);*/
     }
     
     /** 
     * Scope to retrieve singular active course object as of current YearQuarter 
     **/
     public function scopeActiveAsOfYearQuarter($query, $yqrid) {

         return $query->where('EffectiveYearQuarterEnd', '>=', $yqrid)
                    ->where(function ($query) use ($yqrid) {
                        $query->where('EffectiveYearQuarterBegin', '<=', $yqrid)
                            ->orWhereNull('EffectiveYearQuarterBegin');
                    });
     }
}
?>