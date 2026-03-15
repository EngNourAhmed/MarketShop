<x-layouts.app :title="'طلبات توريد المنتجات'">
    <div id="shipping-orders-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">طلبات توريد المنتجات</h1>

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

        <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <div class="text-xl font-bold">طلبات توريد المنتجات</div>

                    @php($isCreateOpen = old('__modal') === 'create-shipping-order')
                    <details {{ ($isCreateOpen && $errors->any()) ? 'open' : '' }}>
                        <summary class="cursor-pointer px-3 py-1.5 text-sm font-semibold rounded-lg bg-rose-500 text-white hover:bg-rose-600 [&::-webkit-details-marker]:hidden">إضافة</summary>
                        <div class="fixed inset-0 z-40">
                            <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                            <div class="relative mx-auto my-6 w-[95vw] max-w-3xl">
                                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                        <div class="font-semibold text-gray-800 dark:text-gray-100">إضافة طلب توريد منتج</div>
                                        <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                    </div>
                                    <div class="p-4 max-h-[75vh] overflow-auto">
                                        <form method="POST" action="{{ route('shipping_orders.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @csrf
                                            <input type="hidden" name="__modal" value="create-shipping-order" />

                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المنتج</label>
                                                <select name="product_id" data-shipping-product-select class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                                    <option value="">اختر المنتج</option>
                                                    @foreach(($products ?? []) as $p)
                                                        @php($suppliersForProduct = ($p->suppliers ?? collect())->map(fn($s) => [
                                                            'id' => (int) $s->id,
                                                            'name' => (string) ($s->name ?? ''),
                                                            'price' => (float) ($s->pivot->price ?? 0),
                                                        ])->values())
                                                        <option value="{{ (int) $p->id }}" data-suppliers='@json($suppliersForProduct)'>
                                                            {{ $p->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المورد</label>
                                                <select name="supplier_id" data-shipping-supplier-select class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required disabled>
                                                    <option value="">اختر المنتج أولاً</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الكمية</label>
                                                <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                            </div>

                                            <div class="md:col-span-2 flex justify-end mt-2">
                                                <button type="submit" class="px-4 py-2 rounded-lg bg-rose-500 text-white font-semibold hover:bg-rose-600">حفظ</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </details>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-right">
                    <thead>
                        <tr class="text-gray-600 border-b border-gray-200 dark:text-slate-300 dark:border-slate-700">
                            <th class="py-3 px-4">المنتج</th>
                            <th class="py-3 px-4">المورد</th>
                            <th class="py-3 px-4">الكمية</th>
                            <th class="py-3 px-4">سعر الوحدة</th>
                            <th class="py-3 px-4">الإجمالي</th>
                            <th class="py-3 px-4">الحالة</th>
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
                                <td class="py-4 px-4">{{ optional($r->supplier)->name ?? '-' }}</td>
                                <td class="py-4 px-4">{{ (int) ($r->quantity ?? 0) }}</td>
                                <td class="py-4 px-4">{{ number_format((float) ($r->unit_price ?? 0), 2, '.', ',') }} ج.م</td>
                                <td class="py-4 px-4 font-bold">{{ number_format((float) ($r->total_price ?? 0), 2, '.', ',') }} ج.م</td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-gray-600 dark:text-gray-300">لا توجد طلبات توريد حالياً</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <script>
                (function () {
                    var productSelect = document.querySelector('[data-shipping-product-select]');
                    var supplierSelect = document.querySelector('[data-shipping-supplier-select]');
                    if (!productSelect || !supplierSelect) return;

                    var fillSuppliers = function () {
                        var option = productSelect.options[productSelect.selectedIndex];
                        var raw = option ? option.getAttribute('data-suppliers') || '' : '';
                        var items = [];
                        try {
                            if (raw) items = JSON.parse(raw) || [];
                        } catch (e) {
                            items = [];
                        }

                        supplierSelect.innerHTML = '';
                        var placeholder = document.createElement('option');
                        placeholder.value = '';
                        placeholder.textContent = 'اختر المورد';
                        supplierSelect.appendChild(placeholder);

                        items.forEach(function (s) {
                            var opt = document.createElement('option');
                            opt.value = String(s.id || '');
                            var priceNum = Number(s.price || 0);
                            var priceLabel = Number.isFinite(priceNum) ? priceNum.toLocaleString(undefined, { maximumFractionDigits: 0 }) + ' ج.م' : '';
                            opt.textContent = String(s.name || '') + (priceLabel ? ' - ' + priceLabel : '');
                            supplierSelect.appendChild(opt);
                        });

                        supplierSelect.disabled = items.length === 0;
                    };

                    productSelect.addEventListener('change', fillSuppliers);

                    if (productSelect.value) {
                        fillSuppliers();
                    }
                })();
            </script>
        </div>
    </div>
</x-layouts.app>
