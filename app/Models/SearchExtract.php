<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchExtract extends Model {
    
    protected $table = 'search_extracts';

    protected $fillable = [
        'uuid',
        'search_id',
        'user_id',
        'is_paid',
        'cpfcnpj',
        'status_code',
        'status_description',
        'request',
        'response',
    ];

    public function search () {
        return $this->belongsTo(Search::class, 'search_id', 'id');
    }

    public function user () {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function cpfcnpjLabel() {

        $cpfCnpj = preg_replace('/[^0-9]/', '', $this->cpfcnpj);
        if (strlen($cpfCnpj) === 11) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $cpfCnpj);
        } elseif (strlen($cpfCnpj) === 14) {
            return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $cpfCnpj);
        }

        return $cpfCnpj;
    }
}
