<?php

namespace App\Models;

use App\Models\Answer;
use App\Models\Comment;
use App\Models\PostAttachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'subject',
        'message',
        'category',
        'type',
        'user_id',
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post_attachments()
    {
        return $this->hasMany(PostAttachment::class, 'post_id', 'id');
    }
}