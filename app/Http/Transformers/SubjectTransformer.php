<?php namespace App\Http\Transformers;

use App\Models\Subject;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class SubjectTransformer extends TransformerAbstract {

    /**
    * A Fractal transformer for a subject. Defines how data for 
    * a subject should be output in the API.
    **/

    public function transform(Subject $sub)
    {
        return [
            'subject'  =>  $sub->Slug,
            'name'  =>  $sub->Title,
        ];
    }
	
}