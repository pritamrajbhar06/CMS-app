<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class ApiUser extends Model
{
    use HasApiTokens;
    
    protected $table = 'api_users';

    protected $fillable = [
        'username',
        'password',
        'api_token'
    ];

}
