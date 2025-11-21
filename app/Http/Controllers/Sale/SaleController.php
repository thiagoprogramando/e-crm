<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\AssasController;
use App\Http\Controllers\Gateway\CoraController;
use App\Http\Controllers\Gateway\OfflineController;
use App\Models\Commission;
use App\Models\Lists;
use App\Models\PaymentOption;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SaleController extends Controller {
    
    public function index(Request $request) {
        
        $query = Sale::query();

        if ($request->filled(('name'))) {
            $query->where('customer_name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled(('customer_cpfcnpj'))) {
            $query->where('customer_cpfcnpj', 'like', '%'.preg_replace('/\D/', '', $request->cpfcnpj).'%');
        }

        if ($request->filled(('product_id'))) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled(('list_id'))) {
            $query->where('list_id', $request->list_id);
        }

        if ($request->filled(('payment_status'))) {
            $query->where('payment_status', $request->payment_status);
        }

        if (Auth::user()->type !== 'admin') {
            $query->where('user_id', Auth::id());
        } else {
            $affiliatedIds = Auth::user()->getDescendantIds();
            $query->whereIn('user_id', array_merge([Auth::id()], $affiliatedIds));
        }

        $products = Product::with('options')
            ->where(function($query) {
                $query->where('access', Auth::user()->type)
                    ->orWhereNull('access');
            })->where('status', 'active')->whereIn('type', ['service', 'product'])->get();

        return view('app.Sale.index', [
            'sales'     => $query->orderBy('created_at', 'desc')->paginate(20),
            'lists'     => Lists::orderBy('created_at', 'desc')->get(),
            'products'  => $products
        ]);
    }

    public function store(Request $request) {
        
        $product = Product::where('uuid', $request->product_id)->first();
        if (!$product) {
            return redirect()->back()->with('infor', 'Produto não inválido ou indisponível!');
        }

        $option = PaymentOption::where('uuid', $request->product_option_id)->first();
        if (!$option) {
            return redirect()->back()->with('infor', 'Opção de pagamento inválida ou indisponível!');
        }

        $uuid = Str::uuid();

        if ($option->commission_seller > 0) {
            $commission = new Commission();
            $commission->uuid           = Str::uuid();
            $commission->user_id        = Auth::user()->id;
            $commission->product_id     = $product->id;
            $commission->payment_token  = $uuid;
            $commission->value          = $option->commission_seller;
            $commission->description    = 'Comissão de Vendedor para venda Cliente:'. $request->name;
            $commission->save();
        }

         if (($option->commission_parent > 0) && Auth::user()->parent_id) {
            $commission = new Commission();
            $commission->uuid           = Str::uuid();
            $commission->user_id        = Auth::user()->id;
            $commission->product_id     = $product->id;
            $commission->payment_token  = $uuid;
            $commission->value          = $option->commission_parent;
            $commission->description    = 'Comissão de Patrocinador para venda Cliente:'. $request->name;
            $commission->save();
        }

        switch (env('APP_BANK')) {
            case 'ASAAS':
                $assasController = new AssasController();

                $customer = $assasController->createdCustomer($request->name, preg_replace('/\D/', '', $request->cpfcnpj),  preg_replace('/\D/', '', $request->phone), $request->email);
                if ($customer['status'] !== 'success') {
                    return redirect()->back()->with('infor', $customer['message']);
                }

                $payment = $assasController->createdCharge($customer['id'], $option->payment_method, $option->payment_installments, ($option->value + $product->fees_value), $product->title, now()->addDays(2), $option->payment_splits);
                if ($payment['status'] !== 'success') {
                    return redirect()->back()->with('infor', $customer['message']);
                }

                $qrCode = false;
                break;
            case 'CORA':
                $coraController = new CoraController();
                
                $payment = $coraController->createdCharge(Auth::user(), ($option->value + $product->fees_value), $product->title, null, $option->payment_splits);
                if ($payment['status'] !== 'success') {
                    return redirect()->back()->with('infor', $payment['message']);
                }

                $qrCode = true;
                break;
            case 'OFFLINE':
                $offlineController = new OfflineController();

                $payment = $offlineController->createdCharge(Auth::user(), ($option->value + $product->fees_value), $product->title, null, $option->payment_splits);
                if ($payment['status'] !== 'success') {
                    return redirect()->back()->with('infor', $payment['message']);
                }

                $qrCode = false;
                break;
            default:
                return redirect()->back()->with('infor', 'Conexão bancária indisponível no momento, tente novamente mais tarde!');
                break;
        }

        $sale = new Sale();
        $sale->uuid                 = $uuid;
        $sale->user_id              = Auth::user()->id;
        $sale->product_id           = $product->id;
        $sale->payment_option_id    = $option->id;
        $sale->customer_name        = $request->name;
        $sale->customer_cpfcnpj     = preg_replace('/\D/', '', $request->cpfcnpj);
        $sale->customer_email       = $request->email;
        $sale->customer_phone       = preg_replace('/\D/', '', $request->phone);
        $sale->value                = $option->value + $product->fees_value;
        $sale->payment_token        = $payment['id'];
        $sale->payment_url          = $payment['invoiceUrl'];
        $sale->payment_due_date     = now()->addDays(2);
        $sale->payment_status       = 'PENDING';
        if ($sale->save()) {
            
            if (!$qrCode) {
                return redirect()->back()->with([
                    'invoiceUrl'  => $payment['invoiceUrl'],
                ]);
            }
            
            $qrSvg = QrCode::format('svg')->size(300)->generate($payment['qrCode']);
            return redirect()->back()->with([
                'qrCodeImg'   => 'data:image/svg+xml;base64,' . base64_encode($qrSvg),
                'qrCode'      => $payment['qrCode'],
                'invoiceUrl'  => $payment['invoiceUrl'],
            ]);
        }

        return redirect()->back()->with('infor', 'Falha ao gerar nova venda, verifique os dados e tente novamente!');
    }

    public function update(Request $request, $uuid) {
        
        $sale = Sale::where('uuid', $uuid)->first();
        if (!$sale) {
            return redirect()->back()->with('infor', 'Venda não encontrada, verifique os dados e tente novamente!');
        }

        if (Auth::user()->type !== 'admin' && $sale->payment_status === 'PAID') {
            return redirect()->back()->with('infor', 'Não é posssível editar esta venda!');
        }

        if ($request->filled(('customer_name'))) {
            $sale->customer_name = $request->customer_name;
        }
        if ($request->filled(('customer_cpfcnpj'))) {
            $sale->customer_cpfcnpj = preg_replace('/\D/', '', $request->customer_cpfcnpj);
        }
        if ($request->filled(('customer_email'))) {
            $sale->customer_email = $request->customer_email;
        }
        if ($request->filled(('customer_phone'))) {
            $sale->customer_phone = preg_replace('/\D/', '', $request->customer_phone);
        }
        if ($request->filled(('list_id'))) {
            $sale->list_id = $request->list_id;
        }
        if ($request->filled(('payment_status'))) {
            $sale->payment_status = $request->payment_status;
        }

        if ($sale->save()) {
            return redirect()->back()->with('success', 'Venda atualizada com sucesso!');
        }

        return redirect()->back()->with('infor', 'Falha ao atualizar venda, verifique os dados e tente novamente!');
    }

    public function destroy(Request $request, $uuid) {
        
        $sale = Sale::where('uuid', $uuid)->first();
        if (!$sale) {
            return redirect()->back()->with('infor', 'Venda não encontrada, verifique os dados e tente novamente!');
        }

        if (Auth::user()->type !== 'admin' || $sale->payment_status === 'PAID') {
            return redirect()->back()->with('infor', 'Não é posssível deletar esta venda!');
        }

        if ($sale->delete()) {
            return redirect()->back()->with('success', 'Venda deletada com sucesso!');
        }

        return redirect()->back()->with('infor', 'Falha ao deletar venda, verifique os dados e tente novamente!');
    }
}
