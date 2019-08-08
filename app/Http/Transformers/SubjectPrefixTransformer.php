<?php namespace App\Http\Transformers;

use App\Models\SubjectPrefix;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class SubjectPrefixTransformer extends TransformerAbstract {

    /**
    * A Fractal transformer for a subject. Defines how data for 
    * a subject should be output in the API.
    **/

    public function transform(SubjectPrefix $prefix)
    {
        return [
            'subject'  =>  $prefix->CoursePrefixID,
            'name'  =>  $prefix->subject->Title,
        ];
    }
	
}