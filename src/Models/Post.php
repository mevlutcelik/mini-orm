<?php

namespace MiniOrm\Models;

use MiniOrm\Model;

class Post extends Model
{
    protected string $table = 'posts';
    protected array $fillable = ['title', 'content', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}