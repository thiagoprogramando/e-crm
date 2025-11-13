<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentOption extends Model {
    
    use SoftDeletes;

    protected $table = 'payment_options';

    protected $fillable = [
        'uuid',
        'product_id',
        'title',
        'description',
        'value',
        'commission_seller',
        'commission_parent',
        'payment_method',
        'payment_installments',
        'payment_splits',
        'payment_settings',
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function methodLabel() {
        return match ($this->payment_method) {
            'CREDIT_CARD' => 'Cartão de Crédito/Débito',
            'BOLETO'      => 'Boleto',
            'PIX'         => 'Pix',
            'CASH'        => 'Espécie/Dinheiro',
            'TED'         => 'Transferência/TED',
            'PDV'         => 'PDV - Manual',
            default       => '---',
        };
    }
}
