<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkFound extends Model
{
    /**
    * Model for a link found by Funnelback Search
    **/
     protected $table = 'vw_LinkFound';
     protected $connection = 'copilot';
     protected $primaryKey = null;
     public $timestamps = false;
     public $incrementing = false;
}
