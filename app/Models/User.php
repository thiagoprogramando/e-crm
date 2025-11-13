<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {

    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'uuid',
        'parent_id',
        'photo',
        'name',
        'cpfcnpj',
        'email',
        'password',
        'phone',
        'address',
        'wallet',
        'status',
        'type',
    ];

    public function parent() {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(User::class, 'parent_id')->whereColumn('parent_id', '!=', 'id');
    }

    public function invoices () {
        return $this->hasMany(Invoice::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getDescendantIds() {
        $ids = [];

        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getDescendantIds());
        }

        return $ids;
    }

    public function typeLabel() {
        $labels = [
            'admin'         => 'Administrador',
            'collaborator'  => 'Colaborador',
            'user'          => 'Consultor',
        ];

        return $labels[$this->type] ?? '---';
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

    public function maskName() {
        
        if (empty($this->name)) {
            return '';
        }

        $nameParts = explode(' ', trim($this->name));

        if (count($nameParts) === 1) {
            return $nameParts[0];
        }

        return $nameParts[0] . ' ' . $nameParts[1];
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

    public function phoneLabel() {
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        if (strlen($phone) === 10) {
            return preg_replace("/(\d{2})(\d{4})(\d{4})/", "($1) $2-$3", $phone);
        } elseif (strlen($phone) === 11) {
            return preg_replace("/(\d{2})(\d{5})(\d{4})/", "($1) $2-$3", $phone);
        }

        return $phone;
    }

    public function hasActiveSubscription() {
       
        $invoice = $this->invoices()
            ->where('payment_status', 'PAID')
            ->whereHas('product', function ($query) {
                $query->where('type', 'subscription');
            })
            ->latest('payment_date')
            ->first();

        if (!$invoice) {
            return false;
        }

        $timeMap = [
            'monthly'        => 30,
            'semi-annually'  => 182,
            'yearly'         => 365,
            'lifetime'       => true,
        ];

        $timeKey = $invoice->product->time;
        if (!isset($timeMap[$timeKey])) {
            return false;
        }

        $days = $timeMap[$timeKey];
        if ($timeKey === 'lifetime') {
            return true;
        }

        $expiresAt = Carbon::parse($invoice->payment_date)->addDays($days);
        return now()->lessThanOrEqualTo($expiresAt);
    }

}
