<x-layouts.app :title="'شركات الشحن'">
    <div id="shipping-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">شركات الشحن</h1>
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


        <div class="mb-6 flex justify-start">
            @php($isCreateOpen = old('__modal') === 'create')
            <details {{ ($isCreateOpen && $errors->any()) ? 'open' : '' }}>
                <summary class="cursor-pointer px-4 py-2 rounded-lg bg-gradient-to-br from-rose-400 to-orange-400 text-white font-semibold shadow hover:opacity-95 [&::-webkit-details-marker]:hidden">+ إضافة شركة شحن</summary>
                <div class="fixed inset-0 z-40">
                    <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>

                    <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="font-semibold text-gray-800 dark:text-gray-100">إضافة شركة شحن</div>
                                <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                            </div>

                            <div class="p-4 max-h-[75vh] overflow-auto">
                                <form method="POST" action="{{ route('shipping.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @csrf
                                    <input type="hidden" name="__modal" value="create" />

                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الكود</label>
                                        <input name="code" value="{{ old('code') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                    </div>

                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الحالة</label>
                                        <select name="status" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>active</option>
                                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>inactive</option>
                                            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>pending</option>
                                            <option value="blocked" {{ old('status') === 'blocked' ? 'selected' : '' }}>blocked</option>
                                            <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>suspended</option>
                                        </select>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الاسم</label>
                                        <input name="name" value="{{ old('name') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العنوان</label>
                                        <input name="address" value="{{ old('address') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                    </div>

                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">تليفون</label>
                                        <input name="phone" value="{{ old('phone') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                    </div>

                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">البريد الإلكتروني</label>
                                        <input type="email" name="email" value="{{ old('email') }}" placeholder="example@example.example" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="example@example.example" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
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

        <div class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="text-xl font-bold">شركات الشحن</div>
                <div class="relative w-72">
                    <input id="shipping-search" type="text" placeholder="ابحث عن شركة شحن..." class="w-full ps-10 pe-3 py-2 rounded-lg bg-gray-100 text-gray-900 placeholder:text-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-400 dark:border-slate-700" />
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400 dark:text-slate-400"></i>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="text-gray-600 border-b border-gray-200 dark:text-slate-300 dark:border-slate-700">
                            <th class="py-3">كود</th>
                            <th class="py-3">الاسم</th>
                            <th class="py-3">تليفون</th>
                            <th class="py-3">الحالة</th>
                            <th class="py-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
                        @forelse(($shippingCompanies ?? []) as $sc)
                            <tr data-shipping-row data-search="{{ mb_strtolower(($sc->code ?? '') . ' ' . $sc->name . ' ' . ($sc->phone ?? '') . ' ' . ($sc->email ?? '') . ' ' . ($sc->status ?? '') . ' ' . ($sc->address ?? '')) }}">
                                <td class="py-3">{{ !empty($sc->code) ? $sc->code : ('SH#' . $sc->id) }}</td>
                                <td class="py-3 font-semibold">{{ $sc->name }}</td>
                                <td class="py-3">{{ $sc->phone }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-1 rounded-md text-xs font-semibold bg-gray-100 border border-gray-200 text-gray-700 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200">{{ $sc->status ?? '-' }}</span>
                                </td>
                                <td class="py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @php($isEditOpen = old('__modal') === ('edit-' . $sc->id))
                                        <details {{ ($isEditOpen && $errors->any()) ? 'open' : '' }}>
                                            <summary class="cursor-pointer px-3 py-1.5 text-sm font-semibold rounded-lg bg-gray-100 border border-gray-200 text-gray-800 hover:bg-gray-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white dark:hover:bg-slate-700 [&::-webkit-details-marker]:hidden">تعديل</summary>
                                            <div class="fixed inset-0 z-40">
                                                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                                                <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                                            <div class="font-semibold text-gray-800 dark:text-gray-100">تعديل شركة شحن</div>
                                                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                                        </div>
                                                        <div class="p-4 max-h-[75vh] overflow-auto">
                                                            <form method="POST" action="{{ route('shipping.update', $sc->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="__modal" value="edit-{{ $sc->id }}" />

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الكود</label>
                                                                    <input name="code" value="{{ $isEditOpen ? old('code', $sc->code) : $sc->code }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الحالة</label>
                                                                    <select name="status" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                                                                        <option value="active" {{ ($isEditOpen ? old('status', $sc->status) : $sc->status) === 'active' ? 'selected' : '' }}>active</option>
                                                                        <option value="inactive" {{ ($isEditOpen ? old('status', $sc->status) : $sc->status) === 'inactive' ? 'selected' : '' }}>inactive</option>
                                                                        <option value="pending" {{ ($isEditOpen ? old('status', $sc->status) : $sc->status) === 'pending' ? 'selected' : '' }}>pending</option>
                                                                        <option value="blocked" {{ ($isEditOpen ? old('status', $sc->status) : $sc->status) === 'blocked' ? 'selected' : '' }}>blocked</option>
                                                                        <option value="suspended" {{ ($isEditOpen ? old('status', $sc->status) : $sc->status) === 'suspended' ? 'selected' : '' }}>suspended</option>
                                                                    </select>
                                                                </div>

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الاسم</label>
                                                                    <input name="name" value="{{ $isEditOpen ? old('name', $sc->name) : $sc->name }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العنوان</label>
                                                                    <input name="address" value="{{ $isEditOpen ? old('address', $sc->address) : $sc->address }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">تليفون</label>
                                                                    <input name="phone" value="{{ $isEditOpen ? old('phone', $sc->phone) : $sc->phone }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">البريد الإلكتروني</label>
                                                                    <input type="email" name="email" value="{{ $isEditOpen ? old('email', $sc->email) : $sc->email }}" placeholder="example@example.example" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="example@example.example" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
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

                                        <form method="POST" action="{{ route('shipping.destroy', $sc->id) }}" onsubmit="return confirm('حذف شركة الشحن؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 text-sm font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700">حذف</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-4 text-center text-gray-500 dark:text-slate-400" colspan="5">لا يوجد شركات شحن</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            (function () {
                var input = document.getElementById('shipping-search');
                if (!input) return;

                var rows = Array.prototype.slice.call(document.querySelectorAll('[data-shipping-row]'));
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