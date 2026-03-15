@php($lang = session('lang', 'en'))
@php($isAr = $lang === 'ar')

<x-shop-layouts.app :title="($isAr ? 'إرجاع طلب' : 'Return an Order')">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $isAr ? 'إرجاع طلب' : 'Return an Order' }}</div>
                <i data-lucide="undo-2" class="w-5 h-5 text-rose-500"></i>
            </div>
        </div>

        @if(session('status'))
            <div class="rounded-2xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 dark:bg-emerald-900/20 dark:border-emerald-700 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="space-y-3">
            @forelse(($orders ?? []) as $order)
                @php($items = $order->items ?? collect())
                @php($firstItem = $items->first())
                @php($product = $firstItem?->product)
                @php($title = $product ? ($isAr ? ($product->name_ar ?? $product->name) : ($product->name_en ?? $product->name)) : (($isAr ? 'اوردر' : 'Order') . ' ' . ($order->order_code ?? '')))
                @php($imgUrl = $product && !empty($product->image) ? asset('storage/' . $product->image) : asset('apple-touch-icon.png'))
                @php($statusText = (string) ($order->status ?? ''))
                @php($returnReq = ($order->returns ?? collect())->sortByDesc('created_at')->first())

                <div class="rounded-2xl bg-white border border-gray-200 p-4 shadow-sm dark:bg-slate-900 dark:border-slate-800">
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 flex-shrink-0 dark:bg-slate-800/60 dark:border-slate-700">
                            <img src="{{ $imgUrl }}" alt="order" class="w-full h-full object-cover" />
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-bold text-gray-900 dark:text-white truncate">{{ $title }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-300 mt-1 truncate">{{ $order->order_code }}</div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $isAr ? 'الحالة الحالية:' : 'Current status:' }} {{ $statusText }}</div>
                                </div>

                                <div class="text-right text-xs text-gray-500 dark:text-gray-300">
                                    <div>{{ $isAr ? 'التاريخ:' : 'Date:' }} {{ optional($order->created_at)->format('Y-m-d') ?? '-' }}</div>
                                    <div class="mt-1 font-semibold text-gray-900 dark:text-white">{{ number_format((float) ($order->total ?? 0), 0, '.', ',') }} {{ $isAr ? 'ج.م' : 'EGP' }}</div>
                                </div>
                            </div>

                            <div class="mt-3 border-t border-dashed border-gray-200 dark:border-slate-700 pt-3">
                                @if(!$returnReq)
                                    <details>
                                        <summary class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-[#F6416C] to-orange-400 text-white text-sm font-semibold hover:opacity-90 [&::-webkit-details-marker]:hidden">
                                            {{ $isAr ? 'طلب إرجاع' : 'Return Order' }}
                                        </summary>
                                        <div class="mt-3 rounded-2xl bg-gray-50 border border-gray-200 p-4 dark:bg-slate-900/40 dark:border-slate-700">
                                            <form method="POST" action="{{ route('shop.returns.store') }}" enctype="multipart/form-data" class="space-y-3 text-sm">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ (int) $order->id }}" />

                                                <div>
                                                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">{{ $isAr ? 'سبب الإرجاع' : 'Return reason' }}</label>
                                                    <textarea name="reason" rows="3" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100" required></textarea>
                                                </div>

                                                <div>
                                                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">{{ $isAr ? 'صور توضيحية (مطلوبة)' : 'Evidence images (required)' }}</label>
                                                    <input type="file" name="images[]" accept="image/*" multiple required class="block w-full text-xs text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-600 hover:file:bg-rose-100 dark:text-slate-200 dark:file:bg-slate-800 dark:file:text-slate-100" />
                                                    <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">{{ $isAr ? 'ارفع صورة أو أكثر توضح سبب الإرجاع.' : 'Upload one or more images that show the issue.' }}</p>
                                                </div>

                                                <div class="flex justify-end">
                                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-[#F6416C] to-orange-400 text-white text-sm font-semibold hover:opacity-95">
                                                        {{ $isAr ? 'إرسال الطلب' : 'Submit request' }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </details>
                                @else
                                    <div class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
                                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-semibold
                                            @if($returnReq->status === 'approved') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200
                                            @elseif($returnReq->status === 'rejected') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-200
                                            @else bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200 @endif">
                                            {{ $isAr ? 'حالة طلب الإرجاع:' : 'Return status:' }}
                                            <span>
                                                @if($returnReq->status === 'approved')
                                                    {{ $isAr ? 'مقبول' : 'Approved' }}
                                                @elseif($returnReq->status === 'rejected')
                                                    {{ $isAr ? 'مرفوض' : 'Rejected' }}
                                                @else
                                                    {{ $isAr ? 'قيد المراجعة' : 'Pending' }}
                                                @endif
                                            </span>
                                        </div>

                                        <div>
                                            <div class="font-semibold text-gray-800 dark:text-gray-100 mb-1">{{ $isAr ? 'سببك:' : 'Your reason:' }}</div>
                                            <div class="text-xs leading-relaxed">{{ $returnReq->reason }}</div>
                                        </div>

                                        @if(!empty($returnReq->admin_note))
                                            <div>
                                                <div class="font-semibold text-gray-800 dark:text-gray-100 mb-1">{{ $isAr ? 'رد الإدارة:' : 'Admin reply:' }}</div>
                                                <div class="text-xs leading-relaxed">{{ $returnReq->admin_note }}</div>
                                            </div>
                                        @endif

                                        @php($imgs = (array) ($returnReq->images ?? []))
                                        @if(!empty($imgs))
                                            <div>
                                                <div class="font-semibold text-gray-800 dark:text-gray-100 mb-1">{{ $isAr ? 'الصور المرفوعة:' : 'Submitted images:' }}</div>
                                                <div class="mt-1 grid grid-cols-3 gap-2">
                                                    @foreach($imgs as $img)
                                                        <div class="h-16 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 dark:bg-slate-800/60 dark:border-slate-700">
                                                            <img src="{{ asset('storage/' . $img) }}" alt="return image" class="w-full h-full object-cover" />
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl bg-white border border-gray-200 p-6 text-sm text-gray-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                    {{ $isAr ? 'لا توجد أوردرات تم توصيلها يمكن إرجاعها.' : 'No delivered orders available for return.' }}
                </div>
            @endforelse
        </div>
    </div>
</x-shop-layouts.app>
