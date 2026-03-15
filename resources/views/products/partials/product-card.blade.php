@php
    $isEditOpen = old('__modal') === ('edit-' . $product->id);
@endphp
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden shrink-0 w-full" data-product-card data-search="{{ mb_strtolower(($product->name_ar ?? '') . ' ' . ($product->name_en ?? '') . ' ' . $product->name . ' ' . $product->sku . ' ' . ($product->category ?? '') . ' ' . ($product->description ?? '') . ' ' . $product->price . ' ' . $product->quantity) }}">
                <div class="p-4 sm:p-5 flex items-center justify-between gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center border border-gray-100 dark:border-gray-600 overflow-hidden">
                        @if(!empty($product->image))
                        <img src="{{ asset('storage/' . $product->image) }}" alt="product" class="w-full h-full object-cover" />
                        @else
                        <img src="{{ asset('apple-touch-icon.png') }}" alt="default" class="w-full h-full object-cover" />
                        @endif
                    </div>

                    <div class="flex-1 text-right">
                        <div class="flex flex-wrap items-center justify-end gap-2">
                            <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $product->name_ar ?? $product->name }}</div>
                            <span class="px-2 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">{{ $product->sku }}</span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $product->name_en ?? $product->name }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex flex-wrap items-center justify-end gap-2">
                            <span>{{ $product->category ?? '-' }}</span>
                            @if(!empty($product->color))
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-sky-50 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300">
                                    اللون: {{ $product->color }}
                                </span>
                            @endif
                            @if(!empty($product->size))
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-300">
                                    المقاس: {{ $product->size }}
                                </span>
                            @endif
                            @if(((int) ($product->featured ?? 0)) === 1)
                                <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300">Featured</span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                            {{ $product->description ?? '-' }}
                        </div>
                    </div>

                    <div class="text-rose-600 font-bold text-lg whitespace-nowrap">
                        {{ number_format((float) $product->price, 0, '.', ',') }} ج.م
                    </div>
                </div>

                <div class="px-4 pb-4 sm:px-5 sm:pb-5">
                    <div class="flex items-center justify-end gap-2 mt-2">
                        <details>
                                <summary class="cursor-pointer px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/40 [&::-webkit-details-marker]:hidden">عرض</summary>
                                <div class="fixed inset-0 z-40">
                                    <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>

                                    <div class="relative mx-auto my-6 w-[95vw] max-w-xl max-h-[90vh] flex flex-col">
                                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl flex flex-col max-h-[90vh] overflow-hidden">
                                            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                                                <div class="font-semibold text-gray-800 dark:text-gray-100">تفاصيل المنتج</div>
                                                <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                            </div>

                                            <div class="p-4 overflow-y-auto flex-1 min-h-0 space-y-4">
                                                <div class="flex items-start gap-3">
                                                    <div class="w-16 h-16 rounded-2xl bg-gray-50 dark:bg-gray-700 border border-gray-100 dark:border-gray-600 overflow-hidden shrink-0">
                                                        @if(!empty($product->image))
                                                        <img src="{{ asset('storage/' . $product->image) }}" alt="product" class="w-full h-full object-cover" />
                                                        @else
                                                        <img src="{{ asset('apple-touch-icon.png') }}" alt="default" class="w-full h-full object-cover" />
                                                        @endif
                                                    </div>

                                                    <div class="flex-1 text-right min-w-0">
                                                        <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $product->name_ar ?? $product->name }}</div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">الكود: {{ $product->sku }}</div>
                                                        <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">التصنيف: {{ $product->category ?? '-' }}</div>
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
                                                        <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">الكمية الإجمالية: {{ $product->quantity }}</div>
                                                        @if((float) ($product->unit_price ?? 0) > 0)
                                                            <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">سعر القطعة (عام): {{ number_format((float) $product->unit_price, 2, '.', ',') }} ج.م</div>
                                                        @endif
                                                        <div class="text-rose-600 font-bold text-lg mt-1">السعر: {{ number_format((float) $product->price, 0, '.', ',') }} ج.م</div>
                                                    </div>
                                                </div>

                                                <div class="text-right text-gray-700 dark:text-gray-200 border-t border-gray-200 dark:border-gray-700 pt-3">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">الوصف:</span>
                                                    {{ $product->description_ar ?? $product->description ?? '-' }}
                                                </div>

                                                @php
                                                    $suppliersList = $product->suppliers ?? collect();
                                                    $tiersBySupplier = ($product->pricingTiers ?? collect())->groupBy('supplier_id');
                                                @endphp
                                                @if($suppliersList->isNotEmpty())
                                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">تفاصيل المنتج حسب المورد</h4>
                                                    <div class="space-y-4">
                                                        @foreach($suppliersList as $supplier)
                                                        @php
                                                            $pivot = $supplier->pivot;
                                                            $supplierTiers = $tiersBySupplier->get($supplier->id, collect())->sortBy('min_quantity');
                                                        @endphp
                                                        <div class="rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 p-3 text-right">
                                                            <div class="font-medium text-gray-800 dark:text-gray-100">{{ $supplier->name ?? 'مورد #' . $supplier->id }}</div>
                                                            <div class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-300">
                                                                @if((float) ($pivot->unit_price ?? 0) > 0)
                                                                <div>سعر القطعة: <span class="font-semibold text-rose-600">{{ number_format((float) $pivot->unit_price, 2, '.', ',') }} ج.م</span></div>
                                                                @endif
                                                                <div>السعر: <span class="font-semibold">{{ number_format((float) ($pivot->price ?? 0), 0, '.', ',') }} ج.م</span></div>
                                                                <div>الكمية المتاحة: <span class="font-semibold">{{ (int) ($pivot->quantity ?? 0) }}</span></div>
                                                                @if($supplierTiers->isNotEmpty())
                                                                <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                                                                    <span class="text-gray-500 dark:text-gray-400">شرائح السعر:</span>
                                                                    <ul class="mt-1 space-y-0.5">
                                                                        @foreach($supplierTiers as $tier)
                                                                        <li>
                                                                            من {{ (int) $tier->min_quantity }} @if($tier->max_quantity !== null) إلى {{ (int) $tier->max_quantity }} @endif قطعة
                                                                            = <span class="font-semibold text-rose-600">{{ number_format((float) $tier->price_per_unit, 2, '.', ',') }} ج.م</span> / قطعة
                                                                        </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </details>

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
                                                        @php
                                                            $editSuppliersInitial = '[]';
                                                            $editTiersInitial = '[]';
                                                            try {
                                                                $suppliersCollection = collect($product->suppliers ?? []);
                                                                $suppliersData = $suppliersCollection->map(fn($s) => [
                                                                    'id' => (int) $s->id,
                                                                    'price' => (float) ($s->pivot->price ?? 0),
                                                                    'unit_price' => (float) ($s->pivot->unit_price ?? 0),
                                                                    'quantity' => (int) ($s->pivot->quantity ?? 0)
                                                                ])->values()->all();
                                                                $tiersCollection = collect($product->pricingTiers ?? []);
                                                                $tiersData = $tiersCollection->groupBy('supplier_id')->map(fn($g) =>
                                                                    $g->map(fn($t) => [
                                                                        'min_quantity' => (int) $t->min_quantity,
                                                                        'max_quantity' => $t->max_quantity !== null ? (int) $t->max_quantity : null,
                                                                        'price_per_unit' => (float) $t->price_per_unit
                                                                    ])->values()
                                                                )->toArray();
                                                                $editSuppliersInitial = json_encode($suppliersData);
                                                                $editTiersInitial = json_encode($tiersData);
                                                            } catch (\Throwable $e) {
                                                                // keep default empty JSON arrays
                                                            }
                                                        @endphp
                                                        <div id="edit-suppliers-wrapper-{{ $product->id }}" class="space-y-2" data-suppliers-wrapper data-initial="{{ $editSuppliersInitial ?? '[]' }}" data-tiers-initial="{{ $editTiersInitial ?? '[]' }}">
                                                            <!-- rows will be injected via JS -->
                                                        </div>
                                                        <button type="button" class="mt-2 inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-rose-400 to-orange-400 text-white text-xs font-semibold" data-add-supplier-row data-target="edit-suppliers-wrapper-{{ $product->id }}">
                                                            + إضافة مورد
                                                        </button>
                                                    </div>

                                                    <input type="hidden" name="unit_price" value="{{ $isEditOpen ? old('unit_price', ($product->unit_price ?? '')) : ($product->unit_price ?? '') }}" />
                                                    <input type="hidden" name="quantity" value="{{ $isEditOpen ? old('quantity', $product->quantity) : $product->quantity }}" />

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
