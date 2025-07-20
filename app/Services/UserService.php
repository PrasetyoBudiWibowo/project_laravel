<?php

namespace App\Services;
use App\Models\LevelUser;

class UserService
{
    public function getLevelUser()
    {
        $levelUser = LevelUser::all();
        return $levelUser;
    }
}
