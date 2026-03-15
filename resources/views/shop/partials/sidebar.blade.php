@php
    $lang = session('lang', 'en');
    $isAr = $lang === 'ar';

    $shopUser = auth()->user();
    $unreadMessageCount = 0;

    if ($shopUser) {
        $unreadMessageCount = \App\Models\Message::query()
            ->where('user_id', $shopUser->id)
            ->whereIn('sender_role', ['admin', 'assistant'])
            ->where('is_read_by_user', false)
            ->count();
    }
@endphp


<div id="shop-overlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden md:hidden transition-opacity duration-300"></div>

<nav id="shop-side-menu"
    class="fixed top-0 {{ $isAr ? 'right-0 translate-x-full' : 'left-0 -translate-x-full' }} h-screen w-3/4 max-w-xs bg-white dark:bg-slate-900 shadow-xl z-50 transform transition-all duration-[350ms] ease-[cubic-bezier(0.4,0,0.2,1)] {{ $isAr ? 'border-l border-gray-200 dark:border-slate-800' : 'border-r border-gray-200 dark:border-slate-800' }} md:hidden flex flex-col">
    <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-slate-800">
        <h2 class="text-xl font-bold bg-gradient-to-tr from-[#FFF687] to-[#F6416C] bg-clip-text text-transparent">{{ $isAr ? 'قائمة' : 'Menu' }}</h2>
        <button id="shop-close-menu-btn"><i data-lucide="x" class="w-6 h-6 text-gray-700 dark:text-slate-200"></i></button>
    </div>

    <div class="p-4 overflow-y-auto flex-grow">
        <!-- Navigation section - hidden on mobile, visible on desktop -->
        <div class="mb-6 hidden md:block">
            <h3 class="px-2 mb-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $isAr ? 'التنقل' : 'Navigation' }}</h3>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('customer.home') }}" class="flex items-center p-2 rounded-lg {{ request()->routeIs('customer.home') ? 'text-rose-600 bg-rose-50 dark:text-rose-300 dark:bg-slate-800' : 'text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                        <i data-lucide="home" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }}"></i>
                        {{ $isAr ? 'الرئيسية' : 'Home' }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('shop.notifications.index') }}" class="flex items-center p-2 rounded-lg {{ request()->routeIs('shop.notifications.index') ? 'text-rose-600 bg-rose-50 dark:text-rose-300 dark:bg-slate-800' : 'text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                        <i data-lucide="bell" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>
                        {{ $isAr ? 'الإشعارات' : 'Notifications' }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('shop.orders.index') }}" class="flex items-center p-2 rounded-lg {{ request()->routeIs('shop.orders.index') ? 'text-rose-600 bg-rose-50 dark:text-rose-300 dark:bg-slate-800' : 'text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                        <i data-lucide="clipboard-list" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>
                        {{ $isAr ? 'الطلبات' : 'Orders' }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('shop.special_orders.create') }}" class="flex items-center p-2 rounded-lg {{ request()->routeIs('shop.special_orders.*') ? 'text-rose-600 bg-rose-50 dark:text-rose-300 dark:bg-slate-800' : 'text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                        <i data-lucide="star" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>
                        {{ $isAr ? 'طلب خاص' : 'Special Order' }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('shop.messages.index') }}" class="flex items-center p-2 rounded-lg {{ request()->routeIs('shop.messages.index') ? 'text-rose-600 bg-rose-50 dark:text-rose-300 dark:bg-slate-800' : 'text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                        <div class="flex items-center gap-2 flex-1">
                            <i data-lucide="message-square" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>
                            <span>{{ $isAr ? 'الرسائل' : 'Messages' }}</span>
                        </div>
                        @if($unreadMessageCount > 0)
                            <span class="inline-flex items-center justify-center min-w-[18px] h-5 px-1.5 rounded-full text-[11px] font-semibold bg-rose-500 text-white">
                                {{ $unreadMessageCount > 99 ? '99+' : $unreadMessageCount }}
                            </span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('shop.account') }}" class="flex items-center p-2 rounded-lg {{ request()->routeIs('shop.account') ? 'text-rose-600 bg-rose-50 dark:text-rose-300 dark:bg-slate-800' : 'text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                        <i data-lucide="user" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>
                        {{ $isAr ? 'الحساب' : 'Account' }}
                    </a>
                </li>
            </ul>
        </div>

        <div>
            <h3 class="px-2 mb-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $isAr ? 'الإعدادات' : 'Settings' }}</h3>
            <ul class="space-y-1">
                <li>
                    <a href="#" class="flex items-center p-2 rounded-lg text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800">
                        <i data-lucide="wrench" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>
                        {{ $isAr ? 'الدعم الفني' : 'Technical Support' }}
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center p-2 rounded-lg text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800">
                        <i data-lucide="shield" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>
                        {{ $isAr ? 'الخصوصية والسياسات' : 'Privacy and Policies' }}
                    </a>
                </li>
                <li class="flex items-center justify-between p-2 text-gray-800 dark:text-slate-200">
                    <div class="flex items-center">
                        <i data-lucide="moon" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>
                        {{ $isAr ? 'الوضع الليلي' : 'Dark Mode' }}
                    </div>
                    <label for="dark-mode-toggle-mobile" class="inline-flex relative items-center cursor-pointer">
                        <input type="checkbox" value="" id="dark-mode-toggle-mobile" class="dark-mode-toggle sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-rose-400"></div>
                    </label>
                </li>
                <li>
                    <a href="{{ route('shop.returns.index') }}" class="flex items-center p-2 rounded-lg {{ request()->routeIs('shop.returns.index') ? 'text-rose-600 bg-rose-50 dark:text-rose-300 dark:bg-slate-800' : 'text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                        <i data-lucide="undo-2" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>
                        {{ $isAr ? 'إرجاع طلب' : 'Return an Order' }}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="p-4 border-t border-gray-200 dark:border-slate-800">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 p-3 bg-gray-100 text-gray-900 dark:bg-slate-800 dark:text-white text-center rounded-lg font-semibold hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors">
                <i data-lucide="log-out" class="w-5 h-5"></i>
                <span>{{ $isAr ? 'تسجيل الخروج' : 'Logout' }}</span>
            </button>
        </form>
    </div>
</nav>

<nav class="hidden md:flex w-64 bg-white dark:bg-slate-900 shadow-l z-30 {{ $isAr ? 'border-l border-gray-200 dark:border-slate-800' : 'border-r border-gray-200 dark:border-slate-800' }} flex-col flex-shrink-0 sticky top-0 max-h-screen">
    <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-slate-800">
        <h1 class="text-3xl pl-2 font-bold bg-gradient-to-tr from-[#FFF687] to-[#F6416C] bg-clip-text text-transparent mb-1 pt-1">Trady</h1>
    </div>

    <div class="p-4 overflow-y-auto flex-grow">
        <div class="mb-6">
            <h3 class="px-2 mb-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $isAr ? 'التنقل' : 'Navigation' }}</h3>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('customer.home') }}" class="flex items-center p-2 rounded-lg {{ request()->routeIs('customer.home') ? 'text-rose-600 bg-rose-50 dark:text-rose-300 dark:bg-slate-800' : 'text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                        <i data-lucide="home" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }}"></i>
                        {{ $isAr ? 'الرئيسية' : 'Home' }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('shop.notifications.index') }}" class="flex items-center p-2 rounded-lg {{ request()->routeIs('shop.notifications.index') ? 'text-rose-600 bg-rose-50 dark:text-rose-300 dark:bg-slate-800' : 'text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                        <i data-lucide="bell" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>
                        {{ $isAr ? 'الإشعارات' : 'Notifications' }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('shop.orders.index') }}" class="flex items-center p-2 rounded-lg {{ request()->routeIs('shop.orders.index') ? 'text-rose-600 bg-rose-50 dark:text-rose-300 dark:bg-slate-800' : 'text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                        <i data-lucide="clipboard-list" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>
                        {{ $isAr ? 'الطلبات' : 'Orders' }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('shop.special_orders.create') }}" class="flex items-center p-2 rounded-lg {{ request()->routeIs('shop.special_orders.*') ? 'text-rose-600 bg-rose-50 dark:text-rose-300 dark:bg-slate-800' : 'text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                        <i data-lucide="star" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>
                        {{ $isAr ? 'طلب خاص' : 'Special Order' }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('shop.messages.index') }}" class="flex items-center p-2 rounded-lg {{ request()->routeIs('shop.messages.index') ? 'text-rose-600 bg-rose-50 dark:text-rose-300 dark:bg-slate-800' : 'text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                        <div class="flex items-center gap-2 flex-1">
                            <i data-lucide="message-square" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>
                            <span>{{ $isAr ? 'الرسائل' : 'Messages' }}</span>
                        </div>
                        @if($unreadMessageCount > 0)
                            <span class="inline-flex items-center justify-center min-w-[18px] h-5 px-1.5 rounded-full text-[11px] font-semibold bg-rose-500 text-white">
                                {{ $unreadMessageCount > 99 ? '99+' : $unreadMessageCount }}
                            </span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('shop.account') }}" class="flex items-center p-2 rounded-lg {{ request()->routeIs('shop.account') ? 'text-rose-600 bg-rose-50 dark:text-rose-300 dark:bg-slate-800' : 'text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                        <i data-lucide="user" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>
                        {{ $isAr ? 'الحساب' : 'Account' }}
                    </a>
                </li>
            </ul>
        </div>

        <div>
            <h3 class="px-2 mb-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $isAr ? 'الإعدادات' : 'Settings' }}</h3>
            <ul class="space-y-1">
                <li>
                    <a href="#" class="flex items-center p-2 rounded-lg text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800">
                        <i data-lucide="wrench" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>
                        {{ $isAr ? 'الدعم الفني' : 'Technical Support' }}
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center p-2 rounded-lg text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800">
                        <i data-lucide="shield" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>
                        {{ $isAr ? 'الخصوصية والسياسات' : 'Privacy and Policies' }}
                    </a>
                </li>
                <li class="flex items-center justify-between p-2 text-gray-800 dark:text-slate-200">
                    <div class="flex items-center"><i data-lucide="moon" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>{{ $isAr ? 'الوضع الليلي' : 'Dark Mode' }}</div>
                    <label for="dark-mode-toggle-desktop" class="inline-flex relative items-center cursor-pointer">
                        <input type="checkbox" value="" id="dark-mode-toggle-desktop" class="dark-mode-toggle sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-rose-400"></div>
                    </label>
                </li>
                <li>
                    <a href="{{ route('shop.returns.index') }}" class="flex items-center p-2 rounded-lg {{ request()->routeIs('shop.returns.index') ? 'text-rose-600 bg-rose-50 dark:text-rose-300 dark:bg-slate-800' : 'text-gray-800 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                        <i data-lucide="undo-2" class="w-5 h-5 {{ $isAr ? 'ml-3' : 'mr-3' }} text-gray-400 dark:text-slate-400"></i>
                        {{ $isAr ? 'إرجاع طلب' : 'Return an Order' }}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="p-4 border-t border-gray-200 dark:border-slate-800">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 p-3 bg-gray-100 text-gray-900 dark:bg-slate-800 dark:text-white text-center rounded-lg font-semibold hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors">
                <i data-lucide="log-out" class="w-5 h-5"></i>
                <span>{{ $isAr ? 'تسجيل الخروج' : 'Logout' }}</span>
            </button>
        </form>
    </div>
</nav>
