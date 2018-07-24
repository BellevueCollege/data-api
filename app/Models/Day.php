<?php namespace App\Models;
  
use Illuminate\Database\Eloquent\Model;
  
class Day extends Model
{
    /* Model for accessing schedule Day information, because this data is oddly 
    * normalized in the ODS for some reason.
    **/
    protected $table = 'vw_Day';
    protected $connection = 'ods';
    protected $primaryKey = 'DayID';
    public $timestamps = false;
	 
}
?>