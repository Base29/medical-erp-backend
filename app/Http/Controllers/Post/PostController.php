<?php

namespace App\Http\Controllers\Post;

use App\Helpers\FileUpload;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\FetchOwnPostRequest;
use App\Http\Requests\Post\FetchSinglePostRequest;
use App\Http\Requests\Post\RecordPostViewRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\PostView;
use App\Models\Practice;
use App\Models\User;

class PostController extends Controller
{
    // Create Post
    public function create(CreatePostRequest $request)
    {

        // Check if the practice exists
        $practice = Practice::find($request->practice);

        // Check if the user belongs to the provided practice
        $user_belongs_to_practice = auth()->user()->practices->contains('id', $practice->id);

        if (!$user_belongs_to_practice) {
            return Response::fail([
                'message' => ResponseMessage::notBelongTo(auth()->user()->name, $practice->practice_name),
                'code' => 409,
            ]);
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
        $post->is_public = $request->has('is_public') ? $request->is_public : 0;
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

        return Response::success(['post' => $post->with('post_attachments')->latest()->first()]);

    }

    // Fetch all posts
    public function fetch()
    {
        $posts = Post::with('post_attachments', 'answers.user.roles', 'comments.user.roles', 'user')->withCount(['answers', 'comments', 'post_views'])->paginate(10);

        return Response::success(['posts' => $posts]);
    }

    // Fetch user's own post
    public function me(FetchOwnPostRequest $request)
    {
        // Fetching the post of the authenticated user only
        $posts = Post::where(['user_id' => auth()->user()->id, 'practice_id' => $request->practice])
            ->with('post_attachments', 'answers.user.roles', 'comments.user.roles', 'user')
            ->withCount(['answers', 'comments'])
            ->paginate(10);

        return Response::success(['posts' => $posts]);
    }

    // Update Post
    public function update(UpdatePostRequest $request)
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
            return Response::fail([
                'message' => ResponseMessage::allowedFields($allowed_fields),
                'code' => 400,
            ]);
        }

        // Check if the post exist
        $post = Post::find($request->post);

        // Check if user own's the post
        if (!$post->owned_by(auth()->user())) {
            return Response::fail([
                'message' => ResponseMessage::notAllowedToUpdate('post'),
                'code' => 403,
            ]);
        }

        // Update task's fields with the ones provided in the $request
        $post_updated = $this->update_post($request->all(), $post);

        if ($post_updated) {
            return Response::success(['post' => $post->with('user')->latest('updated_at')->first()]);
        }
    }

    public function delete($id)
    {
        // Check if post exist with the provided $id
        $post = Post::find($id);

        if (!$post) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Post', $id, false),
                'code' => 404,
            ]);
        }

        // Check if user own's the post
        if (!$post->owned_by(auth()->user())) {
            return Response::fail([
                'message' => ResponseMessage::notAllowedToDelete('post'),
                'code' => 403,
            ]);
        }

        // Delete post
        $post->delete();

        return Response::success(['message' => ResponseMessage::deleteSuccess('Post')]);
    }

    // Fetch single post details
    public function fetch_single_post(FetchSinglePostRequest $request)
    {

        // Check if the post exists
        $post = Post::where('id', $request->post)
            ->with('post_attachments', 'answers', 'comments', 'user')
            ->withCount(['answers', 'comments'])
            ->first();

        // Check if the visibility is private for the post
        $visibility = $post->is_public;

        if (!$visibility) {
            return Response::fail([
                'message' => ResponseMessage::notPublic('Post'),
                'code' => 400,
            ]);
        }

        return Response::success(['post' => $post]);

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
    public function post_view(RecordPostViewRequest $request)
    {
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