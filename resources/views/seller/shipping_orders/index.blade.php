<x-layouts.app :title="'طلبات التوريد'">
    <div id="seller-shipping-orders-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">طلبات التوريد</h1>

        @if(session('status'))
            <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="text-xl font-bold">طلبات التوريد للمورد {{ $supplier->name ?? '' }}</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-right">
                    <thead>
                        <tr class="text-gray-600 border-b border-gray-200 dark:text-slate-300 dark:border-slate-700">
                            <th class="py-3 px-4">المنتج</th>
                            <th class="py-3 px-4">العميل</th>
                            <th class="py-3 px-4">الكمية</th>
                            <th class="py-3 px-4">سعر الوحدة</th>
                            <th class="py-3 px-4">الإجمالي</th>
                            <th class="py-3 px-4">الحالة</th>
                            <th class="py-3 px-4">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                        @forelse(($requests ?? []) as $r)
                            @php($status = (string) ($r->status ?? 'pending'))
                            @php($statusLabel = $status)
                            @php($statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100')
                            @if($status === 'pending')
                                @php($statusLabel = 'قيد المراجعة')
                                @php($statusClass = 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300')
                            @elseif($status === 'available')
                                @php($statusLabel = 'تم توافر الطلب')
                                @php($statusClass = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300')
                            @elseif($status === 'cancelled')
                                @php($statusLabel = 'لم يتم توافر الطلب')
                                @php($statusClass = 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300')
                            @endif
                            <tr>
                                <td class="py-4 px-4 font-semibold">{{ optional($r->product)->name ?? '-' }}</td>
                                <td class="py-4 px-4">{{ optional($r->customer)->name ?? '-' }}</td>
                                <td class="py-4 px-4">{{ (int) ($r->quantity ?? 0) }}</td>
                                <td class="py-4 px-4">{{ number_format((float) ($r->unit_price ?? 0), 2, '.', ',') }} ج.م</td>
                                <td class="py-4 px-4 font-bold">{{ number_format((float) ($r->total_price ?? 0), 2, '.', ',') }} ج.م</td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('seller.shipping_orders.update', $r->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="available" />
                                            <button type="submit" {{ $status === 'available' ? 'disabled' : '' }} class="px-3 py-2 rounded-lg bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600 disabled:opacity-40 disabled:cursor-not-allowed">تم توافر الطلب</button>
                                        </form>

                                        <form method="POST" action="{{ route('seller.shipping_orders.update', $r->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="cancelled" />
                                            <button type="submit" {{ $status === 'cancelled' ? 'disabled' : '' }} class="px-3 py-2 rounded-lg bg-rose-500 text-white text-sm font-semibold hover:bg-rose-600 disabled:opacity-40 disabled:cursor-not-allowed">لم يتم التوافر</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 text-center text-gray-600 dark:text-gray-300">لا توجد طلبات توريد حتى الآن.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
