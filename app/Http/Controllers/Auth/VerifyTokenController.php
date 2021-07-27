<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerifyTokenController extends Controller
{

    public function verify_token()
    {

        $user = JWTAuth::parseToken()->authenticate();
        $user_roles = $user->roles->makeHidden([
            'pivot',
            'created_at',
            'updated_at',
            'guard_name',
        ]);

        $user_practices = $user->practices->makeHidden([
            'pivot',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);
        $userArr = Arr::add($user, 'roles', $user_roles);
        $userWithPractices = Arr::add($userArr, 'practices', $user_practices);
        return response([
            'success' => true,
            'user' => $userWithPractices,
        ], 200);
    }
}