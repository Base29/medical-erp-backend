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

    // fetch post answers
    public function fetch(Request $request)
    {
        // Validation rules
        $rules = [
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

        // Fetch answers for post
        $answers = Answer::where('post_id', $post->id)->with('post')->paginate(10);
        return response([
            'success' => true,
            'post_answers' => $answers,
        ], 200);
    }

    // Update answer
    public function update(Request $request)
    {
        // Validation rules
        $rules = [
            'answer' => 'required',
            'answer_id' => 'required|numeric',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Fetch the answer
        $answer = Answer::where('id', $request->answer_id)->with('post')->first();

        // Check if the user updating the answer is the author of the answer
        $owned_by_user = $answer->owned_by(auth()->user());

        if (!$owned_by_user) {
            return response([
                'success' => false,
                'message' => 'You are not allowed to update this post',
            ], 400);
        }

        // Update answer
        $answer->update(['answer' => $request->answer]);

        return response([
            'success' => true,
            'answer' => $answer,
        ], 200);
    }
}