<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Services\UserService;

use App\Helper\AppLogger;

class UserController extends Controller
{
    protected $userService;

    public function __construct(
        UserService $userService,
    )
    {
        $this->userService = $userService;
    }

    public function getDataLevelUser()
    {
        $data = $this->userService->getLevelUser();

        if ($data->isEmpty()) {
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
