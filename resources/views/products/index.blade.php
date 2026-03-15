<x-layouts.app :title="'المنتجات'">
    <div id="products-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">المنتجات</h1>

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
                    id="product-search"
                    placeholder="ابحث عن منتج بالاسم أو الكود..."
                    class="w-full p-3 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400" />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                </div>
            </div>

            @php($isCreateCategoryOpen = old('__modal') === 'create-category')
            <details class="w-full md:w-auto" {{ ($isCreateCategoryOpen && $errors->any()) ? 'open' : '' }}>
                <summary class="cursor-pointer w-full md:w-auto px-3 py-3 md:py-2 text-sm font-semibold rounded-lg bg-slate-900 text-white hover:bg-slate-800 [&::-webkit-details-marker]:hidden">إضافة فئة</summary>
                <div class="fixed inset-0 z-40">
                    <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                    <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="font-semibold text-gray-800 dark:text-gray-100">إضافة فئة جديدة</div>
                                <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                            </div>

                            <div class="p-4 max-h-[75vh] overflow-auto">
                                <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-3">
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
        </div>

        <div class="space-y-4">
            @forelse(($products ?? []) as $product)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden" data-product-card data-search="{{ mb_strtolower(($product->name_ar ?? '') . ' ' . ($product->name_en ?? '') . ' ' . $product->name . ' ' . $product->sku . ' ' . ($product->category ?? '') . ' ' . ($product->description ?? '') . ' ' . $product->price . ' ' . $product->quantity) }}">
                <div class="p-4 sm:p-5 flex items-center justify-between gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center border border-gray-100 dark:border-gray-600 overflow-hidden">
                        @if(!empty($product->image))
                        <img src="{{ asset('storage/' . $product->image) }}" alt="product" class="w-full h-full object-cover" />
                        @else
                        <img src="{{ asset('apple-touch-icon.png') }}" alt="default" class="w-full h-full object-cover" />
                        @endif
                    </div>

                    <div class="flex-1 text-right">
                        <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $product->name_ar ?? $product->name }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-300 mt-1 line-clamp-2">
                            {{ $product->description ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="px-4 pb-4 sm:px-5 sm:pb-5">
                    <div class="flex items-center justify-between gap-2">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $product->category ?? '-' }}
                        </div>

                        <div class="flex items-center justify-end gap-2">
                            <details>
                                <summary class="cursor-pointer px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/40 [&::-webkit-details-marker]:hidden">عرض</summary>
                                <div class="fixed inset-0 z-40">
                                    <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>

                                    <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                                <div class="font-semibold text-gray-800 dark:text-gray-100">تفاصيل المنتج</div>
                                                <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                            </div>

                                            <div class="p-4 max-h-[75vh] overflow-auto">
                                                <div class="flex items-start gap-3">
                                                    <div class="w-16 h-16 rounded-2xl bg-gray-50 dark:bg-gray-700 border border-gray-100 dark:border-gray-600 overflow-hidden">
                                                        @if(!empty($product->image))
                                                        <img src="{{ asset('storage/' . $product->image) }}" alt="product" class="w-full h-full object-cover" />
                                                        @else
                                                        <img src="{{ asset('apple-touch-icon.png') }}" alt="default" class="w-full h-full object-cover" />
                                                        @endif
                                                    </div>

                                                    <div class="flex-1 text-right">
                                                        <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $product->name }}</div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">الكود :{{ $product->sku }}</div>
                                                        <div class="text-sm text-gray-600 dark:text-gray-300 mt-2">التصنيف: {{ $product->category ?? '-' }}</div>
                                                        @if(!empty($product->color))
                                                            <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">اللون: {{ $product->color }}</div>
                                                        @endif
                                                        @if(!empty($product->size))
                                                            <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">المقاس: {{ $product->size }}</div>
                                                        @endif

                                                        <div class="mt-2">
                                                            @if(((int) ($product->featured ?? 0)) === 1)
                                                            <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-semibold bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300">Featured</span>
                                                            @else
                                                            <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">Normal</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">الكمية: {{ $product->quantity }}</div>
                                                    </div>

                                                    <div class="text-rose-600 font-bold text-lg whitespace-nowrap">
                                                        {{ number_format((float) $product->price, 0, '.', ',') }} ج.م
                                                    </div>
                                                </div>

                                                <div class="mt-4 text-right text-gray-700 dark:text-gray-200">
                                                    {{ $product->description ?? '-' }}
                                                </div>

                                                <div class="mt-4 text-right">
                                                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-2">الموردين</div>
                                                    <div class="space-y-3">
                                                        @forelse(($product->suppliers ?? []) as $s)
                                                        @php($stiers = $product->pricingTiers->where('supplier_id', $s->id))
                                                        <div class="rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 p-3">
                                                            <div class="flex items-center justify-between mb-2">
                                                                <div class="text-gray-700 dark:text-gray-200">
                                                                    <div class="font-semibold">{{ $s->name }}</div>
                                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $s->type }}</div>
                                                                </div>
                                                                <div class="text-right">
                                                                    <div class="font-bold text-rose-600">{{ number_format((float) ($s->pivot->price ?? 0), 0, '.', ',') }} ج.م</div>
                                                                    <div class="text-[10px] text-gray-500 dark:text-gray-400">الكمية: {{ $s->pivot->quantity ?? 0 }}</div>
                                                                </div>
                                                            </div>
                                                            
                                                            @if($stiers->count() > 0)
                                                            <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                                                <div class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 mb-1">مستويات أسعار الجملة:</div>
                                                                <div class="grid grid-cols-2 gap-2">
                                                                    @foreach($stiers as $tier)
                                                                    <div class="text-[10px] p-1.5 rounded bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700">
                                                                        <span class="text-gray-600 dark:text-gray-400">{{ $tier->min_quantity }}{{ $tier->max_quantity ? '-' . $tier->max_quantity : '+' }} قطعة:</span>
                                                                        <span class="font-bold text-rose-500">{{ number_format((float) $tier->price_per_unit, 0, '.', ',') }} ج.م</span>
                                                                    </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        @empty
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">لا يوجد موردين</div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </details>

                            @php($isEditOpen = old('__modal') === ('edit-' . $product->id))
                            <details {{ ($isEditOpen && $errors->any()) ? 'open' : '' }}>
                                <summary class="cursor-pointer px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/40 [&::-webkit-details-marker]:hidden">تعديل</summary>
                                <div class="fixed inset-0 z-40">
                                    <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>

                                    <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                                <div class="font-semibold text-gray-800 dark:text-gray-100">تعديل منتج</div>
                                                <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                            </div>

                                            <div class="p-4 max-h-[75vh] overflow-auto">
                                                <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="__modal" value="edit-{{ $product->id }}" />

                                                    <div class="md:col-span-2">
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الاسم (عربي)</label>
                                                        <input name="name_ar" value="{{ $isEditOpen ? old('name_ar', ($product->name_ar ?? '')) : ($product->name_ar ?? '') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                    </div>

                                                    <div class="md:col-span-2">
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الاسم (English)</label>
                                                        <input name="name_en" value="{{ $isEditOpen ? old('name_en', ($product->name_en ?? ($product->name ?? ''))) : ($product->name_en ?? ($product->name ?? '')) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الكود</label>
                                                        <input name="sku" value="{{ $isEditOpen ? old('sku', $product->sku) : $product->sku }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                    </div>

                                                    <div class="md:col-span-2">
                                                        <label class="block text-sm mb-2 text-gray-600 dark:text-gray-300">أسعار الموردين</label>
                                                        <div id="edit-suppliers-wrapper-{{ $product->id }}" class="space-y-2" data-suppliers-wrapper data-initial="@json(($product->suppliers ?? collect())->map(fn($s) => ['id' => (int) $s->id, 'price' => (float) ($s->pivot->price ?? 0)])->values())">
                                                            <!-- rows will be injected via JS -->
                                                        </div>
                                                        <button type="button" class="mt-2 inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-rose-400 to-orange-400 text-white text-xs font-semibold" data-add-supplier-row data-target="edit-suppliers-wrapper-{{ $product->id }}">
                                                            + إضافة مورد
                                                        </button>
                                                    </div>



                                                    <div class="md:col-span-2">
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الألوان (اختياري)</label>
                                                        <div id="admin-edit-colors-wrapper-{{ $product->id }}" class="space-y-2">
                                                            @php($oldColors = collect((array) old('colors', []))->filter(fn($v) => trim((string) $v) !== '')->values())
                                                            @php($productColors = collect((array) ($product->colors ?? []))->filter(fn($v) => trim((string) $v) !== '')->values())
                                                            @php($colorsToRender = ($isEditOpen && $oldColors->count() > 0) ? $oldColors : ($productColors->count() > 0 ? $productColors : collect([(string) ($product->color ?? '')])->filter(fn($v) => trim((string) $v) !== '')))

                                                            @if($colorsToRender->count() > 0)
                                                                @foreach($colorsToRender as $c)
                                                                    <div class="flex items-center gap-2" data-repeat-row>
                                                                        <input name="colors[]" value="{{ (string) $c }}" class="flex-1 w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                                        <button type="button" class="w-10 h-10 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200" data-remove-row>×</button>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="flex items-center gap-2" data-repeat-row>
                                                                    <input name="colors[]" value="" class="flex-1 w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                                    <button type="button" class="w-10 h-10 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200" data-remove-row>×</button>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <button type="button" class="mt-2 inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-rose-400 to-orange-400 text-white text-xs font-semibold" data-add-text-row data-target="admin-edit-colors-wrapper-{{ $product->id }}" data-name="colors[]">
                                                            + إضافة لون
                                                        </button>
                                                    </div>

                                                    <div class="md:col-span-2">
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المقاسات / Sizes (اختياري)</label>
                                                        <div id="admin-edit-sizes-wrapper-{{ $product->id }}" class="space-y-2">
                                                            @php($oldSizes = collect((array) old('sizes', []))->filter(fn($v) => trim((string) $v) !== '')->values())
                                                            @php($productSizes = collect((array) ($product->sizes ?? []))->filter(fn($v) => trim((string) $v) !== '')->values())
                                                            @php($sizesToRender = ($isEditOpen && $oldSizes->count() > 0) ? $oldSizes : ($productSizes->count() > 0 ? $productSizes : collect([(string) ($product->size ?? '')])->filter(fn($v) => trim((string) $v) !== '')))

                                                            @if($sizesToRender->count() > 0)
                                                                @foreach($sizesToRender as $sz)
                                                                    <div class="flex items-center gap-2" data-repeat-row>
                                                                        <input name="sizes[]" value="{{ (string) $sz }}" class="flex-1 w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                                        <button type="button" class="w-10 h-10 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200" data-remove-row>×</button>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="flex items-center gap-2" data-repeat-row>
                                                                    <input name="sizes[]" value="" class="flex-1 w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                                    <button type="button" class="w-10 h-10 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200" data-remove-row>×</button>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <button type="button" class="mt-2 inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-rose-400 to-orange-400 text-white text-xs font-semibold" data-add-text-row data-target="admin-edit-sizes-wrapper-{{ $product->id }}" data-name="sizes[]">
                                                            + إضافة مقاس
                                                        </button>
                                                    </div>

                                                    <div class="md:col-span-2">
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الوصف (عربي)</label>
                                                        <textarea name="description_ar" rows="3" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">{{ $isEditOpen ? old('description_ar', ($product->description_ar ?? '')) : ($product->description_ar ?? '') }}</textarea>
                                                    </div>

                                                    <div class="md:col-span-2">
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الوصف (English)</label>
                                                        <textarea name="description_en" rows="3" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">{{ $isEditOpen ? old('description_en', ($product->description_en ?? '')) : ($product->description_en ?? '') }}</textarea>
                                                    </div>

                                                    <div class="md:col-span-2">
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الصور الإضافية للمنتج (يمكن إضافة أكثر من صورة)</label>
                                                        <div id="admin-edit-images-wrapper-{{ $product->id }}" class="space-y-2" data-images-wrapper data-prefix="admin-edit-images-{{ $product->id }}">
                                                            <div class="flex items-center gap-2" data-image-row>
                                                                <input id="admin-edit-images-{{ $product->id }}-0" type="file" name="images[]" accept="image/*" class="hidden" data-file-label-target="admin-edit-images-label-{{ $product->id }}-0" />
                                                                <label for="admin-edit-images-{{ $product->id }}-0" class="inline-flex items-center justify-center w-11 h-11 rounded-2xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 cursor-pointer select-none">+</label>
                                                                <div id="admin-edit-images-label-{{ $product->id }}-0" class="text-xs text-gray-500 dark:text-gray-400">اضغطي + لاختيار صورة</div>
                                                                <button type="button" class="ms-auto w-10 h-10 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200" data-remove-image-row>×</button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="mt-2 inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-rose-400 to-orange-400 text-white text-xs font-semibold" data-add-image-row data-target="admin-edit-images-wrapper-{{ $product->id }}" data-prefix="admin-edit-images-{{ $product->id }}">
                                                            + إضافة صورة
                                                        </button>
                                                    </div>

                                                    <div class="md:col-span-2">
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">صورة (اختياري)</label>
                                                        <div class="flex items-center gap-2">
                                                            <input id="admin-edit-image-{{ $product->id }}" type="file" name="image" accept="image/*" class="hidden" data-file-label-target="admin-edit-image-label-{{ $product->id }}" />
                                                            <label for="admin-edit-image-{{ $product->id }}" class="inline-flex items-center justify-center w-11 h-11 rounded-2xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 cursor-pointer select-none">+</label>
                                                            <div id="admin-edit-image-label-{{ $product->id }}" class="text-xs text-gray-500 dark:text-gray-400">صورة أساسية (اختياري)</div>
                                                        </div>
                                                    </div>

                                                    <div class="md:col-span-2">
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الفئة</label>
                                                        <select name="category_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                                            @php($selectedCategoryId = (int) ($isEditOpen ? old('category_id', $product->category_id) : ($product->category_id ?? 0)))
                                                            <option value="">بدون فئة</option>
                                                            @foreach(($categories ?? []) as $cat)
                                                            <option value="{{ $cat->id }}" {{ ((int) $cat->id === $selectedCategoryId) ? 'selected' : '' }}>
                                                                {{ $cat->name_ar }} / {{ $cat->name_en }}
                                                            </option>
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

                            <form method="POST" action="{{ route('products.destroy', $product->id) }}" onsubmit="return confirm('حذف المنتج؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-2 text-sm font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700">حذف</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 text-center text-gray-600 dark:text-gray-300">
                لا توجد منتجات حالياً
            </div>
            @endforelse
        </div>

        <div class="mt-4">
            @if(method_exists(($products ?? null), 'links'))
            {{ $products->links() }}
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
                            <div class="font-semibold text-gray-800 dark:text-gray-100">إضافة منتج</div>
                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                        </div>

                        <div class="p-4 max-h-[75vh] overflow-auto">
                            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @csrf
                                <input type="hidden" name="__modal" value="create" />

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الاسم (عربي)</label>
                                    <input name="name_ar" value="{{ old('name_ar') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الاسم (English)</label>
                                    <input name="name_en" value="{{ old('name_en') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الكود</label>
                                    <input name="sku" value="{{ old('sku') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-2 text-gray-600 dark:text-gray-300">أسعار الموردين</label>
                                    <div id="admin-create-suppliers-wrapper" class="space-y-2" data-suppliers-wrapper data-initial='[]'>
                                        <!-- rows will be injected via JS -->
                                    </div>
                                    <button type="button" class="mt-2 inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-rose-400 to-orange-400 text-white text-xs font-semibold" data-add-supplier-row data-target="admin-create-suppliers-wrapper">
                                        + إضافة مورد
                                    </button>
                                </div>



                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الألوان (اختياري)</label>
                                    <div id="admin-create-colors-wrapper" class="space-y-2">
                                        @php($oldColors = collect((array) old('colors', []))->filter(fn($v) => trim((string) $v) !== '')->values())
                                        @if($oldColors->count() > 0)
                                            @foreach($oldColors as $c)
                                                <div class="flex items-center gap-2" data-repeat-row>
                                                    <input name="colors[]" value="{{ (string) $c }}" class="flex-1 w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                    <button type="button" class="w-10 h-10 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200" data-remove-row>×</button>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="flex items-center gap-2" data-repeat-row>
                                                <input name="colors[]" value="{{ old('color') }}" class="flex-1 w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                <button type="button" class="w-10 h-10 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200" data-remove-row>×</button>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="mt-2 inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-rose-400 to-orange-400 text-white text-xs font-semibold" data-add-text-row data-target="admin-create-colors-wrapper" data-name="colors[]">
                                        + إضافة لون
                                    </button>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المقاسات / Sizes (اختياري)</label>
                                    <div id="admin-create-sizes-wrapper" class="space-y-2">
                                        @php($oldSizes = collect((array) old('sizes', []))->filter(fn($v) => trim((string) $v) !== '')->values())
                                        @if($oldSizes->count() > 0)
                                            @foreach($oldSizes as $sz)
                                                <div class="flex items-center gap-2" data-repeat-row>
                                                    <input name="sizes[]" value="{{ (string) $sz }}" class="flex-1 w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                    <button type="button" class="w-10 h-10 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200" data-remove-row>×</button>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="flex items-center gap-2" data-repeat-row>
                                                <input name="sizes[]" value="{{ old('size') }}" class="flex-1 w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                <button type="button" class="w-10 h-10 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200" data-remove-row>×</button>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="mt-2 inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-rose-400 to-orange-400 text-white text-xs font-semibold" data-add-text-row data-target="admin-create-sizes-wrapper" data-name="sizes[]">
                                        + إضافة مقاس
                                    </button>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الوصف (عربي)</label>
                                    <textarea name="description_ar" rows="3" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">{{ old('description_ar') }}</textarea>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الوصف (English)</label>
                                    <textarea name="description_en" rows="3" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">{{ old('description_en') }}</textarea>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الصور الإضافية للمنتج (يمكن إضافة أكثر من صورة)</label>
                                    <div id="admin-create-images-wrapper" class="space-y-2" data-images-wrapper data-prefix="admin-create-images">
                                        <div class="flex items-center gap-2" data-image-row>
                                            <input id="admin-create-images-0" type="file" name="images[]" accept="image/*" class="hidden" data-file-label-target="admin-create-images-label-0" />
                                            <label for="admin-create-images-0" class="inline-flex items-center justify-center w-11 h-11 rounded-2xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 cursor-pointer select-none">+</label>
                                            <div id="admin-create-images-label-0" class="text-xs text-gray-500 dark:text-gray-400">اضغطي + لاختيار صورة</div>
                                            <button type="button" class="ms-auto w-10 h-10 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200" data-remove-image-row>×</button>
                                        </div>
                                    </div>
                                    <button type="button" class="mt-2 inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-rose-400 to-orange-400 text-white text-xs font-semibold" data-add-image-row data-target="admin-create-images-wrapper" data-prefix="admin-create-images">
                                        + إضافة صورة
                                    </button>
                                </div>

                                <div>
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">صورة (اختياري)</label>
                                    <div class="flex items-center gap-2">
                                        <input id="admin-create-image" type="file" name="image" accept="image/*" class="hidden" data-file-label-target="admin-create-image-label" />
                                        <label for="admin-create-image" class="inline-flex items-center justify-center w-11 h-11 rounded-2xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 cursor-pointer select-none">+</label>
                                        <div id="admin-create-image-label" class="text-xs text-gray-500 dark:text-gray-400">صورة أساسية (اختياري)</div>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الفئة</label>
                                    <select name="category_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                        @php($selectedCategoryId = (int) old('category_id', 0))
                                        <option value="">بدون فئة</option>
                                        @foreach(($categories ?? []) as $cat)
                                        <option value="{{ $cat->id }}" {{ ((int) $cat->id === $selectedCategoryId) ? 'selected' : '' }}>
                                            {{ $cat->name_ar }} / {{ $cat->name_en }}
                                        </option>
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
            (function() {
                var input = document.getElementById('product-search');
                if (input) {
                    var cards = Array.prototype.slice.call(document.querySelectorAll('[data-product-card]'));
                    var handler = function() {
                        var q = (input.value || '').trim().toLowerCase();
                        cards.forEach(function(card) {
                            var hay = (card.getAttribute('data-search') || '').toLowerCase();
                            card.style.display = q === '' || hay.indexOf(q) !== -1 ? '' : 'none';
                        });
                    };
                    input.addEventListener('input', handler);
                }

                var allSuppliers = @json(($suppliers ?? collect())->map(fn($s) => ['id' => (int) $s->id, 'name' => (string) ($s->name ?? ''), 'type' => (string) ($s->type ?? '')])->values());

                function setFileLabel(inputEl) {
                    if (!inputEl) return;
                    var targetId = inputEl.getAttribute('data-file-label-target');
                    if (!targetId) return;
                    var targetEl = document.getElementById(targetId);
                    if (!targetEl) return;

                    var files = inputEl.files ? Array.prototype.slice.call(inputEl.files) : [];
                    if (!files.length) {
                        return;
                    }
                    if (files.length === 1) {
                        targetEl.textContent = files[0].name;
                        return;
                    }
                    targetEl.textContent = 'تم اختيار ' + files.length + ' صور';
                }

                Array.prototype.slice.call(document.querySelectorAll('input[type="file"][data-file-label-target]')).forEach(function(el) {
                    el.addEventListener('change', function() {
                        setFileLabel(el);
                    });
                });

                function wireFileInput(el) {
                    if (!el) return;
                    el.addEventListener('change', function() {
                        setFileLabel(el);
                    });
                }

                function removeRepeatRow(btn) {
                    if (!btn) return;
                    var row = btn.closest('[data-repeat-row]');
                    if (!row) return;
                    var wrapper = row.parentElement;
                    var rowInput = row.querySelector('input');
                    var name = rowInput ? rowInput.name : 'values[]';
                    row.remove();

                    if (wrapper && !wrapper.querySelector('[data-repeat-row]')) {
                        var newRow = document.createElement('div');
                        newRow.className = 'flex items-center gap-2';
                        newRow.setAttribute('data-repeat-row', '');

                        var input = document.createElement('input');
                        input.name = name;
                        input.className = 'flex-1 w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200';

                        var removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'w-10 h-10 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200';
                        removeBtn.textContent = '×';
                        removeBtn.setAttribute('data-remove-row', '');

                        newRow.appendChild(input);
                        newRow.appendChild(removeBtn);
                        wrapper.appendChild(newRow);
                    }
                }

                document.addEventListener('click', function(e) {
                    var addTextBtn = e.target.closest('[data-add-text-row]');
                    if (addTextBtn) {
                        e.preventDefault();
                        var targetId = addTextBtn.getAttribute('data-target');
                        var name = addTextBtn.getAttribute('data-name') || 'values[]';
                        var wrapper = targetId ? document.getElementById(targetId) : null;
                        if (!wrapper) return;

                        var row = document.createElement('div');
                        row.className = 'flex items-center gap-2';
                        row.setAttribute('data-repeat-row', '');

                        var input = document.createElement('input');
                        input.name = name;
                        input.className = 'flex-1 w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200';

                        var removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'w-10 h-10 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200';
                        removeBtn.textContent = '×';
                        removeBtn.setAttribute('data-remove-row', '');

                        row.appendChild(input);
                        row.appendChild(removeBtn);
                        wrapper.appendChild(row);
                        input.focus();
                        return;
                    }

                    var removeTextBtn = e.target.closest('[data-remove-row]');
                    if (removeTextBtn) {
                        e.preventDefault();
                        removeRepeatRow(removeTextBtn);
                        return;
                    }

                    var addImageBtn = e.target.closest('[data-add-image-row]');
                    if (addImageBtn) {
                        e.preventDefault();
                        var targetId = addImageBtn.getAttribute('data-target');
                        var prefix = addImageBtn.getAttribute('data-prefix') || 'images';
                        var wrapper = targetId ? document.getElementById(targetId) : null;
                        if (!wrapper) return;

                        var index = wrapper.querySelectorAll('[data-image-row]').length;
                        var inputId = prefix + '-' + String(index);
                        var labelId = prefix + '-label-' + String(index);

                        var row = document.createElement('div');
                        row.className = 'flex items-center gap-2';
                        row.setAttribute('data-image-row', '');

                        var input = document.createElement('input');
                        input.type = 'file';
                        input.accept = 'image/*';
                        input.name = 'images[]';
                        input.id = inputId;
                        input.className = 'hidden';
                        input.setAttribute('data-file-label-target', labelId);

                        var label = document.createElement('label');
                        label.setAttribute('for', inputId);
                        label.className = 'inline-flex items-center justify-center w-11 h-11 rounded-2xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 cursor-pointer select-none';
                        label.textContent = '+';

                        var text = document.createElement('div');
                        text.id = labelId;
                        text.className = 'text-xs text-gray-500 dark:text-gray-400';
                        text.textContent = 'اضغطي + لاختيار صورة';

                        var removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'ms-auto w-10 h-10 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200';
                        removeBtn.textContent = '×';
                        removeBtn.setAttribute('data-remove-image-row', '');

                        row.appendChild(input);
                        row.appendChild(label);
                        row.appendChild(text);
                        row.appendChild(removeBtn);
                        wrapper.appendChild(row);
                        wireFileInput(input);
                        return;
                    }

                    var removeImageBtn = e.target.closest('[data-remove-image-row]');
                    if (removeImageBtn) {
                        e.preventDefault();
                        var row = removeImageBtn.closest('[data-image-row]');
                        if (!row) return;
                        var wrapper = row.parentElement;
                        row.remove();

                        if (wrapper && !wrapper.querySelector('[data-image-row]')) {
                            var prefix = wrapper.getAttribute('data-prefix') || 'images';

                            var fallback = document.createElement('div');
                            fallback.className = 'flex items-center gap-2';
                            fallback.setAttribute('data-image-row', '');

                            var inputId = prefix + '-0';
                            var labelId = prefix + '-label-0';

                            var input = document.createElement('input');
                            input.type = 'file';
                            input.accept = 'image/*';
                            input.name = 'images[]';
                            input.id = inputId;
                            input.className = 'hidden';
                            input.setAttribute('data-file-label-target', labelId);

                            var label = document.createElement('label');
                            label.setAttribute('for', inputId);
                            label.className = 'inline-flex items-center justify-center w-11 h-11 rounded-2xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 cursor-pointer select-none';
                            label.textContent = '+';

                            var text = document.createElement('div');
                            text.id = labelId;
                            text.className = 'text-xs text-gray-500 dark:text-gray-400';
                            text.textContent = 'اضغطي + لاختيار صورة';

                            var removeBtn = document.createElement('button');
                            removeBtn.type = 'button';
                            removeBtn.className = 'ms-auto w-10 h-10 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200';
                            removeBtn.textContent = '×';
                            removeBtn.setAttribute('data-remove-image-row', '');

                            fallback.appendChild(input);
                            fallback.appendChild(label);
                            fallback.appendChild(text);
                            fallback.appendChild(removeBtn);
                            wrapper.appendChild(fallback);
                            wireFileInput(input);
                        }
                        return;
                    }
                });

                function buildRow(wrapper, initial) {
                    var row = document.createElement('div');
                    row.className = 'flex flex-col gap-2 rounded-xl border border-gray-200 dark:border-gray-700 p-3';

                    var dropdownContainer = document.createElement('div');
                    dropdownContainer.className = 'relative flex-1';

                    var input = document.createElement('input');
                    input.type = 'text';
                    input.placeholder = 'اختر مورداً أو ابحث بالاسم...';
                    input.className = 'w-full p-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-200';
                    input.autocomplete = 'off';

                    var list = document.createElement('div');
                    list.className = 'absolute z-30 mt-1 w-full max-h-56 overflow-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-lg hidden';

                    var items = [];
                    allSuppliers.forEach(function(s) {
                        var btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'w-full text-right px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800';
                        btn.textContent = s.name + (s.type ? ' (' + s.type + ')' : '');
                        btn.dataset.id = String(s.id);
                        btn.dataset.search = (s.name + ' ' + (s.type || '')).toLowerCase();
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            selectedId = String(s.id);
                            input.value = s.name + (s.type ? ' (' + s.type + ')' : '');
                            updateName();
                            closeList();
                        });
                        list.appendChild(btn);
                        items.push(btn);
                    });

                    var unitPriceInput = document.createElement('input');
                    unitPriceInput.type = 'number';
                    unitPriceInput.step = '0.01';
                    unitPriceInput.min = '0';
                    unitPriceInput.placeholder = 'سعر القطعة';
                    unitPriceInput.className = 'w-full p-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm';

                    var qtyInput = document.createElement('input');
                    qtyInput.type = 'number';
                    qtyInput.min = '0';
                    qtyInput.placeholder = 'الكمية';
                    qtyInput.className = 'w-full p-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm';

                    var tiersContainer = document.createElement('div');
                    tiersContainer.className = 'space-y-2';
                    tiersContainer.setAttribute('data-supplier-tiers-wrapper', '');

                    var addTierBtn = document.createElement('button');
                    addTierBtn.type = 'button';
                    addTierBtn.className = 'inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-rose-400 to-orange-400 text-white text-xs font-semibold';
                    addTierBtn.textContent = '+ إضافة مستوى سعر';

                    var selectedId = '';
                    if (initial && initial.id) {
                        selectedId = String(initial.id);

                        var found = allSuppliers.find(function(s) {
                            return String(s.id) === selectedId;
                        });
                        if (found) {
                            input.value = found.name + (found.type ? ' (' + found.type + ')' : '');
                        }
                    }

                    if (initial && typeof initial.unit_price !== 'undefined') {
                        unitPriceInput.value = String(initial.unit_price);
                    }
                    if (initial && typeof initial.quantity !== 'undefined') {
                        qtyInput.value = String(initial.quantity);
                    }

                    var tiersInitialMap = {};
                    try {
                        var tiersRaw = wrapper.getAttribute('data-tiers-initial');
                        if (tiersRaw) tiersInitialMap = JSON.parse(tiersRaw) || {};
                    } catch (e) {
                        tiersInitialMap = {};
                    }

                    function normalizeSupplierTierInitial(item) {
                        if (!item) return null;
                        return {
                            min_quantity: item.min_quantity,
                            max_quantity: item.max_quantity,
                            price_per_unit: item.price_per_unit,
                        };
                    }

                    function updateSupplierTierNames() {
                        if (!selectedId) {
                            return;
                        }
                        var rows = Array.prototype.slice.call(tiersContainer.querySelectorAll('[data-pricing-tier-row]'));
                        rows.forEach(function(tierRow, idx) {
                            var minInput = tierRow.querySelector('[data-tier-min]');
                            var maxInput = tierRow.querySelector('[data-tier-max]');
                            var priceTierInput = tierRow.querySelector('[data-tier-price]');
                            if (minInput) minInput.name = 'supplier_pricing_tiers[' + selectedId + '][' + String(idx) + '][min_quantity]';
                            if (maxInput) maxInput.name = 'supplier_pricing_tiers[' + selectedId + '][' + String(idx) + '][max_quantity]';
                            if (priceTierInput) priceTierInput.name = 'supplier_pricing_tiers[' + selectedId + '][' + String(idx) + '][price_per_unit]';
                        });
                    }

                    function buildSupplierPricingTierRow(initialTier) {
                        var init = normalizeSupplierTierInitial(initialTier);

                        var tierRow = document.createElement('div');
                        tierRow.className = 'flex flex-wrap md:flex-nowrap items-center gap-2';
                        tierRow.setAttribute('data-pricing-tier-row', '');

                        var minInput = document.createElement('input');
                        minInput.type = 'number';
                        minInput.min = '1';
                        minInput.placeholder = 'من';
                        minInput.className = 'w-24 p-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm';
                        minInput.setAttribute('data-tier-min', '');

                        var maxInput = document.createElement('input');
                        maxInput.type = 'number';
                        maxInput.min = '1';
                        maxInput.placeholder = 'إلى';
                        maxInput.className = 'w-24 p-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm';
                        maxInput.setAttribute('data-tier-max', '');

                        var priceTierInput = document.createElement('input');
                        priceTierInput.type = 'number';
                        priceTierInput.step = '0.01';
                        priceTierInput.min = '0';
                        priceTierInput.placeholder = 'سعر/قطعة';
                        priceTierInput.className = 'w-32 p-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm';
                        priceTierInput.setAttribute('data-tier-price', '');

                        if (init && typeof init.min_quantity !== 'undefined' && init.min_quantity !== null) {
                            minInput.value = String(init.min_quantity);
                        }
                        if (init && typeof init.max_quantity !== 'undefined' && init.max_quantity !== null) {
                            maxInput.value = String(init.max_quantity);
                        }
                        if (init && typeof init.price_per_unit !== 'undefined' && init.price_per_unit !== null) {
                            priceTierInput.value = String(init.price_per_unit);
                        }

                        var removeTierBtn = document.createElement('button');
                        removeTierBtn.type = 'button';
                        removeTierBtn.className = 'px-2 py-1 rounded-lg bg-red-500 text-white text-xs';
                        removeTierBtn.textContent = '×';
                        removeTierBtn.addEventListener('click', function() {
                            tierRow.remove();
                            updateSupplierTierNames();
                        });

                        tierRow.appendChild(minInput);
                        tierRow.appendChild(maxInput);
                        tierRow.appendChild(priceTierInput);
                        tierRow.appendChild(removeTierBtn);
                        tiersContainer.appendChild(tierRow);
                        updateSupplierTierNames();
                    }

                    function loadInitialTiersForSelected() {
                        tiersContainer.innerHTML = '';
                        if (!selectedId) {
                            return;
                        }
                        var list = tiersInitialMap && tiersInitialMap[selectedId] ? tiersInitialMap[selectedId] : [];
                        if (Array.isArray(list) && list.length) {
                            list.forEach(function(t) { buildSupplierPricingTierRow(t); });
                        } else {
                            buildSupplierPricingTierRow(null);
                        }
                    }

                    addTierBtn.addEventListener('click', function() {
                        if (!selectedId) {
                            return;
                        }
                        buildSupplierPricingTierRow(null);
                    });

                    function updateName() {
                        if (!selectedId) {
                            unitPriceInput.name = '';
                            unitPriceInput.disabled = true;

                            qtyInput.name = '';
                            qtyInput.disabled = true;

                            tiersContainer.innerHTML = '';
                            addTierBtn.disabled = true;
                        } else {
                            unitPriceInput.name = 'supplier_unit_prices[' + selectedId + ']';
                            unitPriceInput.disabled = false;

                            qtyInput.name = 'supplier_quantities[' + selectedId + ']';
                            qtyInput.disabled = false;

                            addTierBtn.disabled = false;
                            loadInitialTiersForSelected();
                        }
                    }

                    function openList() {
                        if (list.classList.contains('hidden')) {
                            list.classList.remove('hidden');
                        }
                    }

                    function closeList() {
                        if (!list.classList.contains('hidden')) {
                            list.classList.add('hidden');
                        }
                    }

                    input.addEventListener('focus', function() {
                        openList();
                    });
                    input.addEventListener('click', function() {
                        openList();
                    });
                    input.addEventListener('input', function() {
                        var q = (input.value || '').trim().toLowerCase();
                        items.forEach(function(btn) {
                            var hay = btn.dataset.search || '';
                            btn.style.display = !q || hay.indexOf(q) !== -1 ? '' : 'none';
                        });
                        openList();
                    });

                    document.addEventListener('click', function(e) {
                        if (!dropdownContainer.contains(e.target)) {
                            closeList();
                        }
                    });

                    dropdownContainer.appendChild(input);
                    dropdownContainer.appendChild(list);

                    var removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'px-2 py-1 rounded-lg bg-red-500 text-white text-xs';
                    removeBtn.textContent = '×';
                    removeBtn.addEventListener('click', function() {
                        row.remove();
                    });

                    row.appendChild(dropdownContainer);
                    var grid = document.createElement('div');
                    grid.className = 'grid grid-cols-1 md:grid-cols-2 gap-2';
                    grid.appendChild(unitPriceInput);
                    grid.appendChild(qtyInput);
                    row.appendChild(grid);
                    row.appendChild(tiersContainer);
                    row.appendChild(addTierBtn);
                    row.appendChild(removeBtn);
                    wrapper.appendChild(row);

                    updateName();
                }

                document.querySelectorAll('[data-suppliers-wrapper]').forEach(function(wrapper) {
                    var initialRaw = wrapper.getAttribute('data-initial');
                    var initialList = [];
                    try {
                        if (initialRaw) initialList = JSON.parse(initialRaw) || [];
                    } catch (e) {
                        initialList = [];
                    }

                    if (Array.isArray(initialList) && initialList.length) {
                        initialList.forEach(function(item) { buildRow(wrapper, item); });
                    } else {
                        buildRow(wrapper, null);
                    }
                });

                document.querySelectorAll('[data-add-supplier-row]').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var target = btn.getAttribute('data-target');
                        var wrapper = document.getElementById(target);
                        if (!wrapper) return;
                        buildRow(wrapper, null);
                    });
                });

                // per-row supplier search is handled inside buildRow()
                // Pricing Tiers Logic

                function normalizeTierInitial(item) {
                    if (!item) return null;
                    var min = item.min_quantity;
                    var max = item.max_quantity;
                    var ppu = item.price_per_unit;
                    if (typeof item.min !== 'undefined') min = item.min;
                    if (typeof item.max !== 'undefined') max = item.max;
                    if (typeof item.price !== 'undefined') ppu = item.price;
                    return {
                        min_quantity: min,
                        max_quantity: max,
                        price_per_unit: ppu,
                    };
                }

                function updatePricingTierNames(wrapper) {
                    if (!wrapper) return;
                    var rows = Array.prototype.slice.call(wrapper.querySelectorAll('[data-pricing-tier-row]'));
                    rows.forEach(function(row, idx) {
                        var minInput = row.querySelector('[data-tier-min]');
                        var maxInput = row.querySelector('[data-tier-max]');
                        var priceInput = row.querySelector('[data-tier-price]');
                        if (minInput) minInput.name = 'pricing_tiers[' + String(idx) + '][min_quantity]';
                        if (maxInput) maxInput.name = 'pricing_tiers[' + String(idx) + '][max_quantity]';
                        if (priceInput) priceInput.name = 'pricing_tiers[' + String(idx) + '][price_per_unit]';
                    });
                }

                function buildPricingTierRow(wrapper, initial) {
                    if (!wrapper) return;
                    var init = normalizeTierInitial(initial);

                    var row = document.createElement('div');
                    row.className = 'flex flex-wrap md:flex-nowrap items-center gap-2';
                    row.setAttribute('data-pricing-tier-row', '');

                    var minInput = document.createElement('input');
                    minInput.type = 'number';
                    minInput.min = '1';
                    minInput.placeholder = 'من';
                    minInput.className = 'w-24 p-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm';
                    minInput.setAttribute('data-tier-min', '');

                    var maxInput = document.createElement('input');
                    maxInput.type = 'number';
                    maxInput.min = '1';
                    maxInput.placeholder = 'إلى';
                    maxInput.className = 'w-24 p-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm';
                    maxInput.setAttribute('data-tier-max', '');

                    var priceInput = document.createElement('input');
                    priceInput.type = 'number';
                    priceInput.step = '0.01';
                    priceInput.min = '0';
                    priceInput.placeholder = 'سعر/قطعة';
                    priceInput.className = 'w-32 p-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm';
                    priceInput.setAttribute('data-tier-price', '');

                    if (init && typeof init.min_quantity !== 'undefined' && init.min_quantity !== null) {
                        minInput.value = String(init.min_quantity);
                    }
                    if (init && typeof init.max_quantity !== 'undefined' && init.max_quantity !== null) {
                        maxInput.value = String(init.max_quantity);
                    }
                    if (init && typeof init.price_per_unit !== 'undefined' && init.price_per_unit !== null) {
                        priceInput.value = String(init.price_per_unit);
                    }

                    var removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'px-2 py-1 rounded-lg bg-red-500 text-white text-xs';
                    removeBtn.textContent = '×';
                    removeBtn.addEventListener('click', function() {
                        row.remove();
                        updatePricingTierNames(wrapper);
                    });

                    row.appendChild(minInput);
                    row.appendChild(maxInput);
                    row.appendChild(priceInput);
                    row.appendChild(removeBtn);
                    wrapper.appendChild(row);
                    updatePricingTierNames(wrapper);
                }

                document.querySelectorAll('[data-pricing-tiers-wrapper]').forEach(function(wrapper) {
                    var initialRaw = wrapper.getAttribute('data-initial');
                    var initialList = [];
                    try {
                        if (initialRaw) initialList = JSON.parse(initialRaw) || [];
                    } catch (e) {
                        initialList = [];
                    }

                    if (Array.isArray(initialList) && initialList.length) {
                        initialList.forEach(function(item) { buildPricingTierRow(wrapper, item); });
                    } else {
                        buildPricingTierRow(wrapper, null);
                    }
                });

                document.querySelectorAll('[data-add-pricing-tier-row]').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var target = btn.getAttribute('data-target');
                        var wrapper = document.getElementById(target);
                        if (!wrapper) return;
                        buildPricingTierRow(wrapper, null);
                    });
                });

            })();
        </script>

    </div>
</x-layouts.app>