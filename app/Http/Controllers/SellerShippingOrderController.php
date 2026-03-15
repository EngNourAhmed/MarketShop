<?php

namespace App\Http\Controllers;

use App\Models\ShippingOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SellerShippingOrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || ($user->role ?? null) !== 'supplier') {
            abort(403);
        }

        $supplier = $user->supplier;
        if (!$supplier) {
            abort(403);
        }

        $requests = ShippingOrderRequest::with(['product', 'customer', 'order'])
            ->where('supplier_id', $supplier->id)
            ->latest()
            ->get();

        return view('seller.shipping_orders.index', [
            'requests' => $requests,
            'supplier' => $supplier,
        ]);
    }

    public function update(Request $request, ShippingOrderRequest $shippingOrderRequest)
    {
        $user = $request->user();
        if (!$user || ($user->role ?? null) !== 'supplier') {
            abort(403);
        }

        $supplier = $user->supplier;
        if (!$supplier || (int) ($shippingOrderRequest->supplier_id ?? 0) !== (int) $supplier->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:available,cancelled'],
        ]);

        $newStatus = (string) ($validated['status'] ?? '');

        if ($newStatus === '' || (string) ($shippingOrderRequest->status ?? '') === $newStatus) {
            return redirect()->route('seller.shipping_orders.index');
        }

        $shippingOrderRequest->status = $newStatus;
        $shippingOrderRequest->save();

        return redirect()
            ->route('seller.shipping_orders.index')
            ->with('status', 'تم تحديث حالة طلب التوريد بنجاح.');
    }

    
}
