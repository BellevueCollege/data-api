<?php namespace App\Http\Transformers;

use App\Models\Student;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class StudentTransformer extends TransformerAbstract {

    /**
    * Fractal transformer for a YearQuarter. Defines how data for a 
    * YearQuarter should be output in the API.
    **/

    protected $defaultIncludes = [
        'blocks'
    ];

    public function transform(Student $stu)
    {
        return [
            'SID'			=> $stu->SID,
            'firstName' 	=> $stu->FirstName,
            'lastName'      => $stu->LastName,
            'email'         => $stu->Email,
            'phoneDaytime'  => $stu->DaytimePhone,
            'phoneEvening'  => $stu->EveningPhone,
            'username'      => $stu->NTUserName,
        ];
    }
    
    /**
     * Include blocks for student
     *
     * @return League\Fractal\Resource\Collection
     */
    public function includeBlocks(Student $stu)
    {
        $blocks = $stu->blocks;

        if ( $blocks->count() == 0) {
            //return null resource
            return $this->null();
        }
        //set resource key as false so data isn't "double wrapped" with a blocks identifier
        $blocks_transformed = $this->collection($blocks, new BlockTransformer, false);

        return $blocks_transformed;
    }
}