<x-layouts.app :title="'Message Notifications'">
    <div class="page-content">
        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm p-4 md:p-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-gray-100">
                    تنبيهات رسائل المستخدمين
                </h1>
            </div>

            @if(($notifications ?? collect())->isEmpty())
                <div class="py-10 text-center text-sm text-gray-500 dark:text-gray-300">
                    لا يوجد رسائل جديدة من المستخدمين حالياً.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($notifications as $item)
                        @php($user = $item['user'] ?? null)
                        @php($unread = (int) ($item['unread_count'] ?? 0))
                        @php($message = $item['latest_message'] ?? null)
                        @if($user)
                            <a href="{{ route('messages.index', ['user_id' => $user->id]) }}" class="block rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/70 transition">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold bg-rose-500/10 text-rose-600 dark:bg-rose-500/20 dark:text-rose-200">
                                            {{ mb_substr($user->name, 0, 2) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $user->name }}</span>
                                                @if($unread > 0)
                                                    <span class="inline-flex items-center justify-center min-w-[18px] h-5 px-1.5 rounded-full text-[11px] font-semibold bg-rose-500 text-white">
                                                        {{ $unread > 99 ? '99+' : $unread }} جديدة
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</span>
                                            @if($message)
                                                <div class="mt-1 text-xs text-gray-600 dark:text-gray-300 line-clamp-2">
                                                    {{ $message->body }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if($message)
                                        <div class="text-[11px] text-gray-400 dark:text-gray-500 whitespace-nowrap ml-2">
                                            {{ $message->created_at?->format('Y-m-d H:i') }}
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
