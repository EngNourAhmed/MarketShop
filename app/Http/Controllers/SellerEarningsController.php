<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SupplierWithdrawRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerEarningsController extends Controller
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

        $withdrawApproved = (float) SupplierWithdrawRequest::where('supplier_id', $supplier->id)
            ->where('status', 'approved')
            ->get()
            ->sum(function ($wr) {
                $amount = (float) ($wr->approved_amount ?? $wr->amount ?? 0);
                return $amount;
            });

        $withdrawPending = (float) SupplierWithdrawRequest::where('supplier_id', $supplier->id)
            ->where('status', 'pending')
            ->sum('amount');

        $availableBalance = $netIncome - $withdrawApproved;
        if ($availableBalance < 0) {
            $availableBalance = 0.0;
        }

        $months = (int) ($request->query('months', 6));
        if ($months < 1) {
            $months = 6;
        }
        if ($months > 36) {
            $months = 36;
        }

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

        $withdrawCounts = SupplierWithdrawRequest::where('supplier_id', $supplier->id)
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->all();

        $withdrawCountPending = (int) ($withdrawCounts['pending'] ?? 0);
        $withdrawCountApproved = (int) ($withdrawCounts['approved'] ?? 0);
        $withdrawCountRejected = (int) ($withdrawCounts['rejected'] ?? 0);

        $transactions = SupplierWithdrawRequest::with(['approvedBy', 'rejectedBy'])
            ->where('supplier_id', $supplier->id)
            ->latest()
            ->get();

        return view('seller.earnings.index', [
            'supplier' => $supplier,
            'net_income' => $netIncome,
            'gross_income' => $grossIncome,
            'commission_amount' => $commissionAmount,
            'withdraw_approved' => $withdrawApproved,
            'withdraw_pending' => $withdrawPending,
            'available_balance' => $availableBalance,
            'months' => $months,
            'monthly_sales' => $series,
            'withdraw_count_pending' => $withdrawCountPending,
            'withdraw_count_approved' => $withdrawCountApproved,
            'withdraw_count_rejected' => $withdrawCountRejected,
            'transactions' => $transactions,
        ]);
    }

    public function store(Request $request)
    {
        $supplier = $this->currentSupplier();
        $user = Auth::user();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|max:50',
            'payment_method' => 'required|string|max:255',
            'reference' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

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

        $withdrawApproved = (float) SupplierWithdrawRequest::where('supplier_id', $supplier->id)
            ->where('status', 'approved')
            ->get()
            ->sum(function ($wr) {
                $amount = (float) ($wr->approved_amount ?? $wr->amount ?? 0);
                return $amount;
            });

        $availableBalance = $netIncome - $withdrawApproved;

        if ($validated['amount'] > $availableBalance) {
            return back()
                ->withErrors(['amount' => 'المبلغ المطلوب أكبر من الرصيد المتاح.'])
                ->withInput();
        }

        $withdraw = new SupplierWithdrawRequest();
        $withdraw->forceFill([
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
            'amount' => (float) $validated['amount'],
            'currency' => $validated['currency'],
            'payment_method' => $validated['payment_method'],
            'reference' => $validated['reference'] ?? '',
            'description' => $validated['description'] ?? '',
            'status' => 'pending',
        ]);
        $withdraw->save();

        return redirect()
            ->route('seller.earnings.index')
            ->with('status', 'تم إرسال طلب السحب بنجاح، سيتم مراجعته من الأدمن.');
    }
}
