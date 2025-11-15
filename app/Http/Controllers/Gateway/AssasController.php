<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Lists;
use App\Models\Sale;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AssasController extends Controller {
    
    public function createdCustomer($name, $cpfcnpj, $mobilePhone = null, $email = null) {

        try {
            $client = new Client();
            $options = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'accept'       => 'application/json',
                    'access_token' => env('API_BANK_TOKEN'),
                    'User-Agent'   => env('APP_NAME')
                ],
                'json' => [
                    'name'        => $name,
                    'cpfCnpj'     => $cpfcnpj,
                    'mobilePhone' => $mobilePhone,
                    'email'       => $email,
                    'notificationDisabled' => true
                ],
                'verify' => false
            ];

            $response = $client->post(env('API_BANK_URL') . 'v3/customers', $options);
            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['id'])) {
                return [
                    'status' => 'success',
                    'id'     => $data['id']
                ];
            }

            return [
                'status'  => 'error',
                'message' => $data['errors'][0]['description'] ?? 'Erro inesperado ao cadastrar cliente'
            ];

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);

                return [
                    'status'  => 'error',
                    'message' => $responseBody['errors'][0]['description'] 
                                ?? 'Erro na requisição ao Asaas.'
                ];
            }

            return [
                'status'  => 'error',
                'message' => 'Erro ao se comunicar com a API Asaas.'
            ];

        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function createdCharge($customer, $billingType, $installments = null, $value, $description, $dueDate = null, $commissions = null) {

        try {
            $client = new Client();

            $options = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'access_token' => env('API_BANK_TOKEN'),
                    'User-Agent'   => env('APP_NAME')
                ],
                'json' => [
                    'customer'          => $customer,
                    'billingType'       => $billingType,
                    'installmentCount'  => $installments ?? 1,
                    'installmentValue'  => number_format(($value / ($installments ?? 1)), 2, '.', ''),
                    'value'             => number_format($value, 2, '.', ''),
                    'dueDate'           => $dueDate 
                                            ? Carbon::parse($dueDate)->toIso8601String() 
                                            : now()->addDays(1)->toDateString(),
                    'description'       => $description,
                    'isAddressRequired' => false,
                ],
                'verify' => false
            ];

            if (!empty($commissions)) {
                if (is_string($commissions)) {
                    $commissions = json_decode($commissions, true);
                }

                if (!is_array($commissions)) {
                    $commissions = [$commissions];
                }

                foreach ($commissions as &$c) {
                    if (isset($c['fixedValue'])) {
                        $c['fixedValue'] = (float) $c['fixedValue'];
                    }
                }

                $options['json']['split'] = $commissions;
            }

            if (env('APP_ENV') !== 'local') {
                $options['json']['callback'] = ['successUrl' => env('APP_URL')];
            }

            $response = $client->post(env('API_BANK_URL') . 'v3/payments', $options);
            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['id'])) {
                return [
                    'status'     => 'success',
                    'id'         => $data['id'],
                    'invoiceUrl' => $data['invoiceUrl'],
                    'splits'     => $data['split'] ?? [],
                ];
            }

            return [
                'status'  => 'error',
                'message' => $data['errors'][0]['description'] ?? 'Erro inesperado ao gerar cobrança'
            ];

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);

                return [
                    'status'  => 'error',
                    'message' => $responseBody['errors'][0]['description']
                                ?? 'Erro na geração da cobrança.'
                ];
            }

            return [
                'status'  => 'error',
                'message' => 'Falha na comunicação com a API Asaas.'
            ];

        } catch (\Exception $e) {

            return [
                'status'  => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function webhook(Request $request) {

        $jsonData   = $request->json()->all();
        $token      = $jsonData['payment']['id'] ?? null;
        if (!$token) {
            return response()->json(['message' => 'Token de pagamento ausente!'], 400);
        }

        if ($jsonData['event'] === 'PAYMENT_CONFIRMED' || $jsonData['event'] === 'PAYMENT_RECEIVED') {

            $sale = Sale::with(['user.parent', 'product', 'paymentOption'])
                ->where('payment_token', $token)
                ->whereIn('payment_status', ['PENDING', 'CANCELED', 'REFUNDED', 'FAILED'])
                ->first();
            if ($sale) {

                $list = Lists::where('active', true)->first() ?? Lists::latest()->first();
                if (!$list) {
                    return response()->json(['message' => 'Nenhuma lista ativa ou cadastrada encontrada!'], 400);
                }

                $product = $sale->product;
                if ($product && ($product->cashback_value > 0)) {
                    $sale->user->increment('wallet', $product->cashback_value);
                } else if ($product && ($product->cashback_percentage > 0)) {
                    $cashbackAmount = round(($sale->value * $product->cashback_percentage) / 100, 2);
                    $sale->user->increment('wallet', $cashbackAmount);
                }
                
                $option = $sale->paymentOption;
                if ($option && ($option->commission_seller > 0)) {
                    $sale->user->increment('wallet', $option->commission_seller);
                }
                if ($option && $sale->user->parent && ($option->commission_parent > 0)) {
                    $sale->user->parent->increment('wallet', $option->commission_parent);
                }

                $sale->payment_status   = 'PAID';
                $sale->payment_date     = now();
                $sale->list_id          = $list->id;
                if ($sale->save()) {
                    return response()->json(['message' => 'Venda Aprovada!'], 200);
                } else {
                    return response()->json(['message' => 'Falha ao tentar Aprovar Venda!'], 400);
                }
            }

            $invoice = Invoice::where('payment_token', $token)->whereIn('payment_status', ['PENDING', 'OVERDUE'])->first();
            if ($invoice) {

                $invoice->payment_status = 'PAID';
                $invoice->payment_date   = now();
                if ($invoice->save()) {

                    if ($invoice->payment_type === 'DEPOSIT') {
                        $invoice->user->increment('wallet', $invoice->value);
                    }

                    return response()->json(['message' => 'Fatura Atualizada para Paga!'], 200);
                } else {
                    return response()->json(['message' => 'Falha ao tentar Atualizar Fatura!'], 400);
                }
            }

            return response()->json(['message' => 'Nenhuma venda elegível encontrada para atualização.'], 404);
        };

        if ($jsonData['event'] === 'PAYMENT_OVERDUE' || $jsonData['event'] === 'PAYMENT_DELETED') {

            $sale = Sale::where('payment_token', $token)->whereIn('payment_status', ['PENDING', 'CANCELED', 'REFUNDED', 'FAILED'])->first();
            if ($sale) {

                $sale->payment_status = 'CANCELED';
                $sale->payment_date = null;
                if ($sale->save()) {
                    return response()->json(['message' => 'Venda Cancelada por vencimento!'], 200);
                } else {
                    return response()->json(['message' => 'Falha ao tentar Cancelar Venda!'], 400);
                }
            }

            $invoice = Invoice::where('payment_token', $token)->first();
            if ($invoice) {

                $invoice->payment_status = 'OVERDUE';
                $invoice->payment_date   = null;
                if ($invoice->save()) {
                    return response()->json(['message' => 'Fatura Atualizada para Cancelada!'], 200);
                } else {
                    return response()->json(['message' => 'Falha ao tentar Cancelar Fatura!'], 400);
                }
            }

            return response()->json(['message' => 'Nenhum registro atualizado!'], 404);
        };

        if ($jsonData['event'] === 'PAYMENT_RESTORED') {

            $sale = Sale::where('payment_token', $token)->whereIn('payment_status', ['PENDING', 'CANCELED', 'REFUNDED', 'FAILED'])->first();
            if ($sale) {

                $sale->payment_status = 'PENDING';
                $sale->payment_date = null;
                if ($sale->save()) {
                    return response()->json(['message' => 'Venda restaurada via Banco!'], 200);
                } else {
                    return response()->json(['message' => 'Falha ao tentar restaurar a Venda!'], 400);
                }
            }

            $invoice = Invoice::where('payment_token', $token)->first();
            if ($invoice) {

                $invoice->payment_status = 'PENDING';
                $invoice->payment_date   = null;
                if ($invoice->save()) {
                    return response()->json(['message' => 'Fatura Atualizada para Cancelada!'], 200);
                } else {
                    return response()->json(['message' => 'Falha ao tentar Cancelar Fatura!'], 400);
                }
            }

            return response()->json(['message' => 'Nenhum registro atualizado!'], 404);
        };
        
        return response()->json(['message' => 'Nenhum Evento disponível!'], 200);
    }
}
