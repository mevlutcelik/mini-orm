<?php

namespace MiniOrm\Models;

use MiniOrm\Model;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = ['name', 'email', 'password', 'age', 'status'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }
}