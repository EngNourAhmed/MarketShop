<x-layouts.app :title="__('reports')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
            <div class="text-sm text-neutral-700 dark:text-neutral-200">
                <div class="font-semibold mb-3">Available reports</div>
                <ul class="list-disc ps-6 space-y-1">
                    @foreach(($available_reports ?? []) as $r)
                        <li>{{ $r }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-layouts.app>