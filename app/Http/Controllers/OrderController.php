<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResponse;
use App\Models\Card;
use App\Models\Cart;
use App\Models\Commission;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SpecialOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function shopCheckout(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $items = Cart::with(['product', 'supplier'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('shop.cart.index')->with('success', 'Cart is empty');
        }

        $customer = Customer::query()->where('email', (string) ($user->email ?? ''))->first();

        $card = null;
        $availablePoints = 0;
        if ($customer) {
            $card = $customer->latestCard()->first();
            $availablePoints = (int) ($card?->points_remaining ?? 0);
        }

        $subtotal = (float) $items->sum(function ($item) {
            return (float) ($item->total_after_all ?? $item->total ?? 0);
        });

        $deliveryMethod = (string) $request->query('delivery_method', 'standard');
        $shipping = $deliveryMethod === 'express' ? 50.0 : 20.0;

        $applyPoints = (bool) ((int) $request->query('apply_points', 0) === 1);

        return view('shop.checkout.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'deliveryMethod' => $deliveryMethod,
            'availablePoints' => $availablePoints,
            'applyPoints' => $applyPoints,
            'prefill' => [
                'name' => (string) ($user->name ?? ''),
                'phone' => (string) ($user->phone ?? ''),
                'address' => (string) ($user->address ?? ''),
            ],
        ]);
    }

    public function shopIndex(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $type = (string) $request->query('type', 'orders');
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');

        if ($type === 'special') {
            $specialOrdersQuery = SpecialOrder::query()
                ->where('user_id', $user->id)
                ->latest();

            if ($status !== '' && $status !== 'all') {
                $specialOrdersQuery->where('status', $status);
            }

            if ($q !== '') {
                $specialOrdersQuery->where(function ($query) use ($q) {
                    $query->where('title', 'like', '%'.$q.'%')
                        ->orWhere('product_name', 'like', '%'.$q.'%')
                        ->orWhere('status', 'like', '%'.$q.'%');
                });
            }

            $specialOrders = $specialOrdersQuery->get();

            return view('shop.orders.index', [
                'orders' => collect(),
                'specialOrders' => $specialOrders,
                'q' => $q,
                'status' => $status,
                'type' => $type,
            ]);
        }

        $customer = Customer::query()->where('email', (string) ($user->email ?? ''))->first();
        if (! $customer) {
            return view('shop.orders.index', [
                'orders' => collect(),
                'specialOrders' => collect(),
                'q' => $q,
                'status' => $status,
                'type' => $type,
            ]);
        }

        $ordersQuery = Order::query()
            ->with(['items.product.suppliers'])
            ->where('customer_id', $customer->id)
            ->latest();

        if ($status !== '' && $status !== 'all') {
            $ordersQuery->where('status', $status);
        }

        if ($q !== '') {
            $ordersQuery->where(function ($query) use ($q) {
                $query->where('order_code', 'like', '%'.$q.'%')
                    ->orWhere('status', 'like', '%'.$q.'%');
            });
        }

        $orders = $ordersQuery->get();

        return view('shop.orders.index', [
            'orders' => $orders,
            'specialOrders' => collect(),
            'q' => $q,
            'status' => $status,
            'type' => $type,
        ]);
    }

    public function shopInvoice($id)
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $customer = Customer::query()->where('email', (string) ($user->email ?? ''))->first();
        if (! $customer) {
            abort(404);
        }

        $order = Order::where('customer_id', $customer->id)
            ->with(['items.product', 'shipping'])
            ->findOrFail($id);

        return view('shop.orders.invoice', compact('order'));
    }

    public function index(Request $request)
    {
        $orders = Order::with(['customer', 'invoice', 'shipping', 'items', 'returns'])->latest()->get();
        $products = Product::query()->orderBy('name')->get(['id', 'name', 'price']);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(OrderResponse::collection($orders));
        }

        return view('orders.index', [
            'orders' => $orders,
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name', 'phone']),
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        if ($request->routeIs('shop.orders.store')) {
            $user = Auth::user();
            if (! $user) {
                abort(403);
            }

            $validatedCheckout = $request->validate([
                'full_name' => 'required|string|max:255',
                'address_line_1' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:50',
                'phone' => 'required|string|max:50',
                'delivery_method' => 'required|in:standard,express',
                'payment_method' => 'required|in:card,wallet,cod',
                'apply_points' => 'nullable|boolean',
            ]);

            $items = Cart::with(['product'])
                ->where('user_id', Auth::id())
                ->latest()
                ->get();

            if ($items->isEmpty()) {
                return redirect()->route('shop.cart.index')->with('success', 'Cart is empty');
            }

            $customer = Customer::query()->where('email', (string) ($user->email ?? ''))->first();
            if (! $customer) {
                $customer = new Customer;
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

            $subtotal = (float) $items->sum(function ($item) {
                return (float) ($item->total_after_all ?? $item->total ?? 0);
            });

            $deliveryMethod = (string) ($validatedCheckout['delivery_method'] ?? 'standard');
            $shipping = $deliveryMethod === 'express' ? 50.0 : 20.0;

            $paymentMethod = (string) ($validatedCheckout['payment_method'] ?? 'cod');
            $paymentMethodDb = $paymentMethod === 'cod' ? 'cash' : $paymentMethod;

            $applyPoints = (bool) ($validatedCheckout['apply_points'] ?? false);
            $pointsRateEgp = 1; // 1 point = 1 EGP (can be adjusted later)

            $supplierId = (int) ($items->first()?->supplier_id ?? 0);

            $order = DB::transaction(function () use (
                $customer,
                $supplierId,
                $subtotal,
                $shipping,
                $deliveryMethod,
                $paymentMethodDb,
                $applyPoints,
                $pointsRateEgp,
                $validatedCheckout,
                $items
            ) {
                $discount = 0.0;
                $pointsUsed = 0;

                if ($applyPoints && $pointsRateEgp > 0) {
                    $latestCard = $customer->latestCard()->first();
                    if ($latestCard) {
                        $card = Card::query()->where('id', $latestCard->id)->lockForUpdate()->first();
                        $available = (int) ($card?->points_remaining ?? 0);
                        if ($available > 0) {
                            $maxUsablePoints = (int) floor($subtotal / $pointsRateEgp);
                            $pointsUsed = min($available, max(0, $maxUsablePoints));
                            $discount = $pointsUsed * $pointsRateEgp;

                            if ($card) {
                                $card->points_used = (int) ($card->points_used ?? 0) + $pointsUsed;
                                $card->points_remaining = max(0, (int) ($card->points_remaining ?? 0) - $pointsUsed);
                                $card->save();
                            }
                        }
                    }
                }

                $total = max(0.0, ($subtotal + $shipping) - $discount);

                $addressParts = array_filter([
                    (string) ($validatedCheckout['address_line_1'] ?? ''),
                    (string) ($validatedCheckout['city'] ?? ''),
                    (string) ($validatedCheckout['state'] ?? ''),
                    (string) ($validatedCheckout['postal_code'] ?? ''),
                    (string) ($validatedCheckout['phone'] ?? ''),
                ], fn ($v) => trim((string) $v) !== '');
                $addressText = implode(' | ', $addressParts);

                $order = new Order;
                $order->forceFill([
                    'customer_id' => (int) $customer->id,
                    'supplier_id' => $supplierId > 0 ? $supplierId : null,
                    'status' => 'قيد التنفيذ',
                    'subtotal' => $subtotal,
                    'tax' => 0,
                    'shipping' => $shipping,
                    'discount' => $discount,
                    'total' => $total,
                    'payment_method' => $paymentMethodDb,
                    'payment_status' => 'pending',
                    'shipping_method' => $deliveryMethod,
                    'shipping_status' => 'pending',
                    'note' => $addressText !== '' ? ('Checkout: '.$addressText.' | points_used='.$pointsUsed) : ('points_used='.$pointsUsed),
                ]);
                $order->order_code = 'ORD-'.strtoupper(Str::random(10));
                $order->payment_code = Str::random(10);
                $order->shipping_code = Str::random(10);
                $order->added_by = Auth::id() ?? 0;
                $order->save();

                foreach ($items as $item) {
                    if (! $item->product) {
                        continue;
                    }
                    $qty = (int) ($item->quantity ?? 1);
                    $unit = (float) ($item->price ?? $item->product->price ?? 0);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => (int) $item->product->id,
                        'supplier_id' => (int) ($item->supplier_id ?? 0) ?: null,
                        'quantity' => $qty,
                        'color' => $item->color ?? null,
                        'size' => $item->size ?? null,
                        'unit_price' => $unit,
                        'total' => $qty * $unit,
                    ]);
                }

                Cart::query()->where('user_id', Auth::id())->delete();

                return $order;
            });

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json($order->load('items'), 201);
            }

            if (in_array($paymentMethod, ['card', 'wallet'])) {
                return redirect()->route('paymob.pay', ['orderId' => $order->id]);
            }

            return redirect()->route('shop.orders.index')->with('success', 'Order placed');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'total' => 'required|numeric',
            'status' => 'required|string',
            'product_id' => 'nullable|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'subtotal' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'shipping' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'payment_method' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'shipping_method' => 'nullable|string',
            'shipping_status' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $productId = $validated['product_id'] ?? null;
        $quantity = $validated['quantity'] ?? null;
        unset($validated['product_id'], $validated['quantity']);

        $validated['subtotal'] = $validated['subtotal'] ?? 0;
        $validated['tax'] = $validated['tax'] ?? 0;
        $validated['shipping'] = $validated['shipping'] ?? 0;
        $validated['discount'] = $validated['discount'] ?? 0;
        $validated['payment_method'] = $validated['payment_method'] ?? 'cash';
        $validated['payment_status'] = $validated['payment_status'] ?? 'pending';
        $validated['shipping_method'] = $validated['shipping_method'] ?? 'standard';
        $validated['shipping_status'] = $validated['shipping_status'] ?? 'pending';
        $validated['note'] = $validated['note'] ?? '';

        // return response()->json(['message' => 'Order created successfully'], 201);
        $order = new Order;
        $order->forceFill($validated);
        $order->order_code = 'ORD-'.strtoupper(Str::random(10));
        $order->payment_code = Str::random(10);
        $order->shipping_code = Str::random(10);
        $order->note = $validated['note'];
        $order->added_by = Auth::id() ?? 0;
        $order->save();

        if (! empty($productId) && ! empty($quantity)) {
            $product = Product::with(['suppliers', 'pricingTiers'])->find($productId);
            if ($product) {
                $qty = (int) $quantity;
                $supplierId = (int) ($request->input('supplier_id') ?? 0);
                
                // Determine unit price based on tiers or supplier base price
                $unit = (float) ($product->price ?? 0);
                if ($supplierId > 0) {
                    $supplier = $product->suppliers->find($supplierId);
                    if ($supplier) {
                        $baseUnitPrice = (float) ($supplier->pivot->unit_price ?? $supplier->pivot->price ?? 0);
                        
                        $tierPrice = (float) (ProductPricingTier::query()
                            ->where('product_id', $product->id)
                            ->where('supplier_id', $supplierId)
                            ->where('min_quantity', '<=', $qty)
                            ->where(function ($q) use ($qty) {
                                $q->whereNull('max_quantity')
                                    ->or('max_quantity', '>=', $qty);
                            })
                            ->orderByDesc('min_quantity')
                            ->value('price_per_unit') ?? 0);
                        
                        $unit = $tierPrice > 0 ? $tierPrice : $baseUnitPrice;
                    }
                }
                
                $lineTotal = $qty * $unit;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'supplier_id' => $supplierId > 0 ? $supplierId : null,
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'total' => $lineTotal,
                ]);
            }
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($order, 201);
        }

        return redirect()->route('orders.index')->with('status', 'تمت الإضافة بنجاح');
    }

    public function show(Request $request, $id)
    {
        $order = Order::with(['customer', 'invoice', 'shipping', 'items'])->findOrFail($id);
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($order);
        }

        return redirect()->route('orders.index')->with('status', 'تم التعديل بنجاح');
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $originalStatus = (string) ($order->status ?? '');

        $validated = $request->validate([
            'customer_id' => 'sometimes|exists:customers,id',
            'status' => 'sometimes|string',
            'total' => 'sometimes|numeric',
            'product_id' => 'nullable|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'subtotal' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'shipping' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'payment_method' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'shipping_method' => 'nullable|string',
            'shipping_status' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $productId = $validated['product_id'] ?? null;
        $quantity = $validated['quantity'] ?? null;
        unset($validated['product_id'], $validated['quantity']);

        foreach (['subtotal', 'tax', 'shipping', 'discount'] as $k) {
            if (array_key_exists($k, $validated) && $validated[$k] === null) {
                $validated[$k] = 0;
            }
        }
        foreach (['payment_method', 'payment_status', 'shipping_method', 'shipping_status'] as $k) {
            if (array_key_exists($k, $validated) && $validated[$k] === null) {
                unset($validated[$k]);
            }
        }
        if (array_key_exists('note', $validated) && $validated['note'] === null) {
            $validated['note'] = '';
        }

        $order->forceFill($validated);
        $order->save();

        $isDelivered = function (?string $status): bool {
            $s = mb_strtolower(trim((string) $status));
            if ($s === 'delivered' || $s === 'تم التوصيل') {
                return true;
            }
            if (str_contains($s, 'deliver') || str_contains($s, 'توصيل')) {
                return true;
            }

            return false;
        };

        if (array_key_exists('status', $validated)) {
            $newStatus = (string) ($order->status ?? '');

            if ($isDelivered($newStatus)) {
                $supplierId = (int) ($order->supplier_id ?? 0);
                if ($supplierId <= 0) {
                    $supplierId = (int) (OrderItem::query()
                        ->where('order_id', $order->id)
                        ->whereNotNull('supplier_id')
                        ->value('supplier_id') ?? 0);
                }

                if ($supplierId > 0) {
                    $supplier = Supplier::query()->find($supplierId);
                    $commissionPercent = (float) ($supplier?->commission_percent ?? 0);
                    $orderAmount = (float) ($order->total ?? 0);
                    $taxAmount = (float) ($order->tax ?? 0);
                    $commissionAmount = $orderAmount * $commissionPercent / 100;

                    Commission::updateOrCreate(
                        [
                            'order_id' => $order->id,
                            'supplier_id' => $supplierId,
                            'commission_type' => 'supplier',
                        ],
                        [
                            'user_id' => Auth::id() ?? 0,
                            'sale_id' => null,
                            'commission_type_id' => $supplierId,
                            'commission' => $commissionAmount,
                            'order_amount' => $orderAmount,
                            'tax_amount' => $taxAmount,
                            'date' => now()->toDateString(),
                            'status' => 'active',
                        ]
                    );
                }
            } elseif ($isDelivered($originalStatus) && ! $isDelivered((string) ($order->status ?? ''))) {
                Commission::query()
                    ->where('order_id', $order->id)
                    ->delete();
            }
        }

        if (! empty($productId) && ! empty($quantity)) {
            $product = Product::with(['suppliers', 'pricingTiers'])->find($productId);
            if ($product) {
                $qty = (int) $quantity;
                $supplierId = (int) ($request->input('supplier_id') ?? 0);

                // Determine unit price based on tiers or supplier base price
                $unit = (float) ($product->price ?? 0);
                if ($supplierId > 0) {
                    $supplier = $product->suppliers->find($supplierId);
                    if ($supplier) {
                        $baseUnitPrice = (float) ($supplier->pivot->unit_price ?? $supplier->pivot->price ?? 0);
                        
                        $tierPrice = (float) (ProductPricingTier::query()
                            ->where('product_id', $product->id)
                            ->where('supplier_id', $supplierId)
                            ->where('min_quantity', '<=', $qty)
                            ->where(function ($q) use ($qty) {
                                $q->whereNull('max_quantity')
                                    ->or('max_quantity', '>=', $qty);
                            })
                            ->orderByDesc('min_quantity')
                            ->value('price_per_unit') ?? 0);
                        
                        $unit = $tierPrice > 0 ? $tierPrice : $baseUnitPrice;
                    }
                }

                $lineTotal = $qty * $unit;

                OrderItem::query()->where('order_id', $order->id)->delete();
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'supplier_id' => $supplierId > 0 ? $supplierId : null,
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'total' => $lineTotal,
                ]);
            }
        } elseif ($productId === null && $quantity === null && ($request->has('product_id') || $request->has('quantity'))) {
            // If user cleared the product/quantity inputs, remove existing items.
            OrderItem::query()->where('order_id', $order->id)->delete();
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($order);
        }

        return redirect()->route('orders.index')->with('status', 'تم التعديل بنجاح');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Order deleted successfully']);
        }

        return redirect()->route('orders.index')->with('status', 'تم الحذف بنجاح');
    }

    public function print($id)
    {
        $order = Order::with(['customer', 'invoice', 'shipping'])->findOrFail($id);

        return response()->json(['message' => 'Print view not implemented', 'data' => $order]);
    }

    public function pdf($id)
    {
        $order = Order::with(['customer', 'invoice', 'shipping'])->findOrFail($id);

        return response()->json(['message' => 'PDF generation not implemented', 'data' => $order]);
    }

    public function excel($id)
    {
        $order = Order::with(['customer', 'invoice', 'shipping'])->findOrFail($id);

        return response()->json(['message' => 'Excel export not implemented', 'data' => $order]);
    }

    public function invoice($id)
    {
        $order = Order::with(['customer', 'items.product', 'items.supplier', 'shipping'])->findOrFail($id);

        $user = Auth::user();
        $isAdmin = $user && (($user->role ?? '') === 'admin');

        if ((int)Auth::id() !== (int)$order->added_by && ! $isAdmin) {
            abort(403);
        }

        return view('shop.orders.invoice', compact('order'));
    }
}
