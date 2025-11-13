<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model {
    
    use SoftDeletes;

    protected $table = 'templates';

    protected $fillable = [
        'uuid',
        'user_id',
        'product_id',
        'title',
        'content',
        'input_settings',
        'access',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function contracts() {
        return $this->hasMany(Contract::class);
    }
}
