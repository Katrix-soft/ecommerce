<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoController extends Controller
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('mercadopago.access_token'));
    }

    /**
     * Procesar pago con Payment Brick (API directa)
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'issuer_id' => 'required',
            'payment_method_id' => 'required|string',
            'transaction_amount' => 'required|numeric',
            'installments' => 'required|integer',
            'payer.email' => 'required|email',
        ]);

        try {
            $client = new PaymentClient();

            $paymentData = [
                'transaction_amount' => (float) $request->transaction_amount,
                'token' => $request->token,
                'description' => 'Compra en Shoply - Pedido de ' . auth()->user()->name,
                'installments' => (int) $request->installments,
                'payment_method_id' => $request->payment_method_id,
                'issuer_id' => (int) $request->issuer_id,
                'external_reference' => $request->input('external_reference', uniqid('order_')), // Obligatorio para medición de calidad
                'notification_url' => url('/api/webhooks/mercadopago'), // Obligatorio para medición de calidad
                'payer' => [
                    'email' => $request->input('payer.email'),
                    'identification' => $request->input('payer.identification', []),
                ],
                'additional_info' => [
                    'items' => $request->input('items', []), // Recomendado para prevención de fraude
                    'payer' => [
                        'first_name' => auth()->user()->name,
                        // Se pueden agregar más datos como teléfono o dirección si vienen en el request
                    ]
                ]
            ];

            $payment = $client->create($paymentData);

            return response()->json([
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
                'id' => $payment->id,
                'payment_type_id' => $payment->payment_type_id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retornar la public key al frontend
     */
    public function getPublicKey()
    {
        return response()->json([
            'public_key' => config('mercadopago.public_key'),
        ]);
    }

    /**
     * Manejar Webhooks enviados por Mercado Pago
     */
    public function handleWebhook(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('MP Webhook:', $request->all());

        $type   = $request->input('type');
        $action = $request->input('action');

        $paymentId = null;
        if ($type === 'payment' && $request->has('data.id')) {
            $paymentId = $request->input('data.id');
        } elseif (isset($action) && str_starts_with($action, 'payment.') && $request->has('data.id')) {
            $paymentId = $request->input('data.id');
        }

        if (!$paymentId) {
            return response()->json(['status' => 'ok']);
        }

        try {
            $client  = new PaymentClient();
            $payment = $client->get($paymentId);

            if ($payment->status !== 'approved') {
                return response()->json(['status' => 'ok']);
            }

            $monto       = $payment->transaction_amount;
            $descripcion = $payment->description ?? ''; // acá viene el concepto "ECO-0042"
            $email       = $payment->payer->email ?? null;

            // Buscar por external_reference primero (más exacto)
            $order = \App\Models\Order::where('payment_status', 'pending')
                ->where(function ($q) use ($descripcion, $monto, $email) {
                    // Match por referencia en el concepto
                    $q->whereRaw("? LIKE CONCAT('%', CAST(id AS CHAR), '%')", [$descripcion])
                      // O por monto + email como fallback
                      ->orWhere(function ($q2) use ($monto, $email) {
                          $q2->where('total', $monto)
                             ->where(function ($q3) use ($email) {
                                 $q3->where('transfer_issuer_name', 'like', "%{$email}%")
                                    ->orWhereHas('user', fn($u) => $u->where('email', $email));
                             });
                      });
                })
                ->latest()
                ->first();

            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'mp_payment_id'  => $paymentId,
                    'paid_at'        => now(),
                ]);

                \Illuminate\Support\Facades\Log::info("Orden #{$order->id} aprobada automáticamente via webhook MP");
            } else {
                \Illuminate\Support\Facades\Log::warning('MP Webhook: no se encontró orden', [
                    'monto'      => $monto,
                    'descripcion'=> $descripcion,
                    'email'      => $email,
                ]);
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error webhook MP: ' . $e->getMessage());
            return response()->json(['error' => 'failed'], 500);
        }

        return response()->json(['status' => 'ok']);
    }
}
