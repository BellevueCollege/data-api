<?php namespace App\Models;
  
use Illuminate\Database\Eloquent\Model;
  
class CourseDescription extends Model
{
     protected $table = 'vw_CourseDescription';
     protected $connection = 'ods';
     protected $primaryKey = 'CourseDescriptionID';
     public $timestamps = false;
	 
     /**
     * Definition for Course parent relationship
     * This inverse relationship isn't used, so it's commented out to reduce eager loading
     */
     /*
     public function course() {
        return $this->belongsTo('App\Models\Course', 'CourseID', 'CourseID');
     } 
     */
    
    /** 
    * Scope to retrieve active description for a course given a YearQuarterID 
    **/
    public function scopeActiveDescription($query, $yqr = null) {
        if ( is_null($yqr) ) {
            $cur_yqr = YearQuarter::current()->first();
            $yqr = $cur_yqr->YearQuarterID;
        }
        
        return $query->where('EffectiveYearQuarterBegin', '<=', $yqr)
            ->orderBy('EffectiveYearQuarterBegin', 'desc')
            ->take(1);
    }
}
?>