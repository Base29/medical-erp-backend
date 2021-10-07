<?php

namespace App\Http\Controllers\Post;

use App\Helpers\CustomValidation;
use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\PostView;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PostController extends Controller
{
    // Create Post
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
        if ($request->has('attachments') || $request->filled('attachments')) {
            $files = $request->attachments;

            foreach ($files as $file) {
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

    // Fetch all posts
    public function fetch()
    {
        $posts = Post::with('post_attachments', 'answers', 'comments', 'user')->withCount(['answers', 'comments', 'post_views'])->paginate(10);

        return response([
            'success' => true,
            'posts' => $posts,
        ], 200);
    }

    // Fetch user's own post
    public function me(Request $request)
    {
        // Validation rules
        $rules = [
            'practice' => 'required|numeric',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Fetching the post of the authenticated user only
        $posts = Post::where(['user_id' => auth()->user()->id, 'practice_id' => $request->practice])
            ->with('post_attachments', 'answers', 'comments', 'user')
            ->withCount(['answers', 'comments'])
            ->paginate(10);

        return response([
            'success' => true,
            'posts' => $posts,
        ], 200);
    }

    public function update(Request $request)
    {
        // Allowed fields when updating a task
        $allowed_fields = [
            'title',
            'subject',
            'message',
            'category',
            'is_public',
            'is_answered',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowed_fields)) {
            return response([
                'success' => false,
                'message' => 'Update request should contain any of the allowed fields ' . implode("|", $allowed_fields),
            ], 400);
        }

        // Validation rules
        $rules = [
            'title' => 'string',
            'subject' => 'string',
            'message' => 'string',
            'category' => 'string',
            'post' => 'required|numeric',
            'is_public' => 'boolean',
            'is_answered' => 'boolean',
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

        // Check if user own's the post
        if (!$post->owned_by(auth()->user())) {
            return response([
                'success' => false,
                'message' => 'You are not authorize to update this post',
            ], 403);
        }

        // Update task's fields with the ones provided in the $request
        $post_updated = $this->update_post($request->all(), $post);

        if ($post_updated) {
            return response([
                'success' => true,
                'post' => $post->with('user')->latest('updated_at')->first(),
            ]);
        }
    }

    public function delete($id)
    {
        // Check if post exist with the provided $id
        $post = Post::find($id);

        if (!$post) {
            return response([
                'success' => false,
                'message' => 'Post with the ID ' . $id . ' not found',
            ], 404);
        }

        // Check if user own's the post
        if (!$post->owned_by(auth()->user())) {
            return response([
                'success' => false,
                'message' => 'You are not authorize to delete this post',
            ], 403);
        }

        // Delete post
        $post->delete();

        return response([
            'success' => true,
            'message' => 'Post ' . $post->id . ' deleted',
        ], 200);
    }

    // Fetch single post details
    public function fetch_single_post(Request $request)
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

        // Check if the post exists
        $post = Post::where('id', $request->post)
            ->with('post_attachments', 'answers', 'comments', 'user')
            ->withCount(['answers', 'comments'])
            ->first();

        if (!$post) {
            return response([
                'success' => false,
                'message' => 'Post with ID ' . $request->post . ' not found',
            ], 404);
        }

        // Check if the visibility is private for the post
        $visibility = $post->is_public;

        if (!$visibility) {
            return response([
                'success' => true,
                'message' => 'Post ' . $post->id . ' is not public',
            ], 400);
        }

        return response([
            'success' => true,
            'post' => $post,
        ], 200);

    }

    // Helper function for updating fields for the post sent through request
    private function update_post($fields, $post)
    {
        foreach ($fields as $field => $value) {
            if ($field !== 'post') {
                $post->$field = $value;
            }
        }
        $post->save();
        return true;
    }

    // Post Views
    public function post_view(Request $request)
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

        // Get the post
        $post = Post::find($request->post);

        // Check if the user has already viewed the post
        $already_viewed = $post->post_views->contains('user_id', auth()->user()->id);

        // Recording unique view for the post
        if (!$already_viewed) {
            $post_view = new PostView();
            $post_view->post_id = $request->post;
            $post_view->user_id = auth()->user()->id;
            $post_view->save();
        }
    }
}