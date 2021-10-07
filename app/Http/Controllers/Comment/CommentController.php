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
            'answer' => $comment->with('user')->latest('id')->first(),
        ], 200);
    }
}