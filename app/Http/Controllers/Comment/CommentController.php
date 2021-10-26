<?php

namespace App\Http\Controllers\Comment;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\CreateCommentRequest;
use App\Http\Requests\Comment\FetchCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    public function create(CreateCommentRequest $request)
    {

        try {

            // Check if the post exist
            $post = Post::findOrFail($request->post);

            $comment = new Comment();
            $comment->comment = $request->comment;
            $comment->user_id = auth()->user()->id;
            $post->answers()->save($comment);

            return Response::success(['comment' => $comment->with('user')->latest('id')->first()]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // fetch post comments
    public function fetch(FetchCommentRequest $request)
    {

        try {

            // Check if the post exist
            $post = Post::findOrFail($request->post);

            // Fetch comments for post
            $comments = Comment::where('post_id', $post->id)->with('post', 'user')->paginate(10);

            return Response::success(['post_comments' => $comments]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update comment
    public function update(UpdateCommentRequest $request)
    {
        try {

            // Fetch the comment
            $comment = Comment::where('id', $request->comment_id)->with('post', 'user')->withTrashed()->firstOrFail();

            // Check if the comment is soft deleted
            if ($comment->trashed()) {
                return Response::fail([
                    'message' => ResponseMessage::customMessage('The selected comment id is invalid.'),
                    'code' => 404,
                ]);
            }

            // Check if the user updating the comment is the author of the comment
            $owned_by_user = $comment->owned_by(auth()->user());

            if (!$owned_by_user) {
                return Response::fail([
                    'message' => ResponseMessage::notAllowedToUpdate('comment'),
                    'code' => 400,
                ]);
            }

            // Update comment
            $comment->update(['comment' => $request->comment]);

            return Response::success(['comment' => $comment->with('post', 'user')->latest('updated_at')->firstOrFail()]);

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

            // Check if answer exist with the provided ID
            $comment = Comment::find($id);

            if (!$comment) {
                return Response::fail([
                    'message' => ResponseMessage::notFound('Comment', $id, false),
                    'code' => 404,
                ]);
            }

            // Check if the user updating the answer is the author of the answer
            $owned_by_user = $comment->owned_by(auth()->user());

            if (!$owned_by_user) {
                return Response::fail([
                    'message' => ResponseMessage::notAllowedToDelete('comment'),
                    'code' => 400,
                ]);
            }

            // Delete the answer
            $comment->delete();

            return Response::success(['message' => ResponseMessage::deleteSuccess('Comment')]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}