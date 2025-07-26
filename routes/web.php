<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HrdController;

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

// AUTH
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'auth_login');
    Route::get('/register', 'register')->name('register');
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/check-session', 'checkSession')->name('check.session');
    Route::post('/register', 'auth_register');
    // Route::get('/edit-user/{kd_asli_user}', 'edit_user')->name('edit_user');
    Route::get('/edit-user/{encryptedId}', 'edit_user')->name('edit_user');
    Route::post('/valisdasi-ubah-user', 'valisdasi_ubah_user');
});

// HOME
Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->middleware('guest.redirect');
    Route::get('/welcome', 'index')->name('welcome')->middleware('guest.redirect');
});

// LEVEL USER DATA
Route::prefix('user')->controller(UserController::class)->group(function () {
    Route::get('/get-user', 'getDataUser');
    Route::get('/level', 'getDataLevelUser');
    Route::get('/detail/{encryptedId}', 'getUserByKode');

    Route::get('/user-register', 'user_register')->name('user_register');
});

// DATA KARYAWAN
Route::prefix('hrd')->controller(HrdController::class)->group(function () {
    Route::get('/karyawan', 'allDataKaryawan');
});