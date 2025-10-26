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

    public function allKotaKabupaten()
    {
        $kotakabupaten = KotaKabupaten::with('provinsi')->get();

        $result = $kotakabupaten->map(function ($data) {
            return [
                'kd_kota_kabupaten' => $data->kd_kota_kabupaten,
                'nama_kota_kabupaten' => $data->nama_kota_kabupaten,
                'status_tampil' => $data->status_tampil,
                'kd_provinsi' => $data->kd_provinsi,
                'provinsi' => [
                    'kd_provinsi' => $data->provinsi->kd_provinsi,
                    'nama_provinsi' => $data->provinsi->nama_provinsi,
                ]
            ];
        });

        return $result;
    }

    public function allKecamatan()
    {
        $kecamatan = Kecamatan::with('kota_kabupaten')->get();

        $result = $kecamatan->map(function ($data) {
            return [
                'kd_kecamatan' => $data->kd_kecamatan,
                'kd_kota_kabupaten' => $data->kd_kota_kabupaten,
                'nama_kecamatan' => $data->nama_kecamatan,
                'status_tampil' => $data->status_tampil,
                'kota_kabupaten' => [
                    'nama_kota_kabupaten' => $data->kota_kabupaten->nama_kota_kabupaten ?? null,
                    'kd_provinsi' => $data->kota_kabupaten->kd_provinsi ?? null,
                    'provinsi' => [
                        'nama_provinsi' => $data->kota_kabupaten->provinsi->nama_provinsi ?? null,
                    ]
                ]
            ];
        });

        return $result;
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

    private function generateKdKabupatenKota()
    {
        $currentMonth = Carbon::now()->format('Ym');
        $prefix = 'KTK-' . $currentMonth . '-';

        $kotaKabupaten = KotaKabupaten::where('kd_kota_kabupaten', 'LIKE', $prefix . '%')
            ->orderBy('kd_kota_kabupaten', 'DESC')
            ->first();

        if (!$kotaKabupaten) {
            return $prefix . '0000';
        }

        $lastId = $kotaKabupaten->kd_kota_kabupaten;
        $lastNumber = substr($lastId, -4);

        $newNumber = str_pad(intval($lastNumber) + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $newNumber;
    }

    private function generateKdKecamatan()
    {
        $currentMonth = Carbon::now()->format('Ym');
        $prefix = 'KEC-' . $currentMonth . '-';

        $Kecamatan = Kecamatan::where('kd_kecamatan', 'LIKE', $prefix . '%')
            ->orderBy('kd_kecamatan', 'DESC')
            ->first();

        if (!$Kecamatan) {
            return $prefix . '0000';
        }

        $lastId = $Kecamatan->kd_kecamatan;
        $lastNumber = substr($lastId, -4);

        $newNumber = str_pad(intval($lastNumber) + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $newNumber;
    }

    public function cekNamaProvinsi($data)
    {
        $provinsi = Provinsi::where('nama_provinsi', $data)->exists();
        return $provinsi;
    }

    public function cekProvinsiByKode($data)
    {
        $provinsi = Provinsi::where('kd_provinsi', $data)->exists();
        return $provinsi;
    }

    public function cekKotaKabupatenByKode($data)
    {
        $KotaKabupaten = KotaKabupaten::where('kd_kota_kabupaten', $data)->exists();
        return $KotaKabupaten;
    }

    public function cekKecamatanByKode($data)
    {
        // $kecamatan = Kecamatan::where('kd_kecamatan', $data)->exists();
        $kecamatan = Kecamatan::where('kd_kecamatan', $data)->first();
        return $kecamatan;
    }

    public function simpanProvinsi($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('SIMPAN-PROVINSI');
        try {
            $log->info("<================= MULAI PROSES SIMPAN DATA DI DATABASE Provinsi =================>");
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
            $provinsi->user_input = $data['user_input'];
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

    public function ubahProvinsi($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('UBAH-PROVINSI');

        try {
            $log->info("<================= MULAI PROSES SIMPAN DATA KE DATABASE Provinsi =================>");
            $log->info("Data dari controller: " . json_encode($data));

            $provinsi = Provinsi::find($data['kd_provinsi']);

            if ($provinsi) {
                $provinsi->update([
                    'nama_provinsi' => $data['nama_provinsi'],
                    'status_tampil' => $data['status_tampil'],
                ]);
            }

            DB::commit();

            $log->info("PROSES UBAH PROVNISI SELESAI");
            return $provinsi;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }

    public function simpanKabupatenKota($data)
    {
        try {
            DB::beginTransaction();
            $log = AppLogger::getLogger('SIMPAN-KABUPATEN-KOTA');

            $log->info("<================= MULAI PROSES SIMPAN DATA DI DATABASE KotaKabupaten =================>");
            $log->info("Data dari controller: " . json_encode($data));

            $kd_kota_kabupaten = $this->generateKdKabupatenKota();
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

            $kotaKabupaten = new KotaKabupaten();
            $kotaKabupaten->kd_kota_kabupaten = $kd_kota_kabupaten;
            $kotaKabupaten->kd_provinsi = $data['kd_provinsi'];
            $kotaKabupaten->nama_kota_kabupaten = $data['nama_kota_kabupaten'];
            $kotaKabupaten->status_tampil = "ACTIVE";
            $kotaKabupaten->tgl_input = $tgl_input;
            $kotaKabupaten->bln_input = $bln_input;
            $kotaKabupaten->thn_input = $thn_input;
            $kotaKabupaten->waktu_input = $waktu_input;
            $kotaKabupaten->user_input = $data['user_input'];
            $kotaKabupaten->alamat_device = $ipDevice;
            $kotaKabupaten->type_device = $deviceType;
            $kotaKabupaten->device = $device;

            $kotaKabupaten->save();
            $log->info("BERHASIL SIMPAN DATA");

            DB::commit();

            $log->info("PROSES SELESAI");
            return $kotaKabupaten;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }

    public function ubahKotaKabupaten($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('UBAH-KOTA-KABUPATEN');
        try {
            $log->info("<================= MULAI PROSES SIMPAN DATA KE DATABASE KotaKabupaten =================>");
            $log->info("Data dari controller: " . json_encode($data));

            $KotaKabupaten = KotaKabupaten::find($data['kd_kota_kabupaten']);

            if ($KotaKabupaten) {
                $KotaKabupaten->update([
                    'nama_kota_kabupaten' => $data['nama_kota_kabupaten'],
                    'status_tampil' => $data['status_tampil'],
                ]);
            }

            DB::commit();

            $log->info("PROSES UBAH KOTA-KABUPATEN SELESAI");
            return $KotaKabupaten;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }

    public function simpanKecamatan($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('SIMPAN-KECAMATAN');
        try {
            $log->info("<================= MULAI PROSES SIMPAN DATA DI DATABASE Kecamatan =================>");
            $log->info("Data dari controller: ADA");

            $kd_kecamatan = $this->generateKdKecamatan();
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

            $kecamatan = new Kecamatan();
            $kecamatan->kd_kecamatan = $kd_kecamatan;
            $kecamatan->kd_kota_kabupaten = $data['kd_kota_kabupaten'];
            $kecamatan->nama_kecamatan = $data['nama_kecamatan'];
            $kecamatan->status_tampil = "ACTIVE";
            $kecamatan->tgl_input = $tgl_input;
            $kecamatan->bln_input = $bln_input;
            $kecamatan->thn_input = $thn_input;
            $kecamatan->waktu_input = $waktu_input;
            $kecamatan->user_input = $data['user_input'];
            $kecamatan->alamat_device = $ipDevice;
            $kecamatan->type_device = $deviceType;
            $kecamatan->device = $device;

            $kecamatan->save();
            $log->info("BERHASIL SIMPAN DATA");

            DB::commit();

            $log->info("PROSES SELESAI");
            return $kecamatan;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }

    public function ubahKecamatan($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('UBAH-KECAMATAN');
        try {
            $log->info("<================= MULAI PROSES SIMPAN DATA KE DATABASE KECAMATAN =================>");
            $log->info("Data dari controller: ADA" . json_encode($data));

            $kecamatan = Kecamatan::find($data['kd_kecamatan']);

            if ($kecamatan) {
                $kecamatan->update([
                    'nama_kecamatan' => $data['nama_kecamatan'],
                    'status_tampil' => $data['status_tampil'],
                ]);
            }

            DB::commit();

            $log->info("PROSES UBAH KOTA-KECAMATAN SELESAI");
            return $kecamatan;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }
}
