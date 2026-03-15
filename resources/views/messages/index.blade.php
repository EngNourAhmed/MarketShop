<x-layouts.app :title="'Admin Messages'">
    <div class="page-content">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-4">
                <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="font-semibold text-gray-800 dark:text-gray-100">المستخدمون</div>
                    </div>
                    <div class="px-3 pt-2">
                        <div class="relative">
                            <input
                                id="admin-messages-user-search"
                                type="text"
                                placeholder="ابحث عن مستخدم بالاسم أو الإيميل..."
                                class="w-full rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 my-2 px-3 py-1.5 pr-8 text-xs text-gray-800 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-rose-400"
                                autocomplete="off"
                            />
                            <div class="absolute inset-y-0 right-2 flex items-center pointer-events-none">
                                <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    <div class="max-h-[420px] overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800" data-users-list>
                        @forelse($users as $user)
                            @php($isActive = $activeUser && $activeUser->id === $user->id)
                            @php($unread = (int) ($user->unread_messages_count ?? 0))
                            @php($latest = ($latestMessages ?? collect())->get($user->id))
                            @php($preview = $latest ? (string) ($latest->body ?? '') : '')
                            @php($preview = ($preview !== '' && mb_strlen($preview) > 45) ? (mb_substr($preview, 0, 45) . '...') : $preview)
                            <a href="{{ route('messages.index', ['user_id' => $user->id]) }}" data-user-item data-search="{{ mb_strtolower(trim(($user->name ?? '') . ' ' . ($user->email ?? ''))) }}" class="flex items-center justify-between px-4 py-3 text-[15px] {{ $isActive ? 'bg-gradient-to-r from-rose-400 to-orange-300 text-white' : 'hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold {{ $isActive ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-700' }}">
                                        {{ mb_substr($user->name, 0, 2) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-semibold truncate max-w-[170px] text-[15px]">{{ $user->name }}</span>
                                        <span class="text-[12px] {{ $isActive ? 'text-white/80' : 'text-gray-500 dark:text-gray-400' }}">{{ $user->email }}</span>
                                        @if($preview !== '')
                                            <span class="mt-0.5 text-[12px] {{ $isActive ? 'text-white/80' : 'text-gray-400 dark:text-gray-500' }} truncate max-w-[200px]">
                                                {{ $preview }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if($unread > 0)
                                    <span class="inline-flex items-center justify-center min-w-[18px] h-5 px-1.5 rounded-full text-[11px] font-semibold {{ $isActive ? 'bg-white/20 text-white' : 'bg-rose-500 text-white' }}">
                                        {{ $unread > 99 ? '99+' : $unread }}
                                    </span>
                                @endif
                            </a>
                        @empty
                            <div class="px-4 py-6 text-sm text-gray-500 dark:text-gray-300 text-center">لا يوجد رسائل بعد.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="md:col-span-8">
                <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm min-h-[320px] flex flex-col">
                    @if($activeUser)
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $activeUser->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $activeUser->email }}</div>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto px-4 py-3 space-y-3 bg-gray-50 dark:bg-gray-900/20">
                            @forelse($messages as $message)
                                @php($isUser = $message->sender_role === 'user')
                                <div class="flex {{ $isUser ? 'justify-start' : 'justify-end' }}">
                                    <div class="max-w-md rounded-2xl px-3 py-2 text-sm {{ $isUser ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-50' : 'bg-rose-500 text-white' }}">
                                        <div>{{ $message->body }}</div>
                                        <div class="mt-1 text-[11px] opacity-80 {{ $isUser ? 'text-right' : 'text-left' }}">
                                            {{ $message->created_at?->format('h:i A') }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-sm text-gray-500 dark:text-gray-300 mt-10">لا توجد رسائل لهذا المستخدم حتى الآن.</div>
                            @endforelse
                        </div>

                        <form method="POST" action="{{ route('messages.store') }}" class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 flex items-center gap-2">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $activeUser->id }}" />
                            <input
                                type="text"
                                name="body"
                                class="flex-1 h-10 rounded-full bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 px-4 text-sm text-gray-900 dark:text-gray-50 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-rose-500"
                                placeholder="اكتب ردك هنا..."
                                autocomplete="off"
                                required
                            />
                            <button type="submit" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-rose-500 text-white hover:bg-rose-600">
                                <i data-lucide="send" class="w-4 h-4"></i>
                            </button>
                        </form>
                    @else
                        <div class="flex-1 flex items-center justify-center text-sm text-gray-500 dark:text-gray-300 p-6">
                            لا يوجد مستخدمون لديهم رسائل بعد.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <script>
            (function() {
                var input = document.getElementById('admin-messages-user-search');
                if (!input) return;

                var list = document.querySelector('[data-users-list]');
                if (!list) return;

                var items = Array.prototype.slice.call(list.querySelectorAll('[data-user-item]'));

                var handler = function() {
                    var q = (input.value || '').trim().toLowerCase();
                    items.forEach(function(el) {
                        var hay = (el.getAttribute('data-search') || '').toLowerCase();
                        if (!q || hay.indexOf(q) !== -1) {
                            el.style.display = '';
                        } else {
                            el.style.display = 'none';
                        }
                    });
                };

                input.addEventListener('input', handler);
            })();
        </script>
    </div>
</x-layouts.app>
