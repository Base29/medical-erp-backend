<?php

namespace App\Http\Controllers\Post;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\FetchOwnPostRequest;
use App\Http\Requests\Post\FetchSinglePostRequest;
use App\Http\Requests\Post\RecordPostViewRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Post;
use App\Models\PostView;
use App\Services\Post\PostService;

class PostController extends Controller
{
    // Local variable
    protected $postService;

    // Constructor
    public function __construct(PostService $postService)
    {
        // Inject service
        $this->postService = $postService;
    }

    // Create Post
    public function create(CreatePostRequest $request)
    {
        try {
            // Create post
            return $this->postService->createPost($request);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch all posts
    public function fetch()
    {
        try {

            // Fetch posts
            return $this->postService->fetchPosts();

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch user's own post
    public function me(FetchOwnPostRequest $request)
    {
        try {

            // Fetch auth()->user() posts
            return $this->postService->fetchUserPosts($request);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update Post
    public function update(UpdatePostRequest $request)
    {
        try {

            // Update Post
            return $this->postService->updatePost($request);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function delete($id)
    {
        try {

            // Delete post
            return $this->postService->deletePost($id);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch single post details
    public function fetchSinglePost(FetchSinglePostRequest $request)
    {
        try {
            // Fetch single post
            return $this->postService->fetchSinglePost($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }

    }

    // Post Views
    public function postView(RecordPostViewRequest $request)
    {
        try {

            // Record post view
            return $this->postService->recordPostViews($request);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}