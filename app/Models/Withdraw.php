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
        'payment_log',
        'value',
        'description',
        'confirmed_at',
        'is_paid',
    ];

    public function user() {
        return $this->belongsTo(User::class);
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
