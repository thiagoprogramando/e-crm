<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIsAdmin {
    
    public function handle(Request $request, Closure $next): Response {

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Faça login para continuar!');
        }

        if ($user->type == 'admin') {
            return $next($request);
        }

        return redirect()->route('app')->with('infor', 'Acesso negado!');
    }
}
