@props(['routing'])

<div class="flex flex-col gap-3">
    <h2 class="text-lg font-semibold">Routing</h2>
    <div class="flex flex-col">
        @php
            $routingList = $routing ?? [];
            $hasRouting = count($routingList) > 0;
        @endphp
        @if ($hasRouting)
            @php
                foreach ($routingList as $rk => $rv) {
                    echo '<div class="flex max-w-full items-baseline gap-2 h-10 text-sm font-mono">';
                    echo '<div class="uppercase text-neutral-500 dark:text-neutral-400 shrink-0">' . e($rk) . '</div>';
                    echo '<div class="min-w-6 grow h-3 border-b-2 border-dotted border-neutral-300 dark:border-white/20"></div>';
                    echo '<div class="truncate text-neutral-900 dark:text-white">';
                    echo '<span data-tippy-content="' . e($rv) . '">' . e($rv) . '</span>';
                    echo '</div></div>';
                }
            @endphp
        @else
            <x-laravel-exceptions-renderer::empty-state message="No routing context" />
        @endif
    </div>
</div>
