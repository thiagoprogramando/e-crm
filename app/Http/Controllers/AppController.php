<?php

namespace App\Http\Controllers;

use App\Models\Lists;
use App\Models\Post;
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

        $posts = Auth::user()->type == 'admin' 
                ? Post::orderBy('is_fixed', 'desc')->get()
                : Post::where(function($query) {
                        $query->where('access', Auth::user()->type)
                            ->orWhereNull('access');
                    })
                    ->where(function($query) {
                        $query->whereNull('user_id')
                            ->orWhere('user_id', Auth::user()->parent_id);
                    })
                    ->orderBy('is_fixed', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('app.app', [
            'products'  => $products,
            'list'      => $list,
            'sales'     => $sales,
            'posts'     => $posts
        ]);
    }
}
