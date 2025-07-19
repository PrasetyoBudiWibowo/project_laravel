<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;

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

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'auth_login']);

Route::get('/', [HomeController::class, 'index'])->middleware('guest.redirect');

Route::get('/welcome', [HomeController::class, 'index'])->name('welcome')->middleware('guest.redirect');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');