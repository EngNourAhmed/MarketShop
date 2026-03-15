<x-layouts.app :title="__('purchase')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                    <thead class="bg-neutral-50 dark:bg-neutral-900">
                        <tr>
                            <th class="px-4 py-3 text-start text-sm font-semibold text-neutral-700 dark:text-neutral-200">ID</th>
                            <th class="px-4 py-3 text-start text-sm font-semibold text-neutral-700 dark:text-neutral-200">Supplier</th>
                            <th class="px-4 py-3 text-start text-sm font-semibold text-neutral-700 dark:text-neutral-200">Purchase Date</th>
                            <th class="px-4 py-3 text-start text-sm font-semibold text-neutral-700 dark:text-neutral-200">Total</th>
                            <th class="px-4 py-3 text-start text-sm font-semibold text-neutral-700 dark:text-neutral-200">Payment Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach(($purchases ?? []) as $purchase)
                            <tr>
                                <td class="px-4 py-3 text-sm text-neutral-800 dark:text-neutral-200">{{ $purchase->id }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-800 dark:text-neutral-200">{{ optional($purchase->supplier)->name }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-800 dark:text-neutral-200">{{ $purchase->purchase_date }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-800 dark:text-neutral-200">{{ $purchase->total }}</td>
                                <td class="px-4 py-3 text-sm text-neutral-800 dark:text-neutral-200">{{ $purchase->payment_status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4">
                @if(method_exists(($purchases ?? null), 'links'))
                    {{ $purchases->links() }}
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
