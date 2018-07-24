<?php
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Hash;

class User extends Model implements AuthenticatableContract, AuthorizableContract {
    use Authenticatable, Authorizable;

    protected $guard = 'admin';
    //protected $fillable = ['id', 'name', 'clientid', 'clienturl'];
    //protected $hidden   = ['created_at', 'updated_at', 'password'];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    /*public function getJWTIdentifier()
    {
        return $this->getKey();
    }*/
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    /*public function getJWTCustomClaims()
    {
        return [];
    }*/
}