<?php

namespace App\Services;

use App\Models\Module;
use App\Models\HakAksesModule;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

use App\Helper\DeviceHelper;
use App\Helper\GeoDetector;
use App\Helper\AppLogger;

use function PHPSTORM_META\map;

class ModuleService
{
    public function allModule()
    {
        $module = Module::all();
        return $module;
    }

    public function cekModule($data)
    {
        $module = Module::where('kd_module', $data)
            ->where('status_module', 'ACTIVE')
            ->get();

        return $module;
    }

    public function cekModuleByKd($data)
    {
        $module = Module::find($data);
        return $module;
    }

    public function moduleWithMenu()
    {
        $modules = Module::where('status_module', 'ACTIVE')
            ->with('menu')
            ->get();

        $result = $modules->map(function ($module) {
            return [
                'nama_module' => $module->nama_module,
                'url_module'  => $module->url_module,
                'menu' => $module->menu->map(function ($menu) {
                    return [
                        'nama_menu' => $menu->nama_menu,
                        'url_menu'  => $menu->url_menu,
                        'children'  => $menu->children->map(function ($child) {
                            return [
                                'nama_menu' => $child->nama_menu,
                                'url_menu'  => $child->url_menu,
                                'urutan'  => $child->urutan,
                            ];
                        })->filter()->values(),
                    ];
                })
                    ->filter(function ($menu) {
                        return $menu['children']->isNotEmpty();
                    })
                    ->values(),
            ];
        });

        return $result;
    }

    private function generateKdModule()
    {
        $currentMonth = Carbon::now()->format('Ym');
        $prefix = 'MDL-' . $currentMonth . '-';

        $module = Module::where('kd_module', 'LIKE', $prefix . '%')
            ->orderBy('kd_module', 'DESC')
            ->first();

        if (!$module) {
            return $prefix . '0000';
        }

        $lastId = $module->kd_module;
        $lastNumber = substr($lastId, -4);

        $newNumber = str_pad(intval($lastNumber) + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $newNumber;
    }

    private function generateKdHakAksesModule()
    {
        $currentMonth = Carbon::now()->format('Ym');
        $prefix = 'AMDL-' . $currentMonth . '-';

        $module = HakAksesModule::where('kd_hak_akses_module', 'LIKE', $prefix . '%')
            ->orderBy('kd_hak_akses_module', 'DESC')
            ->first();

        if (!$module) {
            return $prefix . '0000';
        }

        $lastId = $module->kd_hak_akses_module;
        $lastNumber = substr($lastId, -4);

        $newNumber = str_pad(intval($lastNumber) + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $newNumber;
    }

    public function simpanModule($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('SIMPAN-MODULE');
        try {
            $log->info("<================= MULAI PROSES SIMPAN DATA DI DATABASE MODULE =================>");
            $log->info("Data dari controller: ADA" . json_encode($data));

            $kd_module = $this->generateKdModule();
            $log->info("<================= BERHASIL BUAT PK =================>");

            $now = Carbon::now('Asia/Jakarta');
            $tgl_input = $now->toDateString();
            $waktu_input = $now->format('H:i');
            $bln_input = $now->format('m');
            $thn_input = $now->year;

            $module = new Module();
            $module->kd_module = $kd_module;
            $module->nama_module = $data['nama_module'];
            $module->tampil_module = $data['tampil_module'];
            $module->url_module = $data['url_module'];
            $module->status_module = $data['status_module'];
            $module->user_input = $data['user_input'];
            $module->tgl_input = $tgl_input;
            $module->bln_input = $bln_input;
            $module->thn_input = $thn_input;
            $module->waktu_input = $waktu_input;

            $module->save();
            $log->info("BERHASIL SIMPAN DATA");

            DB::commit();

            $log->info("PROSES SELESAI");
            return $module;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }

    public function simpanHakAksesUser($user, $data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('SIMPAN-HAK-AKSES');
        try {
            $log->info("<================= MULAI PROSES SIMPAN SIMPAN-HAK-AKSES KE DATABASE =================>");
            $log->info("Data dari controller: ADA");

            HakAksesModule::where('kd_user', $user)->delete();

            $now = Carbon::now('Asia/Jakarta');
            $tgl_input = $now->toDateString();
            $waktu_input = $now->format('H:i');
            $bln_input = $now->format('m');
            $thn_input = $now->year;

            $saveData = [];

            foreach ($data as $d) {
                $kd_hak_akses_module = $this->generateKdHakAksesModule();

                $akses = new HakAksesModule();
                $akses->kd_hak_akses_module = $kd_hak_akses_module;
                $akses->kd_user = $d['kd_user'];
                $akses->kd_module = $d['kd_module'];
                $akses->status_akses = $d['status_akses'];
                $akses->tgl_input = $tgl_input;
                $akses->bln_input = $bln_input;
                $akses->thn_input = $thn_input;
                $akses->waktu_input = $waktu_input;
                $akses->user_input = $d['user_input'];
                $akses->save();

                $saveData[] = $akses;
            }

            DB::commit();
            return $saveData;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }
}
