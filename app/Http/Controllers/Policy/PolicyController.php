<?php

namespace App\Http\Controllers\Policy;

use App\Http\Controllers\Controller;
use App\Models\Policy;

class PolicyController extends Controller
{
    public function fetch_policies()
    {
        // Fetching policies
        $policies = Policy::with('signatures')->get();

        return response([
            'success' => true,
            'policies' => $policies,
        ]);
    }
}