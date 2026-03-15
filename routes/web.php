<?php

use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\AdvertiserAgencyController;
use App\Http\Controllers\PaymobController;
use App\Http\Controllers\cardGenerationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerHomeController;
use App\Http\Controllers\CustomerMessageController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderReturnController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductInteractionController;
use App\Http\Controllers\ProductRatingController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SellerCategoryController;
use App\Http\Controllers\SellerDashboardController;
use App\Http\Controllers\SellerEarningsController;
use App\Http\Controllers\SellerProductController;
use App\Http\Controllers\SellerSettingsController;
use App\Http\Controllers\SellerShippingOrderController;
use App\Http\Controllers\SellerSpecialOrderController;
use App\Http\Controllers\ShippingCompanyController;
use App\Http\Controllers\ShippingOrderAdminController;
use App\Http\Controllers\ShopCategoryController;
use App\Http\Controllers\ShopNotificationController;
use App\Http\Controllers\ShopProductController;
use App\Http\Controllers\ShopSpecialOrderController;
use App\Http\Controllers\SpecialOrderAdminController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierWithdrawAdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawRequestController;
use App\Livewire\Actions\Logout;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    $role = (string) ($user->role ?? '');

    if ($role === 'customer') {
        return redirect()->route('customer.home');
    }

    if ($role === 'supplier') {
        return redirect()->route('seller.dashboard');
    }

    return redirect()->route('dashboard');
})->name('home');

// require __DIR__.'/auth.php';

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('livewire.auth.login');
    })->name('login');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/'],
            'password' => ['required', 'string'],
            'remember' => ['nullable'],
        ]);

        $remember = (bool) ($request->boolean('remember'));

        if (! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            return back()
                ->withErrors(['email' => __('auth.failed')])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user) {
            if (($user->role ?? null) !== 'supplier') {
                $hasAnyPermission = $user->permissionItems()->exists();
                $desiredRole = $hasAnyPermission ? 'admin' : 'customer';
                if (($user->role ?? null) !== $desiredRole) {
                    $user->forceFill(['role' => $desiredRole])->save();
                }
            }
        }

        $role = (string) ($user->role ?? '');
        if ($role === 'admin') {
            $redirectTo = route('dashboard');
        } elseif ($role === 'supplier') {
            $redirectTo = route('seller.dashboard');
        } else {
            $redirectTo = route('customer.home');
        }

        return redirect()->intended($redirectTo);
    })->name('login.store');

    Route::get('/register', function () {
        return view('livewire.auth.register');
    })->name('register');

    Route::post('/register', function (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/', 'max:255', 'unique:users,email'],
            'address' => ['required', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:255', 'unique:users,phone'],
            'password' => ['required', 'confirmed', 'min:8'],
            'account_type' => ['nullable', 'in:customer,supplier'],
        ]);

        $accountType = (string) ($validated['account_type'] ?? 'customer');

        if ($accountType === 'supplier') {
            $supplierEmailExists = Supplier::where('email', $validated['email'])->exists();
            if ($supplierEmailExists) {
                return back()
                    ->withErrors(['email' => 'هذا البريد الإلكتروني مستخدم بالفعل لمورد آخر.'])
                    ->withInput();
            }
        }

        $role = $accountType === 'supplier' ? 'supplier' : 'customer';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
        ]);

        if ($accountType === 'supplier') {
            Supplier::create([
                'name' => $validated['name'],
                'type' => 'factory',
                'commission_percent' => 0,
                'user_id' => $user->id,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'created_by' => (string) ($user->id),
            ]);
        }

        return redirect()->route('login');
    })->name('register.store');

    Route::get('/forgot-password', function () {
        return view('livewire.auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', function (Request $request) {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return back()->withErrors(['email' => 'هذا البريد الإلكتروني غير مسجل لدينا.']);
        }

        $otp = random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $validated['email']],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        try {
            Mail::raw('كود استعادة كلمة المرور الخاصة بك هو: '.$otp, function ($message) use ($validated) {
                $message->to($validated['email'])
                    ->subject('كود استعادة كلمة المرور');
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['email' => 'حدث خطأ أثناء إرسال الكود. حاول مرة أخرى.']);
        }

        return back()->with([
            'status' => 'تم إرسال كود التحقق إلى بريدك الإلكتروني.',
            'otp_email' => $validated['email'],
        ]);
    })->name('password.email');

    Route::post('/forgot-password/verify', function (Request $request) {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/'],
            'otp' => ['required', 'string'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (! $record) {
            return back()->withErrors(['otp' => 'الكود غير صحيح أو منتهي.']);
        }

        if ($record->created_at === null || \Illuminate\Support\Carbon::parse($record->created_at)->lt(now()->subMinutes(15))) {
            return back()->withErrors(['otp' => 'انتهت صلاحية الكود. برجاء طلب كود جديد.']);
        }

        if (! Hash::check($validated['otp'], $record->token)) {
            return back()->withErrors(['otp' => 'الكود غير صحيح.']);
        }

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return back()->withErrors(['email' => 'هذا البريد الإلكتروني غير مسجل لدينا.']);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        $request->session()->forget('otp_email');

        return redirect()->route('login')->with('status', 'تم تعيين كلمة المرور بنجاح. يمكنك تسجيل الدخول الآن.');
    })->name('password.otp.verify');

    Route::get('/reset-password/{token}', function (string $token) {
        return view('livewire.auth.reset-password', ['token' => $token]);
    })->name('password.reset');

    Route::post('/reset-password', function (Request $request) {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    })->name('password.update');
});

Route::post('/logout', Logout::class)->middleware('auth')->name('logout');

// Public shop routes (متاحة للضيوف + المسجّلين)
Route::get('/shop', [CustomerHomeController::class, 'index'])->name('customer.home');
Route::get('/products/{id}', [ShopProductController::class, 'show'])->name('shop.products.show');
Route::get('/categories/{slug}', [ShopCategoryController::class, 'show'])->name('shop.categories.show');
Route::get('/search', [CustomerHomeController::class, 'search'])->name('shop.search');

// Shop routes that تحتاج تسجيل دخول (سلة، طلبات، حساب، رسائل، مرتجعات، تغيير اللغة)
Route::middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        return redirect()->route('customer.home');
    });
    Route::get('/Home', function () {
        return redirect()->route('customer.home');
    });

    Route::get('/orders/{id}/invoice', [OrderController::class, 'shopInvoice'])->name('shop.orders.invoice');
    Route::get('/orders', [OrderController::class, 'shopIndex'])->name('shop.orders.index');
    Route::get('/notifications', [ShopNotificationController::class, 'index'])->name('shop.notifications.index');
    Route::get('/cart', [CartController::class, 'shopIndex'])->name('shop.cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('shop.cart.store');
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('shop.cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('shop.cart.destroy');
    Route::get('/checkout', [OrderController::class, 'shopCheckout'])->name('shop.checkout.index');
    Route::post('/checkout', [OrderController::class, 'store'])->name('shop.orders.store');

    Route::post('/products/{product}/like', [ProductInteractionController::class, 'like'])->name('shop.products.like');
    Route::post('/products/{product}/comment', [ProductInteractionController::class, 'comment'])->name('shop.products.comment');
    Route::post('/products/{product}/share', [ProductInteractionController::class, 'share'])->name('shop.products.share');
    Route::post('/products/{product}/rating', [ProductRatingController::class, 'store'])->name('shop.products.rating');
    Route::post('/products/{id}/rate', [ShopProductController::class, 'rate'])->name('shop.products.rate');
    Route::get('/account', [CustomerAccountController::class, 'show'])->name('shop.account');
    Route::post('/account', [CustomerAccountController::class, 'update'])->name('shop.account.update');

    Route::get('/messages', [CustomerMessageController::class, 'index'])->name('shop.messages.index');
    Route::post('/messages', [CustomerMessageController::class, 'store'])->name('shop.messages.store');

    Route::get('/returns', [OrderReturnController::class, 'shopIndex'])->name('shop.returns.index');
    Route::post('/returns', [OrderReturnController::class, 'shopStore'])->name('shop.returns.store');

    Route::post('/lang', [LanguageController::class, 'set'])->name('shop.lang.set');
    Route::post('/country', [App\Http\Controllers\ExchangeRateController::class, 'setShopCountry'])->name('shop.country.set');

    Route::get('/special-order', [ShopSpecialOrderController::class, 'create'])->name('shop.special_orders.create');
    Route::post('/special-order', [ShopSpecialOrderController::class, 'store'])->name('shop.special_orders.store');

    // Paymob Routes
    Route::get('/pay/credit-card/{orderId}', [PaymobController::class, 'payWithCard'])->name('paymob.pay');
    Route::get('/paymob/response', [PaymobController::class, 'response'])->name('paymob.response');
});

// Paymob Server-to-Server Webhook
Route::post('/paymob/webhook', [PaymobController::class, 'webhook'])->name('paymob.webhook');

Route::prefix('seller')->middleware(['auth', 'supplier'])->group(function () {
    Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('seller.dashboard');
    Route::get('/products', [SellerProductController::class, 'index'])->name('seller.products.index');
    Route::post('/products', [SellerProductController::class, 'store'])->name('seller.products.store');
    Route::put('/products/{id}', [SellerProductController::class, 'update'])->name('seller.products.update');
    Route::delete('/products/{id}', [SellerProductController::class, 'destroy'])->name('seller.products.destroy');

    Route::get('/categories', [SellerCategoryController::class, 'index'])->name('seller.categories.index');
    Route::post('/categories', [SellerCategoryController::class, 'store'])->name('seller.categories.store');
    Route::put('/categories/{category}', [SellerCategoryController::class, 'update'])->name('seller.categories.update');
    Route::delete('/categories/{category}', [SellerCategoryController::class, 'destroy'])->name('seller.categories.destroy');

    Route::get('/settings', [SellerSettingsController::class, 'edit'])->name('seller.settings.edit');
    Route::post('/settings', [SellerSettingsController::class, 'update'])->name('seller.settings.update');

    Route::get('/earnings', [SellerEarningsController::class, 'index'])->name('seller.earnings.index');
    Route::post('/earnings/withdraw', [SellerEarningsController::class, 'store'])->name('seller.earnings.withdraw');

    Route::get('/special-orders', [SellerSpecialOrderController::class, 'index'])->name('seller.special_orders.index');
    Route::put('/special-orders/{specialOrder}', [SellerSpecialOrderController::class, 'update'])->name('seller.special_orders.update');

    Route::get('/shipping-orders', [SellerShippingOrderController::class, 'index'])->name('seller.shipping_orders.index');
    Route::put('/shipping-orders/{shippingOrderRequest}', [SellerShippingOrderController::class, 'update'])->name('seller.shipping_orders.update');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('customers.index');
    });

    Route::get('/dashboard', function (Request $request) {
        return app(SalesController::class)->index($request);
    })->name('dashboard');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index')->middleware('permission:customers');
    Route::post('/customers/create', [CustomerController::class, 'store'])->name('customers.store')->middleware('permission:customers');
    Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update')->middleware('permission:customers');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy')->middleware('permission:customers');
    Route::post('/customers/assign-card', [CustomerController::class, 'assignCard'])->name('customers.assignCard')->middleware('permission:customers');
    Route::post('/customers/{id}/notifications', [CustomerController::class, 'sendNotification'])->name('customers.notify')->middleware('permission:customers');

    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index')->middleware('permission:suppliers');
    Route::post('/suppliers/create', [SupplierController::class, 'store'])->name('suppliers.store')->middleware('permission:suppliers');
    Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update')->middleware('permission:suppliers');
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy')->middleware('permission:suppliers');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index')->middleware('permission:products');
    Route::post('/products/create', [ProductController::class, 'store'])->name('products.store')->middleware('permission:products');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update')->middleware('permission:products');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy')->middleware('permission:products');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index')->middleware('permission:categories');
    Route::post('/categories/create', [CategoryController::class, 'store'])->name('categories.store')->middleware('permission:categories');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update')->middleware('permission:categories');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy')->middleware('permission:categories');

    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index')->middleware('permission:sales');
    Route::post('/sales', [SalesController::class, 'store'])->name('sales.store')->middleware('permission:sales');
    Route::put('/sales/{id}', [SalesController::class, 'update'])->name('sales.update')->middleware('permission:sales');
    Route::delete('/sales/{id}', [SalesController::class, 'destroy'])->name('sales.destroy')->middleware('permission:sales');

    Route::get('/commission', [CommissionController::class, 'index'])->name('commission.index')->middleware('permission:commissions');
    Route::post('/commission/create', [CommissionController::class, 'store'])->name('commission.store')->middleware('permission:commissions');
    Route::put('/commission/{id}', [CommissionController::class, 'update'])->name('commission.update')->middleware('permission:commissions');
    Route::delete('/commission/{id}', [CommissionController::class, 'destroy'])->name('commission.destroy')->middleware('permission:commissions');

    Route::get('/orders/{id}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice')->middleware('permission:orders');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index')->middleware('permission:orders');
    Route::post('/orders/create', [OrderController::class, 'store'])->name('orders.store')->middleware('permission:orders');
    Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update')->middleware('permission:orders');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy')->middleware('permission:orders');

    Route::get('/advertisement', [AdvertiserAgencyController::class, 'index'])->name('advertisement.index')->middleware('permission:ads');
    Route::post('/advertisement/create', [AdvertiserAgencyController::class, 'store'])->name('advertisement.store')->middleware('permission:ads');
    Route::put('/advertisement/{id}', [AdvertiserAgencyController::class, 'update'])->name('advertisement.update')->middleware('permission:ads');
    Route::delete('/advertisement/{id}', [AdvertiserAgencyController::class, 'destroy'])->name('advertisement.destroy')->middleware('permission:ads');

    Route::get('/shipping', [ShippingCompanyController::class, 'index'])->name('shipping.index')->middleware('permission:shipping_companies');
    Route::post('/shipping/create', [ShippingCompanyController::class, 'store'])->name('shipping.store')->middleware('permission:shipping_companies');
    Route::put('/shipping/{id}', [ShippingCompanyController::class, 'update'])->name('shipping.update')->middleware('permission:shipping_companies');
    Route::delete('/shipping/{id}', [ShippingCompanyController::class, 'destroy'])->name('shipping.destroy')->middleware('permission:shipping_companies');

    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index')->middleware('permission:expenses');
    Route::post('/expenses/create', [ExpenseController::class, 'store'])->name('expenses.store')->middleware('permission:expenses');
    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->name('expenses.update')->middleware('permission:expenses');
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy')->middleware('permission:expenses');

    Route::get('/debts', [DebtController::class, 'index'])->name('debts.index')->middleware('permission:debts');
    Route::post('/debts/create', [DebtController::class, 'store'])->name('debts.store')->middleware('permission:debts');
    Route::put('/debts/{id}', [DebtController::class, 'update'])->name('debts.update')->middleware('permission:debts');
    Route::delete('/debts/{id}', [DebtController::class, 'destroy'])->name('debts.destroy')->middleware('permission:debts');

    Route::get('/cards', [cardGenerationController::class, 'index'])->name('cards.index')->middleware('permission:cards');
    Route::post('/cards/create', [cardGenerationController::class, 'store'])->name('cards.store')->middleware('permission:cards');
    Route::put('/cards/{id}', [cardGenerationController::class, 'update'])->name('cards.update')->middleware('permission:cards');
    Route::delete('/cards/{id}', [cardGenerationController::class, 'destroy'])->name('cards.destroy')->middleware('permission:cards');

    Route::get('/withdrawRequest', [WithdrawRequestController::class, 'index'])->name('withdrawRequest.index')->middleware('permission:withdrawals');
    Route::post('/withdrawRequest/create', [WithdrawRequestController::class, 'store'])->name('withdrawRequest.store')->middleware('permission:withdrawals');
    Route::put('/withdrawRequest/{id}', [WithdrawRequestController::class, 'update'])->name('withdrawRequest.update')->middleware('permission:withdrawals');
    Route::delete('/withdrawRequest/{id}', [WithdrawRequestController::class, 'destroy'])->name('withdrawRequest.destroy')->middleware('permission:withdrawals');

    Route::get('/supplier-withdraw', [SupplierWithdrawAdminController::class, 'index'])->name('supplier_withdraw.index')->middleware('permission:withdrawals');
    Route::put('/supplier-withdraw/{id}', [SupplierWithdrawAdminController::class, 'update'])->name('supplier_withdraw.update')->middleware('permission:withdrawals');
    Route::delete('/supplier-withdraw/{id}', [SupplierWithdrawAdminController::class, 'destroy'])->name('supplier_withdraw.destroy')->middleware('permission:withdrawals');

    Route::get('/messages', [AdminMessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [AdminMessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/notifications', [AdminMessageController::class, 'notifications'])->name('messages.notifications');

    Route::get('/order-returns', [OrderReturnController::class, 'index'])->name('order_returns.index');
    Route::get('/order-returns/{id}', [OrderReturnController::class, 'show'])->name('order_returns.show');
    Route::put('/order-returns/{id}', [OrderReturnController::class, 'update'])->name('order_returns.update');

    Route::get('/special-orders', [SpecialOrderAdminController::class, 'index'])->name('special_orders.index');
    Route::post('/special-orders/{order}/assign', [SpecialOrderAdminController::class, 'assign'])->name('special_orders.assign');
    Route::post('/special-orders/{order}/approve', [SpecialOrderAdminController::class, 'approve'])->name('special_orders.approve');
    Route::post('/special-orders/{order}/reject', [SpecialOrderAdminController::class, 'reject'])->name('special_orders.reject');
    Route::post('/special-orders/{order}/factory-status', [SpecialOrderAdminController::class, 'updateFactoryStatus'])->name('special_orders.factory_status');
    Route::get('/media/public/{path}', [SpecialOrderAdminController::class, 'publicMedia'])->where('path', '.*')->name('admin.media.public');

    Route::get('/shipping-orders', [ShippingOrderAdminController::class, 'index'])->name('shipping_orders.index')->middleware('permission:orders');
    Route::post('/shipping-orders', [ShippingOrderAdminController::class, 'store'])->name('shipping_orders.store')->middleware('permission:orders');

    Route::get('/users', function (Request $request) {
        return app(UserController::class)->index($request);
    })->name('users.index')->middleware('permission:users');

    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');

    Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchase.index');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Permissions Routes
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index')->middleware('permission:users');
    Route::get('users/permissions', [PermissionController::class, 'allUsersWithPermissions'])->name('users.permissions.index')->middleware('permission:users');
    Route::get('my-permissions', [PermissionController::class, 'myPermissions'])->name('permissions.my');
    Route::post('users', [PermissionController::class, 'store'])->name('users.store')->middleware('permission:users');
    Route::put('users/{id}/permissions', [PermissionController::class, 'update'])->name('users.permissions.update')->middleware('permission:users');
    Route::delete('users/{id}', [PermissionController::class, 'destroy'])->name('users.destroy')->middleware('permission:users');

});
