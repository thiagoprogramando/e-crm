<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Withdraw extends Model {

    use SoftDeletes;

    protected $table = 'withdrawals';
    
    protected $fillable = [
        'uuid',
        'user_id',
        'payment_key',
        'payment_token',
        'payment_url',
        'value',
        'description',
        'confirmed_at',
        'is_paid',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function statusLabel() {

        $created    = $this->created_at?->format('d/m/Y');
        $confirmed  = $this->confirmed_at?->format('d/m/Y');

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
                Confirmado em ' . ($confirmed ?? $created) . '
            </span>
        ';
    }
}
