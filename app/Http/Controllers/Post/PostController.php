<?php

namespace App\Http\Controllers\Post;

use App\Helpers\FileUploadService;
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
use UpdateService;

class PostController extends Controller
{
    // Create Post
    public function create(CreatePostRequest $request)
    {
        try {

            // Check if the practice exists
            $practice = Practice::findOrFail($request->practice);

            // Check if the user belongs to the provided practice
            $userBelongsToPractice = auth()->user()->practices->contains('id', $practice->id);

            if (!$userBelongsToPractice) {
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
                    $attachmentUrl = FileUploadService::upload($file, 'communication-book', 's3');
                    $attachment = new PostAttachment();
                    $attachment->url = $attachmentUrl;
                    $post->postAttachments()->save($attachment);
                }
            }

            return Response::success(['post' => $post->with('postAttachments')->latest()->first()]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch all posts
    public function fetch()
    {
        try {

            $posts = Post::with('postAttachments', 'answers.user.roles', 'comments.user.roles', 'user.roles')->withCount(['answers', 'comments', 'postViews'])->latest()->paginate(10);

            return Response::success(['posts' => $posts]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch user's own post
    public function me(FetchOwnPostRequest $request)
    {
        try {

            // Fetching the post of the authenticated user only
            $posts = Post::where(['user_id' => auth()->user()->id, 'practice_id' => $request->practice])
                ->with('postAttachments', 'answers.user.roles', 'comments.user.roles', 'user')
                ->withCount(['answers', 'comments'])
                ->latest()
                ->paginate(10);

            return Response::success(['posts' => $posts]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update Post
    public function update(UpdatePostRequest $request)
    {
        try {

            // Allowed fields when updating a task
            $allowedFields = [
                'title',
                'subject',
                'message',
                'category',
                'is_public',
                'is_answered',
            ];

            // Checking if the $request doesn't contain any of the allowed fields
            if (!$request->hasAny($allowedFields)) {
                return Response::fail([
                    'message' => ResponseMessage::allowedFields($allowedFields),
                    'code' => 400,
                ]);
            }

            // Check if the post exist
            $post = Post::findOrFail($request->post);

            // Check if user own's the post
            if (!$post->ownedBy(auth()->user())) {
                return Response::fail([
                    'message' => ResponseMessage::notAllowedToUpdate('post'),
                    'code' => 403,
                ]);
            }

            // Update task's fields with the ones provided in the $request
            $postUpdated = UpdateService::updateModel($post, $request->all(), 'post');

            if ($postUpdated) {
                return Response::success(['post' => $post->with('user')->latest('updated_at')->first()]);
            }

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function delete($id)
    {
        try {

            // Check if post exist with the provided $id
            $post = Post::findOrFail($id);

            if (!$post) {
                return Response::fail([
                    'message' => ResponseMessage::notFound('Post', $id, false),
                    'code' => 404,
                ]);
            }

            // Check if user own's the post
            if (!$post->ownedBy(auth()->user())) {
                return Response::fail([
                    'message' => ResponseMessage::notAllowedToDelete('post'),
                    'code' => 403,
                ]);
            }

            // Delete post
            $post->delete();

            return Response::success(['message' => ResponseMessage::deleteSuccess('Post')]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch single post details
    public function fetchSinglePost(FetchSinglePostRequest $request)
    {
        try {

            // Check if the post exists
            $post = Post::where('id', $request->post)
                ->with('postAttachments', 'answers.user.roles', 'comments.user.roles', 'user.roles')
                ->withCount(['answers', 'comments'])
                ->firstOrFail();

            // Check if the visibility is private for the post
            $visibility = $post->is_public;

            if (!$visibility) {
                return Response::fail([
                    'message' => ResponseMessage::notPublic('Post'),
                    'code' => 400,
                ]);
            }

            return Response::success(['post' => $post]);

        } catch (\Exception $e) {
            ray($e);
            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }

    }

    // Post Views
    public function postView(RecordPostViewRequest $request)
    {
        try {

            // Get the post
            $post = Post::findOrFail($request->post);

            // Check if the user has already viewed the post
            $alreadyViewed = $post->postViews->contains('user_id', auth()->user()->id);

            // Recording unique view for the post
            if (!$alreadyViewed) {
                $postView = new PostView();
                $postView->post_id = $request->post;
                $postView->user_id = auth()->user()->id;
                $postView->save();
            }

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}