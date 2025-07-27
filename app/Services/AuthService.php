<?php

namespace App\Services;

use App\Models\TblUser;
use App\Models\HistoryLoginUser;
use App\Models\LevelUser;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Helper\DeviceHelper;
use App\Helper\GeoDetector;
use App\Helper\AppLogger;
use GuzzleHttp\Promise\Create;

class AuthService
{
    private function generateUserKd()
    {
        $currentMonth = Carbon::now()->format('Ym');
        $prefix = 'USR-' . $currentMonth . '-';

        $lastUser = TblUser::where('kd_asli_user', 'LIKE', $prefix . '%')
            ->orderBy('kd_asli_user', 'DESC')
            ->first();

        if (!$lastUser) {
            return $prefix . '000';
        }

        $lastId = $lastUser->kd_asli_user;
        $lastNumber = substr($lastId, -3);

        $newNumber = str_pad(intval($lastNumber) + 1, 3, '0', STR_PAD_LEFT);
        return $prefix . $newNumber;
    }

    private function buatKodeHistoryLogin()
    {
        $currentMonth = Carbon::now()->format('Ym');
        $prefix = 'HSL-' . $currentMonth . '-';

        $lastUser = HistoryLoginUser::where('kd_history_login', 'LIKE', $prefix . '%')
            ->orderBy('kd_history_login', 'DESC')
            ->first();

        if (!$lastUser) {
            return $prefix . '0000';
        }

        $lastId = $lastUser->kd_history_login;
        $lastNumber = substr($lastId, -4);

        $newNumber = str_pad(intval($lastNumber) + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $newNumber;
    }

    private function buatHistoryLoginUser($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('BUAT-HISTORY-LOGIN');
        try {
            $log->info("<================= MULAI PROSES SIMPAN DATA KE DATABAS HistoryLoginUser =================>");
            $log->info("Data dari controller: " . json_encode($data));

            $kd_history_login = $this->buatKodeHistoryLogin();
            $log->info("berhasil buat code PK HistoryLoginUser");

            $tgl_login = Carbon::now()->toDateString();
            $waktu_login = Carbon::now()->setTimezone('Asia/Jakarta')->format('H:i');
            $bln_login = Carbon::now()->format('m');
            $thn_login = Carbon::now()->year;

            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $deviceInfo = DeviceHelper::detectDevice($userAgent);
            $deviceType = $deviceInfo['deviceType'];
            $device = $deviceInfo['browser'];

            $ipDetector = GeoDetector::getDeviceLocation();
            $ipDevice = isset($ipDetector['ip']) ? $ipDetector['ip'] : 'Unknown IP';

            $historyLogin = new HistoryLoginUser();
            $historyLogin->kd_history_login = $kd_history_login;
            $historyLogin->kd_user = $data['kd_user'];
            $historyLogin->tgl_login = $tgl_login;
            $historyLogin->waktu_login = $waktu_login;
            $historyLogin->bln_login = $bln_login;
            // $historyLogin->thn_login = $thn_login;
            $historyLogin->alamat_device = $ipDevice;
            $historyLogin->type_device = $deviceType;
            $historyLogin->device = $device;

            $historyLogin->save();

            DB::commit();

            return $historyLogin;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }

    public function login($data)
    {
        $log = AppLogger::getLogger('LOGIN');

        $user = TblUser::whereRaw('BINARY nama_user = ?', [$data['nama_user']])
            ->where('blokir', 'TIDAK')
            ->where('status_user', 'ACTIVE')
            ->with('level')
            ->first();

        if (!$user) {
            $log->error("Validasi gagal untuk user", [
                'invalid_input' => 'USER',
                'expected_format' => 'USER TIDAK DITEMUKAN'
            ]);

            throw new \Exception("USER TIDAK DITEMUKAN");
        }

        if ($user && Hash::check($data['password'], $user->password)) {
            $historyLogin = $this->buatHistoryLoginUser(['kd_user' => $user->kd_asli_user]);

            if (!$historyLogin) {
                $log->error("Validasi gagal untuk HISTORY LOGIN", [
                    'invalid_input' => 'HISTORY LOGIN',
                    'expected_format' => 'HISTORY LOGI GAGAL DI BUAT'
                ]);

                throw new \Exception("HISTORY LOGI GAGAL DI BUAT");
            }

            return [
                'kd_asli_user' => $user->kd_asli_user,
                'nama_user' => $user->nama_user,
                'id_level_user' => $user->id_usr_level,
                'password_tampil' => $user->password_tampil,
                'status_user' => $user->status_user,
                'blokir' => $user->blokir,
                'img_user' => $user->img_user,
                'format_img_user' => $user->format_img_user,
                'tgl_input' => $user->tgl_input,
                'level_user' => [
                    [
                        'id' => $user->level->id,
                        'level_user' => $user->level->level_user,
                    ]
                ]
            ];
        }

        throw new \Exception("Password salah");
    }

    public function registrasi($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('BUAT-REGISTRASI');
        try {
            $log->info("<================= MULAI PROSES SIMPAN DATA KE DATABASE TblUser =================>");
            $log->info("Data dari controller: " . json_encode($data));

            $kd_asli_user = $this->generateUserKd();
            $now = Carbon::now('Asia/Jakarta');
            $tgl_input = $now->toDateString();
            $waktu_input = $now->format('H:i');
            $bln_input = $now->format('m');
            $thn_input = $now->year;

            $password = Hash::make($data['password']);

            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $deviceInfo = DeviceHelper::detectDevice($userAgent);
            $deviceType = $deviceInfo['deviceType'];
            $device = $deviceInfo['browser'];

            $ipDetector = GeoDetector::getDeviceLocation();
            $ipDevice = isset($ipDetector['ip']) ? $ipDetector['ip'] : 'Unknown IP';

            $user = new TblUser();
            $user->kd_asli_user = $kd_asli_user;
            $user->nama_user = $data['nama_user'];
            $user->id_usr_level = $data['id_usr_level'];
            $user->password = $password;
            $user->password_tampil = $data['password'];
            $user->status_user = "ACTIVE";
            $user->blokir = "TIDAK";
            $user->tgl_input = $tgl_input;
            $user->waktu_input = $waktu_input;
            $user->bln_input = $bln_input;
            $user->thn_input = $thn_input;
            $user->device = $device;
            $user->type_device = $deviceType;
            $user->nama_device = $ipDevice;
            $user->user_input = $data['user_input'] ?? null;

            $user->save();
            $log->info("BERHASIL SIMPAN DATA");

            DB::commit();

            $log->info("PROSES REGISTRASI SELESAI");
            return $user;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }

    public function edit($data)
    {
        DB::beginTransaction();
        $log = AppLogger::getLogger('EDIT-USER');
        try {
            $log->info("<================= MULAI PROSES UBAH DATA DI DATABASE TblUser =================>");
            $log->info("Data dari controller: " . json_encode($data));

            $user = TblUser::find($data['kd_asli_user']);
            $password = $data['password'];
            $hashedPassword = Hash::make($password);

            if ($user) {
                $updateData = [
                    'nama_user' => $data['nama_user'],
                    'password' => $hashedPassword,
                    'password_tampil' => $password,
                    'id_usr_level' => $data['id_usr_level'] ?? $user['id_usr_level'],
                    'status_user' => $data['status_user'] ?? "ACTIVE",
                    'blokir' => $data['blokir'] ?? "TIDAK",
                ];

                if (isset($data['img_user'])) {
                    $updateData['img_user'] = $data['img_user'];
                }

                if (isset($data['format_img_user'])) {
                    $updateData['format_img_user'] = $data['format_img_user'];
                }

                $user->update($updateData);
            }


            DB::commit();

            $log->info("PROSES REGISTRASI SELESAI");
            return $user;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            throw $th;
        }
    }
}