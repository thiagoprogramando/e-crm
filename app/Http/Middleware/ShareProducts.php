<?php

namespace App\Http\Middleware;

use App\Models\Product;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareProducts {
    
    public function handle(Request $request, Closure $next): Response {

        if (Auth::check()) {
            $products = Product::with('options')
                ->where(function($query) {
                    $query->where('access', Auth::user()->type)
                        ->orWhereNull('access');
                })->where('status', 'active')->whereIn('type', ['service', 'product'])->get();

            View::share([
                'products' => $products,
            ]);
        }

        return $next($request);
    }
}
