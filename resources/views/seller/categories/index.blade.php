<x-layouts.app :title="'فئات المتجر'">
    <div id="seller-categories-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">فئات المتجر</h1>

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

        @php($isCreateOpen = old('__modal') === 'create-category')
        <details class="mb-4" {{ ($isCreateOpen && $errors->any()) ? 'open' : '' }}>
            <summary class="cursor-pointer px-3 py-2 text-sm font-semibold rounded-lg bg-slate-900 text-white hover:bg-slate-800 [&::-webkit-details-marker]:hidden">+ إضافة فئة</summary>
            <div class="fixed inset-0 z-40">
                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="font-semibold text-gray-800 dark:text-gray-100">إضافة فئة جديدة</div>
                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                        </div>
                        <div class="p-4 max-h-[75vh] overflow-auto">
                            <form method="POST" action="{{ route('seller.categories.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @csrf
                                <input type="hidden" name="__modal" value="create-category" />

                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الاسم (عربي)</label>
                                    <input name="name_ar" value="{{ old('name_ar') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>
                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الاسم (English)</label>
                                    <input name="name_en" value="{{ old('name_en') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Image (اختياري)</label>
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

        <div class="grid gap-4 md:grid-cols-3">
            @forelse(($categories ?? []) as $category)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 flex items-center justify-center flex-shrink-0">
                            @if(!empty($category->image))
                                <img src="{{ asset('storage/' . $category->image) }}" alt="category" class="w-full h-full object-cover" />
                            @else
                                <img src="{{ asset('apple-touch-icon.png') }}" alt="default" class="w-full h-full object-cover" />
                            @endif
                        </div>
                        <div class="flex-1 min-w-0 text-right">
                            <div class="font-semibold text-gray-800 dark:text-gray-100 truncate">{{ $category->name_ar }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $category->name_en }}</div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-end gap-2">
                        @php($isEditCategoryOpen = old('__modal') === ('edit-category-' . $category->id))
                        <details class="relative" {{ ($isEditCategoryOpen && $errors->any()) ? 'open' : '' }}>
                            <summary class="cursor-pointer px-3 py-2 text-xs font-semibold rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 [&::-webkit-details-marker]:hidden">تعديل</summary>
                            <div class="fixed inset-0 z-40">
                                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                                <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                            <div class="font-semibold text-gray-800 dark:text-gray-100">تعديل الفئة</div>
                                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                        </div>

                                        <div class="p-4 max-h-[75vh] overflow-auto">
                                            <form method="POST" action="{{ route('seller.categories.update', $category) }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="__modal" value="edit-category-{{ $category->id }}" />

                                                <div>
                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الاسم (عربي)</label>
                                                    <input name="name_ar" value="{{ old('name_ar', $category->name_ar) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                </div>
                                                <div>
                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الاسم (English)</label>
                                                    <input name="name_en" value="{{ old('name_en', $category->name_en) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Image (اختياري)</label>
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

                        <form method="POST" action="{{ route('seller.categories.destroy', $category) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفئة؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 text-xs font-semibold rounded-lg bg-rose-500 text-white hover:bg-rose-600">حذف</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="md:col-span-3 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 text-center text-gray-600 dark:text-gray-300">
                    لا توجد فئات حالياً
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            @if(method_exists(($categories ?? null), 'links'))
                {{ $categories->links() }}
            @endif
        </div>
    </div>
</x-layouts.app>
