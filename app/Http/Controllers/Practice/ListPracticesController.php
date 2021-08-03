<?php

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Models\Practice;

class ListPracticesController extends Controller
{
    public function __invoke()
    {
        // Fetch practices
        $practices = Practice::with('policies')->paginate(10);

        return response([
            'success' => true,
            'practices' => $practices,
        ]);
    }
}