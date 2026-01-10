<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $query = Promotion::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
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

        $promotions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.promotions.index', compact('promotions'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        return view('admin.promotions.create', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'type' => 'required|in:percentage,fixed,free_shipping',
            'discount_value' => 'nullable|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('promotions', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        Promotion::create($validated);

        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promotion créée avec succès');
    }

    public function edit(Promotion $promotion)
    {
        $categories = Category::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        return view('admin.promotions.edit', compact('promotion', 'categories', 'products'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'type' => 'required|in:percentage,fixed,free_shipping',
            'discount_value' => 'nullable|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        if ($request->hasFile('image')) {
            if ($promotion->image) {
                Storage::disk('public')->delete($promotion->image);
            }
            $validated['image'] = $request->file('image')->store('promotions', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $promotion->update($validated);

        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promotion mise à jour avec succès');
    }

    public function destroy(Promotion $promotion)
    {
        if ($promotion->image) {
            Storage::disk('public')->delete($promotion->image);
        }

        $promotion->delete();

        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promotion supprimée avec succès');
    }
}
