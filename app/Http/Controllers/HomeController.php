<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $sales = Sale::sum('total');
        $expenses = Expense::sum('amount');
        $orders = Order::count();
        $customers = Customer::count();
        $products = Product::count();

        // إجمالي قيمة الطلبات التي تم قبول إرجاعها (approved)
        $approvedReturnsTotal = Order::whereHas('returns', function ($q) {
            $q->where('status', 'approved');
        })->sum('total');

        $netProfit = $sales - $expenses - $approvedReturnsTotal;

        $recentSales = Sale::with('customer')->latest()->take(5)->get();
        $recentOrders = Order::with('customer')->latest()->take(5)->get();

        return response()->json([
            'total_sales' => $sales,
            'total_expenses' => $expenses,
            'net_profit' => $netProfit,
            'total_orders' => $orders,
            'total_customers' => $customers,
            'total_products' => $products,
            'recent_sales' => $recentSales,
            'recent_orders' => $recentOrders,
        ]);
    }

}
