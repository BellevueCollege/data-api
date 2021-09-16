<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model //implements AuthenticatableContract, AuthorizableContract
{
    //use Authenticatable, Authorizable;
    protected $connection = 'ods';
    protected $table = 'vw_Student';
    protected $primaryKey = 'EMPLID';
    public $timestamps = false;

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
    public function blocks()
    {
        return $this->hasMany('App\Models\Block', 'SID', 'SID');
        //'App\Models\Block'
    }
}
