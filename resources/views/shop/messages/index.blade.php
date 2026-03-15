<x-shop-layouts.app :title="'Support Chat'">
    @php($messages = $messages ?? collect())
    @php($showConversation = $messages->isNotEmpty() || request()->boolean('start'))

    <div class="space-y-6">
        @if(!$showConversation)
            <div class="rounded-2xl bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-50 border border-slate-200 dark:border-slate-800 shadow-lg p-6 flex flex-col items-center justify-center min-h-[260px]">
                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                    <i data-lucide="message-circle" class="w-8 h-8 text-slate-500 dark:text-slate-400"></i>
                </div>
                <div class="text-xl font-bold mb-1">No Support Messages Yet</div>
                <div class="text-sm text-slate-600 dark:text-slate-300 mb-5 text-center max-w-md">
                    Start a conversation with the <span class="font-semibold">admin team</span> to ask questions or request a custom order.
                </div>
                <a href="{{ route('shop.messages.index', ['start' => 1]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-yellow-300 to-orange-500 text-slate-900 font-semibold shadow-md hover:opacity-95">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                    <span>Start Chat with Admin</span>
                </a>
            </div>
        @else
            <div class="rounded-2xl bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-50 border border-slate-200 dark:border-slate-800 shadow-lg p-4 md:p-6 flex flex-col min-h-[260px]">
                <div class="flex items-center gap-3 mb-4">
                    <a href="{{ route('customer.home') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700">
                        <i data-lucide="arrow-left" class="w-5 h-5 text-slate-600 dark:text-slate-400"></i>
                    </a>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-slate-900 font-bold text-sm">
                            <i data-lucide="sparkles" class="w-4 h-4"></i>
                        </div>
                        <div class="text-base md:text-lg font-semibold">Admin Support</div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto space-y-6">
                    <div class="text-center py-8 px-4">
                        <div class="mb-3 flex items-center justify-center">
                            <div class="w-12 h-12 rounded-full bg-rose-500/10 flex items-center justify-center">
                                <i data-lucide="file-text" class="w-6 h-6 text-rose-500 dark:text-rose-400"></i>
                            </div>
                        </div>
                        <div class="text-lg font-semibold mb-1">New message to admin</div>
                        <div class="text-sm text-slate-600 dark:text-slate-300 max-w-xl mx-auto">
                            Tell the admin what you need help with. You can describe your order, ask questions, or request a special product.
                        </div>
                    </div>

                    <div class="space-y-3 px-1 md:px-2">
                        @forelse($messages as $message)
                            @php($isUser = $message->sender_role === 'user')
                            <div class="flex {{ $isUser ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-xs md:max-w-sm rounded-2xl px-3 py-2 text-sm {{ $isUser ? 'bg-rose-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-100' }}">
                                    <div>{{ $message->body }}</div>
                                    <div class="mt-1 text-[10px] {{ $isUser ? 'text-rose-100/80 text-left' : 'text-slate-400 dark:text-slate-500 text-right' }}">
                                        {{ $message->created_at?->format('Y-m-d H:i') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-sm text-slate-400 dark:text-slate-500 mb-4">No messages yet. Start by sending a message to the admin.</div>
@endforelse
                    </div>
                </div>

                <form method="POST" action="{{ route('shop.messages.store') }}" class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center gap-2">
                    @csrf
                    <input
                        type="text"
                        name="body"
                        class="flex-1 h-11 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 text-sm text-slate-900 dark:text-slate-50 placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-rose-500"
                        placeholder="Type your message..."
                        autocomplete="off"
                        required
                    />
                    <button type="submit" class="inline-flex items-center justify-center w-11 h-11 rounded-full bg-rose-500 text-white hover:bg-rose-600 shadow-md">
                        <i data-lucide="send" class="w-5 h-5"></i>
                    </button>
                </form>
            </div>
        @endif
    </div>
</x-shop-layouts.app>
