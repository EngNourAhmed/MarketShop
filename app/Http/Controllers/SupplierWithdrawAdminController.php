<?php

namespace App\Http\Controllers;

use App\Models\SupplierWithdrawRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierWithdrawAdminController extends Controller
{
    public function index(Request $request)
    {
        $withdrawRequests = SupplierWithdrawRequest::with(['supplier', 'user'])
            ->latest()
            ->get();

        $vendorTotal = (float) $withdrawRequests
            ->filter(fn ($wr) => (string) (optional($wr->supplier)->type ?? '') === 'vendor')
            ->sum(fn ($wr) => (float) ($wr->amount ?? 0));

        $factoryTotal = (float) $withdrawRequests
            ->filter(fn ($wr) => (string) (optional($wr->supplier)->type ?? '') === 'factory')
            ->sum(fn ($wr) => (float) ($wr->amount ?? 0));

        $total = (float) $withdrawRequests->sum(fn ($wr) => (float) ($wr->amount ?? 0));

        $remainingVendorTotal = (float) $withdrawRequests
            ->filter(function ($wr) {
                $status = (string) ($wr->status ?? '');
                $approved = (float) ($wr->approved_amount ?? 0);
                $amount = (float) ($wr->amount ?? 0);
                $type = (string) (optional($wr->supplier)->type ?? '');
                return $status === 'approved' && $type === 'vendor' && $approved > 0 && $approved < $amount;
            })
            ->sum(fn ($wr) => (float) ($wr->amount ?? 0) - (float) ($wr->approved_amount ?? 0));

        $remainingFactoryTotal = (float) $withdrawRequests
            ->filter(function ($wr) {
                $status = (string) ($wr->status ?? '');
                $approved = (float) ($wr->approved_amount ?? 0);
                $amount = (float) ($wr->amount ?? 0);
                $type = (string) (optional($wr->supplier)->type ?? '');
                return $status === 'approved' && $type === 'factory' && $approved > 0 && $approved < $amount;
            })
            ->sum(fn ($wr) => (float) ($wr->amount ?? 0) - (float) ($wr->approved_amount ?? 0));

        $remainingTotal = (float) $withdrawRequests
            ->filter(function ($wr) {
                $status = (string) ($wr->status ?? '');
                $approved = (float) ($wr->approved_amount ?? 0);
                $amount = (float) ($wr->amount ?? 0);
                return $status === 'approved' && $approved > 0 && $approved < $amount;
            })
            ->sum(fn ($wr) => (float) ($wr->amount ?? 0) - (float) ($wr->approved_amount ?? 0));

        return view('withdraw.suppliers', [
            'withdrawRequests' => $withdrawRequests,
            'withdrawVendorTotal' => $vendorTotal,
            'withdrawFactoryTotal' => $factoryTotal,
            'withdrawTotal' => $total,
            'withdrawRemainingVendorTotal' => $remainingVendorTotal,
            'withdrawRemainingFactoryTotal' => $remainingFactoryTotal,
            'withdrawRemainingTotal' => $remainingTotal,
        ]);
    }

    public function update(Request $request, $id)
    {
        $withdrawRequest = SupplierWithdrawRequest::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:pending,approved,rejected',
            'approved_amount' => ['nullable', 'numeric', 'min:0.01', 'max:' . (float) ($withdrawRequest->amount ?? 0)],
        ]);

        $status = $validated['status'];

        if ($status === 'approved') {
            $withdrawRequest->status = 'approved';
            $approvedAmount = $validated['approved_amount'] ?? null;
            if ($approvedAmount === null || $approvedAmount <= 0) {
                $approvedAmount = (float) ($withdrawRequest->amount ?? 0);
            }
            $withdrawRequest->approved_amount = $approvedAmount;
            $withdrawRequest->approved_by = Auth::id() ?? 0;
            $withdrawRequest->approved_at = now();
        } elseif ($status === 'rejected') {
            $withdrawRequest->status = 'rejected';
            $withdrawRequest->approved_amount = null;
            $withdrawRequest->rejected_by = Auth::id() ?? 0;
            $withdrawRequest->rejected_at = now();
        } else {
            $withdrawRequest->status = 'pending';
            $withdrawRequest->approved_amount = null;
            $withdrawRequest->approved_by = null;
            $withdrawRequest->approved_at = null;
            $withdrawRequest->rejected_by = null;
            $withdrawRequest->rejected_at = null;
        }

        $withdrawRequest->save();

        return redirect()
            ->route('supplier_withdraw.index')
            ->with('status', 'تم تحديث حالة طلب السحب بنجاح');
    }

    public function destroy(Request $request, $id)
    {
        $withdrawRequest = SupplierWithdrawRequest::findOrFail($id);
        $withdrawRequest->delete();

        return redirect()
            ->route('supplier_withdraw.index')
            ->with('status', 'تم حذف طلب السحب بنجاح');
    }
}
