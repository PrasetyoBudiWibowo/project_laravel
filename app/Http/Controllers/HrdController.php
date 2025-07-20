<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\HrdService;

class HrdController extends Controller
{
    protected $hrdService;

    public function __construct(HrdService $hrdService)
    {
        $this->hrdService = $hrdService;
    }

    public function allDataKaryawan()
    {
        $karyawan = $this->hrdService->allKaryawan();

        if (empty($karyawan)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Tidak ada data.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $karyawan
        ]);
    }
}
