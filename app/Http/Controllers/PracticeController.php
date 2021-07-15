<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PracticeController extends Controller
{
    public function assign_practice(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        $user->practices()->attach($request->practice);
        return response([
            'success' => true,
            'message' => 'Added to Practice',
        ]);
    }
}