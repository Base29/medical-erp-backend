<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    public function __invoke()
    {
        auth()->logout(true);
        return response([
            'success' => true,
            'message' => 'Logged out successfully',
        ], 200);
    }
}