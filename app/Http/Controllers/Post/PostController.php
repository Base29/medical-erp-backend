<?php

namespace App\Http\Controllers\Post;

use App\Helpers\CustomValidation;
use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostAttachment;
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
            'attachment' => 'file|mimes:doc,docx,pdf,jpg,png,jpeg|nullable',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Create Post
        $post = new Post();
        $post->title = $request->title;
        $post->subject = $request->subject;
        $post->message = $request->message;
        $post->category = $request->category;
        $post->type = $request->type;
        $post->user_id = auth()->user()->id;
        $post->save();

        // If file is attached when creating a post
        if ($request->has('attachment')) {
            $attachment_url = FileUpload::upload(request()->attachment, 'communication-book', 's3');
            $attachment = new PostAttachment();
            $attachment->url = $attachment_url;
            $post->post_attachments()->save($attachment);
        }

        // Adding attachments to the response
        Arr::add($post, 'attachments', $post->post_attachments()->get());

        return response([
            'success' => true,
            'post' => $post,
        ], 200);

    }

    public function fetch(Request $request)
    {
        return 'FETCH';
    }
}