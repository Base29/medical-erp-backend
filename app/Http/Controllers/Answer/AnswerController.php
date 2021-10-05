<?php

namespace App\Http\Controllers\Answer;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AnswerController extends Controller
{
    public function create(Request $request)
    {
        // Validation rules
        $rules = [
            'answer' => 'required',
            'post' => 'required|numeric',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Check if the post exist
        $post = Post::find($request->post);

        if (!$post) {
            return response([
                'success' => false,
                'message' => 'Post with ID ' . $request->post . ' not found',
            ], 404);
        }

        $answer = new Answer();
        $answer->answer = $request->answer;
        $answer->user_id = auth()->user()->id;
        $post->answers()->save($answer);

        Arr::add($answer, 'commenter_name', auth()->user()->name);

        return response([
            'success' => true,
            'answer' => $answer,
        ], 200);
    }
}