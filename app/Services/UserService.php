<?php

namespace App\Services;
use App\Models\LevelUser;
use App\Models\Karyawan;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Helper\DeviceHelper;
use App\Helper\GeoDetector;
use App\Helper\AppLogger;

class UserService
{
    public function getLevelUser()
    {
        $levelUser = LevelUser::all();
        return $levelUser;
    }
}
