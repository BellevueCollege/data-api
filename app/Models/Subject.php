<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    /**
    * Model for a course subject
    **/
     protected $table = 'vw_PSSubject';
     protected $connection = 'ods';
     protected $primaryKey = null;
     public $timestamps = false;
     public $incrementing = false;
}
