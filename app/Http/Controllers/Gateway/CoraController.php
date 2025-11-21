<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Invoice;
use App\Models\Lists;
use App\Models\Sale;
use App\Models\Withdraw;
use App\Services\Cora\CoraAuthService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class CoraController extends Controller {

    public function getToken() {
        return $token = app(CoraAuthService::class)->getToken();
    }
    
    public function createdCharge($customer, $value, $description, $dueDate = null, $commissions = null) {

        $token = app(CoraAuthService::class)->getToken();
        try {
            $client = new Client([
                'cert'    => config('services.cora.certificate'),
                'ssl_key' => config('services.cora.key'),
                'verify'  => false,
            ]);

            $options = [
                'headers' => [
                    'Content-Type'     => 'application/json',
                    'Accept'           => 'application/json',
                    'Authorization'    => "Bearer {$token}",
                    'Idempotency-Key'  => Str::uuid()->toString(),
                    'User-Agent'       => env('APP_NAME'),
                ],
                'json' => [
                    'customer' => [
                        'name' => $customer->name,
                        'email' => $customer->email ?? null,
                        'document' => [
                            'identity' => preg_replace('/\D/', '', $customer->cpfcnpj),
                            'type'     => (strlen(preg_replace('/\D/', '', $customer->cpfcnpj)) == 11) ? 'CPF' : 'CNPJ',
                        ],
                    ],
                    'services' => [
                        [
                            'name'        => $description,
                            'description' => $description,
                            'amount'      => intval($value * 100),
                        ],
                    ],
                    'payment_terms' => [
                        'due_date' => $dueDate ?? now()->addDays(2)->format('Y-m-d'),
                    ],
                    'payment_forms' => [
                        "BANK_SLIP",
                        "PIX",
                    ]
                ],
            ];

            $response = $client->post(env('API_BANK_URL') . 'v2/invoices/', $options);
            $data = json_decode($response->getBody()->getContents(), true);

            return [
                    'status'     => 'success',
                    'id'         => $data['id'],
                    'invoiceUrl' => $data['payment_options']['bank_slip']['url'],
                    'qrCode'     => $data['pix']['emv'] ?? null,
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
                'message' => 'Falha na comunicação com a API Cora.'
            ];

        } catch (\Exception $e) {

            return [
                'status'  => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function sendWithdrawal(Withdraw $withdrawal) {
        return [
            'success' =>'DONE',
            'status'  => 'DONE',
            'payment_token' => Str::uuid(),
            'payment_url'   => null,
        ];
    }

    public function webhook(Request $request) {

        Log::info('Cora Webhook Recebido:', [
            'headers' => $request->headers->all(),
            'body'    => $request->all(),
        ]);

        $headers = $request->headers->all();
        if (empty($headers)) {
            return response()->json(['message' => 'Headers vazios'], 400);
        }

        $eventType  = $request->header('webhook-event-type');
        $token      = $request->header('webhook-resource-id');

        if ($eventType === 'invoice.PAID') {

            $sale = Sale::with(['user.parent', 'product', 'paymentOption'])->where('payment_token', $token)
                            ->whereIn('payment_status', ['PENDING', 'CANCELED', 'REFUNDED', 'FAILED'])->first();
            if ($sale) {

                $list = Lists::where('status', 'active')->first() ?? Lists::latest()->first();
                if (!$list) {
                    return response()->json(['message' => 'Nenhuma lista ativa encontrada!'], 400);
                }

                $product = $sale->product;
                if ($product && $product->cashback_value > 0) {
                    $sale->user->increment('wallet', $product->cashback_value);
                }  else if ($product && $product->cashback_percentage > 0) {
                    $cashbackAmount = round(($sale->value * $product->cashback_percentage) / 100, 2);
                    $sale->user->increment('wallet', $cashbackAmount);
                }

                $option = $sale->paymentOption;
                if ($option && $option->commission_seller > 0) {
                    $sale->user->increment('wallet', $option->commission_seller);
                }
                if ($option && $sale->user->parent && $option->commission_parent > 0) {
                    $sale->user->parent->increment('wallet', $option->commission_parent);
                }

                Commission::where('payment_token', $sale->uuid)->whereNull('confirmed_at')
                    ->update([
                        'is_paid'      => true,
                        'confirmed_at' => now(),
                    ]);

                $sale->payment_status = 'PAID';
                $sale->payment_date   = now();
                $sale->list_id        = $list->id;
                if ($sale->save()) {
                    return response()->json(['message' => 'Venda aprovada via Cora!'], 200);
                } 

                return response()->json(['message' => 'Falha ao aprovar venda!'], 400);
            }

            $invoice = Invoice::where('payment_token', $token)->whereIn('payment_status', ['PENDING', 'OVERDUE'])->first();
            if ($invoice) {

                $invoice->payment_status = 'PAID';
                $invoice->payment_date   = now();
                if ($invoice->save()) {
                    if ($invoice->payment_type === 'DEPOSIT') {
                        $invoice->user->increment('wallet', $invoice->value);
                    }

                    return response()->json(['message' => 'Fatura marcada como paga!'], 200);
                } else {
                    return response()->json(['message' => 'Falha ao atualizar fatura!'], 400);
                }
            }

            return response()->json(['message' => 'Nenhuma venda/fatura elegível encontrada.'], 200);
        }

        if ($eventType === 'invoice.EXPIRED' || $eventType === 'invoice.CANCELED') {

            $sale = Sale::where('payment_token', $token)->whereIn('payment_status', ['PENDING', 'CANCELED', 'REFUNDED', 'FAILED'])->first();
            if ($sale) {

                $sale->payment_status = 'CANCELED';
                $sale->payment_date   = null;
                if ($sale->save()) {
                    return response()->json(['message' => 'Venda cancelada pelo Cora (expirada ou removida).'], 200);
                }

                return response()->json(['message' => 'Falha ao cancelar venda!'], 400);
            }

            $invoice = Invoice::where('payment_token', $token)->first();
            if ($invoice) {

                $invoice->payment_status = 'OVERDUE';
                $invoice->payment_date   = null;
                if ($invoice->save()) {
                    return response()->json(['message' => 'Fatura marcada como vencida/removida.'], 200);
                }

                return response()->json(['message' => 'Falha ao atualizar fatura!'], 400);
            }

            return response()->json(['message' => 'Nenhum registro atualizado.'], 200);
        }

        if ($eventType === 'invoice.RESTORED' || ($eventType === 'invoice.UPDATED')) {

            $sale = Sale::where('payment_token', $token)->first();
            if ($sale) {

                $sale->payment_status = 'PENDING';
                $sale->payment_date   = null;
                if ($sale->save()) {
                    return response()->json(['message' => 'Venda restaurada via Cora!'], 200);
                }

                return response()->json(['message' => 'Falha ao tentar restaurar a Venda!'], 400);
            }

            $invoice = Invoice::where('payment_token', $token)->first();
            if ($invoice) {

                $invoice->payment_status = 'PENDING';
                $invoice->payment_date   = null;
                if ($invoice->save()) {
                    return response()->json(['message' => 'Fatura atualizada para PENDING!'], 200);
                } 
                
                return response()->json(['message' => 'Falha ao tentar atualizar a Fatura!'], 400);
            }

            return response()->json(['message' => 'Nenhum registro atualizado!'], 200);
        }
    }
}
