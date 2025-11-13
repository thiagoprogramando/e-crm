<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends Model { 

    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'uuid',
        'title',
        'description',
        'value',
        'max_value',
        'min_value',
        'cost_value',
        'fees_value',
        'cashback_value',
        'cashback_percentage',
        'status',
        'type',
        'time',
        'access',
        'is_blocked',
    ];

    public function options () {
        return $this->hasMany(PaymentOption::class, 'product_id', 'id');
    }
    
    public function statusLabel() {
        $labels = [
            'active'    => '
                <span class="badge badge-dot bg-success me-1"></span>
                <small>Ativo |</small>
            ',
            'inactive'  => '
                <span class="badge badge-dot bg-danger me-1"></span>
                <small>Inativo |</small>
            ',
        ];

        return $labels[$this->status] ?? '---';
    }

    public function accessLabel() {
        return match ($this->access) {
            'admin'       => 'Administradores',
            'colaborator' => 'Colaboradores',
            'user'        => 'Consultores/Vendedores',
            default       => 'Todos',
        };
    }

    public function timeLabel() {
        switch ($this->time) {
            case 'free':
                return 'Gratuito';
            case 'monthly':
                return 'Mensal';
            case 'semi-annual':
                return 'Semestral';
            case 'yearly':
                return 'Anual';
            case 'lifetime':
                return 'Vitalício';
            default:
                return 'Gratuito';
        }
    }
}
