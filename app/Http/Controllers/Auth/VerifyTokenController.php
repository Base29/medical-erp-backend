<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerifyTokenController extends Controller
{

    public function verify_token()
    {
        return response([
            'success' => true,
            'user' => JWTAuth::parseToken()->authenticate(),
        ], 200);
    }
}