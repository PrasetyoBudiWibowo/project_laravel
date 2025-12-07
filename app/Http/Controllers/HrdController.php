<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


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

    // VIEW
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

    public function master_posisi()
    {
        return view('module.hrd.masterData.master_posisi');
    }

    public function master_jabatan()
    {
        return view('module.hrd.masterData.master_jabatan');
    }

    // GET DATA
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

    public function allDataPosisition()
    {
        $posisi = $this->hrdService->allPosisition();

        if (empty($posisi)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $posisi
        ]);
    }

    public function allDataJabatan()
    {
        $jabatan = $this->hrdService->allJabatan();

        if (empty($jabatan)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $jabatan
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

    public function edit_karyawan($encrypted)
    {
        try {
            $kd_karyawan = Crypt::decryptString($encrypted);

            $karyawan = $this->hrdService->cekKaryawanByPk($kd_karyawan);

            if (!$karyawan) {
                abort(404, 'not found');
            }

            return view('module.hrd.masterData.edit_master_karyawan', [
                'encrypted' => $encrypted,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'ID Karyawan tidak valid.');
        }
    }

    public function detail_karyawan($encrypted)
    {
        try {
            $kd_karyawan = Crypt::decryptString($encrypted);

            $karyawan = $this->hrdService->cekKaryawanByKd($kd_karyawan);

            if (!$karyawan) {
                return response()->json(['message' => 'karyawan not found'], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $karyawan
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }


    // VALIDASI SIMPAN DAN UBAH DATA
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

    public function validasi_simpan_posisi(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-SIMPAN DATA POSISI KARYAWAN');
            $log->info("PROSES PENGECEKAN DATA");

            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di validasi_simpan_posisi"
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

            $kdDepartement = Crypt::decryptString($request->kd_departement);
            $departement = $this->hrdService->cekDepartement($kdDepartement);

            if (!$departement) {
                return response()->json([
                    'status' => 'error',
                    'message' => "DEPARTEMENT TIDAK DITEMUKAN"
                ]);
            }

            $validator = Validator::make($request->all(), [
                'nama_position' => ['required', 'regex:/^[A-Z\s&]+$/i'],
            ], [
                'nama_position.required' => 'Nama Posisi tidak boleh kosong',
                'nama_position.regex' => 'Nama Posisi hanya boleh mengandung huruf dan spasi',
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
                'kd_departement' => $kdDepartement,
                'nama_position' => $request->nama_position,
                'user_input' => $kdAsliUser,
            ];

            $result = $this->hrdService->simpanPosisi($data);

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

    public function validasi_ubah_posisi(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-UBAH DATA POSISI KARYAWAN');
            $log->info("PROSES PENGECEKAN DATA");

            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di validasi_ubah_posisi"
                ]);
            }

            $kdPosisi = Crypt::decryptString($request->kd_position);
            $posisi = $this->hrdService->cekPosisi($kdPosisi);

            if (!$posisi) {
                return response()->json([
                    'status' => 'error',
                    'message' => "POSISI KARYAWAN TIDAK DITEMUKAN"
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

            $kdDepartement = Crypt::decryptString($request->kd_departement);
            $departement = $this->hrdService->cekDepartement($kdDepartement);

            if (!$departement) {
                return response()->json([
                    'status' => 'error',
                    'message' => "DEPARTEMENT TIDAK DITEMUKAN"
                ]);
            }

            $validator = Validator::make($request->all(), [
                'nama_position' => ['required', 'regex:/^[A-Z\s&]+$/i'],
            ], [
                'nama_position.required' => 'Nama Posisi tidak boleh kosong',
                'nama_position.regex' => 'Nama Posisi hanya boleh mengandung huruf dan spasi',
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
                'kd_position' => $kdPosisi,
                'kd_divisi' => $kdDivisi,
                'kd_departement' => $kdDepartement,
                'nama_position' => $request->nama_position,
                'user_input' => $kdAsliUser,
            ];

            $result = $this->hrdService->ubahPosisi($data);

            if (!$result->kd_position) {
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

    public function validasi_simpan_jabatan(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-SIMPAN DATA POSISI KARYAWAN');
            $log->info("PROSES PENGECEKAN DATA");

            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di validasi_simpan_jabatan"
                ]);
            }

            $validator = Validator::make($request->all(), [
                'nama_jabatan' => ['required', 'regex:/^[A-Z\s]+$/i'],
            ], [
                'nama_jabatan.required' => 'Nama Jabatan tidak boleh kosong',
                'nama_jabatan.regex' => 'Nama Jabatan hanya boleh mengandung huruf dan spasi',
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
                'nama_jabatan' => $request->nama_jabatan,
                'user_input' => $kdAsliUser,
            ];

            $result = $this->hrdService->simpanJabatan($data);

            if (!$result->kd_jabatan) {
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

    public function validasi_simpan_karyawan(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-SIMPAN DATA KARYAWAN');
            $log->info("PROSES PENGECEKAN DATA");

            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di validasi_simpan_karyawan"
                ]);
            }

            $kdPosisi = Crypt::decryptString($request->kd_position);
            $posisi = $this->hrdService->cekPosisi($kdPosisi);

            if (!$posisi) {
                return response()->json([
                    'status' => 'error',
                    'message' => "POSISI KARYAWAN TIDAK DITEMUKAN"
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

            $kdDepartement = Crypt::decryptString($request->kd_departement);
            $departement = $this->hrdService->cekDepartement($kdDepartement);

            if (!$departement) {
                return response()->json([
                    'status' => 'error',
                    'message' => "DEPARTEMENT TIDAK DITEMUKAN"
                ]);
            }

            if (!empty($request->kd_jabatan)) {
                $kdJabatan = Crypt::decryptString($request->kd_jabatan);
                $jabatan = $this->hrdService->cekJabatan($kdJabatan);

                if (!$jabatan) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "JABATAN TIDAK DITEMUKAN"
                    ]);
                }
            }

            $validator = Validator::make($request->all(), [
                'nama_karyawan' => ['required', 'regex:/^[A-Z\s]+$/i'],
            ], [
                'nama_karyawan.required' => 'Nama Posisi tidak boleh kosong',
                'nama_karyawan.regex' => 'Nama Posisi hanya boleh mengandung huruf dan spasi',
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
                'nama_karyawan' => $request->nama_karyawan,
                'nama_panggilan_karyawan' => $request->nama_panggilan_karyawan,
                'kd_divisi' => $kdDivisi,
                'kd_departement' => $kdDepartement,
                'kd_position' => $kdPosisi,
                'kd_jabatan' => $request->kd_jabatan ? $kdJabatan : null,
                'user_input' => $kdAsliUser,
            ];

            $result = $this->hrdService->simpanKaryawan($data);

            // $log->info("Data BALIK: " . $result);

            if (!$result->kd_karyawan) {
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
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }

    // EXPORT KE EXCEL
    public function export_excel_departement(Request $request)
    {
        try {
            $divisi = $request->query('divisi');
            if ($divisi) {
                $divisi = urldecode($divisi);
            }

            $data = $this->hrdService->allDepartement();

            if (!empty($divisi)) {
                $data = $data->filter(function ($item) use ($divisi) {
                    return isset($item['divisi']['nama_divisi']) &&
                        $item['divisi']['nama_divisi'] === $divisi;
                })->values()->toArray();
            } else {
                $data = $data->toArray();
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Divisi');
            $sheet->setCellValue('C1', 'Department');

            $headerStyle = [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9E1F2']],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

            // Isi data
            $row = 2;
            $no = 1;

            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $item['divisi']['nama_divisi']);
                $sheet->setCellValue('C' . $row, $item['nama_departement']);
                $row++;
            }

            // Styling data
            if ($row > 2) {
                $sheet->getStyle('A2:C' . ($row - 1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
            }

            foreach (range('A', 'C') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = "departement.xlsx";

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ], 500);
        }
    }

    public function export_excel_posisition(Request $request)
    {
        try {
            $divisi = $request->query('divisi');
            $departement = $request->query('departement');
            if ($divisi) {
                $divisi = urldecode($divisi);
            }

            if ($departement) {
                $departement = urldecode($departement);
            }

            $data = $this->hrdService->allPosisition();

            if (!empty($divisi) && empty($departement)) {
                $data = $data->filter(function ($item) use ($divisi) {
                    return $item['departement']['divisi']['nama_divisi'] === $divisi;
                })->values()->toArray();
            } else if (!empty($divisi) && !empty($departement)) {
                $data = $data->filter(function ($item) use ($divisi, $departement) {
                    return $item['departement']['divisi']['nama_divisi'] === $divisi &&
                        $item['departement']['nama_departement'] === $departement;
                })->values()->toArray();
            } else {
                $data = $data->toArray();
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            //  JUDUL DI ATAS TABEL
            $title = "MASTER POSISI";
            $sheet->setCellValue('A1', $title);

            // Merge A1 sampai D1
            $sheet->mergeCells('A1:D1');

            // Style judul
            $sheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 14,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ]);

            //  HEADER TABEL (mulai baris 3)
            $sheet->setCellValue('A3', 'No');
            $sheet->setCellValue('B3', 'Divisi');
            $sheet->setCellValue('C3', 'Department');
            $sheet->setCellValue('D3', 'Posisi');

            $headerStyle = [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9E1F2']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];

            $sheet->getStyle('A3:D3')->applyFromArray($headerStyle);

            //  ISI DATA (mulai baris 4)
            $row = 4;
            $no = 1;

            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $item['departement']['divisi']['nama_divisi']);
                $sheet->setCellValue('C' . $row, $item['departement']['nama_departement']);
                $sheet->setCellValue('D' . $row, $item['nama_position']);
                $row++;
            }

            // Border untuk data
            if ($row > 4) {
                $sheet->getStyle("A4:D" . ($row - 1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
            }

            // Auto width
            foreach (range('A', 'D') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = "Master Posisi.xlsx";

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ], 500);
        }
    }
}
