<x-layouts.auth>
    <div class="w-full max-w-md">
        <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur rounded-2xl shadow-2xl p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تأكيد البريد الإلكتروني</h1>
                    <p class="text-sm text-gray-500 mt-1">تحقق من بريدك الإلكتروني واضغط على رابط التفعيل.</p>
                </div>

                <label for="dark-mode-toggle" class="inline-flex relative items-center cursor-pointer">
                    <input type="checkbox" value="" id="dark-mode-toggle" class="sr-only peer" />
                    <div
                        class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-rose-400"
                    ></div>
                </label>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm text-center">
                    تم إرسال رابط تفعيل جديد إلى بريدك الإلكتروني.
                </div>
            @endif

            <div class="space-y-3">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full p-3 bg-gradient-to-r from-rose-500 to-orange-400 text-white rounded-lg font-semibold hover:opacity-95 transition-opacity"
                    >
                        إعادة إرسال رابط التفعيل
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.auth>
