<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchExtract extends Model {
    
    protected $table = 'search_extracts';

    protected $fillable = [
        'uuid',
        'search_id',
        'user_id',
        'is_paid',
        'data',
    ];
}
