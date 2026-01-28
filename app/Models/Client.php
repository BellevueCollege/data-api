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
    protected $casts = [
        'permissions' => 'array',
    ];

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

    /**
     * Sync client permissions by permission names
     *
     * @param array $permissionNames Array of permission names to sync
     * @return array
     */
    public function syncPermissionsByName(array $permissionNames)
    {
        $this->permissions = $permissionNames;
        $this->save();
    }

    /**
     * Check if client has a specific permission
     */
    public function hasPermission($permissionName)
    {
        return in_array($permissionName, $this->permissions ?? []);
    }
}