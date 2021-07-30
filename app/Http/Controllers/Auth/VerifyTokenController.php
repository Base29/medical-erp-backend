<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerifyTokenController extends Controller
{

    public function __invoke()
    {

        $user = JWTAuth::parseToken()->authenticate();
        $user_roles = $user->roles
            ->makeHidden([
                'pivot',
                'created_at',
                'updated_at',
                'guard_name',
            ]);

        $user_practices = $user->practices
            ->makeHidden([
                'pivot',
                'created_at',
                'updated_at',
                'deleted_at',
            ]);
        $user_with_roles = Arr::add($user, 'roles', $user_roles);
        $user_with_practices = Arr::add($user_with_roles, 'practices', $user_practices);
        return response([
            'success' => true,
            'user' => $user_with_practices,
        ], 200);
    }
}