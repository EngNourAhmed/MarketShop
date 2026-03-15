<?php

namespace App\Http\Controllers;

use App\Models\SpecialOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Supplier;

class SpecialOrderAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = (string) $request->query('status', 'all');
        $q = trim((string) $request->query('q', ''));
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        $query = SpecialOrder::with(['user', 'supplier', 'product'])->latest();

        if ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($from !== '') {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to !== '') {
            $query->whereDate('created_at', '<=', $to);
        }

        if ($q !== '') {
            $search = '%' . $q . '%';
            $query->where(function ($sub) use ($search) {
                $sub->where('title', 'like', $search)
                    ->orWhere('details', 'like', $search)
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    });
            });
        }

        $orders = $query->get();

        $allOrdersForAssign = SpecialOrder::with(['user'])->latest()->get();

        $availableStatuses = ['pending', 'in_progress', 'done', 'cancelled'];

        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::with('suppliers')->orderBy('name')->get();

        $productSupplierPrices = [];
        foreach ($products as $product) {
            foreach ($product->suppliers as $supplier) {
                $key = $product->id . '-' . $supplier->id;
                $productSupplierPrices[$key] = (float) ($supplier->pivot->price ?? 0);
            }
        }

        $assignOrdersPayload = $allOrdersForAssign->map(function (SpecialOrder $order) {
            return [
                'id' => (int) $order->id,
                'title' => (string) ($order->title ?? ''),
                'customer' => (string) optional($order->user)->name,
                'budget' => $order->budget !== null ? (float) $order->budget : null,
            ];
        })->values();

        $assignSuppliersPayload = $suppliers->map(function (Supplier $supplier) {
            return [
                'id' => (int) $supplier->id,
                'name' => (string) ($supplier->name ?? ''),
                'type' => (string) ($supplier->type ?? ''),
            ];
        })->values();

        return view('special_orders.index', [
            'orders' => $orders,
            'status' => $status,
            'availableStatuses' => $availableStatuses,
            'suppliers' => $suppliers,
            'products' => $products,
            'productSupplierPrices' => $productSupplierPrices,
            'q' => $q,
            'from' => $from,
            'to' => $to,
            'allOrdersForAssign' => $allOrdersForAssign,
            'assignOrdersPayload' => $assignOrdersPayload,
            'assignSuppliersPayload' => $assignSuppliersPayload,
        ]);
    }

    public function assign(Request $request, SpecialOrder $order)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'product_id' => ['nullable', 'exists:products,id'],
            'assigned_price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'in:pending,in_progress,done,cancelled'],
        ]);

        $supplierId = (int) $validated['supplier_id'];
        $productId = array_key_exists('product_id', $validated) ? (int) $validated['product_id'] : null;

        $assignedPrice = $validated['assigned_price'] ?? null;
        if ($assignedPrice === null && $order->budget !== null) {
            $assignedPrice = (float) $order->budget;
        }

        $order->supplier_id = $supplierId;
        $order->product_id = $productId;
        $order->assigned_price = $assignedPrice;
        $order->assigned_at = now();

        $newStatus = (string) ($validated['status'] ?? '');
        if ($newStatus !== '') {
            $order->status = $newStatus;
        } elseif ($order->status === 'pending') {
            $order->status = 'in_progress';
        }

        $order->save();

        return redirect()
            ->route('special_orders.index', ['status' => $request->query('status', 'all')])
            ->with('status', 'تم إسناد الطلب الخاص بنجاح.');
    }

    public function approve(Request $request, SpecialOrder $order)
    {
        $order->admin_status = 'approved';
        $order->admin_rejection_reason = null;
        $order->admin_reviewed_at = now();
        $order->save();

        return redirect()
            ->route('special_orders.index', ['status' => $request->query('status', 'all')])
            ->with('status', 'تمت الموافقة على الطلب الخاص.');
    }

    public function reject(Request $request, SpecialOrder $order)
    {
        $validated = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:5000'],
        ]);

        $order->admin_status = 'rejected';
        $order->admin_rejection_reason = $validated['rejection_reason'] ?? null;
        $order->admin_reviewed_at = now();
        $order->save();

        return redirect()
            ->route('special_orders.index', ['status' => $request->query('status', 'all')])
            ->with('status', 'تم رفض الطلب الخاص.');
    }

    public function updateFactoryStatus(Request $request, SpecialOrder $order)
    {
        $validated = $request->validate([
            'factory_status' => ['required', 'string', 'in:pending,in_progress,done,cancelled'],
        ]);

        // only allow factory status if admin approved and supplier marked it available
        if ((string) ($order->admin_status ?? '') !== 'approved' || !in_array((string) ($order->status ?? ''), ['done', 'shipping', 'shipped'], true)) {
            return redirect()
                ->route('special_orders.index', ['status' => $request->query('status', 'all')])
                ->withErrors(['factory_status' => 'لا يمكن تحديث حالة المصنع قبل موافقة المورد وموافقة الأدمن.']);
        }

        $order->factory_status = (string) $validated['factory_status'];
        $order->factory_updated_at = now();
        $order->save();

        return redirect()
            ->route('special_orders.index', ['status' => $request->query('status', 'all')])
            ->with('status', 'تم تحديث حالة المصنع بنجاح.');
    }

    public function publicMedia(Request $request, string $path)
    {
        $path = ltrim((string) $path, '/');
        $path = str_replace('\\', '/', $path);

        if ($path === '' || str_contains($path, '..')) {
            abort(404);
        }

        $disk = Storage::disk('public');
        if (!$disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path);
    }
}
