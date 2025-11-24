<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\YearQuarter;

class WebRegistrationSetting extends Model
{
    protected $table = 'vw_WebRegistrationSetting';
    protected $connection = 'ods';
    protected $primaryKey = 'STRM';

    protected $casts = [
        'STRM' => 'string',
        'FirstRegistrationDate' => 'datetime',
        'LastRegistrationDate' => 'datetime',
    ];

    /**
     * Belongs to YearQuarter
     */
    public function yearQuarter()
    {
        return $this->belongsTo(YearQuarter::class, 'STRM', 'STRM');
    }
}
