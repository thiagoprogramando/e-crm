<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model {
    
    use SoftDeletes;

    protected $table = 'contracts';

    protected $fillable = [
        'uuid',
        'user_id',
        'sale_id',
        'template_id',
        'content',
        'signature',
        'signed_at',
        'signed_ip',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function sale() {
        return $this->belongsTo(Sale::class);
    }

    public function template() {
        return $this->belongsTo(Template::class);
    }

    public function statusLabel() {
        return $this->signed_at ? '<span class="badge bg-success me-1">Assinado</span>' : '<span class="badge bg-warning me-1">Pendente</span>';
    }
        
}
