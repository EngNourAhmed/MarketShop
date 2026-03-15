<x-layouts.app :title="'الطلبات الخاصة'">
    <div class="page-content">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">الطلبات الخاصة</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">الطلبات الخاصة المسندة إليك كمورد.</p>
            </div>
        </div>

        <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-right">
                    <thead>
                        <tr class="text-gray-600 border-b border-gray-200 dark:text-slate-300 dark:border-slate-700">
                            <th class="py-3 px-4 text-xs font-semibold">#</th>
                            <th class="py-3 px-4 text-xs font-semibold">العميل</th>
                            <th class="py-3 px-4 text-xs font-semibold">عنوان الطلب</th>
                            <th class="py-3 px-4 text-xs font-semibold">المنتج</th>
                            <th class="py-3 px-4 text-xs font-semibold">السعر المقترح</th>
                            <th class="py-3 px-4 text-xs font-semibold">الحالة</th>
                            <th class="py-3 px-4 text-xs font-semibold">تاريخ الطلب</th>
                            <th class="py-3 px-4 text-xs font-semibold">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                        @forelse(($orders ?? []) as $order)
                            <tr>
                                <td class="py-3 px-4 text-sm">{{ $order->id }}</td>
                                <td class="py-3 px-4 text-sm">
                                    <div class="font-semibold">{{ $order->user->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->user->email ?? '' }}</div>
                                </td>
                                <td class="py-3 px-4 text-sm">{{ $order->title }}</td>
                                <td class="py-3 px-4 text-sm">{{ $order->product->name ?? '-' }}</td>
                                <td class="py-3 px-4 text-sm">
                                    @if($order->assigned_price !== null)
                                        {{ number_format((float) $order->assigned_price, 2, '.', ',') }} ج.م
                                    @elseif($order->budget !== null)
                                        {{ number_format((float) $order->budget, 2, '.', ',') }} ج.م
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    @php($status = (string) ($order->status ?? ''))
                                    @php($statusLabel = $status)
                                    @php($statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100')
                                    @if($status === 'pending')
                                        @php($statusLabel = 'قيد المراجعة')
                                        @php($statusClass = 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300')
                                    @elseif($status === 'in_progress')
                                        @php($statusLabel = 'جاري تنفيذ الطلب')
                                        @php($statusClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300')
                                    @elseif($status === 'done')
                                        @php($statusLabel = 'تم توافر الطلب')
                                        @php($statusClass = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300')
                                    @elseif($status === 'manufacturing')
                                        @php($statusLabel = 'تحت التصنيع')
                                        @php($statusClass = 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300')
                                    @elseif($status === 'shipping')
                                        @php($statusLabel = 'يتم الشحن')
                                        @php($statusClass = 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300')
                                    @elseif($status === 'shipped')
                                        @php($statusLabel = 'اتشحن')
                                        @php($statusClass = 'bg-teal-100 text-teal-800 dark:bg-teal-900/40 dark:text-teal-300')
                                    @elseif($status === 'cancelled')
                                        @php($statusLabel = 'لم يتم توافر الطلب')
                                        @php($statusClass = 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300')
                                    @endif
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm">{{ optional($order->created_at)->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="py-3 px-4 text-sm">
                                    @php($current = (string) ($order->status ?? 'pending'))
                                    @php($canEdit = !in_array($current, ['shipped','cancelled'], true))
                                    @if($canEdit)
                                        <form method="POST" action="{{ route('seller.special_orders.update', $order->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <select
                                                name="status"
                                                class="px-3 py-2 rounded-lg text-xs font-semibold border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100"
                                                onchange="this.form.submit()"
                                            >
                                                <option value="done" {{ $current === 'done' ? 'selected' : '' }}>تم توافر الطلب</option>
                                                <option value="manufacturing" {{ $current === 'manufacturing' ? 'selected' : '' }}>تحت التصنيع</option>
                                                <option value="shipping" {{ $current === 'shipping' ? 'selected' : '' }}>يتم الشحن</option>
                                                <option value="shipped" {{ $current === 'shipped' ? 'selected' : '' }}>اتشحن</option>
                                                <option value="cancelled" {{ $current === 'cancelled' ? 'selected' : '' }}>لم يتم توافر الطلب</option>
                                            </select>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">تم التحديث</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-6 px-4 text-center text-sm text-gray-500 dark:text-gray-300">
                                    لا توجد طلبات خاصة مسندة إليك حتى الآن.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
