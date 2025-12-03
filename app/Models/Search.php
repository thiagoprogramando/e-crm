<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Search extends Model {
    
    protected $table = 'searchs';

    protected $fillable = [
        'uuid',
        'title',
        'content',
        'value',
        'addition',
        'api_method',
        'api_url',
        'api_token',
        'api_code',
        'api_version',
        'status'
    ];

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
}
