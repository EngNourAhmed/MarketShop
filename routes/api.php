<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Suppliercontroller;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\cardGenerationController;
use App\Http\Controllers\WithdrawRequestController;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\AdvertiserAgencyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\ShippingCompanyController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [UserController::class, 'logout']);


// Route::middleware('auth:sanctum')->post('refresh', [UserController::class, 'refresh']);
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('home', [HomeController::class, 'index']);

//     // Sales
//     Route::apiResource('sales', SalesController::class);
//     Route::get('sales/{id}/print', [SalesController::class, 'print']);
//     Route::get('sales/{id}/pdf', [SalesController::class, 'pdf']);
//     Route::get('sales/{id}/excel', [SalesController::class, 'excel']);
//     Route::get('sales/{id}/csv', [SalesController::class, 'csv']);
//     Route::get('sales/{id}/invoice/{invoiceId?}', [SalesController::class, 'invoice']);

//     // Purchase
//     Route::apiResource('purchases', PurchaseController::class)->except(['create', 'edit']);

//     // Products
//     Route::apiResource('products', ProductController::class);
//     Route::get('products/{id}/print', [ProductController::class, 'print']);
//     Route::get('products/{id}/pdf', [ProductController::class, 'pdf']);
//     Route::get('products/{id}/excel', [ProductController::class, 'excel']);
//     Route::get('products/{id}/csv', [ProductController::class, 'csv']);

//     // Customers
//     Route::apiResource('customers', CustomerController::class);
//     Route::get('customers/{id}/print', [CustomerController::class, 'print']);
//     Route::get('customers/{id}/pdf', [CustomerController::class, 'pdf']);
//     Route::get('customers/{id}/excel', [CustomerController::class, 'excel']);
//     Route::get('customers/{id}/csv', [CustomerController::class, 'csv']);
//     Route::get('customers/{id}/invoice/{invoiceId?}', [CustomerController::class, 'invoice']);

//     // Orders
//     Route::apiResource('orders', OrderController::class);
//     Route::get('orders/{id}/print', [OrderController::class, 'print']);
//     Route::get('orders/{id}/pdf', [OrderController::class, 'pdf']);
//     Route::get('orders/{id}/excel', [OrderController::class, 'excel']);

//     // Cart
//     Route::apiResource('cart', CartController::class)->except(['create', 'edit', 'update']);
//     Route::get('cart/{id}/pdf', [CartController::class, 'pdf']);
//     Route::get('cart/{id}/excel', [CartController::class, 'excel']);
//     Route::get('cart/{id}/csv', [CartController::class, 'csv']);

//     // Invoices
//     Route::apiResource('invoices', InvoiceController::class);
//     Route::get('invoices/{id}/print', [InvoiceController::class, 'print']);
//     Route::get('invoices/{id}/pdf', [InvoiceController::class, 'pdf']);
//     Route::get('invoices/{id}/excel', [InvoiceController::class, 'excel']);
//     Route::get('invoices/{id}/csv', [InvoiceController::class, 'csv']);

//     // Payments
//     Route::apiResource('payments', PaymentController::class);
//     Route::get('payments/{id}/print', [PaymentController::class, 'print']);
//     Route::get('payments/{id}/pdf', [PaymentController::class, 'pdf']);
//     Route::get('payments/{id}/excel', [PaymentController::class, 'excel']);
//     Route::get('payments/{id}/csv', [PaymentController::class, 'csv']);


//     // Inventory
//     Route::apiResource('inventory', InventoryController::class);
//     Route::get('inventory/{id}/print', [InventoryController::class, 'print']);
//     Route::get('inventory/{id}/pdf', [InventoryController::class, 'pdf']);
//     Route::get('inventory/{id}/excel', [InventoryController::class, 'excel']);
//     Route::get('inventory/{id}/csv', [InventoryController::class, 'csv']);

//     // Other resources
//     Route::apiResource('suppliers', Suppliercontroller::class);
//     Route::apiResource('commissions', CommissionController::class)->except(['edit', 'update', 'destroy']);
//     Route::apiResource('cards', cardGenerationController::class);
//     Route::apiResource('users', UserController::class);


//     // Reports
//     Route::get('reports', [ReportController::class, 'index']);
//     Route::get('reports/invoice/{id?}', [ReportController::class, 'invoice']);
//     Route::get('reports/expense/{id?}', [ReportController::class, 'expense']);
//     Route::get('reports/sales/{id?}', [ReportController::class, 'sales']);
//     Route::get('reports/customers/{id?}', [ReportController::class, 'customers']);
//     Route::get('reports/products/{id?}', [ReportController::class, 'products']);
//     Route::get('reports/orders/{id?}', [ReportController::class, 'orders']);
//     Route::get('reports/total/{id?}', [ReportController::class, 'total']);
//     Route::get('reports/summary/{id?}', [ReportController::class, 'summary']);
// });


// Api Routes

Route::middleware('auth:sanctum')->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index']);

    // Permissions
    Route::get('/my-permissions', [PermissionController::class, 'myPermissions']);
    Route::get('/users-with-permissions', [PermissionController::class, 'allUsersWithPermissions']);
    Route::post('/users', [PermissionController::class, 'store']);
    Route::put('/users/{userId}', [PermissionController::class, 'update']);
    Route::delete('/users/{userId}', [PermissionController::class, 'destroy']);
    Route::get('/permissions', [PermissionController::class, 'index']);

    // Shipping Companies
    Route::get('shipping', [ShippingCompanyController::class, 'index']);
    Route::delete('shipping/{id}', [ShippingCompanyController::class, 'destroy']);
    Route::put('shipping/{id}', [ShippingCompanyController::class, 'update']);
    Route::post('shipping/create', [ShippingCompanyController::class, 'store']);

    // Ads Agency
    Route::get('ads', [AdvertiserAgencyController::class, 'index']);
    Route::delete('ads/{id}', [AdvertiserAgencyController::class, 'destroy']);
    Route::put('ads/{id}', [AdvertiserAgencyController::class, 'update']);
    Route::post('ads/create', [AdvertiserAgencyController::class, 'store']);


    // Products
    Route::get('products', [ProductController::class, 'index']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::post('products/create', [ProductController::class, 'store']);

    // Orders
    Route::get('orders', [OrderController::class, 'index']);
    Route::delete('orders/{id}', [OrderController::class, 'destroy']);
    // for testing purpose only
    Route::post('orders/create', [OrderController::class, 'store']);

    // Card Generation
    Route::get('cards', [cardGenerationController::class, 'index']);
    Route::post('cards/create', [cardGenerationController::class, 'store']);

    // Withdraw Requests
    Route::get('withdraw-requests/{id}', [WithdrawRequestController::class, 'show']);
    Route::get('withdraw-requests', [WithdrawRequestController::class, 'index']);
    Route::put('withdraw-requests/{id}', [WithdrawRequestController::class, 'update']);
    Route::post('withdraw-requests/create', [WithdrawRequestController::class, 'store']);

    // Suppliers
    Route::get('suppliers', [Suppliercontroller::class, 'index']);
    Route::delete('suppliers/{id}', [Suppliercontroller::class, 'destroy']);
    Route::put('suppliers/{id}', [Suppliercontroller::class, 'update']);
    Route::post('suppliers/create', [Suppliercontroller::class, 'store']);


    // Debts
    Route::get('debts', [DebtController::class, 'index']);
    Route::delete('debts/{id}', [DebtController::class, 'destroy']);
    Route::put('debts/{id}', [DebtController::class, 'update']);
    Route::post('debts/create', [DebtController::class, 'store']);


    // Commissions
    Route::get('commissions', [CommissionController::class, 'index']);
    // for testing purpose only
    Route::post('commissions/create', [CommissionController::class, 'store']);


    // Customers
    Route::get('customers/{id}/notifications', [CustomerController::class, 'notifications']);
    Route::post('customers/{id}/notifications', [CustomerController::class, 'sendNotification']);
    Route::put('customers/{id}/notifications/{notificationId}/read', [CustomerController::class, 'markNotificationRead']);


    // Expenses
    Route::get('expenses/{id}', [ExpenseController::class, 'show']);
    Route::delete('expenses/{id}', [ExpenseController::class, 'destroy']);
    Route::post('expenses/create', [ExpenseController::class, 'store']);
    Route::get('expense', [ExpenseController::class, 'index']);


    // Sales
    Route::apiResource('sales', SalesController::class);
});