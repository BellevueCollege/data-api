<?php namespace App\Http\Transformers;

use App\Models\Block;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;
use Illuminate\Support\Facades\Log;

class BlockTransformer extends TransformerAbstract {

    /**
    * Fractal transformer for a Block. Defines how 
    * block data will be output in the API.
    **/
    
    public function transform(Block $block)
    {   
        $reason = null;
        if ( !is_null($block->reason) && !empty($block->reason)) {
            $reason = $block->reason->Reason;
        }
        return [
            'blockId'   => $block->UnusualActionID,
            'reason'    => $reason
        ];
    }	
}