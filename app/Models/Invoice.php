<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model {

    use SoftDeletes;

    protected $table = 'invoices';
    
    protected $fillable = [
        'uuid',
        'user_id',
        'product_id',
        'sale_id',
        'title',
        'description',
        'value',
        'due_date',
        'payment_date',
        'payment_status',
        'payment_token',
        'payment_url',
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
        $labels = [
            'PENDING' => '
                <span class="badge bg-warning me-1">Pendente</span>
            ',
            'PAID' => '
                <span class="badge bg-success me-1">Pago</span>
            ',
            'CANCELED' => '
                <span class="badge bg-danger me-1">Cancelado</span>
            ',
            'REFUNDED' => '
                <span class="badge bg-primary me-1">Estornado</span>
            ',
            'FAILED' => '
                <span class="badge bg-danger me-1">Falhou</span>
            ',
        ];

        return $labels[$this->payment_status] ?? ' ';
    }
}
