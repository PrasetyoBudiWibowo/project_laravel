<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

use App\Helper\AppLogger;

class MenuController extends Controller
{
    public function daftar_menu()
    {
        return view('setting.daftar_menu');
    }
}
