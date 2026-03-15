<x-layouts.app :title="'المبيعات'">
    <div id="sales-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">المبيعات</h1>

        @php($chartLabels = collect($monthly_sales ?? [])->map(fn($r) => (($r['month'] ?? '') . ' ' . ($r['year'] ?? '')))->values())
        @php($chartTotals = collect($monthly_sales ?? [])->map(fn($r) => (float) ($r['total'] ?? 0))->values())

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 flex items-center">
                <div class="flex items-center justify-between w-full">
                    <div class="text-right">
                        <div class="text-sm text-gray-500 dark:text-gray-400">عملاء جدد</div>
                        <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format((int) ($kpis['new_customers'] ?? 0), 0, '.', ',') }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-500/20 flex items-center justify-center">
                        <i data-lucide="star" class="w-6 h-6 text-orange-500"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 flex items-center">
                <div class="flex items-center justify-between w-full">
                    <div class="text-right">
                        <div class="text-sm text-gray-500 dark:text-gray-400">الفواتير الصادرة</div>
                        <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format((int) ($kpis['invoices_count'] ?? 0), 0, '.', ',') }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                        <i data-lucide="shopping-cart" class="w-6 h-6 text-blue-500"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 flex items-center">
                <div class="flex items-center justify-between w-full">
                    <div class="text-right">
                        <div class="text-sm text-gray-500 dark:text-gray-400">إجمالي الدخل</div>
                        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-300">{{ number_format((float) ($kpis['income_total'] ?? 0), 2, '.', ',') }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                        <i data-lucide="wallet" class="w-6 h-6 text-emerald-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-3">
                <div class="text-lg font-bold text-gray-800 dark:text-white">ملخص المبيعات الشهري</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">آخر {{ count($monthly_sales ?? []) }} شهر</div>
            </div>
            <div class="rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-700 p-3 h-56">
                <canvas id="monthlySalesChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="text-lg font-bold text-gray-800 dark:text-white mb-3">أفضل 10 عملاء (المشترين)</div>
                <div class="space-y-2">
                    @foreach(($top_customers ?? []) as $row)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700 px-3 py-2">
                            <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $row['name'] ?? '-' }}</div>
                            <div class="text-rose-600 dark:text-rose-300 font-bold">{{ number_format((float) ($row['total'] ?? 0), 2, '.', ',') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="text-lg font-bold text-gray-800 dark:text-white mb-3">أفضل 10 دول (المشترين)</div>
                <div class="space-y-2">
                    @foreach(($top_countries ?? []) as $row)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700 px-3 py-2">
                            <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $row['name'] ?? '-' }}</div>
                            <div class="text-rose-600 dark:text-rose-300 font-bold">{{ number_format((int) ($row['total'] ?? 0), 0, '.', ',') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            (function () {
                var el = document.getElementById('monthlySalesChart');
                if (!el || typeof Chart === 'undefined') return;

                var labels = @json($chartLabels);
                var totals = @json($chartTotals);

                var isDark = document.documentElement.classList.contains('dark');
                var tickColor = isDark ? '#cbd5e1' : '#6b7280';
                var gridColor = isDark ? 'rgba(148, 163, 184, 0.12)' : 'rgba(107, 114, 128, 0.15)';

                new Chart(el, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'المبيعات',
                            data: totals,
                            borderColor: '#34d399',
                            backgroundColor: 'rgba(52, 211, 153, 0.15)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 2,
                            pointHoverRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                rtl: true
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: tickColor },
                                grid: { color: gridColor }
                            },
                            y: {
                                ticks: { color: tickColor },
                                grid: { color: gridColor }
                            }
                        }
                    }
                });
            })();
        </script>
    </div>
</x-layouts.app>