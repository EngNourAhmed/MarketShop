<?php

namespace App\Http\Controllers;

use App\Models\SpecialOrder;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SellerSpecialOrderController extends Controller
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

        $orders = SpecialOrder::with(['user', 'product'])
            ->where('supplier_id', $supplier->id)
            ->latest()
            ->get();

        return view('seller.special_orders.index', [
            'orders' => $orders,
            'supplier' => $supplier,
        ]);
    }

    public function update(Request $request, SpecialOrder $specialOrder)
    {
        $user = $request->user();
        if (!$user || ($user->role ?? null) !== 'supplier') {
            abort(403);
        }

        $supplier = $user->supplier;
        if (!$supplier || (int) ($specialOrder->supplier_id ?? 0) !== (int) $supplier->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:done,manufacturing,shipping,shipped,cancelled'],
        ]);

        $newStatus = (string) ($validated['status'] ?? '');

        if ($newStatus === '' || (string) ($specialOrder->status ?? '') === $newStatus) {
            return redirect()->route('seller.special_orders.index');
        }

        $specialOrder->status = $newStatus;
        $specialOrder->save();

        if ($newStatus === 'shipped') {
            $this->createOrderFromSpecialOrder($specialOrder, (int) $supplier->id);
        }

        return redirect()
            ->route('seller.special_orders.index')
            ->with('status', 'تم تحديث حالة الطلب الخاص بنجاح.');
    }

    protected function createOrderFromSpecialOrder(SpecialOrder $specialOrder, int $supplierId): void
    {
        $user = $specialOrder->user;
        if (!$user) {
            return;
        }

        $customer = Customer::query()->where('email', (string) ($user->email ?? ''))->first();

        if (!$customer) {
            $customer = new Customer();
            $customer->forceFill([
                'name' => (string) ($user->name ?? 'Customer'),
                'email' => (string) ($user->email ?? ''),
                'phone' => (string) ($user->phone ?? ''),
                'address' => (string) ($user->address ?? ''),
                'password' => Hash::make(Str::random(12)),
                'added_by' => Auth::id() ?? 0,
            ]);
            $customer->save();
        }

        if (!$customer) {
            return;
        }

        $price = (float) ($specialOrder->assigned_price ?? $specialOrder->budget ?? 0);
        if ($price <= 0) {
            return;
        }

        $noteValue = 'special_order:' . (string) $specialOrder->id;

        $existingOrder = Order::query()->where('note', $noteValue)->first();
        if ($existingOrder) {
            return;
        }

        $subtotal = $price;

        $order = new Order();
        $order->forceFill([
            'customer_id' => (int) $customer->id,
            'supplier_id' => $supplierId > 0 ? $supplierId : null,
            'status' => 'قيد التنفيذ',
            'subtotal' => $subtotal,
            'tax' => 0,
            'shipping' => 0,
            'discount' => 0,
            'total' => $subtotal,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'shipping_method' => 'standard',
            'shipping_status' => 'pending',
            'note' => $noteValue,
        ]);
        $order->order_code = 'ORD-' . strtoupper(Str::random(10));
        $order->payment_code = Str::random(10);
        $order->shipping_code = Str::random(10);
        $order->added_by = Auth::id() ?? 0;
        $order->save();

        $productId = (int) ($specialOrder->product_id ?? 0);
        if ($productId > 0) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'supplier_id' => $supplierId > 0 ? $supplierId : null,
                'quantity' => 1,
                'unit_price' => $price,
                'total' => $price,
            ]);
        }
    }
}
