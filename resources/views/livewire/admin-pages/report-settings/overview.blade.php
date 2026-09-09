<div class="space-y-6">

    <!-- HEADER WITH CONTROLS -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#0C2C6B]">
                Report & Settings Overview
            </h2>
            <p class="text-sm text-gray-500">
                Financial performance and system settings overview for <span class="font-semibold text-gray-700">{{ $selectedYear }}</span>
            </p>
        </div>

        <!-- ACTIONS: YEAR FILTER (2020 - Current Year) & REFRESH -->
        <div class="flex items-center gap-3">
            <select wire:model.live="selectedYear"
                class="bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] cursor-pointer">
                @foreach($availableYears as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>

            {{-- REFRESH ANALYTICS --}}
            <button
                type="button"
                wire:click="refreshAnalytics"
                wire:loading.attr="disabled"
                title="Refresh Analytics"
                class="refresh-btn"
            >
                {{-- NORMAL STATE --}}
                <span
                    wire:loading.remove
                    wire:target="refreshAnalytics"
                    class="refresh-icon"
                >
                    <svg
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 4v5h5M20 20v-5h-5M5.5 9A7 7 0 0 1 18 6.5L20 9M18.5 15A7 7 0 0 1 6 17.5L4 15"
                        />
                    </svg>
                </span>

                {{-- LOADING STATE --}}
                <span
                    wire:loading
                    wire:target="refreshAnalytics"
                    class="refresh-loading"
                >
                    <svg
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                            stroke-width="3"
                            class="refresh-circle"
                        />
                        <path
                            d="M21 12a9 9 0 0 1-9 9"
                            stroke-width="3"
                            stroke-linecap="round"
                        />
                    </svg>
                </span>

                <span class="hidden sm:inline">Refresh</span>
            </button>
        </div>
    </div>

    <!-- KPI CARDS -->
    @php
        $cards = [
            ['Total Income', '₱ ' . number_format($totalIncome, 2), '💰', 'hover:shadow-blue-500/60'],
            ['Reports / Entries', $totalReportsGenerated, '📊', 'hover:shadow-blue-500/60'],
            ['Total Users', $totalUsers, '👥', 'hover:shadow-blue-500/60'],
            ['Active Accounts', $activeAccounts, '✅', 'hover:shadow-blue-500/60'],
            ['System Settings', $totalSettingsCount, '⚙️', 'hover:shadow-blue-500/60'],
        ];
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-4">
        @foreach($cards as $card)
            <div class="relative overflow-hidden rounded-2xl p-5 backdrop-blur-xl bg-gradient-to-br from-[#0C2C6B]/90 via-[#133880]/80 to-[#1b469b]/70 border border-white/10 {{ $card[3] }} shadow-xl transition-all duration-300 hover:-translate-y-1.5 group text-white">
                <div class="absolute -right-3 -bottom-3 text-7xl text-white opacity-10 transition-transform duration-500 group-hover:scale-125 group-hover:opacity-20 pointer-events-none select-none brightness-0 invert">
                    {{ $card[2] }}
                </div>
                <div class="relative z-10">
                    <p class="text-xs uppercase tracking-wider text-gray-300 font-semibold opacity-90">
                        {{ $card[0] }}
                    </p>
                    <h3 class="text-xl sm:text-2xl font-bold mt-1 text-white tracking-tight drop-shadow-sm truncate">
                        {{ $card[1] }}
                    </h3>
                </div>
            </div>
        @endforeach
    </div>

    <!-- SECOND ROW: BREAKDOWN CARDS (3 COLUMNS) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- 1. MEMBERSHIP FEE COLLECTION BREAKDOWN -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <div>
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-[#0C2C6B] text-lg">
                            Membership Fee Breakdown
                        </h3>
                        <button
                            onclick="printCard('membership-fee')"
                            title="Print this section"
                            class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-blue-600 transition text-xs cursor-pointer shrink-0"
                        >
                            <span class="text-xl">
                                🖨️
                            </span>
                        </button>
                    </div>
                    <div wire:ignore x-data="{
                        startDate: @entangle('membershipFeeStart').live,
                        endDate: @entangle('membershipFeeEnd').live,
                        picker: null,
                        init() {
                            this.picker = flatpickr(this.$refs.input, {
                                mode: 'range',
                                dateFormat: 'Y-m-d',
                                altInput: true,
                                altFormat: 'm/d/Y',
                                defaultDate: [this.startDate, this.endDate],
                                onChange: (selectedDates, dateStr, instance) => {
                                    if (selectedDates.length === 2) {
                                        this.startDate = instance.formatDate(selectedDates[0], 'Y-m-d');
                                        this.endDate = instance.formatDate(selectedDates[1], 'Y-m-d');
                                    }
                                }
                            });

                            this.$watch('startDate', () => this.$nextTick(() => this.updatePicker()));
                            this.$watch('endDate', () => this.$nextTick(() => this.updatePicker()));

                            window.addEventListener('ranges-reset', () => {
                                this.$nextTick(() => this.updatePicker());
                            });
                        },
                        updatePicker() {
                            if (this.picker && this.startDate && this.endDate) {
                                this.picker.setDate([this.startDate, this.endDate], false);
                            }
                        }
                    }">
                        <input type="text"
                            x-ref="input"
                            class="bg-white border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg px-3 py-1.5 shadow-sm focus:outline-none focus:ring-1 focus:ring-[#0C2C6B] cursor-pointer w-full text-center"
                            placeholder="Select date range">
                    </div>
                </div>

                <div class="divide-y divide-gray-100 max-h-[250px] overflow-y-auto pr-1">
                    @forelse($membershipFeeCollections as $fee)
                        <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{ $fee->FeeName }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    Fee ID: {{ $fee->FeeID }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="block font-bold text-sm text-green-600">
                                    ₱{{ number_format($fee->Total_Collection, 2) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 py-4 text-center">No membership fees found for this period.</p>
                    @endforelse
                </div>
            </div>

            <!-- PRINT-ONLY TABLE -->
            <div data-print-section="membership-fee" class="hidden">
                <div class="text-center font-sans mb-4 pb-3 border-b-2 border-[#1b365d]">
                    <h1 class="m-0 text-2xl font-bold text-[#1b365d] font-serif">TRUELIFE</h1>
                    <p class="my-1 text-[11px] font-bold text-[#c5a059] uppercase tracking-wider">&mdash; S O L I D A R I T A S &mdash;</p>
                    <h2 class="mt-2.5 mb-1 text-sm uppercase text-slate-800 font-bold">MEMBERSHIP FEE COLLECTION BREAKDOWN</h2>
                    <p class="text-xs text-slate-600">
                        Period: {{ $membershipFeeStartLabel }} &mdash; {{ $membershipFeeEndLabel }}
                    </p>
                </div>
                <table class="w-full border-collapse font-sans text-[11px]">
                    <thead>
                        <tr class="bg-[#0C2C6B] text-white font-bold text-center h-[24px]">
                            <th class="border border-slate-900 p-1">#</th>
                            <th class="border border-slate-900 p-1">Fee ID</th>
                            <th class="border border-slate-900 p-1">Fee Name</th>
                            <th class="border border-slate-900 p-1">Total Collection</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($membershipFeeCollections as $index => $fee)
                            <tr>
                                <td class="border border-slate-900 p-1 text-center">{{ $index + 1 }}</td>
                                <td class="border border-slate-900 p-1 text-center">{{ $fee->FeeID }}</td>
                                <td class="border border-slate-900 p-1">{{ $fee->FeeName }}</td>
                                <td class="border border-slate-900 p-1 text-right">₱{{ number_format($fee->Total_Collection, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="border border-slate-900 p-2 text-center">No data for the selected period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. COLLECTION SUMMARY BY SOURCE -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <div>
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-[#0C2C6B] text-lg">
                            Collection Summary by Source
                        </h3>
                        <button
                            onclick="printCard('source')"
                            title="Print this section"
                            class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-blue-600 transition text-xs cursor-pointer shrink-0"
                        >
                            <span class="text-xl">
                                🖨️
                            </span>
                        </button>
                    </div>
                    <div wire:ignore x-data="{
                        startDate: @entangle('sourceStart').live,
                        endDate: @entangle('sourceEnd').live,
                        picker: null,
                        init() {
                            this.picker = flatpickr(this.$refs.input, {
                                mode: 'range',
                                dateFormat: 'Y-m-d',
                                altInput: true,
                                altFormat: 'm/d/Y',
                                defaultDate: [this.startDate, this.endDate],
                                onChange: (selectedDates, dateStr, instance) => {
                                    if (selectedDates.length === 2) {
                                        this.startDate = instance.formatDate(selectedDates[0], 'Y-m-d');
                                        this.endDate = instance.formatDate(selectedDates[1], 'Y-m-d');
                                    }
                                }
                            });

                            this.$watch('startDate', () => this.$nextTick(() => this.updatePicker()));
                            this.$watch('endDate', () => this.$nextTick(() => this.updatePicker()));

                            window.addEventListener('ranges-reset', () => {
                                this.$nextTick(() => this.updatePicker());
                            });
                        },
                        updatePicker() {
                            if (this.picker && this.startDate && this.endDate) {
                                this.picker.setDate([this.startDate, this.endDate], false);
                            }
                        }
                    }">
                        <input type="text"
                            x-ref="input"
                            class="bg-white border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg px-3 py-1.5 shadow-sm focus:outline-none focus:ring-1 focus:ring-[#0C2C6B] cursor-pointer w-full text-center"
                            placeholder="Select date range">
                    </div>
                </div>

                <div class="divide-y divide-gray-100 max-h-[250px] overflow-y-auto pr-1">
                    @forelse($incomeSources as $source)
                        <div class="flex justify-between items-center py-2.5 text-sm">
                            <div>
                                <span class="font-semibold text-gray-800">{{ $source->Collection_Type }}</span>
                                <span class="text-xs text-gray-400 ml-1">({{ $source->Source }})</span>
                                <span class="text-xs text-gray-400 ml-1">({{ $source->count }} entries)</span>
                            </div>
                            <span class="font-bold text-[#0C2C6B] bg-blue-50 px-3 py-1 rounded-lg text-xs">
                                ₱{{ number_format($source->Total_Collection, 2) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 py-4 text-center">No collection summary available for this period.</p>
                    @endforelse
                </div>
            </div>

            <!-- PRINT-ONLY TABLE -->
            <div data-print-section="source" class="hidden">
                <div class="text-center font-sans mb-4 pb-3 border-b-2 border-[#1b365d]">
                    <h1 class="m-0 text-2xl font-bold text-[#1b365d] font-serif">TRUELIFE</h1>
                    <p class="my-1 text-[11px] font-bold text-[#c5a059] uppercase tracking-wider">&mdash; S O L I D A R I T A S &mdash;</p>
                    <h2 class="mt-2.5 mb-1 text-sm uppercase text-slate-800 font-bold">COLLECTION SUMMARY BY SOURCE</h2>
                    <p class="text-xs text-slate-600">
                        Period: {{ $sourceStartLabel }} &mdash; {{ $sourceEndLabel }}
                    </p>
                </div>
                <table class="w-full border-collapse font-sans text-[11px]">
                    <thead>
                        <tr class="bg-[#0C2C6B] text-white font-bold text-center h-[24px]">
                            <th class="border border-slate-900 p-1">#</th>
                            <th class="border border-slate-900 p-1">Collection Type</th>
                            <th class="border border-slate-900 p-1">Source</th>
                            <th class="border border-slate-900 p-1">Entries</th>
                            <th class="border border-slate-900 p-1">Total Collection</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incomeSources as $index => $source)
                            <tr>
                                <td class="border border-slate-900 p-1 text-center">{{ $index + 1 }}</td>
                                <td class="border border-slate-900 p-1">{{ $source->Collection_Type }}</td>
                                <td class="border border-slate-900 p-1 text-center">{{ $source->Source }}</td>
                                <td class="border border-slate-900 p-1 text-center">{{ $source->count }}</td>
                                <td class="border border-slate-900 p-1 text-right">₱{{ number_format($source->Total_Collection, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="border border-slate-900 p-2 text-center">No data for the selected period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. WALLET INCENTIVES BREAKDOWN -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <div>
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-[#0C2C6B] text-lg">
                            Wallet Incentives Breakdown
                        </h3>
                        <button
                            onclick="printCard('wallet')"
                            title="Print this section"
                            class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-blue-600 transition text-xs cursor-pointer shrink-0"
                        >
                            <span class="text-xl">
                                🖨️
                            </span>
                        </button>
                    </div>
                    <div wire:ignore x-data="{
                        startDate: @entangle('walletStart').live,
                        endDate: @entangle('walletEnd').live,
                        picker: null,
                        init() {
                            this.picker = flatpickr(this.$refs.input, {
                                mode: 'range',
                                dateFormat: 'Y-m-d',
                                altInput: true,
                                altFormat: 'm/d/Y',
                                defaultDate: [this.startDate, this.endDate],
                                onChange: (selectedDates, dateStr, instance) => {
                                    if (selectedDates.length === 2) {
                                        this.startDate = instance.formatDate(selectedDates[0], 'Y-m-d');
                                        this.endDate = instance.formatDate(selectedDates[1], 'Y-m-d');
                                    }
                                }
                            });

                            this.$watch('startDate', () => this.$nextTick(() => this.updatePicker()));
                            this.$watch('endDate', () => this.$nextTick(() => this.updatePicker()));

                            window.addEventListener('ranges-reset', () => {
                                this.$nextTick(() => this.updatePicker());
                            });
                        },
                        updatePicker() {
                            if (this.picker && this.startDate && this.endDate) {
                                this.picker.setDate([this.startDate, this.endDate], false);
                            }
                        }
                    }">
                        <input type="text"
                            x-ref="input"
                            class="bg-white border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg px-3 py-1.5 shadow-sm focus:outline-none focus:ring-1 focus:ring-[#0C2C6B] cursor-pointer w-full text-center"
                            placeholder="Select date range">
                    </div>
                </div>

                <div class="divide-y divide-gray-100 max-h-[250px] overflow-y-auto pr-1">
                    @forelse($walletCollections as $item)
                        <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0 text-sm">
                            <div>
                                <p class="font-semibold text-gray-800">
                                    {{ $item->Member_Name }}
                                    <span class="text-xs font-medium text-blue-600">
                                        ({{ $item->referralCode }})
                                    </span>
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $item->Incentive_Type }} ({{ $item->Source }})
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="block font-bold text-sm text-green-600">
                                    ₱{{ number_format($item->Total_Collection, 2) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 py-4 text-center">No wallet incentives found for this period.</p>
                    @endforelse
                </div>
            </div>

            <!-- PRINT-ONLY TABLE -->
            <div data-print-section="wallet" class="hidden">
                <div class="text-center font-sans mb-4 pb-3 border-b-2 border-[#1b365d]">
                    <h1 class="m-0 text-2xl font-bold text-[#1b365d] font-serif">TRUELIFE</h1>
                    <p class="my-1 text-[11px] font-bold text-[#c5a059] uppercase tracking-wider">&mdash; S O L I D A R I T A S &mdash;</p>
                    <h2 class="mt-2.5 mb-1 text-sm uppercase text-slate-800 font-bold">WALLET INCENTIVES BREAKDOWN</h2>
                    <p class="text-xs text-slate-600">
                        Period: {{ $walletStartLabel }} &mdash; {{ $walletEndLabel }}
                    </p>
                </div>
                <table class="w-full border-collapse font-sans text-[11px]">
                    <thead>
                        <tr class="bg-[#0C2C6B] text-white font-bold text-center h-[24px]">
                            <th class="border border-slate-900 p-1">#</th>
                            <th class="border border-slate-900 p-1">Member Name</th>
                            <th class="border border-slate-900 p-1">Incentive Type</th>
                            <th class="border border-slate-900 p-1">Source</th>
                            <th class="border border-slate-900 p-1">Total Collection</th>
                            <th class="border border-slate-900 p-1">Member Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($walletCollections as $index => $item)
                            <tr>
                                <td class="border border-slate-900 p-1 text-center">{{ $index + 1 }}</td>
                                <td class="border border-slate-900 p-1">{{ $item->Member_Name }}</td>
                                <td class="border border-slate-900 p-1">{{ $item->Incentive_Type }}</td>
                                <td class="border border-slate-900 p-1 text-center">{{ $item->Source }}</td>
                                <td class="border border-slate-900 p-1 text-right">₱{{ number_format($item->Total_Collection, 2) }}</td>
                                <td class="border border-slate-900 p-1 text-right">₱{{ number_format($item->User_Total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="border border-slate-900 p-2 text-center">No data for the selected period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- CHARTS SECTION: MONTHLY INCOME -->
    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6"
             x-data="monthlyChart()"
             @chart-data-updated.window="updateChart($event.detail)">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                <h3 class="font-bold text-[#0C2C6B] text-lg">
                    Monthly Income Analytics (₱)
                </h3>
                {{-- PRINT MONTHLY --}}
                <button
                    onclick="printCard('monthly')"
                    title="Print this section"
                    class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-blue-600 transition text-xs cursor-pointer shrink-0 self-start sm:self-auto"
                >
                    <span class="text-xl">
                        🖨️
                    </span>
                </button>
            </div>

            <div class="relative h-64 sm:h-80 w-full">
                <canvas id="monthlyIncomeChart"></canvas>
            </div>

            <!-- PRINT-ONLY TABLE -->
            <div data-print-section="monthly" class="hidden">
                <div class="text-center font-sans mb-4 pb-3 border-b-2 border-[#1b365d]">
                    <h1 class="m-0 text-2xl font-bold text-[#1b365d] font-serif">TRUELIFE</h1>
                    <p class="my-1 text-[11px] font-bold text-[#c5a059] uppercase tracking-wider">&mdash; S O L I D A R I T A S &mdash;</p>
                    <h2 class="mt-2.5 mb-1 text-sm uppercase text-slate-800 font-bold">MONTHLY INCOME ANALYTICS</h2>
                    <p class="text-xs text-slate-600">Year: {{ $selectedYear }}</p>
                </div>
                <table class="w-full border-collapse font-sans text-[11px]">
                    <thead>
                        <tr class="bg-[#0C2C6B] text-white font-bold text-center h-[24px]">
                            <th class="border border-slate-900 p-1">Month</th>
                            <th class="border border-slate-900 p-1">Total Income</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyIncome as $row)
                            <tr>
                                <td class="border border-slate-900 p-1 text-center">{{ $row['month'] }}</td>
                                <td class="border border-slate-900 p-1 text-right">₱{{ number_format($row['total'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('monthlyChart', () => ({
            chartInstance: null,
            init() {
                this.renderChart(
                    @json($monthlyIncome->pluck('month')),
                    @json($monthlyIncome->pluck('total'))
                );
            },
            renderChart(labels, data) {
                const ctx = document.getElementById('monthlyIncomeChart');
                if (!ctx) return;

                if (this.chartInstance) {
                    this.chartInstance.destroy();
                }

                this.chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Income (₱)',
                            data: data,
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
            },
            updateChart(detail) {
                this.renderChart(detail.labels, detail.data);
            }
        }));
    });

    // ---- PER-CARD PRINT ----
    function printCard(section) {
        document.body.setAttribute('data-print-target', section);
        window.print();
    }
    window.addEventListener('afterprint', () => {
        document.body.removeAttribute('data-print-target');
    });
</script>

<style>
@media print {
    @page { size: landscape; margin: 8mm; }

    body[data-print-target] * { visibility: hidden !important; }

    [data-print-section] { display: none !important; }

    body[data-print-target="membership-fee"] [data-print-section="membership-fee"],
    body[data-print-target="source"] [data-print-section="source"],
    body[data-print-target="wallet"] [data-print-section="wallet"],
    body[data-print-target="monthly"] [data-print-section="monthly"] {
        display: block !important;
        visibility: visible !important;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    body[data-print-target="membership-fee"] [data-print-section="membership-fee"] *,
    body[data-print-target="source"] [data-print-section="source"] *,
    body[data-print-target="wallet"] [data-print-section="wallet"] *,
    body[data-print-target="monthly"] [data-print-section="monthly"] * {
        visibility: visible !important;
    }

    table { border-collapse: collapse !important; width: 100%; }
    th, td { border: 1px solid #000 !important; padding: 4px !important; }
    th {
        background-color: #0C2C6B !important;
        color: white !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
