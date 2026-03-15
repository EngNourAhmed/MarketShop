<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
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

        $invoicesCount = (int) Card::query()->count();
        $avgInvoice = $invoicesCount > 0 ? ($income / $invoicesCount) : 0;

        $newCustomersQuery = Customer::query()->where('created_at', '>=', now()->subDays(30));
        $newCustomers = (int) $newCustomersQuery->count();

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
                'invoices_count' => $invoicesCount,
                'avg_invoice' => $avgInvoice,
                'new_customers' => $newCustomers,
            ],
            'monthly_sales' => $series,
            'top_customers' => $topCustomers,
            'top_countries' => $topCountries,
        ];

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($payload);
        }

        return view('sales.index', $payload);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'subtotal' => 'required|numeric',
            'tax' => 'required|numeric',
            'total' => 'required|numeric',
            'grand_total' => 'sometimes|required|numeric',
            'discount' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric',
            'due_amount' => 'nullable|numeric',
            'status' => 'required|string',
            'payment_status' => 'sometimes|required|string',
            'payment_method' => 'sometimes|required|string',
            'updated_by' => 'sometimes|exists:users,id',
        ]);

        $sale = new Sale();
        $sale->forceFill($validated);
        $sale->user_id = Auth::id();
        $sale->created_by = Auth::id();
        $sale->invoice_number = 'INV-' . strtoupper(uniqid());
        $sale->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($sale, 201);
        }

        return redirect()->route('sales.index');
    }

    public function show($id)
    {
        $sale = Sale::with(['customer', 'user'])->findOrFail($id);
        return response()->json($sale);
    }

    public function update(Request $request, $id)
    {
        $sale = Sale::findOrFail($id);

        $validated = $request->validate([
            'customer_id' => 'sometimes|exists:customers,id',
            'invoice_date' => 'sometimes|date',
            'due_date' => 'sometimes|date',
            'subtotal' => 'sometimes|numeric',
            'tax' => 'sometimes|numeric',
            'total' => 'sometimes|numeric',
            'grand_total' => 'sometimes|numeric',
            'discount' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric',
            'due_amount' => 'nullable|numeric',
            'status' => 'sometimes|string',
            'payment_status' => 'sometimes|string',
            'payment_method' => 'sometimes|string',
        ]);

        $sale->forceFill($validated);
        $sale->updated_by = Auth::id();
        $sale->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($sale);
        }

        return redirect()->route('sales.index');
    }

    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Sale deleted successfully']);
        }

        return redirect()->route('sales.index');
    }

    public function print($id)
    {
        $sale = Sale::with(['customer', 'user'])->findOrFail($id);
        return response()->json(['message' => 'Print view not implemented', 'data' => $sale]);
    }

    public function pdf($id)
    {
        $sale = Sale::with(['customer', 'user'])->findOrFail($id);
        return response()->json(['message' => 'PDF generation not implemented', 'data' => $sale]);
    }

    public function excel($id)
    {
        $sale = Sale::with(['customer', 'user'])->findOrFail($id);
        return response()->json(['message' => 'Excel export not implemented', 'data' => $sale]);
    }

    public function csv($id)
    {
        $sale = Sale::with(['customer', 'user'])->findOrFail($id);
        return response()->json(['message' => 'CSV export not implemented', 'data' => $sale]);
    }

    public function invoice($id, $invoiceId = null)
    {
        $sale = Sale::with(['customer', 'user'])->findOrFail($id);
        return response()->json($sale);
    }
}
