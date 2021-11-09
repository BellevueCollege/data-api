<?php namespace App\Http\Transformers;

use App\Models\YearQuarter;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class YearQuarterTransformer extends TransformerAbstract {

    /**
    * Fractal transformer for a YearQuarter. Defines how data for a
    * YearQuarter should be output in the API.
    **/

    public function transform(YearQuarter $yqr)
    {
        return [
            'quarter'          => $yqr->YearQuarterID,
            'strm'             => $yqr->STRM,
            'title'            => $yqr->Title
        ];
    }

}
