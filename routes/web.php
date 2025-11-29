<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HrdController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\MenuController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'auth_login']);

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'auth_register']);
});

// Logout
Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])->name('logout');

// Edit user (gunakan encryptedId)
Route::middleware('auth')->group(function () {
    Route::get('/edit-user/{encryptedId}', [AuthController::class, 'edit_user'])->name('edit_user');
    Route::post('/valisdasi-ubah-user', [AuthController::class, 'valisdasi_ubah_user']);

    Route::get('/check-session', [AuthController::class, 'checkSession'])->name('checkSession');
});

// -----------------------
// HOME
// -----------------------
Route::middleware(['auth'])->controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home')->middleware('guest.redirect');
    Route::get('/welcome', 'index')->name('welcome')->middleware('guest.redirect');
});

// -----------------------
// MODULE & SETTING
// -----------------------
Route::middleware(['auth'])->controller(ModuleController::class)->group(function () {
    Route::get('/module', 'module')->name('module');;
    Route::post('/module', 'validasi_simpan_module');

    Route::get('/get-module', 'getModule');

    Route::get('/akses-module-user', 'akses_module_user')->name('akses_module_user');
    Route::post('/akses-module-user', 'validasi_hak_akses_module_user');

    Route::get('/module-by-user', 'getModuleByUser');
    Route::get('/module-with-menu', 'getModuleWithMenu');
});

// -----------------------
// MENU
// -----------------------
Route::middleware(['auth'])->controller(MenuController::class)->group(function () {
    Route::get('/daftar-menu', 'daftar_menu')->name('daftar_menu');
    Route::get('/get-menu', 'getMenu');

    Route::post('/simpan-menu', 'validasi_simpan_menu');

    Route::get('/hak-akses-menu', 'hak_akses_menu')->name('hak_akses_menu');
    Route::post('/hak-akses-menu', 'validasi_simpan_hak_akses_menu');

    Route::get('/menu-user', 'menuByUser');
});

// -----------------------
// LEVEL USER DATA
// -----------------------
Route::middleware(['auth'])->prefix('user')->controller(UserController::class)->group(function () {
    Route::get('/get-user', 'getDataUser');
    Route::get('/level', 'getDataLevelUser');
    Route::get('/detail/{encryptedId}', 'getUserByKode');
    Route::get('/user-register', 'user_register')->name('user_register');

    Route::post('/cek-halaman-by-user', 'validasi_user_visit_halaman');
});

// -----------------------
// DATA HRD
// -----------------------
Route::middleware(['auth'])->prefix('hrd')->controller(HrdController::class)->group(function () {
    Route::get('/', 'index');

    Route::get('/master-divisi', 'master_divisi')->name('master_divisi');
    Route::get('/divisi', 'allDataDivisi');

    Route::post('/simpan-divisi', 'validasi_simpan_divisi');
    Route::post('/ubah-divisi', 'validasi_ubah_divisi');

    Route::get('/master-departement', 'master_departement')->name('master_departement');
    Route::get('/departement', 'allDataDepartement');
    Route::post('/simpan-departement', 'validasi_simpan_departement');
    Route::post('/ubah-departement', 'valiadasi_ubah_departement');
    Route::get('/export-excel-departement', 'export_excel_departement');

    Route::get('/master-posisi', 'master_posisi')->name('master_posisi');
    Route::post('/simpan-posisi', 'validasi_simpan_posisi');
    Route::post('/ubah-posisi', 'validasi_ubah_posisi');
    Route::get('/posisi', 'allDataPosisition');
    Route::get('/export-excel-posisition', 'export_excel_posisition');

    Route::get('/master-jabatan', 'master_jabatan')->name('master_jabatan');
    Route::post('/simpan-jabatan', 'validasi_simpan_jabatan');

    Route::get('/karyawan', 'allDataKaryawan');
    Route::get('/master-karyawan', 'master_karyawan')->name('master_karyawan');
});

// -----------------------
// WILAYAH
// -----------------------
Route::middleware(['auth'])->prefix('wilayah')->controller(WilayahController::class)->group(function () {
    // Provinsi
    Route::get('/provinsi', 'provinsi')->name('provinsi');
    Route::get('/get-provinsi', 'getDataProvinsi');
    Route::post('/simpan-provinsi', 'validasi_simpan_provinsi');
    Route::post('/ubah-provinsi', 'validasi_ubah_provinsi');

    // Kota/Kabupaten
    Route::get('/kota-kabupaten', 'kota_kabupten')->name('kota_kabupaten');
    Route::get('/get-kota-kabupaten', 'getDataKotaKabupaten');
    Route::get('/print-kota-kabupaten', 'printKotaKabupaten');
    Route::post('/simpan-kabupaten-kota', 'validasi_simpan_kota_kabupten');
    Route::post('/ubah-kabupaten-kota', 'validasi_ubah_kota_kabupten');

    // Kecamatan
    Route::get('/kecamatan', 'kecamatan')->name('kecamatan');
    Route::get('/get-kecamatan', 'getDataKecamatan');
    Route::post('/simpan-kecamatan', 'validasi_simpan_kecamatan');
    Route::post('/ubah-kecamatan', 'validasi_ubah_kecamatan');
});
