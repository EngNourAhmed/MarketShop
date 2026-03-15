<x-layouts.app :title="__('Dashboard')">
    @php
        $currentUser = auth()->user();
        if ($currentUser) {
            $currentUser->loadMissing('permissionItems');
        }
        $permissionKeys = $currentUser && $currentUser->permissionItems
            ? $currentUser->permissionItems->pluck('key')->all()
            : [];
        $isAdmin = (bool) ($currentUser && $currentUser->role === 'admin');

        $allPermissionKeys = \App\Models\Permission::query()->pluck('key')->all();
        $hasAllPermissions = (count(array_diff($allPermissionKeys, $permissionKeys)) === 0);

        $sections = [
            'customers' => ['route' => 'customers.index', 'label' => 'العملاء'],
            'suppliers' => ['route' => 'suppliers.index', 'label' => 'الموردين'],
            'products' => ['route' => 'products.index', 'label' => 'المنتجات'],
            'sales' => ['route' => 'sales.index', 'label' => 'المبيعات'],
            'commissions' => ['route' => 'commission.index', 'label' => 'العمولات'],
            'orders' => ['route' => 'orders.index', 'label' => 'الاوردرات'],
            'cards' => ['route' => 'cards.index', 'label' => 'مولد الكروت'],
            'withdrawals' => ['route' => 'withdrawRequest.index', 'label' => 'طلبات السحب'],
            'ads' => ['route' => 'advertisement.index', 'label' => 'الاعلانات'],
            'shipping_companies' => ['route' => 'shipping.index', 'label' => 'شركات الشحن'],
            'expenses' => ['route' => 'expenses.index', 'label' => 'المصروفات'],
            'debts' => ['route' => 'debts.index', 'label' => 'المديونيات'],
            'users' => ['route' => 'users.index', 'label' => 'المستخدمين'],
        ];
    @endphp

    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">الأقسام المتاحة</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2">
                @foreach($sections as $key => $s)
                    @if($hasAllPermissions || in_array($key, $permissionKeys, true))
                        <a href="{{ route($s['route']) }}" class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                            {{ $s['label'] }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="grid auto-rows-min gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="text-sm text-gray-500 dark:text-gray-400">Income</div>
                <div class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white">{{ $kpis['income_total'] ?? 0 }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="text-sm text-gray-500 dark:text-gray-400">Completed Imports</div>
                <div class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white">{{ $kpis['completed_imports'] ?? 0 }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="text-sm text-gray-500 dark:text-gray-400">New Customers (30d)</div>
                <div class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white">{{ $kpis['new_customers'] ?? 0 }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Monthly sales</div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                    <thead class="bg-gray-50 dark:bg-neutral-900">
                        <tr>
                            <th class="px-4 py-2 text-start text-sm font-semibold text-gray-700 dark:text-neutral-200">Month</th>
                            <th class="px-4 py-2 text-start text-sm font-semibold text-gray-700 dark:text-neutral-200">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                        @foreach(($monthly_sales ?? []) as $row)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-800 dark:text-neutral-200">{{ ($row['month'] ?? '') . ' ' . ($row['year'] ?? '') }}</td>
                                <td class="px-4 py-2 text-sm text-gray-800 dark:text-neutral-200">{{ $row['total'] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
