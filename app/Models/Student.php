<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model //implements AuthenticatableContract, AuthorizableContract
{
    //use Authenticatable, Authorizable;
    protected $connection = 'ods';
    protected $table = 'vw_Student_Unfiltered';
    protected $primaryKey = 'EMPLID';
    public $timestamps = false;
    protected $casts = [
        'EMPLID'        => 'string',
        'PrivateRecord' => 'boolean',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    /*protected $hidden = [
        'SSN'
    ];*/

    /**
     * Define blocks relationship
     */
}
