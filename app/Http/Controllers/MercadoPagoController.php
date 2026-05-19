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
        \Illuminate\Support\Facades\Log::info('Mercado Pago Webhook Received:', $request->all());

        $type = $request->input('type');
        $action = $request->input('action');
        
        $paymentId = null;
        
        if ($type === 'payment' && $request->has('data.id')) {
            $paymentId = $request->input('data.id');
        } elseif (isset($action) && strpos($action, 'payment.') === 0 && $request->has('data.id')) {
            $paymentId = $request->input('data.id');
        }

        if ($paymentId) {
            try {
                $client = new PaymentClient();
                $payment = $client->get($paymentId);

                $order = \App\Models\Order::where('mp_payment_id', $paymentId)->first();

                if ($order) {
                    if ($payment->status === 'approved') {
                        $order->payment_status = 'paid';
                    } elseif ($payment->status === 'rejected' || $payment->status === 'cancelled') {
                        $order->payment_status = 'failed';
                    }
                    $order->save();
                    \Illuminate\Support\Facades\Log::info("Order #{$order->id} payment status updated to {$order->payment_status} via webhook");
                }

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error processing Mercado Pago webhook: ' . $e->getMessage());
                return response()->json(['error' => 'Webhook processing failed'], 500);
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}
