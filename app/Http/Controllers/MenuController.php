<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

use App\Services\MenuService;

use App\Helper\AppLogger;

class MenuController extends Controller
{
    protected $menuService;

    public function __construct(
        MenuService $menuService
    ) {
        $this->menuService = $menuService;
    }

    public function daftar_menu()
    {
        return view('setting.daftar_menu');
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
}
