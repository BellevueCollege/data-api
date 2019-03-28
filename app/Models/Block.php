<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $connection = 'da';
    protected $table = 'vw_Blocks';
    //protected $primaryKey = 'SID';
    public $timestamps = false;

    /**
     * Define block reason child relationship
     */
    public function reason()
    {
        return $this->hasOne('App\Models\BlockReason', 'UnusualActionID', 'UnusualActionID');
    }
}