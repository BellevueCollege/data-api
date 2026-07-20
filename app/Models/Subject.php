<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    /**
    * Model for a course subject
    **/
     protected $table = 'vw_PS_CS_SubjectTable';
     protected $connection = 'ods';
     protected $primaryKey = null;
     public $timestamps = false;
     public $incrementing = false;

    /**
     * Always condense to the current PeopleSoft effective-dated row.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('latestEffective', function (Builder $query) {
            $query->latestEffective();
        });
    }

    /**
     * Courses belonging to this subject.
     *
     * @return HasMany
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'CourseSubject', 'SUBJECT');
    }

    /**
     * Course offerings (classes) belonging to this subject.
     *
     * @return HasMany
     */
    public function courseYearQuarters(): HasMany
    {
        return $this->hasMany(CourseYearQuarter::class, 'Department', 'SUBJECT');
    }

    /**
     * Scope to the latest EFFDT on or before today on SUBJECT.
     *
     * Prevent returning duplicate subjects or future-dated information.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeLatestEffective(Builder $query): Builder
    {
        $table = $query->getModel()->getTable();
        $asOfDate = now()->toDateString();

        return $query->where("{$table}.EFFDT", '=', function ($sub) use ($table, $asOfDate) {
            $sub->selectRaw('MAX(EFFDT)')
                ->from("{$table} as subject_effective")
                ->whereColumn('subject_effective.SUBJECT', "{$table}.SUBJECT")
                ->where('subject_effective.EFFDT', '<=', $asOfDate);
        });
    }
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
