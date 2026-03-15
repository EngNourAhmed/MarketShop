<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResponse;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Card;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use App\Models\Invoice;
use App\Models\CustomerNotification;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customersQuery = Customer::query()
            ->withSum('cards', 'points')
            ->with([
                'latestCard',
                'cards' => function ($q) {
                    $q->select('id', 'customer_id', 'card_number', 'points', 'amount')
                        ->latest();
                },
            ])
            ->latest();

        $customers = $customersQuery->paginate(10);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(CustomerResponse::collection($customers));
        }

        $availableCards = Card::whereNull('customer_id')->latest()->get();

        return view('customers.index', [
            'customers' => $customers,
            'availableCards' => $availableCards,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/', 'unique:customers,email'],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        $customer = new Customer();
        $customer->fill($validated);
        $customer->password = Hash::make($request->password);
        $customer->added_by = auth()->id();
        $customer->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($customer, 201);
        }

        return redirect()->route('customers.index')->with('status', 'تم إضافة العميل بنجاح');
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/', 'unique:customers,email,' . $id],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $customer->update($validated);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($customer);
        }

        return redirect()->route('customers.index')->with('status', 'تم تعديل العميل بنجاح');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Customer deleted successfully']);
        }

        return redirect()->route('customers.index')->with('status', 'تم حذف العميل بنجاح');
    }

    public function assignCard(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'card_id' => 'required|exists:cards,id',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $card = Card::whereNull('customer_id')->findOrFail($validated['card_id']);

        $card->customer_id = $customer->id;

        if (Schema::hasColumn('cards', 'distribution')) {
            $card->distribution = $card->distribution ?? 'customer';
        }

        if (Schema::hasColumn('cards', 'status')) {
            $card->status = $card->status ?? 'active';
        }

        $card->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($card);
        }

        return redirect()->route('customers.index')->with('status', 'تم ربط الكارت بالعميل بنجاح');
    }

    public function print($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json(['message' => 'Print view not implemented', 'data' => $customer]);
    }

    public function pdf($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json(['message' => 'PDF generation not implemented', 'data' => $customer]);
    }

    public function excel($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json(['message' => 'Excel export not implemented', 'data' => $customer]);
    }

    public function csv($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json(['message' => 'CSV export not implemented', 'data' => $customer]);
    }

    public function invoice($id, $invoiceId = null)
    {
        $customer = Customer::findOrFail($id);

        if ($invoiceId) {
            $invoice = Invoice::whereHas('order', function ($q) use ($id) {
                $q->where('customer_id', $id);
            })->where('id', $invoiceId)->firstOrFail();

            return response()->json($invoice);
        }

        $invoices = Invoice::whereHas('order', function ($q) use ($id) {
            $q->where('customer_id', $id);
        })->get();

        return response()->json($invoices);
    }

    public function notifications($id)
    {
        $customer = Customer::findOrFail($id);
        $items = CustomerNotification::where('customer_id', $customer->id)
            ->latest()
            ->paginate(10);

        return response()->json($items);
    }

    public function sendNotification(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array',
        ]);

        $notification = new CustomerNotification();
        $notification->customer_id = $customer->id;
        $notification->title = $validated['title'];
        $notification->body = $validated['body'];
        $notification->data = $validated['data'] ?? null;
        $notification->created_by = auth()->id();
        $notification->save();

        $emailSent = false;
        $emailError = null;

        if (!empty($customer->email)) {
            try {
                Mail::raw($notification->body, function ($message) use ($customer, $notification) {
                    $message->to($customer->email)
                        ->subject($notification->title);
                });
                $emailSent = true;
            } catch (\Throwable $e) {
                $emailError = $e->getMessage();
            }
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'notification' => $notification,
                'email_sent' => $emailSent,
                'email_error' => $emailError,
            ], 201);
        }

        return redirect()->route('customers.index')->with('status', 'تم إرسال الإشعار بنجاح');
    }

    public function markNotificationRead($id, $notificationId)
    {
        $customer = Customer::findOrFail($id);
        $notification = CustomerNotification::where('customer_id', $customer->id)
            ->where('id', $notificationId)
            ->firstOrFail();

        $notification->read_at = now();
        $notification->save();

        return response()->json($notification);
    }
}
