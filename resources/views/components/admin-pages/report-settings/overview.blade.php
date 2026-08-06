<div class="space-y-6">

    <!-- HEADER WITH CONTROLS -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#0C2C6B]">
                Report & Settings Overview
            </h2>
            <p class="text-sm text-gray-500">
                Financial performance and system settings overview for <span class="font-semibold text-gray-700" x-text="selectedYear">{{ $selectedYear }}</span>
            </p>
        </div>

        <!-- ACTIONS: YEAR FILTER & REFRESH -->
        <div class="flex items-center gap-3">
            <select wire:model.live="selectedYear"
                class="bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] cursor-pointer">
                @foreach($availableYears as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>

            <button wire:click="refreshAnalytics" wire:loading.attr="disabled" title="Refresh Analytics"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl shadow-sm transition cursor-pointer">
                <span wire:loading.remove wire:target="refreshAnalytics">🔄</span>
                <span wire:loading wire:target="refreshAnalytics" class="animate-spin">⏳</span>
                <span class="hidden sm:inline">Refresh</span>
            </button>
        </div>
    </div>

    <!-- KPI CARDS -->
    @php
        $cards = [
            ['Total Income', '₱ ' . number_format($totalIncome, 2), '💰', 'bg-blue-600'],
            ['Reports / Entries', $totalReportsGenerated, '📊', 'bg-indigo-600'],
            ['Total Users', $totalUsers, '👥', 'bg-purple-600'],
            ['Active Accounts', $activeAccounts, '✅', 'bg-green-600'],
            ['System Settings', $totalSettingsCount, '⚙️', 'bg-orange-600'],
        ];
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-4">
        @foreach($cards as $card)
            <div class="rounded-xl text-white p-5 shadow-lg transition-transform hover:-translate-y-1 {{ $card[3] }}">
                <div class="text-3xl mb-2">
                    {{ $card[2] }}
                </div>
                <p class="text-xs uppercase tracking-wider opacity-80 font-medium">
                    {{ $card[0] }}
                </p>
                <h3 class="text-xl sm:text-2xl font-bold mt-1">
                    {{ $card[1] }}
                </h3>
            </div>
        @endforeach
    </div>

    <!-- SECOND ROW: RECENT TRANSACTIONS & INCOME SOURCES -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- RECENT LEDGER TRANSACTIONS -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-[#0C2C6B] text-lg mb-4 flex items-center gap-2">
                    Recent Ledger Activities
                </h3>
                <div class="divide-y divide-gray-100">
                    @forelse($recentTransactions as $tx)
                        <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">
                                    Source: <span class="text-[#0C2C6B]">{{ $tx->Source }}</span>
                                    <span class="text-xs text-gray-500 font-normal">(Ref: {{ $tx->RefID }})</span>
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $tx->created_at ? $tx->created_at->diffForHumans() : 'N/A' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="block font-bold text-sm text-green-600">
                                    +₱{{ number_format($tx->amount, 2) }}
                                </span>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600">
                                    {{ $tx->status }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 py-4 text-center">No ledger activity found for this year.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- INCOME SOURCE DISTRIBUTION -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-[#0C2C6B] text-lg mb-4">
                Income Sources Breakdown
            </h3>
            <div class="divide-y divide-gray-100 max-h-[250px] overflow-y-auto pr-1">
                @forelse($incomeSources as $source)
                    <div class="flex justify-between items-center py-2.5 text-sm">
                        <div>
                            <span class="font-semibold text-gray-800">{{ $source->Source }}</span>
                            <span class="text-xs text-gray-400 ml-2">({{ $source->count }} entries)</span>
                        </div>
                        <span class="font-bold text-[#0C2C6B] bg-blue-50 px-3 py-1 rounded-lg text-xs">
                            ₱{{ number_format($source->total_amount, 2) }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 py-4 text-center">No income source metrics available.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- CHARTS SECTION: MONTHLY INCOME -->
    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6">
            <h3 class="font-bold text-[#0C2C6B] text-lg mb-4">
                Monthly Income Analytics (₱)
            </h3>
            <div class="relative h-64 sm:h-80 w-full">
                <canvas id="monthlyIncomeChart"></canvas>
            </div>
        </div>
    </div>

</div>

<!-- CHART INITIALIZATION WITH LIVEWIRE RE-RENDER SUPPORT -->
<script>
    let monthlyIncomeChartInstance = null;

    function renderMonthlyIncomeChart() {
        const ctx = document.getElementById('monthlyIncomeChart');
        if (!ctx) return;

        const monthlyLabels = @json($monthlyIncome->pluck('month'));
        const monthlyData = @json($monthlyIncome->pluck('total'));

        if (monthlyIncomeChartInstance) {
            monthlyIncomeChartInstance.destroy();
        }

        monthlyIncomeChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Total Income (₱)',
                    data: monthlyData,
                    backgroundColor: '#0C2C6B',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', renderMonthlyIncomeChart);
    document.addEventListener('livewire:initialized', () => {
        renderMonthlyIncomeChart();
        Livewire.hook('morph.updated', ({ el, component }) => {
            renderMonthlyIncomeChart();
        });
    });
</script>
