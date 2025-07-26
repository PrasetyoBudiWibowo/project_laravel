<?php

namespace App\Services;

use App\Models\LevelUser;
use App\Models\Karyawan;
use App\Models\TblUser;

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

    public function getUserByKdAsli($data)
    {
        $user = TblUser::find($data);
        return $user;
    }

    public function generateImage($data)
    {
        $formatDate = Carbon::now()->format('Ym');
        $prefix = 'IMGUSR-' . $formatDate . '-';

        if (!empty($data['kd_asli_user'])) {
            $oldImg = TblUser::where('kd_asli_user', $data['kd_asli_user'])->value('img_user');

            if (!empty($oldImg)) {
                $oldFilePath = $_SERVER['DOCUMENT_ROOT'] . '/assets/img/user/' . $oldImg;
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
        }

        $lastImage = TblUser::where('img_user', 'LIKE', $prefix . '%')->max('img_user');

        if ($lastImage) {
            $lastNumber = (int) substr($lastImage, 11, 4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT) . '-' . $data['kd_asli_user'];
    }
}