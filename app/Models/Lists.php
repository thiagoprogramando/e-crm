<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lists extends Model {

    use SoftDeletes;
    
    protected $table = 'lists';

    protected $fillable = [
        'title',
        'description',
        'status',
        'status_serasa',
        'status_boa_vista',
        'status_spc',
        'status_ceprot',
        'status_bacen',
        'status_rating',
        'status_score',
        'date_start',
        'date_end',
    ];

    public function statusLabel() {
        return $this->status === 'active' ? 'ATIVA' : 'INATIVA';
    }

    public function serasaLabel() {
        return $this->status_serasa === 'completed' ? 'CONCLUÍDO' : 'PENDENTE';
    }

    public function boaVistaLabel() {
        return $this->status_boa_vista === 'completed' ? 'CONCLUÍDO' : 'PENDENTE';
    }

    public function spcLabel() {
        return $this->status_spc === 'completed' ? 'CONCLUÍDO' : 'PENDENTE';
    }

    public function ceprotLabel() {
        return $this->status_ceprot === 'completed' ? 'CONCLUÍDO' : 'PENDENTE';
    }

    public function bacenLabel() {
        return $this->status_bacen === 'completed' ? 'CONCLUÍDO' : 'PENDENTE';
    }

    public function ratingLabel() {
        return $this->status_rating === 'completed' ? 'CONCLUÍDO' : 'PENDENTE';
    }

    public function scoreLabel() {
        return $this->status_score === 'completed' ? 'CONCLUÍDO' : 'PENDENTE';
    }
}
