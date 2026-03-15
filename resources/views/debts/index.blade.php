<x-layouts.app :title="'المديونيات'">
    <div id="debts-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">المديونيات</h1>

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

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-slate-300">مديونيات البائعين (تشمل الرصيد المتاح للسحب)</div>
                        <div class="text-2xl font-bold">{{ number_format((float) ($vendorTotal ?? 0), 0, '.', ',') }} ج.م</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-emerald-500/20 flex items-center justify-center">
                        <i data-lucide="store" class="w-6 h-6 text-emerald-300"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-slate-300">مديونيات المصانع (تشمل الرصيد المتاح للسحب)</div>
                        <div class="text-2xl font-bold">{{ number_format((float) ($factoryTotal ?? 0), 0, '.', ',') }} ج.م</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center">
                        <i data-lucide="factory" class="w-6 h-6 text-blue-300"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-slate-300">إجمالي المديونيات (تشمل الرصيد المتاح للسحب)</div>
                        <div class="text-2xl font-bold text-rose-300">{{ number_format((float) ($totalDebts ?? 0), 0, '.', ',') }} ج.م</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-rose-500/20 flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-6 h-6 text-rose-300"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="text-xl font-bold">المديونيات</div>
                <div class="relative w-72">
                    <input id="debt-search" type="text" placeholder="ابحث عن مورد..." class="w-full ps-10 pe-3 py-2 rounded-lg bg-gray-100 text-gray-900 placeholder:text-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-400 dark:border-slate-700" />
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400 dark:text-slate-400"></i>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="text-gray-600 border-b border-gray-200 dark:text-slate-300 dark:border-slate-700">
                            <th class="py-3">المورد</th>
                            <th class="py-3">النوع</th>
                            <th class="py-3">المبلغ المستحق</th>
                            <th class="py-3">تاريخ الاستحقاق</th>
                            <th class="py-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                        @if(!empty(($debts ?? [])) && count(($debts ?? [])) > 0)
                            @foreach(($debts ?? []) as $debt)
                            @php($supplierName = optional($debt->supplier)->name)
                            @php($customerName = optional($debt->customer)->name)
                            <tr data-debt-row data-search="{{ mb_strtolower(($supplierName ?? '') . ' ' . ($customerName ?? '') . ' ' . ($debt->type ?? '') . ' ' . ($debt->amount ?? '') . ' ' . ($debt->due_date ?? '') . ' ' . ($debt->status ?? '') . ' ' . ($debt->description ?? '')) }}">
                                <td class="py-3 font-semibold">{{ $supplierName ?: '-' }}</td>
                                <td class="py-3">
                                    @php($typeLabel = ($debt->type ?? '') === 'factory' ? 'مصنع' : (($debt->type ?? '') === 'vendor' ? 'محل' : ($debt->type ?? '-')))
                                    <span class="px-2 py-1 rounded-md text-xs font-semibold bg-gray-100 border border-gray-200 text-gray-700 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200">{{ $typeLabel }}</span>
                                </td>
                                <td class="py-3 text-rose-300 font-bold">{{ number_format((float) ($debt->amount ?? 0), 0, '.', ',') }} ج.م</td>
                                <td class="py-3">{{ $debt->due_date }}</td>
                                <td class="py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @php($isEditOpen = old('__modal') === ('edit-' . $debt->id))
                                        <details {{ ($isEditOpen && $errors->any()) ? 'open' : '' }}>
                                            <summary class="cursor-pointer px-3 py-1.5 text-sm font-semibold rounded-lg bg-gray-100 border border-gray-200 text-gray-800 hover:bg-gray-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white dark:hover:bg-slate-700 [&::-webkit-details-marker]:hidden">تعديل</summary>
                                            <div class="fixed inset-0 z-40">
                                                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                                                <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                                            <div class="font-semibold text-gray-800 dark:text-gray-100">تعديل مديونية</div>
                                                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                                        </div>
                                                        <div class="p-4 max-h-[75vh] overflow-auto">
                                                            <form method="POST" action="{{ route('debts.update', $debt->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="__modal" value="edit-{{ $debt->id }}" />

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المورد</label>
                                                                    <select name="supplier_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                                                        @foreach(($suppliers ?? []) as $s)
                                                                            <option value="{{ $s->id }}" data-supplier-type="{{ $s->type }}" {{ (string) ($isEditOpen ? old('supplier_id', $debt->supplier_id) : $debt->supplier_id) === (string) $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العميل (اختياري)</label>
                                                                    <select name="customer_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                                                        <option value="">-</option>
                                                                        @foreach(($customers ?? []) as $c)
                                                                            <option value="{{ $c->id }}" {{ (string) ($isEditOpen ? old('customer_id', $debt->customer_id) : $debt->customer_id) === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الوصف</label>
                                                                    <input name="description" value="{{ $isEditOpen ? old('description', $debt->description) : $debt->description }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">النوع</label>
                                                                    <select name="type" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                                                        <option value="vendor" {{ ($isEditOpen ? old('type', $debt->type) : $debt->type) === 'vendor' ? 'selected' : '' }}>vendor</option>
                                                                        <option value="factory" {{ ($isEditOpen ? old('type', $debt->type) : $debt->type) === 'factory' ? 'selected' : '' }}>factory</option>
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المبلغ</label>
                                                                    <input type="number" step="0.01" min="0" name="amount" value="{{ $isEditOpen ? old('amount', $debt->amount) : $debt->amount }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">تاريخ الاستحقاق</label>
                                                                    <input type="date" name="due_date" value="{{ $isEditOpen ? old('due_date', $debt->due_date) : $debt->due_date }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الحالة</label>
                                                                    <input name="status" value="{{ $isEditOpen ? old('status', $debt->status) : $debt->status }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
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

                                        <form method="POST" action="{{ route('debts.destroy', $debt->id) }}" onsubmit="return confirm('حذف المديونية؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 text-sm font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700">حذف</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="py-4 text-center text-slate-400" colspan="5">لا يوجد مديونيات</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>

<script>
    (function () {
        var input = document.getElementById('debt-search');
        if (!input) return;

        var rows = Array.prototype.slice.call(document.querySelectorAll('[data-debt-row]'));
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

<script>
    (function () {
        var syncForm = function (form) {
            if (!form) return;

            var supplierSelect = form.querySelector('select[name="supplier_id"]');
            var typeSelect = form.querySelector('select[name="type"]');
            if (!supplierSelect || !typeSelect) return;

            var sync = function () {
                var opt = supplierSelect.options[supplierSelect.selectedIndex];
                var supplierType = opt ? opt.getAttribute('data-supplier-type') : null;
                if (!supplierType) return;
                typeSelect.value = supplierType;
            };

            supplierSelect.addEventListener('change', sync);
            sync();
        };

        Array.prototype.slice.call(document.querySelectorAll('form[action*="/debts"]')).forEach(syncForm);
    })();
</script>