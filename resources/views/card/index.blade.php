<x-layouts.app :title="'مولد الكروت'">
    <div id="cards-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">مولد الكروت</h1>

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
                        <div class="text-sm text-gray-600 dark:text-slate-300">عدد الكروت</div>
                        <div class="text-2xl font-bold">{{ number_format((int) ($totalCards ?? 0), 0, '.', ',') }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-emerald-500/20 flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-6 h-6 text-emerald-300"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-slate-300">إجمالي النقاط</div>
                        <div class="text-2xl font-bold">{{ number_format((int) ($totalPoints ?? 0), 0, '.', ',') }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center">
                        <i data-lucide="sparkles" class="w-6 h-6 text-blue-300"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-slate-300">إجمالي القيمة</div>
                        <div class="text-2xl font-bold text-rose-300">{{ number_format((float) ($totalAmount ?? 0), 2, '.', ',') }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-rose-500/20 flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-6 h-6 text-rose-300"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="text-xl font-bold">الكروت</div>
                <div class="relative w-72">
                    <input id="cards-search" type="text" placeholder="ابحث عن كارت..." class="w-full ps-10 pe-3 py-2 rounded-lg bg-gray-100 text-gray-900 placeholder:text-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-400 dark:border-slate-700" />
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400 dark:text-slate-400"></i>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="text-gray-600 border-b border-gray-200 dark:text-slate-300 dark:border-slate-700">
                            <th class="py-3">كود الكارت</th>
                            <th class="py-3">اسم الكارت</th>
                            <th class="py-3">النوع</th>
                            <th class="py-3">النقاط</th>
                            <th class="py-3">القيمة</th>
                            <th class="py-3">التوزيع</th>
                            <th class="py-3">الحالة</th>
                            <th class="py-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                        @forelse(($cards ?? []) as $c)
                            @php($pricingCurrency = ((float) ($c->price_in_uk ?? 0) > 0) ? 'uk' : (((float) ($c->price_in_us ?? 0) > 0) ? 'us' : 'eg'))
                            @php($pricingPointPrice = $pricingCurrency === 'uk' ? (float) ($c->price_in_uk ?? 0) : ($pricingCurrency === 'us' ? (float) ($c->price_in_us ?? 0) : (float) ($c->price_in_eg ?? 0)))
                            <tr data-card-row data-search="{{ mb_strtolower(($c->card_number ?? '') . ' ' . ($c->card_holder ?? '') . ' ' . ($c->card_type ?? '') . ' ' . ($c->type ?? '') . ' ' . ($c->points ?? '') . ' ' . ($c->amount ?? '') . ' ' . ($c->distribution ?? '') . ' ' . ($c->status ?? '')) }}">
                                <td class="py-3 font-semibold">{{ $c->card_number }}</td>
                                <td class="py-3">{{ $c->card_holder ?? '-' }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-1 rounded-md text-xs font-semibold bg-gray-100 border border-gray-200 text-gray-700 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200">{{ $c->card_type ?? '-' }}</span>
                                </td>
                                <td class="py-3">{{ number_format((int) ($c->points ?? 0), 0, '.', ',') }}</td>
                                <td class="py-3 text-rose-300 font-bold">{{ number_format((float) ($c->amount ?? 0), 2, '.', ',') }}</td>
                                <td class="py-3">{{ $c->distribution ?? '-' }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-1 rounded-md text-xs font-semibold bg-gray-100 border border-gray-200 text-gray-700 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200">{{ $c->status ?? 'active' }}</span>
                                </td>
                                <td class="py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @php($isEditOpen = old('__modal') === ('edit-' . $c->id))
                                        <details {{ ($isEditOpen && $errors->any()) ? 'open' : '' }}>
                                            <summary class="cursor-pointer px-3 py-1.5 text-sm font-semibold rounded-lg bg-gray-100 border border-gray-200 text-gray-800 hover:bg-gray-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white dark:hover:bg-slate-700 [&::-webkit-details-marker]:hidden">تعديل</summary>
                                            <div class="fixed inset-0 z-40">
                                                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                                                <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                                            <div class="font-semibold text-gray-800 dark:text-gray-100">تعديل كارت</div>
                                                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                                        </div>
                                                        <div class="p-4 max-h-[75vh] overflow-auto">
                                                            <form method="POST" action="{{ route('cards.update', $c->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="__modal" value="edit-{{ $c->id }}" />

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">كود الكارت</label>
                                                                    <input name="card_number" value="{{ $isEditOpen ? old('card_number', $c->card_number) : $c->card_number }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">تقدر تسيبه زي ما هو.</div>
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Card Type</label>
                                                                    <input name="card_type" value="{{ $isEditOpen ? old('card_type', $c->card_type) : $c->card_type }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Type</label>
                                                                    <input name="type" value="{{ $isEditOpen ? old('type', $c->type) : $c->type }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المالك</label>
                                                                    <input name="card_holder" value="{{ $isEditOpen ? old('card_holder', $c->card_holder) : $c->card_holder }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">CVV</label>
                                                                    <input name="cvv" value="{{ $isEditOpen ? old('cvv', $c->cvv) : $c->cvv }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Expiry Date</label>
                                                                    <input name="expiry_date" value="{{ $isEditOpen ? old('expiry_date', $c->expiry_date) : $c->expiry_date }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">التوزيع</label>
                                                                    <input name="distribution" value="{{ $isEditOpen ? old('distribution', $c->distribution) : ($c->distribution ?? '') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الحالة</label>
                                                                    <select name="status" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                                                        @php($editStatus = $isEditOpen ? old('status', $c->status) : ($c->status ?? 'active'))
                                                                        <option value="active" {{ $editStatus === 'active' ? 'selected' : '' }}>active</option>
                                                                        <option value="inactive" {{ $editStatus === 'inactive' ? 'selected' : '' }}>inactive</option>
                                                                        <option value="pending" {{ $editStatus === 'pending' ? 'selected' : '' }}>pending</option>
                                                                        <option value="blocked" {{ $editStatus === 'blocked' ? 'selected' : '' }}>blocked</option>
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">النقاط</label>
                                                                    <input type="number" min="0" name="points" value="{{ $isEditOpen ? old('points', $c->points) : $c->points }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">سعر النقطة</label>
                                                                    <input type="number" step="0.01" min="0" name="point_price" value="{{ $isEditOpen ? old('point_price', $pricingPointPrice) : $pricingPointPrice }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العملة</label>
                                                                    <select name="currency" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                                                        @php($editCurrency = $isEditOpen ? old('currency', $pricingCurrency) : $pricingCurrency)
                                                                        <option value="eg" {{ $editCurrency === 'eg' ? 'selected' : '' }}>EG</option>
                                                                        <option value="us" {{ $editCurrency === 'us' ? 'selected' : '' }}>US</option>
                                                                        <option value="uk" {{ $editCurrency === 'uk' ? 'selected' : '' }}>UK</option>
                                                                    </select>
                                                                </div>

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العميل (اختياري)</label>
                                                                    <select name="customer_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                                                        <option value="">-</option>
                                                                        @foreach(($customers ?? []) as $cu)
                                                                            <option value="{{ $cu->id }}" {{ (string) ($isEditOpen ? old('customer_id', $c->customer_id) : $c->customer_id) === (string) $cu->id ? 'selected' : '' }}>{{ $cu->name }}</option>
                                                                        @endforeach
                                                                    </select>
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

                                        <form method="POST" action="{{ route('cards.destroy', $c->id) }}" onsubmit="return confirm('حذف الكارت؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 text-sm font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700">حذف</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-4 text-center text-slate-400" colspan="8">لا يوجد كروت</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @php($isCreateOpen = old('__modal') === 'create')
        <details class="fixed bottom-6 left-6 z-50" {{ ($isCreateOpen && $errors->any()) ? 'open' : '' }}>
            <summary class="cursor-pointer w-14 h-14 rounded-full bg-gradient-to-br from-rose-400 to-orange-400 text-white shadow-lg flex items-center justify-center hover:opacity-95 [&::-webkit-details-marker]:hidden" title="إضافة كارت">
                <i data-lucide="plus" class="w-6 h-6"></i>
            </summary>
            <div class="fixed inset-0 z-40">
                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="font-semibold text-gray-800 dark:text-gray-100">إضافة كارت</div>
                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                        </div>

                        <div class="p-4 max-h-[75vh] overflow-auto">
                            <form method="POST" action="{{ route('cards.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @csrf
                                <input type="hidden" name="__modal" value="create" />

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">كود الكارت</label>
                                    <input name="card_number" value="{{ old('card_number') }}" placeholder="اتركه فارغ للتوليد التلقائي" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                </div>

                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Card Type</label>
                                    <input name="card_type" value="{{ old('card_type') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Type</label>
                                    <input name="type" value="{{ old('type') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المالك</label>
                                    <input name="card_holder" value="{{ old('card_holder') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">CVV</label>
                                    <input name="cvv" value="{{ old('cvv') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Expiry Date</label>
                                    <input name="expiry_date" value="{{ old('expiry_date') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">التوزيع</label>
                                    <input name="distribution" value="{{ old('distribution') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                </div>

                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الحالة</label>
                                    <select name="status" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>active</option>
                                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>inactive</option>
                                        <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>pending</option>
                                        <option value="blocked" {{ old('status') === 'blocked' ? 'selected' : '' }}>blocked</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">النقاط</label>
                                    <input type="number" min="0" name="points" value="{{ old('points', 0) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">سعر النقطة</label>
                                    <input type="number" step="0.01" min="0" name="point_price" value="{{ old('point_price', 0) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العملة</label>
                                    <select name="currency" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                        <option value="eg" {{ old('currency', 'eg') === 'eg' ? 'selected' : '' }}>EG</option>
                                        <option value="us" {{ old('currency') === 'us' ? 'selected' : '' }}>US</option>
                                        <option value="uk" {{ old('currency') === 'uk' ? 'selected' : '' }}>UK</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العميل (اختياري)</label>
                                    <select name="customer_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                        <option value="" {{ old('customer_id') ? '' : 'selected' }}>-</option>
                                        @foreach(($customers ?? []) as $cu)
                                            <option value="{{ $cu->id }}" {{ (string) old('customer_id') === (string) $cu->id ? 'selected' : '' }}>{{ $cu->name }}</option>
                                        @endforeach
                                    </select>
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

        <script>
            (function () {
                var input = document.getElementById('cards-search');
                if (!input) return;

                var rows = Array.prototype.slice.call(document.querySelectorAll('[data-card-row]'));
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