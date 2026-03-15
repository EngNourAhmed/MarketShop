<aside class="w-64 bg-white dark:bg-gray-800 shadow-lg flex flex-col transition-all duration-300">
  @php
    $currentUser = auth()->user();
    if ($currentUser) {
      $currentUser->loadMissing('permissionItems');
    }

    $permissionKeys = $currentUser && $currentUser->permissionItems
      ? $currentUser->permissionItems->pluck('key')->all()
      : [];

    $role = $currentUser->role ?? null;
    $isAdmin = (bool) ($role === 'admin');

    $allPermissionKeys = \App\Models\Permission::query()->pluck('key')->all();
    $hasAllPermissions = (count(array_diff($allPermissionKeys, $permissionKeys)) === 0);

    $unreadAdminMessageCount = \App\Models\Message::query()
      ->where('sender_role', 'user')
      ->where('is_read_by_admin', false)
      ->count();

    if ($role === 'supplier') {
      // قائمة خاصة بالمورد (Seller Dashboard)
      $menuItems = [
        'seller_dashboard' => [
          'route' => 'seller.dashboard',
          'label' => 'لوحة التحكم',
          'icon'  => 'layout-dashboard',
          'active' => ['seller.dashboard'],
        ],
        'seller_products' => [
          'route' => 'seller.products.index',
          'label' => 'منتجاتي',
          'icon'  => 'package',
          'active' => ['seller.products.*'],
        ],
        'seller_special_orders' => [
          'route' => 'seller.special_orders.index',
          'label' => 'الطلبات الخاصة',
          'icon'  => 'star',
          'active' => ['seller.special_orders.*'],
        ],
        'seller_shipping_orders' => [
          'route' => 'seller.shipping_orders.index',
          'label' => 'طلبات التوريد',
          'icon'  => 'truck',
          'active' => ['seller.shipping_orders.*'],
        ],
        'seller_settings' => [
          'route' => 'seller.settings.edit',
          'label' => 'إعدادات المتجر',
          'icon'  => 'settings',
          'active' => ['seller.settings.*'],
        ],
        'seller_earnings' => [
          'route' => 'seller.earnings.index',
          'label' => 'أرباحي',
          'icon'  => 'wallet',
          'active' => ['seller.earnings.*'],
        ],
      ];

      // إظهار كل عناصر قائمة المورد بدون الاعتماد على صلاحيات لوحة الأدمن
      $hasAllPermissions = true;
    } else {
      // القائمة الافتراضية للأدمن ولوحة الإدارة
      $menuItems = [
        'customers' => ['route' => 'customers.index', 'label' => 'العملاء', 'icon' => 'users'],
        'suppliers' => ['route' => 'suppliers.index', 'label' => 'الموردين', 'icon' => 'building-2'],
        'products' => ['route' => 'products.index', 'label' => 'المنتجات', 'icon' => 'package'],
        'categories' => ['route' => 'categories.index', 'label' => 'الفئات', 'icon' => 'grid-2x2'],
        'sales' => [
          'route' => 'dashboard',
          'active' => ['dashboard', 'sales.*'],
          'label' => 'المبيعات',
          'icon' => 'trending-up',
        ],
        'commissions' => ['route' => 'commission.index', 'label' => 'العمولات', 'icon' => 'percent'],
        'orders' => ['route' => 'orders.index', 'label' => 'الاوردرات', 'icon' => 'clipboard-list'],
        'shipping_orders' => ['route' => 'shipping_orders.index', 'label' => 'طلبات توريد المنتجات', 'icon' => 'truck'],
        'special_orders' => ['route' => 'special_orders.index', 'label' => 'الطلبات الخاصة', 'icon' => 'star'],
        'cards' => ['route' => 'cards.index', 'label' => 'مولد الكروت', 'icon' => 'credit-card'],
        'withdrawals' => ['route' => 'supplier_withdraw.index', 'label' => 'طلبات السحب', 'icon' => 'arrow-down-up'],
        'ads' => ['route' => 'advertisement.index', 'label' => 'الاعلانات', 'icon' => 'megaphone'],
        'shipping_companies' => ['route' => 'shipping.index', 'label' => 'شركات الشحن', 'icon' => 'truck'],
        'expenses' => ['route' => 'expenses.index', 'label' => 'المصروفات', 'icon' => 'dollar-sign'],
        'debts' => ['route' => 'debts.index', 'label' => 'المديونيات', 'icon' => 'trending-down'],
        'users' => ['route' => 'users.index', 'label' => 'المستخدمين', 'icon' => 'user-pen'],
        'order_returns' => ['route' => 'order_returns.index', 'label' => 'مرتجعات الطلبات', 'icon' => 'undo-2'],
        'messages' => ['route' => 'messages.index', 'label' => 'الرسائل', 'icon' => 'message-circle'],
        'messages_notifications' => ['route' => 'messages.notifications', 'label' => 'تنبيهات الرسائل', 'icon' => 'bell'],
      ];
    }
  @endphp
  <div class="flex items-center justify-center p-4 border-b dark:border-gray-700">
    <h1 class="text-3xl font-bold bg-gradient-to-tr from-[#FFF687] to-[#F6416C] bg-clip-text text-transparent">
      Trady Admin
    </h1>
  </div>

  <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
    @foreach($menuItems as $key => $item)
      @if($hasAllPermissions || in_array($key, $permissionKeys, true))
        @php
          $activePatterns = $item['active'] ?? [$item['route']];
          $isActive = false;
          foreach ($activePatterns as $pattern) {
            if (request()->routeIs($pattern)) {
              $isActive = true;
              break;
            }
          }
        @endphp
        <a href="{{ route($item['route']) }}"
           class="nav-link flex items-center p-3 rounded-lg transition
           {{ $isActive
                ? 'bg-gradient-to-r from-rose-400 to-orange-300 text-white shadow-md'
                : 'text-gray-700 hover:bg-gray-200 dark:text-gray-200 dark:hover:bg-gray-700' }}">
          <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 ml-3"></i>
          <span>{{ $item['label'] }}</span>
          @if(($key === 'messages' || $key === 'messages_notifications') && $unreadAdminMessageCount > 0)
            <span class="ml-auto inline-flex items-center justify-center min-w-[18px] h-5 px-1.5 rounded-full text-[11px] font-semibold bg-rose-500 text-white">
              {{ $unreadAdminMessageCount > 99 ? '99+' : $unreadAdminMessageCount }}
            </span>
          @endif
        </a>
      @endif
    @endforeach
  </nav>

  <div class="p-4 border-t dark:border-gray-700">
    <div class="flex items-center justify-between p-2 text-gray-700 dark:text-gray-200">
      <div class="flex items-center">
        <i data-lucide="moon" class="w-5 h-5 ml-3 text-gray-500 dark:text-gray-400"></i>Dark Mode
      </div>
      <label for="dark-mode-toggle" class="inline-flex relative items-center cursor-pointer">
        <input type="checkbox" value="" id="dark-mode-toggle" class="sr-only peer" />
        <div
          class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-rose-400"
        ></div>
      </label>
    </div>
  </div>

  <div class="p-4 border-t dark:border-gray-700">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="w-full flex items-center justify-center gap-2 p-3 bg-gray-900 text-white text-center rounded-lg font-semibold hover:bg-gray-800 transition-colors">
        <i data-lucide="log-out" class="w-5 h-5"></i>
        <span>تسجيل الخروج</span>
      </button>
    </form>
  </div>

  <a
    href="{{ route('customer.home') }}"
    target="_blank"
    class="block w-[80%] mb-4 mr-4 p-3 bg-gradient-to-r from-rose-400 to-orange-300 text-white text-center rounded-lg font-semibold hover:opacity-90 transition-opacity"
  >
    <span>Trady Shop</span>
  </a>
</aside>
