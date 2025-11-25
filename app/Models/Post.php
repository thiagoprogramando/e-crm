<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model {

    use SoftDeletes;

    protected $table = 'posts';
    
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'url',
        'is_fixed',
        'access',
    ];
}
