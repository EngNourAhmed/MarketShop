@extends('shop.layouts.app')

@section('title', __('Order Invoice #') . $order->order_code)

@push('head')
<style>
    @media print {
        body * { visibility: hidden; }
        #invoice-print-area,
        #invoice-print-area * { visibility: visible; }
        #invoice-print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            background: #fff;
            color: #111;
            box-shadow: none;
        }
        #invoice-print-area .dark\:bg-slate-900 { background: #fff !important; }
        #invoice-print-area .dark\:text-white { color: #111 !important; }
        #invoice-print-area .dark\:text-slate-400 { color: #374151 !important; }
        #invoice-print-area .dark\:border-slate-800 { border-color: #e5e7eb !important; }
    }
</style>
@endpush

@section('content')
@php($isAr = app()->getLocale() === 'ar')
@php($safeTrans = fn($val) => is_array($t = __($val)) ? $val : $t)

<div class="container mx-auto px-4 py-8">
    <div id="invoice-print-area" class="max-w-4xl mx-auto bg-white dark:bg-slate-900 rounded-2xl shadow-lg overflow-hidden print:shadow-none print:max-w-none">
        <!-- Invoice Header -->
        <div class="p-8 border-b border-gray-200 dark:border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('INVOICE') }}</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1">#{{ $order->order_code }}</p>
            </div>
            <div class="{{ $isAr ? 'text-left' : 'text-right' }}">
                <div class="text-sm text-gray-500 dark:text-slate-400">{{ __('Order Date') }}</div>
                <div class="font-semibold text-gray-900 dark:text-white" dir="ltr">{{ $order->created_at->format('Y-m-d H:i') }}</div>
                <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                    {{ $order->status === 'delivered' || $order->status === 'تم التوصيل' ? 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-200' : 
                       ($order->status === 'cancelled' || $order->status === 'ملغي' ? 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-200' : 
                       'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-200') }}">
                    {{ $safeTrans($order->status) }}
                </div>
            </div>
        </div>

        <!-- Addresses -->
        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8 border-b border-gray-200 dark:border-slate-800">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400 mb-3">{{ __('Customer Details') }}</h3>
                <div class="text-gray-900 dark:text-white space-y-1">
                    <p class="font-semibold">{{ $order->name ?? $order->customer?->name ?? __('Guest') }}</p>
                    <p>{{ $order->email ?? $order->customer?->email ?? '' }}</p>
                    <p>{{ $order->phone ?? $order->customer?->phone ?? '' }}</p>
                    <p class="text-sm text-gray-600 dark:text-slate-300 mt-2">{{ $order->address ?? '' }}</p>
                </div>
            </div>
            <div class="{{ $isAr ? 'text-left' : 'md:text-right' }}">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400 mb-3">{{ __('Payment Info') }}</h3>
                <div class="text-gray-900 dark:text-white space-y-1">
                    <p>{{ __('Payment Method:') }} {{ $safeTrans($order->payment_method ?? 'Cash on Delivery') }}</p>
                    <p>{{ __('Payment Status:') }} {{ $safeTrans($order->payment_status ?? 'Pending') }}</p>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="p-8">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-slate-400 mb-4">{{ __('Order Items') }}</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-slate-800 text-gray-500 dark:text-slate-400">
                            <th class="py-3 font-semibold text-start">{{ __('Product') }}</th>
                            <th class="py-3 font-semibold text-center">{{ __('Qty') }}</th>
                            <th class="py-3 font-semibold text-end">{{ __('Unit Price') }}</th>
                            <th class="py-3 font-semibold text-end">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        @if($item->product && !empty($item->product->image))
                                            <img src="{{ asset('storage/' . $item->product->image) }}" class="w-12 h-12 rounded-lg object-cover bg-gray-100 print:hidden" alt="">
                                        @endif
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">
                                                {{ $isAr ? ($item->product->name_ar ?? $item->product->name) : ($item->product->name_en ?? $item->product->name) }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-slate-400">
                                                {{ $item->product?->sku ?? '' }}
                                                @if($item->color) | {{ $item->color }} @endif
                                                @if($item->size) | {{ $item->size }} @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-center text-gray-900 dark:text-white">{{ $item->quantity }}</td>
                                <td class="py-4 text-end text-gray-900 dark:text-white">{{ \App\Helpers\CurrencyHelper::format((float) ($item->unit_price ?? 0)) }}</td>
                                <td class="py-4 text-end font-semibold text-gray-900 dark:text-white">{{ \App\Helpers\CurrencyHelper::format((float) ($item->total ?? 0)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary -->
        <div class="p-8 bg-gray-50 dark:bg-slate-800/50 border-t border-gray-200 dark:border-slate-800">
            <div class="flex flex-col items-end gap-2">
                <div class="flex justify-between w-full md:w-64 text-sm">
                    <span class="text-gray-500 dark:text-slate-400">{{ __('Subtotal') }}</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ \App\Helpers\CurrencyHelper::format((float) $order->items->sum('total')) }}</span>
                </div>
                <div class="flex justify-between w-full md:w-64 text-sm">
                    <span class="text-gray-500 dark:text-slate-400">{{ __('Shipping') }}</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ \App\Helpers\CurrencyHelper::format((float) ($order->shipping_cost ?? 0)) }}</span>
                </div>
                <div class="w-full md:w-64 border-t border-gray-200 dark:border-slate-700 my-2"></div>
                <div class="flex justify-between w-full md:w-64 text-lg font-bold">
                    <span class="text-gray-900 dark:text-white">{{ __('Total') }}</span>
                    <span class="text-rose-600">{{ \App\Helpers\CurrencyHelper::format((float) ($order->total ?? 0)) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div id="invoice-footer-actions" class="p-8 border-t border-gray-200 dark:border-slate-800 print:hidden flex flex-wrap justify-between gap-3">
            <a href="{{ route('shop.orders.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 font-medium transition dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                {{ __('Back to Orders') }}
            </a>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-gray-900 text-white hover:bg-gray-800 font-medium transition flex items-center gap-2 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    {{ __('Print Invoice') }}
                </button>
                <button type="button" id="invoice-download-pdf" class="px-5 py-2.5 rounded-xl bg-rose-600 text-white hover:bg-rose-700 font-medium transition flex items-center gap-2">
                    <i data-lucide="file-down" class="w-4 h-4"></i>
                    {{ __('Download PDF') }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('invoice-download-pdf');
    var el = document.getElementById('invoice-print-area');
    if (!btn || !el) return;
    btn.addEventListener('click', function () {
        btn.disabled = true;
        var clone = el.cloneNode(true);
        var footer = clone.querySelector('#invoice-footer-actions');
        if (footer) footer.remove();
        var opt = {
            margin: 10,
            filename: 'invoice-{{ $order->order_code }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(clone).save().then(function () {
            btn.disabled = false;
        }).catch(function () {
            btn.disabled = false;
        });
    });
});
</script>
@endpush
@endsection
