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

    public function statusLabel() {
        
        $createdAt = $this->created_at instanceof \Carbon\Carbon
            ? $this->created_at
            : \Carbon\Carbon::parse($this->created_at);

        $created = $createdAt->format('d/m/Y');

        $confirmedAt = null;

        if (!empty($this->confirmed_at)) {
            $confirmedAt = $this->confirmed_at instanceof \Carbon\Carbon
                ? $this->confirmed_at
                : \Carbon\Carbon::parse($this->confirmed_at);

            $confirmedAt = $confirmedAt->format('d/m/Y');
        }

        if (!$this->is_paid) {
            return '
                <span class="badge bg-info me-1">
                    Solicitado em ' . $created . '
                </span>
            ';
        }

        return '
            <span class="badge bg-warning me-1">
                Solicitado em ' . $created . '
            </span>
            <span class="badge bg-success me-1">
                Confirmado em ' . ($confirmedAt ?? $created) . '
            </span>
        ';
    }
}
