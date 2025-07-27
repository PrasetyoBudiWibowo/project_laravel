<?php

namespace App\Services;

use App\Models\Provinsi;
use App\Models\KotaKabupaten;
use App\Models\Kecamatan;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Helper\DeviceHelper;
use App\Helper\GeoDetector;
use App\Helper\AppLogger;

class WilayahService
{
    public function allProvinsi()
    {
        $allProvinsi = Provinsi::where('status_tampil', 'ACTIVE')->get();
        return $allProvinsi;
    }

    private function generateKdProvinsi()
    {
        $currentMonth = Carbon::now()->format('Ym');
        $prefix = 'PRV-' . $currentMonth . '-';

        $lastProvinsi = Provinsi::where('kd_provinsi', 'LIKE', $prefix . '%')
            ->orderBy('kd_provinsi', 'DESC')
            ->first();

        if (!$lastProvinsi) {
            return $prefix . '0000';
        }

        $lastId = $lastProvinsi->kd_provinsi;
        $lastNumber = substr($lastId, -4);

        $newNumber = str_pad(intval($lastNumber) + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $newNumber;
    }

    public function cekNamaProvinsi($data)
    {
        $provinsi = Provinsi::where('nama_provinsi', $data)->exists();
        return $provinsi;
    }

    public function simpanProvinsi($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('SIMPAN-PROVINSI');
        try {
            $log->info("<================= MULAI PROSES UBAH DATA DI DATABASE Provinsi =================>");
            $log->info("Data dari controller: " . json_encode($data));

            $kd_provinsi = $this->generateKdProvinsi();
            $log->info("<================= BERHASIL BUAT PK =================>");
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

            $provinsi = new Provinsi();
            $provinsi->kd_provinsi = $kd_provinsi;
            $provinsi->nama_provinsi = $data['nama_provinsi'];
            $provinsi->status_tampil = "ACTIVE";
            $provinsi->tgl_input = $tgl_input;
            $provinsi->bln_input = $bln_input;
            $provinsi->thn_input = $thn_input;
            $provinsi->waktu_input = $waktu_input;
            $provinsi->user_input = $data['kd_user'];
            $provinsi->alamat_device = $ipDevice;
            $provinsi->type_device = $deviceType;
            $provinsi->device = $device;

            $provinsi->save();
            $log->info("BERHASIL SIMPAN DATA");

            DB::commit();

            $log->info("PROSES SELESAI");
            return $provinsi;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }
}
