<?php
  
namespace App\Http\Controllers;
  
use App\Models\CourseYearQuarter;
use App\Http\Transformers\CourseYearQuarterTransformer;
use App\Http\Controllers\Controller;
use App\Http\Serializers\CourseDataArraySerializer;
use App\Http\Serializers\CustomDataArraySerializer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use DB;

class CourseYearQuarterController extends ApiController {
  
    const WRAPPER = "classes";
    
    /**
    * Return a CourseYearQuarter based on a YearQuarterID, subject, and course number.
    **/
    public function getCourseYearQuarter($yqrid, $subjectid, $coursenum) {
        
        //DB::connection('ods')->enableQueryLog();
        $cyq = CourseYearQuarter::where('YearQuarterID', '=', $yqrid)
            ->where('Department', '=', $subjectid)
            ->where('CourseNumber', '=', $coursenum)
            ->first();
        //$queries = DB::connection('ods')->getQueryLog();
        //dd($queries);
        //var_dump($cyq);
        $data = $cyq;
        
        //handle gracefully if null
        if ( !is_null($cyq) ) {
            $item = new Item($cyq, new CourseYearQuarterTransformer, self::WRAPPER);
         
            //define serializer
            $fractal = new Manager;
            $fractal->setSerializer(new CustomDataArraySerializer);
            $data = $fractal->createData($item)->toArray();
        }
         
        return $this->respond($data);
    }
    
    /**
    * Return CourseYearQuarters based on a given YearQuarterID and subject.
    **/
    public function getCourseYearQuartersBySubject($yqrid, $subjectid) {

        //DB::connection('cs')->enableQueryLog();
        $cyqs = DB::connection('ods')
            ->table('vw_Class')
            ->where('YearQuarterID', '=', $yqrid)
            ->where('Department', '=', $subjectid)
            ->select('CourseID', 'Department', 'CourseNumber', 'YearQuarterID')
            ->groupBy('CourseID', 'Department', 'CourseNumber', 'YearQuarterID')
            ->orderBy('CourseNumber', 'asc')
            ->get(); 
        
         //$queries = DB::connection('cs')->getQueryLog();
         //dd($queries);  
        
        $data = $cyqs;
        
        if ( !is_null($cyqs) && !$cyqs->isEmpty() ) {
            //When using the Eloquent query builder, we must "hydrate" the results back to collection of objects
            $cyqs_hydrated = CourseYearQuarter::hydrate($cyqs->toArray());
            $collection = new Collection($cyqs_hydrated, new CourseYearQuarterTransformer, self::WRAPPER);
         
            //define serializer
            $fractal = new Manager;
            $fractal->setSerializer(new CustomDataArraySerializer);
            $data = $fractal->createData($collection)->toArray();
        }

        return $this->respond($data);
    }
}
?>