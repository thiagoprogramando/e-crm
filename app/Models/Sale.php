<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model {
    
    use SoftDeletes;

    protected $table = 'sales';

    protected $fillable = [
        'uuid',
        'user_id',
        'product_id',
        'payment_option_id',
        'customer_name',
        'customer_cpfcnpj',
        'customer_email',
        'customer_phone',
        'value',
        'discount',
        'payment_token',
        'payment_url',
        'payment_date',
        'payment_due_date',
        'payment_status',
        'settings',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function paymentOption() {
        return $this->belongsTo(PaymentOption::class);
    }

    public function list() {
        return $this->belongsTo(Lists::class, 'list_id');
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

    public function statusExportLabel() {
        $labels = [
            'PENDING'   => 'PENDENTE',
            'PAID'      => 'PAGO',
            'CANCELED'  => 'CANCELADO',
            'REFUNDED'  => 'ESTORNADO',
            'FAILED'    => 'FALHOU',
        ];

        return $labels[$this->payment_status] ?? ' ';
    }

    public function cpfcnpjLabel() {

        $cpfCnpj = preg_replace('/[^0-9]/', '', $this->customer_cpfcnpj);
        if (strlen($cpfCnpj) === 11) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $cpfCnpj);
        } elseif (strlen($cpfCnpj) === 14) {
            return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $cpfCnpj);
        }

        return $cpfCnpj;
    }

    public function phoneLabel() {
        $phone = preg_replace('/[^0-9]/', '', $this->customer_phone);
        if (strlen($phone) === 10) {
            return preg_replace("/(\d{2})(\d{4})(\d{4})/", "($1) $2-$3", $phone);
        } elseif (strlen($phone) === 11) {
            return preg_replace("/(\d{2})(\d{5})(\d{4})/", "($1) $2-$3", $phone);
        }

        return $phone;
    }
}
