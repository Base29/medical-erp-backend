<?php
namespace App\Services\Post;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\PostView;
use App\Models\Practice;
use Exception;

class PostService
{
    // Create post
    public function createPost($request)
    {
        // Check if the practice exists
        $practice = Practice::findOrFail($request->practice);

        // Check if the user belongs to the provided practice
        $userBelongsToPractice = auth()->user()->practices->contains('id', $practice->id);

        if (!$userBelongsToPractice) {
            throw new Exception(ResponseMessage::notBelongTo(auth()->user()->email, $practice->practice_name), Response::HTTP_CONFLICT);
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

        return Response::success([
            'code' => Response::HTTP_CREATED,
            'post' => $post->with('postAttachments')->latest()->first(),
        ]);
    }

    // Fetch Posts
    public function fetchPosts()
    {
        $posts = Post::with('postAttachments', 'answers.user.roles', 'comments.user.roles', 'user.roles')
            ->withCount(['answers', 'comments', 'postViews'])
            ->latest()
            ->paginate(10);

        return Response::success([
            'code' => Response::HTTP_OK,
            'posts' => $posts,
        ]);
    }

    // Fetch user's own posts
    public function fetchUserPosts($request)
    {
        // Fetching the post of the authenticated user only
        $posts = Post::where(['user_id' => auth()->user()->id, 'practice_id' => $request->practice])
            ->with('postAttachments', 'answers.user.roles', 'comments.user.roles', 'user')
            ->withCount(['answers', 'comments'])
            ->latest()
            ->paginate(10);

        return Response::success([
            'code' => Response::HTTP_OK,
            'posts' => $posts,
        ]);
    }

    // Update post
    public function updatePost($request)
    {
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
            throw new Exception(ResponseMessage::allowedFields($allowedFields), Response::HTTP_BAD_REQUEST);
        }

        // Check if the post exist
        $post = Post::findOrFail($request->post);

        // Check if user own's the post
        if (!$post->ownedBy(auth()->user())) {
            throw new Exception(ResponseMessage::notAllowedToUpdate('post'), Response::HTTP_FORBIDDEN);
        }

        // Update task's fields with the ones provided in the $request
        UpdateService::updateModel($post, $request->validated(), 'post');

        return Response::success([
            'code' => Response::HTTP_OK,
            'post' => $post->with('user')->latest('updated_at')->first(),
        ]);

    }

    // Delete post
    public function deletePost($id)
    {
        // Check if post exist with the provided $id
        $post = Post::findOrFail($id);

        if (!$post) {
            throw new Exception(ResponseMessage::notFound('Post', $id, false), Response::HTTP_NOT_FOUND);
        }

        // Check if user own's the post
        if (!$post->ownedBy(auth()->user())) {
            throw new Exception(ResponseMessage::notAllowedToDelete('post'), Response::HTTP_FORBIDDEN);
        }

        // Delete post
        $post->delete();

        return Response::success([
            'code' => Response::HTTP_OK,
            'post' => $post,
        ]);
    }

    // Fetch single post
    public function fetchSinglePost($request)
    {
        // Check if the post exists
        $post = Post::where('id', $request->post)
            ->with('postAttachments', 'answers.user.roles', 'comments.user.roles', 'user.roles')
            ->withCount(['answers', 'comments'])
            ->firstOrFail();

        // Check if the visibility is private for the post
        $visibility = $post->is_public;

        if (!$visibility) {
            throw new Exception(ResponseMessage::notPublic('Post'), Response::HTTP_FORBIDDEN);
        }

        return Response::success([
            'code' => Response::HTTP_OK,
            'post' => $post,
        ]);
    }

    // Record views on posts
    public function recordPostViews($request)
    {
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
    }
}