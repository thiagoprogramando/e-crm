<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\AssasController;
use App\Models\Commission;
use App\Models\Invoice;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WalletController extends Controller {
    
    public function index(Request $request) {

        $start = $request->payment_date_start;
        $end   = $request->payment_date_end;

        $withdrawalsQuery = Withdraw::where('user_id', Auth::id());
        if ($start) {
            $withdrawalsQuery->whereDate('created_at', '>=', $start);
        }
        if ($end) {
            $withdrawalsQuery->whereDate('created_at', '<=', $end);
        }

        $commissionsQuery = Commission::where('user_id', Auth::id());
        if ($start) {
            $commissionsQuery->whereDate('created_at', '>=', $start);
        }
        if ($end) {
            $commissionsQuery->whereDate('created_at', '<=', $end);
        }

        return view('app.Finance.Wallet.index', [
            'withdrawals' =>$withdrawalsQuery->orderBy('created_at', 'desc')->paginate(40),
            'commissions' => $commissionsQuery->orderBy('created_at', 'desc')->paginate(20),
        ]);
    }

    public function store(Request $request) {

        switch (env('APP_BANK')) {
            case 'ASAAS':
                $assasController = new AssasController();

                $customer = $assasController->createdCustomer(Auth::user()->name, Auth::user()->cpfcnpj,  Auth::user()->phone, Auth::user()->email);
                if ($customer['status'] !== 'success') {
                    return redirect()->back()->with('infor', $customer['message']);
                }

                $payment = $assasController->createdCharge($customer['id'], 'PIX', 1, $this->formatValue($request->value), 'Depósito na carteira', now()->addDays(2));
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
        
        $deposit                 = new Invoice();
        $deposit->uuid           = Str::uuid();
        $deposit->user_id        = Auth::id();
        $deposit->title          = 'Depósito na carteira';
        $deposit->description    = 'Depósito na carteira';
        $deposit->value          = $this->formatValue($request->value);
        $deposit->due_date       = now()->addDays(2);
        $deposit->payment_status = 'PENDING';
        $deposit->payment_type   = 'DEPOSIT';
        $deposit->payment_token = $payment['id'];
        $deposit->payment_url   = $payment['invoiceUrl'];
        if ($deposit->save()) {
            return redirect($payment['invoiceUrl']);
        }

        return redirect()->back()->with('infor', 'Falha ao gerar assinatura, tente novamente mais tarde!');
    }

    private function formatValue($valor) {
        
        $valor = preg_replace('/[^0-9,]/', '', $valor);
        $valor = str_replace(',', '.', $valor);
        $valorFloat = floatval($valor);
    
        return number_format($valorFloat, 2, '.', '');
    }
}
