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
     * Get PayGate callback URL
     */
    private function getCallbackUrl(): string
    {
        $appUrl = config('app.url', 'http://72.60.188.146:6500');
        return rtrim($appUrl, '/') . '/api/payments/paygate/callback';
    }

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
                'url' => $this->getCallbackUrl(), // Callback URL for PayGate
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
     * 
     * This endpoint is called by PayGate when a payment is completed.
     * URL: http://72.60.188.146:6500/api/payments/paygate/callback
     * 
     * PayGate will send the following data:
     * - tx_reference: Transaction reference from PayGate
     * - identifier: Order identifier (order_number)
     * - status: Payment status (0 = success, 2 = pending, 4 = expired, 6 = cancelled)
     * - payment_reference: Payment reference from mobile money
     * - datetime: Payment date and time
     * - payment_method: Payment method used
     * - phone_number: Phone number used for payment
     */
    public function payGateCallback(Request $request): JsonResponse
    {
        $data = $request->all();

        Log::info('PayGate callback received', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'data' => $data,
        ]);

        try {
            // Validate required fields
            if (!isset($data['tx_reference']) && !isset($data['identifier'])) {
                Log::warning('PayGate callback: Missing tx_reference or identifier', $data);
                return response()->json(['success' => false, 'message' => 'Missing required fields'], 400);
            }

            // Find transaction
            $transaction = PaymentTransaction::where(function ($query) use ($data) {
                if (isset($data['tx_reference'])) {
                    $query->where('tx_reference', $data['tx_reference']);
                }
                if (isset($data['identifier'])) {
                    $query->orWhere('identifier', $data['identifier']);
                }
            })->first();

            if (!$transaction) {
                Log::warning('PayGate callback: Transaction not found', $data);
                return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
            }

            // Prevent duplicate processing
            if ($transaction->status === 'completed' && $transaction->order->payment_status === 'paid') {
                Log::info('PayGate callback: Transaction already processed', [
                    'tx_reference' => $data['tx_reference'] ?? $data['identifier'],
                ]);
                return response()->json(['success' => true, 'message' => 'Already processed']);
            }

            DB::beginTransaction();

            // Determine payment status from PayGate response
            $paymentStatus = 'completed';
            if (isset($data['status'])) {
                // PayGate status: 0 = success, 2 = pending, 4 = expired, 6 = cancelled
                $paymentStatus = match ($data['status']) {
                    0 => 'completed',
                    2 => 'pending',
                    4 => 'expired',
                    6 => 'cancelled',
                    default => 'pending',
                };
            }

            // Update transaction
            $transaction->update([
                'payment_reference' => $data['payment_reference'] ?? $transaction->payment_reference,
                'status' => $paymentStatus,
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'payment_reference' => $data['payment_reference'] ?? null,
                    'datetime' => $data['datetime'] ?? null,
                    'payment_method' => $data['payment_method'] ?? null,
                    'phone_number' => $data['phone_number'] ?? null,
                    'callback_received_at' => now()->toIso8601String(),
                ]),
            ]);

            // Update order only if payment is completed
            if ($paymentStatus === 'completed') {
                $order = $transaction->order;
                
                // Double-check order hasn't been paid by another transaction
                if ($order->payment_status !== 'paid') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing',
                    ]);
                }
            }

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PayGate callback error: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $data,
            ]);
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
            // First, check our database
            $transaction = PaymentTransaction::where(function ($query) use ($validated) {
                if (isset($validated['tx_reference'])) {
                    $query->where('tx_reference', $validated['tx_reference']);
                }
                if (isset($validated['identifier'])) {
                    $query->orWhere('identifier', $validated['identifier']);
                }
            })->with('order')->first();

            if ($transaction && $transaction->status === 'completed' && $transaction->order->payment_status === 'paid') {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'status' => 0, // PayGate success status
                        'payment_reference' => $transaction->payment_reference,
                        'payment_method' => $transaction->metadata['payment_method'] ?? null,
                        'datetime' => $transaction->metadata['datetime'] ?? null,
                        'from_cache' => true,
                    ],
                ]);
            }

            // Query PayGate API
            $response = null;
            if (isset($validated['tx_reference'])) {
                $response = Http::timeout(10)->post(self::PAYGATE_API_URL . '/status', [
                    'auth_token' => self::PAYGATE_AUTH_TOKEN,
                    'tx_reference' => $validated['tx_reference'],
                ]);
            } else {
                $response = Http::timeout(10)->post(self::PAYGATE_API_URL . '/v2/status', [
                    'auth_token' => self::PAYGATE_AUTH_TOKEN,
                    'identifier' => $validated['identifier'],
                ]);
            }

            if (!$response->successful()) {
                throw new \Exception('PayGate API returned error: ' . $response->status());
            }

            $data = $response->json();

            // Update local transaction if status changed
            if ($transaction && isset($data['status'])) {
                $paymentStatus = match ($data['status']) {
                    0 => 'completed',
                    2 => 'pending',
                    4 => 'expired',
                    6 => 'cancelled',
                    default => 'pending',
                };

                if ($transaction->status !== $paymentStatus) {
                    DB::beginTransaction();
                    
                    $transaction->update([
                        'status' => $paymentStatus,
                        'payment_reference' => $data['payment_reference'] ?? $transaction->payment_reference,
                        'metadata' => array_merge($transaction->metadata ?? [], [
                            'payment_reference' => $data['payment_reference'] ?? null,
                            'datetime' => $data['datetime'] ?? null,
                            'payment_method' => $data['payment_method'] ?? null,
                            'last_checked_at' => now()->toIso8601String(),
                        ]),
                    ]);

                    // Update order if payment completed
                    if ($paymentStatus === 'completed' && $transaction->order->payment_status !== 'paid') {
                        $transaction->order->update([
                            'payment_status' => 'paid',
                            'status' => 'processing',
                        ]);
                    }

                    DB::commit();
                }
            }

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Check payment status error: ' . $e->getMessage(), [
                'exception' => $e,
                'validated' => $validated,
            ]);
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
            1 => 'Transaction en attente',
            2 => 'Jeton d\'authentification invalide',
            3 => 'Solde insuffisant',
            4 => 'Paramètres invalides',
            5 => 'Numéro de téléphone invalide',
            6 => 'Une transaction avec le même identifiant existe déjà',
            7 => 'Transaction expirée',
            8 => 'Transaction annulée',
            9 => 'Réseau non disponible',
            10 => 'Montant invalide',
            default => 'Erreur inconnue (Code: ' . $status . ')',
        };
    }
}

