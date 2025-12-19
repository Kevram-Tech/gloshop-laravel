<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    private const PAYGATE_API_URL = 'https://paygateglobal.com/api/v1';
    private const PAYGATE_AUTH_TOKEN = 'bb8f5926-4460-46b3-8b3a-9b4abbbad46f';

    /**
     * Initiate PayGate payment (T-Money/Flooz)
     */
    public function initiatePayGate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'phone_number' => 'required|string',
            'network' => 'required|in:FLOOZ,TMONEY',
        ]);

        $order = Order::where('id', $validated['order_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Cette commande a déjà été payée',
            ], 400);
        }

        try {
            $response = Http::post(self::PAYGATE_API_URL . '/pay', [
                'auth_token' => self::PAYGATE_AUTH_TOKEN,
                'phone_number' => $validated['phone_number'],
                'amount' => (int) $order->total_amount,
                'description' => "Paiement commande #{$order->order_number}",
                'identifier' => $order->order_number,
                'network' => $validated['network'],
            ]);

            $data = $response->json();

            if ($data['status'] == 0) {
                // Create payment transaction record
                $transaction = PaymentTransaction::create([
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'payment_method' => 'paygate_' . strtolower($validated['network']),
                    'amount' => $order->total_amount,
                    'tx_reference' => $data['tx_reference'],
                    'identifier' => $order->order_number,
                    'status' => 'pending',
                    'metadata' => [
                        'phone_number' => $validated['phone_number'],
                        'network' => $validated['network'],
                    ],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Paiement initié avec succès',
                    'data' => [
                        'tx_reference' => $data['tx_reference'],
                        'transaction_id' => $transaction->id,
                        'order_id' => $order->id,
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $this->getPayGateErrorMessage($data['status']),
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('PayGate payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'initiation du paiement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle PayGate callback
     */
    public function payGateCallback(Request $request): JsonResponse
    {
        $data = $request->all();

        Log::info('PayGate callback received', $data);

        try {
            $transaction = PaymentTransaction::where('tx_reference', $data['tx_reference'])
                ->orWhere('identifier', $data['identifier'])
                ->first();

            if (!$transaction) {
                Log::warning('PayGate callback: Transaction not found', $data);
                return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
            }

            DB::beginTransaction();

            // Update transaction
            $transaction->update([
                'payment_reference' => $data['payment_reference'] ?? null,
                'status' => 'completed',
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'payment_reference' => $data['payment_reference'] ?? null,
                    'datetime' => $data['datetime'] ?? null,
                    'payment_method' => $data['payment_method'] ?? null,
                    'phone_number' => $data['phone_number'] ?? null,
                ]),
            ]);

            // Update order
            $order = $transaction->order;
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PayGate callback error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tx_reference' => 'required_without:identifier|string',
            'identifier' => 'required_without:tx_reference|string',
        ]);

        try {
            if (isset($validated['tx_reference'])) {
                $response = Http::post(self::PAYGATE_API_URL . '/status', [
                    'auth_token' => self::PAYGATE_AUTH_TOKEN,
                    'tx_reference' => $validated['tx_reference'],
                ]);
            } else {
                $response = Http::post(self::PAYGATE_API_URL . '/v2/status', [
                    'auth_token' => self::PAYGATE_AUTH_TOKEN,
                    'identifier' => $validated['identifier'],
                ]);
            }

            $data = $response->json();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Check payment status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification du statut',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process card payment (Visa)
     */
    public function processCardPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'card_number' => 'required|string',
            'card_holder' => 'required|string',
            'expiry_month' => 'required|string',
            'expiry_year' => 'required|string',
            'cvv' => 'required|string',
        ]);

        $order = Order::where('id', $validated['order_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Cette commande a déjà été payée',
            ], 400);
        }

        try {
            // TODO: Intégrer avec un processeur de paiement par carte (Stripe, PayPal, etc.)
            // Pour l'instant, on simule le paiement
            
            DB::beginTransaction();

            // Create payment transaction
            $transaction = PaymentTransaction::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'payment_method' => 'card_visa',
                'amount' => $order->total_amount,
                'tx_reference' => 'CARD-' . strtoupper(uniqid()),
                'identifier' => $order->order_number,
                'status' => 'completed',
                'metadata' => [
                    'card_last4' => substr($validated['card_number'], -4),
                    'card_holder' => $validated['card_holder'],
                ],
            ]);

            // Update order
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Paiement effectué avec succès',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'order_id' => $order->id,
                    'tx_reference' => $transaction->tx_reference,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Card payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement du paiement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get payment error message
     */
    private function getPayGateErrorMessage(int $status): string
    {
        return match ($status) {
            2 => 'Jeton d\'authentification invalide',
            4 => 'Paramètres invalides',
            6 => 'Une transaction avec le même identifiant existe déjà',
            default => 'Erreur inconnue',
        };
    }
}

