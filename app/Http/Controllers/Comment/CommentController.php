<?php

namespace App\Http\Controllers\Comment;

use App\Helpers\CustomValidation;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function create(Request $request)
    {
        // Validation rules
        $rules = [
            'comment' => 'required',
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

        $comment = new Comment();
        $comment->comment = $request->comment;
        $comment->user_id = auth()->user()->id;
        $post->answers()->save($comment);

        return Response::success(['comment' => $comment->with('user')->latest('id')->first()]);
    }

    // fetch post comments
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
        $comments = Comment::where('post_id', $post->id)->with('post', 'user')->paginate(10);

        return Response::success(['post_comments' => $comments]);
    }

    // Update comment
    public function update(Request $request)
    {
        // Validation rules
        $rules = [
            'comment' => 'required',
            'comment_id' => 'required|numeric',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Fetch the answer
        $comment = Comment::where('id', $request->comment_id)->with('post', 'user')->first();

        // Check if the user updating the answer is the author of the answer
        $owned_by_user = $comment->owned_by(auth()->user());

        if (!$owned_by_user) {
            return Response::fail([
                'message' => ResponseMessage::notAllowedToUpdate('comment'),
                'code' => 400,
            ]);
        }

        // Update answer
        $comment->update(['comment' => $request->comment]);

        return Response::success(['comment' => $comment->with('post', 'user')->latest('updated_at')->first()]);
    }

    public function delete($id)
    {
        // Check if answer exist with the provided ID
        $comment = Comment::find($id);

        if (!$comment) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Comment', $id, false),
                'code' => 404,
            ]);
        }

        // Check if the user updating the answer is the author of the answer
        $owned_by_user = $comment->owned_by(auth()->user());

        if (!$owned_by_user) {
            return Response::fail([
                'message' => ResponseMessage::notAllowedToDelete('comment'),
                'code' => 400,
            ]);
        }

        // Delete the answer
        $comment->delete();

        return Response::success(['message' => ResponseMessage::deleteSuccess('Comment')]);
    }
}