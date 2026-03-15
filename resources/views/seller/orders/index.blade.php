<x-layouts.app :title="'طلبات العملاء'">
    <div id="seller-orders-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">طلبات العملاء</h1>

        <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <div class="text-xl font-bold">الاوردرات</div>
                </div>

                <div class="relative w-full md:w-[520px]">
                    <input id="order-search" type="text" placeholder="ابحث كود الاوردر/اسم العميل..." class="w-full ps-10 pe-3 py-2 rounded-lg bg-gray-100 text-gray-900 placeholder:text-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-400 dark:border-slate-700" />
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400 dark:text-slate-400"></i>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-right">
                    <thead>
                        <tr class="text-gray-600 border-b border-gray-200 dark:text-slate-300 dark:border-slate-700">
                            <th class="py-3 px-4">كود الاوردر</th>
                            <th class="py-3 px-4">العميل</th>
                            <th class="py-3 px-4">التاريخ</th>
                            <th class="py-3 px-4">الحالة</th>
                            <th class="py-3 px-4">حالة الارجاع</th>
                            <th class="py-3 px-4">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                        @forelse(($orders ?? []) as $order)
                            @php($date = $order->created_at ? $order->created_at->format('Y-m-d') : '-')
                            @php($status = $order->status ?? '-')
                            @php($statusLower = mb_strtolower($status))
                            @php($badge = 'bg-slate-700 text-slate-200')
                            @if($statusLower === 'delivered' || str_contains($statusLower, 'تم'))
                                @php($badge = 'bg-emerald-500/20 text-emerald-300')
                            @elseif($statusLower === 'processing' || str_contains($statusLower, 'قيد'))
                                @php($badge = 'bg-amber-500/20 text-amber-300')
                            @elseif($statusLower === 'cancelled' || $statusLower === 'canceled' || str_contains($statusLower, 'ملغي'))
                                @php($badge = 'bg-red-500/20 text-red-300')
                            @endif
                            @php($returnObj = ($order->returns ?? collect())->first())
                            @php($returnStatus = $returnObj?->status)
                            @php($returnLabel = 'لا يوجد طلب إرجاع')
                            @php($returnBadge = 'bg-slate-700 text-slate-200')
                            @if($returnStatus === 'approved')
                                @php($returnLabel = 'تم قبول الإرجاع')
                                @php($returnBadge = 'bg-red-500/20 text-red-300')
                            @elseif($returnStatus === 'pending')
                                @php($returnLabel = 'طلب إرجاع قيد المراجعة')
                                @php($returnBadge = 'bg-amber-500/20 text-amber-300')
                            @elseif($returnStatus === 'rejected')
                                @php($returnLabel = 'تم رفض الإرجاع')
                                @php($returnBadge = 'bg-emerald-500/20 text-emerald-300')
                            @elseif(!$returnStatus)
                                @php($returnLabel = 'لا يوجد طلب إرجاع')
                            @endif
                            @php($searchText = mb_strtolower(($order->order_code ?? '') . ' ' . (optional($order->customer)->name ?? '') . ' ' . $date . ' ' . ($order->total ?? '') . ' ' . $status . ' ' . ($returnStatus ?? '')))
                            <tr data-order-row data-search="{{ $searchText }}">
                                <td class="py-4 px-4 font-semibold">{{ $order->order_code ?? ('ORD#' . $order->id) }}</td>
                                <td class="py-4 px-4">{{ optional($order->customer)->name ?? '-' }}</td>
                                <td class="py-4 px-4">{{ $date }}</td>
                                <td class="py-4 px-4"><span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $badge }}">{{ $status }}</span></td>
                                <td class="py-4 px-4"><span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $returnBadge }}">{{ $returnLabel }}</span></td>
                                <td class="py-4 px-4 font-bold">{{ number_format((float) ($order->total ?? 0), 2, '.', ',') }} ج.م</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-gray-600 dark:text-gray-300">لا توجد أوردرات حالياً</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            @if(method_exists(($orders ?? null), 'links'))
                {{ $orders->links() }}
            @endif
        </div>

        <script>
            (function () {
                var input = document.getElementById('order-search');
                if (!input) return;

                var rows = Array.prototype.slice.call(document.querySelectorAll('[data-order-row]'));
                var handler = function () {
                    var q = (input.value || '').trim().toLowerCase();
                    rows.forEach(function (row) {
                        var hay = (row.getAttribute('data-search') || '').toLowerCase();
                        row.style.display = q === '' || hay.indexOf(q) !== -1 ? '' : 'none';
                    });
                };

                input.addEventListener('input', handler);
            })();
        </script>
    </div>
</x-layouts.app>
