<x-layouts.app :title="'منتجات المتجر'">
    @php($isCreateCategoryOpen = old('__modal') === 'create-category')
    @php($isCreateProductOpen = old('__modal') === 'create')
    <div class="page-content" id="seller-products-page">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">منتجات المتجر</h1>

       
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

        <div class="mb-6 flex items-center gap-3">
            <details class="shrink-0" {{ ($isCreateCategoryOpen && $errors->any()) ? 'open' : '' }}>
                <summary class="cursor-pointer px-4 py-3 text-sm font-bold rounded-xl bg-slate-800/50 text-white border border-slate-700/50 hover:bg-slate-700/50 [&::-webkit-details-marker]:hidden flex items-center gap-2">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    إضافة فئة
                </summary>
                <!-- Existing Category Modal Content -->
                <div class="fixed inset-0 z-[60]">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="this.closest('details').removeAttribute('open')"></div>
                    <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                        <div class="rounded-2xl border border-slate-700 bg-slate-900 shadow-2xl overflow-hidden">
                            <div class="flex items-center justify-between p-5 border-b border-slate-800">
                                <div class="font-bold text-lg text-white">إضافة فئة جديدة</div>
                                <button type="button" class="p-2 rounded-xl text-slate-400 hover:bg-slate-800" onclick="this.closest('details').removeAttribute('open')">✕</button>
                            </div>
                            <div class="p-6 max-h-[75vh] overflow-auto">
                                <form method="POST" action="{{ route('seller.categories.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @csrf
                                    <input type="hidden" name="__modal" value="create-category" />
                                    <div>
                                        <label class="block text-sm mb-2 text-slate-400">الاسم (عربي)</label>
                                        <input name="name_ar" value="{{ old('name_ar') }}" class="w-full p-3 rounded-xl bg-slate-800 border-none text-white focus:ring-2 focus:ring-rose-500/50" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-2 text-slate-400">الاسم (English)</label>
                                        <input name="name_en" value="{{ old('name_en') }}" class="w-full p-3 rounded-xl bg-slate-800 border-none text-white focus:ring-2 focus:ring-rose-500/50" required />
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm mb-2 text-slate-400">الصورة</label>
                                        <input type="file" name="image" accept="image/*" class="w-full p-3 rounded-xl bg-slate-800 border-none text-white" />
                                    </div>
                                    <div class="md:col-span-2 flex justify-end">
                                        <button type="submit" class="px-6 py-3 rounded-xl bg-rose-500 text-white font-bold hover:bg-rose-600 transition-all">حفظ الفئة</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </details>

            <form method="GET" action="{{ route('seller.products.index') }}" class="flex-1 relative group">
                <input
                    type="text"
                    name="q"
                    value="{{ $q ?? '' }}"
                    placeholder="ابحث عن منتج بالاسم أو الكود..."
                    class="w-full p-3.5 pr-12 rounded-2xl bg-slate-800/50 text-white border border-slate-700/50 focus:border-rose-500/50 focus:ring-0 transition-all text-sm font-medium" />
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                    <i data-lucide="search" class="w-5 h-5 text-slate-400 group-focus-within:text-rose-500 transition-colors"></i>
                </div>
            </form>
        </div>

        <!-- Floating Action Button (FAB) -->
        <details class="fixed bottom-6 left-6 z-[100]" {{ ($isCreateProductOpen && $errors->any()) ? 'open' : '' }}>
            <summary class="cursor-pointer w-16 h-16 rounded-full bg-gradient-to-br from-rose-500 to-orange-500 text-white shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all [&::-webkit-details-marker]:hidden">
                <i data-lucide="plus" class="w-8 h-8"></i>
            </summary>
            <!-- Create Product Modal Content -->
            <div class="fixed inset-0 z-[110]">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="this.closest('details').removeAttribute('open')"></div>
                <div class="relative mx-auto my-6 w-[95vw] max-w-xl">
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl overflow-hidden">
                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="font-semibold text-gray-800 dark:text-gray-100 italic">إضافة منتج جديد</div>
                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                        </div>
                        <div class="p-4 max-h-[75vh] overflow-auto text-right">
                            <form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <input type="hidden" name="__modal" value="create" />
                                
                                <div class="space-y-4">
                                    <div class="text-[10px] font-bold text-rose-500 uppercase tracking-widest text-right">المعلومات الأساسية</div>
                                    
                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">اسم المنتج (عربي)</label>
                                        <input name="name_ar" value="{{ old('name_ar') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-right" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">اسم المنتج (English)</label>
                                        <input name="name_en" value="{{ old('name_en') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الكود (SKU)</label>
                                        <input name="sku" value="{{ old('sku') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الوصف (عربي)</label>
                                        <textarea name="description_ar" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-right" rows="2">{{ old('description_ar') }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الوصف (English)</label>
                                        <textarea name="description_en" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" rows="2">{{ old('description_en') }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الفئة</label>
                                        <select name="category_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-right">
                                            <option value="">اختر الفئة</option>
                                            @foreach(($categories ?? []) as $cat)
                                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name_ar }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <hr class="border-gray-200 dark:border-gray-700 my-4" />

                                <div class="space-y-4">
                                    <div class="text-[10px] font-bold text-rose-500 uppercase tracking-widest text-right">الصور</div>
                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الصورة الأساسية</label>
                                        <div class="flex items-center gap-2 mb-2">
                                            <input type="file" name="image" id="create-main-image" class="hidden" data-file-label-target="create-main-image-label" accept="image/*" />
                                            <label for="create-main-image" class="w-11 h-11 rounded-2xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 flex items-center justify-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">+</label>
                                            <div id="create-main-image-label" class="text-xs text-gray-500">اضغط + لاختيار صورة</div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">صور إضافية</label>
                                        <div id="seller-create-images-wrapper" class="space-y-3"></div>
                                        <button type="button" class="mt-2 inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-rose-400 to-orange-400 text-white text-xs font-semibold" data-add-image-row data-target="seller-create-images-wrapper">+ إضافة صورة أخرى</button>
                                    </div>
                                </div>

                                <hr class="border-gray-200 dark:border-gray-700 my-4" />

                                <div class="space-y-4">
                                    <div class="text-[10px] font-bold text-rose-500 uppercase tracking-widest text-right">الأسعار والكمية</div>
                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">سعر القطعة</label>
                                        <input type="number" step="0.01" name="price" value="{{ old('price') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-left font-bold" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الكمية</label>
                                        <input type="number" name="quantity" value="{{ old('quantity') }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-left font-bold" required />
                                    </div>

                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 space-y-4 text-right">
                                        <div class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center justify-between">
                                            <span>تدرج أسعار الجملة</span>
                                            <span class="text-[10px] text-gray-500 italic uppercase">Bulk Pricing</span>
                                        </div>
                                        <div id="seller-create-pricing-tiers-wrapper" class="space-y-3" data-pricing-tiers-wrapper data-initial='@json(old('pricing_tiers', []))'></div>
                                        <button type="button" class="w-full py-2.5 rounded-lg bg-gradient-to-r from-rose-400 to-orange-400 text-white font-bold text-sm shadow-lg shadow-rose-500/10 active:scale-[0.98] transition-all" data-add-pricing-tier-row data-target="seller-create-pricing-tiers-wrapper">
                                            + إضافة مستوى سعر
                                        </button>
                                    </div>
                                </div>

                                <div class="pt-4">
                                    <button type="submit" class="w-full py-3 rounded-lg bg-rose-500 text-white font-bold hover:bg-rose-600 shadow-xl shadow-rose-500/20 transition-all">حفظ المنتج الجديد</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </details>

        <div class="space-y-6">
            @forelse(($products ?? []) as $product)
                <div class="group bg-white dark:bg-[#151b2b] hover:bg-gray-50 dark:hover:bg-[#1a2135] rounded-3xl p-4 flex items-center justify-between border border-gray-100 dark:border-slate-800/50 shadow-lg transition-all" data-product-card data-search="{{ mb_strtolower(($product->name_ar ?? '') . ' ' . ($product->name_en ?? '') . ' ' . $product->name . ' ' . $product->sku . ' ' . (optional($product->getRelation('category'))->name_ar ?? ($product->category ?? '')) . ' ' . (optional($product->getRelation('category'))->name_en ?? ($product->category ?? ''))) }}">
                    
                    <!-- Right Side: Image (First in source for RTL) -->
                    <div class="relative shrink-0">
                        <div class="w-20 h-20 rounded-2xl bg-gray-50 dark:bg-[#1c2336] border border-gray-100 dark:border-slate-700/30 flex items-center justify-center overflow-hidden shadow-2xl group-hover:border-rose-500/30 transition-all duration-300">
                            @if(!empty($product->image))
                                <img src="{{ asset('storage/' . $product->image) }}" alt="product" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                            @else
                                <img src="{{ asset('apple-touch-icon.png') }}" alt="default" class="w-12 h-12 object-contain opacity-10" />
                            @endif
                        </div>
                    </div>

                    <!-- Center: Content -->
                    <div class="flex-1 text-right mx-6">
                        <div class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-rose-500 transition-colors">{{ $product->name_ar ?? $product->name }}</div>
                        <div class="text-xs text-gray-500 dark:text-slate-500 mt-1 line-clamp-1 opacity-80">{{ $product->description_ar ?? '-' }}</div>
                        
                        <div class="mt-4 flex flex-wrap gap-2 justify-end">
                            <span class="px-2.5 py-1 rounded-lg bg-rose-500/10 text-rose-500 text-[10px] font-bold border border-rose-500/20">
                                {{ optional($product->getRelation('category'))->name_ar ?? ($product->category ?: 'بدون فئة') }}
                            </span>
                            <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-400 text-[10px] font-bold border border-slate-700/50">
                                SKU: {{ $product->sku }}
                            </span>
                        </div>
                    </div>

                    <!-- Left Side: Actions (Last in source for RTL) -->
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('seller.products.destroy', $product->id) }}" onsubmit="return confirm('حذف المنتج من متجرك؟');">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-5 py-2.5 text-xs font-bold rounded-xl bg-red-500 text-white hover:bg-red-600 shadow-lg shadow-red-500/20 transition-all">حذف</button>
                        </form>

                        @php($isEditOpen = old('__modal') === ('edit-' . $product->id))
                        <details {{ ($isEditOpen && $errors->any()) ? 'open' : '' }}>
                            <summary class="cursor-pointer px-5 py-2.5 text-xs font-bold text-gray-700 dark:text-white rounded-xl bg-gray-100 dark:bg-[#1e2538] hover:bg-gray-200 dark:hover:bg-slate-700/50 border border-gray-200 dark:border-slate-700/50 [&::-webkit-details-marker]:hidden flex items-center gap-1.5 transition-all">
                                تعديل
                                <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                            </summary>
                            <div class="fixed inset-0 z-[110]">
                                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="this.closest('details').removeAttribute('open')"></div>
                                <div class="relative mx-auto my-6 w-[95vw] max-w-xl text-right">
                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl overflow-hidden">
                                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                            <div class="font-semibold text-gray-800 dark:text-gray-100 italic">تعديل: {{ $product->name_ar }}</div>
                                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                        </div>
                                        <div class="p-4 max-h-[75vh] overflow-auto">
                                            <form method="POST" action="{{ route('seller.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-4 text-right">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="__modal" value="edit-{{ $product->id }}" />
                                                
                                                <div class="space-y-4">
                                                    <div class="text-[10px] font-bold text-rose-500 uppercase tracking-widest">المعلومات الأساسية</div>
                                                    <div>
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">اسم المنتج (عربي)</label>
                                                        <input name="name_ar" value="{{ old('name_ar', $product->name_ar) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-right" required />
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">اسم المنتج (English)</label>
                                                        <input name="name_en" value="{{ old('name_en', $product->name_en) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الكود (SKU)</label>
                                                        <input name="sku" value="{{ old('sku', $product->sku) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الفئة</label>
                                                        <select name="category_id" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-right">
                                                            <option value="">اختر الفئة</option>
                                                            @foreach(($categories ?? []) as $cat)
                                                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name_ar }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <hr class="border-gray-200 dark:border-gray-700 my-4" />

                                                <div class="space-y-4">
                                                    <div class="text-[10px] font-bold text-rose-500 uppercase tracking-widest text-right">الصور</div>
                                                    <div>
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الصورة الأساسية</label>
                                                        <div class="flex items-center gap-4 mb-4">
                                                            <div class="w-16 h-16 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 overflow-hidden shrink-0">
                                                                @if(!empty($product->image))
                                                                    <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover" />
                                                                @else
                                                                    <div class="w-full h-full flex items-center justify-center text-gray-400 font-bold">N/A</div>
                                                                @endif
                                                            </div>
                                                            <div class="flex-1">
                                                                <div class="flex items-center gap-2">
                                                                    <input type="file" name="image" id="edit-main-image-{{ $product->id }}" class="hidden" data-file-label-target="edit-main-image-label-{{ $product->id }}" accept="image/*" />
                                                                    <label for="edit-main-image-{{ $product->id }}" class="w-11 h-11 rounded-2xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 flex items-center justify-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">+</label>
                                                                    <div id="edit-main-image-label-{{ $product->id }}" class="text-xs text-gray-500">تغيير الصورة</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">صور إضافية</label>
                                                        <div id="seller-edit-images-wrapper-{{ $product->id }}" class="space-y-3"></div>
                                                        <button type="button" class="mt-2 inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-rose-400 to-orange-400 text-white text-xs font-semibold" data-add-image-row data-target="seller-edit-images-wrapper-{{ $product->id }}">+ إضافة صورة أخرى</button>
                                                    </div>
                                                </div>

                                                <hr class="border-gray-200 dark:border-gray-700 my-4" />

                                                <div class="space-y-4">
                                                    <div class="text-[10px] font-bold text-rose-500 uppercase tracking-widest">الأسعار والكمية</div>
                                                    <div>
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">سعر القطعة</label>
                                                        <input type="number" step="0.01" name="price" value="{{ old('price', $product->pivot->price ?? $product->price) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-left font-bold" required />
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الكمية</label>
                                                        <input type="number" name="quantity" value="{{ old('quantity', $product->pivot->quantity ?? $product->quantity) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-left font-bold" required />
                                                    </div>

                                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 space-y-4">
                                                        <div class="text-sm font-bold text-gray-800 dark:text-gray-100 flex items-center justify-between">
                                                            <span>تدرج أسعار الجملة</span>
                                                            <span class="text-[10px] text-gray-500 italic uppercase">Bulk Pricing</span>
                                                        </div>
                                                        <div id="seller-edit-pricing-tiers-{{ $product->id }}" class="space-y-3" data-pricing-tiers-wrapper data-initial='@json($product->pricingTiers ?? [])'></div>
                                                        <button type="button" class="w-full py-2.5 rounded-lg bg-gradient-to-r from-rose-400 to-orange-400 text-white font-bold text-sm shadow-lg shadow-rose-500/10 transform active:scale-[0.98] transition-all" data-add-pricing-tier-row data-target="seller-edit-pricing-tiers-{{ $product->id }}">
                                                            + إضافة مستوى سعر
                                                        </button>
                                                    </div>
                                                </div>

                                                <hr class="border-gray-200 dark:border-gray-700 my-4" />

                                                <div class="space-y-4">
                                                    <div class="text-[10px] font-bold text-rose-500 uppercase tracking-widest mb-2">الوصف والمواصفات</div>
                                                    <div>
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الوصف (عربي)</label>
                                                        <textarea name="description_ar" rows="3" placeholder="وصف المنتج..." class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-right">{{ old('description_ar', $product->description_ar) }}</textarea>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الوصف (English)</label>
                                                        <textarea name="description_en" rows="3" placeholder="Product description..." class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">{{ old('description_en', $product->description_en) }}</textarea>
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الألوان (اختياري)</label>
                                                        <div id="edit-colors-{{ $product->id }}" class="space-y-2">
                                                            @foreach((array) $product->colors as $c)
                                                                <div class="flex gap-2" data-repeat-row>
                                                                    <input name="colors[]" value="{{ $c }}" class="flex-1 p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm text-right" />
                                                                    <button type="button" class="w-10 h-10 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200" data-remove-row>×</button>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <button type="button" class="mt-2 inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-rose-400 to-orange-400 text-white text-xs font-semibold" data-add-text-row data-target="edit-colors-{{ $product->id }}" data-name="colors[]">+ أضف لون</button>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المقاسات (اختياري)</label>
                                                        <div id="edit-sizes-{{ $product->id }}" class="space-y-2">
                                                            @foreach((array) $product->sizes as $sz)
                                                                <div class="flex gap-2" data-repeat-row>
                                                                    <input name="sizes[]" value="{{ $sz }}" class="flex-1 p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 text-sm text-right" />
                                                                    <button type="button" class="w-10 h-10 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200" data-remove-row>×</button>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <button type="button" class="mt-2 inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-rose-400 to-orange-400 text-white text-xs font-semibold" data-add-text-row data-target="edit-sizes-{{ $product->id }}" data-name="sizes[]">+ أضف مقاس</button>
                                                    </div>
                                                </div>

                                                <div class="pt-4">
                                                    <button type="submit" class="w-full py-3 rounded-lg bg-rose-500 text-white font-bold hover:bg-rose-600 shadow-xl shadow-rose-500/20 transition-all">حفظ التعديلات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </details>

                        <details>
                            <summary class="cursor-pointer px-5 py-2.5 text-xs font-bold text-white rounded-xl bg-[#1e2538] hover:bg-slate-700/50 border border-slate-700/50 [&::-webkit-details-marker]:hidden flex items-center gap-1.5 transition-all">
                                عرض
                                <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                            </summary>
                            <div class="fixed inset-0 z-[110]">
                                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="this.closest('details').removeAttribute('open')"></div>
                                <div class="relative mx-auto my-6 w-[95vw] max-w-2xl text-right">
                                    <div class="rounded-3xl border border-slate-700 bg-slate-900 shadow-2xl overflow-hidden p-6">
                                        <div class="flex gap-6">
                                            <div class="w-32 h-32 rounded-3xl overflow-hidden border border-slate-700 shadow-inner bg-slate-800 shrink-0">
                                                @if(!empty($product->image))
                                                    <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover" />
                                                @else
                                                    <img src="{{ asset('apple-touch-icon.png') }}" class="w-full h-full object-contain p-4 opacity-30" />
                                                @endif
                                            </div>
                                            <div class="flex-1 text-right">
                                                <h3 class="text-2xl font-bold text-white mb-2">{{ $product->name_ar }}</h3>
                                                <p class="text-slate-400 text-sm leading-relaxed mb-4">{{ $product->description_ar ?? 'لا يوجد وصف متاح' }}</p>
                                                <div class="flex flex-wrap gap-2 justify-end">
                                                    <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-500 text-[10px] font-bold italic tracking-wider">{{ optional($product->getRelation('category'))->name_ar ?? ($product->category ?? 'General') }}</span>
                                                    <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-400 text-[10px] font-bold">SKU: {{ $product->sku }}</span>
                                                    <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-400 text-[10px] font-bold">Qty: {{ $product->quantity }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </details>
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
    </div>
</x-layouts.app>

<script>
    (function() {
        function setFileLabel(inputEl) {
            if (!inputEl) return;
            var targetId = inputEl.getAttribute('data-file-label-target');
            var targetEl = targetId ? document.getElementById(targetId) : null;
            if (!targetEl) return;
            var files = inputEl.files ? Array.prototype.slice.call(inputEl.files) : [];
            targetEl.textContent = files.length === 0 ? 'اضغط + لاختيار صورة' : (files.length === 1 ? files[0].name : 'تم اختيار ' + files.length + ' صور');
        }

        function wireFileInput(el) {
            if (!el) return;
            el.addEventListener('change', function() { setFileLabel(this); });
        }

        document.querySelectorAll('input[type="file"][data-file-label-target]').forEach(wireFileInput);

        document.addEventListener('click', function(e) {
            // Add Text/Repeat Row
            var addTextBtn = e.target.closest('[data-add-text-row]');
            if (addTextBtn) {
                e.preventDefault();
                var targetId = addTextBtn.getAttribute('data-target');
                var name = addTextBtn.getAttribute('data-name');
                var wrapper = targetId ? document.getElementById(targetId) : null;
                if (!wrapper) return;

                var row = document.createElement('div');
                row.className = 'flex gap-2 items-center mb-2';
                row.setAttribute('data-repeat-row', '');

                var input = document.createElement('input');
                input.name = name;
                input.className = 'flex-1 p-2.5 rounded-xl bg-slate-900 border-none text-white text-sm text-right';

                var removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'w-10 h-10 rounded-xl bg-slate-800 text-slate-400 hover:text-red-500 transition-colors';
                removeBtn.textContent = '×';
                removeBtn.addEventListener('click', function() { row.remove(); });

                row.appendChild(input);
                row.appendChild(removeBtn);
                wrapper.appendChild(row);
                input.focus();
                return;
            }

            // Remove Row (data-remove-row)
            var removeBtn = e.target.closest('[data-remove-row]');
            if (removeBtn) {
                e.preventDefault();
                var row = removeBtn.closest('[data-repeat-row]');
                if (row) row.remove();
                return;
            }

            // Add Image Row
            var addImgBtn = e.target.closest('[data-add-image-row]');
            if (addImgBtn) {
                e.preventDefault();
                var targetId = addImgBtn.getAttribute('data-target');
                var wrapper = targetId ? document.getElementById(targetId) : null;
                if (!wrapper) return;

                var id = 'img_' + Math.random().toString(36).substr(2, 9);
                var row = document.createElement('div');
                row.className = 'flex items-center gap-2 mb-2';
                row.setAttribute('data-image-row', '');

                var input = document.createElement('input');
                input.type = 'file'; input.name = 'images[]'; input.id = id;
                input.className = 'hidden'; input.setAttribute('data-file-label-target', 'lbl_' + id);

                var label = document.createElement('label');
                label.setAttribute('for', id);
                label.className = 'w-11 h-11 rounded-2xl border border-slate-700 bg-slate-900 text-slate-400 flex items-center justify-center cursor-pointer hover:bg-slate-800 transition-colors';
                label.textContent = '+';

                var text = document.createElement('div');
                text.id = 'lbl_' + id; text.className = 'text-xs text-slate-500'; text.textContent = 'اضغط + لاختيار صورة';

                var rem = document.createElement('button');
                rem.type = 'button'; rem.className = 'ms-auto w-10 h-10 rounded-xl bg-slate-800 text-slate-400 hover:text-red-500 transition-colors';
                rem.textContent = '×'; rem.addEventListener('click', function() { row.remove(); });

                row.appendChild(input); row.appendChild(label); row.appendChild(text); row.appendChild(rem);
                wrapper.appendChild(row);
                wireFileInput(input);
                return;
            }

            // Add Pricing Tier
            var addTierBtn = e.target.closest('[data-add-pricing-tier-row]');
            if (addTierBtn) {
                e.preventDefault();
                var targetId = addTierBtn.getAttribute('data-target');
                var wrapper = targetId ? document.getElementById(targetId) : null;
                if (wrapper) buildPricingTierRow(wrapper, null);
                return;
            }
        });

        function updateTierNames(wrapper) {
            wrapper.querySelectorAll('[data-pricing-tier-row]').forEach(function(row, idx) {
                row.querySelector('[data-tier-min]').name = 'pricing_tiers['+idx+'][min_quantity]';
                row.querySelector('[data-tier-max]').name = 'pricing_tiers['+idx+'][max_quantity]';
                row.querySelector('[data-tier-price]').name = 'pricing_tiers['+idx+'][price_per_unit]';
            });
        }

        function buildPricingTierRow(wrapper, initData) {
            var row = document.createElement('div');
            row.className = 'flex flex-col gap-3 mb-4 bg-slate-800/40 p-4 rounded-2xl border border-slate-700/50 shadow-inner group transition-all hover:border-rose-500/30';
            row.setAttribute('data-pricing-tier-row', '');

            var createField = function(label, name, placeholder, isPrice) {
                var fieldDiv = document.createElement('div');
                fieldDiv.className = 'flex flex-col gap-1.5';
                
                var l = document.createElement('label');
                l.className = 'text-[10px] font-bold text-slate-500 uppercase tracking-widest px-1';
                l.textContent = label;
                
                var input = document.createElement('input');
                input.type = 'number';
                if (isPrice) input.step = '0.01';
                input.placeholder = placeholder;
                input.className = 'w-full p-3 rounded-xl bg-slate-900 border border-slate-700/50 text-white text-sm focus:ring-1 focus:ring-rose-500/30 transition-all font-bold';
                input.setAttribute('data-tier-' + name, '');
                
                fieldDiv.appendChild(l);
                fieldDiv.appendChild(input);
                return { div: fieldDiv, input: input };
            };

            var minField = createField('من كمية', 'min', '1');
            var maxField = createField('إلى كمية', 'max', '100');
            var prcField = createField('السعر للقطعة', 'price', '0.00', true);

            if (initData) {
                minField.input.value = initData.min_quantity || initData.min || '';
                maxField.input.value = initData.max_quantity || initData.max || '';
                prcField.input.value = initData.price_per_unit || initData.price || '';
            }

            var remBtn = document.createElement('button');
            remBtn.type = 'button';
            remBtn.className = 'mt-1 w-full py-2.5 rounded-xl bg-red-500/10 text-red-500 border border-red-500/20 hover:bg-red-500 hover:text-white transition-all text-xs font-bold';
            remBtn.textContent = 'حذف هذا المستوى';
            remBtn.addEventListener('click', function() {
                row.classList.add('opacity-0', 'scale-95');
                setTimeout(function() {
                    row.remove();
                    updateTierNames(wrapper);
                }, 200);
            });

            row.appendChild(minField.div);
            row.appendChild(maxField.div);
            row.appendChild(prcField.div);
            row.appendChild(remBtn);
            wrapper.appendChild(row);
            updateTierNames(wrapper);
        }

        document.querySelectorAll('[data-pricing-tiers-wrapper]').forEach(function(w) {
            var raw = w.getAttribute('data-initial');
            try {
                var list = JSON.parse(raw) || [];
                if (Array.isArray(list) && list.length) {
                    list.forEach(function(i) { buildPricingTierRow(w, i); });
                } else {
                    buildPricingTierRow(w, null);
                }
            } catch(e) { 
                if (!w.querySelector('[data-pricing-tier-row]')) buildPricingTierRow(w, null);
            }
        });

        if (typeof lucide !== 'undefined') lucide.createIcons();

        // Initialize Swiper
        if (typeof Swiper !== 'undefined') {
            new Swiper('.hero-swiper', {
                loop: true,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.hero-swiper .swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.hero-swiper .swiper-button-next',
                    prevEl: '.hero-swiper .swiper-button-prev',
                },
            });
        }
    })();
</script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
