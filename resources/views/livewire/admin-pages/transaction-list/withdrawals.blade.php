<div class="masterlist-container">

    <div class="members-header px-4 pt-4">
        <div class="members-header-content">
            <h2 class="masterlist-title">Withdrawal History</h2>
            <p class="masterlist-description">Every withdrawal requested by members</p>
        </div>

        <button
            type="button"
            wire:click="resetTable"
            wire:loading.attr="disabled"
            title="Refresh Table"
            class="refresh-btn"
        >
            {{-- NORMAL STATE --}}
            <span wire:loading.remove wire:target="resetTable" class="refresh-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h5M20 20v-5h-5M5.5 9A7 7 0 0 1 18 6.5L20 9M18.5 15A7 7 0 0 1 6 17.5L4 15"/>
                </svg>
            </span>

            {{-- LOADING STATE --}}
            <span wire:loading wire:target="resetTable" class="refresh-loading">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9" stroke-width="3" class="refresh-circle"/>
                    <path d="M21 12a9 9 0 0 1-9 9" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </span>

            <span class="hidden sm:inline">Refresh</span>
        </button>
    </div>

    {{-- SEARCH + PAYMENT + DATE RANGE + PRINT (one flex row) --}}
    <div class="mb-4 flex flex-wrap items-center gap-3 px-4">
        <input type="text" wire:model.live.debounce.350ms="search" class="txn-search-input"
            placeholder="Search ID, member, method, reference, status...">

        {{-- PAYMENT METHOD DROPDOWN — filters the table; print follows it too --}}
        <select wire:model.live="paymentFilter" class="txn-filter-select" title="Filter by payment method">
            <option value="">All payment methods</option>
            <option value="Cash">Cash</option>
            <option value="GCash">GCash</option>
            <option value="Maya">Maya</option>
            <option value="Bank Transfer">Bank Transfer</option>
        </select>

        {{-- DATE RANGE FILTER — same flatpickr pattern as the Inquiries tab --}}
        <div wire:ignore class="relative w-60">
            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none z-10">
                📅
            </span>
            <input type="text" id="withdrawalDateRange" readonly
                class="date-range-input pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white text-slate-700 shadow-sm cursor-pointer w-full text-center font-medium"
                placeholder="Select date range">
        </div>

        @if ($dateRange)
            <button type="button" wire:click="clearDateFilter" title="Clear date filter"
                class="inline-flex items-center px-3 py-2 text-sm text-slate-500 hover:text-red-500 hover:border-red-200 hover:bg-red-50 rounded-xl border border-slate-200 bg-white shadow-sm cursor-pointer transition">
                ✕
            </button>
        @endif

        @if ($this->canPrint)
            {{-- PRINT — prints exactly what the current filters show --}}
            <button type="button" wire:click="preparePrint" title="Print Report"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:text-[#0C2C6B] text-sm font-semibold rounded-xl shadow-sm transition cursor-pointer">
                <span>🖨️</span>
                <span class="hidden sm:inline">Print</span>
            </button>
        @endif
    </div>

    {{-- TABLE --}}
    <div class="px-4 pb-4">
        <div class="txn-table-wrap">
            <table class="txn-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Member</th>
                        <th>Type</th>
                        <th>Requested</th>
                        <th>Net Paid</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($withdrawals as $w)
                        <tr wire:key="wd-{{ $w->WithdrawID }}">
                            {{-- Row number generated in the view (continues across pages) --}}
                            <td data-label="#">{{ ($withdrawals->perPage() * ($withdrawals->currentPage() - 1)) + $loop->iteration }}</td>
                            <td data-label="Member">
                                {{ trim($w->user?->lastname.' '.$w->user?->firstname) ?: '—' }}
                            </td>
                            <td data-label="Type">{{ $w->type ?: '—' }}</td>
                            <td data-label="Requested" class="txn-amount">₱{{ number_format((float) $w->requested_amount, 2) }}</td>
                            <td data-label="Net Paid" class="txn-amount">₱{{ number_format((float) $w->amount, 2) }}</td>
                            <td data-label="Method">{{ $w->withdrawal_method ?: '—' }}</td>
                            <td data-label="Reference">{{ $w->payment_reference ?: '—' }}</td>
                            <td data-label="Status">
                                <span class="txn-pill txn-pill-{{ strtolower($w->status) }}">{{ $w->status }}</span>
                            </td>
                            <td data-label="Date">{{ $w->created_at?->format('M d, Y') }}</td>
                            <td data-label="Actions" class="txn-actions">
                                @if ($this->canProcess)
                                    <button type="button"
                                        wire:click="reviewWithdrawal({{ $w->WithdrawID }})"
                                        class="txn-action-btn txn-action-review"
                                        title="Review details"
                                        aria-label="Review withdrawal for {{ trim($w->user?->lastname.' '.$w->user?->firstname) ?: 'member' }}">👁</button>
                                @else
                                    <span class="txn-actions-none">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="txn-empty">No withdrawals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Approve/reject dialogs are handled globally by the page shell —
         see resources/views/pages/admin-pages/transaction-list.blade.php --}}

    {{-- PAGINATION — same style as Report & Settings tables --}}
    @if ($withdrawals->lastPage() > 1)
        <div class="pagination-wrapper">
            @if ($withdrawals->onFirstPage())
                <span class="pagination-btn-disabled">Previous</span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled"
                    class="pagination-btn pagination-btn-prev">Previous</button>
            @endif

            <span class="pagination-label">Page {{ $withdrawals->currentPage() }} of
                {{ $withdrawals->lastPage() }}</span>

            @if ($withdrawals->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled"
                    class="pagination-btn pagination-btn-next">Next</button>
            @else
                <span class="pagination-btn-disabled">Next</span>
            @endif
        </div>
    @endif

    {{-- PRINTABLE REPORT (hidden on screen, visible only when printing).
         Rendered ONLY while printing — mirrors the current filters exactly:
         whatever the table shows is what prints. Same approach as the
         Report & Settings Ledgers print. --}}
    @if ($printing)
    <div id="printable-withdrawals" class="hidden print:block font-sans">
        <div class="text-center font-sans mb-5 pb-3 border-b-2 border-[#0C2C6B]">
            <h1 class="m-0 text-2xl font-bold text-[#0C2C6B] font-serif">
                TRUE LIFE DAYONGAN
            </h1>

            <p class="my-1 text-[11px] font-bold text-[#D79B10] uppercase tracking-wider">
                &mdash; S O L I D A R I T A S &mdash;
            </p>

            <h2 class="mt-2.5 mb-1 text-sm uppercase text-slate-800 font-bold">
                WITHDRAWAL REPORT
            </h2>

            <p class="text-xs text-slate-600">
                Period: {{ $dateRangeLabel }}
                @if (trim($paymentFilter))
                    &nbsp;|&nbsp; Payment: {{ $paymentFilter }}
                @endif
                @if (trim($search))
                    &nbsp;|&nbsp; Search: "{{ $search }}"
                @endif
            </p>
        </div>

        <table class="w-full border-collapse font-sans text-[10px]">
            <thead>
                <tr class="bg-[#0C2C6B] text-white font-bold text-center h-[22px]">
                    <th class="border border-slate-900 p-1">#</th>
                    <th class="border border-slate-900 p-1">Member</th>
                    <th class="border border-slate-900 p-1">Requested</th>
                    <th class="border border-slate-900 p-1">Net Paid</th>
                    <th class="border border-slate-900 p-1">Method</th>
                    <th class="border border-slate-900 p-1">Reference</th>
                    <th class="border border-slate-900 p-1">Status</th>
                    <th class="border border-slate-900 p-1">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($printWithdrawals as $index => $w)
                    <tr>
                        <td class="border border-slate-900 p-1 text-center">{{ $index + 1 }}</td>
                        <td class="border border-slate-900 p-1 text-center">{{ trim($w->user?->lastname.' '.$w->user?->firstname) ?: 'N/A' }}</td>
                        <td class="border border-slate-900 p-1 text-right">{{ number_format((float) $w->requested_amount, 2) }}</td>
                        <td class="border border-slate-900 p-1 text-right">{{ number_format((float) $w->amount, 2) }}</td>
                        <td class="border border-slate-900 p-1 text-center">{{ $w->withdrawal_method ?: 'N/A' }}</td>
                        <td class="border border-slate-900 p-1 text-center">{{ $w->payment_reference ?: '—' }}</td>
                        <td class="border border-slate-900 p-1 text-center font-bold">{{ $w->status }}</td>
                        <td class="border border-slate-900 p-1 text-center">{{ $w->created_at?->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border border-slate-900 p-2.5 text-center">
                            No withdrawals found for the selected filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    <style>
        @media print {
            @page {
                size: landscape;
                margin: 8mm;
            }

            body * {
                visibility: hidden !important;
            }

            #printable-withdrawals,
            #printable-withdrawals * {
                visibility: visible !important;
            }

            #printable-withdrawals {
                display: block !important;
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
            }

            table {
                border-collapse: collapse !important;
                width: 100% !important;
            }

            th,
            td {
                border: 1px solid #000 !important;
                padding: 4px !important;
            }

            th {
                background-color: #0C2C6B !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>

    @script
<script>
    const dateInput = $wire.$el.querySelector('#withdrawalDateRange');

    const picker = flatpickr(dateInput, {
        mode: 'range',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'M d, Y',
        maxDate: 'today',
        defaultDate: @js($dateRange ? explode(' to ', $dateRange) : []),
        onChange: (selectedDates, dateStr, instance) => {
            if (selectedDates.length === 2) {
                const start = instance.formatDate(selectedDates[0], 'Y-m-d');
                const end = instance.formatDate(selectedDates[1], 'Y-m-d');
                $wire.set('dateRange', `${start} to ${end}`);
            } else if (selectedDates.length === 0) {
                $wire.set('dateRange', '');
            }
        },
        onClose: (selectedDates, dateStr, instance) => {
            if (selectedDates.length === 1) {
                const day = instance.formatDate(selectedDates[0], 'Y-m-d');
                instance.setDate([selectedDates[0], selectedDates[0]], false);
                $wire.set('dateRange', `${day} to ${day}`);
            }
        }
    });

    $wire.on('ranges-reset', () => {
        picker.clear();
    });

    // The printable table is now in the DOM: print it, then remove it again
    // so the page stays light.
    $wire.on('print-withdrawals', () => {
        window.print();
        $wire.set('printing', false);
    });
</script>
    @endscript

</div>
