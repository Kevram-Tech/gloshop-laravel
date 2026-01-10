<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromoCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = PromoCode::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $promoCodes = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.promo-codes.index', compact('promoCodes'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        return view('admin.promo-codes.create', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed,free_shipping',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
        ]);

        // Generate code if not provided
        if (empty($validated['code'])) {
            do {
                $validated['code'] = strtoupper(Str::random(8));
            } while (PromoCode::where('code', $validated['code'])->exists());
        } else {
            $validated['code'] = strtoupper($validated['code']);
        }

        $validated['is_active'] = $request->has('is_active');

        PromoCode::create($validated);

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Code promo créé avec succès');
    }

    public function edit(PromoCode $promoCode)
    {
        $categories = Category::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        return view('admin.promo-codes.edit', compact('promoCode', 'categories', 'products'));
    }

    public function update(Request $request, PromoCode $promoCode)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code,' . $promoCode->id,
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed,free_shipping',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        $promoCode->update($validated);

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Code promo mis à jour avec succès');
    }

    public function destroy(PromoCode $promoCode)
    {
        $promoCode->delete();

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Code promo supprimé avec succès');
    }
}
