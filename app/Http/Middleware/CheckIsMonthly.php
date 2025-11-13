<?php

namespace App\Http\Middleware;

use App\Models\Invoice;
use App\Models\Sale;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIsMonthly {
    
    public function handle(Request $request, Closure $next): Response {

        if (!env('APP_MONTHLY')) {
            return $next($request);
        }

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->created_at->greaterThanOrEqualTo(now()->subDays(7))) {
            return $next($request);
        }

        if ($user->invoices->where('payment_status', 'PENDING')->where('due_date', '>=', now())->count() > 0) {
            return redirect()->route('app')->with('infor', 'Você possui uma fatura pendente, por favor realize o pagamento para continuar usando os serviços!');
        }

        if ($user->hasActiveSubscription()) {
            return $next($request);
        }

        return redirect()->route('subscriptions')->with('infor', 'Você precisa de uma assinatura ativa para continuar usando os serviços!');
    }
}
