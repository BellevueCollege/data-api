<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use App\Exceptions\MissingParameterException;
use App\Http\Resources\ClassScheduleCollection;

class ClassScheduleController extends ApiController{

    const WRAPPER = "classSchedules";

    /**
    * Return class schedules based on given PSClassID.
    * Status: inactive
    * No route, not yet serialized
    **/
    public function getClassSchedules($psclassid, Request $request)
    {
        try {
            $schedules = ClassSchedule::where('PSClassID', '=', $psclassid)->get();
            return new ClassScheduleCollection($schedules);
        } catch (\Exception $e) {
            return response()->json(['classSchedules' => []], 404);
        }
    }
}
