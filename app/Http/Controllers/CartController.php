<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductPricingTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::with(['user', 'product'])->latest()->paginate(10);

        return response()->json($cart);
    }

    public function shopIndex(Request $request)
    {
        $items = Cart::with(['product', 'supplier'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $user = Auth::user();
        $availablePoints = 0;
        if ($user) {
            $customer = Customer::query()->where('email', (string) ($user->email ?? ''))->first();
            $availablePoints = (int) ($customer?->latestCard?->points_remaining ?? 0);
        }

        return view('shop.cart.index', [
            'items' => $items,
            'availablePoints' => $availablePoints,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'supplier_id' => 'required|exists:suppliers,id',
            'color' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
        ]);

        $product = Product::query()->findOrFail($validated['product_id']);
        $quantity = (int) $validated['quantity'];
        $supplierId = (int) $validated['supplier_id'];

        $existingSupplierId = (int) (Cart::query()
            ->where('user_id', Auth::id())
            ->whereNotNull('supplier_id')
            ->value('supplier_id') ?? 0);

        if ($existingSupplierId > 0 && $existingSupplierId !== $supplierId) {
            $message = 'لا يمكن إضافة منتجات من موردين مختلفين في نفس السلة. قم بإفراغ السلة أولاً أو اختر نفس المورد.';
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                ], 422);
            }

            return back()->withErrors([
                'supplier_id' => $message,
            ])->withInput();
        }

        $supplier = $product->suppliers()
            ->where('suppliers.id', $supplierId)
            ->first();

        if (! $supplier) {
            $message = 'المورد المحدد غير متاح لهذا المنتج.';
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                ], 422);
            }

            return back()->withErrors([
                'supplier_id' => $message,
            ])->withInput();
        }

        $baseUnitPrice = (float) ($supplier->pivot->unit_price ?? $supplier->pivot->price ?? 0);

        $tierUnitPrice = (float) (ProductPricingTier::query()
            ->where('product_id', (int) $product->id)
            ->where('supplier_id', $supplierId)
            ->where('min_quantity', '<=', $quantity)
            ->where(function ($q) use ($quantity) {
                $q->whereNull('max_quantity')
                    ->orWhere('max_quantity', '>=', $quantity);
            })
            ->orderByDesc('min_quantity')
            ->value('price_per_unit') ?? 0);

        $unitPrice = $tierUnitPrice > 0 ? $tierUnitPrice : $baseUnitPrice;
        $total = $unitPrice * $quantity;

        $cart = new Cart;
        $cart->user_id = Auth::id();
        $cart->product_id = (int) $product->id;
        $cart->supplier_id = $supplierId;
        $cart->quantity = $quantity;
        $cart->color = $validated['color'] ?? null;
        $cart->size = $validated['size'] ?? null;
        $cart->price = $unitPrice;
        $cart->total = $total;
        $cart->discount = 0;
        $cart->total_after_discount = $total;
        $cart->tax = 0;
        $cart->total_after_tax = $total;
        $cart->shipping_cost = 0;
        $cart->total_after_shipping = $total;
        $cart->total_after_all = $total;
        $cart->save();

        if ($request->wantsJson()) {
            return response()->json($cart, 201);
        }

        return redirect()->route('shop.cart.index')->with('success', 'Added to cart');
    }

    public function show($id)
    {
        $cart = Cart::with(['user', 'product'])->findOrFail($id);

        return response()->json($cart);
    }

    public function destroy($id)
    {
        $cart = Cart::query()->where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $cart->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Cart item deleted successfully']);
        }

        return back()->with('success', 'Removed from cart');
    }

    public function update(Request $request, $id)
    {
        $cart = Cart::query()->where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1',
            'delta' => 'nullable|integer',
        ]);

        $currentQty = (int) ($cart->quantity ?? 1);
        $nextQty = $currentQty;
        if (array_key_exists('quantity', $validated) && $validated['quantity'] !== null) {
            $nextQty = (int) $validated['quantity'];
        } elseif (array_key_exists('delta', $validated) && $validated['delta'] !== null) {
            $nextQty = $currentQty + (int) $validated['delta'];
        }
        $nextQty = max(1, $nextQty);

        $product = Product::query()->find($cart->product_id);
        $supplierId = (int) ($cart->supplier_id ?? 0);

        $baseUnitPrice = 0.0;
        if ($product && $supplierId > 0) {
            $supplier = $product->suppliers()
                ->where('suppliers.id', $supplierId)
                ->first();
            $baseUnitPrice = (float) ($supplier?->pivot?->unit_price ?? $supplier?->pivot?->price ?? 0);
        }

        $tierUnitPrice = 0.0;
        if ($product && $supplierId > 0) {
            $tierUnitPrice = (float) (ProductPricingTier::query()
                ->where('product_id', (int) $cart->product_id)
                ->where('supplier_id', $supplierId)
                ->where('min_quantity', '<=', $nextQty)
                ->where(function ($q) use ($nextQty) {
                    $q->whereNull('max_quantity')
                        ->orWhere('max_quantity', '>=', $nextQty);
                })
                ->orderByDesc('min_quantity')
                ->value('price_per_unit') ?? 0);
        }

        $unitPrice = $tierUnitPrice > 0 ? $tierUnitPrice : $baseUnitPrice;
        if ($unitPrice <= 0) {
            $unitPrice = (float) ($cart->price ?? 0);
        }

        $total = $unitPrice * $nextQty;

        $cart->quantity = $nextQty;
        $cart->total = $total;
        $cart->price = $unitPrice;
        $cart->discount = 0;
        $cart->total_after_discount = $total;
        $cart->tax = 0;
        $cart->total_after_tax = $total;
        $cart->shipping_cost = 0;
        $cart->total_after_shipping = $total;
        $cart->total_after_all = $total;
        $cart->save();

        if ($request->wantsJson()) {
            return response()->json($cart);
        }

        return back();
    }

    public function pdf($id)
    {
        $cart = Cart::with(['user', 'product'])->findOrFail($id);

        return response()->json(['message' => 'PDF generation not implemented', 'data' => $cart]);
    }

    public function excel($id)
    {
        $cart = Cart::with(['user', 'product'])->findOrFail($id);

        return response()->json(['message' => 'Excel export not implemented', 'data' => $cart]);
    }

    public function csv($id)
    {
        $cart = Cart::with(['user', 'product'])->findOrFail($id);

        return response()->json(['message' => 'CSV export not implemented', 'data' => $cart]);
    }
}
