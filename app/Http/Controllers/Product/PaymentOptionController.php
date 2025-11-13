<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\PaymentOption;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentOptionController extends Controller {
    
    public function store(Request $request, $productUuid) {

        $product = Product::where('uuid', $productUuid)->first();
        if (!$product) {
            return redirect()->back()->with('error', 'Produto indisponível!');
        }

        $option = new PaymentOption();
        $option->uuid               = Str::uuid();
        $option->product_id         = $product->id;
        $option->title              = $request->title;
        $option->description        = $request->description;
        $option->value              = $this->formatValue($request->value);
        $option->commission_seller  = $this->formatValue($request->commission_seller);
        $option->commission_parent  = $this->formatValue($request->commission_parent);
        $option->payment_splits     = $request->payment_splits;
        $option->payment_settings   = $request->payment_settings;
        if ($option->save()) {
            return redirect()->back()->with('success', 'Opção de pagamento criada com sucesso!');
        } 
        
        return redirect()->back()->with('error', 'Falha ao criar opção de pagamento, verifique os dados e tente novamente!'); 
    }

    public function destroy($uuid) {
        
        $option = PaymentOption::where('uuid', $uuid)->first();
        if ($option && $option->delete()) {
            return redirect()->back()->with('success', 'Opção de pagamento deletada com sucesso!');
        }
        
        return redirect()->back()->with('error', 'Falha ao deletar opção de pagamento, verifique os dados e tente novamente!');
    }

    private function formatValue($valor) {
        
        $valor = preg_replace('/[^0-9,]/', '', $valor);
        $valor = str_replace(',', '.', $valor);
        $valorFloat = floatval($valor);
    
        return number_format($valorFloat, 2, '.', '');
    }
}
