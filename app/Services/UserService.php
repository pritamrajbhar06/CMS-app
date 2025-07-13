<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getApiUserByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }
}