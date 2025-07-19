<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Services\AuthService;

use App\Helper\AppLogger;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function auth_login(Request $request)
    {
        try {
            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di auth_login"
                ]);
            }

            $validator = Validator::make($request->all(), [
                'nama_user' => 'required|alpha_num',
                'password' => 'required'
            ], [
                'nama_user.required' => 'User name tidak boleh kosong',
                'nama_user.alpha_num' => 'User name hanya boleh mengandung huruf dan angka',
                'password.required' => 'Password tidak boleh kosong'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ]);
            }

            $user = $this->authService->login($request->all());

            if ($user) {
                session([
                    'user' => [
                        'kd_asli_user' => $user['kd_asli_user'],
                        'nama_user' => $user['nama_user'],
                        'id_level_user' => $user['id_level_user'],
                        'level_user' => $user['level_user'][0]['level_user'] ?? 'Unknown',
                        'img_user' => $user['img_user'],
                        'format_img_user' => $user['format_img_user'],
                        'password_tampil' => $user['password_tampil'],
                        'status_user' => $user['status_user'],
                        'blokir' => $user['blokir'],
                    ],
                    'user_logged_in' => true
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Login berhasil',
                    'user' => $user,
                    'redirect' => route('welcome')
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Login gagal. Nama user atau password salah'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => 'success',
            'message' => 'Anda Akan Segera Logout',
            'redirect' => route('login')
        ]);
    }
}
