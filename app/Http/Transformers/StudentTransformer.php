<?php namespace App\Http\Transformers;

use App\Models\Student;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class StudentTransformer extends TransformerAbstract
{

    /**
    * Fractal transformer for a YearQuarter. Defines how data for a
    * YearQuarter should be output in the API.
    **/


    public function transform(Student $stu)
    {
        return [
            'SID'           => $stu->SID,
            'EMPLID'        => $stu->EMPLID,
            'firstName'     => $stu->FirstName,
            'lastName'      => $stu->LastName,
            'email'         => $stu->Email,
            'phoneDaytime'  => $stu->DaytimePhone,
            'phoneEvening'  => $stu->EveningPhone,
            'username'      => $stu->NTUserName,
            'ferpaBlock'    => $stu->PrivateRecord,
        ];
    }
}
