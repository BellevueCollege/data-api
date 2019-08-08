<?php namespace App\Models;
  
use Illuminate\Database\Eloquent\Model;
  
class Subject extends Model
{
    /**
    * Model for a course subject
    **/
     protected $table = 'Subjects';
     protected $connection = 'cs';
     protected $primaryKey = 'SubjectID';
     public $timestamps = false;
    
    public function prefixes()
    {
        return $this->hasMany('App\Models\SubjectPrefix', 'SubjectID');
    }
}
?>