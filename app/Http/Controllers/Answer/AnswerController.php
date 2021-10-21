<?php

namespace App\Http\Controllers\Answer;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Answer\CreateAnswerRequest;
use App\Http\Requests\Answer\FetchAnswersRequest;
use App\Http\Requests\Answer\UpdateAnswerRequest;
use App\Models\Answer;
use App\Models\Post;

class AnswerController extends Controller
{
    public function create(CreateAnswerRequest $request)
    {

        // Check if the post exist
        $post = Post::find($request->post);

        // Create answer for the post
        $answer = new Answer();
        $answer->answer = $request->answer;
        $answer->user_id = auth()->user()->id;
        $post->answers()->save($answer);

        return Response::success(['answer' => $answer->with('user')->latest('id')->first()]);
    }

    // fetch post answers
    public function fetch(FetchAnswersRequest $request)
    {

        // Check if the post exist
        $post = Post::find($request->post);

        // Fetch answers for post
        $answers = Answer::where('post_id', $post->id)->with('post', 'user')->paginate(10);
        return Response::success(['post_answers' => $answers]);
    }

    // Update answer
    public function update(UpdateAnswerRequest $request)
    {

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