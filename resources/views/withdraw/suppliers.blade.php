<x-layouts.app :title="'طلبات سحب الموردين'">
    <div id="supplier-withdraw-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">طلبات سحب الموردين</h1>

        @if(session('status'))
            <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-slate-300">طلبات سحب البائعين</div>
                        <div class="text-2xl font-bold">{{ number_format((float) ($withdrawVendorTotal ?? 0), 2, '.', ',') }} ج.م</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-emerald-500/20 flex items-center justify-center">
                        <i data-lucide="store" class="w-6 h-6 text-emerald-300"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-slate-300">طلبات سحب المصانع</div>
                        <div class="text-2xl font-bold">{{ number_format((float) ($withdrawFactoryTotal ?? 0), 2, '.', ',') }} ج.م</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center">
                        <i data-lucide="factory" class="w-6 h-6 text-blue-300"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-slate-300">إجمالي طلبات السحب</div>
                        <div class="text-2xl font-bold text-rose-300">{{ number_format((float) ($withdrawTotal ?? 0), 2, '.', ',') }} ج.م</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-rose-500/20 flex items-center justify-center">
                        <i data-lucide="wallet" class="w-6 h-6 text-rose-300"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-slate-300">متبقي سحب البائعين (موافقة جزئية)</div>
                        <div class="text-2xl font-bold text-amber-300">{{ number_format((float) ($withdrawRemainingVendorTotal ?? 0), 2, '.', ',') }} ج.م</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-amber-500/20 flex items-center justify-center">
                        <i data-lucide="clock" class="w-6 h-6 text-amber-300"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-slate-300">متبقي سحب المصانع (موافقة جزئية)</div>
                        <div class="text-2xl font-bold text-amber-300">{{ number_format((float) ($withdrawRemainingFactoryTotal ?? 0), 2, '.', ',') }} ج.م</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-amber-500/20 flex items-center justify-center">
                        <i data-lucide="clock" class="w-6 h-6 text-amber-300"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-slate-300">إجمالي المتبقي (موافقة جزئية)</div>
                        <div class="text-2xl font-bold text-amber-300">{{ number_format((float) ($withdrawRemainingTotal ?? 0), 2, '.', ',') }} ج.م</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-amber-500/20 flex items-center justify-center">
                        <i data-lucide="clock" class="w-6 h-6 text-amber-300"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                <div class="text-xl font-bold">طلبات السحب</div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative">
                        <input id="withdraw-search" type="text" placeholder="ابحث..." class="w-72 max-w-full ps-10 pe-3 py-2 rounded-lg bg-gray-100 text-gray-900 placeholder:text-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-400 dark:border-slate-700" />
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400 dark:text-slate-400"></i>
                        </div>
                    </div>

                    <select id="withdraw-filter-type" class="h-10 px-3 rounded-lg bg-gray-100 text-gray-900 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400 dark:bg-slate-800 dark:text-white dark:border-slate-700">
                        <option value="">كل الأنواع</option>
                        <option value="factory">مصانع</option>
                        <option value="vendor">بائعين</option>
                    </select>

                    <select id="withdraw-filter-status" class="h-10 px-3 rounded-lg bg-gray-100 text-gray-900 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400 dark:bg-slate-800 dark:text-white dark:border-slate-700">
                        <option value="">كل الحالات</option>
                        <option value="pending">قيد المراجعة</option>
                        <option value="approved">تمت الموافقة</option>
                        <option value="rejected">مرفوض</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-slate-800">
                <table class="min-w-full text-right text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-900">
                        <tr class="text-gray-600 dark:text-slate-300">
                            <th class="py-3 px-4 whitespace-nowrap">المورد</th>
                            <th class="py-3 px-4 whitespace-nowrap">النوع</th>
                            <th class="py-3 px-4 whitespace-nowrap">المبلغ المطلوب</th>
                            <th class="py-3 px-4 whitespace-nowrap">المبلغ المعتمد</th>
                            <th class="py-3 px-4 whitespace-nowrap">طريقة السحب</th>
                            <th class="py-3 px-4 whitespace-nowrap">المرجع</th>
                            <th class="py-3 px-4 whitespace-nowrap">الحالة</th>
                            <th class="py-3 px-4 whitespace-nowrap">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-800 bg-white dark:bg-slate-950">
                        @forelse(($withdrawRequests ?? []) as $wr)
                            @php($status = $wr->status ?? 'pending')
                            @php($supplierName = (string) (optional($wr->supplier)->name ?? '-'))
                            @php($supplierType = (string) (optional($wr->supplier)->type ?? ''))
                            @php($typeLabel = $supplierType === 'factory' ? 'مصنع' : ($supplierType === 'vendor' ? 'محل' : ($supplierType ?: '-')))
                            <tr class="odd:bg-white even:bg-gray-50 dark:odd:bg-slate-950 dark:even:bg-slate-900/30" data-withdraw-row data-status="{{ $status }}" data-type="{{ $supplierType }}" data-search="{{ mb_strtolower($supplierName . ' ' . $typeLabel . ' ' . ($wr->reference ?? '') . ' ' . ($wr->payment_method ?? '') . ' ' . ($wr->currency ?? '') . ' ' . ($wr->amount ?? '') . ' ' . ($wr->approved_amount ?? '') . ' ' . $status) }}">
                                <td class="py-3 px-4 font-semibold whitespace-nowrap">{{ $supplierName }}</td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-md text-xs font-semibold bg-gray-100 border border-gray-200 text-gray-700 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200">{{ $typeLabel }}</span>
                                </td>
                                <td class="py-3 px-4 font-bold whitespace-nowrap">{{ number_format((float) ($wr->amount ?? 0), 2, '.', ',') }} {{ $wr->currency ?? '' }}</td>
                                <td class="py-3 px-4 font-bold whitespace-nowrap">
                                    @php($approvedAmount = $wr->approved_amount ?? $wr->amount)
                                    @if($approvedAmount !== null)
                                        {{ number_format((float) $approvedAmount, 2, '.', ',') }} {{ $wr->currency ?? '' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">{{ $wr->payment_method ?? '-' }}</td>
                                <td class="py-3 px-4 text-gray-700 dark:text-slate-200 whitespace-nowrap">{{ $wr->reference ?? '-' }}</td>
                                <td class="py-3 px-4 whitespace-nowrap">
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
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <details class="relative">
                                        <summary class="cursor-pointer inline-flex items-center justify-center gap-2 h-10 px-3 rounded-lg bg-gray-100 border border-gray-200 text-gray-800 hover:bg-gray-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white dark:hover:bg-slate-700 [&::-webkit-details-marker]:hidden">
                                            <span class="text-sm font-semibold">إجراءات</span>
                                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                        </summary>

                                        <div class="absolute z-20 mt-2 left-0 w-80 max-w-[90vw] rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-xl p-3">
                                            <div class="text-xs text-gray-500 dark:text-slate-400 mb-2">تحديث الحالة</div>

                                            <form method="POST" action="{{ route('supplier_withdraw.update', $wr->id) }}" class="flex items-center gap-2">
                                                @csrf
                                                @method('PUT')
                                                @php($defaultApprovedAmount = $wr->approved_amount ?? $wr->amount)
                                                <input type="hidden" name="status" value="approved" />
                                                <input
                                                    type="number"
                                                    name="approved_amount"
                                                    step="0.01"
                                                    min="0"
                                                    max="{{ (float) ($wr->amount ?? 0) }}"
                                                    value="{{ $defaultApprovedAmount }}"
                                                    class="flex-1 px-2 py-2 text-sm rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100"
                                                    placeholder="المبلغ المعتمد"
                                                />
                                                <button type="submit" {{ $status === 'approved' ? 'disabled' : '' }} class="h-10 px-3 rounded-lg bg-emerald-500 text-white font-semibold hover:bg-emerald-600 disabled:opacity-40 disabled:cursor-not-allowed">موافقة</button>
                                            </form>

                                            <form method="POST" action="{{ route('supplier_withdraw.update', $wr->id) }}" class="mt-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="rejected" />
                                                <button type="submit" {{ $status === 'rejected' ? 'disabled' : '' }} class="w-full h-10 rounded-lg bg-red-500 text-white font-semibold hover:bg-red-600 disabled:opacity-40 disabled:cursor-not-allowed">رفض</button>
                                            </form>

                                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-slate-700">
                                                <form method="POST" action="{{ route('supplier_withdraw.destroy', $wr->id) }}" onsubmit="return confirm('حذف طلب السحب؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full h-10 rounded-lg bg-gray-100 border border-gray-200 text-gray-800 font-semibold hover:bg-gray-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white dark:hover:bg-slate-700">حذف الطلب</button>
                                                </form>
                                            </div>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 text-center text-gray-600 dark:text-gray-300">لا توجد طلبات سحب للموردين حالياً</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>

<script>
    (function () {
        var rows = Array.prototype.slice.call(document.querySelectorAll('[data-withdraw-row]'));
        var input = document.getElementById('withdraw-search');
        var typeSel = document.getElementById('withdraw-filter-type');
        var statusSel = document.getElementById('withdraw-filter-status');

        var apply = function () {
            var q = String(input && input.value ? input.value : '').trim().toLowerCase();
            var t = String(typeSel && typeSel.value ? typeSel.value : '').trim();
            var s = String(statusSel && statusSel.value ? statusSel.value : '').trim();

            rows.forEach(function (row) {
                var hay = String(row.getAttribute('data-search') || '').toLowerCase();
                var rowType = String(row.getAttribute('data-type') || '');
                var rowStatus = String(row.getAttribute('data-status') || '');

                var ok = true;
                if (q && hay.indexOf(q) === -1) ok = false;
                if (t && rowType !== t) ok = false;
                if (s && rowStatus !== s) ok = false;

                row.style.display = ok ? '' : 'none';
            });
        };

        if (input) input.addEventListener('input', apply);
        if (typeSel) typeSel.addEventListener('change', apply);
        if (statusSel) statusSel.addEventListener('change', apply);
    })();
</script>
