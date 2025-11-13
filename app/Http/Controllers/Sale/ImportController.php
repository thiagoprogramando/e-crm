<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\AssasController;
use App\Models\PaymentOption;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
        $billingMode  = $request->input('payment_customer', 'CLIENT');
        $asaas       = new AssasController();

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

        if ($billingMode === 'CLIENT') {

            foreach ($salesBuffer as $index => $data) {

                try {

                    $customer = $asaas->createdCustomer($data['nome'], $data['cpfcnpj'], $data['phone'], $data['email']);

                    if ($customer['status'] !== 'success') {
                        $failedSales[] = "Linha $index: " . $customer['message'];
                        continue;
                    }

                    $payment = $asaas->createdCharge(
                        $customer['id'],
                        $option->payment_method,
                        $option->payment_installments,
                        $data['value'],
                        $product->title,
                        now()->addDays(2),
                        $option->payment_splits
                    );

                    if ($payment['status'] !== 'success') {
                        $failedSales[] = "Linha $index: " . $payment['message'];
                        continue;
                    }

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

                    $createdSales++;

                } catch (\Throwable $th) {
                    $failedSales[] = "Linha $index: Erro inesperado: " . $th->getMessage();
                }
            }
        } else {

            $user = Auth::user();
            $customer = $asaas->createdCustomer($user->name, preg_replace('/\D/', '', $user->cpfcnpj), $user->phone, $user->email);

            if ($customer['status'] !== 'success')
                return back()->with('infor', 'Erro ao criar cliente para boleto único');

            $payment = $asaas->createdCharge(
                $customer['id'],
                $option->payment_method,
                1,
                $totalValue,
                "Venda conjunta de " . count($salesBuffer) . " clientes",
                now()->addDays(2),
                $option->payment_splits
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
