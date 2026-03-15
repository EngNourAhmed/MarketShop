<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerNotification;
use App\Models\Order;
use App\Models\OrderReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderReturnController extends Controller
{
    public function shopIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $customer = Customer::query()->where('email', (string) ($user->email ?? ''))->first();
        if (!$customer) {
            return view('shop.returns.index', [
                'orders' => collect(),
            ]);
        }

        $orders = Order::query()
            ->with(['items.product', 'returns'])
            ->where('customer_id', $customer->id)
            ->latest()
            ->get()
            ->filter(function ($order) {
                $status = mb_strtolower((string) ($order->status ?? ''), 'UTF-8');
                return $status === 'delivered' || str_contains($status, 'تم');
            })
            ->values();

        return view('shop.returns.index', [
            'orders' => $orders,
        ]);
    }

    public function shopStore(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $customer = Customer::query()->where('email', (string) ($user->email ?? ''))->first();
        if (!$customer) {
            abort(403);
        }

        $data = $request->validate([
            'order_id' => ['required', 'integer'],
            'reason' => ['required', 'string', 'max:2000'],
            'images' => ['required'],
            'images.*' => ['image', 'max:4096'],
        ]);

        $order = Order::query()
            ->with('returns')
            ->where('id', (int) $data['order_id'])
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        if ($order->returns()->exists()) {
            return back()->with('status', 'تم إرسال طلب إرجاع لهذا الاوردر من قبل.')->withInput();
        }

        $paths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('order-returns', 'public');
            }
        }

        $return = OrderReturn::create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'status' => 'pending',
            'reason' => $data['reason'],
            'images' => $paths,
        ]);

        return redirect()->route('shop.returns.index')->with('status', 'تم إرسال طلب الإرجاع بنجاح');
    }

    public function index(Request $request)
    {
        $returns = OrderReturn::with(['order.customer'])
            ->latest()
            ->get();

        return view('order_returns.index', [
            'returns' => $returns,
        ]);
    }

    public function show($id)
    {
        $return = OrderReturn::with(['order.items.product', 'customer'])->findOrFail($id);

        return view('order_returns.show', [
            'return' => $return,
        ]);
    }

    public function update(Request $request, $id)
    {
        $return = OrderReturn::with(['customer', 'order'])->findOrFail($id);

        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $return->status = $data['status'];
        $return->admin_note = $data['admin_note'] ?? null;
        $return->admin_id = $request->user()->id ?? null;
        $return->save();

        if ($return->customer) {
            $notification = new CustomerNotification();
            $notification->customer_id = $return->customer->id;
            $notification->title = 'تحديث طلب الإرجاع';
            $notification->body = 'تم تحديث حالة طلب الإرجاع الخاص بك إلى: ' . $return->status;
            $notification->data = [
                'type' => 'order_return',
                'order_id' => $return->order_id,
                'return_id' => $return->id,
                'status' => $return->status,
            ];
            $notification->created_by = $request->user()->id ?? null;
            $notification->save();
        }

        return redirect()->route('order_returns.show', $return->id)->with('status', 'تم تحديث طلب الإرجاع');
    }
}
