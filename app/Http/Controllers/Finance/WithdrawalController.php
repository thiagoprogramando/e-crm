<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\AssasController;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WithdrawalController extends Controller {

    public function index(Request $request) {

        $baseQuery = Withdraw::query();

        if ($request->filled('uuid')) {
            $baseQuery->where('uuid', $request->uuid);
        }
        if ($request->filled('payment_key')) {
            $baseQuery->where('payment_key', $request->payment_key);
        }
        if ($request->filled('user_id')) {
            $baseQuery->where('user_id', $request->user_id);
        }

        $pendingsQuery = (clone $baseQuery)->where('is_paid', false);
        if ($request->filled('date_start')) {
            $pendingsQuery->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $pendingsQuery->whereDate('created_at', '<=', $request->date_end);
        }

        $approvedsQuery = (clone $baseQuery)->where('is_paid', true);
        if ($request->filled('date_start')) {
            $approvedsQuery->where(function ($q) use ($request) {
                $q->whereDate('confirmed_at', '>=', $request->date_start)
                ->orWhereDate('created_at', '>=', $request->date_start);
            });
        }

        if ($request->filled('date_end')) {
            $approvedsQuery->where(function ($q) use ($request) {
                $q->whereDate('confirmed_at', '<=', $request->date_end)
                ->orWhereDate('created_at', '<=', $request->date_end);
            });
        }

        return view('app.Finance.Withdraw.index', [
            'pendings'  => $pendingsQuery->orderBy('created_at', 'desc')->paginate(60),
            'approveds' => $approvedsQuery->orderBy('created_at', 'desc')->paginate(30),
        ]);
    }
    
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

    public function sendWithdrawal(Request $request) {

        if (!Hash::check($request->password, Auth::user()->password)) {
            return redirect()->back()->with('infor', 'Senha incorreta!');
        }

        if (empty($request->requests) || !is_array($request->requests)) {
            return redirect()->back()->with('infor', 'Selecione ao menos uma solicitação!');
        }

        $uuids = $request->requests;
        if (in_array('ALL', $uuids)) {
            $withdrawals = Withdraw::where('is_paid', false)->get();
        } else {
            $withdrawals = Withdraw::whereIn('uuid', $uuids)->where('is_paid', false)->get();
        }

        if ($withdrawals->isEmpty()) {
            return redirect()->back()->with('infor', 'Nenhuma solicitação encontrada para processar.');
        }

        $results = [
            'success' => [],
            'errors'  => []
        ];

        switch (env('APP_BANK')) {
            case 'ASAAS':
                $gatewayController = new AssasController();
                foreach ($withdrawals as $withdrawal) {
                    $response = $gatewayController->sendWithdrawal($withdrawal);
                    if ($response['status'] === 'DONE' || $response['status'] === 'BANK_PROCESSING' || $response['status'] === 'PENDING') {

                        $withdrawal->is_paid = true;
                        $withdrawal->payment_token  = $response['payment_token'] ?? null;
                        $withdrawal->payment_url    = $response['payment_url'] ?? null;
                        if ($response['success'] === 'DONE') {
                            $withdrawal->confirmed_at = now();
                        }
                        $withdrawal->save();

                        $results['success'][] = $withdrawal->uuid;
                    } else {
                        $withdrawal->payment_log = $response['error'] ?? 'Erro desconhecido no processamento!';
                        $withdrawal->save();
                        $results['errors'][] = [
                            'uuid' => $withdrawal->uuid,
                            'msg'  => $response['message'] ?? 'Erro desconhecido no processamento.'
                        ];
                    }
                }
                break;
            case 'CORA':
                
                break;
            default:
                return redirect()->back()->with('infor', 'Conexão bancária indisponível no momento, tente novamente mais tarde!');
                break;
        }

        $successCount = count($results['success']);
        $errorCount   = count($results['errors']);
        $msg          = "{$successCount} solicitação" . ($successCount != 1 ? 'es' : '') . " processada" . ($successCount != 1 ? 's' : '') . " com sucesso!";

        if ($errorCount > 0) {
            $msg .= " {$errorCount} falharam.";
        }

        return redirect()->back()->with([
            'infor' => $msg,
            'withdraw_results' => $results
        ]);
    }

    public function destroy(Request $request, $uuid) {

        if (!Hash::check($request->password, Auth::user()->password)) {
            return redirect()->back()->with('infor', 'Senha incorreta!');
        }

        if ( Auth::user()->type !== 'admin' ) {
            return redirect()->back()->with('infor', 'Para cancelar ou alterar uma solicitação, fale com o suporte!');
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
