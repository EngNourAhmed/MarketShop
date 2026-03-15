<x-layouts.app :title="'طلبات السحب'">
    <div id="withdraw-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">طلبات السحب</h1>

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
                    <div class="text-xl font-bold">طلبات السحب</div>

                    @php($isCreateOpen = old('__modal') === 'create-withdraw')
                    <details {{ ($isCreateOpen && $errors->any()) ? 'open' : '' }}>
                        <summary class="cursor-pointer px-3 py-1.5 text-sm font-semibold rounded-lg bg-rose-500 text-white hover:bg-rose-600 [&::-webkit-details-marker]:hidden">إضافة</summary>
                        <div class="fixed inset-0 z-40">
                            <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                            <div class="relative mx-auto my-6 w-[95vw] max-w-2xl">
                                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                        <div class="font-semibold text-gray-800 dark:text-gray-100">إضافة طلب سحب</div>
                                        <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                    </div>
                                    <div class="p-4 max-h-[75vh] overflow-auto">
                                        <form method="POST" action="{{ route('withdrawRequest.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @csrf
                                            <input type="hidden" name="__modal" value="create-withdraw" />
                                            <input type="hidden" name="status" value="pending" />

                                            <div class="md:col-span-2">
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العميل</label>
                                                <select name="customer_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                                    @foreach(($customers ?? []) as $c)
                                                        <option value="{{ $c->id }}" {{ (string) old('customer_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->phone ?? '-' }})</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">النقاط</label>
                                                <input type="number" name="points" value="{{ old('points', 0) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                            </div>

                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المبلغ</label>
                                                <input type="number" step="0.01" name="amount" value="{{ old('amount', 0) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                            </div>

                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العملة</label>
                                                <input type="text" name="currency" value="{{ old('currency', 'ج.م') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                            </div>

                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">نوع الطلب</label>
                                                <input type="text" name="payment_method" value="{{ old('payment_method', 'فودافون كاش') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                            </div>

                                            <div class="md:col-span-2">
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">بيانات التحويل (مرجع/رقم حساب/رقم محفظة)</label>
                                                <input type="text" name="reference" value="{{ old('reference') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                            </div>

                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">اسم البنك</label>
                                                <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                            </div>
                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">رقم الحساب</label>
                                                <input type="text" name="account_number" value="{{ old('account_number') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                            </div>
                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">اسم الحساب</label>
                                                <input type="text" name="account_name" value="{{ old('account_name') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                            </div>
                                            <div>
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المدينة</label>
                                                <input type="text" name="bank_city" value="{{ old('bank_city') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العنوان</label>
                                                <input type="text" name="bank_address" value="{{ old('bank_address') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">ملاحظات</label>
                                                <textarea name="description" rows="3" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">{{ old('description') }}</textarea>
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
                    <input id="withdraw-search" type="text" placeholder="ابحث باسم العميل رقم التليفون..." class="w-full ps-10 pe-3 py-2 rounded-lg bg-gray-100 text-gray-900 placeholder:text-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-400 dark:border-slate-700" />
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400 dark:text-slate-400"></i>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-right">
                    <thead>
                        <tr class="text-gray-600 border-b border-gray-200 dark:text-slate-300 dark:border-slate-700">
                            <th class="py-3 px-4">العميل</th>
                            <th class="py-3 px-4">النقاط</th>
                            <th class="py-3 px-4">المبلغ</th>
                            <th class="py-3 px-4">نوع الطلب</th>
                            <th class="py-3 px-4">بيانات التحويل</th>
                            <th class="py-3 px-4">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                        @foreach(($withdrawRequests ?? []) as $wr)
                            @php($status = $wr->status ?? 'pending')
                            @php($type = $wr->payment_method ?? '-')
                            @php($transferInfo = $wr->reference ?: ($wr->account_number ?: '-'))
                            @php($searchText = mb_strtolower((optional($wr->customer)->name ?? '') . ' ' . (optional($wr->customer)->phone ?? '') . ' ' . ($wr->amount ?? '') . ' ' . ($wr->points ?? '') . ' ' . $type . ' ' . $transferInfo))
                            <tr data-withdraw-row data-search="{{ $searchText }}">
                                <td class="py-4 px-4 font-semibold">{{ optional($wr->customer)->name ?? '-' }}</td>
                                <td class="py-4 px-4">{{ (int) ($wr->points ?? 0) }}</td>
                                <td class="py-4 px-4 font-bold">{{ number_format((float) ($wr->amount ?? 0), 2, '.', ',') }} {{ $wr->currency ?? '' }}</td>
                                <td class="py-4 px-4">
                                    @php($badge = 'bg-slate-700 text-slate-200')
                                    @if(str_contains(mb_strtolower($type), 'فودافون'))
                                        @php($badge = 'bg-amber-500/20 text-amber-300')
                                    @elseif(str_contains(mb_strtolower($type), 'موبيل') || str_contains(mb_strtolower($type), 'mobile'))
                                        @php($badge = 'bg-blue-500/20 text-blue-300')
                                    @elseif(str_contains(mb_strtolower($type), 'فوري') || str_contains(mb_strtolower($type), 'fawry'))
                                        @php($badge = 'bg-violet-500/20 text-violet-300')
                                    @endif
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $badge }}">{{ $type }}</span>
                                </td>
                                <td class="py-4 px-4 text-gray-700 dark:text-slate-200">{{ $transferInfo }}</td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('withdrawRequest.update', $wr->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="approved" />
                                            <button type="submit" {{ $status === 'approved' ? 'disabled' : '' }} class="w-11 h-10 rounded-lg bg-emerald-500 text-white font-bold hover:bg-emerald-600 disabled:opacity-40 disabled:cursor-not-allowed">✓</button>
                                        </form>

                                        <form method="POST" action="{{ route('withdrawRequest.update', $wr->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected" />
                                            <button type="submit" {{ $status === 'rejected' ? 'disabled' : '' }} class="w-11 h-10 rounded-lg bg-red-500 text-white font-bold hover:bg-red-600 disabled:opacity-40 disabled:cursor-not-allowed">✕</button>
                                        </form>

                                        @php($isEditOpen = old('__modal') === ('edit-withdraw-' . $wr->id))
                                        <details {{ ($isEditOpen && $errors->any()) ? 'open' : '' }}>
                                            <summary class="cursor-pointer w-11 h-10 rounded-lg bg-gray-200 text-gray-800 font-bold hover:bg-gray-300 dark:bg-slate-700 dark:text-white dark:hover:bg-slate-600 flex items-center justify-center [&::-webkit-details-marker]:hidden">✎</summary>
                                            <div class="fixed inset-0 z-40">
                                                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                                                <div class="relative mx-auto my-6 w-[95vw] max-w-2xl">
                                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                                            <div class="font-semibold text-gray-800 dark:text-gray-100">تعديل طلب سحب</div>
                                                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                                        </div>
                                                        <div class="p-4 max-h-[75vh] overflow-auto">
                                                            <form method="POST" action="{{ route('withdrawRequest.update', $wr->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="__modal" value="edit-withdraw-{{ $wr->id }}" />

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العميل</label>
                                                                    <select name="customer_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                                                        @foreach(($customers ?? []) as $c)
                                                                            <option value="{{ $c->id }}" {{ (string) old('customer_id', $wr->customer_id) === (string) $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->phone ?? '-' }})</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">النقاط</label>
                                                                    <input type="number" name="points" value="{{ old('points', $wr->points) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المبلغ</label>
                                                                    <input type="number" step="0.01" name="amount" value="{{ old('amount', $wr->amount) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العملة</label>
                                                                    <input type="text" name="currency" value="{{ old('currency', $wr->currency) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">نوع الطلب</label>
                                                                    <input type="text" name="payment_method" value="{{ old('payment_method', $wr->payment_method) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">بيانات التحويل</label>
                                                                    <input type="text" name="reference" value="{{ old('reference', $wr->reference) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الحالة</label>
                                                                    <select name="status" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                                                        @foreach(['pending' => 'قيد المراجعة', 'approved' => 'تمت الموافقة', 'rejected' => 'مرفوض'] as $key => $label)
                                                                            <option value="{{ $key }}" {{ (string) old('status', $wr->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">ملاحظات</label>
                                                                    <input type="text" name="description" value="{{ old('description', $wr->description) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
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

                                        @php($isDeleteOpen = old('__modal') === ('delete-withdraw-' . $wr->id))
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
                                                        <div class="p-4 text-gray-800 dark:text-gray-100">
                                                            هل تريد حذف طلب السحب للعميل <span class="font-semibold">{{ optional($wr->customer)->name ?? '-' }}</span>؟
                                                        </div>
                                                        <div class="p-4 flex justify-end gap-2">
                                                            <button type="button" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600" onclick="this.closest('details').removeAttribute('open')">إلغاء</button>
                                                            <form method="POST" action="{{ route('withdrawRequest.destroy', $wr->id) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <input type="hidden" name="__modal" value="delete-withdraw-{{ $wr->id }}" />
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
                    var input = document.getElementById('withdraw-search');
                    if (!input) return;

                    var rows = Array.prototype.slice.call(document.querySelectorAll('[data-withdraw-row]'));
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