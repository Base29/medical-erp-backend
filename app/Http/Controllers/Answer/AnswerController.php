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
use Exception;

class AnswerController extends Controller
{
    public function create(CreateAnswerRequest $request)
    {
        try {
            // Check if the post exist
            $post = Post::findOrFail($request->post);

            // Create answer for the post
            $answer = new Answer();
            $answer->answer = $request->answer;
            $answer->user_id = auth()->user()->id;
            $post->answers()->save($answer);

            return Response::success(['answer' => $answer->with('user')->latest('id')->first()]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // fetch post answers
    public function fetch(FetchAnswersRequest $request)
    {
        try {
            // Check if the post exist
            $post = Post::findOrFail($request->post);

            // Fetch answers for post
            $answers = Answer::where('post_id', $post->id)->with('post', 'user')->latest()->paginate(10);
            return Response::success(['post_answers' => $answers]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update answer
    public function update(UpdateAnswerRequest $request)
    {

        try {
            // Fetch the answer
            $answer = Answer::where('id', $request->answer_id)->with('post', 'user')->firstOrFail();

            // Check if the user updating the answer is the author of the answer
            $ownedByUser = $answer->ownedBy(auth()->user());

            if (!$ownedByUser) {
                return Response::fail([
                    'message' => ResponseMessage::notAllowedToUpdate('answer'),
                    'code' => 400,
                ]);
            }

            // Update answer
            $answer->update(['answer' => $request->answer]);

            return Response::success(['answer' => $answer->with('post', 'user')->latest('updated_at')->first()]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function delete($id)
    {
        try {
            // Check if answer exist with the provided ID
            $answer = Answer::findOrFail($id);

            if (!$answer) {
                return Response::fail([
                    'message' => ResponseMessage::notFound('Answer', $id, false),
                    'code' => 404,
                ]);
            }

            // Check if the user updating the answer is the author of the answer
            $ownedByUser = $answer->ownedBy(auth()->user());

            if (!$ownedByUser) {
                return Response::fail([
                    'message' => ResponseMessage::notAllowedToDelete('answer'),
                    'code' => 400,
                ]);
            }

            // Delete the answer
            $answer->delete();

            return Response::success(['message' => ResponseMessage::deleteSuccess('Answer')]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
