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
                <h3 class="font-bold text-[#0C2C6B] text-lg mb-2">
                    Membership Fee Breakdown
                </h3>
                <div wire:ignore x-data="{
                    startDate: @entangle('membershipFeeStart'),
                    endDate: @entangle('membershipFeeEnd'),
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

                        // Defer so both startDate and endDate have landed before reading them
                        this.$watch('startDate', () => this.$nextTick(() => this.updatePicker()));
                        this.$watch('endDate', () => this.$nextTick(() => this.updatePicker()));

                        // Fallback: explicit resync fired by the server whenever the year changes
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
    </div>

    <!-- 2. COLLECTION SUMMARY BY SOURCE -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
        <div>
            <div class="mb-4">
                <h3 class="font-bold text-[#0C2C6B] text-lg mb-2">
                    Collection Summary by Source
                </h3>
                <div wire:ignore x-data="{
                    startDate: @entangle('sourceStart'),
                    endDate: @entangle('sourceEnd'),
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

                        // Defer so both startDate and endDate have landed before reading them
                        this.$watch('startDate', () => this.$nextTick(() => this.updatePicker()));
                        this.$watch('endDate', () => this.$nextTick(() => this.updatePicker()));

                        // Fallback: explicit resync fired by the server whenever the year changes
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
    </div>

    <!-- 3. WALLET INCENTIVES BREAKDOWN -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
        <div>
            <div class="mb-4">
                <h3 class="font-bold text-[#0C2C6B] text-lg mb-2">
                    Wallet Incentives Breakdown
                </h3>
                <div wire:ignore x-data="{
                    startDate: @entangle('walletStart'),
                    endDate: @entangle('walletEnd'),
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

                        // Defer so both startDate and endDate have landed before reading them
                        this.$watch('startDate', () => this.$nextTick(() => this.updatePicker()));
                        this.$watch('endDate', () => this.$nextTick(() => this.updatePicker()));

                        // Fallback: explicit resync fired by the server whenever the year changes
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
        </div>

        <div class="relative h-64 sm:h-80 w-full">
            <canvas id="monthlyIncomeChart"></canvas>
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
                // detail.labels and detail.data match the named arguments from Livewire
                this.renderChart(detail.labels, detail.data);
            }
        }));
    });
</script>
