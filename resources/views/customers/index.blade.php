<x-layouts.app :title="'العملاء'">
    <div id="customers-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">العملاء</h1>

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
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="text-xl font-bold">العملاء</div>

                    @php($isAssignOpen = old('__modal') === 'assign-card')
                    <details {{ ($isAssignOpen && $errors->any()) ? 'open' : '' }}>
                        <summary class="cursor-pointer px-3 py-1.5 text-sm font-semibold rounded-lg bg-gray-100 border border-gray-200 text-gray-800 hover:bg-gray-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white dark:hover:bg-slate-700 [&::-webkit-details-marker]:hidden">ربط كارت</summary>
                        <div class="fixed inset-0 z-40">
                            <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                            <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                        <div class="font-semibold text-gray-800 dark:text-gray-100">ربط كارت بعميل</div>
                                        <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                    </div>
                                    <div class="p-4 max-h-[75vh] overflow-auto">
                                        <form method="POST" action="{{ route('customers.assignCard') }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @csrf
                                            <input type="hidden" name="__modal" value="assign-card" />

                                            <div class="md:col-span-2">
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العميل</label>
                                                <select name="customer_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                                    @foreach(($customers ?? []) as $c)
                                                        <option value="{{ $c->id }}" {{ (string) old('customer_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->phone ?? '-' }})</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="md:col-span-2">
                                                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الكارت</label>
                                                <select name="card_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                                    @foreach(($availableCards ?? []) as $card)
                                                        <option value="{{ $card->id }}" {{ (string) old('card_id') === (string) $card->id ? 'selected' : '' }}>{{ $card->card_number }} ({{ (int) ($card->points ?? 0) }} نقطة)</option>
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
                </div>

                <div class="relative w-96">
                    <input id="customers-search" type="text" placeholder="ابحث عن عميل بالاسم، التليفون، العنوان..." class="w-full ps-10 pe-3 py-2 rounded-lg bg-gray-100 text-gray-900 placeholder:text-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-400 dark:border-slate-700" />
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400 dark:text-slate-400"></i>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="text-gray-600 border-b border-gray-200 dark:text-slate-300 dark:border-slate-700">
                            <th class="py-3">كود العميل</th>
                            <th class="py-3">الاسم</th>
                            <th class="py-3">تليفون</th>
                            <th class="py-3">عنوان</th>
                            <th class="py-3">بريد الكتروني</th>
                            <th class="py-3">كود الكارت</th>
                            <th class="py-3">النقاط</th>
                            <th class="py-3">القيمة</th>
                            <th class="py-3">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                        @forelse(($customers ?? []) as $customer)
                            @php($latestCard = $customer->latestCard)
                            @php($pointsSum = (int) ($customer->cards_points_sum ?? 0))
                            <tr data-customer-row data-search="{{ mb_strtolower('#' . ($customer->id ?? '') . ' ' . ($customer->name ?? '') . ' ' . ($customer->phone ?? '') . ' ' . ($customer->address ?? '') . ' ' . ($customer->email ?? '') . ' ' . ($latestCard->card_number ?? '') . ' ' . ($pointsSum ?? 0) . ' ' . ($latestCard->amount ?? '')) }}">
                                <td class="py-3">#{{ $customer->id }}</td>
                                <td class="py-3 font-semibold">{{ $customer->name }}</td>
                                <td class="py-3">{{ $customer->phone ?? '-' }}</td>
                                <td class="py-3">{{ $customer->address ?? '-' }}</td>
                                <td class="py-3">{{ $customer->email ?? '-' }}</td>
                                <td class="py-3">{{ $latestCard?->card_number ?? '-' }}</td>
                                <td class="py-3">
                                    @if(($customer->cards ?? collect())->count() > 0)
                                        <div class="flex flex-col gap-1">
                                            @foreach($customer->cards as $card)
                                                <div class="text-rose-300 font-bold text-sm">
                                                    {{ $card->card_number }}: {{ number_format((int) ($card->points ?? 0), 0, '.', ',') }} نقطة
                                                </div>
                                            @endforeach
                                            <div class="text-xs text-gray-500 dark:text-slate-400">الإجمالي: {{ number_format($pointsSum, 0, '.', ',') }} نقطة</div>
                                        </div>
                                    @else
                                        <div class="text-gray-500 dark:text-slate-400">-</div>
                                    @endif
                                </td>
                                <td class="py-3">{{ number_format((float) ($latestCard?->amount ?? 0), 2, '.', ',') }}</td>
                                <td class="py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @php($isEditOpen = old('__modal') === ('edit-' . $customer->id))
                                        <details {{ ($isEditOpen && $errors->any()) ? 'open' : '' }}>
                                            <summary class="cursor-pointer px-3 py-1.5 text-sm font-semibold rounded-lg bg-gray-100 border border-gray-200 text-gray-800 hover:bg-gray-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white dark:hover:bg-slate-700 [&::-webkit-details-marker]:hidden">تعديل</summary>
                                            <div class="fixed inset-0 z-40">
                                                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                                                <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                                            <div class="font-semibold text-gray-800 dark:text-gray-100">تعديل عميل</div>
                                                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                                        </div>
                                                        <div class="p-4 max-h-[75vh] overflow-auto">
                                                            <form method="POST" action="{{ route('customers.update', $customer->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="__modal" value="edit-{{ $customer->id }}" />

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الاسم</label>
                                                                    <input name="name" value="{{ $isEditOpen ? old('name', $customer->name) : $customer->name }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">تليفون</label>
                                                                    <input name="phone" value="{{ $isEditOpen ? old('phone', $customer->phone) : $customer->phone }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">بريد إلكتروني</label>
                                                                    <input type="email" name="email" value="{{ $isEditOpen ? old('email', $customer->email) : $customer->email }}" placeholder="example@example.example" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="example@example.example" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العنوان</label>
                                                                    <input name="address" value="{{ $isEditOpen ? old('address', $customer->address) : $customer->address }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                                </div>

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">كلمة المرور (اختياري)</label>
                                                                    <input type="password" name="password" value="" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
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

                                        @php($isNotifyOpen = old('__modal') === ('notify-' . $customer->id))
                                        <details {{ ($isNotifyOpen && $errors->any()) ? 'open' : '' }}>
                                            <summary class="cursor-pointer px-3 py-1.5 text-sm font-semibold rounded-lg bg-rose-600 text-white hover:bg-rose-700 [&::-webkit-details-marker]:hidden">إرسال إشعار</summary>
                                            <div class="fixed inset-0 z-40">
                                                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                                                <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                                            <div class="font-semibold text-gray-800 dark:text-gray-100">إرسال إشعار</div>
                                                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                                        </div>
                                                        <div class="p-4 max-h-[75vh] overflow-auto">
                                                            <form method="POST" action="{{ route('customers.notify', $customer->id) }}" class="grid grid-cols-1 gap-3">
                                                                @csrf
                                                                <input type="hidden" name="__modal" value="notify-{{ $customer->id }}" />

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العنوان</label>
                                                                    <input name="title" value="{{ $isNotifyOpen ? old('title') : '' }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المحتوى</label>
                                                                    <textarea name="body" rows="4" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>{{ $isNotifyOpen ? old('body') : '' }}</textarea>
                                                                </div>

                                                                <div class="flex justify-end">
                                                                    <button type="submit" class="px-4 py-2 rounded-lg bg-rose-500 text-white font-semibold hover:bg-rose-600">إرسال</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </details>

                                        <form method="POST" action="{{ route('customers.destroy', $customer->id) }}" onsubmit="return confirm('حذف العميل؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 text-sm font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700">حذف</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-4 text-center text-slate-400" colspan="9">لا يوجد عملاء</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                @if(method_exists(($customers ?? null), 'links'))
                    {{ $customers->links() }}
                @endif
            </div>
        </div>

        @php($isCreateOpen = old('__modal') === 'create')
        <details class="fixed bottom-6 left-6 z-50" {{ ($isCreateOpen && $errors->any()) ? 'open' : '' }}>
            <summary class="cursor-pointer w-14 h-14 rounded-full bg-gradient-to-br from-rose-400 to-orange-400 text-white shadow-lg flex items-center justify-center hover:opacity-95 [&::-webkit-details-marker]:hidden" title="إضافة عميل">
                <i data-lucide="plus" class="w-6 h-6"></i>
            </summary>
            <div class="fixed inset-0 z-40">
                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="font-semibold text-gray-800 dark:text-gray-100">إضافة عميل</div>
                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                        </div>

                        <div class="p-4 max-h-[75vh] overflow-auto">
                            <form method="POST" action="{{ route('customers.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @csrf
                                <input type="hidden" name="__modal" value="create" />

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الاسم</label>
                                    <input name="name" value="{{ old('name') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">تليفون</label>
                                    <input name="phone" value="{{ old('phone') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                </div>

                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">بريد إلكتروني</label>
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="example@example.example" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="example@example.example" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العنوان</label>
                                    <input name="address" value="{{ old('address') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">كلمة المرور</label>
                                    <input type="password" name="password" value="" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
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
                var input = document.getElementById('customers-search');
                if (!input) return;

                var rows = Array.prototype.slice.call(document.querySelectorAll('[data-customer-row]'));
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