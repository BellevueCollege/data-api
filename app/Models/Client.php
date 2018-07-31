<?php
namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

class Client extends Authenticatable implements JWTSubject {
    use Notifiable;

    protected $connection = 'da';
    protected $fillable = ['id', 'clientname', 'clientid', 'clienturl'];
    protected $hidden   = ['created_at', 'updated_at', 'password'];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function generateClientID() 
    {
        return (string) Str::uuid();
    }

    public static function generateClientKey()
    {
        return (string) Str::uuid();
    }
}