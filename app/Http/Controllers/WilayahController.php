<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mpdf\Mpdf;

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

    public function kota_kabupten()
    {
        return view('wilayah.kota_kabupaten');
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

    public function getDataKotaKabupaten()
    {
        $data = $this->wilyahService->allKotaKabupaten();

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
                    'message' => "Metode request tidak valid di validasi_simpan_provinsi"
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

    public function validasi_ubah_provinsi(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-UBAH DATA PROVINSI');
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

            $cekProvinsi = $this->wilyahService->cekProvinsiByKode($request['kd_provinsi']);

            if (!$cekProvinsi) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Nama provinsi '{$request['nama_provinsi']}' tidak ditemukan"
                ]);
            }

            $log->info("BERHSIL LEWAT PROSES CEK DATA");

            $provinsi = $this->wilyahService->ubahProvinsi($request->all());

            if (!$provinsi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal Simpan'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil Ubah Data',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function validasi_simpan_kota_kabupten(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-SIMPAN DATA KOTA KABUPATEN');
            $log->info("PROSES PENGECEKAN DATA");

            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di validasi_simpan_kota_kabupten"
                ]);
            }

            $validator = Validator::make($request->all(), [
                'nama_kota_kabupaten' => ['required', 'regex:/^[A-Z\s]+$/i'],
            ], [
                'nama_kota_kabupaten.required' => 'Nama Kota/kabupaten tidak boleh kosong',
                'nama_kota_kabupaten.regex' => 'Nama Kota/kabupaten hanya boleh mengandung huruf dan spasi',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ]);
            }

            $cekProvinsi = $this->wilyahService->cekProvinsiByKode($request['kd_provinsi']);

            if (!$cekProvinsi) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Nama provinsi '{$request['nama_provinsi']}' tidak ditemukan"
                ]);
            }

            $log->info("BERHSIL LEWAT PROSES CEK DATA");


            $kotaKabupaten = $this->wilyahService->simpanKabupatenKota($request->all());

            if (!$kotaKabupaten) {
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

    public function printKotaKabupaten(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-PRINT DATA KOTA / KABUPATEN');

            if (!$request->isMethod('get')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di printKotaKabupaten"
                ]);
            }

            $kdProvinsi = $request->query('kd_provinsi');
            $namaKota = $request->query('nama_kota_kabupaten');

            $allKota = $this->wilyahService->allKotaKabupaten();
            $filteredKota = collect($allKota)->filter(function ($item) use ($kdProvinsi, $namaKota) {
                $matchProvinsi = !$kdProvinsi || $item['kd_provinsi'] === $kdProvinsi;
                $matchNama = !$namaKota || stripos($item['nama_kota_kabupaten'], $namaKota) !== false;
                return $matchProvinsi && $matchNama;
            })->values()->all();


            $html = view('wilayah.pdf.pdf_kota_kabupaten', [
                'data' => $filteredKota,
                'tanggal' => now()->format('d-m-Y H:i:s'),
            ])->render();

            $mpdf = new \Mpdf\Mpdf([
                'format' => 'A4',
                'orientation' => 'P',
                'margin_top' => 30,
                'margin_bottom' => 20,
                'margin_left' => 15,
                'margin_right' => 15,
            ]);


            $mpdf->SetHTMLHeader('<div style="text-align:center; font-size:16pt; font-weight:bold;">Data Kota / Kabupaten</div>');
            $mpdf->SetHTMLFooter('<div style="text-align:right; font-size:10px;">Halaman {PAGENO}</div>');

            $mpdf->WriteHTML($html);

            return response($mpdf->Output('', \Mpdf\Output\Destination::INLINE))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="data_kota_kabupaten.pdf"');
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function validasi_ubah_kota_kabupten(Request $request)
    {
        try {
            $log = AppLogger::getLogger('MULAI-PROSES-UBAH DATA KOTA KABUPATEN');
            $log->info("PROSES PENGECEKAN DATA");

            if (!$request->isMethod('post')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Metode request tidak valid di validasi_ubah_kota_kabupten"
                ]);
            }

            $validator = Validator::make($request->all(), [
                'nama_kota_kabupaten' => ['required', 'regex:/^[A-Z\s]+$/i'],
            ], [
                'nama_kota_kabupaten.required' => 'Nama Kota/kabupaten tidak boleh kosong',
                'nama_kota_kabupaten.regex' => 'Nama Kota/kabupaten hanya boleh mengandung huruf dan spasi',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ]);
            }

            $cekKotaKabupaten = $this->wilyahService->cekKotaKabupatenByKode($request['kd_kota_kabupaten']);

            if (!$cekKotaKabupaten) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Nama Kota Kabupaten '{$request['nama_kota_kabupaten']}' tidak ditemukan"
                ]);
            }

            $log->info("BERHSIL LEWAT PROSES CEK DATA");

            $KotaKabupaten = $this->wilyahService->ubahKotaKabupaten($request->all());

            if (!$KotaKabupaten) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal Simpan'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil Ubah Data',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
