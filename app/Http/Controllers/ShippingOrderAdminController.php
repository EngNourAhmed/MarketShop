<?php

namespace App\Http\Controllers;

use App\Models\ShippingOrderRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingOrderAdminController extends Controller
{
    public function index(Request $request)
    {
        $requests = ShippingOrderRequest::with(['product', 'supplier'])
            ->latest()
            ->get();

        $products = Product::with(['suppliers' => function ($query) {
            $query->orderBy('name');
        }])->orderBy('name')->get();

        return view('shipping_orders.index', [
            'requests' => $requests,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::with('suppliers')->find($validated['product_id']);
        if (!$product) {
            return redirect()
                ->route('shipping_orders.index')
                ->withErrors(['product_id' => 'المنتج غير موجود.']);
        }

        $suppliers = $product->suppliers ?? collect();
        $supplier = $suppliers->firstWhere('id', (int) $validated['supplier_id']);
        if (!$supplier) {
            return redirect()
                ->route('shipping_orders.index')
                ->withErrors(['supplier_id' => 'المورد المختار غير مرتبط بهذا المنتج.'])
                ->withInput();
        }

        $unitPrice = (float) ($supplier->pivot->price ?? 0);
        $quantity = (int) $validated['quantity'];
        $totalPrice = $unitPrice * $quantity;

        ShippingOrderRequest::create([
            'customer_id' => null,
            'product_id' => (int) $validated['product_id'],
            'supplier_id' => (int) $validated['supplier_id'],
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'order_id' => null,
            'created_by' => Auth::id() ?? 0,
        ]);

        return redirect()
            ->route('shipping_orders.index')
            ->with('status', 'تم إنشاء طلب التوريد بنجاح.');
    }
}
