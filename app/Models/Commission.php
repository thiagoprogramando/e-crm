<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model {

    protected $table = 'commissions';
    
    protected $fillable = [
        'uuid',
        'user_id',
        'product_id',
        'payment_token',
        'value',
        'description',
        'confirmed_at',
        'is_paid'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function sale() {
        return $this->belongsTo(Sale::class);
    }
}
