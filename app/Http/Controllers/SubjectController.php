<?php
  
namespace App\Http\Controllers;
  
use App\Models\Subject;
use App\Http\Transformers\SubjectTransformer;
use App\Http\Controllers\Controller;
use App\Http\Serializers\CustomDataArraySerializer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use DB;

class SubjectController extends ApiController{
  
  const WRAPPER = "areas";
  
  /**
  * Return all subjects
  **/
  public function index(Manager $fractal){
        $subjects  = Subject::all();
        
        $data = $subjects;
        if ( !is_null($subjects) ) {
            $collection = new Collection($subjects, new SubjectTransformer, self::WRAPPER);

            $fractal->setSerializer(new CustomDataArraySerializer);
            $data = $fractal->createData($collection)->toArray();
        }
        
        return $this->respond($data);
    }
  
    /**
    * Return a subject based on a provided slug
    **/
    public function getSubject($slug){
  
        $subject  = Subject::where('Slug', '=', $slug)->first();
        
        $data = $subject;
        //handle gracefully if null
        if( !is_null($subject) ) {
            $item = new Item($subject, new SubjectTransformer, self::WRAPPER);

            $fractal = new Manager;
        
            $fractal->setSerializer(new CustomDataArraySerializer);
            $data = $fractal->createData($item)->toArray();
        }
        
        return $this->respond($data);
    }
    
    /**
    * Return subjects based on a provided YearQuarterID
    **/
    public function getSubjectsByYearQuarter($yqr) {

        //DB::connection('cs')->enableQueryLog();
        $subjects = DB::connection('cs')
            ->table('vw_SubjectDetails')
            ->join('vw_Class', 'vw_SubjectDetails.Slug', '=', 'vw_Class.Department')
            ->where('vw_Class.YearQuarterID', '=', $yqr)
            ->select('vw_SubjectDetails.Slug', 'vw_SubjectDetails.SubjectTitle as Title')
            ->groupBy('vw_SubjectDetails.Slug', 'vw_SubjectDetails.SubjectTitle')
            ->orderBy('vw_SubjectDetails.Slug', 'asc')
            ->get();
         //$queries = DB::connection('cs')->getQueryLog();
         //dd($queries);  

         $data = $subjects;
         if ( !is_null($subjects) && !$subjects->isEmpty() ) {
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
?>