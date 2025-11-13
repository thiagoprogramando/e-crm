<?php

namespace App\Http\Middleware;

use App\Models\Product;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class CheckIsSubscription {
    
    public function handle(Request $request, Closure $next): Response {

        if (Auth::check()) {
            $subscriptions = Product::with('options')
            ->where(function($query) {
                $query->where('access', Auth::user()->type)
                    ->orWhereNull('access');
            })->where('status', 'active')->where('type', ['subscription'])->get();

            View::share([
                'subscriptions' => $subscriptions,
            ]);
        }

        return $next($request);
    }
}
