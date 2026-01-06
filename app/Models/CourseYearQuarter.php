<?php namespace App\Models;

use App\Models\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use DB;



class CourseYearQuarter extends Model
{
    // Allow for relationships based on composite keys
    use \Awobaz\Compoships\Compoships;
    
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
     * Set relationship for sections (use Compoships to handle composite keys)
     **/
    public function sections() {
        return $this->hasMany(
            Section::class,
            ['YearQuarterID', 'Department', 'CourseNumber'],
            ['YearQuarterID', 'Department', 'CourseNumber']
        );
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

    /**
     * Scope to only return distinct courses
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeDistinctCourses(Builder $query)
    {
        // previously was ->groupBy('CourseID', 'Department', 'CourseNumber', 'YearQuarterID', 'STRM')
        $query->select('CourseID', 'Department', 'CourseNumber', 'YearQuarterID', 'STRM')->distinct();
    }
}
?>