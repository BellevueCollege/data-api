<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use App\Exceptions\MissingParameterException;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use App\Http\Transformers\ClassScheduleTransformer;
use App\Http\Serializers\CustomDataArraySerializer;
use DB;


class ClassScheduleController extends ApiController{

    const WRAPPER = "classSchedules";

    /**
    * Return class schedules based on given PSClassID.
    * Status: inactive
    * No route, not yet serialized
    **/
    public function getClassSchedules($psclassid, Request $request){
        $schedules = ClassSchedule::where('PSClassID', '=', $psclassid)->get();

        $data = $schedules;
        if ( !is_null($schedules) ) {
            $collection = new Collection($schedules, new ClassScheduleTransformer, self::WRAPPER);

            $fractal = new Manager;
            $fractal->setSerializer(new CustomDataArraySerializer);
            $data = $fractal->createData($collection)->toArray();
        }
        return $this->respond($data);
    }
}
