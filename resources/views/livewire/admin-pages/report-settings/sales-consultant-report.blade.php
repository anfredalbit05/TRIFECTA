<div class="branches-container w-full px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Sales Consultants Report</h2>
        <p class="mt-1 text-sm text-slate-500">Daily wallet totals and running balance for sales consultants.</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 rounded-xl border border-[var(--color-primary)]/30 bg-[var(--color-primary)]/10 p-4 text-sm text-[var(--color-primary)]">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">

    {{-- SEARCH --}}
    <input
        wire:model.live.debounce.300ms="search"
        type="search"
        placeholder="Search name or referral code..."
        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm shadow-sm focus:border-[var(--color-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] lg:max-w-sm"
    >

    <div class="flex flex-wrap items-center gap-3">

        {{-- DATE RANGE PICKER --}}
        <div wire:ignore class="relative w-60">
            <span class="pointer-events-none absolute inset-y-0 left-3 z-10 flex items-center text-slate-500">
                📅
            </span>

            <input
                type="text"
                id="userDateRange"
                readonly
                class="date-range-input w-full cursor-pointer rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-3 text-center text-sm font-medium text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                placeholder="Select date range"
            >
        </div>

        {{-- CLEAR DATE FILTER --}}
        @if ($dateRange)
            <button
                type="button"
                wire:click="clearDateFilter"
                title="Clear date filter"
                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-500 shadow-sm transition hover:border-red-200 hover:bg-red-50 hover:text-red-500"
            >
                ✕
            </button>
        @endif

        {{-- PRINT REPORT --}}
        @if ($this->canPrint)
            <button
                type="button"
                wire:click="preparePrint"
                wire:loading.attr="disabled"
                wire:target="preparePrint"
                title="Print Report"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-[var(--color-primary)]"
            >
                <span>🖨️</span>
                <span class="hidden sm:inline">Print</span>
            </button>
        @endif

        {{-- REFRESH TABLE --}}
        <button
            type="button"
            wire:click="resetTable"
            wire:loading.attr="disabled"
            title="Refresh Table"
            class="refresh-btn"
        >
            <span wire:loading.remove wire:target="resetTable" class="refresh-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 4v5h5M20 20v-5h-5M5.5 9A7 7 0 0 1 18 6.5L20 9M18.5 15A7 7 0 0 1 6 17.5L4 15"
                    />
                </svg>
            </span>

            <span wire:loading wire:target="resetTable" class="refresh-loading">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
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


    {{-- Screen table --}}
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm print:hidden">
        <table class="w-full border-collapse text-left text-sm">
            <thead class="bg-[var(--color-primary)] text-xs uppercase tracking-wide text-white">
                <tr>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Sales Consultant</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3 text-right">DR</th>
                    <th class="px-4 py-3 text-right">PF</th>
                    <th class="px-4 py-3 text-right">CS</th>
                    <th class="px-4 py-3 text-right">SDR</th>
                    <th class="px-4 py-3 text-right">OV</th>
                    <th class="px-4 py-3 text-right">TX</th>
                    <th class="px-4 py-3 text-right">WD</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3 text-right">Running Balance</th>
                    {{-- @if ($this->canRead)
                    <th class="px-4 py-3 text-center">Details</th>
                @endif --}}
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 text-slate-700">
                @php
                    // Group the current page by consultant, not referral code.
                    $groupedRows = $rows->getCollection()->groupBy('UserID');
                @endphp

                @forelse ($groupedRows as $userId => $userRows)
                    @foreach ($userRows as $index => $row)
                        <tr wire:key="sales-report-{{ $row->UserID }}-{{ $row->report_row_id }}"
                            class="hover:bg-slate-50">

                            {{-- Date remains separate for every transaction day --}}
                            <td class="whitespace-nowrap px-4 py-3">
                                {{ \Carbon\Carbon::parse($row->created_date)->format('M d, Y') }}
                            </td>

                            {{-- Merge Code for this consultant --}}
                            @if ($index === 0)
                                <td rowspan="{{ $userRows->count() }}"
                                    class="border border-slate-200 px-4 py-3 text-center align-middle font-medium">
                                    {{ $row->referralCode ?: '-' }}
                                </td>

                                {{-- Merge consultant name --}}
                                <td rowspan="{{ $userRows->count() }}"
                                    class="border border-slate-200 px-4 py-3 text-center align-middle font-semibold text-slate-900">
                                    {{ trim($row->name) }}
                                </td>
                            @endif

                            {{-- Financial details remain separate for every date --}}
                            <td class="px-4 py-3">
                                {{ $row->rolename }}
                            </td>

                            @foreach (['DR', 'PF', 'CS', 'SDR', 'OV', 'TX', 'WD', 'TOTAL', 'RUNNING_BALANCE'] as $field)
                                <td
                                    class="whitespace-nowrap px-4 py-3 text-right font-medium
                                {{ in_array($field, ['TOTAL', 'RUNNING_BALANCE']) ? 'font-bold text-[var(--color-primary)]' : '' }}">
                                    ₱{{ number_format((float) $row->{$field}, 2) }}
                                </td>
                            @endforeach

                            {{-- @if ($this->canRead)
                            <td class="px-4 py-3 text-center">
                            <button
                                wire:click="viewLedger({{ $row->UserID }})"
                                type="button"
                                title="View Financial Details"
                                class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-[var(--color-primary)] text-white hover:bg-[var(--color-primary-dark)] transition cursor-pointer text-xs font-semibold shadow">
                                 👁️
                            </button>
                            </td>
                        @endif --}}
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="13" class="px-4 py-8 text-center text-slate-500">
                            No sales consultant records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Horizontal Legend --}}
        <div class="border-t border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-600">
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                <span class="font-bold text-slate-700">Legend:</span>
                <span class="inline-flex items-center gap-1">
                    <strong class="font-semibold text-[var(--color-primary)]">DR:</strong> Direct Referral
                </span>
                <span class="inline-flex items-center gap-1">
                    <strong class="font-semibold text-[var(--color-primary)]">PF:</strong> Provident Fund
                </span>
                <span class="inline-flex items-center gap-1">
                    <strong class="font-semibold text-[var(--color-primary)]">CS:</strong> Commission Satellite
                </span>
                <span class="inline-flex items-center gap-1">
                    <strong class="font-semibold text-[var(--color-primary)]">SDR:</strong> Satellite Direct Referral
                </span>
                <span class="inline-flex items-center gap-1">
                    <strong class="font-semibold text-[var(--color-primary)]">OV:</strong> Overrides
                </span>
                <span class="inline-flex items-center gap-1">
                    <strong class="font-semibold text-[var(--color-primary)]">TX:</strong> Tax 10%
                </span>
                <span class="inline-flex items-center gap-1">
                    <strong class="font-semibold text-[var(--color-primary)]">WD:</strong> Withdrawal
                </span>
            </div>
        </div>

        {{-- PAGINATION: match UsersAccounts --}}
        @if ($rows->hasPages())
            <div class="member-pagination">
                @if ($rows->onFirstPage())
                    <span class="pagination-btn pagination-disabled">
                        Previous
                    </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled" type="button"
                        class="pagination-btn pagination-prev">
                        Previous
                    </button>
                @endif

                <span class="pagination-info">
                    Page {{ $rows->currentPage() }} of {{ $rows->lastPage() }}
                </span>

                @if ($rows->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled" type="button"
                        class="pagination-btn pagination-next">
                        Next
                    </button>
                @else
                    <span class="pagination-btn pagination-disabled">
                        Next
                    </span>
                @endif
            </div>
        @endif

    </div>


    {{-- Printable report --}}
    <div id="printable-users" class="hidden print:block">
        <header class="mb-5 border-b-2 border-[var(--color-primary)] pb-3 text-center">
            <h1 class="text-2xl font-bold text-[var(--color-primary)]">TRIFECTA</h1>
            <p class="text-xs font-bold uppercase tracking-widest text-[var(--color-accent)]">Health · Wellness · Protection</p>
            <h2 class="mt-2 text-sm font-bold">SALES CONSULTANTS DAILY WALLET REPORT</h2>
            <p class="text-xs text-slate-600">Period: {{ $dateRangeLabel }} @if ($statusFilter)
                    | Status: {{ $statusFilter }}
                @endif
            </p>
        </header>
        <table class="w-full border-collapse text-[10px]">
            <thead>
                <tr class="bg-[var(--color-primary)] text-white">
                    @foreach (['Date', 'Code', 'Sales Consultant', 'Role', 'DR', 'PF', 'CS', 'SDR', 'OV', 'TX', 'WD', 'Total', 'Running Balance'] as $heading)
                        <th class="border border-black p-1">{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($printRows as $row)
                    <tr>
                        <td class="border border-black p-1">
                            {{ \Carbon\Carbon::parse($row->created_date)->format('M d, Y') }}</td>
                        <td class="border border-black p-1">{{ $row->referralCode ?: '-' }}</td>
                        <td class="border border-black p-1">{{ trim($row->name) }}</td>
                        <td class="border border-black p-1">{{ $row->rolename }}</td>
                        @foreach (['DR', 'PF', 'CS', 'SDR', 'OV', 'TX', 'WD', 'TOTAL', 'RUNNING_BALANCE'] as $field)
                            <td class="border border-black p-1 text-right">
                                {{ number_format((float) $row->{$field}, 2) }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="border border-black p-2 text-center">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>{{-- Printable Legend --}}
        <div class="mt-3 border-t border-black pt-2 text-[9px] text-slate-700">
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                <span class="font-bold">Legend:</span>
                <span><strong>DR:</strong> Direct Referral</span>
                <span><strong>PF:</strong> Provident Fund</span>
                <span><strong>CS:</strong> Commission Satellite</span>
                <span><strong>SDR:</strong> Satellite Direct Referral</span>
                <span><strong>WD:</strong> Withdrawal</span>
            </div>
        </div>
    </div>

    {{-- Financial details modal --}}
    @if ($isLedgerModalOpen && $selectedUser)
        <div class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-950/70 p-4 print:hidden"
            wire:key="ledger-modal-{{ $selectedUser->UserID }}">
            <div class="flex min-h-full items-center justify-center">
                <section
                    class="flex max-h-[calc(100vh-2rem)] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
                    <header class="flex items-center justify-between gap-4 bg-[var(--color-primary)] px-6 py-4 text-white">
                        <div>
                            <h3 class="font-bold">Financial Details —
                                {{ trim(($selectedUser->firstname ?? '') . ' ' . ($selectedUser->lastname ?? '')) }}</h3>
                            <p class="text-xs text-white/75">{{ $selectedUser->referralCode ?? 'No referral code' }} ·
                                {{ $selectedUser->status }}</p>
                        </div>
                        <button wire:click="closeLedgerModal" type="button" aria-label="Close modal"
                            class="rounded-full bg-white/15 px-3 py-1.5 font-bold hover:bg-white/25">✕</button>
                    </header>

                    <div class="space-y-6 overflow-y-auto p-5">
                        <div class="grid gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-3">
                            <div>
                                <p class="text-xs uppercase text-slate-500">Email</p>
                                <p class="break-all text-sm font-medium">{{ $selectedUser->email ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-slate-500">Referral Code</p>
                                <p class="text-sm font-medium">{{ $selectedUser->referralCode ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-slate-500">Role</p>
                                <p class="text-sm font-medium">{{ $selectedUser->role->rolename ?? 'Member' }}</p>
                            </div>
                        </div>

                        <section>
                            <h4 class="mb-2 font-bold text-slate-800">Wallet Entries</h4>
                            <div class="max-h-64 overflow-auto rounded-xl border border-slate-200">
                                <table class="w-full text-left text-xs">
                                    <thead class="sticky top-0 bg-slate-100 text-slate-600">
                                        <tr>
                                            <th class="p-3">Wallet ID</th>
                                            <th class="p-3">Source</th>
                                            <th class="p-3">Activation ID</th>
                                            <th class="p-3 text-right">Amount</th>
                                            <th class="p-3">Status</th>
                                            <th class="p-3">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($selectedUser->wallets->sortByDesc('created_at') as $wallet)
                                            <tr>
                                                <td class="p-3">{{ $wallet->WalletID ?? $wallet->id }}</td>
                                                <td class="p-3 font-semibold">{{ $wallet->source }}</td>
                                                <td class="p-3">{{ $wallet->ActivationID ?? '—' }}</td>
                                                <td class="p-3 text-right">
                                                    ₱{{ number_format((float) $wallet->amount, 2) }}</td>
                                                <td class="p-3">{{ $wallet->status ?? '—' }}</td>
                                                <td class="whitespace-nowrap p-3">
                                                    {{ $wallet->created_at?->format('M d, Y h:i A') ?? '—' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="p-4 text-center text-slate-500">No wallet
                                                    entries found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <section>
                            <h4 class="mb-2 font-bold text-slate-800">Ledger Entries</h4>
                            <div class="max-h-64 overflow-auto rounded-xl border border-slate-200">
                                <table class="w-full text-left text-xs">
                                    <thead class="sticky top-0 bg-slate-100 text-slate-600">
                                        <tr>
                                            <th class="p-3">Transaction ID</th>
                                            <th class="p-3">Ref ID</th>
                                            <th class="p-3">Source</th>
                                            <th class="p-3">Details</th>
                                            <th class="p-3 text-right">Amount</th>
                                            <th class="p-3">Status</th>
                                            <th class="p-3">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($statementLedgers as $ledger)
                                            <tr>
                                                <td class="p-3">{{ $ledger->TransactionID }}</td>
                                                <td class="p-3">{{ $ledger->RefID }}</td>
                                                <td class="p-3 font-semibold">{{ $ledger->Source }}</td>
                                                <td class="p-3">
                                                    @if ($ledger->Source === 'PK')
                                                        {{ $ledger->draw?->prize_name ?? 'Pangkabuhayan Prize' }}
                                                        @if ($ledger->draw)
                                                            <span class="block text-slate-500">Draw:
                                                                {{ $ledger->draw->draw_date?->format('M j, Y') }}</span>
                                                        @endif
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td class="p-3 text-right font-semibold">
                                                    ₱{{ number_format((float) ($ledger->amount ?? 0), 2) }}</td>
                                                <td class="p-3">{{ $ledger->status ?? '—' }}</td>
                                                <td class="whitespace-nowrap p-3">
                                                    {{ $ledger->created_at?->format('M d, Y h:i A') ?? '—' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="p-4 text-center text-slate-500">No ledger
                                                    entries found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>

                    <footer class="flex justify-end border-t border-slate-200 bg-slate-50 px-5 py-3">
                        <button wire:click="closeLedgerModal" type="button"
                            class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-300">Close</button>
                    </footer>
                </section>
            </div>
        </div>
    @endif


    @script
    <script>
        const initSalesConsultantDateRange = () => {
            // Lazy components run this block before Livewire morphs the
            // real markup in, so the input is not in the DOM on the first run.
            const dateInput = document.getElementById('userDateRange');

            if (!dateInput) {
                return false;
            }

            if (dateInput._flatpickr) {
                return true;
            }

            if (typeof flatpickr === 'undefined') {
                return false;
            }

            const picker = flatpickr(dateInput, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'M d, Y',
                maxDate: 'today',

                defaultDate: @js(
                    $dateRange
                        ? explode(' to ', $dateRange)
                        : []
                ),

                onChange: (dates, dateStr, instance) => {
                    if (dates.length === 2) {
                        const from = instance.formatDate(
                            dates[0],
                            'Y-m-d'
                        );

                        const to = instance.formatDate(
                            dates[1],
                            'Y-m-d'
                        );

                        $wire.set('dateRange', `${from} to ${to}`);
                    } else if (dates.length === 0) {
                        $wire.set('dateRange', '');
                    }
                },

                onClose: (dates, dateStr, instance) => {
                    if (dates.length === 1) {
                        const day = instance.formatDate(
                            dates[0],
                            'Y-m-d'
                        );

                        instance.setDate(
                            [dates[0], dates[0]],
                            false
                        );

                        $wire.set('dateRange', `${day} to ${day}`);
                    }
                }
            });

            $wire.on('ranges-reset', () => {
                picker.clear();
            });

            return true;
        };

        initSalesConsultantDateRange();

        // Finish the job once Livewire has morphed the lazy component in.
        const stopWaitingForSalesConsultantDateRange = Livewire.hook('morphed', ({ component }) => {
            if (component.id !== $wire.$id) {
                return;
            }

            if (initSalesConsultantDateRange()) {
                stopWaitingForSalesConsultantDateRange();
            }
        });

        // Print the report after Livewire renders the printable table.
        $wire.on('print-sales-consultant-report', () => {
            window.print();

            $wire.set('printing', false);
        });
    </script>
    @endscript
</div>
