<?php

namespace App\Http\Controllers\Answer;

use App\Helpers\CustomValidation;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Post;
use Illuminate\Http\Request;

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
            return Response::fail([
                'message' => ResponseMessage::notFound('Post', $request->post, false),
                'code' => 404,
            ]);
        }

        $answer = new Answer();
        $answer->answer = $request->answer;
        $answer->user_id = auth()->user()->id;
        $post->answers()->save($answer);

        return Response::success(['answer' => $answer->with('user')->latest('id')->first()]);
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
            return Response::fail([
                'message' => ResponseMessage::notFound('Post', $request->post, false),
                'code' => 404,
            ]);
        }

        // Fetch answers for post
        $answers = Answer::where('post_id', $post->id)->with('post', 'user')->paginate(10);
        return Response::success(['post_answers' => $answers]);
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
        $answer = Answer::where('id', $request->answer_id)->with('post', 'user')->first();

        // Check if the user updating the answer is the author of the answer
        $owned_by_user = $answer->owned_by(auth()->user());

        if (!$owned_by_user) {
            return Response::fail([
                'message' => ResponseMessage::notAllowedToUpdate('answer'),
                'code' => 400,
            ]);
        }

        // Update answer
        $answer->update(['answer' => $request->answer]);

        return Response::success(['answer' => $answer->with('post', 'user')->latest('updated_at')->first()]);
    }

    public function delete($id)
    {
        // Check if answer exist with the provided ID
        $answer = Answer::find($id);

        if (!$answer) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Answer', $id, false),
                'code' => 404,
            ]);
        }

        // Check if the user updating the answer is the author of the answer
        $owned_by_user = $answer->owned_by(auth()->user());

        if (!$owned_by_user) {
            return Response::fail([
                'message' => ResponseMessage::notAllowedToDelete('answer'),
                'code' => 400,
            ]);
        }

        // Delete the answer
        $answer->delete();

        return Response::success(['message' => ResponseMessage::deleteSuccess('Answer')]);
    }
}