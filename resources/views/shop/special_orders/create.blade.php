@php($lang = session('lang', 'en'))
@php($isAr = $lang === 'ar')

<x-shop-layouts.app :title="($isAr ? 'طلب خاص' : 'Special order')">
    <div class="max-w-xl mx-auto space-y-4">
        <div class="flex items-center gap-2 mb-2">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $isAr ? 'طلب خاص' : 'Special order request' }}
            </h1>
        </div>

        @if (session('status'))
            <div class="p-3 rounded-xl bg-green-50 text-green-700 text-sm dark:bg-green-900/20 dark:text-green-200">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3 rounded-xl bg-red-50 text-red-700 text-sm dark:bg-red-900/20 dark:text-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('shop.special_orders.store') }}" class="space-y-4" enctype="multipart/form-data">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-100 mb-1">
                    {{ $isAr ? 'عنوان الطلب' : 'Request title' }}
                </label>
                <input
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    required
                    class="w-full h-11 rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 px-4 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700"
                />
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-100 mb-1">
                    {{ $isAr ? 'التفاصيل' : 'Details' }}
                </label>
                <textarea
                    name="details"
                    rows="4"
                    class="w-full rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700"
                    placeholder="{{ $isAr ? 'اكتب لنا ما هو المنتج أو الخدمة الخاصة التي تريدها...' : 'Describe the product or service you would like to request...' }}"
                >{{ old('details') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-100 mb-1">
                    {{ $isAr ? 'اسم المنتج (اختياري)' : 'Product name (optional)' }}
                </label>
                <input
                    type="text"
                    name="product_name"
                    value="{{ old('product_name') }}"
                    class="w-full h-11 rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 px-4 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700"
                />
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-100 mb-1">
                        {{ $isAr ? 'الكمية (اختياري)' : 'Quantity (optional)' }}
                    </label>
                    <input
                        type="number"
                        name="quantity"
                        value="{{ old('quantity') }}"
                        min="1"
                        class="w-full h-11 rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 px-4 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700"
                    />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-100 mb-1">
                        {{ $isAr ? 'الخامة (اختياري)' : 'Material (optional)' }}
                    </label>
                    <input
                        type="text"
                        name="material"
                        value="{{ old('material') }}"
                        class="w-full h-11 rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 px-4 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700"
                    />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-100 mb-1">
                        {{ $isAr ? 'اللون (اختياري)' : 'Color (optional)' }}
                    </label>
                    <input
                        type="text"
                        name="color"
                        value="{{ old('color') }}"
                        class="w-full h-11 rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 px-4 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700"
                    />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-100 mb-1">
                        {{ $isAr ? 'المقاس (اختياري)' : 'Size (optional)' }}
                    </label>
                    <input
                        type="text"
                        name="size"
                        value="{{ old('size') }}"
                        class="w-full h-11 rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 px-4 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700"
                    />
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-100 mb-1">
                    {{ $isAr ? 'المواصفات (اختياري)' : 'Specifications (optional)' }}
                </label>
                <textarea
                    name="specs"
                    rows="4"
                    class="w-full rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700"
                >{{ old('specs') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-100 mb-1">
                    {{ $isAr ? 'رابط مرجعي (اختياري)' : 'Reference URL (optional)' }}
                </label>
                <input
                    type="url"
                    name="reference_url"
                    value="{{ old('reference_url') }}"
                    class="w-full h-11 rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 px-4 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700"
                />
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-100 mb-1">
                    {{ $isAr ? 'صور (اختياري)' : 'Images (optional)' }}
                </label>
                <input
                    type="file"
                    name="images[]"
                    multiple
                    accept="image/png,image/jpeg,image/jpg,image/webp"
                    class="w-full rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700"
                />
                <div class="mt-1 text-xs text-gray-500 dark:text-slate-300">{{ $isAr ? 'حد أقصى 5 صور' : 'Up to 5 images' }}</div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-100 mb-1">
                    {{ $isAr ? 'الميزانية المتوقعة (اختياري)' : 'Expected budget (optional)' }}
                </label>
                <input
                    type="number"
                    name="budget"
                    value="{{ old('budget') }}"
                    step="0.01"
                    min="0"
                    class="w-full h-11 rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 px-4 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700"
                />
            </div>

            <button
                type="submit"
                class="w-full h-11 rounded-2xl bg-gradient-to-r from-[#F6416C] to-orange-400 text-white font-semibold hover:opacity-90"
            >
                {{ $isAr ? 'إرسال الطلب' : 'Submit request' }}
            </button>
        </form>
    </div>
</x-shop-layouts.app>
