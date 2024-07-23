<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\BelongsTo;

class ClassSchedule extends Model
{
    /**
    * Model for a section offered in a CourseYearQuarter.
    **/
     protected $table = 'vw_ClassSchedule';
     protected $connection = 'ods';
     protected $primaryKey = 'PSClassScheduleID';
     public $incrementing = false;
     public $timestamps = false;

     /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Defines child relationship for the schedule's Days details
    **/
    public function day() {
        return $this->hasOne('App\Models\Day', 'DayID', 'DayID');
    }

    /**
     * Get the section that owns the schedule.
    **/
    public function section() /*: BelongsTo*/
    {
        return $this->belongsTo(Section::class, 'PSClassID', 'PSClassID');
    }

     /**
     * This accessor wraps some logic around the room/location for a class.
     **/
    public function getLocationAttribute() {
        /*
        if ( $this->isOnlineSection() ) {
            //can't have a room for an online section
            return null;
        } else {
            return trim($this->Room);
        }
        */
        return trim($this->Room);
    }

     /**
     * This accessor wraps logic around the section schedule
     **/
    public function getScheduleAttribute() {
        $schedule = "";
        if ( !is_null($this->StartTime) && !is_null($this->EndTime) ) {
            $st = new \DateTime($this->StartTime);
            $et = new \DateTime($this->EndTime);
            $st_str = $st->format("g:ia");
            $et_str = $et->format("g:ia");
            //$schedule = $this->day->Title ?? '' . " " . $st_str . "-" . $et_str;
            $schedule = $st_str . "-" . $et_str;
        }
        /*
        if ( $this->isOnlineSection() ) {
             //online section so don't include day/time info
             $schedule = "Online";
        }
        */
        return $schedule;
    }

    /**
    * Format a date to the format we want to use for section dates
    **/
    public function getFormattedDate($date) {
        $date_str = "";
        if ( !is_null($date) ) {
         $dateobj = new \DateTime($date);
         $date_str = $dateobj->format("m-d-Y");
        }
        return $date_str;
    }

    /**
    * Logic to determine of section is an online section
    **/
    /*
    protected function isOnlineSection () {
        if ( !empty($this->Section) && $this->Section == "OAS" ) {
            return true;
        } else {
            return false;
        }
    }
    */
}
