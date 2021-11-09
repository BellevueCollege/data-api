<?php

namespace App\Http\Controllers;

use App\Models\YearQuarter;
use App\Http\Transformers\YearQuarterTransformer;
use App\Http\Controllers\Controller;
use App\Http\Serializers\CustomDataArraySerializer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use DB;

class YearQuarterController extends ApiController {

    const WRAPPER = "quarters";

    /**
     * Return all YearQuarters
    * Status: inactive
    * No active route. No set serialization.
    **/
    public function index(){

        $yqrs  = YearQuarter::all();
        $collection = new Collection($yqrs, new YearQuarterTransformer);

        $fractal = new Manager;
        $data = $fractal->createData($collection)->toArray();

        return $this->respond($data);

    }

    /**
    * Get YearQuarter based on a given YearQuarterID or STRM
    *
    * use ?format=strm or ?format=yrq
    **/
    public function getYearQuarter($yqrid, Request $request){
        if ( $request->input('format') === 'strm') {
            $yqr = YearQuarter::where('STRM', '=', $yqrid)->first();
        } else {
            $yqr = YearQuarter::where('YearQuarterID', '=', $yqrid)->first();
        }

        $data = $yqr;
        //only serialize if not empty
        if ( !is_null($yqr) ) {
            $item = new Item($yqr, new YearQuarterTransformer, "quarter");
            $fractal = new Manager;
            $fractal->setSerializer(new CustomDataArraySerializer);
            $data = $fractal->createData($item)->toArray();
        }

        return $this->respond($data);
    }


    /**
    * Get current YearQuarter
    **/
    public function getCurrentYearQuarter() {
        $yqr = YearQuarter::current()->first();

        $data = $yqr;
        //only serialize if not empty
        if (!is_null($yqr)) {
            $item = new Item($yqr, new YearQuarterTransformer, "quarter");

            $fractal = new Manager;
            $fractal->setSerializer(new CustomDataArraySerializer);
            $data = $fractal->createData($item)->toArray();
        }

        return $this->respond($data);
    }

    /**
    * Returns "active" YearQuarters
    **/
    public function getViewableYearQuarters() {

        //get max quarters to be shown
        $max_yqr = config('dataapi.yearquarter_maxactive');
        $lookahead = config('dataapi.yearquarter_lookahead');
        $timezone = new \DateTimeZone(config("dataapi.timezone"));

        //Create now date/time object
        $now = new \DateTime();
        $now->setTimezone($timezone);
        $now_string = $now->format("Y-m-d H:i:s");

        //Create lookahead date
        $la_date = $now->add(new \DateInterval('P'.$lookahead.'D'));
        $la_string = $la_date->format("Y-m-d H:i:s");

        //Use Eloquent query builder to query results
        //DB::connection('ods')->enableQueryLog();
        $yqrs = DB::connection('ods')
            ->table('vw_YearQuarter')
            ->join('vw_WebRegistrationSetting', 'vw_WebRegistrationSetting.YearQuarterID', '=', 'vw_YearQuarter.YearQuarterID')
            ->where(function ($query) {
                $lookahead = config('dataapi.yearquarter_lookahead');
                $timezone = new \DateTimeZone(config("dataapi.timezone"));

                //Create now date/time object
                $now = new \DateTime();
                $now->setTimezone($timezone);
                $now_string = $now->format("Y-m-d H:i:s");

                //Create lookahead date
                $la_date = $now->add(new \DateInterval('P'.$lookahead.'D'));
                $la_string = $la_date->format("Y-m-d H:i:s");

                $query->whereNotNull('vw_WebRegistrationSetting.FirstRegistrationDate')
                ->where('vw_WebRegistrationSetting.FirstRegistrationDate', '<=', $la_string )
                ->orWhere('vw_YearQuarter.LastClassDay', '<=', $now_string );
            })
            ->select('vw_YearQuarter.YearQuarterID', 'vw_YearQuarter.STRM', 'vw_YearQuarter.Title', 'vw_YearQuarter.FirstClassDay', 'vw_YearQuarter.LastClassDay', 'vw_YearQuarter.AcademicYear')
            ->orderBy('vw_YearQuarter.FirstClassDay', 'desc')
            ->take($max_yqr)
            ->get();
         //$queries = DB::connection('ods')->getQueryLog();
         //dd($queries);
         //var_dump($yqrs);

         //When using the Eloquent query builder, we must "hydrate" the results back to collection of objects
         $data = $yqrs;

         if ( !empty($yqrs) && !$yqrs->isEmpty() ) {
            $yqr_hydrated = YearQuarter::hydrate($yqrs->toArray());
            $collection = new Collection($yqr_hydrated, new YearQuarterTransformer, self::WRAPPER);

            //Set data serializer
            $fractal = new Manager;
            $fractal->setSerializer(new CustomDataArraySerializer);
            $data = $fractal->createData($collection)->toArray();
         }

         return $this->respond($data);
    }

}
?>
