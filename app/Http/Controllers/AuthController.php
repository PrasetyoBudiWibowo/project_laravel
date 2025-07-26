<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

use App\Services\AuthService;
use App\Services\HrdService;
use App\Services\UserService;

use App\Helper\AppLogger;

class AuthController extends Controller
{
    protected $authService;
    protected $hrdService;
    protected $userService;

    public function __construct(
        AuthService $authService,
        HrdService $hrdService,
        UserService $userService
    ) {
        $this->authService = $authService;
        $this->hrdService = $hrdService;
        $this->userService = $userService;
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

    public function checkSession(Request $request)
    {
        if (session('user_logged_in')) {
            return response()->json([
                'status' => 'success',
                'user' => session('user')
            ]);
        }

        return response()->json([
            'status' => 'unauthenticated'
        ], 401);
    }

    public function register()
    {
        return view('auth.register');
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

    public function auth_register(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-REGISTRASI');

            $log->info("PROSES PENGECEKAN DATA REGISTRASI");

            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di auth_login"
                ]);
            }

            $log->info('Data Request:', $request->all());

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

            if ($request['is_karyawan'] === true) {
                $cekKaryawan = $this->hrdService->cekKaryawanByPk($request['kd_karyawan']);

                if (!$cekKaryawan) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "karyawan Tidak di temukan"
                    ]);
                }
            }

            $log->info("BERHSIL LEWAT PROSES CEK DATA REGISTRASI");

            $register = $this->authService->registrasi($request->all());

            if (!$register) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal Registrasi'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil Registrasi',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function edit_user($encryptedId)
    {
        try {
            $kdAsliUser = Crypt::decryptString($encryptedId);

            $user = $this->userService->getUserByKdAsli($kdAsliUser);

            if (!$user) {
                abort(404, 'User not found');
            }

            return view('auth.edit_user', compact('encryptedId'));
        } catch (\Throwable $th) {
            abort(403, 'Invalid or expired link.');
        }
    }

    public function valisdasi_ubah_user(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-UBAH DATA USER');
            $log->info("PROSES PENGECEKAN DATA UBAH DATA USER");

            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di valisdasi_ubah_user"
                ]);
            }

            $fileTmpPath = $_FILES['foto']['tmp_name'] ?? null;
            $fileName = basename($_FILES['foto']['name'] ?? '');
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $fileSize = $_FILES['foto']['size'] ?? 0;

            $maxFileSize = 50 * 1024 * 1024;
            $allowedExtensions = ['jpg', 'jpeg', 'png'];

            if ($fileTmpPath) {
                if (!in_array($fileExtension, $allowedExtensions)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Format tidak valid. Hanya diperbolehkan jpg, jpeg, dan png.'
                    ]);
                }

                if ($fileSize > $maxFileSize) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Ukuran gambar melebihi batas maksimal 50MB."
                    ]);
                }
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

            $newFileName = null;

            if ($fileTmpPath) {
                $newCode = $this->userService->generateImage($request->all());
                $newFileName = $newCode . '.' . $fileExtension;
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/img/user/';
                $filePath = $uploadDir . $newFileName;

                if (!move_uploaded_file($fileTmpPath, $filePath)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal menyimpan gambar ke server.'
                    ]);
                }
            }

            $data = [
                'nama_user' => $request['nama_user'],
                'password' => $request['password'],
                'img_user' => $newFileName ? pathinfo($newFileName, PATHINFO_FILENAME) : null,
                'user_input' => $request['user_input'],
                'format_img_user' => $newFileName ? $fileExtension : null,
                'kd_asli_user' => $request['kd_asli_user'],
            ];

            $editUser = $this->authService->edit($data);

            if (!$editUser) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal Edit data'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil Edit data',
                'redirect' => route('welcome')
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
}