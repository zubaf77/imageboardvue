<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'thread_id', 'ip', 'parent_post_id', 'user_id'];

    public function thread()
    {
        return $this->belongsTo(Thread::class);

    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parentPost()
    {
        return $this->belongsTo(Post::class, 'parent_post_id');
    }

    public function replies()
    {
        return $this->hasMany(Post::class, 'parent_post_id');
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

}
