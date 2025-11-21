<?php

namespace App\Http\Controllers;

use App\Models\YearQuarter;
use App\Http\Resources\YearQuarterResource;
use App\Http\Resources\YearQuarterCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class YearQuarterController extends ApiController {

    /**
    * List all Terms (YearQuarters)
    * Status: inactive
    * No active route. No set serialization.
    *
    * @return YearQuarterCollection | stdClass
    **/
    public function index()
    {
        try {
            $yqrs  = YearQuarter::all();
            return new YearQuarterCollection($yqrs);
        } catch (\Exception $e) {
            return response()->json(new stdClass());
        }
    }

    /**
    * Get Term (YearQuarter) by YearQuarterID or STRM
    *
    * use ?format=strm or ?format=yrq to specify which format to return
    *
    * @param string $yqrid YearQuarterID or STRM
    * @param \Illuminate\Http\Request $request
    *
    * @response array{quarter: array{quarter: string, strm: string, title: string}}
    *
    * @return \Illuminate\Http\JsonResponse | stdClass
    **/
    public function getYearQuarter($yqrid, Request $request){
        try {
            if ( $request->input('format') === 'strm') {
                $yqr = YearQuarter::where('STRM', '=', $yqrid)->firstOrFail();
            } else {
                $yqr = YearQuarter::where('YearQuarterID', '=', $yqrid)->firstOrFail();
            }
            // We're wrapping in a quarter array to match the response format
            // Normal wrapping methods don't work because of double 'quarter'
            // in both the wrapper and the resource.
            // This also requires the response to be manually listed in the
            // docblock to ensure documentation is correct.
            return response()->json([
                'quarter' => new YearQuarterResource($yqr)
            ]);
        } catch (\Exception $e) {
            return response()->json(new stdClass());
        }
    }


    /**
    * Get Current Term (YearQuarter)
    *
    * @response array{quarter: array{quarter: string, strm: string, title: string}}
    
    * @return \Illuminate\Http\JsonResponse
    **/
    public function getCurrentYearQuarter() {
        try {
            $yqr = YearQuarter::current()->firstOrFail();
            // We're wrapping in a quarter array to match the response format
            // Normal wrapping methods don't work because of double 'quarter'
            // in both the wrapper and the resource.
            // This also requires the response to be manually listed in the
            // docblock to ensure documentation is correct.
            return response()->json([
                'quarter' => new YearQuarterResource($yqr)
            ]);
        } catch (\Exception $e) {
            return response()->json(new stdClass());
        }
    }

    /**
    * List Active Terms (YearQuarters)
    *
    * @return YearQuarterCollection | stdClass
    **/
    public function getViewableYearQuarters() {

        try {
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
            return new YearQuarterCollection($yqrs);
        } catch (\Exception $e) {
            return response()->json(new stdClass());
        }
    }

}
