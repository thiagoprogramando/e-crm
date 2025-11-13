<?php

namespace App\Http\Controllers\Contract;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ContractController extends Controller {

    public function index(Request $request) {
        
        $query = Contract::query();

        if ($request->has('sale_id')) {
            $query->where('sale_id', $request->input('sale_id'));
        }
        if ($request->has('template_id')) {
            $query->where('template_id', $request->input('template_id'));
        }
        if (Auth::user()->type !== 'admin') {
            $query->where('user_id', Auth::id());
        } else {
            $affiliatedIds = Auth::user()->getDescendantIds();
            $query->whereIn('user_id', array_merge([Auth::id()], $affiliatedIds));
        }

        $templates = Template::where(function($query) {
                $query->where('access', Auth::user()->type)
                    ->orWhereNull('access');
            })->get();

        $sales = Sale::whereIn('user_id', function($query) {
                $affiliatedIds = Auth::user()->getDescendantIds();
                $query->select('id')
                    ->from('users')
                    ->whereIn('id', array_merge([Auth::id()], $affiliatedIds));
            })->get();

        return view('app.Contract.index', [
            'contracts' => $query->orderBy('created_at', 'desc')->paginate(30),
            'templates' => $templates,
            'sales'     => $sales,
        ]);
    }

    public function show($uuid) {

        $contract = Contract::where('uuid', $uuid)->first();
        if (!$contract) {
            return redirect()->back()->with('error', 'Contrato não encontrado!');
        }

        if ($contract->signed_at) {
            $content = $contract->content;
        } else {
            $sale     = $contract->sale;
            $product  = $sale->product;
            $company  = $contract->user;
            $content  = $contract->template->content;

            $variables = [
                '{{ CUSTOMER_NAME }}'    => $sale->customer_name,
                '{{ CUSTOMER_CPFCNPJ }}' => $sale->cpfcnpjLabel(),
                '{{ CUSTOMER_EMAIL }}'   => $sale->customer_email ?? 'N/A',
                '{{ CUSTOMER_PHONE }}'   => $sale->phoneLabel() ?? 'N/A',

                '{{ COMPANY_NAME }}'     => $company->name,
                '{{ COMPANY_CPFCNPJ }}'  => $company->cpfcnpjLabel(),
                '{{ COMPANY_EMAIL }}'    => $company->email ?? 'N/A',
                '{{ COMPANY_PHONE }}'    => $company->phoneLabel() ?? 'N/A',
                '{{ COMPANY_ADDRESS }}'  => $company->address ?? 'N/A',

                '{{ PRODUCT_TITLE }}'    => $product->title,
                '{{ PRODUCT_VALUE }}'    => number_format($sale->value, 2, ',', '.')
            ];

            $content = str_replace(array_keys($variables), array_values($variables), $content);
        }

        return view('app.Contract.show', [
            'contract'          => $contract,
            'content'           => $content,
            'signatureRequired' => empty($contract->signature) ? true : false,
        ]);
    }

    public function store (Request $request) {

        $template = Template::where('uuid', $request->template_id)->first();
        if (!$template) {
            return redirect()->back()->with('error', 'Template indisponível!');
        }

        $sale = Sale::where('uuid', $request->sale_id)->first();
        if (!$sale) {
            return redirect()->back()->with('error', 'Venda indisponível!');
        }

        $contract = new Contract();
        $contract->uuid         = Str::uuid();
        $contract->user_id      = Auth::id();
        $contract->sale_id      = $sale->id;
        $contract->template_id  = $template->id;
        if ($contract->save()) {
            return redirect()->back()->with('success', 'Contrato gerado com sucesso!');
        } 

        return redirect()->back()->with('error', 'Falha ao gerar o contrato, verifique os dados e tente novamente!');
    }

    public function update (Request $request, $uuid) {

        $contract = Contract::where('uuid', $uuid)->first();
        if (!$contract) {
            return response()->json(['error' => 'Contrato indisponível!'], 404);
        }

        $contract->content    = $request->content;
        $contract->signature  = $request->signature;
        $contract->signed_at  = now();
        $contract->signed_ip  = $request->ip();
        if ($contract->save()) {
            return response()->json(['success' => 'Contrato atualizado com sucesso!']);
        } 
        
        return response()->json(['error' => 'Falha ao atualizar o contrato, verifique os dados e tente novamente!'], 500);
    }
}
