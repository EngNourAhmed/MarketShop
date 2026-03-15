<x-layouts.app :title="'المصروفات'">
    <div id="expenses-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">المصروفات</h1>

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

        <div class="mb-4 flex flex-col md:flex-row gap-3 items-center">
            <div class="relative flex-1 w-full">
                <input
                    type="text"
                    id="expense-search"
                    placeholder="ابحث في المصروفات..."
                    class="w-full p-3 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400" />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            @forelse(($expenses ?? []) as $exp)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden" data-expense-card data-search="{{ mb_strtolower($exp->description . ' ' . ($exp->category ?? '') . ' ' . ($exp->type ?? '') . ' ' . $exp->amount . ' ' . $exp->expense_date) }}">
                <div class="p-4 sm:p-5 flex items-center justify-between gap-4">

                    <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-gray-700 flex items-center justify-center border border-rose-100 dark:border-gray-600 overflow-hidden">
                        @if(!empty($exp->image))
                            <img src="{{ asset('storage/' . $exp->image) }}" alt="expense" class="w-full h-full object-cover" />
                        @else
                            <i data-lucide="receipt" class="w-5 h-5 text-rose-500"></i>
                        @endif
                    </div>
                    <div class="flex-1 text-right">
                        <div class="font-semibold text-gray-800 dark:text-gray-100">
                            {{ $exp->description }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $exp->expense_date }}
                        </div>

                    </div>

                    <div class="text-rose-600 font-bold text-lg whitespace-nowrap">
                        -{{ number_format((float) $exp->amount, 0, '.', ',') }} ج.م
                    </div>

                </div>

                <div class="px-4 pb-4 sm:px-5 sm:pb-5">
                    <div class="flex items-center justify-end gap-2">
                        <details>
                            <summary class="cursor-pointer px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/40 [&::-webkit-details-marker]:hidden">عرض</summary>
                            <div class="fixed inset-0 z-40">
                                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>

                                <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                            <div class="font-semibold text-gray-800 dark:text-gray-100">تفاصيل المصروف</div>
                                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                        </div>

                                        <div class="p-4">
                                            <div class="flex items-start gap-3">
                                                <div class="w-14 h-14 rounded-2xl bg-rose-50 dark:bg-gray-700 border border-rose-100 dark:border-gray-600 overflow-hidden flex items-center justify-center">
                                                    @if(!empty($exp->image))
                                                        <img src="{{ asset('storage/' . $exp->image) }}" alt="expense" class="w-full h-full object-cover" />
                                                    @else
                                                        <i data-lucide="receipt" class="w-6 h-6 text-rose-500"></i>
                                                    @endif
                                                </div>

                                                <div class="flex-1 text-right">
                                                    <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $exp->description }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $exp->expense_date }}</div>
                                                    <div class="text-sm text-gray-600 dark:text-gray-300 mt-2">{{ $exp->category }} - {{ $exp->type }}</div>
                                                </div>

                                                <div class="text-rose-600 font-bold text-lg whitespace-nowrap">
                                                    -{{ number_format((float) $exp->amount, 0, '.', ',') }} ج.م
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </details>

                        @php($isEditOpen = old('__modal') === ('edit-' . $exp->id))
                        <details {{ ($isEditOpen && $errors->any()) ? 'open' : '' }}>
                            <summary class="cursor-pointer px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/40 [&::-webkit-details-marker]:hidden">تعديل</summary>
                            <div class="fixed inset-0 z-40">
                                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>

                                <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                            <div class="font-semibold text-gray-800 dark:text-gray-100">تعديل مصروف</div>
                                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                        </div>

                                        <div class="p-4 max-h-[75vh] overflow-auto">
                                            <form method="POST" action="{{ route('expenses.update', $exp->id) }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="__modal" value="edit-{{ $exp->id }}" />

                                                <div class="md:col-span-2">
                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الوصف</label>
                                                    <input name="description" value="{{ $isEditOpen ? old('description', $exp->description) : $exp->description }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                </div>

                                                <div>
                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">التصنيف</label>
                                                    <input name="category" value="{{ $isEditOpen ? old('category', $exp->category) : $exp->category }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                </div>

                                                <div>
                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">النوع</label>
                                                    <input name="type" value="{{ $isEditOpen ? old('type', $exp->type) : $exp->type }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                </div>

                                                <div>
                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المبلغ</label>
                                                    <input type="number" step="0.01" name="amount" value="{{ $isEditOpen ? old('amount', $exp->amount) : $exp->amount }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                </div>

                                                <div>
                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">التاريخ</label>
                                                    <input type="date" name="expense_date" value="{{ $isEditOpen ? old('expense_date', $exp->expense_date) : $exp->expense_date }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">صورة (اختياري)</label>
                                                    <input type="file" name="image" accept="image/*" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
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

                        <form method="POST" action="{{ route('expenses.destroy', $exp->id) }}" onsubmit="return confirm('حذف المصروف؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 text-sm font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700">حذف</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 text-center text-gray-600 dark:text-gray-300">
                لا توجد مصروفات حالياً
            </div>
            @endforelse
        </div>

        <div class="mt-4">
            @if(method_exists(($expenses ?? null), 'links'))
            {{ $expenses->links() }}
            @endif
        </div>

        @php($isCreateOpen = old('__modal') === 'create')
        <details class="fixed bottom-6 left-6 z-30" {{ ($isCreateOpen && $errors->any()) ? 'open' : '' }}>
            <summary class="w-14 h-14 rounded-full bg-gradient-to-br from-rose-400 to-orange-400 text-white shadow-xl flex items-center justify-center cursor-pointer select-none text-3xl leading-none [&::-webkit-details-marker]:hidden">+</summary>
            <div class="fixed inset-0 z-40">
                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>

                <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="font-semibold text-gray-800 dark:text-gray-100">إضافة مصروف</div>
                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                        </div>

                        <div class="p-4 max-h-[75vh] overflow-auto">
                            <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @csrf
                                <input type="hidden" name="__modal" value="create" />

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الوصف</label>
                                    <input name="description" value="{{ old('description') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">التصنيف</label>
                                    <input name="category" value="{{ old('category') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">النوع</label>
                                    <input name="type" value="{{ old('type') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المبلغ</label>
                                    <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">التاريخ</label>
                                    <input type="date" name="expense_date" value="{{ old('expense_date') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">صورة (اختياري)</label>
                                    <input type="file" name="image" accept="image/*" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
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
                var input = document.getElementById('expense-search');
                if (!input) return;

                var cards = Array.prototype.slice.call(document.querySelectorAll('[data-expense-card]'));
                var handler = function () {
                    var q = (input.value || '').trim().toLowerCase();
                    cards.forEach(function (card) {
                        var hay = (card.getAttribute('data-search') || '').toLowerCase();
                        card.style.display = q === '' || hay.indexOf(q) !== -1 ? '' : 'none';
                    });
                };
                input.addEventListener('input', handler);
            })();
        </script>
</x-layouts.app>