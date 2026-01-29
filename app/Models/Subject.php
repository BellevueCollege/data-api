<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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

     /**
      * Scope to Active Subjects in a Given Term
      * 
      * @param Builder $query
      * @param string $term
      * @param string $format |yrq|strm|
      * @return Builder
      */
    public function scopeActiveInTerm( Builder $query, string $term, string $format = 'yrq' ) {
        if ($format === 'strm') {
            $termColumn = 'STRM';
        } else {
            $termColumn = 'YearQuarterID';
        }
        return $query->join('vw_Class', 'vw_PSSubject.SUBJECT', '=', 'vw_Class.Department')
            ->where('vw_Class.' . $termColumn, $term)
            ->select('vw_PSSubject.SUBJECT', 'vw_PSSubject.DESCR as DESCR')
            ->groupBy('vw_PSSubject.SUBJECT', 'vw_PSSubject.DESCR')
            ->orderBy('vw_PSSubject.SUBJECT');
    }
}
