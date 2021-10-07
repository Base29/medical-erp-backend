<?php

namespace App\Http\Controllers\Comment;

use App\Helpers\CustomValidation;
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
            return response([
                'success' => false,
                'message' => 'Post with ID ' . $request->post . ' not found',
            ], 404);
        }

        $comment = new Comment();
        $comment->comment = $request->comment;
        $comment->user_id = auth()->user()->id;
        $post->answers()->save($comment);

        return response([
            'success' => true,
            'comment' => $comment->with('user')->latest('id')->first(),
        ], 200);
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
            return response([
                'success' => false,
                'message' => 'Post with ID ' . $request->post . ' not found',
            ], 404);
        }

        // Fetch answers for post
        $comments = Comment::where('post_id', $post->id)->with('post', 'user')->paginate(10);
        return response([
            'success' => true,
            'post_comments' => $comments,
        ], 200);
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
            return response([
                'success' => false,
                'message' => 'You are not allowed to update this comment',
            ], 400);
        }

        // Update answer
        $comment->update(['comment' => $request->comment]);

        return response([
            'success' => true,
            'comment' => $comment->with('post', 'user')->latest('updated_at')->first(),
        ], 200);
    }

    public function delete($id)
    {
        // Check if answer exist with the provided ID
        $comment = Comment::find($id);

        if (!$comment) {
            return response([
                'success' => false,
                'message' => 'Comment with the given ID ' . $id . ' not found',
            ], 404);
        }

        // Check if the user updating the answer is the author of the answer
        $owned_by_user = $comment->owned_by(auth()->user());

        if (!$owned_by_user) {
            return response([
                'success' => false,
                'message' => 'You are not allowed to delete this comment',
            ], 400);
        }

        // Delete the answer
        $comment->delete();

        return response([
            'success' => true,
            'message' => 'Comment deleted',
        ], 200);
    }
}