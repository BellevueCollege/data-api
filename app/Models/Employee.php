<?php

namespace App\Models;

//use Illuminate\Auth\Authenticatable;
//use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;

//use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
//use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Employee extends Model //implements AuthenticatableContract, AuthorizableContract
{
    //use Authenticatable, Authorizable;
     protected $connection = 'ods';
     protected $table = 'vw_Employee';
     protected $primaryKey = 'EMPLID';
     public $timestamps = false;
     protected $casts = [
        'EMPLID' => 'string',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'SSN'
    ];
}
