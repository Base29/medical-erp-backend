<?php

namespace App\Http\Controllers\Post;

use App\Helpers\CustomValidation;
use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PostController extends Controller
{
    public function create(Request $request)
    {
        // Validation rules
        $rules = [
            'title' => 'required|string',
            'subject' => 'required|string',
            'message' => 'required|string',
            'category' => 'required|string',
            'type' => 'required|string',
            'attachments.*' => 'mimes:doc,docx,pdf,jpg,png,jpeg',
            'practice' => 'required|numeric',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Check if the practice exists
        $practice = Practice::find($request->practice);

        if (!$practice) {
            return response([
                'success' => false,
                'message' => 'Practice with ID ' . $request->practice . ' does not exist',
            ], 404);
        }

        // Check if the user belongs to the provided practice
        $user_belongs_to_practice = auth()->user()->practices->contains('id', $practice->id);

        if (!$user_belongs_to_practice) {
            return response([
                'success' => false,
                'message' => 'User ' . auth()->user()->name . ' does not belongs to practice ' . $practice->practice_name,
            ], 409);
        }

        // Create Post
        $post = new Post();
        $post->title = $request->title;
        $post->subject = $request->subject;
        $post->message = $request->message;
        $post->category = $request->category;
        $post->type = $request->type;
        $post->user_id = auth()->user()->id;
        $post->practice_id = $practice->id;
        $post->save();

        // If file is attached when creating a post
        if ($request->has('attachments')) {
            $files = $request->attachments;

            foreach ($files as $file) {
                ray($file);
                $attachment_url = FileUpload::upload($file, 'communication-book', 's3');
                $attachment = new PostAttachment();
                $attachment->url = $attachment_url;
                $post->post_attachments()->save($attachment);
            }
        }

        // Adding attachments to the response
        Arr::add($post, 'post_attachments', $post->post_attachments()->get());

        return response([
            'success' => true,
            'post' => $post,
        ], 200);

    }

    public function fetch_own()
    {
        // Fetching the post of the authenticated user only
        $posts = Post::where('user_id', auth()->user()->id)->with('post_attachments')->paginate(10);

        return response([
            'success' => true,
            'posts' => $posts,
        ], 200);
    }
}