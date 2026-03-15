<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $months = (int) ($request->query('months', 12));
        if ($months < 1) {
            $months = 12;
        }
        if ($months > 36) {
            $months = 36;
        }

        $ordersBaseQuery = Order::query()
            ->where('status', 'delivered')
            ->whereDoesntHave('returns', function ($q) {
                $q->where('status', 'approved');
            });

        $income = (float) (clone $ordersBaseQuery)->sum('total');

        $completedImports = Purchase::where('payment_status', 'completed')->count();
        if ($completedImports === 0) {
            $completedImports = Purchase::count();
        }

        $newCustomers = Customer::where('created_at', '>=', now()->subDays(30))->count();

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

        $topCustomers = Order::query()
            ->where('orders.status', 'delivered')
            ->whereDoesntHave('returns', function ($q) {
                $q->where('status', 'approved');
            })
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->selectRaw('customers.id, customers.name, SUM(COALESCE(orders.total, 0)) as total')
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn($r) => [
                'customer_id' => (int) $r->id,
                'name' => $r->name,
                'total' => (float) $r->total,
            ]);

        $extractCountry = function (?string $address): string {
            $address = trim((string) $address);
            if ($address === '') {
                return 'غير محدد';
            }

            $normalized = str_replace(['،', '|', ';'], ',', $address);
            $parts = array_values(array_filter(array_map('trim', explode(',', $normalized)), fn($p) => $p !== ''));
            $candidate = $parts ? $parts[count($parts) - 1] : $address;

            return $candidate !== '' ? $candidate : 'غير محدد';
        };

        $countryCounts = Order::query()
            ->where('orders.status', 'delivered')
            ->whereDoesntHave('returns', function ($q) {
                $q->where('status', 'approved');
            })
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->get(['customers.address'])
            ->map(fn($r) => $extractCountry($r->address ?? null))
            ->countBy();

        $topCountries = $countryCounts
            ->sortDesc()
            ->take(10)
            ->map(fn($total, $name) => [
                'country_code' => '',
                'name' => $name,
                'total' => (int) $total,
            ])
            ->values();

        $payload = [
            'kpis' => [
                'income_total' => $income,
                'completed_imports' => $completedImports,
                'new_customers' => $newCustomers,
                'invoices_count' => (int) Card::query()->count(),
            ],
            'monthly_sales' => $series,
            'top_customers' => $topCustomers,
            'top_countries' => $topCountries,
        ];

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($payload);
        }

        return view('dashboard', $payload);
    }
}
