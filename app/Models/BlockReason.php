<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockReason extends Model
{
    protected $connection = 'da';
    protected $table = 'BlockReasons';
    //protected $primaryKey = 'SID';
    public $timestamps = false;
}
