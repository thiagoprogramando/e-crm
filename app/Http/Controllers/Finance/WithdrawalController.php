<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WithdrawalController extends Controller {
    
    public function store(Request $request) {

        if ($this->formatValue($request->value) <= 0) {
            return redirect()->back()->with('infor', 'O valor do saque deve ser maior que R$ 0,00!');
        }

        if (Auth::user()->wallet < $this->formatValue($request->value)) {
            return redirect()->back()->with('infor', 'Saldo insuficiente para realizar o saque!');
        }
        
        $withdraw                   = new Withdraw();
        $withdraw->uuid             = Str::uuid();
        $withdraw->user_id          = Auth::id();
        $withdraw->payment_name     = $request->payment_name;
        $withdraw->payment_document = preg_replace('/[^0-9]/', '', $request->payment_document);
        $withdraw->description      = 'Solicitação de saque';
        $withdraw->value            = $this->formatValue($request->value);
        $withdraw->payment_key      = $request->payment_key;
        if ($withdraw->save()) {
            Auth::user()->decrement('wallet', $this->formatValue($request->value));
            return redirect()->back()->with('success', 'Saque solicitado com sucesso!');
        }

        return redirect()->back()->with('error', 'Falha ao solicitar saque, verifique os dados e tente novamente!');
    }

    public function destroy(Request $request, $uuid) {

        if (!Hash::check($request->password, Auth::user()->password)) {
            return redirect()->back()->with('infor', 'Senha incorreta!');
        }

        $withdraw = Withdraw::where('uuid', $uuid)->where('user_id', Auth::id())->where('is_paid', false)->first();
        if (!$withdraw) {
            return redirect()->back()->with('infor', 'Saque não encontrado ou já processado!');
        }

        if ($withdraw->delete()) {
            Auth::user()->increment('wallet', $withdraw->value);
            return redirect()->back()->with('success', 'Saque cancelado com sucesso!');
        }

        return redirect()->back()->with('error', 'Falha ao cancelar saque, tente novamente!');
    }

    private function formatValue($valor) {
        
        $valor = preg_replace('/[^0-9,]/', '', $valor);
        $valor = str_replace(',', '.', $valor);
        $valorFloat = floatval($valor);
    
        return number_format($valorFloat, 2, '.', '');
    }
}
