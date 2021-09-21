<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    /**
    * Model for a section offered in a CourseYearQuarter.
    **/
     protected $table = 'vw_Class';
     protected $connection = 'ods';
     protected $primaryKey = 'PSClassID';
     public $incrementing = false;
     public $timestamps = false;

     /**
     * Defines child relationship for the section's schedule
     **/
     public function day() {
         return $this->hasOne('App\Models\Day', 'DayID', 'DayID');
     }

     /**
     * This accessor wraps some logic around the room/location for a class.
     **/
     public function getLocationAttribute() {
         if ( $this->isOnlineSection() ) {
             //can't have a room for an online section
             return null;
         } else {
             return trim($this->Room);
         }
     }

     /**
     * This accessor wraps logic around the section schedule
     **/
     public function getScheduleAttribute() {
         $st = new \DateTime($this->StartTime);
         $et = new \DateTime($this->EndTime);

         $st_str = $st->format("g:ia");
         $et_str = $et->format("g:ia");

         $schedule = $this->day->Title ?? '' . " " . $st_str . "-" . $et_str;
         if ( $this->isOnlineSection() ) {
             //online section so don't include day/time info
             $schedule = "Online";
         }
         return $schedule;

     }

     /**
     * Format a date to the format we want to use for section dates
     **/
     public function getFormattedDate($date) {
         $dateobj = new \DateTime($date);

         return $dateobj->format("m-d-Y");
     }

     /**
     * Logic to determine of section is an online section
     **/
     protected function isOnlineSection () {
         if ( !empty($this->Section) && $this->Section == "OAS" ) {
             return true;
         } else {
             return false;
         }
     }
}
?>
