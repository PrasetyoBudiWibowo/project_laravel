<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

use App\Services\ModuleService;
use App\Services\UserService;

use App\Helper\AppLogger;

class ModuleController extends Controller
{
    protected $moduleService;
    protected $userService;


    public function __construct(
        ModuleService $moduleService,
        UserService $userService
    ) {
        $this->moduleService = $moduleService;
        $this->userService = $userService;
    }

    public function module()
    {
        return view('module.index');
    }

    public function akses_module_user()
    {
        return view('module.hak_akses_user');
    }

    public function getModule()
    {
        $data = $this->moduleService->allModule();

        if (empty($data)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Tidak ada data.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function getModuleWithMenu()
    {
        $data = $this->moduleService->moduleWithMenu();

        if (empty($data)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Tidak ada data.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function getModuleByUser(Request $request)
    {
        $sessionUser = session('user');

        $log = AppLogger::getLogger('CEK-USER-SESSION');
        $log->info("PROSES PENGECEKAN DATA");

        $user = $this->userService->getUserByKdAsli($sessionUser['kd_asli_user']);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => "User TIDAK DITEMUKAN"
            ]);
        }

        $log->info("LEWAT CEK USER");

        $aksesModule = $this->moduleService->cekAksesModuleByUser($user);

        if (!$aksesModule) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal'
            ]);
        }

        $log->info("LEWAT CEK MODULE");

        return response()->json([
            'status' => 'success',
            'data' => $aksesModule
        ]);
    }

    public function validasi_simpan_module(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-SIMPAN DATA MODULE');
            $log->info("PROSES PENGECEKAN DATA");

            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di validasi_simpan_kota_kabupten"
                ]);
            }

            $validator = Validator::make($request->all(), [
                'nama_module' => ['required', 'regex:/^[A-Z\s]+$/i'],
                'tampil_module' => ['required', 'regex:/^[A-Z\s]+$/i'],
            ], [
                'nama_module.required' => 'Nama Module tidak boleh kosong',
                'nama_module.regex' => 'Nama Module hanya boleh mengandung huruf dan spasi',
                'tampil_module.required' => 'Tampil Module tidak boleh kosong',
                'tampil_module.regex' => 'Nama Tampil Module hanya boleh mengandung huruf dan spasi',
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
                    'message' => "User TIDAK DITEMUKAN"
                ]);
            }

            $log->info("BERHSIL LEWAT PROSES CEK DATA");

            $data = [
                'nama_module' => $request->nama_module,
                'tampil_module' => $request->tampil_module,
                'url_module'    => '/' . ltrim($request->url_module, '/'),
                'status_module' => "ACTIVE",
                'user_input' => $kdAsliUser,
            ];

            $module = $this->moduleService->simpanModule($data);

            if (!$module) {
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

    public function validasi_hak_akses_module_user(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-VALIDASI-HAK-AKSES-MODULE-USER');
            $log->info("PROSES PENGECEKAN DATA");

            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di validasi_hak_akses_module_user"
                ]);
            }

            if (empty($request->akses)) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Module Harus ada yang di pilih"
                ]);
            }

            $kdAsliUser = Crypt::decryptString($request->user_input);
            $userInput = $this->userService->getUserByKdAsli($kdAsliUser);

            if (!$userInput) {
                return response()->json([
                    'status' => 'error',
                    'message' => "User TIDAK DITEMUKAN"
                ]);
            }

            $data = [];

            foreach ($request->akses as $akses) {

                $cekModule = $this->moduleService->cekModule($akses['kd_module']);

                if (!$cekModule) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Module '{$cekModule->nama_module}' sudah tidak aktif atau tidak ada",
                    ]);
                }

                $cekUser = Crypt::decryptString($request->kd_user);

                $user = $this->userService->getUserByKdAsli($cekUser);

                if (!$user) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Module '{$user->nama_user}' yang ingin di beri hak akses tidak ditemukan",
                    ]);
                }

                $data[] = [
                    'kd_user' => $cekUser,
                    'kd_module' => $akses['kd_module'],
                    'status_akses' => "YA",
                    'user_input' => $kdAsliUser,
                ];
            }

            $userDelete = Crypt::decryptString($request->kd_user);

            $log->info("BERHSIL LEWAT PROSES CEK DATA");

            $result = $this->moduleService->simpanHakAksesUser($userDelete, $data);

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
