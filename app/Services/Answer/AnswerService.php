<?php

/**
 * Answer Service
 */

namespace App\Services\Answer;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Models\Answer;
use App\Models\Post;
use Exception;

class AnswerService
{
    // Create service
    public function createAnswer($request)
    {
        // Check if the post exist
        $post = Post::findOrFail($request->post);

        // Create answer for the post
        $answer = new Answer();
        $answer->answer = $request->answer;
        $answer->user_id = auth()->user()->id;
        $post->answers()->save($answer);

        // Return answer
        return $answer;
    }

    // Fetch all answers
    public function fetchAllAnswers($request)
    {
        // Check if the post exist
        $post = Post::findOrFail($request->post);

        // Fetch answers for post
        return Answer::where('post_id', $post->id)->with('post', 'user')->latest()->paginate(10);
    }

    // Update answer
    public function updateAnswer($request)
    {
        // Fetch the answer
        $answer = Answer::where('id', $request->answer_id)->with('post', 'user')->firstOrFail();

        // Check if the user updating the answer is the author of the answer
        $ownedByUser = $answer->ownedBy(auth()->user());

        if (!$ownedByUser) {
            throw new Exception(ResponseMessage::notAllowedToUpdate('answer'), Response::HTTP_FORBIDDEN);
        }

        // Update answer
        $answer->update(['answer' => $request->answer]);

        return $answer->with('post', 'user')->latest('updated_at')->first();
    }

    // Delete answer
    public function deleteAnswer($id)
    {
        // Check if answer exist with the provided ID
        $answer = Answer::findOrFail($id);

        if (!$answer) {
            throw new Exception(ResponseMessage::notFound('Answer', $id, false));
        }

        // Check if the user updating the answer is the author of the answer
        $ownedByUser = $answer->ownedBy(auth()->user());

        if (!$ownedByUser) {
            throw new Exception(ResponseMessage::notAllowedToDelete('answer'));
        }

        // Delete the answer
        $answer->delete();
    }
}