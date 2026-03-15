<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $payload = [
            'available_reports' => [
                'invoice', 'expense', 'sales', 'customers', 'products', 'orders', 'total', 'summary'
            ]
        ];

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Welcome to the Reports API',
                ...$payload,
            ]);
        }

        return view('report.index', $payload);
    }

    public function invoice($id = null)
    {
        if ($id) {
            $invoice = Invoice::with(['order', 'payments'])->findOrFail($id);
            return response()->json($invoice);
        }
        $invoices = Invoice::with(['order', 'payments'])->latest()->paginate(10);
        return response()->json($invoices);
    }

    public function expense($id = null)
    {
        if ($id) {
            $expense = Expense::with('user')->findOrFail($id);
            return response()->json($expense);
        }
        $expenses = Expense::with('user')->latest()->paginate(10);
        return response()->json($expenses);
    }

    public function sales($id = null)
    {
        if ($id) {
            $sale = Sale::with(['customer', 'user'])->findOrFail($id);
            return response()->json($sale);
        }
        $sales = Sale::with(['customer', 'user'])->latest()->paginate(10);
        return response()->json($sales);
    }

    public function customers($id = null)
    {
        if ($id) {
            $customer = Customer::with(['sales', 'orders'])->findOrFail($id);
            return response()->json($customer);
        }
        $customers = Customer::latest()->paginate(10);
        return response()->json($customers);
    }

    public function products($id = null)
    {
        if ($id) {
            $product = Product::with('inventory')->findOrFail($id);
            return response()->json($product);
        }
        $products = Product::with('inventory')->latest()->paginate(10);
        return response()->json($products);
    }

    public function orders($id = null)
    {
        if ($id) {
            $order = Order::with(['customer', 'invoice', 'shipping'])->findOrFail($id);
            return response()->json($order);
        }
        $orders = Order::with(['customer', 'invoice', 'shipping'])->latest()->paginate(10);
        return response()->json($orders);
    }

    public function total($id = null)
    {
        $salesTotal = Sale::sum('total');
        $expensesTotal = Expense::sum('amount');
        $ordersTotal = Order::sum('total');

        // إجمالي قيمة الطلبات التي تم قبول إرجاعها (approved)
        $approvedReturnsTotal = Order::whereHas('returns', function ($q) {
            $q->where('status', 'approved');
        })->sum('total');

        $netProfit = $salesTotal - $expensesTotal - $approvedReturnsTotal;

        return response()->json([
            'total_sales' => $salesTotal,
            'total_expenses' => $expensesTotal,
            'total_orders' => $ordersTotal,
            'net_profit' => $netProfit,
        ]);
    }

    public function summary($id = null)
    {
        return response()->json([
            'invoices_count' => Invoice::count(),
            'sales_count' => Sale::count(),
            'customers_count' => Customer::count(),
            'products_count' => Product::count(),
            'orders_count' => Order::count(),
            'expenses_count' => Expense::count(),
        ]);
    }
}
