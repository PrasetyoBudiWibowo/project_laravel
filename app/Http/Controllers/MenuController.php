<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

use App\Services\MenuService;
use App\Services\UserService;
use App\Services\ModuleService;

use App\Helper\AppLogger;

class MenuController extends Controller
{
    protected $menuService;
    protected $userService;
    protected $moduleService;

    public function __construct(
        MenuService $menuService,
        UserService $userService,
        ModuleService $moduleService
    ) {
        $this->menuService = $menuService;
        $this->userService = $userService;
        $this->moduleService = $moduleService;
    }

    public function daftar_menu()
    {
        return view('setting.daftar_menu');
    }

    public function hak_akses_menu()
    {
        return view('setting.hak_akses_menu');
    }

    public function getMenu()
    {
        $data = $this->menuService->getAllMenu();

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

    public function menuByUser()
    {
        $sessionUser = session('user');

        // $log = AppLogger::getLogger('CEK-USER-SESSION-menuByUser');
        // $log->info("PROSES PENGECEKAN DATA");

        $user = $this->userService->getUserByKdAsli($sessionUser['kd_asli_user']);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => "User TIDAK DITEMUKAN"
            ]);
        }

        // $log->info("LEWAT CEK USER");
    }

    public function validasi_simpan_menu(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-SIMPAN MENU MODULE');
            $log->info("PROSES PENGECEKAN DATA");

            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di validasi_simpan_menu"
                ]);
            }

            $validator = Validator::make($request->all(), [
                'nama_menu' => ['required', 'regex:/^[A-Z\s]+$/i'],
                'url_menu'  => ['required', 'regex:/^[a-z\-]+$/'],
                'icon_menu'  => ['nullable', 'regex:/^[a-z\s\-]+$/'],
            ], [
                'nama_menu.required' => 'Nama Module tidak boleh kosong',
                'nama_menu.regex' => 'Nama Module hanya boleh mengandung huruf dan spasi',
                'url_menu.required' => 'URL Menu tidak boleh kosong',
                'url_menu.regex'     => 'URL Menu hanya boleh mengandung huruf dan "-" tanpa spasi dan angka',
                'icon_menu.regex'     => 'URL Menu hanya boleh mengandung huruf, "-" dan spasi dan angka',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ]);
            }

            $perentMenu = "";
            if (!empty($request->parent_menu) && $request->parent_menu !== "default") {
                $cekParentMenu = Crypt::decryptString($request->parent_menu);
                $getParentMenu = $this->menuService->getMenuByKode($cekParentMenu);

                if (!$getParentMenu) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Parent menu TIDAK DITEMUKAN"
                    ]);
                }

                $perentMenu = $getParentMenu->kd_menu;
            }

            $cekMenuSidebar = $this->menuService->getMenuByNameAndParent($perentMenu, $request->nama_menu);

            if ($cekMenuSidebar) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Menu '{$request->nama_menu}' sudah ada"
                ]);
            }

            $moduleUrl = "";
            if ($request->kd_module !== "") {
                $module = $this->moduleService->cekModuleByKd($request->kd_module);

                if (!$module) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Module TIDAK DITEMUKAN"
                    ]);
                }

                $moduleUrl = strtolower($module->url_module);
            }

            $kdAsliUser = Crypt::decryptString($request->user_input);
            $user = $this->userService->getUserByKdAsli($kdAsliUser);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => "User input TIDAK DITEMUKAN"
                ]);
            }

            $log->info("BERHSIL LEWAT PROSES CEK DATA");

            $data = [
                'kd_module' => $request->kd_module,
                'nama_menu' => $request->nama_menu,
                'parent_menu' => !empty($request->parent_menu) &&  $request->parent_menu !== "default" ? $perentMenu : null,
                'url_menu' => !empty($request->parent_menu) && $request->tipe_menu === "child" ? $moduleUrl . '/' . ltrim($request->url_menu, '/') : null,
                'icon_menu' => $request->icon_menu !== null ? $request->icon_menu : "fa-regular fa-circle",
                'user_input' => $kdAsliUser,
                'tipe_menu' => $request->tipe_menu,
            ];

            $menuSideBar = $this->menuService->simpanMenu($data);

            if (!$menuSideBar) {
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

    public function validasi_simpan_hak_akses_menu(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-SIMPAN HAK AKSES MENU');
            $log->info("PROSES PENGECEKAN DATA");

            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Metode request tidak valid (harus POST).'
                ], 405);
            }

            if (empty($request->user_input)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User input tidak ditemukan.'
                ], 400);
            }

            try {
                $kdUserInput = Crypt::decryptString($request->user_input);
                $userInput = $this->userService->getUserByKdAsli($kdUserInput);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payload user input tidak valid.'
                ], 400);
            }

            if (!$userInput) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User yang menginput tidak ditemukan.'
                ], 404);
            }

            if (empty($request->kd_user)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User yang dipilih tidak ditemukan.'
                ], 400);
            }

            try {
                $kdSelectedUser = Crypt::decryptString($request->kd_user);
                $selectedUser = $this->userService->getUserByKdAsli($kdSelectedUser);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payload user yang dipilih tidak valid.'
                ], 400);
            }

            if (!$selectedUser) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User yang dipilih tidak ditemukan di database.'
                ], 404);
            }

            if (empty($request->menus) || !is_array($request->menus)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Belum ada menu yang dipilih.'
                ], 400);
            }

            $data = [];
            foreach ($request->menus as $menu) {

                if (empty($menu['kd_menu'])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Salah satu data menu tidak memiliki kode.'
                    ], 400);
                }

                try {
                    $cekMenu = $this->menuService->getMenuByKode($menu['kd_menu']);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Kode menu tidak valid.'
                    ], 400);
                }

                if (!$cekMenu) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Menu dengan kode '{$menu['kd_menu']}' tidak ditemukan."
                    ], 404);
                }

                $data[] = [
                    'kd_user' => $selectedUser['kd_asli_user'],
                    'kd_menu' => $cekMenu['kd_menu'],
                    'status_akses' => !empty($menu['status_akses']) && $menu['status_akses'] === true ? "YA" : "TIDAK",
                    'bisa_insert' => !empty($menu['can_insert']) && $menu['can_insert'] === true ? "YA" : "TIDAK",
                    'bisa_edit' => !empty($menu['can_edit']) && $menu['can_edit'] === true ? "YA" : "TIDAK",
                    'bisa_export' => !empty($menu['can_export']) && $menu['can_export'] === true ? "YA" : "TIDAK",
                    'user_input' => $userInput['kd_asli_user'],
                ];
            }

            $log->info("BERHASIL LEWAT PROSES CEK DATA");

            $result = $this->menuService->simpanHakAksesMenu($selectedUser, $data);

            if (!$result || count($result) <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan data hak akses.'
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil menyimpan data hak akses menu.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }
}