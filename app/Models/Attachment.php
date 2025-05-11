<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'file_path', 'file_type','thread_id'];


    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }
}
