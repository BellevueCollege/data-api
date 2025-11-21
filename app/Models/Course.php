<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Course extends Model
{
    protected $table = 'vw_Course';
    protected $connection = 'ods';
    protected $primaryKey = 'PSCourseID';
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
    public function getCourseDescriptionsAttribute()
    {
        return $this->coursedescriptions();
    }

    /**
     * Accessor for title attribute
     * - Includes fall-through logic for using correct title
     **/
    public function getTitleAttribute()
    {
        $title = null;
        if (!is_null($this->CourseTitle2)) {
            $title = $this->CourseTitle2;
        } else {
            $title = $this->CourseTitle;
        }

        return $title;
    }

    /**
     * Get if course is a common course
     **/
    public function getIsCommonCourseAttribute()
    {
        if (strpos($this->CourseID, config('dataapi.common_course_char')) === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Scope to retrieve singular active course object as of current YearQuarter
     **/
    public function scopeActive($query)
    {
        $yqr = YearQuarter::current()->first();

        return $this->scopeActiveAsOfYearQuarter($query, $yqr->YearQuarterID);
        /*return $query->where('EffectiveYearQuarterBegin', '<=', $yqr->YearQuarterID)
            ->where('EffectiveYearQuarterEnd', '>=', $yqr->YearQuarterID);*/
    }

    /**
     * Scope to retrieve singular active course object as of current YearQuarter
     **/
    public function scopeActiveAsOfYearQuarter($query, $yqrid)
    {
        return $query->where(function ($query) use ($yqrid) {
            $query->where('EffectiveYearQuarterEnd', '>=', $yqrid)
                ->orWhereNull('EffectiveYearQuarterEnd');
        });
    }

    /** Scope to filter out transfer-in courses */
    public function scopeNotTransferIn($query)
    {
        return $query->where('CourseTitle2', '<>', 'Transfer In Course')
                ->where('CourseTitle', '<>', 'Transferred-In Course');
    }
}
