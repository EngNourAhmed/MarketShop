<x-layouts.app :title="'الاوردرات'">
    <div id="orders-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">الاوردرات</h1>

        @if(session('status'))
            <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-700 border border-red-200">
                <div class="font-semibold mb-2">حدثت أخطاء أثناء الحفظ</div>
                <ul class="list-disc ps-6 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
              <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div> -->

        <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <div class="text-xl font-bold">الاوردرات</div>

                    @php($isCreateOpen = old('__modal') === 'create-order')
                    <details {{ ($isCreateOpen && $errors->any()) ? 'open' : '' }}>
                        <summary class="cursor-pointer px-3 py-1.5 text-sm font-semibold rounded-lg bg-rose-500 text-white hover:bg-rose-600 [&::-webkit-details-marker]:hidden">إضافة</summary>
                        <div class="fixed inset-0 z-40">
                            <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                            <div class="relative mx-auto my-6 w-[95vw] max-w-2xl">
                                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                        <div class="font-semibold text-gray-800 dark:text-gray-100">إضافة اوردر</div>
                                        <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                    </div>
                                    <div class="p-4 max-h-[75vh] overflow-auto">
                                        <form method="POST" action="{{ route('orders.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @csrf
                                            <input type="hidden" name="__modal" value="create-order" />

                                            <div class="md:col-span-2">
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العميل</label>
                                                <select name="customer_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                                    @foreach(($customers ?? []) as $c)
                                                        <option value="{{ $c->id }}" {{ (string) old('customer_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->phone ?? '-' }})</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الحالة</label>
                                                <select name="status" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                                    @foreach(['delivered' => 'تم التوصيل', 'processing' => 'قيد التنفيذ', 'cancelled' => 'ملغي'] as $key => $label)
                                                        <option value="{{ $key }}" {{ (string) old('status', 'processing') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="md:col-span-2">
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المنتج</label>
                                                <select name="product_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                                    <option value="">-</option>
                                                    @foreach(($products ?? []) as $p)
                                                        <option value="{{ $p->id }}" {{ (string) old('product_id') === (string) $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الكمية</label>
                                                <input type="number" min="1" name="quantity" value="{{ old('quantity') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                            </div>

                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الإجمالي</label>
                                                <input type="number" step="0.01" name="total" value="{{ old('total', 0) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                            </div>

                                            <div class="md:col-span-2">
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">ملاحظة</label>
                                                <input type="text" name="note" value="{{ old('note') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                            </div>

                                            <div class="md:col-span-2 flex justify-end">
                                                <button type="submit" class="px-4 py-2 rounded-lg bg-rose-500 text-white font-semibold hover:bg-rose-600">حفظ</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </details>
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
                            <th class="py-3 px-4">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                        @foreach(($orders ?? []) as $order)
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
                                <td class="py-4 px-4 font-semibold">{{ $order->order_code }}</td>
                                <td class="py-4 px-4">{{ optional($order->customer)->name ?? '-' }}</td>
                                <td class="py-4 px-4">{{ $date }}</td>
                                <td class="py-4 px-4"><span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $badge }}">{{ $status }}</span></td>
                                <td class="py-4 px-4"><span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $returnBadge }}">{{ $returnLabel }}</span></td>
                                <td class="py-4 px-4 font-bold">{{ number_format((float) ($order->total ?? 0), 2, '.', ',') }} ج.م</td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        @php($isEditOpen = old('__modal') === ('edit-order-' . $order->id))
                                        <details {{ ($isEditOpen && $errors->any()) ? 'open' : '' }}>
                                            <summary class="cursor-pointer w-11 h-10 rounded-lg bg-gray-200 text-gray-800 font-bold hover:bg-gray-300 dark:bg-slate-700 dark:text-white dark:hover:bg-slate-600 flex items-center justify-center [&::-webkit-details-marker]:hidden">✎</summary>
                                            <div class="fixed inset-0 z-40">
                                                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                                                <div class="relative mx-auto my-6 w-[95vw] max-w-2xl">
                                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                                            <div class="font-semibold text-gray-800 dark:text-gray-100">تعديل اوردر</div>
                                                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                                        </div>
                                                        <div class="p-4 max-h-[75vh] overflow-auto">
                                                            <form method="POST" action="{{ route('orders.update', $order->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="__modal" value="edit-order-{{ $order->id }}" />

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العميل</label>
                                                                    <select name="customer_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                                                        @foreach(($customers ?? []) as $c)
                                                                            <option value="{{ $c->id }}" {{ (string) old('customer_id', $order->customer_id) === (string) $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->phone ?? '-' }})</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الحالة</label>
                                                                    <select name="status" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                                                        @foreach(['delivered' => 'تم التوصيل', 'processing' => 'قيد التنفيذ', 'cancelled' => 'ملغي'] as $key => $label)
                                                                            <option value="{{ $key }}" {{ (string) old('status', $order->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المنتج</label>
                                                                    @php($existingItem = ($order->items ?? collect())->first())
                                                                    <select name="product_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                                                        <option value="">-</option>
                                                                        @foreach(($products ?? []) as $p)
                                                                            <option value="{{ $p->id }}" {{ (string) old('product_id', $existingItem?->product_id) === (string) $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الكمية</label>
                                                                    <input type="number" min="1" name="quantity" value="{{ old('quantity', $existingItem?->quantity) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الإجمالي</label>
                                                                    <input type="number" step="0.01" name="total" value="{{ old('total', $order->total) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">ملاحظة</label>
                                                                    <input type="text" name="note" value="{{ old('note', $order->note) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                                </div>

                                                                <div class="md:col-span-2 flex justify-end">
                                                                    <button type="submit" class="px-4 py-2 rounded-lg bg-rose-500 text-white font-semibold hover:bg-rose-600">حفظ</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </details>

                                        @php($isDeleteOpen = old('__modal') === ('delete-order-' . $order->id))
                                        <details {{ ($isDeleteOpen && $errors->any()) ? 'open' : '' }}>
                                            <summary class="cursor-pointer w-11 h-10 rounded-lg bg-gray-200 text-gray-800 font-bold hover:bg-gray-300 dark:bg-slate-800 dark:text-white dark:hover:bg-slate-700 flex items-center justify-center [&::-webkit-details-marker]:hidden">🗑</summary>
                                            <div class="fixed inset-0 z-40">
                                                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                                                <div class="relative mx-auto my-24 w-[95vw] max-w-md">
                                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                                        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                                            <div class="font-semibold text-gray-800 dark:text-gray-100">تأكيد الحذف</div>
                                                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                                        </div>
                                                        <div class="p-4 text-gray-800 dark:text-gray-100">هل تريد حذف الاوردر <span class="font-semibold">{{ $order->order_code }}</span>؟</div>
                                                        <div class="p-4 flex justify-end gap-2">
                                                            <button type="button" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600" onclick="this.closest('details').removeAttribute('open')">إلغاء</button>
                                                            <form method="POST" action="{{ route('orders.destroy', $order->id) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <input type="hidden" name="__modal" value="delete-order-{{ $order->id }}" />
                                                                <button type="submit" class="px-4 py-2 rounded-lg bg-red-500 text-white font-semibold hover:bg-red-600">حذف</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </details>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
    </div>
</x-layouts.app>
