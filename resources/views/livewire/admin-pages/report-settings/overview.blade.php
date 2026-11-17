{{-- Styles for this component are bundled via resources/css/app.css --}}
<div class="overview">

    <!-- HEADER WITH CONTROLS -->
    <div class="overview__header">
        <div>
            <h2 class="overview__title">
                Report & Settings Overview
            </h2>
            <p class="overview__subtitle">
                Financial performance and system settings overview for <span class="overview__subtitle-highlight">{{ $selectedYear }}</span>
            </p>
        </div>

        <!-- ACTIONS: YEAR FILTER (2020 - Current Year) & REFRESH -->
        <div class="overview__actions">
            <select wire:model.live="selectedYear" class="overview__year-select">
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
                    class="refresh-btn__icon"
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
                    class="refresh-btn__loading"
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
                            class="refresh-btn__circle"
                        />
                        <path
                            d="M21 12a9 9 0 0 1-9 9"
                            stroke-width="3"
                            stroke-linecap="round"
                        />
                    </svg>
                </span>

                <span class="refresh-btn__label">Refresh</span>
            </button>
        </div>
    </div>

    <!-- KPI CARDS -->
    @php
        $cards = [
            ['Total Income', '₱ ' . number_format($totalIncome, 2), '💰'],
            ['Reports / Entries', $totalReportsGenerated, '📊'],
            ['Total Users', $totalUsers, '👥'],
            ['Active Accounts', $activeAccounts, '✅'],
            ['System Settings', $totalSettingsCount, '⚙️'],
        ];
    @endphp

    <div class="kpi-grid">
        @foreach($cards as $card)
            <div class="kpi-card">
                <div class="kpi-card__icon">
                    {{ $card[2] }}
                </div>
                <div class="kpi-card__content">
                    <p class="kpi-card__label">
                        {{ $card[0] }}
                    </p>
                    <h3 class="kpi-card__value">
                        {{ $card[1] }}
                    </h3>
                </div>
            </div>
        @endforeach
    </div>

    <!-- SECOND ROW: BREAKDOWN CARDS (3 COLUMNS) -->
    <div class="breakdown-grid">

        <!-- 1. MEMBERSHIP FEE COLLECTION BREAKDOWN -->
        <div class="breakdown-card">
            <div>
                <div class="breakdown-card__head">
                    <div class="breakdown-card__title-row">
                        <h3 class="breakdown-card__title">
                            Membership Fee Breakdown
                        </h3>
                        <button
                            onclick="printCard('membership-fee')"
                            title="Print this section"
                            class="print-btn"
                        >
                            <span class="print-btn__icon">
                                🖨️
                            </span>
                        </button>
                    </div>
                    {{-- <div wire:ignore x-data="{
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
                            class="date-range-input"
                            placeholder="Select date range">
                    </div> --}}
                </div>

             <div>
                <label for="membershipFeeVariant">Filter by Variant</label>

                <select
                    id="membershipFeeVariant"
                    wire:model.live="selectedVariant"
                    class="date-range-input"
                >
                    @foreach ($variants as $variant)
                        <option value="{{ $variant->VID }}">
                            {{ $variant->name }}
                        </option>
                    @endforeach
                </select>
            </div>

                <div class="breakdown-list membership-fee-card__list">
                    @forelse($membershipFeeCollections as $fee)
                        <div class="breakdown-list__item">
                            <div>
                                <p class="breakdown-list__name">
                                    {{ $fee->FeeName }}
                                </p>

                                <p class="breakdown-list__sub">
                                    Fee ID: {{ $fee->FeeID }}
                                    ·
                                    {{ $fee->Variant_Name ?? 'No Variant' }}
                                </p>

                            </div>

                            <div class="breakdown-list__value-wrap">
                                <span class="breakdown-list__value">
                                    ₱{{ number_format((float) $fee->Total_Collection, 2) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="breakdown-list__empty">
                            No membership fees found for this period and variant.
                        </p>
                    @endforelse
                </div>

                {{-- TOTAL COLLECTION FOOTER --}}
                <div class="membership-fee-total">
                    <div>
                        <span class="membership-fee-total__label">
                            Total Collection
                        </span>

                        <span class="membership-fee-total__sub">
                            {{ $variants->firstWhere('VID', (int) $selectedVariant)->name
                                ?? 'Selected Variant' }}
                        </span>
                    </div>

                    <span class="membership-fee-total__amount">
                        ₱{{ number_format($membershipFeeTotal, 2) }}
                    </span>
                </div>
            </div>

            <!-- PRINT-ONLY TABLE -->
            <div data-print-section="membership-fee" class="print-section">
                <div class="print-header">
                    <h1 class="print-header__logo">TRIFECTA</h1>
                    <p class="print-header__tagline">&mdash; S O L I D A R I T A S &mdash;</p>
                    <h2 class="print-header__section-title">MEMBERSHIP FEE COLLECTION BREAKDOWN</h2>
                    <p class="print-header__period">
                        Period: {{ $membershipFeeStartLabel }} &mdash; {{ $membershipFeeEndLabel }}
                    </p>
                </div>
                <table class="print-table">
                    <thead>
                        <tr class="print-table__head-row">
                            <th>#</th>
                            <th>Fee ID</th>
                            <th>Fee Name</th>
                            <th>Variant</th>
                            <th>Total Collection</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($membershipFeeCollections as $index => $fee)
                            <tr>
                                <td class="print-table__cell--center">
                                    {{ $index + 1 }}
                                </td>

                                <td class="print-table__cell--center">
                                    {{ $fee->FeeID }}
                                </td>

                                <td>{{ $fee->FeeName }}</td>

                                <td>{{ $fee->Variant_Name ?? 'No Variant' }}</td>

                                <td class="print-table__cell--right">
                                    ₱{{ number_format((float) $fee->Total_Collection, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="print-table__empty">
                                    No data for the selected period.
                                </td>
                            </tr>
                        @endforelse
                        <tr>
                            <td colspan="4" class="print-table__cell--right">
                                <strong>Total Collection</strong>
                            </td>

                            <td class="print-table__cell--right">
                                <strong>
                                    ₱{{ number_format($membershipFeeTotal, 2) }}
                                </strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. COLLECTION SUMMARY BY SOURCE -->
        <div class="breakdown-card">
            <div>
                <div class="breakdown-card__head">
                    <div class="breakdown-card__title-row">
                        <h3 class="breakdown-card__title">
                            Collection Summary by Source
                        </h3>
                        <button
                            onclick="printCard('source')"
                            title="Print this section"
                            class="print-btn"
                        >
                            <span class="print-btn__icon">
                                🖨️
                            </span>
                        </button>
                    </div>
                    {{-- <div wire:ignore x-data="{
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
                            class="date-range-input"
                            placeholder="Select date range">
                    </div> --}}
                </div>

                <div class="breakdown-list">
                    @forelse($incomeSources as $source)
                        <div class="source-list__item">
                            <div>
                                <span class="source-list__type">{{ $source->Collection_Type }}</span>
                                <span class="source-list__meta">({{ $source->Source }})</span>
                                <span class="source-list__meta">({{ $source->count }} entries)</span>
                            </div>
                            <span class="source-list__value">
                                ₱{{ number_format($source->Total_Collection, 2) }}
                            </span>
                        </div>
                    @empty
                        <p class="breakdown-list__empty">No collection summary available for this period.</p>
                    @endforelse
                </div>
            </div>

            <!-- PRINT-ONLY TABLE -->
            <div data-print-section="source" class="print-section">
                <div class="print-header">
                    <h1 class="print-header__logo">TRIFECTA</h1>
                    <p class="print-header__tagline">&mdash; S O L I D A R I T A S &mdash;</p>
                    <h2 class="print-header__section-title">COLLECTION SUMMARY BY SOURCE</h2>
                    <p class="print-header__period">
                        Period: {{ $sourceStartLabel }} &mdash; {{ $sourceEndLabel }}
                    </p>
                </div>
                <table class="print-table">
                    <thead>
                        <tr class="print-table__head-row">
                            <th>#</th>
                            <th>Collection Type</th>
                            <th>Source</th>
                            <th>Entries</th>
                            <th>Total Collection</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incomeSources as $index => $source)
                            <tr>
                                <td class="print-table__cell--center">{{ $index + 1 }}</td>
                                <td>{{ $source->Collection_Type }}</td>
                                <td class="print-table__cell--center">{{ $source->Source }}</td>
                                <td class="print-table__cell--center">{{ $source->count }}</td>
                                <td class="print-table__cell--right">₱{{ number_format($source->Total_Collection, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="print-table__empty">No data for the selected period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="membership-fee-total">
            <div>
                <span class="membership-fee-total__label">
                    Total Collection
                </span>

                <span class="membership-fee-total__sub">
                    All Sources
                </span>
            </div>

            <span class="membership-fee-total__amount">
                ₱{{ number_format($incomeSourcesTotal, 2) }}
            </span>
        </div>
        </div>

        <!-- 3. WALLET INCENTIVES BREAKDOWN -->
        <div class="breakdown-card">
            <div>
                <div class="breakdown-card__head">
                    <div class="breakdown-card__title-row">
                        <h3 class="breakdown-card__title">
                            Wallet Incentives Breakdown
                        </h3>
                        <button
                            onclick="printCard('wallet')"
                            title="Print this section"
                            class="print-btn"
                        >
                            <span class="print-btn__icon">
                                🖨️
                            </span>
                        </button>
                    </div>
                    {{-- <div wire:ignore x-data="{
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
                            class="date-range-input"
                            placeholder="Select date range">
                    </div> --}}
                </div>

                <div class="breakdown-list">
                    @forelse($walletCollections as $item)
                        <div class="wallet-list__item">
                            <div>
                                <p class="wallet-list__name">
                                    {{ $item->Member_Name }}
                                    <span class="wallet-list__referral">
                                        ({{ $item->referralCode }})
                                    </span>
                                </p>
                                <p class="breakdown-list__sub">
                                    {{ $item->Incentive_Type }} ({{ $item->Source }})
                                </p>
                            </div>
                            <div class="breakdown-list__value-wrap">
                                <span class="breakdown-list__value">
                                    ₱{{ number_format($item->Total_Collection, 2) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="breakdown-list__empty">No wallet incentives found for this period.</p>
                    @endforelse
                </div>
            </div>

            <!-- PRINT-ONLY TABLE -->
            <div data-print-section="wallet" class="print-section">
                <div class="print-header">
                    <h1 class="print-header__logo">TRIFECTA</h1>
                    <p class="print-header__tagline">&mdash; S O L I D A R I T A S &mdash;</p>
                    <h2 class="print-header__section-title">WALLET INCENTIVES BREAKDOWN</h2>
                    <p class="print-header__period">
                        Period: {{ $walletStartLabel }} &mdash; {{ $walletEndLabel }}
                    </p>
                </div>
                <table class="print-table">
                    <thead>
                        <tr class="print-table__head-row">
                            <th>#</th>
                            <th>Member Name</th>
                            <th>Incentive Type</th>
                            <th>Source</th>
                            <th>Total Collection</th>
                            <th>Member Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($walletCollections as $index => $item)
                            <tr>
                                <td class="print-table__cell--center">{{ $index + 1 }}</td>
                                <td>{{ $item->Member_Name }}</td>
                                <td>{{ $item->Incentive_Type }}</td>
                                <td class="print-table__cell--center">{{ $item->Source }}</td>
                                <td class="print-table__cell--right">₱{{ number_format($item->Total_Collection, 2) }}</td>
                                <td class="print-table__cell--right">₱{{ number_format($item->User_Total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="print-table__empty">No data for the selected period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

             <div class="membership-fee-total">
                <div>
                    <span class="membership-fee-total__label">
                        Total Incentives
                    </span>

                    <span class="membership-fee-total__sub">
                        All Members
                    </span>
                </div>

                <span class="membership-fee-total__amount">
                    ₱{{ number_format($walletCollectionsTotal, 2) }}
                </span>
            </div>
        </div>



    </div>

    <!-- CHARTS SECTION: MONTHLY INCOME -->
    <div class="chart-section">
        <div class="chart-card"
             x-data="monthlyChart()"
             data-chart-labels="{{ $monthlyIncome->pluck('month')->toJson() }}"
             data-chart-values="{{ $monthlyIncome->pluck('total')->toJson() }}"
             @chart-data-updated.window="updateChart($event.detail)">
            <div class="chart-card__head">
                <h3 class="breakdown-card__title">
                    Monthly Income Analytics (₱)
                </h3>
                {{-- PRINT MONTHLY --}}
                <button
                    onclick="printCard('monthly')"
                    title="Print this section"
                    class="print-btn print-btn--auto-align"
                >
                    <span class="print-btn__icon">
                        🖨️
                    </span>
                </button>
            </div>

            <div class="chart-card__canvas-wrap">
                <canvas id="monthlyIncomeChart"></canvas>
            </div>

            <!-- PRINT-ONLY TABLE -->
            <div data-print-section="monthly" class="print-section">
                <div class="print-header">
                    <h1 class="print-header__logo">TRIFECTA</h1>
                    <p class="print-header__tagline">&mdash; S O L I D A R I T A S &mdash;</p>
                    <h2 class="print-header__section-title">MONTHLY INCOME ANALYTICS</h2>
                    <p class="print-header__period">Year: {{ $selectedYear }}</p>
                </div>
                <table class="print-table">
                    <thead>
                        <tr class="print-table__head-row">
                            <th>Month</th>
                            <th>Total Income</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyIncome as $row)
                            <tr>
                                <td class="print-table__cell--center">{{ $row['month'] }}</td>
                                <td class="print-table__cell--right">₱{{ number_format($row['total'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
