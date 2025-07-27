<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Services\WilayahService;

use App\Helper\AppLogger;

class WilayahController extends Controller
{
    protected $wilyahService;

    public function __construct(WilayahService $wilyahService)
    {
        $this->wilyahService = $wilyahService;
    }

    public function provinsi()
    {
        return view('wilayah.provinsi');
    }

    public function getDataProvinsi()
    {
        $data = $this->wilyahService->allProvinsi();

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

    public function validasi_simpan_provinsi(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-SIMPAN DATA PROVINSI');
            $log->info("PROSES PENGECEKAN DATA");

            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di valisdasi_ubah_user"
                ]);
            }

            $validator = Validator::make($request->all(), [
                'nama_provinsi' => ['required', 'regex:/^[A-Z\s]+$/i'],
            ], [
                'nama_provinsi.required' => 'Nama provinsi tidak boleh kosong',
                'nama_provinsi.regex' => 'Nama provinsi hanya boleh mengandung huruf dan spasi',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ]);
            }

            $cekNamaProvinsi = $this->wilyahService->cekNamaProvinsi($request['nama_provinsi']);

            if ($cekNamaProvinsi) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Nama provinsi '{$request['nama_provinsi']}' sudah ada"
                ]);
            }

            $log->info("BERHSIL LEWAT PROSES CEK DATA");

            $provinsi = $this->wilyahService->simpanProvinsi($request->all());

            if (!$provinsi) {
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
