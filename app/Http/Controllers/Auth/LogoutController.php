<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    public function logout()
    {

        $isTokenValid = auth()->check();
        if (!$isTokenValid) {
            return response([
                'success' => false,
                'message' => 'Invalid Bearer Token',
            ], 401);
        }
        auth()->logout();
        return response([
            'success' => true,
            'message' => 'Logged out successfully',
        ], 200);
    }
}