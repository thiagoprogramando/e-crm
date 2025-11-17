<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Invoice;
use App\Models\Lists;
use App\Models\Sale;
use App\Models\Withdraw;
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

    public function sendWithdrawal(Withdraw $withdrawal) {

        $client = new Client();
        try {
            $response = $client->request('POST', env('API_BANK_URL').'v3/transfers', [
                'headers' => [
                    'accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                    'access_token' => env('API_BANK_TOKEN'),
                    'User-Agent'   => env('APP_NAME')
                ],
                'json' => [
                    'value'             => $withdrawal->value,
                    'operationType'     => 'PIX',
                    'pixAddressKey'     => $withdrawal->payment_key,
                    'pixAddressKeyType' => strlen($withdrawal->key) == 11 ? 'CPF' : 'CNPJ',
                    'description'       => 'Saque '.env('APP_NAME'),
                ],
                'verify'  => false,
            ]);

            $decoded = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => $decoded['status'] === 'PENDING',
                'status'  => $decoded['status'],
                'payment_token' => $decoded['id'] ?? null,
                'payment_url'   => $decoded['transactionReceiptUrl'] ?? null,
            ];

        } catch (RequestException $e) {
            $decoded = json_decode($e->getResponse()->getBody()->getContents(), true);
            return [
                'success' => false,
                'status'  => 'FAILED',
                'error'   => $decoded['errors'][0]['description'] ?? 'Erro desconhecido'
            ];
        }
    }


    public function webhook(Request $request) {

        $jsonData   = $request->json()->all();
        $token      = $jsonData['payment']['id'] ?? null;

        if ($jsonData['event'] === 'PAYMENT_CONFIRMED' || $jsonData['event'] === 'PAYMENT_RECEIVED') {

            $sale = Sale::with(['user.parent', 'product', 'paymentOption'])
                ->where('payment_token', $token)
                ->whereIn('payment_status', ['PENDING', 'CANCELED', 'REFUNDED', 'FAILED'])
                ->first();
            if ($sale) {

                $list = Lists::where('status', 'active')->first() ?? Lists::latest()->first();
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

                $commissions = Commission::where('payment_token', $sale->uuid)->whereNull('confirmed_at')
                                ->update([
                                    'is_paid'      => true,
                                    'confirmed_at' => now(),
                                ]);

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

            return response()->json(['message' => 'Nenhuma venda elegível encontrada para atualização.'], 200);
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

            return response()->json(['message' => 'Nenhum registro atualizado!'], 200);
        };

        if ($jsonData['event'] === 'PAYMENT_RESTORED' || $jsonData['event'] === 'PAYMENT_RECEIVED_IN_CASH_UNDONE' || $jsonData['event'] === 'PAYMENT_REFUND_DENIED') {

            $sale = Sale::where('payment_token', $token)->first();
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
                    return response()->json(['message' => 'Fatura Atualizada para pendente!'], 200);
                } else {
                    return response()->json(['message' => 'Falha ao tentar Atualizar Fatura!'], 400);
                }
            }

            return response()->json(['message' => 'Nenhum registro atualizado!'], 200);
        };

        if ($jsonData['event'] === 'PAYMENT_RECEIVED_IN_CASH_UNDONE' || $jsonData['event'] === 'PAYMENT_REFUND_DENIED') {

            $sale = Sale::where('payment_token', $token)->first();
            if ($sale) {

                $sale->payment_status = 'PENDING';
                $sale->payment_date = null;
                if ($sale->save()) {

                    $commissionTotal    = Commission::where('payment_token', $sale->uuid)->whereNotNull('confirmed_at')->sum('value');
                    $commissions        = Commission::where('payment_token', $sale->uuid)->whereNotNull('confirmed_at') // <- estava errado usar whereNull()
                                            ->update([
                                                'is_paid'      => false,
                                                'confirmed_at' => null,
                                            ]);

                    if ($commissionTotal > 0) {
                        $sale->user->decrement('wallet', $commissionTotal);
                    }
    
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

                    if ($invoice->payment_type === 'DEPOSIT') {
                        $invoice->user->decrement('wallet', $invoice->value);
                    }

                    return response()->json(['message' => 'Fatura Atualizada para pendente!'], 200);
                } else {
                    return response()->json(['message' => 'Falha ao tentar Atualizar Fatura!'], 400);
                }
            }

            return response()->json(['message' => 'Nenhum registro atualizado!'], 200);
        };

        $token = $jsonData['transfer']['id'] ?? null;
        if ($jsonData['event'] === 'TRANSFER_DONE') {

            $withdrawal = Withdraw::where('payment_token', $token)->whereNull('confirmed_at')->first();
            if ($withdrawal) {

                $withdrawal->is_paid      = true;
                $withdrawal->confirmed_at = now();
                $withdrawal->payment_url  = $jsonData['transfer']['transactionReceiptUrl'] ?? null;
                if ($withdrawal->save()) {
                    return response()->json(['message' => 'Saque marcado como pago!'], 200);
                } else {
                    return response()->json(['message' => 'Falha ao tentar atualizar saque!'], 400);
                }
            }

            return response()->json(['message' => 'Nenhum Saque encontrado para atualizar!'], 200);
        }

        if ($jsonData['event'] === 'TRANSFER_FAILED' || $jsonData['event'] === 'TRANSFER_CANCELED') {

            $withdrawal = Withdraw::where('payment_token', $token)->first();
            if ($withdrawal) {

                $withdrawal->is_paid        = false;
                $withdrawal->confirmed_at   = null;
                $withdrawal->payment_log    = 'Transferência falhou ou foi cancelada pelo banco!';
                if ($withdrawal->save()) {
                    return response()->json(['message' => 'Saque atualizado!'], 200);
                } else {
                    return response()->json(['message' => 'Falha ao tentar atualizar saque!'], 400);
                }
            }

            return response()->json(['message' => 'Nenhum Saque encontrado para atualizar!'], 200);
        }
        
        return response()->json(['message' => 'Nenhum Evento disponível!'], 200);
    }
}
