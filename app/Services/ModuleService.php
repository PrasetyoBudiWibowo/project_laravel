<?php

namespace App\Services;

use App\Models\MasterModule;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Helper\DeviceHelper;
use App\Helper\GeoDetector;
use App\Helper\AppLogger;

class ModuleService
{
    private function generateKdModule()
    {
        $currentMonth = Carbon::now()->format('Ym');
        $prefix = 'MDL-' . $currentMonth . '-';

        $module = MasterModule::where('kd_module', 'LIKE', $prefix . '%')
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

    public function simpanModule($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('SIMPAN-MODULE');
        try {
            $log->info("<================= MULAI PROSES SIMPAN DATA DI DATABASE MODULE =================>");
            $log->info("Data dari controller: ADA");

            $kd_module = $this->generateKdModule();
            $log->info("<================= BERHASIL BUAT PK =================>");

            $now = Carbon::now('Asia/Jakarta');
            $tgl_input = $now->toDateString();
            $waktu_input = $now->format('H:i');
            $bln_input = $now->format('m');
            $thn_input = $now->year;

            $module = new MasterModule();
            $module->kd_module = $kd_module;
            $module->nama_module = $data['nama_module'];
            $module->tampil_module = $data['tampil_module'];
            $module->url = $data['url'];
            $module->status_module = $data['status_module'];
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
}
