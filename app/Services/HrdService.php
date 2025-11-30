<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\Divisi;
use App\Models\Departement;
use App\Models\Posisi;
use App\Models\Negara;
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
            ->with('Departement')
            ->with('Posisi')
            ->with('HistoryKontrak')
            ->get();

        $result = [];

        foreach ($karyawan as $kr) {
            $result[] = [
                'kd_karyawan' => $kr->kd_karyawan,
                'nama_karyawan' => $kr->nama_karyawan,
                'nama_panggilan_karyawan' => $kr->nama_panggilan_karyawan,
                'gender' => $kr->gender,
                'tgl_lahir' => $kr->tgl_lahir,
                'bln_lahir' => $kr->bln_lahir,
                'thn_lahir' => $kr->thn_lahir,
                'email_pribadi' => $kr->email_pribadi,
                'kd_negara' => $kr->kd_negara,
                'agama' => $kr->agama,
                'npwp' => $kr->npwp,
                'no_ktp' => $kr->npwp,
                'tgl_awal_kontrak' => $kr->tgl_awal_kontrak,
                'tgl_bergabung' => $kr->tgl_bergabung,
                'bln_bergabung' => $kr->bln_bergabung,
                'thn_bergabung' => $kr->thn_bergabung,
                'tgl_akhir_kontrak' => $kr->tgl_akhir_kontrak,
                'tgl_keluar' => $kr->tgl_keluar,
                'bln_keluar' => $kr->bln_keluar,
                'thn_keluar' => $kr->thn_keluar,
                'foto_karyawan' => $kr->foto_karyawan,
                'format_gambar' => $kr->format_gambar,
                'gaji_angka' => $kr->gaji_angka,
                'tempat_lahir' => $kr->tempat_lahir,
                'provinsi_lahir' => $kr->provinsi_lahir,
                'kota_kab_lahir' => $kr->kota_kab_lahir,
                'kecamatan_lahir' => $kr->kecamatan_lahir,
                'provinsi_tinggal' => $kr->provinsi_tinggal,
                'kota_kab_tinggal' => $kr->kota_kab_tinggal,
                'kecamatan_tinggal' => $kr->kecamatan_tinggal,
                'alamat_tinggal' => $kr->alamat_tinggal,
                'kd_divisi' => $kr->kd_divisi,
                'kd_departement' => $kr->kd_departement,
                'kd_position' => $kr->kd_position,
                'status_karyawan' => $kr->status_karyawan,
                'daftar_sistem' => $kr->daftar_sistem,
                'no_telp1' => $kr->no_telp1,
                'no_telp2' => $kr->no_telp2,
                'no_telp3' => $kr->no_telp3,
                'daftar_sales' => $kr->daftar_sales,
                'daftar_spv_sales' => $kr->daftar_spv_sales,
                'negara' => [
                    'kd_negara' => $kr->Negara->kd_negara ?? null,
                    'name'      => $kr->Negara->name ?? null,
                ],
                'divisi' => [
                    'kd_divisi' => $kr->Divisi->kd_divisi,
                    'nama_divisi' => $kr->Divisi->nama_divisi,
                ],
                'departement' => [
                    'kd_departement' => $kr->Departement->kd_departement,
                    'nama_departement' => $kr->Departement->nama_departement,
                ],
                'posisi' => [
                    'kd_position' => $kr->Posisi->kd_position ?? null,
                    'nama_position' => $kr->Posisi->nama_position ?? null,
                ],
                'ProvinsiLahir' => [
                    'kd_provinsi' => $kr->ProvinsiLahir->kd_provinsi,
                    'nama_provinsi' => $kr->ProvinsiLahir->nama_provinsi,
                ],
                'ProvinsiTinggal' => [
                    'kd_provinsi' => $kr->ProvinsiTinggal->kd_provinsi,
                    'nama_provinsi' => $kr->ProvinsiTinggal->nama_provinsi,
                ],
                'KotaKabLahir' => [
                    'kd_kota_kabupaten' => $kr->KotaKabLahir->kd_kota_kabupaten,
                    'nama_kota_kabupaten' => $kr->KotaKabLahir->nama_kota_kabupaten,
                ],
                'KotaKabTinggal' => [
                    'kd_kota_kabupaten' => $kr->KotaKabTinggal->kd_kota_kabupaten,
                    'nama_kota_kabupaten' => $kr->KotaKabTinggal->nama_kota_kabupaten,
                ],
                'KecamatanLahir' => [
                    'kd_kecamatan' => $kr->KecamatanLahir->kd_kecamatan,
                    'nama_kecamatan' => $kr->KecamatanLahir->nama_kecamatan,
                ],
                'KecamatanTinggal' => [
                    'kd_kecamatan' => $kr->KecamatanTinggal->kd_kecamatan,
                    'nama_kecamatan' => $kr->KecamatanTinggal->nama_kecamatan,
                ],
                'historyKontrak' => $kr->HistoryKontrak->map(function ($kontrak) {
                    return [
                        'kd_hsr_kontrak_karyawan' => $kontrak->kd_hsr_kontrak_karyawan,
                        'kd_karyawan' => $kontrak->kd_karyawan,
                        'tgl_awal' => $kontrak->tgl_awal,
                        'tgl_akhir' => $kontrak->tgl_akhir,
                        'status_kontrak' => $kontrak->status_kontrak,
                        'note' => $kontrak->note,
                        'karyawan' => [
                            'nama_karyawan' => $kontrak->karyawan->nama_karyawan
                        ]
                    ];
                }),
                'HistoryPenempatan' => $kr->HistoryPenempatan->map(function ($penempatan) {
                    return [
                        'kd_penempatan_karyawan' => $penempatan->kd_penempatan_karyawan,
                        'kd_karyawan' => $penempatan->kd_karyawan,
                        'tgl_awal_penempatan' => $penempatan->tgl_awal_penempatan,
                        'tgl_akhir_penempatan' => $penempatan->tgl_akhir_penempatan,
                        'doc_penempatan' => $penempatan->doc_penempatan,
                        'note' => $penempatan->note,
                        'doc_penempatan' => $penempatan->doc_penempatan,
                        'karyawan' => [
                            'nama_karyawan' => $penempatan->karyawan->nama_karyawan
                        ],
                        'divisi' => [
                            'kd_divisi' => $penempatan->Divisi->kd_divisi,
                            'nama_divisi' => $penempatan->Divisi->nama_divisi,
                        ],
                        'departement' => [
                            'kd_departement' => $penempatan->Departement->kd_departement,
                            'nama_departement' => $penempatan->Departement->nama_departement,
                        ],
                        'posisi' => [
                            'kd_position' => $penempatan->Posisi->kd_position ?? null,
                            'nama_position' => $penempatan->Posisi->nama_position ?? null,
                        ],
                    ];
                })
            ];
        }

        return $result;
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

    private function buatHistoryInputKaryawan($dataKaryawan)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('BUAT-HISTORY-INPUT-KARYAWAN');
        try {
            $log->info("<================= MULAI PROSES SIMPAN DATA KE DATABASE HISTORY INPUT KARYAWAN =================>");

            $kd_history_input_master_karyawan = $this->generateKdHistoryInputkaryawan();
            $log->info("<================= BEHASIL BUAT PK generateKdHistoryInputkaryawan  =================>");

            $historyInputMasterKaryawan = HistoryInputKaryawan::create([
                'kd_history_input_master_karyawan' => $kd_history_input_master_karyawan,
                'jenis_input' => "INPUT",
                'keterangan_input' => "INPUT KARYAWAN BARU",
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
                throw new \Exception("Data yang dikirim tidak lengkap untuk simpanJabatan.");
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
}
