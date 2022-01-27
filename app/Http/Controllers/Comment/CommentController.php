<?php

namespace App\Http\Controllers\Comment;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\CreateCommentRequest;
use App\Http\Requests\Comment\FetchCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Services\Comment\CommentService;

class CommentController extends Controller
{

    // Local variable
    protected $commentService;

    // Constructor
    public function __construct(CommentService $commentService)
    {
        // Inject comment service
        $this->commentService = $commentService;
    }

    // Create comment
    public function create(CreateCommentRequest $request)
    {

        try {

            // Create comment service
            $comment = $this->commentService->createComment($request);

            // Return success response
            return Response::success(['comment' => $comment]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // fetch post comments
    public function fetch(FetchCommentRequest $request)
    {

        try {

            // Fetch comments service
            $comments = $this->commentService->fetchComments($request);

            // Return success response
            return Response::success(['post_comments' => $comments]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update comment
    public function update(UpdateCommentRequest $request)
    {
        try {

            // Update comment service
            $comment = $this->commentService->updateComment($request);

            // Return success response
            return Response::success([
                'comment' => $comment,
            ]);

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

            // Delete comment service
            $this->commentService->deleteComment($id);

            // Return success response
            return Response::success(['message' => ResponseMessage::deleteSuccess('Comment')]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}