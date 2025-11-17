<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Client;

class Permission extends Model
{
    // Set DB connection
    protected $connection = 'da'; 

    // Set fillable fields
    protected $fillable = ['name', 'description'];

    // Configure relationship with clients
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_permission');
    }
}
