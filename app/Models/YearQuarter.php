<?php namespace App\Models;
  
use Illuminate\Database\Eloquent\Model;
  
class YearQuarter extends Model
{
    /**
    * Model for a YearQuarter, BC's version of a term.
    **/
     protected $table = 'vw_YearQuarter';
     protected $connection = 'ods';
     protected $primaryKey = null; //Lumen will convert the YearQuarterID value to an integer if we make it aware of it
     //protected $primaryKey = 'YearQuarterID';
     
     public function scopeCurrent($query) {
        //Create now date/time object
        $timezone = new \DateTimeZone(config("app.timezone"));
        $now = new \DateTime();
        $now->setTimezone($timezone);
        $now_string = $now->format("Y-m-d 00:00:00.000");
        
        return $query->where('LastClassDay', '>=', $now_string)
            ->where('YearQuarterID', '<>', config('app.yearquarter_max'))
            ->orderBy('YearQuarterID', 'asc')
            ->take(1);
    }
}
?>