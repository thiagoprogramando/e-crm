<?php

namespace App\Http\Controllers;

use App\Models\Lists;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppController extends Controller {
    
    public function index() {

        $products = Product::with('options')
            ->where(function($query) {
                $query->where('access', Auth::user()->type)
                    ->orWhereNull('access');
            })
            ->where('status', 'active')
            ->whereIn('type', ['service', 'product'])->get();

        $list = Lists::where('status', 'active')->first();

        $sales = Sale::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(30);

        return view('app.app', [
            'products'  => $products,
            'list'      => $list,
            'sales'     => $sales
        ]);
    }
}
