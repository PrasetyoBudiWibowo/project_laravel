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

    public function getMenuByKode($data)
    {
        $user = MasterMenu::find($data);
        return $user;
    }

    private function generateKdModule()
    {
        $currentMonth = Carbon::now()->format('Ym');
        $prefix = 'MN-' . $currentMonth . '-';

        $menu = MasterMenu::where('kd_menu', 'LIKE', $prefix . '%')
            ->orderBy('kd_menu', 'DESC')
            ->first();

        if (!$menu) {
            return $prefix . '0000';
        }

        $lastId = $menu->kd_menu;
        $lastNumber = substr($lastId, -4);

        $newNumber = str_pad(intval($lastNumber) + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $newNumber;
    }

    private function generateNoUrut($kd_module)
    {
        $lastMenu = MasterMenu::where('kd_module', $kd_module)
            ->orderBy('urutan', 'DESC')
            ->first();

        if (!$lastMenu) {
            return 1;
        }

        return $lastMenu->urutan + 1;
    }

    public function simpanMenu($data)
    {
        try {
            DB::beginTransaction();
            $log = AppLogger::getLogger('SIMPAN-MENU');

            $log->info("<================= MULAI PROSES SIMPAN DATA DI DATABASE MENU =================>");
            $log->info("Data dari controller: ADA" . json_encode($data));

            $kd_menu = $this->generateKdModule();
            $log->info("<================= BERHASIL BUAT PK =================>");

            $urutan = $this->generateNoUrut($data['kd_module']);
            $log->info("<================= BERHASIL BUAT NO URUT =================>");

            $now = Carbon::now('Asia/Jakarta');
            $tgl_input = $now->toDateString();
            $waktu_input = $now->format('H:i');
            $bln_input = $now->format('m');
            $thn_input = $now->year;

            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $deviceInfo = DeviceHelper::detectDevice($userAgent);
            $deviceType = $deviceInfo['deviceType'];
            $device = $deviceInfo['browser'];

            $ipDetector = GeoDetector::getDeviceLocation();
            $ipDevice = isset($ipDetector['ip']) ? $ipDetector['ip'] : 'Unknown IP';

            $log->info("<================= MULAI PROSES SIMPAN =================>");

            $masterMenu = new MasterMenu();
            $masterMenu->kd_menu = $kd_menu;
            $masterMenu->kd_module = $data['kd_module'];
            $masterMenu->nama_menu = $data['nama_menu'];
            $masterMenu->urutan = $data['parent_menu'] !== null ? $urutan : null;
            $masterMenu->url_menu = $data['url_menu'];
            $masterMenu->parent_menu = $data['parent_menu'] ?? null;
            $masterMenu->icon_menu = $data['icon_menu'];
            $masterMenu->status_akses = "YA";
            $masterMenu->tgl_input = $tgl_input;
            $masterMenu->bln_input = $bln_input;
            $masterMenu->thn_input = $thn_input;
            $masterMenu->waktu_input = $waktu_input;
            $masterMenu->user_input = $data['user_input'];
            $masterMenu->device = $device;
            $masterMenu->alamat_device = $ipDevice;
            $masterMenu->type_device = $deviceType;

            $masterMenu->save();
            $log->info("BERHASIL SIMPAN DATA");

            DB::commit();

            $log->info("PROSES SELESAI");
            return $masterMenu;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }
}
