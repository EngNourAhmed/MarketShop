<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $supplier = $user->supplier;
        if (!$supplier) {
            abort(403, 'Supplier profile not found for this user.');
        }

        $months = (int) ($request->query('months', 12));
        if ($months < 1) {
            $months = 12;
        }
        if ($months > 36) {
            $months = 36;
        }

        $ordersBaseQuery = Order::query()
            ->where('supplier_id', $supplier->id)
            ->where('status', 'delivered')
            ->whereDoesntHave('returns', function ($q) {
                $q->where('status', 'approved');
            });

        $grossIncome = (float) (clone $ordersBaseQuery)->sum('total');

        $commissionPercent = (float) ($supplier->commission_percent ?? 0);
        if ($commissionPercent < 0) {
            $commissionPercent = 0;
        }
        if ($commissionPercent > 100) {
            $commissionPercent = 100;
        }

        $commissionAmount = $grossIncome * $commissionPercent / 100.0;
        $netIncome = $grossIncome - $commissionAmount;

        $ordersCount = (int) (clone $ordersBaseQuery)->count();
        $productsCount = (int) $supplier->products()->count();

        $start = now()->copy()->startOfMonth()->subMonths($months - 1);
        $monthlySales = (clone $ordersBaseQuery)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(COALESCE(total, 0)) as total")
            ->where('created_at', '>=', $start)
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $monthlyByYm = $monthlySales->pluck('total', 'ym');

        $series = [];
        for ($i = 0; $i < $months; $i++) {
            $d = $start->copy()->addMonths($i);
            $ym = $d->format('Y-m');
            $series[] = [
                'month' => $d->format('M'),
                'year' => (int) $d->format('Y'),
                'ym' => $ym,
                'total' => (float) ($monthlyByYm[$ym] ?? 0),
            ];
        }

        $topProducts = OrderItem::query()
            ->whereHas('order', function ($q) use ($supplier) {
                $q->where('supplier_id', $supplier->id)
                    ->where('status', 'delivered')
                    ->whereDoesntHave('returns', function ($r) {
                        $r->where('status', 'approved');
                    });
            })
            ->with('product')
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(total) as total_amount')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                return [
                    'product_id' => (int) $row->product_id,
                    'name' => (string) ($row->product->name ?? ''),
                    'total_qty' => (int) ($row->total_qty ?? 0),
                    'total_amount' => (float) ($row->total_amount ?? 0),
                ];
            });

        $payload = [
            'supplier' => $supplier,
            'kpis' => [
                'orders_count' => $ordersCount,
                'products_count' => $productsCount,
                'gross_income' => $grossIncome,
                'commission_amount' => $commissionAmount,
                'net_income' => $netIncome,
            ],
            'monthly_sales' => $series,
            'top_products' => $topProducts,
        ];

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($payload);
        }

        return view('seller.dashboard', $payload);
    }
}
