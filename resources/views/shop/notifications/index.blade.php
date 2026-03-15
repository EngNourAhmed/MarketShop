@php($lang = session('lang', 'en'))
@php($isAr = $lang === 'ar')

<x-shop-layouts.app :title="($isAr ? 'الإشعارات' : 'Notifications')">
    <div class="max-w-5xl mx-auto space-y-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $isAr ? 'الإشعارات' : 'Notifications' }}</div>
                <i data-lucide="bell" class="w-5 h-5 text-rose-500"></i>
            </div>

            <a href="{{ route('customer.home') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/40">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                {{ $isAr ? 'رجوع' : 'Back' }}
            </a>
        </div>

        @if(($notifications ?? collect())->isEmpty())
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 text-sm text-gray-500 dark:text-gray-300">
            {{ $isAr ? 'لا توجد إشعارات.' : 'No notifications.' }}
        </div>
        @else
        <div class="space-y-4">
            @foreach($notifications as $n)
            @php($isRead = !empty($n->read_at))
            @php($timeText = $n->created_at ? $n->created_at->diffForHumans() : '')
            @php($t = mb_strtolower((string) ($n->title ?? '')))
            @php($icon = str_contains($t, 'order') || str_contains($t, 'اوردر') ? 'package' : (str_contains($t, 'security') || str_contains($t, 'login') ? 'shield-alert' : (str_contains($t, 'payment') || str_contains($t, 'دفع') ? 'credit-card' : (str_contains($t, 'promo') || str_contains($t, 'خصم') ? 'percent' : 'bell'))))
            @php($bubble = $icon === 'package' ? 'bg-rose-100 text-rose-600' : ($icon === 'percent' ? 'bg-yellow-100 text-yellow-600' : ($icon === 'shield-alert' ? 'bg-blue-100 text-blue-600' : ($icon === 'credit-card' ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-600'))))
            @php($bubbleDark = $icon === 'package' ? 'dark:bg-rose-500/20 dark:text-rose-300' : ($icon === 'percent' ? 'dark:bg-yellow-500/20 dark:text-yellow-300' : ($icon === 'shield-alert' ? 'dark:bg-blue-500/20 dark:text-blue-300' : ($icon === 'credit-card' ? 'dark:bg-emerald-500/20 dark:text-emerald-300' : 'dark:bg-gray-700 dark:text-gray-200'))))

            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-4 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0 {{ $bubble }} {{ $bubbleDark }} {{ $isRead ? 'opacity-70' : '' }}">
                        <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-900 dark:text-white">{{ $n->title }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                            {{ $n->body }}
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-400 mt-2">{{ $timeText }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            @if(method_exists(($notifications ?? null), 'links'))
            {{ $notifications->links() }}
            @endif
        </div>
        @endif
    </div>
</x-shop-layouts.app>