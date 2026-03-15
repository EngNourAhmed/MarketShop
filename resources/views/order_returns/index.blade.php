<x-layouts.app :title="'مرتجعات الطلبات'">
    <div class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">مرتجعات الطلبات</h1>

        @if(session('status'))
            <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="text-xl font-bold">طلبات الإرجاع</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-right">
                    <thead>
                        <tr class="text-gray-600 border-b border-gray-200 dark:text-slate-300 dark:border-slate-700">
                            <th class="py-3 px-4">المستخدم</th>
                            <th class="py-3 px-4">كود الاوردر</th>
                            <th class="py-3 px-4">الحالة</th>
                            <th class="py-3 px-4">التاريخ</th>
                            <th class="py-3 px-4">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                        @forelse(($returns ?? []) as $return)
                            @php($customer = $return->order->customer ?? $return->customer)
                            <tr>
                                <td class="py-3 px-4">
                                    @if($customer)
                                        <a href="{{ route('order_returns.show', $return->id) }}" class="text-rose-600 hover:underline">
                                            {{ $customer->name }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($return->order)
                                        <a href="{{ route('order_returns.show', $return->id) }}" class="text-rose-600 hover:underline">
                                            {{ $return->order->order_code }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @php($status = $return->status)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                        @if($status === 'approved') bg-emerald-500/20 text-emerald-300
                                        @elseif($status === 'rejected') bg-red-500/20 text-red-300
                                        @else bg-amber-500/20 text-amber-300 @endif">
                                        @if($status === 'approved')
                                            مقبول
                                        @elseif($status === 'rejected')
                                            مرفوض
                                        @else
                                            قيد المراجعة
                                        @endif
                                    </span>
                                </td>
                                <td class="py-3 px-4">{{ optional($return->created_at)->format('Y-m-d') ?? '-' }}</td>
                                <td class="py-3 px-4">
                                    <a href="{{ route('order_returns.show', $return->id) }}" class="px-3 py-1.5 rounded-lg bg-rose-500 text-white text-xs font-semibold hover:bg-rose-600">عرض</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 px-4 text-sm text-gray-500 dark:text-slate-300">لا توجد طلبات إرجاع.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
