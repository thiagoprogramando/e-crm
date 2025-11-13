<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller {
    
    public function index (Request $request, $type = null) {
        
        $query = Product::query();

        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($type) {
            $query->where('type', $type);
        } else {
            $query->where('type', '!=', 'subscription');
        }

        return view('app.Product.index', [
            'products' => $query->orderBy('title', 'desc')->paginate(30),
            'type'     => $type,
        ]);
    }

    public function store (Request $request) {

        $request->validate([
            'title'         => 'required|string|max:191',
            'value'         => 'required',
            'status'        => 'required|in:active,inactive',
        ], [
            'title.required'    => 'O campo Título é obrigatório!',
            'value.required'    => 'O campo Valor Padrão é obrigatório!',
            'status.required'   => 'O campo Status é obrigatório!',
            'status.in'         => 'O campo Status possui um valor inválido!',
        ]);
        
        $product                = new Product();
        $product->uuid          = Str::uuid();
        $product->title         = $request->title;
        $product->description   = $request->description;
        $product->value         =  $this->formatValue($request->value);
        $product->max_value     = $this->formatValue($request->max_value);
        $product->min_value     = $this->formatValue($request->min_value);
        $product->cost_value    = $this->formatValue($request->cost_value);
        $product->fees_value    = $this->formatValue($request->fees_value);
        $product->cashback_value        = $this->formatValue($request->cashback_value);
        $product->cashback_percentage   = $this->formatPercent($request->cashback_percentage);
        $product->status                = $request->status;
        $product->type                  = $request->type;
        $product->time                  = $request->time;
        $product->access                = $request->access;
        if ($product->save()) {
            return redirect()->back()->with('success', 'Produto cadastrado com sucesso!');
        }   

        return redirect()->back()->with('error', 'Erro ao cadastrar o produto, verifique os dados e tente novamente!');
    }

    public function update (Request $request, $uuid) {

        $product = Product::where('uuid', $uuid)->first();
        if (!$product) {
            return redirect()->back()->with('error', 'Produto não encontrado!');
        }

        if ($request->has('title')) {
            $product->title = $request->title;
        }
        if ($request->has('description')) {
            $product->description = $request->description;
        }
        if ($request->has('value')) {
            $product->value = $this->formatValue($request->value);
        }
        if ($request->has('max_value')) {
            $product->max_value = $this->formatValue($request->max_value);
        }
        if ($request->has('min_value')) {
            $product->min_value = $this->formatValue($request->min_value);
        }
        if ($request->has('cost_value')) {
            $product->cost_value = $this->formatValue($request->cost_value);
        }
        if ($request->has('fees_value')) {
            $product->fees_value = $this->formatValue($request->fees_value);
        }
        if ($request->has('cashback_value')) {
            $product->cashback_value = $this->formatValue($request->cashback_value);
        }
        if ($request->has('cashback_percentage')) {
            $product->cashback_percentage = $this->formatPercent($request->cashback_percentage);
        }
        if ($request->has('status')) {
            $product->status = $request->status;
        }
        if ($request->has('access')) {
            $product->access = $request->access;
        }
        if ($product->save()) {
            return redirect()->back()->with('success', 'Produto atualizado com sucesso!');
        }   

        return redirect()->back()->with('error', 'Erro ao atualizar o produto, verifique os dados e tente novamente!');

    }

    public function destroy ($uuid) {

        $product = Product::where('uuid', $uuid)->first();
        if ($product && $product->delete()) {
            return redirect()->back()->with('success', 'Produto deletado com sucesso!');
        }

        return redirect()->back()->with('error', 'Erro ao deletar o produto, tente novamente!');
    }

    private function formatValue($valor) {
        
        $valor = preg_replace('/[^0-9,]/', '', $valor);
        $valor = str_replace(',', '.', $valor);
        $valorFloat = floatval($valor);
    
        return number_format($valorFloat, 2, '.', '');
    }

    private function formatPercent($percent) {
        $percent        = preg_replace('/[^0-9,]/', '', $percent);
        $percent        = str_replace(',', '.', $percent);
        $percentFloat   = floatval($percent);
        $percentFloat   = max(0, min(100, $percentFloat));
        return number_format($percentFloat, 2, '.', '');
    }
}
