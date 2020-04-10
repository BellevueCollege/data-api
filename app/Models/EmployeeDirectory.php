<?php
/**
 * Employee Directory information (subset of employees)
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDirectory extends Model //implements AuthenticatableContract, AuthorizableContract
{
    //use Authenticatable, Authorizable;
     protected $connection = 'empdirectory';
     protected $table = 'Employees';
     protected $primaryKey = 'SID';
     public $incrementing = false;
     public $timestamps = false;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'SID',
        'SearchField',
        'SearchFieldDisplay',
        'EmploymentDate',
        'EmployeeTypeID',
        'FullPartInd'
    ];
}
