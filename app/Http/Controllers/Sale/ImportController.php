<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\AssasController;
use App\Http\Controllers\Gateway\CoraController;

use App\Models\Commission;
use App\Models\PaymentOption;
use App\Models\Product;
use App\Models\Sale;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use PhpOffice\PhpSpreadsheet\IOFactory;
class ImportController extends Controller {
    
    public function store(Request $request) {

        $product = Product::where('uuid', $request->product_id)->first();
        if (!$product) {
            return redirect()->back()->with('infor', 'Produto indisponível!');
        }

        $option = PaymentOption::where('uuid', $request->product_option_id)->first();
        if (!$option) {
            return redirect()->back()->with('infor', 'Opção de pagamento inválida ou indisponível!');
        }

        if (!$request->hasFile('file')) {
            return redirect()->back()->with('infor', 'Selecione um arquivo para importar!');
        }

        $file         = $request->file('file');
        $spreadsheet  = IOFactory::load($file->getPathname());
        $worksheet    = $spreadsheet->getActiveSheet();
        $rows         = $worksheet->toArray();
        $billingMode  = $request->input('customer', 'CLIENT');
        $user         = Auth::user();
        
        $createdSales = 0;
        $failedSales  = [];
        $salesBuffer  = [];
        $totalValue   = 0;

        for ($i = 3; $i < count($rows); $i++) {

            $row        = $rows[$i];
            $nome       = trim($row[0] ?? '');
            $cpfcnpj    = preg_replace('/\D/', '', $row[1] ?? '');
            $email      = trim($row[2] ?? '');
            $phone      = preg_replace('/\D/', '', $row[3] ?? '');

            if (empty($nome) || empty($cpfcnpj)) {
                $failedSales[] = "Linha $i: Nome ou CPF/CNPJ vazio.";
                continue;
            }

            if (!(self::validateCpf($cpfcnpj) || self::validateCnpj($cpfcnpj))) {
                $failedSales[] = "Linha $i: CPF/CNPJ inválido.";
                continue;
            }

            $value = $option->value + $product->fees_value;

            $salesBuffer[] = compact('nome', 'cpfcnpj', 'email', 'phone', 'value');
            $totalValue += $value;
        }

        switch (env('APP_BANK')) {
            case 'ASAAS':
                $asaas      = new AssasController();
                $customer  = $asaas->createdCustomer($user->name, preg_replace('/\D/', '', $user->cpfcnpj), $user->phone, $user->email);
                
                if ($customer['status'] !== 'success') return back()->with('infor', 'Erro ao criar cliente para boleto único');
                
                if ($option->commission_seller > 0) {
                    $commissions[] = [
                        'walletId'          => Auth::user()->bank_api_key,
                        'fixedValue'        => $option->commission_seller,
                        'description'       => 'Comissão de Vendedor para venda Cliente:'. $request->name
                    ];
                }

                if (($option->commission_parent > 0) && Auth::user()->parent_id) {
                    $commissions[] = [
                        'walletId'          => Auth::user()->parent->bank_api_key,
                        'fixedValue'        => $option->commission_parent,
                        'description'       => 'Comissão de Vendedor para venda Cliente:'. $request->name
                    ];
                }

                if (Auth::user()->addition > 0) {
                    $commissions[] = [
                        'walletId'          => Auth::user()->parent->bank_api_key,
                        'fixedValue'        => max(0, Auth::user()->addition),
                        'description'       => 'Adicional de Patrocinador para venda Cliente:'. $request->name
                    ];
                }

                $payment = $asaas->createdCharge(
                    $customer['id'], $option->payment_method, 1, $totalValue, "Venda conjunta de " . count($salesBuffer) . " clientes", now()->addDays(2), $commissions ?? null
                );

                foreach ($salesBuffer as $data) {
                    Sale::create([
                        'uuid'                 => Str::uuid(),
                        'user_id'              => Auth::id(),
                        'product_id'           => $product->id,
                        'payment_option_id'    => $option->id,
                        'customer_name'        => $data['nome'],
                        'customer_cpfcnpj'     => $data['cpfcnpj'],
                        'customer_email'       => $data['email'],
                        'customer_phone'       => $data['phone'],
                        'value'                => $data['value'],
                        'payment_token'        => $payment['id'],
                        'payment_url'          => $payment['invoiceUrl'],
                        'payment_due_date'     => now()->addDays(2),
                        'payment_status'       => 'PENDING'
                    ]);
                }

                $createdSales = count($salesBuffer);
                break;
            case 'CORA':
                $coraController = new CoraController();
                $customer = [
                    'name'      => Auth::user()->name,
                    'cpfcnpj'   => preg_replace('/\D/', '', Auth::user()->cpfcnpj),
                    'phone'     => preg_replace('/\D/', '', Auth::user()->phone),
                    'email'     => Auth::user()->email,
                ];
                
                $payment = $coraController->createdCharge($customer, $totalValue, $product->title, null, null);
                if ($payment['status'] !== 'success') {
                    return redirect()->back()->with('infor', $payment['message']);
                }

                foreach ($salesBuffer as $data) {
                    $uuid = Str::uuid();

                    if ($option->commission_seller > 0) {
                        $commission = new Commission();
                        $commission->uuid           = Str::uuid();
                        $commission->user_id        = Auth::user()->id;
                        $commission->product_id     = $product->id;
                        $commission->payment_token  = $uuid;
                        $commission->value          = $option->commission_seller;
                        $commission->description    = 'Comissão de Vendedor para venda Cliente:'. $data['nome'];
                        $commission->save();
                    }

                    if (($option->commission_parent > 0) && Auth::user()->parent_id) {
                        $commission = new Commission();
                        $commission->uuid           = Str::uuid();
                        $commission->user_id        = Auth::user()->id;
                        $commission->product_id     = $product->id;
                        $commission->payment_token  = $uuid;
                        $commission->value          = $option->commission_parent;
                        $commission->description    = 'Comissão de Patrocinador para venda Cliente:'. $data['nome'];
                        $commission->save();
                    }

                    if (Auth::user()->addition > 0) {
                        $commission = new Commission();
                        $commission->uuid           = Str::uuid();
                        $commission->user_id        = Auth::user()->parent_id;
                        $commission->product_id     = $product->id;
                        $commission->payment_token  = $uuid;
                        $commission->value          = max(0, Auth::user()->addition);
                        $commission->description    = 'Adicional de Patrocinador para venda Cliente:'. $data['nome'];
                        $commission->save();
                    }

                    Sale::create([
                        'uuid'                 => $uuid,
                        'user_id'              => Auth::id(),
                        'product_id'           => $product->id,
                        'payment_option_id'    => $option->id,
                        'customer_name'        => $data['nome'],
                        'customer_cpfcnpj'     => $data['cpfcnpj'],
                        'customer_email'       => $data['email'],
                        'customer_phone'       => $data['phone'],
                        'value'                => $data['value'],
                        'payment_token'        => $payment['id'],
                        'payment_url'          => $payment['invoiceUrl'],
                        'payment_due_date'     => now()->addDays(2),
                        'payment_status'       => 'PENDING'
                    ]);
                }

                $createdSales = count($salesBuffer);
                break;
            default:
                return redirect()->back()->with('infor', 'Conexão bancária indisponível no momento, tente novamente mais tarde!');
                break;
        }
        
        return back()->with('success', "Importação concluída! ✔ $createdSales vendas criadas.")->with('warning', count($failedSales) ? implode("\n", $failedSales) : null);
    }

    private static function validateCpf($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($cpf) !== 11 || preg_match('/(\d)\1{10}/', $cpf)) return false;

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) return false;
        }
        return true;
    }

    private static function validateCnpj($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        if (strlen($cnpj) != 14) return false;

        for ($t = 12; $t < 14; $t++) {
            for ($d = 0, $c = 0, $p = $t - 7; $c < $t; $c++, $p--) {
                $p = $p < 2 ? 9 : $p;
                $d += $cnpj[$c] * $p;
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cnpj[$c] != $d) return false;
        }
        return true;
    }
}
