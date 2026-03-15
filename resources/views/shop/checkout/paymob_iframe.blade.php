@php($lang = session('lang', 'en'))
@php($isAr = $lang === 'ar')

<x-shop-layouts.app :title="($isAr ? 'الدفع الإلكتروني' : 'Online Payment')">
    <div class="max-w-4xl mx-auto py-8 px-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-gray-100 dark:border-slate-800 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gradient-to-r from-slate-50 to-white dark:from-slate-900 dark:to-slate-950">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/10 flex items-center justify-center text-rose-500">
                        <i data-lucide="credit-card" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $isAr ? 'بوابة الدفع الآمنة' : 'Secure Payment Gateway' }}</h1>
                        <p class="text-sm text-gray-500 dark:text-slate-400">{{ $isAr ? 'يرجى إدخال تفاصيل البطاقة لإتمام العملية' : 'Please enter your card details to complete the transaction' }}</p>
                    </div>
                </div>
                <div class="hidden md:block">
                    <img src="https://paymob.com/assets/images/paymob-logo.svg" alt="Paymob" class="h-8 opacity-50 dark:invert">
                </div>
            </div>

            <div class="relative bg-gray-50 dark:bg-slate-950" style="min-height: 600px;">
                <!-- Loading State -->
                <div id="iframe-loader" class="absolute inset-0 flex flex-col items-center justify-center bg-white dark:bg-slate-950 z-10">
                    <div class="w-12 h-12 border-4 border-rose-500/20 border-t-rose-500 rounded-full animate-spin mb-4"></div>
                    <p class="text-gray-500 dark:text-slate-400 font-medium">{{ $isAr ? 'جاري تحميل بوابة الدفع...' : 'Loading payment gateway...' }}</p>
                </div>

                <!-- Paymob Iframe -->
                <iframe 
                    src="{{ $iframeUrl }}" 
                    width="100%" 
                    height="800" 
                    frameborder="0" 
                    class="w-full border-0"
                    onload="document.getElementById('iframe-loader').style.display='none';"
                ></iframe>
            </div>

            <div class="p-4 bg-gray-50/50 dark:bg-slate-900/50 border-t border-gray-100 dark:border-slate-800">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-slate-400">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-500"></i>
                        <span>{{ $isAr ? 'جميع المعاملات مشفرة وآمنة بنسبة 100%' : 'All transactions are 100% encrypted and secure' }}</span>
                    </div>
                    <a href="{{ route('shop.orders.index') }}" class="text-sm font-semibold text-gray-600 dark:text-slate-300 hover:text-rose-500 transition-colors">
                        {{ $isAr ? 'إلغاء والعودة لطلباتي' : 'Cancel and return to my orders' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-shop-layouts.app>
