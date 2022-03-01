<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\SubjectPrefix;
use App\Http\Transformers\SubjectTransformer;
use App\Http\Controllers\Controller;
use App\Http\Serializers\CustomDataArraySerializer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use DB;

class SubjectController extends ApiController
{

    const WRAPPER = "subjects";

  /**
  * Return all subjects
  **/
    public function index(Manager $fractal, Request $request)
    {
        if ($request->input('filter') === 'active-credit') {
            $subjects = Subject::
                where('DESCR', 'not like', 'CE%')
                ->where('DESCR', 'not like', '% (Inactive)')
                ->orderBy('SUBJECT', 'ASC')
                ->get();
        } else {
            $subjects = Subject::
            orderBy('SUBJECT', 'ASC')
            ->get();
        }
        $data = $subjects;
        if (!is_null($subjects)) {
            $collection = new Collection($subjects, new SubjectTransformer, self::WRAPPER);

            $fractal->setSerializer(new CustomDataArraySerializer);
            $data = $fractal->createData($collection)->toArray();
        }

        return $this->respond($data);
    }

    /**
    * Return a subject based on a provided slug
    **/
    public function getSubject($slug)
    {

        $subject  = Subject::where('SUBJECT', '=', $slug)->first();

        $data = $subject;
        //handle gracefully if null
        if (!is_null($subject)) {
            $item = new Item($subject, new SubjectTransformer, "subject");

            $fractal = new Manager;

            $fractal->setSerializer(new CustomDataArraySerializer);
            $data = $fractal->createData($item)->toArray();
        }

        return $this->respond($data);
    }

    /**
    * Return subjects based on a provided YearQuarterID
    **/
    public function getSubjectsByYearQuarter($yqr)
    {

        //DB::connection('cs')->enableQueryLog();
        $subjects = DB::connection('ods')
            ->table('vw_PSSubject')
            ->join('vw_Class', 'vw_PSSubject.SUBJECT', '=', 'vw_Class.Department')
            ->where('vw_Class.YearQuarterID', '=', $yqr)
            ->select('vw_PSSubject.SUBJECT', 'vw_PSSubject.DESCR as DESCR')
            ->groupBy('vw_PSSubject.SUBJECT', 'vw_PSSubject.DESCR')
            ->orderBy('vw_PSSubject.SUBJECT', 'asc')
            ->get();
         //$queries = DB::connection('cs')->getQueryLog();
         //dd($queries);

         $data = $subjects;
        if (!is_null($subjects) && !$subjects->isEmpty()) {
            //When using the Eloquent query builder, we must "hydrate" the results back to collection of objects
            $subjects_hydrated = Subject::hydrate($subjects->toArray());
            $collection = new Collection($subjects_hydrated, new SubjectTransformer, self::WRAPPER);

            //set data serializer
            $fractal = new Manager;
            $fractal->setSerializer(new CustomDataArraySerializer);
            $data = $fractal->createData($collection)->toArray();
        }

         return $this->respond($data);
    }
}
