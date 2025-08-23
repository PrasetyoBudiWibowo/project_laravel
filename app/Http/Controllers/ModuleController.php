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
}
