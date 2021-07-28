<?php

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Http\Request;

class PracticeController extends Controller
{
    public function assign_practice(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        ray($user->practices());
        $user->practices()->attach($request->practice);
        return response([
            'success' => true,
            'message' => 'Added to Practice',
        ]);
    }

    public function assign_policy(Request $request)
    {
        $practice = Practice::where('practice_name', $request->practice_name)->first();

        $practice->policies()->attach($request->policy);

        return response([
            'success' => true,
            'message' => 'Policy assigned to practice ' . $request->practice_name,
        ]);
    }

    public function get_practices()
    {
        $practices = Practice::with('policies')->get();

        return response([
            'success' => true,
            'practices' => $practices,
        ]);
    }
}