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
    /**
     * Get PayGate configuration
     */
    private function getPayGateConfig(): array
    {
        return config('services.paygateglobal');
    }

    /**
     * Méthode 1: Initiate PayGate payment via API (T-Money/Flooz)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function initiatePayGate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'phone_number' => 'required|string',
            'network' => 'required|in:FLOOZ,TMONEY',
            'description' => 'nullable|string|max:255',
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

        // Check if transaction already exists
        $existingTransaction = PaymentTransaction::where('order_id', $order->id)
            ->where('status', 'pending')
            ->first();

        if ($existingTransaction) {
            return response()->json([
                'success' => false,
                'message' => 'Une transaction est déjà en cours pour cette commande',
                'data' => [
                    'tx_reference' => $existingTransaction->tx_reference,
                    'transaction_id' => $existingTransaction->id,
                ],
            ], 400);
        }

        try {
            $config = $this->getPayGateConfig();
            
            $response = Http::timeout(30)->post($config['api_url'] . '/pay', [
                'auth_token' => $config['auth_token'],
                'phone_number' => $validated['phone_number'],
                'amount' => (int) round($order->total_amount), // Montant sans décimales (FCFA)
                'description' => $validated['description'] ?? "Paiement commande #{$order->order_number}",
                'identifier' => $order->order_number, // Identifiant unique
                'network' => $validated['network'],
            ]);

            $data = $response->json();

            // Log the response for debugging
            Log::info('PayGate API Response', [
                'order_id' => $order->id,
                'response' => $data,
            ]);

            if (isset($data['status']) && $data['status'] == 0) {
                // Transaction enregistrée avec succès
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
                        'description' => $validated['description'] ?? "Paiement commande #{$order->order_number}",
                        'initiated_at' => now()->toDateTimeString(),
                    ],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Paiement initié avec succès. Veuillez confirmer le paiement sur votre téléphone.',
                    'data' => [
                        'tx_reference' => $data['tx_reference'],
                        'transaction_id' => $transaction->id,
                        'order_id' => $order->id,
                        'status' => 'pending',
                    ],
                ]);
            } else {
                // Handle error status codes
                $errorMessage = $this->getPayGateErrorMessage($data['status'] ?? -1);
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error_code' => $data['status'] ?? -1,
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('PayGate payment initiation error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'initiation du paiement. Veuillez réessayer.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Méthode 2: Generate payment page URL for redirection
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function generatePaymentPageUrl(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'nullable|numeric|min:1',
            'description' => 'nullable|string|max:255',
            'phone' => 'nullable|string',
            'network' => 'nullable|in:FLOOZ,TMONEY,MOOV,TOGOCEL',
            'return_url' => 'nullable|url',
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
            $config = $this->getPayGateConfig();
            $amount = $validated['amount'] ?? (int) round($order->total_amount);
            
            // Build query parameters
            $params = [
                'token' => $config['auth_token'],
                'amount' => $amount,
                'description' => $validated['description'] ?? "Paiement commande #{$order->order_number}",
                'identifier' => $order->order_number,
            ];

            // Optional parameters
            if (isset($validated['return_url'])) {
                $params['url'] = $validated['return_url'];
            } else {
                $params['url'] = $config['callback_url'];
            }

            if (isset($validated['phone'])) {
                $params['phone'] = $validated['phone'];
            }

            if (isset($validated['network'])) {
                $params['network'] = $validated['network'];
            }

            // Create transaction record
            $transaction = PaymentTransaction::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'payment_method' => 'paygate_page',
                'amount' => $order->total_amount,
                'identifier' => $order->order_number,
                'status' => 'pending',
                'metadata' => [
                    'payment_method_type' => 'page_redirect',
                    'phone' => $validated['phone'] ?? null,
                    'network' => $validated['network'] ?? null,
                    'return_url' => $params['url'],
                ],
            ]);

            // Build payment page URL
            $paymentUrl = $config['page_url'] . '?' . http_build_query($params);

            return response()->json([
                'success' => true,
                'message' => 'URL de paiement générée avec succès',
                'data' => [
                    'payment_url' => $paymentUrl,
                    'transaction_id' => $transaction->id,
                    'order_id' => $order->id,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('PayGate payment page URL generation error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération de l\'URL de paiement',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Handle PayGate callback (confirmation de paiement)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function payGateCallback(Request $request): JsonResponse
    {
        $data = $request->all();

        Log::info('PayGate callback received', $data);

        try {
            // Find transaction by tx_reference or identifier
            $transaction = PaymentTransaction::where('tx_reference', $data['tx_reference'] ?? null)
                ->orWhere('identifier', $data['identifier'] ?? null)
                ->first();

            if (!$transaction) {
                Log::warning('PayGate callback: Transaction not found', $data);
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            DB::beginTransaction();

            // Update transaction with callback data
            $updateData = [
                'payment_reference' => $data['payment_reference'] ?? $transaction->payment_reference,
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'payment_reference' => $data['payment_reference'] ?? null,
                    'datetime' => $data['datetime'] ?? null,
                    'payment_method' => $data['payment_method'] ?? null,
                    'phone_number' => $data['phone_number'] ?? null,
                    'amount' => $data['amount'] ?? $transaction->amount,
                    'callback_received_at' => now()->toDateTimeString(),
                ]),
            ];

            // Update transaction status based on payment status
            // Note: PayGate sends callback only when payment is successful
            $updateData['status'] = 'completed';

            $transaction->update($updateData);

            // Update order if payment is completed
            $order = $transaction->order;
            if ($order && $order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => $order->status === 'pending' ? 'processing' : $order->status,
                    'payment_method' => $data['payment_method'] ?? $transaction->payment_method,
                ]);
            }

            DB::commit();

            Log::info('PayGate callback processed successfully', [
                'transaction_id' => $transaction->id,
                'order_id' => $order->id ?? null,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PayGate callback error', [
                'error' => $e->getMessage(),
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement du callback'
            ], 500);
        }
    }

    /**
     * Check payment status (Vérifier l'état d'un Paiement)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function checkPaymentStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tx_reference' => 'required_without:identifier|string',
            'identifier' => 'required_without:tx_reference|string',
        ]);

        try {
            $config = $this->getPayGateConfig();
            
            if (isset($validated['tx_reference'])) {
                // Méthode 1: Vérification par tx_reference
                $response = Http::timeout(30)->post($config['api_url'] . '/status', [
                    'auth_token' => $config['auth_token'],
                    'tx_reference' => $validated['tx_reference'],
                ]);
            } else {
                // Méthode 2: Vérification par identifier (v2 API)
                $response = Http::timeout(30)->post($config['api_url'] . '/v2/status', [
                    'auth_token' => $config['auth_token'],
                    'identifier' => $validated['identifier'],
                ]);
            }

            $data = $response->json();

            // Map PayGate status codes to our status
            $statusMap = [
                0 => 'completed',  // Paiement réussi
                2 => 'pending',     // En cours
                4 => 'failed',      // Expiré
                6 => 'cancelled',   // Annulé
            ];

            $status = $statusMap[$data['status'] ?? -1] ?? 'pending';

            // Update local transaction if exists
            $transaction = PaymentTransaction::where('tx_reference', $data['tx_reference'] ?? null)
                ->orWhere('identifier', $validated['identifier'] ?? $data['identifier'] ?? null)
                ->first();

            if ($transaction && $transaction->status !== $status) {
                DB::beginTransaction();
                
                $transaction->update([
                    'status' => $status,
                    'payment_reference' => $data['payment_reference'] ?? $transaction->payment_reference,
                    'metadata' => array_merge($transaction->metadata ?? [], [
                        'last_status_check' => now()->toDateTimeString(),
                        'paygate_status' => $data['status'] ?? null,
                        'datetime' => $data['datetime'] ?? null,
                        'payment_method' => $data['payment_method'] ?? null,
                    ]),
                ]);

                // Update order if payment is completed
                if ($status === 'completed' && $transaction->order && $transaction->order->payment_status !== 'paid') {
                    $transaction->order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing',
                    ]);
                }

                DB::commit();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'tx_reference' => $data['tx_reference'] ?? null,
                    'identifier' => $data['identifier'] ?? $validated['identifier'] ?? null,
                    'payment_reference' => $data['payment_reference'] ?? null,
                    'status' => $status,
                    'status_code' => $data['status'] ?? null,
                    'datetime' => $data['datetime'] ?? null,
                    'payment_method' => $data['payment_method'] ?? null,
                    'amount' => $data['amount'] ?? null,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Check payment status error', [
                'error' => $e->getMessage(),
                'request' => $validated,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification du statut',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Check PayGate balance (Consulter votre Solde)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function checkBalance(Request $request): JsonResponse
    {
        // This endpoint should be protected and only accessible from whitelisted IPs
        // For now, we'll require admin authentication
        
        try {
            $config = $this->getPayGateConfig();
            
            $response = Http::timeout(30)->post($config['api_url'] . '/check-balance', [
                'auth_token' => $config['auth_token'],
            ]);

            $data = $response->json();

            return response()->json([
                'success' => true,
                'data' => [
                    'flooz_balance' => $data['flooz'] ?? 0,
                    'tmoney_balance' => $data['tmoney'] ?? 0,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Check PayGate balance error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification du solde',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Process card payment (Visa/Mastercard)
     * Note: This is a placeholder for future card payment integration
     * 
     * @param Request $request
     * @return JsonResponse
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
            Log::error('Card payment error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement du paiement',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get PayGate error message
     * 
     * @param int $status
     * @return string
     */
    private function getPayGateErrorMessage(int $status): string
    {
        return match ($status) {
            0 => 'Transaction enregistrée avec succès',
            2 => 'Jeton d\'authentification invalide',
            4 => 'Paramètres invalides',
            6 => 'Doublons détectées. Une transaction avec le même identifiant existe déjà.',
            default => 'Erreur inconnue (Code: ' . $status . ')',
        };
    }
}
