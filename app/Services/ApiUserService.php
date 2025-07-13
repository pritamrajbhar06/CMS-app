<?php

namespace App\Services;

use App\Models\ApiUser;

class ApiUserService
{
    public function getApiUserByUsername(string $username)
    {
        return ApiUser::where('username', $username)->first();
    }
}