<?php

namespace App\Http\Controllers;

use App\Models\YearQuarter;
use App\Http\Resources\YearQuarterResource;
use App\Http\Resources\YearQuarterCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use stdClass;

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
            // get max quarters to be shown
            $max_yqr = config('dataapi.yearquarter_maxactive');

            // get max days to look ahead
            $lookaheadDays = config('dataapi.yearquarter_lookahead');

            //Create now date/time object and lookahead date
            $now = Carbon::now();
            $lookahead = Carbon::now()->addDays($lookaheadDays);

            // get active year quarters based on registration settings
            // ideally this should move to the model in the future
            $yrqs = YearQuarter::whereHas('webRegistrationSetting')
                ->where(function($query) use ($lookahead, $now) {
                    $query->whereHas('webRegistrationSetting', function ($q) use ($lookahead) {
                        $q->whereNotNull('FirstRegistrationDate')
                        ->where('FirstRegistrationDate', '<=', $lookahead);
                    })
                    ->orWhere('LastClassDay', '<=', $now);
                })
                ->orderBy('FirstClassDay', 'desc')
                ->take($max_yqr)
                ->get();
            return new YearQuarterCollection($yrqs);
        } catch (\Exception $e) {
            return response()->json(new stdClass());
        }
    }

}
