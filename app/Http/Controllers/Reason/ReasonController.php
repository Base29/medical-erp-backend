<?php

namespace App\Http\Controllers\Reason;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReasonController extends Controller
{
    public function create(Request $request)
    {
        return $request->all();
    }
}