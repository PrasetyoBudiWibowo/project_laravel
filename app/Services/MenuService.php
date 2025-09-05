<?php

namespace App\Services;

use App\Models\MasterMenu;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

use App\Helper\DeviceHelper;
use App\Helper\GeoDetector;
use App\Helper\AppLogger;

class MenuService
{
    public function getAllMenu()
    {
        $menus = MasterMenu::get();

        $result = $menus->map(function ($menu) {
            return [
                'kd_menu' => Crypt::encryptString($menu->kd_menu),
                'kd_module' => Crypt::encryptString($menu->kd_module),
                'nama_menu' => $menu->nama_menu,
                'url_menu' => $menu->url_menu,
                'parent_menu' => $menu->parent_menu,
                'icon_menu' => $menu->icon_menu,
                'status_akses' => $menu->status_akses,
            ];
        });

        return $result;
    }
}
