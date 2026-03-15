<x-layouts.app :title="'العمولات'">
    <div id="commission-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">العمولات</h1>

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
            <div class="flex flex-col gap-4 mb-4">
                <div class="flex flex-col lg:flex-row gap-3 lg:items-center lg:justify-between">
                    <div class="flex items-center gap-2">
                        <div class="text-xl font-bold">العمولات</div>

                        @php($isCreateOpen = old('__modal') === 'create-commission')
                        <details {{ ($isCreateOpen && $errors->any()) ? 'open' : '' }}>
                            <summary class="cursor-pointer px-3 py-1.5 text-sm font-semibold rounded-lg bg-rose-500 text-white hover:bg-rose-600 [&::-webkit-details-marker]:hidden">إضافة</summary>
                            <div class="fixed inset-0 z-40">
                                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                                <div class="relative mx-auto my-6 w-[95vw] max-w-2xl">
                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                            <div class="font-semibold text-gray-800 dark:text-gray-100">إضافة عمولة</div>
                                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                        </div>
                                        <div class="p-4 max-h-[75vh] overflow-auto">
                                            <form method="POST" action="{{ route('commission.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                @csrf
                                                <input type="hidden" name="__modal" value="create-commission" />
                                                <input type="hidden" name="commission_type" value="vendor" />

                                                <div>
                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المستخدم</label>
                                                    <select name="user_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                                        @foreach(($users ?? []) as $u)
                                                            <option value="{{ $u->id }}" {{ (string) old('user_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">البيع / الفاتورة</label>
                                                    <select name="sale_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                                        @foreach(($sales ?? []) as $s)
                                                            <option value="{{ $s->id }}" {{ (string) old('sale_id') === (string) $s->id ? 'selected' : '' }}>{{ $s->invoice_number }}{{ $s->customer ? ' - ' . $s->customer->name : '' }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">قيمة الاوردر</label>
                                                    <input type="number" step="0.01" name="order_amount" value="{{ old('order_amount', 0) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                </div>

                                                <div>
                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العمولة</label>
                                                    <input type="number" step="0.01" name="commission" value="{{ old('commission', 0) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                </div>

                                                <div>
                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الضريبة</label>
                                                    <input type="number" step="0.01" name="tax_amount" value="{{ old('tax_amount', 0) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                </div>

                                                <div>
                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">التاريخ</label>
                                                    <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الحالة</label>
                                                    <input type="text" name="status" value="{{ old('status', 'active') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
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

                    <div class="relative w-full lg:w-[420px]">
                        <input id="commission-search" type="text" placeholder="ابحث بكود الاوردر أو اسم المورد..." class="w-full ps-10 pe-3 py-2 rounded-lg bg-gray-100 text-gray-900 placeholder:text-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-400 dark:border-slate-700" />
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400 dark:text-slate-400"></i>
                        </div>
                    </div>
                </div>

                <form method="GET" action="{{ route('commission.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-slate-300 mb-1">من تاريخ</label>
                        <input type="date" name="from" value="{{ request()->query('from') }}" class="w-full p-2.5 rounded-lg bg-white text-gray-800 border border-gray-300 dark:bg-slate-800 dark:text-white dark:border-slate-700" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-slate-300 mb-1">إلى تاريخ</label>
                        <input type="date" name="to" value="{{ request()->query('to') }}" class="w-full p-2.5 rounded-lg bg-white text-gray-800 border border-gray-300 dark:bg-slate-800 dark:text-white dark:border-slate-700" />
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 rounded-lg bg-rose-500 text-white font-semibold hover:bg-rose-600">بحث</button>
                        <a href="{{ route('commission.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 border border-gray-200 text-gray-800 hover:bg-gray-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white dark:hover:bg-slate-700">مسح</a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-right">
                    <thead>
                        <tr class="text-gray-600 border-b border-gray-200 dark:text-slate-300 dark:border-slate-700">
                            <th class="py-3 px-4">كود الاوردر</th>
                            <th class="py-3 px-4">التاريخ</th>
                            <th class="py-3 px-4">المورد</th>
                            <th class="py-3 px-4">قيمة الاوردر</th>
                            <th class="py-3 px-4">العمولة</th>
                            <th class="py-3 px-4">حالة الارجاع</th>
                            <th class="py-3 px-4">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                        @foreach(($commissions ?? []) as $c)
                            @php($orderCode = optional($c->order)->order_code ?? optional($c->sale)->invoice_number ?? ('#' . $c->id))
                            @php($vendorName = optional($c->supplier)->name ?? optional(optional($c->sale)->customer)->name ?? '-')
                            @php($orderStatus = optional($c->order)->status ?? null)
                            @php($returnObj = optional($c->order)->returns->first())
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
                            @elseif($orderStatus)
                                @php($returnLabel = 'لا يوجد طلب إرجاع')
                            @else
                                @php($returnLabel = '-')
                            @endif
                            @php($searchText = mb_strtolower($orderCode . ' ' . $vendorName . ' ' . ($c->date ?? '') . ' ' . ($c->commission ?? '') . ' ' . ($c->order_amount ?? '') . ' ' . ($orderStatus ?? '') . ' ' . ($returnStatus ?? '')))
                            <tr data-commission-row data-search="{{ $searchText }}">
                                <td class="py-4 px-4 font-semibold">{{ $orderCode }}</td>
                                <td class="py-4 px-4">{{ $c->date }}</td>
                                <td class="py-4 px-4">{{ $vendorName }}</td>
                                <td class="py-4 px-4">{{ number_format((float) ($c->order_amount ?? 0), 2, '.', ',') }} ج.م</td>
                                <td class="py-4 px-4 font-bold text-emerald-300">{{ number_format((float) ($c->commission ?? 0), 2, '.', ',') }} ج.م</td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $returnBadge }}">{{ $returnLabel }}</span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        @php($isEditOpen = old('__modal') === ('edit-commission-' . $c->id))
                                        <details {{ ($isEditOpen && $errors->any()) ? 'open' : '' }}>
                                            <summary class="cursor-pointer w-11 h-10 rounded-lg bg-gray-200 text-gray-800 font-bold hover:bg-gray-300 dark:bg-slate-700 dark:text-white dark:hover:bg-slate-600 flex items-center justify-center [&::-webkit-details-marker]:hidden">✎</summary>
                                            <div class="fixed inset-0 z-40">
                                                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                                                <div class="relative mx-auto my-6 w-[95vw] max-w-2xl">
                                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                                            <div class="font-semibold text-gray-800 dark:text-gray-100">تعديل عمولة</div>
                                                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                                        </div>
                                                        <div class="p-4 max-h-[75vh] overflow-auto">
                                                            <form method="POST" action="{{ route('commission.update', $c->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="__modal" value="edit-commission-{{ $c->id }}" />

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المستخدم</label>
                                                                    <select name="user_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                                                        @foreach(($users ?? []) as $u)
                                                                            <option value="{{ $u->id }}" {{ (string) old('user_id', $c->user_id) === (string) $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">البيع / الفاتورة</label>
                                                                    <select name="sale_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                                                        @foreach(($sales ?? []) as $s)
                                                                            <option value="{{ $s->id }}" {{ (string) old('sale_id', $c->sale_id) === (string) $s->id ? 'selected' : '' }}>{{ $s->invoice_number }}{{ $s->customer ? ' - ' . $s->customer->name : '' }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">قيمة الاوردر</label>
                                                                    <input type="number" step="0.01" name="order_amount" value="{{ old('order_amount', $c->order_amount) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العمولة</label>
                                                                    <input type="number" step="0.01" name="commission" value="{{ old('commission', $c->commission) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الضريبة</label>
                                                                    <input type="number" step="0.01" name="tax_amount" value="{{ old('tax_amount', $c->tax_amount) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">التاريخ</label>
                                                                    <input type="date" name="date" value="{{ old('date', $c->date) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الحالة</label>
                                                                    <input type="text" name="status" value="{{ old('status', $c->status) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
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

                                        @php($isDeleteOpen = old('__modal') === ('delete-commission-' . $c->id))
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
                                                        <div class="p-4 text-gray-800 dark:text-gray-100">هل تريد حذف العمولة الخاصة بـ <span class="font-semibold">{{ $orderCode }}</span>؟</div>
                                                        <div class="p-4 flex justify-end gap-2">
                                                            <button type="button" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600" onclick="this.closest('details').removeAttribute('open')">إلغاء</button>
                                                            <form method="POST" action="{{ route('commission.destroy', $c->id) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <input type="hidden" name="__modal" value="delete-commission-{{ $c->id }}" />
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
                    var input = document.getElementById('commission-search');
                    if (!input) return;

                    var rows = Array.prototype.slice.call(document.querySelectorAll('[data-commission-row]'));
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