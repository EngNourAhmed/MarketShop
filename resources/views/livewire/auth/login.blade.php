<x-layouts.auth>
    <div class="w-full max-w-md">
        <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur rounded-2xl shadow-2xl p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-tr from-[#FFF687] to-[#F6416C] bg-clip-text text-transparent">Trady</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">سجّل دخولك للوصول للوحة الإدارة.</p>
                </div>

                <label for="dark-mode-toggle" class="inline-flex relative items-center cursor-pointer">
                    <input type="checkbox" value="" id="dark-mode-toggle" class="sr-only peer" />
                    <div
                        class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-rose-400"
                    ></div>
                </label>
            </div>

            @if (session('status'))
                <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm text-center">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 text-red-700 rounded-lg text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-200">البريد الإلكتروني</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="example@example.example"
                        pattern="[^@\s]+@[^@\s]+\.[^@\s]+"
                        title="example@example.example"
                        class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400"
                        required
                        autofocus
                        autocomplete="email"
                    />
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="font-semibold text-gray-700 dark:text-gray-200">كلمة المرور</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm underline text-rose-600">نسيت كلمة المرور؟</a>
                        @endif
                    </div>
                    <div class="relative">
                        <input
                            id="login-password"
                            type="password"
                            name="password"
                            class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400 pr-10"
                            required
                            autocomplete="current-password"
                        />
                        <button
                            type="button"
                            id="toggle-login-password"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-200 text-sm"
                        >
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                    <input type="checkbox" name="remember" value="1" class="rounded" @checked(old('remember'))>
                    <span>تذكرني</span>
                </label>

                <button
                    type="submit"
                    class="w-full p-3 bg-gradient-to-r from-rose-500 to-orange-400 text-white rounded-lg font-semibold hover:opacity-95 transition-opacity"
                >
                    تسجيل الدخول
                </button>
            </form>

            @if (Route::has('register'))
                <div class="mt-6 text-sm text-gray-600 dark:text-gray-300">
                    ليس لديك حساب؟
                    <a href="{{ route('register') }}" class="font-semibold underline text-rose-600">إنشاء حساب</a>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var input = document.getElementById('login-password');
            var btn = document.getElementById('toggle-login-password');
            if (!input || !btn) return;

            btn.addEventListener('click', function () {
                var icon = btn.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    if (icon) {
                        icon.setAttribute('data-lucide', 'eye-off');
                    }
                } else {
                    input.type = 'password';
                    if (icon) {
                        icon.setAttribute('data-lucide', 'eye');
                    }
                }

                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            });
        });
    </script>
</x-layouts.auth>
