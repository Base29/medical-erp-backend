<?php

/**
 * Comment Service
 */

namespace App\Services\Comment;

use App\Helpers\ResponseMessage;
use App\Models\Comment;
use App\Models\Post;

class CommentService
{
    // Create Comment
    public function createComment($request)
    {
        // Check if the post exist
        $post = Post::findOrFail($request->post);

        $comment = new Comment();
        $comment->comment = $request->comment;
        $comment->user_id = auth()->user()->id;
        $post->answers()->save($comment);

        return $comment->with('user')->latest()->first();
    }

    // Fetch post comments
    public function fetchComments($request)
    {
        // Check if the post exist
        $post = Post::findOrFail($request->post);

        // Return comments for post
        return Comment::where('post_id', $post->id)->with('post', 'user')->latest()->paginate(10);
    }

    // Update comment
    public function updateComment($request)
    {
        // Fetch the comment
        $comment = Comment::where('id', $request->comment_id)->with('post', 'user')->withTrashed()->firstOrFail();

        // Check if the comment is soft deleted
        if ($comment->trashed()) {
            throw new \Exception(ResponseMessage::customMessage('The selected comment is invalid or deleted.'));
        }

        // Check if the user updating the comment is the author of the comment
        $ownedByUser = $comment->ownedBy(auth()->user());

        if (!$ownedByUser) {
            throw new \Exception(ResponseMessage::notAllowedToUpdate('comment'));
        }

        // Update comment
        $comment->update(['comment' => $request->comment]);

        return $comment->with('post', 'user')->latest('updated_at')->firstOrFail();
    }

    // Delete comment
    public function deleteComment($id)
    {
        // Check if answer exist with the provided ID
        $comment = Comment::find($id);

        if (!$comment) {
            throw new \Exception(ResponseMessage::notFound('Comment', $id, false));
        }

        // Check if the user updating the answer is the author of the answer
        $ownedByUser = $comment->ownedBy(auth()->user());

        if (!$ownedByUser) {
            throw new \Exception(ResponseMessage::notAllowedToDelete('comment'));
        }

        // Delete the answer
        return $comment->delete();
    }
}