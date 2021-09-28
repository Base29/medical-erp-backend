<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function create(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required',
            'practice' => 'required|numeric',
            'icon' => 'required',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }
    }
}