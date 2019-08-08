<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectPrefix extends Model
{
    /**
    * Model for course subject prefixes (many to one with Subjects)
    **/
     protected $table = 'SubjectsCoursePrefixes';
     protected $connection = 'cs';
     protected $primaryKey = 'CoursePrefixID';
     public $timestamps = false;
     public $incrementing = false;

     public function subject()
    {
        return $this->belongsTo('App\Models\Subject', 'SubjectID');
    }
}
