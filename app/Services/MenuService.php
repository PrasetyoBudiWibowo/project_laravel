<?php

namespace App\Services;

use App\Models\MasterMenu;
use App\Models\HakAksesMenu;

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
        $menus = MasterMenu::with(['module', 'parent'])->get();

        $result = $menus->map(function ($menu) {
            return [
                'kd_menu' => Crypt::encryptString($menu->kd_menu),
                'kd_module' => $menu->kd_module,
                'nama_menu' => $menu->nama_menu,
                'urutan' => $menu->urutan,
                'url_menu' => $menu->url_menu,
                'parent_menu' => $menu->parent_menu,
                'icon_menu' => $menu->icon_menu,
                'status_akses' => $menu->status_akses,
                'module' => [
                    'nama_module' => $menu->module->nama_module ?? null,
                    'status_module' => $menu->module->status_module ?? null,
                ],
                'parent' => [
                    'nama_menu' => $menu->parent?->nama_menu ?? null,
                ],
            ];
        });

        return $result;
    }

    public function getMenuByKode($data)
    {
        $menu = MasterMenu::find($data);
        return $menu;
    }

    public function getMenuByNameAndParent($parentMenu, $namaMenu)
    {
        $menu = MasterMenu::where('kd_menu', $parentMenu)
            ->where('nama_menu', $namaMenu)
            ->first();
        return $menu;
    }

    public function cekAksesMenuByUser($data)
    {
        $log = AppLogger::getLogger('=======> cekAksesMenuByUser');
        $log->info("Data dari controller: ADA" . json_encode($data));

        $kd_asli_user = is_array($data) ? $data['kd_asli_user'] : $data->kd_asli_user;

        $aksesMenu = HakAksesMenu::where('kd_user', $kd_asli_user)
            ->where('status_akses', "YA")
            ->with('menu')
            ->get();

        // $result = $aksesMenu->map(function ($data) {
        //     return [
        //         'menu' => $data->menu->
        //     ];
        // });

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

    private function generateKdHakAksesMenu()
    {
        $currentMonth = Carbon::now()->format('Ym');
        $prefix = 'HAKM-' . $currentMonth . '-';

        $menu = HakAksesMenu::where('kd_hak_akses_menu', 'LIKE', $prefix . '%')
            ->orderBy('kd_hak_akses_menu', 'DESC')
            ->first();

        if (!$menu) {
            return $prefix . '0000';
        }

        $lastId = $menu->kd_hak_akses_menu;
        $lastNumber = substr($lastId, -4);

        $newNumber = str_pad(intval($lastNumber) + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $newNumber;
    }

    private function generateNoUrut($kd_module, $parent_menu)
    {
        $lastMenu = MasterMenu::where('kd_module', $kd_module)
            ->where('parent_menu', $parent_menu)
            ->orderBy('urutan', 'DESC')
            ->first();

        if (!$lastMenu || !$lastMenu->urutan) {
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

            $urutan = null;
            if (!empty($data['parent_menu']) && $data['tipe_menu'] === "child") {
                $urutan = $this->generateNoUrut($data['kd_module'], $data['parent_menu']);
                $log->info("<================= BERHASIL BUAT NO URUT =================>");
            }

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
            $masterMenu->urutan =  $urutan;
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

    public function simpanHakAksesMenu($user, $data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('SIMPAN-HAK-AKSES-MENU');
        try {
            $log->info("<================= MULAI PROSES SIMPAN SIMPAN-HAK-AKSES-MENU KE DATABASE =================>");
            $log->info("Data dari controller: ADA" . json_encode($data));

            HakAksesMenu::where('kd_user', $user['kd_asli_user'])->delete();

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

            $saveData = [];

            foreach ($data as $d) {
                $kd_hak_akses_menu = $this->generateKdHakAksesMenu();

                $hakAksesMenu = new HakAksesMenu();
                $hakAksesMenu->kd_hak_akses_menu = $kd_hak_akses_menu;
                $hakAksesMenu->kd_menu  = $d['kd_menu'];
                $hakAksesMenu->kd_user = $d['kd_user'];
                $hakAksesMenu->bisa_insert = $d['bisa_insert'];
                $hakAksesMenu->bisa_edit = $d['bisa_edit'];
                $hakAksesMenu->bisa_export = $d['bisa_export'];
                $hakAksesMenu->status_akses = $d['status_akses'];
                $hakAksesMenu->tgl_input = $tgl_input;
                $hakAksesMenu->bln_input = $bln_input;
                $hakAksesMenu->thn_input = $thn_input;
                $hakAksesMenu->waktu_input = $waktu_input;
                $hakAksesMenu->user_input = $d['user_input'];
                $hakAksesMenu->device = $device;
                $hakAksesMenu->alamat_device = $ipDevice;
                $hakAksesMenu->type_device = $deviceType;
                $hakAksesMenu->save();

                $saveData[] = $hakAksesMenu;
            }

            $log->info("BERHASILLLLLLLLL");

            DB::commit();
            return $saveData;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }
}
