<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerOrderController extends Controller
{
    protected function currentSupplier()
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $supplier = $user->supplier;
        if (!$supplier) {
            abort(403, 'Supplier profile not found for this user.');
        }

        return $supplier;
    }

    public function index(Request $request)
    {
        $supplier = $this->currentSupplier();

        $status = (string) $request->query('status', 'all');
        $q = trim((string) $request->query('q', ''));

        $ordersQuery = Order::with(['customer', 'returns'])
            ->where('supplier_id', $supplier->id)
            ->latest();

        if ($status !== '' && $status !== 'all') {
            $ordersQuery->where('status', $status);
        }

        if ($q !== '') {
            $ordersQuery->where(function ($query) use ($q) {
                $query->where('order_code', 'like', "%{$q}%")
                    ->orWhere('status', 'like', "%{$q}%");
            });
        }

        $orders = $ordersQuery->paginate(15);

        return view('seller.orders.index', [
            'orders' => $orders,
            'status' => $status,
            'q' => $q,
        ]);
    }
}
