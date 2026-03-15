<x-layouts.app :title="'أرباح المتجر'">
    <div id="seller-earnings-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">أرباح المتجر</h1>

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

        <div class="grid auto-rows-min gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="text-sm text-gray-500 dark:text-gray-400">إجمالي المبيعات (قبل العمولة)</div>
                <div class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white">
                    {{ number_format((float) $gross_income, 2, '.', ',') }} ج.م
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="text-sm text-gray-500 dark:text-gray-400">قيمة عمولة المنصة</div>
                <div class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white">
                    {{ number_format((float) $commission_amount, 2, '.', ',') }} ج.م
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="text-sm text-gray-500 dark:text-gray-400">صافي أرباحك</div>
                <div class="mt-1 text-2xl font-semibold text-emerald-600 dark:text-emerald-400">
                    {{ number_format((float) $net_income, 2, '.', ',') }} ج.م
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="text-sm text-gray-500 dark:text-gray-400">الرصيد المتاح للسحب</div>
                <div class="mt-1 text-2xl font-semibold text-blue-600 dark:text-blue-400">
                    {{ number_format((float) $available_balance, 2, '.', ',') }} ج.م
                </div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    تم سحب: {{ number_format((float) $withdraw_approved, 2, '.', ',') }} ج.م · قيد المراجعة: {{ number_format((float) $withdraw_pending, 2, '.', ',') }} ج.م
                </div>
            </div>
        </div>

        <div class="grid auto-rows-min gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="text-sm text-gray-500 dark:text-gray-400">طلبات السحب قيد المراجعة</div>
                <div class="mt-1 text-2xl font-semibold text-amber-500 dark:text-amber-300">
                    {{ (int) ($withdraw_count_pending ?? 0) }}
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="text-sm text-gray-500 dark:text-gray-400">طلبات السحب المقبولة</div>
                <div class="mt-1 text-2xl font-semibold text-emerald-500 dark:text-emerald-300">
                    {{ (int) ($withdraw_count_approved ?? 0) }}
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="text-sm text-gray-500 dark:text-gray-400">طلبات السحب المرفوضة</div>
                <div class="mt-1 text-2xl font-semibold text-red-500 dark:text-red-300">
                    {{ (int) ($withdraw_count_rejected ?? 0) }}
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 items-start mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between gap-3 mb-2">
                    <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">طلب سحب جديد</div>

                    @php($isCreateOpen = old('__modal') === 'create-seller-withdraw')
                    <details {{ ($isCreateOpen && $errors->any()) ? 'open' : '' }}>
                        <summary class="cursor-pointer px-3 py-1.5 text-sm font-semibold rounded-lg bg-rose-500 text-white hover:bg-rose-600 [&::-webkit-details-marker]:hidden">
                            + طلب سحب
                        </summary>
                        <div class="fixed inset-0 z-40">
                            <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                            <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                        <div class="font-semibold text-gray-800 dark:text-gray-100">طلب سحب الأرباح</div>
                                        <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                    </div>
                                    <div class="p-4 max-h-[75vh] overflow-auto">
                                        <form method="POST" action="{{ route('seller.earnings.withdraw') }}" class="grid grid-cols-1 gap-3">
                                            @csrf
                                            <input type="hidden" name="__modal" value="create-seller-withdraw" />

                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المبلغ المطلوب</label>
                                                <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                            </div>

                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العملة</label>
                                                <input type="text" name="currency" value="{{ old('currency', 'ج.م') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                            </div>

                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">طريقة السحب (محفظة/حساب بنكي ...)</label>
                                                <input type="text" name="payment_method" value="{{ old('payment_method', 'فودافون كاش') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                            </div>

                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">بيانات التحويل (رقم المحفظة / الحساب)</label>
                                                <input type="text" name="reference" value="{{ old('reference') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                            </div>

                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">ملاحظات</label>
                                                <textarea name="description" rows="3" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">{{ old('description') }}</textarea>
                                            </div>

                                            <div class="flex justify-end mt-2">
                                                <button type="submit" class="px-4 py-2 rounded-lg bg-rose-500 text-white font-semibold hover:bg-rose-600">إرسال طلب السحب</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </details>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    يمكنك طلب سحب من الرصيد المتاح فقط، وسيتم مراجعة الطلب من إدارة المنصة.
                </p>
            </div>

            <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
                <div class="flex flex-col gap-3 mb-4">
                    <div class="text-xl font-bold">سجل طلبات السحب</div>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <div class="relative flex-1">
                        <input id="seller-withdraw-search" type="text" placeholder="ابحث..." class="w-full ps-10 pe-3 py-2 rounded-lg bg-gray-100 text-gray-900 placeholder:text-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-400 dark:border-slate-700" />
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <select id="seller-withdraw-filter-status" class="h-10 px-3 rounded-lg bg-gray-100 text-gray-900 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400 dark:bg-slate-800 dark:text-white dark:border-slate-700">
                        <option value="">كل الحالات</option>
                        <option value="pending">قيد المراجعة</option>
                        <option value="approved">تمت الموافقة</option>
                        <option value="rejected">مرفوض</option>
                    </select>
                </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-right">
                            <thead>
                                <tr class="text-gray-600 border-b border-gray-200 dark:text-slate-300 dark:border-slate-700 text-xs font-semibold">
                                    <th class="py-3 px-4 whitespace-nowrap">المبلغ</th>
                                    <th class="py-3 px-4 whitespace-nowrap">المبلغ المعتمد / المسحوب</th>
                                    <th class="py-3 px-4 whitespace-nowrap">الحالة</th>
                                    <th class="py-3 px-4 whitespace-nowrap">التاريخ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                            @forelse(($transactions ?? []) as $tr)
                                @php($status = (string) ($tr->status ?? 'pending'))
                                @php($amountText = number_format((float) ($tr->amount ?? 0), 2, '.', ',') . ' ' . ($tr->currency ?? ''))
                                @php($approvedText = (($status === 'approved') ? number_format((float) (($tr->approved_amount ?? $tr->amount) ?? 0), 2, '.', ',') . ' ' . ($tr->currency ?? '') : '-'))
                                @php($dateText = (string) (optional($tr->created_at)->format('Y-m-d H:i') ?? ''))
                                <tr data-seller-withdraw-row data-status="{{ $status }}" data-search="{{ mb_strtolower($amountText . ' ' . $approvedText . ' ' . $status . ' ' . $dateText) }}">
                                    <td class="py-4 px-4 font-semibold whitespace-nowrap">{{ number_format((float) ($tr->amount ?? 0), 2, '.', ',') }} {{ $tr->currency ?? '' }}</td>
                                    <td class="py-4 px-4 font-semibold whitespace-nowrap">
                                        @if(($tr->status ?? 'pending') === 'approved')
                                            @php($approvedAmount = $tr->approved_amount ?? $tr->amount)
                                            {{ number_format((float) ($approvedAmount ?? 0), 2, '.', ',') }} {{ $tr->currency ?? '' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @php($badge = 'bg-slate-700 text-slate-200')
                                        @if($status === 'approved')
                                            @php($badge = 'bg-emerald-500/20 text-emerald-300')
                                        @elseif($status === 'rejected')
                                            @php($badge = 'bg-red-500/20 text-red-300')
                                        @endif
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $badge }}">
                                            {{ $status === 'approved' ? 'تمت الموافقة' : ($status === 'rejected' ? 'مرفوض' : 'قيد المراجعة') }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ optional($tr->created_at)->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-600 dark:text-gray-300">لا توجد طلبات سحب بعد.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

<script>
    (function () {
        var rows = Array.prototype.slice.call(document.querySelectorAll('[data-seller-withdraw-row]'));
        var input = document.getElementById('seller-withdraw-search');
        var statusSel = document.getElementById('seller-withdraw-filter-status');

        var apply = function () {
            var q = String(input && input.value ? input.value : '').trim().toLowerCase();
            var s = String(statusSel && statusSel.value ? statusSel.value : '').trim();

            rows.forEach(function (row) {
                var hay = String(row.getAttribute('data-search') || '').toLowerCase();
                var rowStatus = String(row.getAttribute('data-status') || '');

                var ok = true;
                if (q && hay.indexOf(q) === -1) ok = false;
                if (s && rowStatus !== s) ok = false;

                row.style.display = ok ? '' : 'none';
            });
        };

        if (input) input.addEventListener('input', apply);
        if (statusSel) statusSel.addEventListener('change', apply);
    })();
</script>
