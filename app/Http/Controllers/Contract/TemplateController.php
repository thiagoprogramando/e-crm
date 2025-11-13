<?php

namespace App\Http\Controllers\Contract;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TemplateController extends Controller {
    
    public function index (Request $request) {

        $query = Template::query();

        if ($request->has('title')) {
            $query->where('title', 'like', "%{$request->input('title')}%");
        }

        if ($request->has('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        $products = Product::with('options')
            ->where(function($query) {
                $query->where('access', Auth::user()->type)
                    ->orWhereNull('access');
            })->where('status', 'active')->whereIn('type', ['service', 'product'])->get();

        return view('app.Contract.Template.index', [
            'templates' => $query->paginate(30),
            'products'  => $products,
        ]);
    }

    public function show ($uuid) {
        
        $template = Template::where('uuid', $uuid)->first();
        if (!$template) {
            return redirect()->back()->with('error', 'Template não encontrado!');
        }   

        $products = Product::with('options')
            ->where(function($query) {
                $query->where('access', Auth::user()->type)
                    ->orWhereNull('access');
            })->where('status', 'active')->whereIn('type', ['service', 'product'])->get();

        return view('app.Contract.Template.show', [
            'template'  => $template,
            'products'  => $products,
        ]);
    }

    public function store(Request $request) {
        
        $template               = new Template();
        $template->uuid         = Str::uuid();
        $template->user_id      = Auth::id();
        $template->product_id   = $request->product_id;
        $template->title        = $request->title;
        $template->content      = $request->content;
        $template->access       = $request->access;
        if ($template->save()) {
            return redirect()->back()->with('success', 'Template gerado com sucesso!');
        }
        
        return redirect()->back()->with('error', 'Falha ao criar o template, verifique os dados e tente novamente!');
    }

    public function update(Request $request, $uuid) {
        
        $template               = Template::where('uuid', $uuid)->first();
        if (!$template) {
            return redirect()->back()->with('error', 'Template não encontrado!');
        }   

        $template->product_id   = $request->product_id;
        $template->title        = $request->title;
        $template->content      = $request->content;
        $template->access       = $request->access;
        if ($template->save()) {
            return redirect()->back()->with('success', 'Template atualizado com sucesso!');
        }
        
        return redirect()->back()->with('error', 'Falha ao atualizar o template, verifique os dados e tente novamente!');
    }

    public function destroy($uuid) {
        
        $template = Template::where('uuid', $uuid)->first();
        if ($template && $template->delete()) {
            return redirect()->back()->with('success', 'Template deletado com sucesso!');
        }   
        
        return redirect()->back()->with('error', 'Falha ao deletar o template, tente novamente!');
    }
}
