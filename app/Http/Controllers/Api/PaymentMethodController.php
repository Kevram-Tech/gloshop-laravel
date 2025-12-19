<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class PaymentMethodController extends Controller
{
    /**
     * Display the user's payment methods.
     */
    public function index(): JsonResponse
    {
        $paymentMethods = PaymentMethod::where('user_id', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Mask sensitive data
        $paymentMethods->transform(function ($method) {
            if ($method->type === 'card' && $method->card_number) {
                $method->card_number = $this->maskCardNumber($method->card_number);
            }
            return $method;
        });

        return response()->json([
            'success' => true,
            'data' => $paymentMethods,
        ]);
    }

    /**
     * Store a newly created payment method.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:card,paypal,mobile_money',
            'card_number' => 'required_if:type,card|nullable|string',
            'card_holder' => 'required_if:type,card|nullable|string|max:255',
            'expiry_date' => 'required_if:type,card|nullable|string',
            'cvv' => 'required_if:type,card|nullable|string',
            'phone' => 'required_if:type,mobile_money|nullable|string',
            'provider' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            PaymentMethod::where('user_id', Auth::id())
                ->update(['is_default' => false]);
        }

        // Encrypt sensitive data
        if (isset($validated['cvv'])) {
            $validated['cvv'] = Crypt::encryptString($validated['cvv']);
        }
        if (isset($validated['card_number'])) {
            $validated['card_number'] = Crypt::encryptString($validated['card_number']);
        }

        $paymentMethod = PaymentMethod::create([
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'card_number' => $validated['card_number'] ?? null,
            'card_holder' => $validated['card_holder'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'cvv' => $validated['cvv'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'provider' => $validated['provider'] ?? null,
            'is_default' => $validated['is_default'] ?? false,
        ]);

        // Mask card number in response
        if ($paymentMethod->card_number) {
            $paymentMethod->card_number = $this->maskCardNumber(Crypt::decryptString($paymentMethod->card_number));
        }

        return response()->json([
            'success' => true,
            'message' => 'Moyen de paiement ajouté avec succès',
            'data' => $paymentMethod,
        ], 201);
    }

    /**
     * Update the specified payment method.
     */
    public function update(Request $request, PaymentMethod $paymentMethod): JsonResponse
    {
        if ($paymentMethod->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        $validated = $request->validate([
            'card_holder' => 'sometimes|string|max:255',
            'expiry_date' => 'sometimes|string',
            'phone' => 'sometimes|string',
            'provider' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
        ]);

        // If this is set as default, unset other defaults
        if (isset($validated['is_default']) && $validated['is_default']) {
            PaymentMethod::where('user_id', Auth::id())
                ->where('id', '!=', $paymentMethod->id)
                ->update(['is_default' => false]);
        }

        $paymentMethod->update($validated);

        // Mask card number in response
        if ($paymentMethod->card_number) {
            $paymentMethod->card_number = $this->maskCardNumber(Crypt::decryptString($paymentMethod->card_number));
        }

        return response()->json([
            'success' => true,
            'message' => 'Moyen de paiement mis à jour avec succès',
            'data' => $paymentMethod,
        ]);
    }

    /**
     * Remove the specified payment method.
     */
    public function destroy(PaymentMethod $paymentMethod): JsonResponse
    {
        if ($paymentMethod->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        $paymentMethod->delete();

        return response()->json([
            'success' => true,
            'message' => 'Moyen de paiement supprimé avec succès',
        ]);
    }

    /**
     * Set a payment method as default.
     */
    public function setDefault(PaymentMethod $paymentMethod): JsonResponse
    {
        if ($paymentMethod->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        // Unset other defaults
        PaymentMethod::where('user_id', Auth::id())
            ->where('id', '!=', $paymentMethod->id)
            ->update(['is_default' => false]);

        $paymentMethod->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Moyen de paiement par défaut mis à jour',
            'data' => $paymentMethod,
        ]);
    }

    /**
     * Mask card number for display.
     */
    private function maskCardNumber(string $cardNumber): string
    {
        $cardNumber = preg_replace('/\s+/', '', $cardNumber);
        if (strlen($cardNumber) <= 4) {
            return str_repeat('*', strlen($cardNumber));
        }
        return '**** **** **** ' . substr($cardNumber, -4);
    }
}
