<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\AssasController;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SubscriptionController extends Controller {
    
    public function index(Request $request) {

        return view('app.Product.Subscription.index');
    }

    public function store(Request $request) {

        $product = Product::where('uuid', $request->uuid)->where('type', 'subscription')->first();
        if (!$product) {
            return redirect()->back()->with('error', 'Plano de assinatura indisponível!'); 
        }

        switch (env('APP_BANK')) {
            case 'ASAAS':
                $assasController = new AssasController();

                $customer = $assasController->createdCustomer(Auth::user()->name, Auth::user()->cpfcnpj,  Auth::user()->phone, Auth::user()->email);
                if ($customer['status'] !== 'success') {
                    return redirect()->back()->with('infor', $customer['message']);
                }

                $splits = [
                    [
                        'walletId'        => env('APP_WALLET'),
                        'percentualValue' => 5,
                    ],
                    [
                        'walletId'        => env('APP_SOCIO'),
                        'percentualValue' => 5,
                    ],
                ];

                $payment = $assasController->createdCharge($customer['id'], 'PIX', 1, $product->value, $product->title, now()->addDays(2), $splits);
                if ($payment['status'] !== 'success') {
                    return redirect()->back()->with('infor', $customer['message']);
                }
                break;
            case 'CORA':
                
                break;
            default:
                return redirect()->back()->with('infor', 'Conexão bancária indisponível no momento, tente novamente mais tarde!');
                break;
        }

        $invoice                = new Invoice();
        $invoice->uuid          = Str::uuid();
        $invoice->user_id       = Auth::id();
        $invoice->product_id    = $product->id;
        $invoice->title         = $product->title;
        $invoice->description   = $product->title;
        $invoice->value         = $product->value;
        $invoice->due_date      = now()->addDays(2);
        $invoice->payment_token = $payment['id'];
        $invoice->payment_url   = $payment['invoiceUrl'];
        if ($invoice->save()) {
            return redirect($payment['invoiceUrl']);
        }

        return redirect()->back()->with('infor', 'Falha ao gerar assinatura, tente novamente mais tarde!');
    }
}
