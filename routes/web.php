<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HrdController;
use App\Http\Controllers\WilayahController;

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

// Logout harus tetap auth
Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])->name('logout');

// Edit user (gunakan encryptedId)
Route::middleware('auth')->group(function () {
    Route::get('/edit-user/{encryptedId}', [AuthController::class, 'edit_user'])->name('edit_user');
    Route::post('/valisdasi-ubah-user', [AuthController::class, 'valisdasi_ubah_user']);
});

// -----------------------
// HOME
// -----------------------
Route::middleware(['auth'])->controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home')->middleware('guest.redirect'); // jika ingin redirect guest
    Route::get('/welcome', 'index')->name('welcome')->middleware('guest.redirect');
});

// -----------------------
// LEVEL USER DATA
// -----------------------
Route::middleware(['auth'])->prefix('user')->controller(UserController::class)->group(function () {
    Route::get('/get-user', 'getDataUser');
    Route::get('/level', 'getDataLevelUser');
    Route::get('/detail/{encryptedId}', 'getUserByKode');
    Route::get('/user-register', 'user_register')->name('user_register');
});

// -----------------------
// DATA KARYAWAN
// -----------------------
Route::middleware(['auth'])->prefix('hrd')->controller(HrdController::class)->group(function () {
    Route::get('/karyawan', 'allDataKaryawan');
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
});
