<div class="reports-container w-full px-4 sm:px-6 lg:px-8 py-6">

    <!-- HEADER -->
    <div class="members-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-2">
        <h2 class="report-title text-2xl font-bold text-slate-800">
            Ledgers
        </h2>
    </div>

    <p class="report-description text-sm text-slate-500 mb-6">
        View and monitor system financial ledger entries.
    </p>

    <!-- SEARCH INPUT, DATE FILTER & REFRESH BUTTON CONTAINER -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <!-- SEARCH INPUT -->
    <div class="relative max-w-sm w-full sm:w-auto flex-1">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
            <svg
                class="h-4 w-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"
                />
            </svg>
        </span>

        <input
            type="text"
            wire:model.live="search"
            placeholder="Search ledgers..."
            class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl
                focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]
                focus:border-[#0C2C6B]
                bg-white shadow-sm transition"
        >
    </div>

        <!-- FILTERS AND REFRESH GROUP -->
        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">

            <!-- DATE RANGE FILTER -->
            <div wire:ignore class="relative w-72">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none z-10">
                    📅
                </span>
                <input type="text" id="ledgerDateRange" readonly
                    class="date-range-input pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white text-slate-700 shadow-sm cursor-pointer w-full text-center font-medium"
                    placeholder="Select date range">
            </div>

            @if ($dateRange)
                <button type="button" wire:click="clearDateFilter" title="Clear date filter"
                    class="inline-flex items-center px-3 py-2 text-sm text-slate-500 hover:text-[#D79B10] hover:border-[#D79B10]/30 hover:bg-[#D79B10]/10 rounded-xl border border-slate-200 bg-white shadow-sm cursor-pointer transition">
                    ✕
                </button>
            @endif

             <!-- PRINT BUTTON — builds the printable report, prints, then tears it down -->
            <button type="button" wire:click="preparePrint" title="Print Report"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:text-[#0C2C6B] text-sm font-semibold rounded-xl shadow-sm transition cursor-pointer">
                <span>🖨️</span>
                <span class="hidden sm:inline">Print</span>
            </button>

            <!-- REFRESH BUTTON -->
            {{-- REFRESH TABLE --}}
            <button
                type="button"
                wire:click="resetTable"
                wire:loading.attr="disabled"
                title="Refresh Table"
                class="refresh-btn"
            >
                {{-- NORMAL STATE --}}
                <span
                    wire:loading.remove
                    wire:target="resetTable"
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
                    wire:target="resetTable"
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

    <!-- LEDGERS TABLE (screen only) -->
    <div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-sm print:hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr
                    class="border-b border-slate-200 bg-[#0C2C6B] text-xs font-semibold uppercase tracking-wider text-white">
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">Source</th>
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Activation ID</th>
                    <th class="py-3 px-4">Ref ID</th>
                    <th class="py-3 px-4">Amount</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                @forelse($ledgers as $index => $ledger)
                    @php
                        // Human-readable name for the row:
                        //  - MF rows  → the fee name the RefID points to
                        //  - otherwise → the member behind the activation,
                        //    falling back to the user carried in RefID
                        //    (DR / OV / PF / CF / WD / TX / PK store a UserID there)
                        $displayName = $ledger->Source === 'MF'
                            ? ($ledger->fee_name ?: 'N/A')
                            : (trim((string) ($ledger->member_name ?: $ledger->ref_user_name)) ?: 'N/A');
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-4 font-medium text-slate-500">
                            {{ $ledgers->firstItem() + $index }}
                        </td>

                        <td class="py-4 px-4 font-semibold text-slate-900">
                            <span
                                class="px-2.5 py-1 text-xs font-semibold rounded-full bg-[#0C2C6B]/10 text-[#0C2C6B] border border-[#0C2C6B]/30">
                                {{ $this->sourceLabel($ledger->Source) }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-slate-700 font-medium">
                            {{ $displayName }}
                        </td>

                        <td class="py-4 px-4 text-slate-600">
                            {{ $ledger->ActivationID ?? 'N/A' }}
                        </td>

                        <td class="py-4 px-4 text-slate-600">
                            {{ $ledger->RefID ?? 'N/A' }}
                        </td>

                        <td
                            class="py-4 px-4 font-semibold {{ $ledger->amount < 0 ? 'text-[#D79B10]' : 'text-[#0C2C6B]' }}">
                            {{ number_format($ledger->amount, 2) }}
                        </td>

                        <td class="py-4 px-4">
                            <span
                                class="px-2.5 py-1 text-xs font-semibold rounded-full
                                {{ $ledger->status === 'Completed' ? 'bg-[#0C2C6B]/10 text-[#0C2C6B] border border-[#0C2C6B]/30' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                {{ $ledger->status }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-slate-500 text-xs">
                            {{ $ledger->created_at?->format('M d, Y h:i A') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-slate-500">
                            No ledger entries available.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- PAGINATION -->
        @if ($ledgers->hasPages())
            <div class="px-4 py-4 border-t border-slate-200 bg-slate-50 flex justify-center items-center space-x-4">
                {{-- Previous Page Link --}}
                @if ($ledgers->onFirstPage())
                    <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed text-sm">
                        Previous
                    </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded transition cursor-pointer text-sm"
                        style="background-color: #D79B10;">
                        Previous
                    </button>
                @endif

                <span class="text-sm text-gray-600">
                    Page {{ $ledgers->currentPage() }} of {{ $ledgers->lastPage() }}
                </span>

                {{-- Next Page Link --}}
                @if ($ledgers->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded transition cursor-pointer text-sm"
                        style="background-color: #0C2C6B;">
                        Next
                    </button>
                @else
                    <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed text-sm">
                        Next
                    </span>
                @endif
            </div>
        @endif
    </div>

    <!-- PRINTABLE REPORT (hidden on screen, visible only when printing).
         Rendered ONLY while printing — an always-present unpaginated table of
         every ledger row made the page multi-megabyte and laggy. -->
    @if ($printing)
    <div id="printable-ledgers" class="hidden print:block font-sans">
        <div class="text-center font-sans mb-5 pb-3 border-b-2 border-[#0C2C6B]">
            <h1 class="m-0 text-2xl font-bold text-[#0C2C6B] font-serif">
                TRUE LIFE DAYONGAN
            </h1>

            <p class="my-1 text-[11px] font-bold text-[#D79B10] uppercase tracking-wider">
                &mdash; S O L I D A R I T A S &mdash;
            </p>

            <h2 class="mt-2.5 mb-1 text-sm uppercase text-slate-800 font-bold">
                LEDGER REPORT
            </h2>

            <p class="text-xs text-slate-600">
                Period: {{ $dateRangeLabel }}
                @if ($search)
                    &nbsp;|&nbsp; Search: "{{ $search }}"
                @endif
            </p>
        </div>

        <table class="w-full border-collapse font-sans text-[10px]">
            <thead>
                <tr class="bg-[#0C2C6B] text-white font-bold text-center h-[22px]">
                    <th class="border border-slate-900 p-1">#</th>
                    <th class="border border-slate-900 p-1">Source</th>
                    <th class="border border-slate-900 p-1">Name</th>
                    <th class="border border-slate-900 p-1">Activation ID</th>
                    <th class="border border-slate-900 p-1">Ref ID</th>
                    <th class="border border-slate-900 p-1">Amount</th>
                    <th class="border border-slate-900 p-1">Status</th>
                    <th class="border border-slate-900 p-1">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($printLedgers as $index => $ledger)
                    @php
                        $displayName = $ledger->Source === 'MF'
                            ? ($ledger->fee_name ?: 'N/A')
                            : (trim((string) ($ledger->member_name ?: $ledger->ref_user_name)) ?: 'N/A');
                    @endphp
                    <tr>
                        <td class="border border-slate-900 p-1 text-center">{{ $index + 1 }}</td>
                        <td class="border border-slate-900 p-1 text-center font-bold">{{ $this->sourceLabel($ledger->Source) }}</td>
                        <td class="border border-slate-900 p-1 text-center">{{ $displayName }}</td>
                        <td class="border border-slate-900 p-1 text-center">{{ $ledger->ActivationID ?? 'N/A' }}</td>
                        <td class="border border-slate-900 p-1 text-center">{{ $ledger->RefID ?? 'N/A' }}</td>
                        <td class="border border-slate-900 p-1 text-right">{{ number_format($ledger->amount, 2) }}</td>
                        <td class="border border-slate-900 p-1 text-center">{{ $ledger->status }}</td>
                        <td class="border border-slate-900 p-1 text-center">
                            {{ $ledger->created_at?->format('M d, Y h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border border-slate-900 p-2.5 text-center">
                            No ledger entries found for the selected filters.
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

            #printable-ledgers,
            #printable-ledgers * {
                visibility: visible !important;
            }

            #printable-ledgers {
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
            const dateInput = $wire.$el.querySelector('#ledgerDateRange');

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
        </script>
    @endscript

    @script
        <script>
            // The printable table is now in the DOM: print it, then remove it
            // again so the page stays light.
            $wire.on('print-ledgers', () => {
                window.print();
                $wire.set('printing', false);
            });
        </script>
    @endscript

</div>
