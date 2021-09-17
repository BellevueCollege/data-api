<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    /**
    * Model for a course subject
    **/
     protected $table = 'SYSADM_CS.PS_SUBJECT_TBL';
     protected $connection = 'datalink';
     protected $primaryKey = null;
     public $timestamps = false;
     public $incrementing = false;
}
