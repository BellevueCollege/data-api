<?php namespace App\Http\Transformers;

use App\Models\LinkFound;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class LinkFoundTransformer extends TransformerAbstract
{

    /**
    * A Fractal transformer for a link. Defines how data for
    * a link should be output in the API.
    **/

    public function transform(LinkFound $link)
    {
        return [
            'Link'  =>  $link->LinkText,
            'Description'  =>  $link->LinkDescr,
        ];
    }
}
