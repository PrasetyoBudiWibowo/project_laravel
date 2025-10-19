<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

use App\Services\UserService;
use App\Services\MenuService;

use App\Helper\AppLogger;

class UserController extends Controller
{
    protected $userService;
    protected $menuService;

    public function __construct(
        UserService $userService,
        MenuService $menuService,
    ) {
        $this->userService = $userService;
        $this->menuService = $menuService;
    }

    public function user_register()
    {
        return view('Auth.user_register');
    }

    public function getDataLevelUser()
    {
        $data = $this->userService->getLevelUser();

        if ($data->isEmpty()) {
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

    public function getDataUser()
    {
        $data = $this->userService->getAlluser();

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

    public function getUserByKode($encryptedId)
    {
        try {
            $kdAsliUser = Crypt::decryptString($encryptedId);
            $user = $this->userService->getUserByKdAsli($kdAsliUser);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $user
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function validasi_user_visit_halaman(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-VALIDASI-USER-PERHALAMAN');
            $log->info("PROSES PENGECEKAN DATA");


            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di validasi_user_visit_halaman"
                ]);
            }

            if (!$request->user || !$request->url) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Parameter user atau url tidak ditemukan',
                ]);
            }

            $kdAsliUser = Crypt::decryptString($request->user);
            $user = $this->userService->getUserByKdAsli($kdAsliUser);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => "USER YANG SEDANG AKSES TIDAK DI DIKETAHUI"
                ]);
            }

            $urlMenu = $this->menuService->cekUrlMenu($request->url);

            if (!$urlMenu) {
                return response()->json([
                    'status' => 'error',
                    'message' => "HALAMAN TIDAK DITEMUKAN"
                ]);
            }

            $log->info("BERHSIL LEWAT PROSES CEK VALIDASI-USER-PERHALAMAN");

            $data = [
                'kd_user' => is_array($user) ? $user['kd_asli_user'] : $user->kd_asli_user,
                'kd_menu' => is_array($urlMenu) ? $urlMenu['kd_menu'] : $urlMenu->kd_menu,
            ];


            $result = $this->menuService->cekAksesMenuByUser($data);

            if (!$result || count($result) <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => "USER {$user['nama_user']}  TIDAK MEMILIKI AKSES KE HALAMAN INI"
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'ANDA DAPAT MENGAKSES HALAMAN INI',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
