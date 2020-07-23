<?php

namespace MarksIhor\LaravelMessaging\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $guarded = ['id'];

    protected $visible = ['favorite_id', 'favorite_type'];

    public function favoriteable()
    {
        return $this->morphTo();
    }
}