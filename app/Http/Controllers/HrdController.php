<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

use App\Services\HrdService;
use App\Services\UserService;

use App\Helper\AppLogger;

class HrdController extends Controller
{
    protected $hrdService;
    protected $userService;

    public function __construct(
        HrdService $hrdService,
        UserService $userService
    ) {
        $this->hrdService = $hrdService;
        $this->userService = $userService;
    }

    public function index()
    {
        return view('module.hrd.dasboard');
    }

    public function master_karyawan()
    {
        return view('module.hrd.masterData.master_karyawan');
    }

    public function master_divisi()
    {
        return view('module.hrd.masterData.master_divisi');
    }

    public function master_departement()
    {
        return view('module.hrd.masterData.master_departement');
    }

    public function allDataDivisi()
    {
        $divisi = $this->hrdService->allDivisi();

        if (empty($divisi)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $divisi
        ]);
    }

    public function allDataDepartement()
    {
        $departement = $this->hrdService->allDepartement();

        if (empty($departement)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $departement
        ]);
    }

    public function allDataKaryawan()
    {
        $karyawan = $this->hrdService->allKaryawan();

        if (empty($karyawan)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $karyawan
        ]);
    }

    public function validasi_simpan_divisi(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-SIMPAN DATA DIVISI');
            $log->info("PROSES PENGECEKAN DATA");

            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di validasi_simpan_kota_kabupten"
                ]);
            }

            $validator = Validator::make($request->all(), [
                'nama_divisi' => ['required', 'regex:/^[A-Z\s]+$/i'],
            ], [
                'nama_divisi.required' => 'Nama Divisi tidak boleh kosong',
                'nama_divisi.regex' => 'Nama Divisi hanya boleh mengandung huruf dan spasi',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ]);
            }

            $kdAsliUser = Crypt::decryptString($request->user_input);
            $user = $this->userService->getUserByKdAsli($kdAsliUser);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => "USER YANG SEDANG INPUT TIDAK DITEMUKAN"
                ]);
            }

            $log->info("BERHSIL LEWAT PROSES CEK DATA");

            $data = [
                'nama_divisi' => $request->nama_divisi,
                'user_input' => $kdAsliUser,
            ];

            $result = $this->hrdService->simpanDivisi($data);

            if (!$result) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal Simpan'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil Simpan Data',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function validasi_ubah_divisi(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-UBAH DATA DIVISI');
            $log->info("PROSES PENGECEKAN DATA");

            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di validasi_ubah_divisi"
                ]);
            }

            $validator = Validator::make($request->all(), [
                'nama_divisi' => ['required', 'regex:/^[A-Z\s]+$/i'],
            ], [
                'nama_divisi.required' => 'Nama Divisi tidak boleh kosong',
                'nama_divisi.regex' => 'Nama Divisi hanya boleh mengandung huruf dan spasi',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ]);
            }

            $kdAsliUser = Crypt::decryptString($request->user_input);
            $user = $this->userService->getUserByKdAsli($kdAsliUser);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => "USER YANG SEDANG INPUT TIDAK DITEMUKAN"
                ]);
            }

            $kdDivisi = Crypt::decryptString($request->kd_divisi);
            $divisi = $this->hrdService->cekDivisi($kdDivisi);

            if (!$divisi) {
                return response()->json([
                    'status' => 'error',
                    'message' => "DIVISI '{$request->nama_divisi}' TIDAK DITEMUKAN",
                ]);
            }

            $log->info("BERHSIL LEWAT PROSES CEK DATA");

            $data = [
                'kd_divisi' => $kdDivisi,
                'nama_divisi' => $request->nama_divisi,
            ];

            $result = $this->hrdService->ubahDivisi($data);

            if (!$result) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal Simpan'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil Simpan Data',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function validasi_simpan_departement(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-SIMPAN DATA DEPARTEMENT');
            $log->info("PROSES PENGECEKAN DATA");

            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di validasi_simpan_departement"
                ]);
            }

            $kdDivisi = Crypt::decryptString($request->kd_divisi);
            $divisi = $this->hrdService->cekDivisi($kdDivisi);

            if (!$divisi) {
                return response()->json([
                    'status' => 'error',
                    'message' => "DIVISI TIDAK DITEMUKAN"
                ]);
            }

            $validator = Validator::make($request->all(), [
                'nama_departement' => ['required', 'regex:/^[A-Z\s]+$/i'],
            ], [
                'nama_departement.required' => 'Nama Departement tidak boleh kosong',
                'nama_departement.regex' => 'Nama Departement hanya boleh mengandung huruf dan spasi',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ]);
            }

            $kdAsliUser = Crypt::decryptString($request->user_input);
            $user = $this->userService->getUserByKdAsli($kdAsliUser);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => "USER YANG SEDANG INPUT TIDAK DITEMUKAN"
                ]);
            }

            $log->info("BERHSIL LEWAT PROSES CEK DATA");

            $data = [
                'kd_divisi' => $kdDivisi,
                'nama_departement' => $request->nama_departement,
                'user_input' => $kdAsliUser,
            ];

            $result = $this->hrdService->simpanDepartement($data);

            if (!$result) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal Simpan'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil Simpan Data',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function valiadasi_ubah_departement(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-UBAH DATA DEPARTEMENT');
            $log->info("PROSES PENGECEKAN DATA");

            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di valiadasi_ubah_departement"
                ]);
            }

            $kdDepartement = Crypt::decryptString($request->kd_departement);
            $departement = $this->hrdService->cekDepartement($kdDepartement);


            if (!$departement) {
                return response()->json([
                    'status' => 'error',
                    'message' => "DEPARTEMENT TIDAK DITEMUKAN"
                ]);
            }

            $kdDivisi = Crypt::decryptString($request->kd_divisi);
            $divisi = $this->hrdService->cekDivisi($kdDivisi);

            if (!$divisi) {
                return response()->json([
                    'status' => 'error',
                    'message' => "DIVISI TIDAK DITEMUKAN"
                ]);
            }

            $validator = Validator::make($request->all(), [
                'nama_departement' => ['required', 'regex:/^[A-Z\s]+$/i'],
            ], [
                'nama_departement.required' => 'Nama Departement tidak boleh kosong',
                'nama_departement.regex' => 'Nama Departement hanya boleh mengandung huruf dan spasi',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ]);
            }

            $kdAsliUser = Crypt::decryptString($request->user_input);
            $user = $this->userService->getUserByKdAsli($kdAsliUser);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => "USER YANG SEDANG INPUT TIDAK DITEMUKAN"
                ]);
            }

            $log->info("BERHSIL LEWAT PROSES CEK DATA");

            $data = [
                'kd_departement' => $kdDepartement,
                'kd_divisi' => $kdDivisi,
                'nama_departement' => $request->nama_departement,
            ];

            $result = $this->hrdService->ubahDepartement($data);

            if (!$result) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal Simpan'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil Simpan Data',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
