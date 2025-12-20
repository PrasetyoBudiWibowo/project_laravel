<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\Divisi;
use App\Models\Departement;
use App\Models\Posisi;
use App\Models\Negara;
use App\Models\agama;
use App\Models\MasterJabatan;
use App\Models\HistoryInputKaryawan;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

use App\Helper\DeviceHelper;
use App\Helper\GeoDetector;
use App\Helper\AppLogger;

class HrdService
{
    public function allDivisi()
    {
        $data = Divisi::all();

        $result = $data->map(function ($item) {
            return [
                'kd_divisi' => Crypt::encryptString($item->kd_divisi),
                'nama_divisi' => $item->nama_divisi,
            ];
        });

        return $result;
    }

    public function allDepartement()
    {
        $data = Departement::with('Divisi')->get();

        $result = $data->map(function ($departement) {
            return [
                'kd_departement' => Crypt::encryptString($departement->kd_departement),
                'nama_departement' => $departement->nama_departement,
                'kd_divisi' => Crypt::encryptString($departement->kd_divisi),
                'divisi' => [
                    'nama_divisi' => $departement->Divisi->nama_divisi,
                ],
            ];
        });
        // foreach ($data as $d) {
        //     $result[] = [
        //         'kd_departement' => Crypt::encryptString($d->kd_departement),
        //         'nama_departement' => $d->nama_departement,
        //         'kd_divisi' => Crypt::encryptString($d->kd_divisi),
        //         'divisi' => [
        //             'nama_divisi' => $d->Divisi->nama_divisi,
        //         ],
        //     ];
        // }

        return $result;

        // cara kedua
        // $data = Departement::select('kd_departement', 'nama_departement', 'kd_divisi')
        //     ->with('Divisi:id,kd_divisi,nama_divisi')
        //     ->get();
    }

    public function allPosisition()
    {
        $data = Posisi::with('Departement')->with('Divisi')->get();

        $result = $data->map(function ($posisi) {
            return [
                'kd_position' => Crypt::encryptString($posisi->kd_position),
                'nama_position' => $posisi->nama_position,
                'departement' => [
                    'kd_departement' => Crypt::encryptString($posisi->departement->kd_departement),
                    'nama_departement' => $posisi->Departement->nama_departement,
                    'divisi' => [
                        'kd_divisi' => Crypt::encryptString($posisi->departement->divisi->kd_divisi),
                        'nama_divisi' => $posisi->Divisi->nama_divisi,
                    ],
                ],
            ];
        });

        return $result;
    }

    public function allCountry()
    {
        $data = Negara::all();
        return $data;
    }

    public function allReligion()
    {
        $data = agama::all();

        $result = $data->map(function ($item) {
            return [
                'kd_agama' => Crypt::encryptString($item->kd_agama),
                'nama_agama' => $item->nama_agama,
            ];
        });

        return $result;
    }

    public function allJabatan()
    {
        $data = MasterJabatan::orderBy('nama_jabatan', 'asc')->get();

        $result = $data->map(function ($datas) {
            return [
                'kd_jabatan' => Crypt::encryptString($datas->kd_jabatan),
                'nama_jabatan' => $datas->nama_jabatan,
                'kd_jabatan_tampil' => $datas->kd_jabatan_tampil,
            ];
        });

        return $result;
    }

    public function allKaryawan()
    {
        $karyawan = Karyawan::with('Divisi')
            ->with('Divisi')
            ->with('Departement')
            ->with('Posisi')
            ->with('JabatanKaryawan')
            ->with('Agama')
            ->get();

        $result = $karyawan->map(function ($data) {
            return [
                'kd_karyawan' => Crypt::encryptString($data->kd_karyawan),
                'nama_karyawan' => $data->nama_karyawan,
                'nama_panggilan_karyawan' => $data->nama_panggilan_karyawan,
                'foto_karyawan' => $data->foto_karyawan,
                'format_gambar' => $data->format_gambar,
                'gender' => $data->gender,
                'emai_pribadi' => $data->emai_pribadi,
                'tgl_lahir' => $data->tgl_lahir,
                'no_ktp' => $data->no_ktp,
                'npwp' => $data->npwp,
                'Agama' => [
                    'kd_agama' => optional($data->Agama)->kd_agama ? Crypt::encryptString(optional($data->Agama)->kd_agama) : null,
                    'nama_agama' => $data->Agama->nama_agama ?? null,
                ],
                'Divisi' => [
                    'kd_divisi' => Crypt::encryptString($data->Divisi->kd_divisi) ?? null,
                    'nama_divisi' => $data->Divisi->nama_divisi ?? null,
                ],
                'Departement' => [
                    'kd_departement' => Crypt::encryptString($data->Departement->kd_departement) ?? null,
                    'nama_departement' => $data->Departement->nama_departement ?? null,
                ],
                'Posisi' => [
                    'kd_position' => Crypt::encryptString($data->Posisi->kd_position) ?? null,
                    'nama_position' => $data->Posisi->nama_position ?? null,
                ],
                'JabatanKaryawan' => [
                    'kd_jabatan' => optional($data->JabatanKaryawan)->kd_jabatan
                        ? Crypt::encryptString(optional($data->JabatanKaryawan)->kd_jabatan)
                        : null,
                    'nama_jabatan' => optional($data->JabatanKaryawan)->nama_jabatan,
                ],
            ];
        });

        return $result;
    }

    public function cekKaryawanByKd($kd_karyawan)
    {
        $karyawan = Karyawan::where('kd_karyawan', $kd_karyawan)
            ->with(['Divisi', 'Departement', 'Posisi', 'JabatanKaryawan', 'Agama'])
            ->first();

        if (!$karyawan) {
            return null;
        }

        return [
            'kd_karyawan' => Crypt::encryptString($karyawan->kd_karyawan),
            'nama_karyawan' => $karyawan->nama_karyawan,
            'nama_panggilan_karyawan' => $karyawan->nama_panggilan_karyawan,
            'foto_karyawan' => $karyawan->foto_karyawan,
            'format_gambar' => $karyawan->format_gambar,
            'gender' => $karyawan->gender,
            'emai_pribadi' => $karyawan->emai_pribadi,
            'tgl_lahir' => $karyawan->tgl_lahir,
            'no_ktp' => $karyawan->no_ktp,
            'tgl_lahir' => $karyawan->tgl_lahir,
            'no_ktp' => $karyawan->no_ktp,
            'npwp' => $karyawan->npwp,
            'kd_provinsi_lahir' => $karyawan->kd_provinsi_lahir,
            'kd_kota_kab_lahir' => $karyawan->kd_kota_kab_lahir,
            'kd_kecamatan_lahir' => $karyawan->kd_kecamatan_lahir,
            'alamat_lahir' => $karyawan->alamat_lahir,
            'kd_provinsi_tinggal' => $karyawan->kd_provinsi_tinggal,
            'kd_kota_kab_tinggal' => $karyawan->kd_kota_kab_tinggal,
            'kd_kecamatan_tinggal' => $karyawan->kd_kecamatan_tinggal,
            'alamat_tinggal' => $karyawan->alamat_tinggal,
            'no_telp1' => $karyawan->no_telp1,
            'no_telp2' => $karyawan->no_telp2,
            'no_telp3' => $karyawan->no_telp3,

            'Agama' => [
                'kd_agama' => $karyawan->Agama ? Crypt::encryptString($karyawan->Agama->kd_agama) : null,
                'nama_agama' => $karyawan->Agama->nama_agama ?? null,
            ],
            'Divisi' => [
                'kd_divisi' => $karyawan->Divisi
                    ? Crypt::encryptString($karyawan->Divisi->kd_divisi)
                    : null,
                'nama_divisi' => $karyawan->Divisi->nama_divisi ?? null,
            ],

            'Departement' => [
                'kd_departement' => $karyawan->Departement
                    ? Crypt::encryptString($karyawan->Departement->kd_departement)
                    : null,
                'nama_departement' => $karyawan->Departement->nama_departement ?? null,
            ],

            'Posisi' => [
                'kd_position' => $karyawan->Posisi
                    ? Crypt::encryptString($karyawan->Posisi->kd_position)
                    : null,
                'nama_position' => $karyawan->Posisi->nama_position ?? null,
            ],

            'JabatanKaryawan' => [
                'kd_jabatan' => $karyawan->JabatanKaryawan
                    ? Crypt::encryptString($karyawan->JabatanKaryawan->kd_jabatan)
                    : null,
                'nama_jabatan' => $karyawan->JabatanKaryawan->nama_jabatan ?? null,
            ],
        ];
    }


    public function cekKaryawanByPk($data)
    {
        $karyawan = Karyawan::find($data);
        return $karyawan;
    }

    public function cekDivisi($data)
    {
        $divisi = Divisi::find($data);
        return $divisi;
    }

    public function cekDepartement($data)
    {
        $divisi = Departement::find($data);
        return $divisi;
    }

    public function cekPosisi($data)
    {
        $posisi = Posisi::find($data);
        return $posisi;
    }

    public function cekJabatan($data)
    {
        $jabatan = MasterJabatan::find($data);
        return $jabatan;
    }

    public function religionCek($data)
    {
        $agama = agama::find($data);
        return $agama;
    }

    private function generateKdDivisi()
    {
        $currentMonth = Carbon::now()->format('Ym');
        $prefix = 'DVS-' . $currentMonth . '-';

        $lastDivisi = Divisi::where('kd_divisi', 'LIKE', $prefix . '%')
            ->orderBy('kd_divisi', 'DESC')
            ->first();

        if (!$lastDivisi) {
            return $prefix . '0000';
        }

        $lastId = $lastDivisi->kd_divisi;
        $lastNumber = substr($lastId, -4);

        $newNumber = str_pad(intval($lastNumber) + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $newNumber;
    }

    private function generateKdDepartement()
    {
        $currentMonth = Carbon::now()->format('Ym');
        $prefix = 'DPT-' . $currentMonth . '-';

        $lasDepartement = Departement::where('kd_departement', 'LIKE', $prefix . '%')
            ->orderBy('kd_departement', 'DESC')
            ->first();

        if (!$lasDepartement) {
            return $prefix . '0000';
        }

        $lastId = $lasDepartement->kd_departement;
        $lastNumber = substr($lastId, -4);

        $newNumber = str_pad(intval($lastNumber) + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $newNumber;
    }

    private function generateKdPosisi()
    {
        $currentMonth = Carbon::now()->format('Ym');
        $prefix = 'PST-' . $currentMonth . '-';

        $lastPosisition = Posisi::where('kd_position', 'LIKE', $prefix . '%')
            ->orderBy('kd_position', 'DESC')
            ->first();

        if (!$lastPosisition) {
            return $prefix . '0000';
        }

        $lastId = $lastPosisition->kd_position;
        $lastNumber = substr($lastId, -4);

        $newNumber = str_pad(intval($lastNumber) + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $newNumber;
    }

    private function generateKdJabatan()
    {
        $currentMonth = Carbon::now()->format('Ym');
        $prefix = 'JBT-' . $currentMonth . '-';

        $kd_jabatan_terakhir = MasterJabatan::where('kd_jabatan', 'LIKE', $prefix . '%')
            ->orderBy('kd_jabatan', 'DESC')
            ->first();

        if (!$kd_jabatan_terakhir) {
            return $prefix . '0000';
        }

        $lastId = $kd_jabatan_terakhir->kd_jabatan;
        $lastNumber = substr($lastId, -4);

        $newNumber = str_pad(intval($lastNumber) + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $newNumber;
    }

    private function generateKdKaryawan()
    {
        $currentMonth = Carbon::now()->format('Ym');
        $prefix = 'KRY-' . $currentMonth . '-';

        $lastKaryawan = Karyawan::where('kd_karyawan', 'LIKE', $prefix . '%')
            ->orderBy('kd_karyawan', 'DESC')
            ->first();

        if (!$lastKaryawan) {
            return $prefix . '0000';
        }

        $lastId = $lastKaryawan->kd_karyawan;
        $lastNumber = substr($lastId, -4);

        $newNumber = str_pad(intval($lastNumber) + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $newNumber;
    }

    private function generateKdHistoryInputkaryawan()
    {
        $currentMonth = Carbon::now()->format('Ym');
        $prefix = 'HIMK-' . $currentMonth . '-';

        $lastHistoryinput = HistoryInputKaryawan::where('kd_history_input_master_karyawan', 'LIKE', $prefix . '%')
            ->orderBy('kd_history_input_master_karyawan', 'DESC')
            ->first();

        if (!$lastHistoryinput) {
            return $prefix . '0000';
        }

        $lastId = $lastHistoryinput->kd_history_input_master_karyawan;
        $lastNumber = substr($lastId, -4);

        $newNumber = str_pad(intval($lastNumber) + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $newNumber;
    }

    public function generateFotoKaryawan(string $kd_karyawan)
    {
        $formatDate = Carbon::now()->format('Ym');
        $prefix = 'IMGKRY-' . $formatDate . '-';

        $oldImg = Karyawan::where('kd_karyawan', $kd_karyawan)
            ->value('foto_karyawan');

        if (!empty($oldImg)) {
            $oldFilePath = public_path('assets/img/karyawan/' . $oldImg);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        $lastImage = Karyawan::where('foto_karyawan', 'LIKE', $prefix . '%')
            ->max('foto_karyawan');

        if ($lastImage) {
            $lastNumber = (int) substr($lastImage, 11, 4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix
            . str_pad($newNumber, 4, '0', STR_PAD_LEFT)
            . '-' . $kd_karyawan;
    }

    private function buatHistoryInputKaryawan($dataKaryawan)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('BUAT-HISTORY-INPUT-KARYAWAN');
        try {
            $log->info("<================= MULAI PROSES SIMPAN DATA KE DATABASE HISTORY INPUT KARYAWAN =================>");

            $kd_history_input_master_karyawan = $this->generateKdHistoryInputkaryawan();
            $log->info("<================= BEHASIL BUAT PK generateKdHistoryInputkaryawan  =================>");

            $jenis_input =
                $dataKaryawan['type_history'] === "INPUT"
                ? "INPUT"
                : (
                    $dataKaryawan['type_history'] === "EDIT-FOTO" ||
                    $dataKaryawan['type_history'] === "DATA PRIBADI"
                    ? "EDIT"
                    : null
                );

            $keterangan_input = $dataKaryawan['type_history'] === "INPUT" ? "INPUT KARYAWAN BARU" : $dataKaryawan['keterangan_input'];

            $historyInputMasterKaryawan = HistoryInputKaryawan::create([
                'kd_history_input_master_karyawan' => $kd_history_input_master_karyawan,
                'jenis_input' => $jenis_input,
                'keterangan_input' => $keterangan_input,
                'kd_karyawan' => $dataKaryawan['kd_karyawan'],
                'nama_karyawan' => $dataKaryawan['nama_karyawan'],
                'user_input' => $dataKaryawan['user_input'],
                'tgl_input' => $dataKaryawan['tgl_input'],
                'bln_input' => $dataKaryawan['bln_input'],
                'thn_input' => $dataKaryawan['thn_input'],
                'waktu_input' => $dataKaryawan['waktu_input'],
                'alamat_device' => $dataKaryawan['alamat_device'],
                'type_device' => $dataKaryawan['type_device'],
                'device' => $dataKaryawan['device'],
            ]);


            DB::commit();

            return $historyInputMasterKaryawan;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }

    public function simpanDivisi($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('SIMPAN-DIVISI');
        try {
            $log->info("<================= MULAI PROSES SIMPAN DATA DI DATABASE MASTER DIVISI =================>");

            $kd_divisi = $this->generateKdDivisi();
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

            $divisi = Divisi::create([
                'kd_divisi' => $kd_divisi,
                'nama_divisi' => $data['nama_divisi'],
                'user_input' => $data['user_input'],
                'tgl_input' => $tgl_input,
                'bln_input' => $bln_input,
                'thn_input' => $thn_input,
                'waktu_input' => $waktu_input,
                'alamat_device' => $ipDevice,
                'type_device' => $deviceType,
                'device' => $device,
            ]);

            $log->info("BERHASIL SIMPAN DATA");

            DB::commit();

            $log->info("PROSES SELESAI");
            return $divisi;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }

    public function ubahDivisi($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('UBAH-DIVISI');
        try {
            $log->info("<================= MULAI PROSES UBAH DATA DI DATABASE MASTER DIVISI =================>");

            $divisi = Divisi::find($data['kd_divisi']);

            if ($divisi) {
                $divisi->update([
                    'nama_divisi' => $data['nama_divisi'],
                ]);
            }

            $log->info("BERHASIL UBAH DATA");

            DB::commit();

            $log->info("PROSES UBAH DIVISI SELESAI");
            return $divisi;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }

    public function simpanDepartement($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('SIMPAN-DEPARTEMENT');
        try {
            $log->info("<================= MULAI PROSES SIMPAN DATA DI DATABASE MASTER DEPARTEMENT =================>");
            // $log->info("Data CONTROLLER: ADA" . json_encode($data));

            $kd_departement = $this->generateKdDepartement();
            $log->info("<================= BERHASIL BUAT PK =================>");

            // $log->info("Data PK: ADA" . json_encode($kd_departement));

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

            $departement = Departement::create([
                'kd_departement' => $kd_departement,
                'kd_divisi' => $data['kd_divisi'],
                'nama_departement' => $data['nama_departement'],
                'user_input' => $data['user_input'],
                'tgl_input' => $tgl_input,
                'bln_input' => $bln_input,
                'thn_input' => $thn_input,
                'waktu_input' => $waktu_input,
                'alamat_device' => $ipDevice,
                'type_device' => $deviceType,
                'device' => $device,
            ]);

            if (!$departement) {
                throw new \Exception("GAGAL ADA DATA YANG SALAH");
            }

            // $log->info("Data SIMPAN: ADA" . json_encode($departement));
            $log->info("BERHASIL SIMPAN DATA");

            DB::commit();

            $log->info("PROSES SELESAI");
            return $departement;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }

    public function ubahDepartement($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('UBAH-DEPARTEMENT');
        try {
            $log->info("<================= MULAI PROSES UBAH DATA DI DATABASE MASTER DEPARTEMENT =================>");

            if (empty($data['kd_departement']) || empty($data['kd_divisi']) || empty($data['nama_departement'])) {
                throw new \Exception("Data tidak lengkap untuk mengubah departement.");
            }

            $departement = Departement::find($data['kd_departement']);

            if (!$departement) {
                throw new \Exception("Departement tidak ditemukan.");
            }

            if ($departement) {
                $departement->update([
                    'kd_divisi' => $data['kd_divisi'],
                    'nama_departement' => $data['nama_departement'],
                ]);
            }

            $log->info("BERHASIL UBAH DATA");

            DB::commit();

            $log->info("PROSES UBAH DIVISI SELESAI");
            return $departement;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }

    public function simpanPosisi($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('SIMPAN-POSISI');
        try {
            $log->info("<================= MULAI PROSES SIMPAN DATA DI DATABASE MASTER DEPARTEMENT =================>");
            // $log->info("Data CONTROLLER: ADA" . json_encode($data));

            $kd_posisi =  $this->generateKdPosisi();
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

            $posisi = Posisi::create([
                'kd_position' => $kd_posisi,
                'kd_divisi' => $data['kd_divisi'],
                'kd_departement' => $data['kd_departement'],
                'nama_position' => $data['nama_position'],
                'user_input' => $data['user_input'],
                'tgl_input' => $tgl_input,
                'bln_input' => $bln_input,
                'thn_input' => $thn_input,
                'waktu_input' => $waktu_input,
                'alamat_device' => $ipDevice,
                'type_device' => $deviceType,
                'device' => $device,
            ]);

            if (!$posisi) {
                throw new \Exception("GAGAL ADA DATA YANG SALAH");
            }

            // $log->info("Data SIMPAN: ADA" . json_encode($posisi));
            $log->info("BERHASIL SIMPAN DATA");

            DB::commit();

            $log->info("PROSES SELESAI");
            return $posisi;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }

    public function ubahPosisi($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('UBAH-MASTER-POSISI');
        try {
            $log->info("<================= MULAI PROSES UBAH DATA DI DATABASE MASTER POSISITION =================>");

            if (empty($data['kd_position']) || empty($data['kd_departement']) || empty($data['kd_divisi']) || empty($data['nama_position'])) {
                throw new \Exception("Data tidak lengkap untuk mengubah Posisi.");
            }

            // $log->info("Data dari controller: ADA" . json_encode($data));


            $posisi = Posisi::find($data['kd_position']);

            // $log->info("Data dari posisi: ADA" . json_encode($posisi));

            if (!$posisi) {
                throw new \Exception("Posisi tidak ditemukan.");
            }

            if ($posisi) {
                $posisi->update([
                    'kd_divisi' => $data['kd_divisi'],
                    'kd_departement' => $data['kd_departement'],
                    'nama_position' => $data['nama_position'],
                ]);
            }

            $log->info("BERHASIL UBAH DATA");

            DB::commit();

            $log->info("PROSES UBAH POSISI SELESAI");
            return $posisi;
        } catch (\Throwable $th) {
            DB::rollBack();
            // return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }

    public function simpanJabatan($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('SIMPAN-JABATAN');
        try {
            $log->info("<================= MULAI PROSES SIMPAN DATA DI DATABASE MASTER JABATAN =================>");

            if (empty($data['nama_jabatan']) || empty($data['user_input'])) {
                throw new \Exception("Data yang dikirim tidak lengkap untuk simpanJabatan.");
            }

            $kdJabatan = $this->generateKdJabatan();
            $log->info("<================= BERHASIL BUAT PK =================>");

            // $log->info("Data CONTROLLER: PK" . json_encode($kdJabatan));

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

            $jabatan = MasterJabatan::create([
                'kd_jabatan' => $kdJabatan,
                'nama_jabatan' => $data['nama_jabatan'],
                'user_input' => $data['user_input'],
                'tgl_input' => $tgl_input,
                'bln_input' => $bln_input,
                'thn_input' => $thn_input,
                'waktu_input' => $waktu_input,
                'alamat_device' => $ipDevice,
                'type_device' => $deviceType,
                'device' => $device,
            ]);

            if (!$jabatan) {
                throw new \Exception("GAGAL ADA DATA YANG SALAH");
            }

            $log->info("BERHASIL SIMPAN DATA");

            DB::commit();

            $log->info("PROSES SELESAI");
            return $jabatan;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage(), 'line' => $th->getLine()], 500);
            throw $th;
        }
    }

    public function simpanKaryawan($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('SIMPAN-KARYAWAN');
        try {
            $log->info("<================= MULAI PROSES SIMPAN DATA DI DATABASE MASTER KARYAWAN =================>");

            if (empty($data['nama_karyawan']) || empty($data['kd_divisi']) || empty($data['kd_departement']) || empty($data['kd_position']) || empty($data['user_input'])) {
                throw new \Exception("Data yang dikirim tidak lengkap untuk simpanKaryawan.");
            }

            $kd_karyawan = $this->generateKdKaryawan();
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

            $karyawan = Karyawan::create([
                'kd_karyawan' => $kd_karyawan,
                'nama_karyawan' => $data['nama_karyawan'],
                'nama_panggilan_karyawan' => $data['nama_panggilan_karyawan'] ?? null,
                'kd_divisi' => $data['kd_divisi'],
                'kd_departement' => $data['kd_departement'],
                'kd_position' => $data['kd_position'],
                'kd_jabatan' => $data['kd_jabatan'] ?? null,
                'user_input' => $data['user_input'],
                'tgl_input' => $tgl_input,
                'bln_input' => $bln_input,
                'thn_input' => $thn_input,
                'waktu_input' => $waktu_input,
                'alamat_device' => $ipDevice,
                'type_device' => $deviceType,
                'device' => $device
            ]);

            if (!$karyawan) {
                throw new \Exception("GAGAL ADA DATA YANG SALAH");
            }

            $datakaryawan = [
                'type_history' => "INPUT",
                'kd_karyawan' => $karyawan->kd_karyawan,
                'nama_karyawan' => $karyawan->nama_karyawan,
                'user_input' => $karyawan->user_input,
                'tgl_input' => $karyawan->tgl_input,
                'bln_input' => $karyawan->bln_input,
                'thn_input' => $karyawan->thn_input,
                'waktu_input' => $karyawan->waktu_input,
                'alamat_device' => $karyawan->alamat_device,
                'type_device' => $karyawan->type_device,
                'device' => $karyawan->device,
            ];

            $log->info("<================= MULAI PROSES SIMPAN DATA DI DATABASE HISTORY INPUT MASTER KARYAWAN =================>");
            $historyInputKaryawan = $this->buatHistoryInputKaryawan($datakaryawan);

            if (!$historyInputKaryawan->kd_history_input_master_karyawan) {
                $log->error("Validasi gagal untuk HISTORY INPUT MASTER KARYAWAN", [
                    'invalid_input' => 'HISTORY INPUT MASTER KARYAWAN',
                    'expected_format' => 'HISTORY INPUT MASTER KARYAWAN GAGAL DI BUAT'
                ]);

                throw new \Exception("HISTORY INPUT MASTER KARYAWAN GAGAL DI BUAT");
            }

            $log->info("BERHASIL SIMPAN DATA");

            DB::commit();

            $log->info("PROSES SELESAI");
            return $karyawan;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }

    public function ubahKaryawan($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('UBAH-KARYAWAN');
        try {
            $log->info("<================= MULAI PROSES UBAH DATA DI DATABASE MASTER KARYAWAN =================>");

            $karyawan = Karyawan::find($data['kd_karyawan']);

            if (!$karyawan) {
                throw new \Exception("Karyawan tidak ditemukan.");
            }

            $now = Carbon::now('Asia/Jakarta');
            $tgl_ubah = $now->toDateString();
            $waktu_ubah = $now->format('H:i:s');
            $bln_ubah = $now->format('m');
            $thn_ubah = $now->year;

            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $deviceInfo = DeviceHelper::detectDevice($userAgent);
            $deviceType = $deviceInfo['deviceType'];
            $device = $deviceInfo['browser'];

            $ipDetector = GeoDetector::getDeviceLocation();
            $ipDevice = isset($ipDetector['ip']) ? $ipDetector['ip'] : 'Unknown IP';

            if ($data['type'] === "FOTO") {
                if (empty($data['type']) || empty($data['kd_karyawan']) || empty($data['foto_karyawan']) || empty($data['user_ubah'])) {
                    throw new \Exception("Data yang dikirim tidak lengkap untuk simpanKaryawan.");
                }

                $karyawan->update([
                    'foto_karyawan' => $data['foto_karyawan'],
                    'format_gambar' => $data['format_gambar'],
                    'user_ubah' => $data['user_ubah'],
                    'tgl_ubah' => $tgl_ubah,
                    'bln_ubah' => $bln_ubah,
                    'thn_ubah' => $thn_ubah,
                    'waktu_ubah' => $waktu_ubah,
                    'alamat_device_ubah' => $ipDevice,
                    'type_device_ubah' => $deviceType,
                    'device_ubah' => $device
                ]);

                if (!$karyawan) {
                    throw new \Exception("GAGAL ADA DATA YANG SALAH");
                }

                // $log->info("KARYAWAN: ADA" . json_encode($karyawan));

                $datakaryawan = [
                    'type_history' => "EDIT-FOTO",
                    'kd_karyawan' => $karyawan->kd_karyawan,
                    'nama_karyawan' => $karyawan->nama_karyawan,
                    'user_input' => $karyawan->user_ubah,
                    'tgl_input' => $karyawan->tgl_ubah,
                    'bln_input' => $karyawan->bln_ubah,
                    'thn_input' => $karyawan->thn_ubah,
                    'waktu_input' => $karyawan->waktu_ubah,
                    'alamat_device' => $karyawan->alamat_device_ubah,
                    'type_device' => $karyawan->type_device_ubah,
                    'device' => $karyawan->device_ubah,
                    'keterangan_input' => $data['keterangan_input'],
                ];

                $log->info("<================= MULAI PROSES SIMPAN DATA DI DATABASE HISTORY INPUT MASTER KARYAWAN =================>");
                $historyInputKaryawan = $this->buatHistoryInputKaryawan($datakaryawan);

                if (!$historyInputKaryawan || !$historyInputKaryawan->kd_history_input_master_karyawan) {
                    $log->error("Validasi gagal untuk HISTORY INPUT MASTER KARYAWAN", [
                        'invalid_input' => 'HISTORY INPUT MASTER KARYAWAN',
                        'expected_format' => 'HISTORY INPUT MASTER KARYAWAN GAGAL DI BUAT'
                    ]);

                    throw new \Exception("HISTORY INPUT MASTER KARYAWAN GAGAL DI BUAT");
                }
            } else if ($data['type'] === "DATA PRIBADI") {
                if (empty($data['type']) || empty($data['kd_karyawan']) || empty($data['user_ubah'])) {
                    throw new \Exception("Data yang dikirim tidak lengkap untuk simpanKaryawan.");
                }

                $karyawan->update([
                    'nama_karyawan' => $data['nama_karyawan'],
                    'nama_panggilan_karyawan' => $data['nama_panggilan_karyawan'],
                    'gender' => $data['gender'],
                    'kd_agama' => $data['kd_agama'],
                    'tgl_lahir' => $data['tgl_lahir'],
                    'bln_lahir' => $data['bln_lahir'],
                    'thn_lahir' => $data['thn_lahir'],
                    'no_ktp' => $data['no_ktp'],
                    'npwp' => $data['npwp'],
                    'kd_provinsi_lahir' => $data['kd_provinsi_lahir'],
                    'kd_kota_kab_lahir' => $data['kd_kota_kab_lahir'],
                    'kd_kecamatan_lahir' => $data['kd_kecamatan_lahir'],
                    'alamat_lahir' => $data['alamat_lahir'],
                    'kd_provinsi_tinggal' => $data['kd_provinsi_tinggal'],
                    'kd_kota_kab_tinggal' => $data['kd_kota_kab_tinggal'],
                    'kd_kecamatan_tinggal' => $data['kd_kecamatan_tinggal'],
                    'alamat_tinggal' => $data['alamat_tinggal'],
                    'tinggi_karyawan' => $data['tinggi_karyawan'],
                    'berat_karyawan' => $data['berat_karyawan'],
                    'no_telp1' => $data['no_telp1'],
                    'no_telp2' => $data['no_telp2'],
                    'no_telp3' => $data['no_telp3'],
                    'user_ubah' => $data['user_ubah'],
                    'tgl_ubah' => $tgl_ubah,
                    'bln_ubah' => $bln_ubah,
                    'thn_ubah' => $thn_ubah,
                    'waktu_ubah' => $waktu_ubah,
                    'alamat_device_ubah' => $ipDevice,
                    'type_device_ubah' => $deviceType,
                    'device_ubah' => $device
                ]);

                if (!$karyawan) {
                    throw new \Exception("GAGAL ADA DATA YANG SALAH");
                }

                $datakaryawan = [
                    'type_history' => "DATA PRIBADI",
                    'kd_karyawan' => $karyawan->kd_karyawan,
                    'nama_karyawan' => $karyawan->nama_karyawan,
                    'user_input' => $karyawan->user_ubah,
                    'tgl_input' => $karyawan->tgl_ubah,
                    'bln_input' => $karyawan->bln_ubah,
                    'thn_input' => $karyawan->thn_ubah,
                    'waktu_input' => $karyawan->waktu_ubah,
                    'alamat_device' => $karyawan->alamat_device_ubah,
                    'type_device' => $karyawan->type_device_ubah,
                    'device' => $karyawan->device_ubah,
                    'keterangan_input' => $data['keterangan_input'],
                ];

                $log->info("<================= MULAI PROSES SIMPAN DATA DI DATABASE HISTORY INPUT MASTER KARYAWAN =================>");
                $historyInputKaryawan = $this->buatHistoryInputKaryawan($datakaryawan);

                if (!$historyInputKaryawan || !$historyInputKaryawan->kd_history_input_master_karyawan) {
                    $log->error("Validasi gagal untuk HISTORY INPUT MASTER KARYAWAN", [
                        'invalid_input' => 'HISTORY INPUT MASTER KARYAWAN',
                        'expected_format' => 'HISTORY INPUT MASTER KARYAWAN GAGAL DI BUAT'
                    ]);

                    throw new \Exception("HISTORY INPUT MASTER KARYAWAN GAGAL DI BUAT");
                }
            }

            $log->info("BERHASIL UBAH DATA");

            DB::commit();

            $log->info("PROSES UBAH POSISI SELESAI");
            return $karyawan;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }
}
