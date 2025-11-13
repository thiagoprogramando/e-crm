<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Lists;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ListController extends Controller {
    
    public function index(Request $request) {

        $query = Lists::query();

        if ($request->filled('title')) {
            $title = $request->input('title');
            $query->where('name', 'like', '%' . $title . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_start')) {
            $query->whereDate('date_start', '>=', $request->date_start);
        }

        if ($request->filled('date_end')) {
            $query->whereDate('date_end', '<=', $request->date_end);
        }

        $products = Product::with('options')
            ->where(function($query) {
                $query->where('access', Auth::user()->type)
                    ->orWhereNull('access');
            })->where('status', 'active')->whereIn('type', ['service', 'product'])->get();

        return view('app.List.index', [
            'lists'     => $query->orderBy('created_at', 'desc')->paginate(30),
            'products'  => $products,
        ]);
    }

    public function store (Request $request) {

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_start'  => 'required|date',
            'date_end'    => 'required|date|after_or_equal:date_start',
            'status'      => 'required|in:active,inactive'
        ], [
            'title.required'      => 'O campo título é obrigatório!',
            'title.string'        => 'O campo título deve ser uma string!',
            'title.max'           => 'O campo título não pode exceder 255 caracteres!',
            'description.string'  => 'O campo descrição deve ser uma string!',
            'date_start.required' => 'O campo data de início é obrigatório!',
            'date_start.date'     => 'O campo data de início deve ser uma data válida!',
            'date_end.required'   => 'O campo data de término é obrigatório!',
            'date_end.date'       => 'O campo data de término deve ser uma data válida!',
            'date_end.after_or_equal' => 'A data de término deve ser igual ou posterior à data de início!',
            'status.required'     => 'O campo status é obrigatório!',
            'status.in'           => 'O campo status deve ser ativo ou inativo!'
        ]);

       
        if ($request->status === 'active') {
            Lists::where('status', 'active')->update(['status' => 'inactive']);
        }

        $list               = new Lists();
        $list->uuid         = Str::uuid();
        $list->title        = $request->title;
        $list->description  = $request->description;
        $list->date_start   = $request->date_start;
        $list->date_end     = $request->date_end;
        if ($list->save()) {
            return redirect()->back()->with('success', 'Lista cadastrada com sucesso!');
        }

        return redirect()->back()->with('error', 'Falha ao criar a lista, verifique os dados e tente novamente!');
    }

    public function update (Request $request, $uuid) {

        $list = Lists::where('uuid', $uuid)->first();
        if (!$list) {
            return redirect()->back()->with('error', 'Lista não encontrada!');
        }

        if ($request->status === 'active') {
            Lists::where('status', 'active')->where('uuid', '!=', $uuid)->update(['status' => 'inactive']);
        }

        if (!empty($request->title)) {
            $list->title = $request->title;
        }
        if (!empty($request->description)) {
            $list->description = $request->description;
        }
        if (!empty($request->date_start)) {
            $list->date_start = $request->date_start;
        }
        if (!empty($request->date_end)) {
            $list->date_end = $request->date_end;
        }
        if (!empty($request->status)) {
            $list->status = $request->status;
        }
        if (!empty($request->status_serasa)) {
            $list->status_serasa = $request->status_serasa;
        }
        if (!empty($request->status_spc)) {
            $list->status_spc = $request->status_spc;
        }
        if (!empty($request->status_ceprot)) {
            $list->status_ceprot = $request->status_ceprot;
        }
        if (!empty($request->status_boa_vista)) {
            $list->status_boa_vista = $request->status_boa_vista;
        }
        if (!empty($request->status_bacen)) {
            $list->status_bacen = $request->status_bacen;
        }
        if (!empty($request->status_rating)) {
            $list->status_rating = $request->status_rating;
        }
        if (!empty($request->status_score)) {
            $list->status_score = $request->status_score;
        }

        if ($list->save()) {
            return redirect()->back()->with('success', 'Dados atualizados com sucesso!');
        } 

        return redirect()->back()->with('error', 'Erro ao atualizar dados, tente novamente!');
    }

    public function destroy($uuid) {

        $list = Lists::where('uuid', $uuid)->first();
        if ($list && $list->delete()) {
            return redirect()->back()->with('success', 'Lista excluída com sucesso!');
        }

        return redirect()->back()->with('error', 'Falha ao excluir lista, verifique os dados e tente novamente!');
    }

    public function export (Request $request, $uuid) {
        
        $list = Lists::where('uuid', $uuid)->first();
        if (!$list) {
            return redirect()->back()->with('error', 'Lista não encontrada!');
        }

        $query = Sale::where('list_id', $list->id);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }

        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        if (Auth::user()->type !== 'admin') {
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        } else {
            $affiliatedIds = Auth::user()->getDescendantIds();
            $query->whereIn('user_id', array_merge([Auth::id()], $affiliatedIds));
        }

        $sales          = $query->orderBy('created_at', 'desc')->get();
        $spreadsheet    = new Spreadsheet();
        $sheet          = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['Produto', 'Cliente', 'CPF/CNPJ', 'Email', 'Telefone', 'Status de Pagamento', 'Criado em', 'Confirmado em']
        ]);

        foreach ($sales as $index => $sale) {
            $sheet->fromArray([
                [
                    $sale->product->title ?? '',
                    $sale->customer_name,
                    $sale->cpfcnpjLabel(),
                    $sale->customer_email,
                    $sale->phoneLabel(),
                    $sale->statusExportLabel(),
                    $sale->created_at->format('d/m/Y H:i'),
                    $sale->payment_date ? $sale->payment_date->format('d/m/Y H:i') : ' - '
                ]
            ], null, 'A' . ($index + 2));
        }

        $fileName   = "vendas-{$list->title}.xlsx";
        $writer     = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }
}
