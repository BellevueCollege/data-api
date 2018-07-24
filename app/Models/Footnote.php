<?php namespace App\Models;
  
use Illuminate\Database\Eloquent\Model;
  
class Footnote extends Model
{
    /** 
    * The Footnote model is for, wait for it, footnote information.
    * It can be used as a child for Course or Section.
    **/
     protected $table = 'vw_Footnote';
     protected $connection = 'ods';
     protected $primaryKey = 'FootnoteID';
     public $timestamps = false;
}
?>