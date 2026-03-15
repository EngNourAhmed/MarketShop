@php($lang = session('lang', 'en'))
@php($isAr = $lang === 'ar')
@php($productTitle = $isAr ? ($product->name_ar ?? $product->name) : ($product->name_en ?? $product->name))
<x-shop-layouts.app :title="$productTitle">
    <x-slot name="headerActions">
        <a href="{{ route('customer.home') }}" class="md:hidden flex items-center justify-center p-2 -ml-2 rounded-full hover:bg-gray-100 dark:hover:bg-slate-800 text-gray-700 dark:text-slate-200">
            <i data-lucide="arrow-left" class="w-6 h-6 {{ $isAr ? 'rotate-180' : '' }}"></i>
        </a>
    </x-slot>
    @php($suppliers = $product->suppliers ?? collect())
    @php($supplierType = $supplierType ?? null)
    @if(in_array($supplierType, ['factory', 'vendor'], true))
        @php($suppliers = $suppliers->where('type', $supplierType)->values())
    @endif
    @php($factory = $suppliers->firstWhere('type', 'factory'))
    @php($primaryImage = !empty($product->image) ? asset('storage/' . $product->image) : asset('apple-touch-icon.png'))
    @php($sortedSuppliers = $suppliers->sortBy(fn($s) => (float) ($s->pivot->price ?? 0))->values())
    @php($minPrice = $sortedSuppliers->isNotEmpty() ? (float) ($sortedSuppliers->first()->pivot->price ?? 0) : (float) ($product->price ?? 0))
    @php($avgRatingVal = (float) ($avgRating ?? 0))
    @php($ratingsCountVal = (int) ($ratingsCount ?? 0))
    @php($userRatingVal = (int) ($userRating ?? 0))
    @php($productImages = collect(($product->images ?? []))->filter()->values())
    @php($gallery = $productImages->map(fn($p) => asset('storage/' . ltrim((string) $p, '/')))->filter()->values())
    @php($imageUrl = $gallery->first() ?: $primaryImage)
    @php($factories = ($suppliers ?? collect())->where('type', 'factory')->values())
    @php($unitPrice = $sortedSuppliers->isNotEmpty() ? (float) ($sortedSuppliers->first()->pivot->unit_price ?? $sortedSuppliers->first()->pivot->price ?? 0) : (float) ($product->unit_price ?? 0))
    @php($firstTier = ($product->pricingTiers ?? collect())->whereNotNull('supplier_id')->sortBy('min_quantity')->first())
    @php($wholesalePrice = $firstTier ? (float) ($firstTier->price_per_unit ?? 0) : 0)
    @php($suppliersForModal = ($sortedSuppliers ?? collect())->map(fn($s) => [
        'id' => (int) $s->id,
        'name' => (string) ($s->name ?? ''),
        'type' => (string) ($s->type ?? ''),
        'price' => (float) ($s->pivot->price ?? 0),
        'unit_price' => (float) ($s->pivot->unit_price ?? 0),
        'quantity' => $s->pivot->quantity !== null ? (int) $s->pivot->quantity : null,
    ])->values())


    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <style>
        .expandable-content { max-height: 0; overflow: hidden; transition: max-height 0.5s ease-in-out, opacity 0.5s ease; opacity: 0; }
        .expandable-content.expanded { max-height: 1000px; opacity: 1; }
        .rotate-icon { transition: transform 0.3s ease-in-out; }
        .rotate-icon.rotated { transform: rotate(180deg); }

        .swiper-button-next,
        .swiper-button-prev {
            width: 42px;
            height: 42px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(226, 232, 240, 0.8);
            color: #1e293b;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            backdrop-filter: blur(6px);
        }
        .dark .swiper-button-next,
        .dark .swiper-button-prev {
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid rgba(51, 65, 85, 0.9);
            color: #fff;
            box-shadow: 0 10px 25px rgba(0,0,0,0.35);
        }
        .swiper-button-next:after,
        .swiper-button-prev:after {
            font-size: 14px;
            font-weight: 700;
        }
        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background: rgba(248, 250, 252, 0.95);
        }
        .dark .swiper-button-next:hover,
        .dark .swiper-button-prev:hover {
            background: rgba(30, 41, 59, 0.85);
        }

        .swiper-pagination-bullet { background: rgba(148, 163, 184, 0.55); opacity: 1; }
        .swiper-pagination-bullet-active { background: rgba(244, 63, 94, 0.95); }

        /* Product page order modal */
        #product-order-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 50;
            opacity: 0;
            transition: opacity 0.3s ease-out;
        }
        #product-order-overlay.product-order-overlay-visible { opacity: 1; }
        .product-order-modal-dialog {
            position: fixed;
            z-index: 60;
            width: min(96vw, 760px);
            max-height: 92vh;
            border-radius: 1rem 1rem 0 0;
            box-shadow: 0 -4px 24px rgba(0,0,0,0.15);
            bottom: 0;
            left: 50%;
            transform: translateX(-50%) translateY(100%);
            transition: transform 0.35s cubic-bezier(0.32, 0.72, 0, 1), opacity 0.25s ease-out;
            display: flex;
            flex-direction: column;
        }
        .product-order-modal-dialog.product-order-modal-visible {
            transform: translateX(-50%) translateY(0);
        }
        @media (min-width: 768px) {
            .product-order-modal-dialog { border-radius: 1rem; }
        }

        /* Reaction Picker */
        .reaction-picker {
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(-10px);
            background: white;
            border-radius: 9999px;
            padding: 4px 8px;
            display: flex;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            z-index: 20;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .dark .reaction-picker { background: #1e293b; border: 1px solid #334155; }
        .reaction-container:hover .reaction-picker {
            opacity: 1;
            pointer-events: auto;
            transform: translateX(-50%) translateY(-20px);
        }
        .reaction-btn {
            font-size: 24px;
            transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
        }
        .reaction-btn:hover { transform: scale(1.3); }

    </style>

    <div class="space-y-6 text-gray-900 dark:text-slate-100 rounded-2xl p-4 md:p-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <div class="lg:col-span-5">
                <div class="rounded-2xl overflow-hidden p-">
                    <div class="h-32 md:h-96 bg-gray-200 dark:bg-gray-700 rounded-xl flex items-center justify-center font-semibold text-gray-500 text-center text-xs p-1">
                        <img src="{{ $imageUrl }}" alt="product" class="w-full h-full object-cover rounded-xl"/>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="mx-3">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="text-lg md:text-3xl lg:text-4xl font-bold text-gray-800 dark:text-white mb-1 md:mb-2 leading-tight">{{ $productTitle }}</div>
                        </div>
                    </div>

                    <div class="text-xs md:text-base lg:text-lg text-gray-600 dark:text-gray-400 mb-4">
                        {{ ($isAr ? ($product->description_ar ?? $product->description) : ($product->description_en ?? $product->description)) ?? ($isAr ? 'لا يوجد وصف.' : 'No description.') }}
                    </div>

                </div>
            </div>
        </div>

        <!-- Product Info Grid -->
        <!-- Product Stats Grid -->
        <div class="grid grid-cols-1 gap-4 mb-8">
            <!-- Main Stats -->
            <div class="grid grid-cols-3 gap-2">
                <div class="py-4 px-2 bg-gray-50 rounded-xl flex flex-col items-center justify-center border border-gray-200 dark:bg-slate-900 dark:border-slate-800">
                    <div class="text-xs text-gray-500 mb-2 dark:text-gray-400">{{ $isAr ? 'المناسب ل' : 'Appropriate for' }}</div>
                    <div class="flex items-center gap-1">
                        <div class="w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center text-[10px] font-bold">+</div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">18</span>
                    </div>
                </div>
                
                <div class="py-4 px-2 bg-gray-50 rounded-xl flex flex-col items-center justify-center border border-gray-200 dark:bg-slate-900 dark:border-slate-800">
                    <div class="text-xs text-gray-500 mb-2 dark:text-gray-400">{{ $isAr ? 'الطلبات' : 'Orders' }}</div>
                    <div class="flex items-center gap-1">
                        <i data-lucide="box" class="w-5 h-5 text-rose-500"></i>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ (int) $ordersCount }}</span>
                    </div>
                </div>

                <div class="py-4 px-2 bg-gray-50 rounded-xl flex flex-col items-center justify-center border border-gray-200 dark:bg-slate-900 dark:border-slate-800">
                    <div class="text-xs text-gray-500 mb-2 dark:text-gray-400">{{ $isAr ? 'التقييمات' : 'Ratings' }}</div>
                    <div class="flex items-center gap-0.5 mb-1">
                        @for($i = 1; $i <= 5; $i++)
                            <i data-lucide="star" class="w-4 h-4 {{ $avgRatingVal >= $i ? 'fill-rose-500 text-rose-500' : 'text-gray-300 dark:text-gray-600' }}"></i>
                        @endfor
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($avgRatingVal, 1) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Tiers Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg md:text-xl font-bold text-gray-800 dark:text-white mb-3">{{ $isAr ? 'مستويات الأسعار' : 'Pricing Tiers' }}</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($sortedSuppliers as $supplier)
                    @php($supplierTiers = ($product->pricingTiers ?? collect())->where('supplier_id', $supplier->id)->sortBy('min_quantity'))
                    @if($supplierTiers->isNotEmpty())
                        @foreach($supplierTiers as $tier)
                            <div class="bg-gray-50 border border-gray-200 shadow-sm rounded-2xl p-6 dark:bg-slate-900 dark:border-slate-800 dark:shadow-lg relative overflow-hidden group transition-all hover:border-gray-300 dark:hover:border-slate-700">
                                <div class="flex items-center justify-between gap-4 mb-4">
                                    <div class="text-gray-600 font-medium text-base dark:text-slate-400">
                                        {{ $supplier->name }}
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-900 font-bold text-xl dark:text-white">{{ \App\Helpers\CurrencyHelper::format($tier->price_per_unit) }}</span>
                                    </div>
                                </div>
                                <div class="w-full h-[1px] bg-gray-200 dark:bg-slate-800/80 mb-4"></div>
                                <div class="text-center">
                                    <div class="text-gray-900 font-bold text-2xl tracking-wide dark:text-white">
                                        {{ $tier->min_quantity }}
                                        @if($tier->max_quantity) - {{ $tier->max_quantity }} @else + @endif
                                        <span class="text-base font-normal text-gray-500 ml-1 dark:text-slate-400">{{ $isAr ? 'قطعة' : 'units' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="bg-gray-50 border border-gray-200 shadow-sm rounded-2xl p-6 dark:bg-slate-900 dark:border-slate-800 dark:shadow-lg relative overflow-hidden group transition-all hover:border-gray-300 dark:hover:border-slate-700">
                            <div class="flex items-center justify-between gap-4 mb-4">
                                <div class="text-gray-600 font-medium text-base dark:text-slate-400">
                                    {{ $supplier->name }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-900 font-bold text-xl dark:text-white">{{ \App\Helpers\CurrencyHelper::format($supplier->pivot->price ?? 0) }}</span>
                                </div>
                            </div>
                            <div class="w-full h-[1px] bg-gray-200 dark:bg-slate-800/80 mb-4"></div>
                            <div class="text-center">
                                <div class="text-gray-900 font-bold text-2xl tracking-wide dark:text-white">
                                    1 <span class="text-base font-normal text-gray-500 ml-1 dark:text-slate-400">{{ $isAr ? 'قطعة' : 'unit' }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-span-3 text-center text-gray-500 py-4">
                        {{ $isAr ? 'لا توجد أسعار متاحة حالياً.' : 'No pricing tiers available.' }}
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Product Gallery -->
        <div class="mb-6 mt-7">
            <h2 class="text-lg md:text-xl font-bold text-gray-800 dark:text-white mb-3">{{ $isAr ? 'معرض الصور' : 'Product Gallery' }}</h2>
            
            @php($galleryItems = ($gallery->isNotEmpty() ? $gallery : collect([$imageUrl]))->values())
            <!-- Take first 5 or fill -->
            <div class="grid grid-cols-5 gap-2 md:gap-4">
                @foreach($galleryItems->take(5) as $g)
                    <div class="aspect-square rounded-xl overflow-hidden bg-gray-100 border border-gray-200 dark:bg-slate-800 dark:border-slate-700">
                        <img src="{{ $g }}" alt="gallery" class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition-opacity" onclick="window.open('{{ $g }}', '_blank')">
                    </div>
                @endforeach
                @for($i = $galleryItems->count(); $i < 5; $i++)
                    <div class="aspect-square rounded-xl bg-gray-100 border border-gray-200 dark:bg-slate-800 dark:border-slate-700"></div>
                @endfor
            </div>

            <!-- Social Stats & Actions -->
            <div class="mt-4">
                <!-- Stats Row -->
                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-3 px-1">
                    <div class="flex items-center gap-3">
                         <span><span id="shares-count" class="font-medium text-gray-700 dark:text-slate-300">{{ (int) ($sharesCount ?? 0) }}</span> {{ $isAr ? 'مشاركات' : 'Shares' }}</span>
                         <button type="button" id="comments-stat-btn" class="inline-flex items-center gap-1 text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white cursor-pointer">
                            <span id="comments-count" class="font-medium text-gray-700 dark:text-slate-300">{{ (int) ($commentsCount ?? 0) }}</span>
                            <span>{{ $isAr ? 'تعليقات' : 'comments' }}</span>
                         </button>
                    </div>
                    <div class="flex items-center gap-1">
                        <span id="likes-count" class="font-medium text-gray-700 dark:text-slate-300">{{ (int) ($likesCount ?? 0) }}</span>
                        <span>{{ !empty($userReaction) ? '❤️' : '👍' }}</span>
                    </div>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-200 dark:border-slate-800 mb-3"></div>

                <!-- Actions Row -->
                <div class="flex items-center justify-between">
                    <button type="button" id="share-btn" class="flex-1 flex items-center justify-center gap-2 py-2 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                        <i data-lucide="share-2" class="w-5 h-5"></i>
                        <span class="font-medium text-sm">{{ $isAr ? 'مشاركة' : 'Share' }}</span>
                    </button>
                    
                    <button type="button" id="comments-btn" class="flex-1 flex items-center justify-center gap-2 py-2 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                        <i data-lucide="message-circle" class="w-5 h-5"></i>
                        <span class="font-medium text-sm">{{ $isAr ? 'تعليق' : 'Comment' }}</span>
                    </button>

                    <div class="flex-1 relative reaction-container">
                        <div id="reaction-picker" class="reaction-picker hidden">
                            <button type="button" class="reaction-btn" data-reaction="like" title="Like">👍</button>
                            <button type="button" class="reaction-btn" data-reaction="love" title="Love">❤️</button>
                            <button type="button" class="reaction-btn" data-reaction="haha" title="Haha">😂</button>
                            <button type="button" class="reaction-btn" data-reaction="wow" title="Wow">😮</button>
                            <button type="button" class="reaction-btn" data-reaction="sad" title="Sad">😢</button>
                            <button type="button" class="reaction-btn" data-reaction="angry" title="Angry">😡</button>
                        </div>
                        <button type="button" id="like-btn" class="w-full flex items-center justify-center gap-2 py-2 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer {{ !empty($userReaction) ? 'text-blue-600 dark:text-blue-500' : '' }}">
                            <i data-lucide="thumbs-up" class="w-5 h-5 {{ !empty($userReaction) ? 'fill-current' : '' }}"></i>
                            <span class="font-medium text-sm">{{ !empty($userReaction) ? $userReaction : ($isAr ? 'أعجبني' : 'Like') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Options & Order Section (Matching Image 4) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 mt-16">
        <div class="rounded-3xl p-6 mb-8 relative border border-gray-200 bg-gray-50 dark:border-slate-800 dark:bg-slate-900 shadow-sm">
            <!-- Available Colors -->
            <div class="mb-6">
                <div class="text-sm font-bold text-gray-900 mb-3 dark:text-white">{{ $isAr ? 'الألوان المتاحة' : 'Available Colors' }}</div>
                <div class="flex flex-wrap gap-2">
                    @php($colors = collect((array)($product->colors ?? []))->filter())
                    @if($colors->isNotEmpty())
                        @foreach($colors as $color)
                             <div class="px-4 py-2 rounded-xl bg-white border border-gray-200 text-xs font-semibold text-gray-700 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300">
                                {{ $color }}
                             </div>
                        @endforeach
                    @else
                        <span class="text-xs text-gray-500 font-medium">{{ $isAr ? 'غير محدد' : 'Not specified' }}</span>
                    @endif
                </div>
            </div>

            <!-- Available Sizes -->
            <div class="mb-6">
                <div class="text-sm font-bold text-gray-900 mb-3 dark:text-white">{{ $isAr ? 'المقاسات المتاحة' : 'Available Sizes' }}</div>
                <div class="flex flex-wrap gap-2">
                    @php($sizes = collect((array)($product->sizes ?? []))->filter())
                    @if($sizes->isNotEmpty())
                        @foreach($sizes as $size)
                             <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-700 uppercase dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300">
                                {{ $size }}
                             </div>
                        @endforeach
                    @else
                        <span class="text-xs text-gray-500 font-medium">{{ $isAr ? 'غير محدد' : 'Not specified' }}</span>
                    @endif
                </div>
            </div>

            <!-- Quantity Info -->
            <div class="mb-4">
                 <div class="text-sm font-bold text-gray-900 mb-2 dark:text-white">{{ $isAr ? 'الكمية' : 'Quantity' }}</div>
                 <div class="flex items-center gap-4">
                    <div class="text-xs text-gray-500 font-medium dark:text-slate-400">{{ $isAr ? 'أقل كمية: 1' : 'Min. Order: 1' }}</div>
                    <div class="text-xs text-rose-500 font-bold">{{ $isAr ? 'في المخزون: 5,000' : 'In Stock: 5,000' }}</div>
                 </div>
            </div>

            <div class="mt-6">
                @auth
                    <button type="button" onclick="openProductPageOrderModal(document.getElementById('current-product-card'))" 
                            class="w-full py-4 rounded-xl font-bold text-white text-lg bg-gradient-to-r from-rose-500 to-orange-400 shadow-xl hover:shadow-2xl hover:opacity-95 transition-all transform hover:-translate-y-1">
                        {{ $isAr ? 'اطلب الآن' : 'Order Now' }}
                    </button>
                @else
                   <a href="{{ route('login') }}" class="block text-center w-full py-4 rounded-xl font-bold text-white text-lg bg-gradient-to-r from-gray-600 to-gray-500 shadow-lg hover:opacity-90">
                       {{ $isAr ? 'سجل الدخول للطلب' : 'Login to Order' }}
                   </a>
                @endauth
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 mt-16">

        <!-- Factory Details (Expandable) -->
        @if(($factories ?? collect())->isNotEmpty())
            <div class="bg-gray-50 rounded-3xl p-6 border border-gray-100 mb-8 dark:bg-slate-900 dark:border-slate-800">
                <div class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-200 pb-2 dark:text-white dark:border-slate-800">{{ $isAr ? 'تفاصيل المصنع' : 'Factory Details' }}</div>
                
                @foreach($factories as $idx => $f)
                     <div class="text-base font-bold text-gray-900 mb-1 dark:text-white">{{ $f->name ?? ($isAr ? 'مصنع' : 'Factory') }}</div>
                     <div class="text-sm text-gray-600 leading-relaxed mb-4 dark:text-slate-400">
                         {{ $f->factory_short_details ?? ($isAr ? 'لا توجد تفاصيل مختصرة.' : 'No short details available.') }}
                     </div>
                     
                     <div id="factory-more-{{ $idx }}" class="expandable-content hidden text-sm text-gray-600 leading-relaxed mb-4 border-t border-gray-200 pt-3 dark:text-slate-400 dark:border-slate-800">
                         {{ $f->factory_long_details ?? ($isAr ? 'لا توجد تفاصيل إضافية.' : 'No additional details available.') }}
                         <div class="mt-2 font-semibold text-rose-400">{{ $f->name }}</div>
                     </div>
                     
                     <button type="button" class="flex items-center gap-1 text-rose-500 text-sm font-semibold hover:text-rose-400 transition-colors w-full justify-center py-2"
                             onclick="const el = document.getElementById('factory-more-{{ $idx }}'); el.classList.toggle('expanded'); if(!el.classList.contains('expanded')) { this.innerHTML = '{{ $isAr ? 'المزيد من المعلومات' : 'More info' }} <i data-lucide=\'chevron-down\' class=\'w-4 h-4 ml-1\'></i>'; } else { this.innerHTML = '{{ $isAr ? 'أقل' : 'Less info' }} <i data-lucide=\'chevron-up\' class=\'w-4 h-4 ml-1\'></i>'; lucide.createIcons(); }">
                         {{ $isAr ? 'المزيد من المعلومات' : 'More info' }}
                         <i data-lucide="chevron-down" class="w-4 h-4"></i>
                     </button>
                @endforeach
            </div>
        @endif

    </div>

    <!-- Rate this product section (Now theme-aware) -->
    <div id="interactive-rating-section" class="mt-10 mb-12 max-w-sm rounded-3xl border border-gray-200 bg-white p-6 shadow-xl relative overflow-hidden dark:bg-slate-900/95 dark:border-slate-800">
        <div class="relative z-10">
            <h3 class="text-xl font-bold text-gray-900 mb-1 dark:text-white">{{ $isAr ? 'قيم هذا المنتج' : 'Rate this product' }}</h3>
            <p class="text-gray-500 text-xs mb-5 dark:text-slate-400">{{ $isAr ? 'شاركنا رأيك وساعد الآخرين في الاختيار' : 'Share your opinion and help others choose the right product' }}</p>
            
            <div id="interactive-stars" class="flex items-center gap-2">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button" class="rating-action-btn transition-all duration-200 hover:scale-110 focus:outline-none" data-value="{{ $i }}">
                        <i data-lucide="star" class="w-8 h-8 {{ $userRatingVal >= $i ? 'fill-yellow-400 text-yellow-400' : 'text-gray-300 dark:text-slate-700' }}"></i>
                    </button>
                @endfor
            </div>
            <div id="rating-status-msg" class="mt-3 text-xs font-semibold h-4 transition-all duration-300"></div>
        </div>
    </div>

        <div class="p-5 md:p-6">
            <div class="flex items-center justify-between">
                <h2 class="mb-4 text-2xl font-bold text-gray-800 dark:text-white">{{ $isAr ? 'منتجات مشابهة' : 'Similar Products' }}</h2>
            </div>
            <div class="mt-4">
                <div class="swiper" id="similar-swiper">
                    <div class="swiper-wrapper">
                        @forelse(($similarProducts ?? []) as $sp)
                            @php($spFactory = ($sp->suppliers ?? collect())->firstWhere('type', 'factory'))
                            @php($spSupplier = ($sp->suppliers ?? collect())->firstWhere('type', 'vendor'))
                            @php($spFactoryPrice = (float) ($spFactory->pivot->price ?? 0))
                            @php($spSupplierPrice = (float) ($spSupplier->pivot->price ?? 0))
                            @php($spFactoryUsd = (int) round($spFactoryPrice / max(1, $usdRate ?? 1)))
                            @php($spSupplierUsd = (int) round($spSupplierPrice / max(1, $usdRate ?? 1)))
                            @php($spFactoryPriceK = $spFactoryPrice >= 1000 ? (string) round($spFactoryPrice / 1000) . 'k' : number_format($spFactoryPrice, 0, '.', ','))
                            @php($spSupplierPriceK = $spSupplierPrice >= 1000 ? (string) round($spSupplierPrice / 1000) . 'k' : number_format($spSupplierPrice, 0, '.', ','))
                            @php($spSuppliersForModal = ($sp->suppliers ?? collect())->map(fn($s) => [
                                'id' => (int) $s->id,
                                'name' => (string) ($s->name ?? ''),
                                'type' => (string) ($s->type ?? ''),
                                'price' => (float) ($s->pivot->price ?? 0),
                                'unit_price' => (float) ($s->pivot->unit_price ?? 0),
                                'quantity' => $s->pivot->quantity !== null ? (int) $s->pivot->quantity : null,
                            ])->values())
                            @php($spPricingTiers = ($sp->pricingTiers ?? collect())
                                ->groupBy('supplier_id')
                                ->map(fn($g) => $g->map(fn($t) => [
                                    'min' => (int) $t->min_quantity,
                                    'max' => $t->max_quantity ? (int) $t->max_quantity : null,
                                    'price' => (float) $t->price_per_unit,
                                ])->values())
                                ->toArray())
                            <div class="swiper-slide">
                                <div class="p-4 rounded-2xl bg-white border border-gray-200 shadow-sm hover:cursor-pointer dark:bg-slate-900 dark:border-slate-800" data-shop-product
                                    data-product-id="{{ $sp->id }}"
                                    data-name-en="{{ $sp->name_en ?? $sp->name }}"
                                    data-name-ar="{{ $sp->name_ar ?? $sp->name }}"
                                    data-image="{{ !empty($sp->image) ? asset('storage/' . $sp->image) : asset('apple-touch-icon.png') }}"
                                    data-description="{{ $sp->description ?? '' }}"
                                    data-description-ar="{{ $sp->description_ar ?? '' }}"
                                    data-description-en="{{ $sp->description_en ?? '' }}"
                                    data-color="{{ $sp->color ?? '' }}"
                                    data-size="{{ $sp->size ?? '' }}"
                                    data-colors="{{ collect((array) ($sp->colors ?? []))->filter(fn($v) => trim((string) $v) !== '')->implode(',') }}"
                                    data-sizes="{{ collect((array) ($sp->sizes ?? []))->filter(fn($v) => trim((string) $v) !== '')->implode(',') }}"
                                    data-suppliers='@json($spSuppliersForModal)'
                                    data-factory-id="{{ (int) ($spFactory->id ?? 0) ?: '' }}"
                                    data-supplier-id="{{ (int) ($spSupplier->id ?? 0) ?: '' }}"
                                    data-factory-name="{{ $spFactory->name ?? '' }}"
                                    data-factory-price="{{ (string) ($spFactory->pivot->price ?? '') }}"
                                    data-factory-price="{{ (string) ($spFactory->pivot->price ?? '') }}"
                                    data-supplier-name="{{ $spSupplier->name ?? '' }}"
                                    data-supplier-price="{{ (string) ($spSupplier->pivot->price ?? '') }}"
                                    data-pricing-tiers='@json($spPricingTiers)'>
                                    <div class="w-full h-28 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 dark:bg-slate-800/60 dark:border-slate-700">
                                        <img src="{{ !empty($sp->image) ? asset('storage/' . $sp->image) : asset('apple-touch-icon.png') }}" alt="product" class="w-full h-full object-cover" />
                                    </div>
                                    <div class="mt-3 text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $isAr ? ($sp->name_ar ?? $sp->name) : ($sp->name_en ?? $sp->name) }}</div>
                                    <div class="mt-3 space-y-2 text-xs text-gray-700 dark:text-slate-200">
                                        <div class="flex items-start justify-between">
                                            <div class="min-w-0">
                                                <div class="text-gray-500 dark:text-slate-400">{{ $isAr ? 'المصنع:' : 'Factory:' }}</div>
                                                <div class="text-gray-900 dark:text-white">{{ \App\Helpers\CurrencyHelper::format($spFactoryPrice) }}</div>
                                            </div>
                                        </div>
                                        <div class="flex items-start justify-between">
                                            <div class="min-w-0">
                                                <div class="text-gray-500 dark:text-slate-400">{{ $isAr ? 'المورد:' : 'Supplier:' }}</div>
                                                <div class="text-gray-900 dark:text-white">{{ \App\Helpers\CurrencyHelper::format($spSupplierPrice) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="swiper-slide">
                                <div class="text-sm text-gray-600 dark:text-slate-300">{{ $isAr ? 'لا توجد منتجات مشابهة.' : 'No similar products.' }}</div>
                            </div>
                        @endforelse
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            </div>
        </div>
    </div>

   

    </div>

    @php($pricingTiersForModal = ($product->pricingTiers ?? collect())
        ->groupBy('supplier_id')
        ->map(fn($g) => $g->map(fn($t) => [
            'min' => (int) $t->min_quantity,
            'max' => $t->max_quantity ? (int) $t->max_quantity : null,
            'price' => (float) $t->price_per_unit,
        ])->values())
        ->toArray())

    <div id="current-product-card" class="hidden" data-shop-product
         data-product-id="{{ (int) $product->id }}"
         data-modal-title="{{ $isAr ? 'اختر خياراتك' : 'Select Your Options' }}"
         data-modal-mode="sheet"
         data-name-en="{{ $product->name_en ?? $product->name }}"
         data-name-ar="{{ $product->name_ar ?? $product->name }}"
         data-image="{{ $imageUrl }}"
         data-description="{{ $product->description ?? '' }}"
         data-description-ar="{{ $product->description_ar ?? '' }}"
         data-description-en="{{ $product->description_en ?? '' }}"
         data-color="{{ $product->color ?? '' }}"
         data-size="{{ $product->size ?? '' }}"
         data-colors="{{ collect((array) ($product->colors ?? []))->filter(fn($v) => trim((string) $v) !== '')->implode(',') }}"
         data-sizes="{{ collect((array) ($product->sizes ?? []))->filter(fn($v) => trim((string) $v) !== '')->implode(',') }}"
         data-suppliers='@json($suppliersForModal)'
         data-pricing-tiers='@json($pricingTiersForModal)'
         data-factory-id="{{ (int) ($factory->id ?? 0) ?: '' }}"
         data-factory-name="{{ $factory->name ?? '' }}"
         data-factory-price="{{ (string) ($factory->pivot->price ?? '') }}">
    </div>

    {{-- مودال الطلب داخل صفحة المنتج – التحكم في الموضع عبر CSS class: .product-order-modal-dialog --}}
    <div id="product-order-overlay" class="hidden fixed inset-0 bg-black/40 z-50" aria-hidden="true"></div>
    <div id="product-order-modal" class="product-order-modal-dialog hidden bg-white shadow-2xl overflow-hidden flex flex-col dark:bg-slate-900" role="dialog" aria-labelledby="product-order-title" aria-modal="true">
        <div class="relative p-4 border-b border-gray-100 flex items-center flex-shrink-0 dark:border-slate-800">
            <h2 id="product-order-title" class="text-lg font-bold text-gray-900 w-full {{ $isAr ? 'text-right' : 'text-left' }} dark:text-white">{{ $isAr ? 'اختر خياراتك' : 'Select Your Options' }}</h2>
            <button type="button" id="product-order-close" class="absolute top-1/2 -translate-y-1/2 {{ $isAr ? 'left-4' : 'right-4' }}" aria-label="{{ $isAr ? 'إغلاق' : 'Close' }}">
                <i data-lucide="x" class="w-6 h-6 text-gray-400 dark:text-slate-500"></i>
            </button>
        </div>
        <form id="product-order-form" method="POST" action="{{ route('shop.cart.store') }}" class="flex-1 overflow-y-auto p-4 space-y-4">
            @csrf
            <input type="hidden" name="product_id" id="product-order-product-id" value="{{ (int) $product->id }}" />
            <input type="hidden" name="supplier_id" id="product-order-supplier-id" value="" />
            <input type="hidden" name="quantity" id="product-order-quantity-hidden" value="1" />

            <div>
                <label for="product-order-supplier" class="text-xs text-gray-500 font-medium dark:text-slate-400">{{ $isAr ? 'اختر المورد' : 'Choose supplier' }}</label>
                <select id="product-order-supplier" class="mt-2 w-full h-11 rounded-2xl bg-gray-50 text-gray-900 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 px-3 transition-all" required>
                    <option value="">-</option>
                </select>
            </div>

            <!-- Pricing Levels (Image 3) -->
            <div id="product-order-tiers-section" class="hidden">
                <label class="text-sm font-semibold text-gray-700 mb-2 block dark:text-white">{{ $isAr ? 'مستويات أسعار الجملة:' : 'Wholesale Pricing Levels:' }}</label>
                <div id="product-order-tiers-list" class="space-y-2">
                    <!-- Tiers will be injected here -->
                </div>
            </div>

            <div class="w-full h-28 md:h-32 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 dark:bg-slate-800 dark:border-slate-700">
                <img id="product-order-image" src="" alt="" class="w-full h-full object-cover" />
            </div>

            <div>
                <div id="product-order-name" class="font-bold text-gray-900 dark:text-white text-base"></div>
                <div class="flex items-center justify-between mt-2 text-sm">
                    <span id="product-order-unit" class="text-gray-500 font-medium dark:text-slate-400"></span>
                    <span id="product-order-total" class="font-bold text-rose-600 dark:text-rose-500"></span>
                </div>
            </div>

            <div>
                <label for="product-order-quantity" class="text-sm font-semibold text-gray-700 mb-2 block dark:text-white">{{ $isAr ? 'الكمية' : 'Quantity' }}</label>
                <div class="flex items-center gap-3">
                    <div class="flex-1 flex items-center justify-between bg-gray-50 rounded-2xl px-4 border border-gray-100 h-14 dark:bg-slate-800 dark:border-slate-700">
                        <p class="text-xs text-gray-400 dark:text-slate-500 font-medium">{{ $isAr ? 'أقل كمية: 1' : 'Min: 1' }}</p>
                        <input id="product-order-quantity" type="number" min="1" value="1" class="w-20 bg-transparent text-center font-bold text-gray-900 focus:outline-none dark:text-white" />
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="p-4 border-t dark:border-slate-800 flex gap-3">
                <button type="submit" class="flex-1 py-3 text-lg font-bold text-white bg-gradient-to-r from-rose-600 to-rose-500 rounded-xl shadow-md hover:opacity-90">
                    {{ $isAr ? 'أضف للسلة' : 'Add to Cart' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Comments Overlay -->
    <div id="comments-overlay" class="hidden fixed inset-0 z-[9999] bg-black/60 flex items-center justify-center p-4">
        <div id="comments-dialog" class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-2xl shadow-2xl flex flex-col max-h-[80vh] overflow-hidden">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-slate-900 z-10">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $isAr ? 'التعليقات' : 'Comments' }}</h3>
                <button type="button" id="comments-close" class="text-gray-500 hover:text-gray-700 dark:text-slate-400 dark:hover:text-white cursor-pointer">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <!-- Body: List -->
            <div id="comments-list-outer" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-slate-950/30">
                 @if(isset($product->comments) && $product->comments->count() > 0)
                    @foreach($product->comments as $comment)
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 uppercase dark:bg-slate-800 dark:text-slate-200">
                                {{ substr($comment->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="flex-1">
                                 <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-2xl px-3 py-2 shadow-sm">
                                     <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $comment->user->name ?? 'User' }}</div>
                                     <div class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $comment->body }}</div>
                                 </div>
                                 <div class="text-xs text-gray-500 mt-1 ml-2">{{ $comment->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endforeach
                 @else
                    <div class="text-center text-gray-500 py-10">{{ $isAr ? 'لا توجد تعليقات بعد' : 'No comments yet' }}</div>
                 @endif
            </div>
            <!-- Footer: Form -->
            <form id="comment-form-outer" class="p-4 border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 z-10">
                <div class="flex gap-2">
                    <input type="text" id="comment-body-outer" placeholder="{{ $isAr ? 'اكتب تعليقاً...' : 'Write a comment...' }}" class="flex-1 rounded-full bg-gray-100 dark:bg-slate-800 border-none px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white" required>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-2 disabled:opacity-50 transition-colors">
                        <i data-lucide="send" class="w-5 h-5"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const productId = {{ (int) $product->id }};
        const routes = {
            like: @json(route('shop.products.like', $product)),
            comment: @json(route('shop.products.comment', $product)),
            share: @json(route('shop.products.share', $product)),
            rating: @json(route('shop.products.rating', $product)),
        };

        const suppliersForPrices = @json($suppliersForModal);
        const pricingTiersBySupplier = @json($pricingTiersForModal);

        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        function looksLikeJsonResponse(res) {
            const ct = res.headers.get('content-type') || '';
            return ct.includes('application/json');
        }

        function reactionEmoji(r) {
            const map = { like: '👍', love: '❤️', haha: '😂', wow: '😮', sad: '😢', angry: '😡' };
            return map[r] || '👍';
        }

        function reactionLabel(r) {
            const mapEn = { like: 'Like', love: 'Love', haha: 'Haha', wow: 'Wow', sad: 'Sad', angry: 'Angry' };
            const mapAr = { like: 'إعجاب', love: 'حب', haha: 'ضحك', wow: 'واو', sad: 'حزين', angry: 'غاضب' };
            const m = @json($isAr) ? mapAr : mapEn;
            const label = m[r] || (@json($isAr) ? 'إعجاب' : 'Like');
            return label;
        }

        function setLikeButtonUI(userReaction) {
            const btn = document.getElementById('like-btn');
            if (!btn) return;
            const icon = btn.querySelector('svg') || btn.querySelector('i');
            const label = btn.querySelector('span');
            
            const isLiked = !!userReaction;
            const r = userReaction || 'like';

            if (isLiked) {
                // Show ONLY the emoji and label
                btn.classList.add('text-blue-600', 'dark:text-blue-500');
                btn.classList.remove('text-gray-600', 'dark:text-slate-300');
                if (icon) icon.classList.add('hidden'); // Hide the thumbs-up icon
                
                if (label) {
                    label.innerHTML = `<span class="text-xl mr-2">${reactionEmoji(r)}</span> ${reactionLabel(r)}`;
                }
            } else {
                btn.classList.remove('text-blue-600', 'dark:text-blue-500');
                btn.classList.add('text-gray-600', 'dark:text-slate-300');
                if (icon) {
                    icon.classList.remove('hidden', 'fill-current', 'text-blue-600', 'dark:text-blue-500');
                }
                if (label) {
                    label.textContent = @json($isAr ? 'أعجبني' : 'Like');
                }
            }
        }

        function renderReactionsSummary(topReactions) {
            const wrap = document.getElementById('reactions-summary');
            if (!wrap) return;
            wrap.innerHTML = '';
            (topReactions || []).slice(0, 3).forEach((r) => {
                const span = document.createElement('span');
                span.className = 'inline-flex items-center justify-center w-5 h-5 rounded-full bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-[11px]';
                span.title = r;
                span.textContent = reactionEmoji(r);
                wrap.appendChild(span);
            });
        }

        function showMiniToast(message) {
            const existing = document.getElementById('shop-mini-toast');
            if (existing) existing.remove();
            const toast = document.createElement('div');
            toast.id = 'shop-mini-toast';
            toast.className = 'fixed top-5 right-5 z-[9999] bg-gray-900 text-white px-4 py-3 rounded-xl shadow-lg text-sm';
            toast.textContent = message || '';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2200);
        }

        function showMiniError(message) {
            const existing = document.getElementById('shop-mini-toast');
            if (existing) existing.remove();
            const toast = document.createElement('div');
            toast.id = 'shop-mini-toast';
            toast.className = 'fixed top-5 right-5 z-[9999] bg-rose-600 text-white px-4 py-3 rounded-xl shadow-lg text-sm';
            toast.textContent = message || '';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3200);
        }

        function openComments() {
            const overlay = document.getElementById('comments-overlay');
            if (overlay) overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeComments() {
            const overlay = document.getElementById('comments-overlay');
            if (overlay) overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function openProductPageOrderModal(cardEl) {
            const overlay = document.getElementById('product-order-overlay');
            const modal = document.getElementById('product-order-modal');
            if (!overlay || !modal || !cardEl) return;

            const isRtl = document.documentElement.dir === 'rtl';
            const name = isRtl ? (cardEl.dataset.nameAr || cardEl.dataset.nameEn || '') : (cardEl.dataset.nameEn || cardEl.dataset.nameAr || '');
            const image = cardEl.dataset.image || '';
            const productId = cardEl.dataset.productId || '';
            const suppliersRaw = cardEl.dataset.suppliers || '';
            const pricingTiersRaw = cardEl.dataset.pricingTiers || '';
            const factoryId = cardEl.dataset.factoryId || '';
            const supplierId = cardEl.dataset.supplierId || '';
            let suppliers = [];
            try { if (suppliersRaw) suppliers = JSON.parse(suppliersRaw) || []; } catch (e) { suppliers = []; }
            let pricingTiersBySupplier = {};
            try { if (pricingTiersRaw) pricingTiersBySupplier = JSON.parse(pricingTiersRaw) || {}; } catch (e) { pricingTiersBySupplier = {}; }

            const findTierPrice = function (sid, qty) {
                const q = Math.max(1, Number(qty || 1) || 1);
                const idStr = sid !== null && typeof sid !== 'undefined' ? String(sid) : '';
                const list = pricingTiersBySupplier && pricingTiersBySupplier[idStr] ? pricingTiersBySupplier[idStr] : [];
                const tiers = Array.isArray(list) ? list : [];
                for (let i = 0; i < tiers.length; i++) {
                    const t = tiers[i] || {};
                    const min = Number(t.min ?? t.min_quantity ?? 0) || 0;
                    const maxVal = t.max ?? t.max_quantity;
                    const max = (maxVal === null || maxVal === undefined || maxVal === '') ? null : (Number(maxVal) || null);
                    if (min > 0 && q >= min && (max === null || q <= max)) {
                        const p = Number(t.price ?? t.price_per_unit ?? 0);
                        return Number.isFinite(p) && p >= 0 ? p : null;
                    }
                }
                return null;
            };

            const s_formatMoney = (n) => {
                if (typeof window.formatMoney === 'function') return window.formatMoney(n);
                const exRate = window.exchangeRate || 1;
                const converted = Number(n || 0) / exRate;
                return converted.toLocaleString(undefined, { 
                    maximumFractionDigits: converted < 1 ? 2 : 0,
                    minimumFractionDigits: converted < 1 ? 2 : 0
                });
            };
            const s_currencySymbol = window.currencySymbol || 'EGP';

            const productOrderTitle = document.getElementById('product-order-title');
            if (productOrderTitle) productOrderTitle.textContent = cardEl.dataset.modalTitle || (isRtl ? 'اختر خياراتك' : 'Select Your Options');

            const productOrderProductId = document.getElementById('product-order-product-id');
            const productOrderSupplierId = document.getElementById('product-order-supplier-id');
            const productOrderSupplier = document.getElementById('product-order-supplier');
            const productOrderQuantityHidden = document.getElementById('product-order-quantity-hidden');
            const productOrderQuantity = document.getElementById('product-order-quantity');
            const productOrderImage = document.getElementById('product-order-image');
            const productOrderName = document.getElementById('product-order-name');
            const productOrderUnit = document.getElementById('product-order-unit');
            const productOrderTotal = document.getElementById('product-order-total');

            if (productOrderProductId) productOrderProductId.value = String(productId || '');

            if (productOrderSupplier) {
                productOrderSupplier.innerHTML = '<option value="">-</option>';
                suppliers.forEach((s) => {
                    const o = document.createElement('option');
                    o.value = String(s?.id ?? '');
                    const t = (s?.type || '') === 'factory' ? (isRtl ? 'مصنع' : 'Factory') : (isRtl ? 'مورد' : 'Supplier');
                    const basePrice = Number(s?.unit_price ?? s?.price ?? 0);
                    const convertedBase = basePrice / (window.exchangeRate || 1);
                    const priceLabel = Number.isFinite(convertedBase) ? convertedBase.toLocaleString(undefined, { maximumFractionDigits: 0 }) + ' ' + s_currencySymbol : '';
                    o.textContent = t + ' - ' + (s?.name ?? '') + (priceLabel ? ' - ' + priceLabel : '');
                    productOrderSupplier.appendChild(o);
                });
                const preferred = (supplierId || factoryId) ? String(supplierId || factoryId) : (suppliers[0] ? String(suppliers[0].id ?? '') : '');
                productOrderSupplier.value = preferred;
            }

            if (productOrderImage) { productOrderImage.src = image; productOrderImage.alt = name; }
            if (productOrderName) productOrderName.textContent = name;
            if (productOrderQuantity) productOrderQuantity.value = '1';
            if (productOrderQuantityHidden) productOrderQuantityHidden.value = '1';

            function updateProductOrderTotals() {
                const sid = productOrderSupplier?.value || '';
                const qty = Math.max(1, Number(productOrderQuantity?.value || 1) || 1);
                const supplier = suppliers.find(s => String(s?.id ?? '') === sid) || null;
                const tiersSection = document.getElementById('product-order-tiers-section');
                const tiersList = document.getElementById('product-order-tiers-list');

                if (!supplier) {
                    if (productOrderUnit) productOrderUnit.textContent = '';
                    if (productOrderTotal) productOrderTotal.textContent = '';
                    if (productOrderSupplierId) productOrderSupplierId.value = '';
                    if (tiersSection) tiersSection.classList.add('hidden');
                    return;
                }

                // Render Tiers in Modal (Image 3)
                const idStr = String(supplier.id);
                const tiers = pricingTiersBySupplier && pricingTiersBySupplier[idStr] ? pricingTiersBySupplier[idStr] : [];
                if (tiers.length > 0 && tiersSection && tiersList) {
                    tiersSection.classList.remove('hidden');
                    tiersList.innerHTML = '';
                    tiers.forEach(t => {
                        const min = t.min ?? 0;
                        const max = t.max;
                        const p = t.price ?? 0;
                        const row = document.createElement('div');
                        row.className = 'flex items-center justify-between p-2 rounded-lg border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/50';
                        if (qty >= min && (!max || qty <= max)) {
                            row.classList.add('ring-1', 'ring-rose-500', 'border-rose-500');
                        }
                        
                        const qtyText = max ? `${min}-${max}` : `${min}+`;
                    row.innerHTML = `
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">${qtyText} {{ $isAr ? 'قطعة' : 'units' }}:</span>
                        <span class="text-sm font-bold text-rose-500">${s_formatMoney(p)} ${s_currencySymbol}</span>
                    `;
                    tiersList.appendChild(row);
                });
            } else if (tiersSection) {
                tiersSection.classList.add('hidden');
            }

            const basePrice = Number(supplier?.unit_price ?? supplier?.price ?? 0);
            const tierPrice = findTierPrice(supplier?.id ?? null, qty);
            const unitPrice = tierPrice !== null ? tierPrice : (Number.isFinite(basePrice) ? basePrice : 0);
            const total = unitPrice * qty;
            if (productOrderUnit) productOrderUnit.textContent = (isRtl ? 'سعر القطعة: ' : 'Unit: ') + s_formatMoney(unitPrice) + ' ' + s_currencySymbol;
            if (productOrderTotal) productOrderTotal.textContent = (isRtl ? 'الإجمالي: ' : 'Total: ') + s_formatMoney(total) + ' ' + s_currencySymbol;
                if (productOrderSupplierId) productOrderSupplierId.value = String(supplier.id ?? '');
                if (productOrderQuantityHidden) productOrderQuantityHidden.value = String(qty);
            }

            updateProductOrderTotals();
            if (productOrderSupplier) productOrderSupplier.onchange = updateProductOrderTotals;
            if (productOrderQuantity) productOrderQuantity.oninput = updateProductOrderTotals;

            overlay.classList.remove('hidden');
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            requestAnimationFrame(function () {
                overlay.classList.add('product-order-overlay-visible');
                modal.classList.add('product-order-modal-visible');
            });
            if (typeof lucide !== 'undefined' && lucide.createIcons) lucide.createIcons();
        }

        function closeProductPageOrderModal() {
            const overlay = document.getElementById('product-order-overlay');
            const modal = document.getElementById('product-order-modal');
            if (!overlay || !modal) return;
            overlay.classList.remove('product-order-overlay-visible');
            modal.classList.remove('product-order-modal-visible');
            const duration = 350;
            setTimeout(function () {
                overlay.classList.add('hidden');
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }, duration);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const productOrderOverlay = document.getElementById('product-order-overlay');
            const productOrderClose = document.getElementById('product-order-close');
            const productOrderForm = document.getElementById('product-order-form');
            if (productOrderOverlay) productOrderOverlay.addEventListener('click', closeProductPageOrderModal);
            if (productOrderClose) productOrderClose.addEventListener('click', closeProductPageOrderModal);
            if (productOrderForm) {
                productOrderForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const form = this;
                    const supplierIdEl = document.getElementById('product-order-supplier-id');
                    if (!supplierIdEl || !supplierIdEl.value) {
                        document.getElementById('product-order-supplier')?.focus();
                        return;
                    }
                    const formData = new FormData(form);
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        },
                        body: formData,
                    }).then(async (res) => {
                        if (res.ok) {
                            showMiniToast(@json($isAr ? 'تمت الإضافة إلى السلة' : 'Added to cart'));
                            closeProductPageOrderModal();
                            return;
                        }
                        let payload = null;
                        try { payload = await res.json(); } catch (err) { payload = null; }
                        showMiniError(payload?.message || @json($isAr ? 'فشل الإضافة إلى السلة' : 'Failed to add to cart'));
                    }).catch(() => {
                        showMiniError(@json($isAr ? 'فشل الإضافة إلى السلة' : 'Failed to add to cart'));
                    });
                });
            }

            let selectedReaction = @json($userReaction);
            setLikeButtonUI(@json($userReaction));

            const egpSuffix = window.currencySymbol || 'EGP';

            const findTierPrice = function (supplierId, qty) {
                const q = Math.max(1, Number(qty || 1) || 1);
                const sid = supplierId !== null && typeof supplierId !== 'undefined' ? String(supplierId) : '';
                const list = pricingTiersBySupplier && (pricingTiersBySupplier[sid] ?? pricingTiersBySupplier[Number(sid)]) ? (pricingTiersBySupplier[sid] ?? pricingTiersBySupplier[Number(sid)]) : [];
                const tiers = Array.isArray(list) ? list : [];
                const sorted = [...tiers].sort((a, b) => (Number(b?.min ?? b?.min_quantity ?? 0) || 0) - (Number(a?.min ?? a?.min_quantity ?? 0) || 0));
                for (let i = 0; i < sorted.length; i++) {
                    const t = sorted[i] || {};
                    const min = Number(t.min ?? t.min_quantity ?? 0) || 0;
                    const maxVal = t.max ?? t.max_quantity;
                    const max = (maxVal === null || typeof maxVal === 'undefined' || maxVal === '') ? null : (Number(maxVal) || null);
                    if (min > 0 && q >= min && (max === null || q <= max)) {
                        const p = Number(t.price ?? t.price_per_unit ?? 0);
                        return Number.isFinite(p) && p >= 0 ? p : null;
                    }
                }
                return null;
            };

            const formatMoney0 = function (num) {
                const exRate = window.exchangeRate || 1;
                const n = (Number(num || 0)) / exRate;
                if (!Number.isFinite(n)) return '';
                return n.toLocaleString(undefined, { maximumFractionDigits: 0 });
            };

            const renderSupplierPrices = function () {
                const qtyInput = document.getElementById('supplier-prices-qty');
                const list = document.getElementById('supplier-prices-list');
                if (!qtyInput || !list) return;

                const qty = Math.max(1, Number(qtyInput.value || 1) || 1);
                qtyInput.value = String(qty);
                list.innerHTML = '';

                const suppliers = Array.isArray(suppliersForPrices) ? suppliersForPrices : [];
                if (!suppliers.length) {
                    const empty = document.createElement('div');
                    empty.className = 'text-sm text-gray-600 dark:text-slate-300';
                    empty.textContent = @json($isAr ? 'لا يوجد أسعار للموردين.' : 'No supplier prices found.');
                    list.appendChild(empty);
                    return;
                }

                suppliers.forEach((s) => {
                    const baseUnit = Number(s?.unit_price ?? s?.price ?? 0);
                    const tierUnit = findTierPrice(s?.id ?? null, qty);
                    const unit = tierUnit !== null ? tierUnit : (Number.isFinite(baseUnit) ? baseUnit : 0);
                    const total = unit * qty;
                    const typeLabel = (s?.type || '') === 'factory'
                        ? (@json($isAr) ? 'مصنع' : 'Factory')
                        : (@json($isAr) ? 'مورد' : 'Supplier');

                    const detailsBase = @json(url('/products'));
                    const detailsType = (s?.type || '') === 'factory' ? 'factory' : 'vendor';
                    const detailsHref = `${detailsBase}/${productId}?type=${detailsType}`;
                    const orderLabel = @json($isAr ? 'اطلب من هذا المورد' : 'Order from this supplier');
                    const detailsLabel = @json($isAr ? 'تفاصيل' : 'Details');

                    const card = document.createElement('div');
                    card.className = 'rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-slate-800 dark:bg-slate-900';

                    const qtyAvailable = s?.quantity !== null && typeof s?.quantity !== 'undefined' ? Number(s.quantity) : null;
                    const qtyLine = qtyAvailable !== null && Number.isFinite(qtyAvailable)
                        ? `<div class="text-xs text-gray-600 dark:text-slate-300">${@json($isAr ? 'الكمية المتاحة' : 'Available')}: <span class="font-semibold text-gray-900 dark:text-white">${qtyAvailable}</span></div>`
                        : '';

                    card.innerHTML = `
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">${s?.name ?? ''}</div>
                                <div class="mt-0.5 text-xs text-gray-500 dark:text-slate-400">${typeLabel}</div>
                                ${qtyLine}
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-600 dark:text-slate-300">${@json($isAr ? 'سعر القطعة' : 'Unit price')}</div>
                                <div class="text-sm font-bold text-gray-900 dark:text-white">${formatMoney0(unit)} ${egpSuffix}</div>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <div class="text-xs text-gray-600 dark:text-slate-300">${@json($isAr ? 'الإجمالي' : 'Total')}</div>
                            <div class="text-base font-bold text-rose-500">${formatMoney0(total)} ${egpSuffix}</div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2">
                            <button type="button" class="supplier-price-order h-10 rounded-2xl bg-gradient-to-r from-[#F6416C] to-orange-400 text-white text-sm font-semibold hover:opacity-95" data-supplier-id="${String(s?.id ?? '')}" data-supplier-type="${String(s?.type ?? '')}">
                                ${orderLabel}
                            </button>
                            <a href="${detailsHref}" class="h-10 rounded-2xl border border-gray-200 bg-white text-gray-800 text-sm font-semibold inline-flex items-center justify-center hover:bg-gray-50 dark:bg-slate-900 dark:text-slate-100 dark:border-slate-800 dark:hover:bg-slate-800">
                                ${detailsLabel}
                            </a>
                        </div>
                    `;
                    list.appendChild(card);
                });
            };

            const supplierPricesQtyEl = document.getElementById('supplier-prices-qty');
            if (supplierPricesQtyEl) {
                supplierPricesQtyEl.addEventListener('input', renderSupplierPrices);
                supplierPricesQtyEl.addEventListener('change', renderSupplierPrices);
            }
            renderSupplierPrices();

            document.getElementById('supplier-prices-list')?.addEventListener('click', (e) => {
                const btn = e.target?.closest?.('button.supplier-price-order');
                if (!btn) return;
                const sid = String(btn.dataset.supplierId || '');
                const stype = String(btn.dataset.supplierType || '');
                const el = document.getElementById('current-product-card');
                if (!el) return;

                if (stype === 'factory') {
                    el.dataset.factoryId = sid;
                    el.dataset.supplierId = '';
                } else {
                    el.dataset.supplierId = sid;
                    el.dataset.factoryId = '';
                }

                if (document.getElementById('product-order-modal')) {
                    openProductPageOrderModal(el);
                } else if (typeof openShopProductModal === 'function' && document.getElementById('shop-product-modal')) {
                    openShopProductModal(el);
                }
            });

            const ratingWrapMain = document.getElementById('rating-stars-interactive');
            const ratingAvgTextMain = document.getElementById('rating-avg-text-main');
            const ratingWrap = document.getElementById('rating-stars');
            const ratingAvgText = document.getElementById('rating-avg-text');
            const ratingSummaryAvg = document.getElementById('rating-summary-avg');
            const ratingSummaryCount = document.getElementById('rating-summary-count');

            function renderRatingStars(avg) {
                const v = Number(avg || 0);
                [ratingWrapMain, ratingWrap].forEach(wrap => {
                    if (!wrap) return;
                    wrap.querySelectorAll('.rating-star-btn i').forEach((icon, idx) => {
                        const n = idx + 1;
                        if (v >= n) {
                            icon.classList.add('fill-rose-500', 'text-rose-500');
                            icon.classList.remove('text-gray-600');
                        } else {
                            icon.classList.remove('fill-rose-500', 'text-rose-500');
                            icon.classList.add('text-gray-600');
                        }
                    });
                });
            }

            function setRatingsCountText(count) {
                if (!ratingAvgText) return;
                const parent = ratingAvgText.parentElement;
                if (!parent) return;
                const parts = parent.querySelectorAll('span');
                const last = parts.length ? parts[parts.length - 1] : null;
                if (last && last.textContent && last.textContent.trim().startsWith('(')) {
                    last.textContent = '(' + String(count ?? 0) + ')';
                }
            }

            function renderRatingSummaryStars(avg) {
                const v = Number(avg || 0);
                for (let i = 1; i <= 5; i++) {
                    const star = document.getElementById('rating-summary-star-' + String(i));
                    if (!star) continue;
                    const filled = v >= i;
                    star.className = filled ? 'text-rose-500' : 'text-gray-300 dark:text-gray-600';
                }
            }

            function renderBreakdownBars(count, breakdown) {
                const total = Number(count || 0);
                for (let r = 1; r <= 5; r++) {
                    const c = Number((breakdown && breakdown[r]) || 0);
                    const pct = total > 0 ? Math.round((c * 100) / total) : 0;
                    const bar = document.getElementById('rating-breakdown-bar-' + String(r));
                    const countEl = document.getElementById('rating-breakdown-count-' + String(r));
                    if (bar) bar.style.width = String(pct) + '%';
                    if (countEl) countEl.textContent = String(c);
                }
            }

            const interactiveRatingWrap = document.getElementById('interactive-stars');
            const ratingStatusMsg = document.getElementById('rating-status-msg');

            async function submitRating(value) {
                const ratingVal = Number(value || 0);
                if (!routes.rating || !ratingVal) return;
                
                if (ratingStatusMsg) {
                    ratingStatusMsg.textContent = @json($isAr ? 'جاري الحفظ...' : 'Saving...');
                    ratingStatusMsg.className = 'mt-4 text-sm font-semibold text-slate-400';
                }

                const res = await fetch(routes.rating, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                    },
                    body: JSON.stringify({ rating: ratingVal }),
                });

                if (!res.ok) {
                    if (ratingStatusMsg) {
                        ratingStatusMsg.textContent = @json($isAr ? 'فشل الحفظ' : 'Failed to save');
                        ratingStatusMsg.className = 'mt-4 text-sm font-semibold text-rose-500';
                    }
                    showMiniError(@json($isAr ? 'تعذر حفظ التقييم' : 'Failed to save rating'));
                    return;
                }

                const data = await res.json();
                const avg = Number(data.avg_rating || 0);
                const count = Number(data.ratings_count || 0);
                const breakdown = data.ratings_breakdown || null;
                
                // Update UIs
                if (ratingAvgText) ratingAvgText.textContent = avg.toFixed(1);
                setRatingsCountText(count);
                renderRatingStars(avg);
                if (ratingSummaryAvg) ratingSummaryAvg.textContent = avg.toFixed(1);
                if (ratingSummaryCount) ratingSummaryCount.textContent = String(count);
                renderRatingSummaryStars(avg);
                renderBreakdownBars(count, breakdown);

                if (ratingStatusMsg) {
                    ratingStatusMsg.textContent = @json($isAr ? 'تم الحفظ بنجاح!' : 'Saved successfully!');
                    ratingStatusMsg.className = 'mt-4 text-sm font-semibold text-emerald-500';
                }

                // Update the interactive stars highlight
                if (interactiveRatingWrap) {
                    interactiveRatingWrap.querySelectorAll('i[data-lucide="star"]').forEach((star, idx) => {
                        if (idx < ratingVal) {
                            star.classList.add('fill-yellow-400', 'text-yellow-400');
                        } else {
                            star.classList.remove('fill-yellow-400', 'text-yellow-400');
                            star.classList.add('text-slate-700');
                        }
                    });
                }

                showMiniToast(@json($isAr ? 'تم حفظ التقييم' : 'Rating saved'));
            }

            if (interactiveRatingWrap) {
                interactiveRatingWrap.querySelectorAll('button.rating-action-btn').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        @auth
                            submitRating(btn.dataset.value);
                        @else
                            window.location.href = @json(route('login'));
                        @endauth
                    });
                });
            }
            if (window.Swiper) {
                new Swiper('#pricing-swiper', {
                    slidesPerView: 1,
                    spaceBetween: 10,
                    pagination: { el: '#pricing-swiper .swiper-pagination', clickable: true },
                    navigation: { nextEl: '#pricing-swiper .swiper-button-next', prevEl: '#pricing-swiper .swiper-button-prev' },
                    breakpoints: { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } },
                });

                const gallerySwiperEl = document.getElementById('gallery-swiper');
                const galleryCount = Number(gallerySwiperEl?.dataset?.slidesCount || 0);
                if (gallerySwiperEl && galleryCount > 1) {
                    new Swiper('#gallery-swiper', {
                        slidesPerView: 'auto',
                        spaceBetween: 12,
                        pagination: { el: '#gallery-swiper .swiper-pagination', clickable: true },
                        navigation: { nextEl: '#gallery-swiper .swiper-button-next', prevEl: '#gallery-swiper .swiper-button-prev' },
                    });
                }

                new Swiper('#similar-swiper', {
                    slidesPerView: 1,
                    spaceBetween: 10,
                    pagination: { el: '#similar-swiper .swiper-pagination', clickable: true },
                    navigation: { nextEl: '#similar-swiper .swiper-button-next', prevEl: '#similar-swiper .swiper-button-prev' },
                    breakpoints: { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 4 } },
                });
            }

            document.querySelectorAll('.factory-item-toggle').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-target');
                    const el = id ? document.getElementById(id) : null;
                    const icon = btn.querySelector('.rotate-icon');
                    if (!el) return;
                    el.classList.toggle('expanded');
                    icon?.classList.toggle('rotated');
                });
            });

            document.getElementById('order-now-btn')?.addEventListener('click', () => {
                const el = document.getElementById('current-product-card');
                if (!el) {
                    showMiniError(@json($isAr ? 'المودال غير متاح حالياً' : 'Modal is not available'));
                    return;
                }
                if (document.getElementById('product-order-modal')) {
                    openProductPageOrderModal(el);
                    return;
                }
                if (typeof openShopProductModal === 'function' && document.getElementById('shop-product-modal')) {
                    openShopProductModal(el);
                    return;
                }
                try {
                    el.click();
                } catch (e) {
                    showMiniError(@json($isAr ? 'المودال غير متاح حالياً' : 'Modal is not available'));
                }
            });

            document.getElementById('comments-btn')?.addEventListener('click', openComments);
            document.getElementById('comments-stat-btn')?.addEventListener('click', openComments);
            document.getElementById('comments-close')?.addEventListener('click', closeComments);
            document.getElementById('comments-overlay')?.addEventListener('click', (e) => {
                if (e.target && e.target.id === 'comments-overlay') closeComments();
            });
            document.getElementById('comments-dialog')?.addEventListener('click', (e) => {
                e.stopPropagation();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    const overlay = document.getElementById('comments-overlay');
                    if (overlay && !overlay.classList.contains('hidden')) closeComments();
                }
            });

            const reactionPicker = document.getElementById('reaction-picker');
            const likeBtn = document.getElementById('like-btn');
            if (likeBtn && reactionPicker) {
                let hideTimer = null;

                const displayPicker = () => {
                    if (hideTimer) {
                        clearTimeout(hideTimer);
                        hideTimer = null;
                    }
                    reactionPicker.classList.remove('hidden');
                    // Add a small delay to opacity to ensure transition works
                    setTimeout(() => {
                        reactionPicker.style.opacity = '1';
                        reactionPicker.style.pointerEvents = 'auto';
                    }, 10);
                };

                const hidePickerDelayed = () => {
                    if (hideTimer) {
                        clearTimeout(hideTimer);
                    }
                    hideTimer = setTimeout(() => {
                        reactionPicker.style.opacity = '0';
                        reactionPicker.style.pointerEvents = 'none';
                        setTimeout(() => {
                            if (reactionPicker.style.opacity === '0') {
                                reactionPicker.classList.add('hidden');
                            }
                        }, 200);
                    }, 800); // Increased duration to 800ms
                };

                likeBtn.addEventListener('mouseenter', displayPicker);
                likeBtn.addEventListener('mouseleave', hidePickerDelayed);
                reactionPicker.addEventListener('mouseenter', displayPicker);
                reactionPicker.addEventListener('mouseleave', hidePickerDelayed);
                reactionPicker.querySelectorAll('button.reaction-btn').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        selectedReaction = btn.dataset.reaction || 'like';
                        reactionPicker.classList.add('hidden');
                        likeBtn.click();
                    });
                });
            }

            document.getElementById('like-btn')?.addEventListener('click', async () => {
                @auth
                    const res = await fetch(routes.like, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken(),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ reaction: selectedReaction || 'like' }),
                    });
                    if (!res.ok) {
                        showMiniError(@json($isAr ? 'تعذر تنفيذ التفاعل' : 'Reaction failed'));
                        return;
                    }
                    if (!looksLikeJsonResponse(res)) {
                        window.location.href = @json(route('login'));
                        return;
                    }
                    const data = await res.json();
                    const likesCountEl = document.getElementById('likes-count');
                    if (likesCountEl && typeof data.likes_count !== 'undefined') likesCountEl.textContent = String(data.likes_count);
                    if (Array.isArray(data.top_reactions)) {
                        renderReactionsSummary(data.top_reactions);
                    }
                    setLikeButtonUI(data.user_reaction || 'like');
                @else
                    window.location.href = @json(route('login'));
                @endauth
            });

            document.getElementById('share-btn')?.addEventListener('click', async () => {
                @auth
                    const res = await fetch(routes.share, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken(),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    if (!res.ok) {
                        showMiniError(@json($isAr ? 'تعذر تنفيذ المشاركة' : 'Share failed'));
                        return;
                    }
                    if (!looksLikeJsonResponse(res)) {
                        window.location.href = @json(route('login'));
                        return;
                    }
                    const data = await res.json();
                    const sharesCountEl = document.getElementById('shares-count');
                    if (sharesCountEl && typeof data.shares_count !== 'undefined') sharesCountEl.textContent = String(data.shares_count);

                    if (navigator.share) {
                        navigator.share({ title: document.title, url: window.location.href }).catch(() => {});
                        showMiniToast(@json($isAr ? 'تم فتح المشاركة' : 'Share opened'));
                    } else {
                        try {
                            await navigator.clipboard.writeText(window.location.href);
                            showMiniToast(@json($isAr ? 'تم نسخ الرابط' : 'Link copied'));
                        } catch (e) {
                            showMiniToast(@json($isAr ? 'لم يتم نسخ الرابط' : 'Copy failed'));
                        }
                    }
                @else
                    window.location.href = @json(route('login'));
                @endauth
            });

            // Rating Stars
            document.querySelectorAll('.rating-action-btn').forEach((btn) => {
                btn.addEventListener('click', async () => {
                    const val = btn.dataset.value;
                    const statusEl = document.getElementById('rating-status');
                    if (statusEl) statusEl.textContent = @json($isAr ? 'جاري الحفظ...' : 'Saving...');
                    
                    const res = await fetch(routes.rating, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken(),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ rating: val }),
                    });

                    if (res.ok) {
                        const data = await res.json();
                        // Update stars UI in the rating section
                        const allRatingBtns = document.querySelectorAll('.rating-star-btn i');
                        allRatingBtns.forEach((star, idx) => {
                            if (idx < val) {
                                star.classList.add('fill-yellow-500', 'text-yellow-500');
                                star.classList.remove('text-gray-700');
                            } else {
                                star.classList.remove('fill-yellow-500', 'text-yellow-500');
                                star.classList.add('text-gray-700');
                            }
                        });
                        if (statusEl) {
                            statusEl.textContent = @json($isAr ? 'تم حفظ تقييمك بنجاح!' : 'Rating saved successfully!');
                            statusEl.classList.replace('text-rose-500', 'text-green-500');
                        }
                        showMiniToast(@json($isAr ? 'تم حفظ التقييم' : 'Rating saved'));
                    } else {
                        if (statusEl) statusEl.textContent = @json($isAr ? 'فشل حفظ التقييم' : 'Failed to save rating');
                        showMiniError(@json($isAr ? 'حدث خطأ أثناء التقييم' : 'Error saving rating'));
                    }
                });
            });

            const commentForm = document.getElementById('comment-form-outer');
            if (commentForm) {
                commentForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const bodyEl = document.getElementById('comment-body-outer');
                    const body = (bodyEl?.value || '').trim();
                    if (!body) return;

                    const res = await fetch(routes.comment, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken(),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ body }),
                    });

                    if (!looksLikeJsonResponse(res)) {
                        if (!res.ok) {
                            window.location.href = @json(route('login'));
                            return;
                        }
                        showMiniError(@json($isAr ? 'حدث خطأ أثناء إرسال التعليق' : 'Failed to send comment'));
                        return;
                    }

                    let data = null;
                    try {
                        data = await res.json();
                    } catch (e) {
                        data = null;
                    }

                    if (!res.ok) {
                        const msg = data?.message || @json($isAr ? 'تعذر إرسال التعليق' : 'Unable to send comment');
                        showMiniError(msg);
                        return;
                    }

                    if (bodyEl) bodyEl.value = '';
                    const commentsCountEl = document.getElementById('comments-count');
                    if (commentsCountEl && typeof data.comments_count !== 'undefined') commentsCountEl.textContent = String(data.comments_count);
                    if (data.comment?.body) {
                        const list = document.getElementById('comments-list-outer');
                        if (list) {
                            const wrap = document.createElement('div');
                            wrap.className = 'flex items-start gap-3';
                            const initial = String(data.comment.user_name || 'U').trim().slice(0, 1) || 'U';
                            wrap.innerHTML = `
                                <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 uppercase dark:bg-slate-800 dark:text-slate-200">${initial}</div>
                                <div class="flex-1">
                                    <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-2xl px-3 py-2 shadow-sm">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white"></div>
                                        <div class="text-sm text-gray-700 dark:text-gray-300 mt-1"></div>
                                    </div>
                                </div>
                            `;
                            const nameDiv = wrap.querySelector('div.text-sm.font-semibold');
                            const bodyDiv = wrap.querySelector('div.text-sm.text-gray-700');
                            if (nameDiv) nameDiv.textContent = data.comment.user_name || 'User';
                            if (bodyDiv) bodyDiv.textContent = data.comment.body;
                            list.prepend(wrap);
                            showMiniToast(@json($isAr ? 'تم إضافة التعليق' : 'Comment added'));
                        }
                    }
                });
            }

            lucide.createIcons();
        });
    </script>
</x-shop-layouts.app>
