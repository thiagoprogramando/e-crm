<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OfflineController extends Controller {
    
    public function createdCharge($customer, $billingType, $installments = null, $value, $description, $dueDate = null, $commissions = null) {
         return [
            'status'     => 'success',
            'id'         => Str::uuid()->toString(),
            'invoiceUrl' => env('APP_URL'),
        ];
    }

    public function sendWithdrawal(Withdraw $withdrawal) {
        return [
            'success'       =>'DONE',
            'status'        => 'DONE',
            'payment_token' => Str::uuid(),
            'payment_url'   => null,
        ];
    }
}
