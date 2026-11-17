<div class="space-y-4">

    {{-- =========================================
         TOOLBAR · PRINT THE WHOLE FUND REPORT
    ========================================== --}}

    <div class="flex justify-end print:hidden">
        <button
            type="button"
            onclick="printFundReport()"
            title="Print the whole Pangkabuhayan fund report"
            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-100 transition cursor-pointer"
        >
            🖨️ Print report
        </button>
    </div>


    {{-- Printable region for "print the whole fund report" (revealed only in print) --}}
    <div id="printable-spin-fund-report" class="space-y-4">

    {{-- =========================================
         TOTALS
    ========================================== --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">

        @php
            $totals = $this->report['totals'];
        @endphp

        <div class="rounded-2xl border border-[var(--color-primary)]/10 bg-[var(--color-primary)] p-3.5 text-white">
            <p class="text-[10px] font-bold uppercase tracking-wider text-white">
                Collected
            </p>
            <p class="mt-1 text-lg font-bold">
                ₱{{ number_format($totals['collected'], 2) }}
            </p>
            <p class="text-[10px] text-white">Pangkabuhayan, all cycles</p>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-3.5">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">
                Prizes claimed
            </p>
            <p class="mt-1 text-lg font-bold text-slate-800">
                ₱{{ number_format($totals['claimed'], 2) }}
            </p>
            <p class="text-[10px] text-gray-500">Released to winners</p>
        </div>

        <div class="rounded-2xl border border-amber-100 bg-amber-50 p-3.5">
            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-700">
                Awaiting claim
            </p>
            <p class="mt-1 text-lg font-bold text-amber-700">
                ₱{{ number_format($totals['pending'], 2) }}
            </p>
            <p class="text-[10px] text-amber-700">Drawn, not yet released</p>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-3.5">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">
                Fund balance
            </p>
            <p class="mt-1 text-lg font-bold {{ $totals['balance'] < 0 ? 'text-red-600' : 'text-[var(--color-gold-darker)]' }}">
                ₱{{ number_format($totals['balance'], 2) }}
            </p>
            <p class="text-[10px] text-gray-500">Collected − claimed</p>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-3.5">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">
                Committed
            </p>
            <p class="mt-1 text-lg font-bold text-slate-800">
                ₱{{ number_format($totals['booked'], 2) }}
            </p>
            <p class="text-[10px] text-gray-500">Claimed + awaiting</p>
        </div>
    </div>

    {{-- =========================================
         PER-CYCLE TABLE
    ========================================== --}}
    <div class="overflow-x-auto rounded-2xl border border-gray-100 bg-white">

        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-4 py-3">Draw date</th>
                    <th class="px-4 py-3">Period covered</th>
                    <th class="px-4 py-3 text-right">Collected</th>
                    <th class="px-4 py-3">Prize</th>
                    <th class="px-4 py-3 text-center">Winners</th>
                    <th class="px-4 py-3 text-right">Claimed</th>
                    <th class="px-4 py-3 text-right">Awaiting</th>
                    <th class="px-4 py-3 text-right">Fund balance</th>
                    <th class="px-4 py-3 text-center print:hidden"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse ($this->report['rows'] as $row)
                    <tr class="hover:bg-gray-50/60">
                        <td class="px-4 py-3 font-bold text-[var(--color-primary)]">
                            {{ $row['draw_date']->format('M j, Y') }}
                            @if ($row['status'] === 'Completed')
                                <span class="ml-1 rounded-full bg-[var(--color-gold-tint)] px-1.5 py-0.5 text-[9px] font-bold text-[var(--color-gold-darker)]">done</span>
                            @else
                                <span class="ml-1 rounded-full bg-[var(--color-primary)]/10 px-1.5 py-0.5 text-[9px] font-bold text-[var(--color-primary)]">open</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $row['period_label'] }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-slate-800">
                            ₱{{ number_format($row['collected'], 2) }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $row['prize_name'] }}
                            <span class="text-gray-500">· ₱{{ number_format($row['prize_amount'], 2) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ $row['winners_drawn'] }} / {{ $row['winners_target'] }}
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-[var(--color-gold-darker)]">
                            ₱{{ number_format($row['claimed'], 2) }}
                        </td>
                        <td class="px-4 py-3 text-right font-semibold {{ $row['pending'] > 0 ? 'text-amber-700' : 'text-gray-500' }}">
                            ₱{{ number_format($row['pending'], 2) }}
                            @if ($row['pending_count'] > 0)
                                <span class="text-[9px] font-normal text-gray-500">({{ $row['pending_count'] }})</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-bold {{ $row['balance'] < 0 ? 'text-red-600' : 'text-slate-800' }}">
                            ₱{{ number_format($row['balance'], 2) }}
                        </td>
                        <td class="px-4 py-3 text-center print:hidden">
                            @if ($row['winners_drawn'] > 0)
                                <button
                                    type="button"
                                    wire:click="openWinners({{ $row['draw_id'] }})"
                                    wire:loading.attr="disabled"
                                    title="View the winners of this draw"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-[var(--color-primary)] hover:text-[var(--color-primary)]"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </button>
                            @else
                                <span class="text-gray-500">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                            No Spin the Wheel cycle has collected Pangkabuhayan yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if (count($this->report['unrecorded']) > 0)
            <div class="border-t border-gray-100 px-4 py-3 text-[11px] text-gray-500">
                Not yet drawn (no prizes booked):
                @foreach ($this->report['unrecorded'] as $unrecorded)
                    <span class="font-semibold text-gray-500">
                        {{ $unrecorded['draw_date']->format('M j, Y') }}
                    </span>
                    ({{ $unrecorded['period_label'] }} · collected
                    ₱{{ number_format($unrecorded['collected'], 2) }})@unless ($loop->last) · @endunless
                @endforeach
            </div>
        @endif
    </div>

    <p class="text-[11px] leading-relaxed text-gray-500">
        Prizes are booked as Pangkabuhayan expenses (Source PK) the moment a winner is drawn,
        but only count against the fund once tagged as claimed. Negative balances mean the cycle
        drew more prizes than it collected.
    </p>

    </div><!-- /#printable-spin-fund-report -->

    {{-- =========================================
         WINNERS OF THE SELECTED DRAW (modal,
         rendered server-side like the ledger
         modal in Users Reports)
    ========================================== --}}
    @if ($viewingDrawId && $this->viewingDraw)
        <div
            class="fixed inset-0 z-[70] w-screen h-screen bg-black/60 backdrop-blur-[2px] overflow-y-auto print:hidden"
        >
            <div class="min-h-screen w-full flex items-start sm:items-center justify-center p-4">

            <div class="relative my-auto w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl border border-gray-100">

                {{-- HEADER --}}
                <div class="flex flex-wrap items-start justify-between gap-3 bg-[var(--color-primary)] px-6 py-4 text-white">
                    <div>
                        <h3 class="flex items-center gap-2 text-base font-bold">
                            🏆 Winners — {{ $this->viewingDraw->draw_date->format('F j, Y') }}
                        </h3>
                        <p class="mt-0.5 text-[11px] text-white/70">
                            Covering {{ $this->viewingDraw->period_start->format('M j') }} –
                            {{ $this->viewingDraw->period_end->format('M j, Y') }}
                            · {{ $this->viewingWinners->count() }} shown
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="closeWinners"
                        class="rounded-lg p-1.5 text-white/70 transition hover:bg-white/10 hover:text-white"
                        aria-label="Close"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- SEARCH --}}
                <div class="border-b border-gray-100 px-6 py-3">
                    <div class="relative max-w-sm">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 pointer-events-none">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                            </svg>
                        </span>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="winnerSearch"
                            placeholder="Search winner by name or referral code..."
                            class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] bg-white shadow-sm transition"
                        >
                    </div>
                </div>

                {{-- WINNERS LIST --}}
                <div class="max-h-[60vh] overflow-y-auto px-6 py-4 print:hidden">
                    @if ($this->viewingWinners->isEmpty())
                        <p class="py-6 text-center text-sm text-gray-500">
                            @if ($winnerSearch !== '')
                                No winners match “{{ $winnerSearch }}” in this draw.
                            @else
                                No winners drawn for this cycle yet.
                            @endif
                        </p>
                    @else
                        <ul class="space-y-2">
                            @foreach ($this->viewingWinners as $winner)
                                <li class="flex items-start gap-3 rounded-xl border border-gray-100 bg-gray-50 px-3 py-2.5">
                                    <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[var(--color-accent)] text-[10px] font-bold text-white">
                                        {{ $winner->spin_order }}
                                    </span>

                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-bold text-gray-800">
                                            {{ trim(($winner->user->firstname ?? '').' '.($winner->user->lastname ?? '')) ?: ($winner->user->username ?? 'Unknown') }}
                                            <span class="ml-1 text-[10px] font-medium text-gray-500">{{ $winner->user->referralCode }}</span>
                                        </p>
                                        <p class="truncate text-xs font-bold text-gray-800">
                                            Agent:
                                            <span class="font-italic text-[var(--color-accent)]">{{ $winner->upline_name ?: '—' }}</span>
                                        </p>
                                        <small class="italic text-gray-500">{{ $winner->user->address }}</small>

                                        <p class="text-[11px] text-gray-500">
                                            {{ $winner->prize_name }}
                                            · <span class="font-semibold text-[var(--color-accent)]">₱{{ number_format($winner->prize_amount, 2) }}</span>
                                            · {{ $winner->qualified_invites }} qualifying activation(s)
                                            · drawn {{ $winner->created_at?->format('M j, Y g:i A') }}
                                        </p>
                                    </div>

                                    <div class="shrink-0 text-right">
                                        @if (($winner->ledger?->status ?? 'Pending') === 'Completed')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-[var(--color-gold-tint)] px-2 py-0.5 text-[10px] font-bold text-[var(--color-gold-darker)]">
                                                ✅ Claimed
                                                @if ($winner->ledger?->updated_at)
                                                    · {{ $winner->ledger->updated_at->format('M j, Y') }}
                                                @endif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700">
                                                ⏳ Pending claim
                                            </span>
                                        @endif
                                        @if ($winner->ledger)
                                            <span class="block text-[9px] text-gray-500">PK ledger #{{ $winner->ledger->TransactionID }}</span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- FOOTER --}}
                <div class="flex items-center justify-between gap-2 border-t border-slate-200 px-6 py-3 bg-slate-50 print:hidden">
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            onclick="printDrawWinners()"
                            class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-100 transition cursor-pointer"
                        >
                            🖨️ Print
                        </button>

                        <button
                            type="button"
                            wire:click="exportWinners"
                            wire:loading.attr="disabled"
                            title="Download the winners currently shown as a CSV file"
                            class="px-4 py-2 text-xs font-semibold text-white bg-[var(--color-primary)] rounded-lg hover:bg-[var(--color-primary-mid)] transition cursor-pointer disabled:opacity-60"
                        >
                            ⬇ Export CSV
                        </button>
                    </div>

                    <button
                        type="button"
                        wire:click="closeWinners"
                        class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-200 rounded-lg hover:bg-slate-300 transition cursor-pointer"
                    >
                        Close
                    </button>
                </div>

            </div>
            </div>
        </div>
    @endif

    {{-- =========================================
         PRINTABLE WINNERS LIST — kept OUTSIDE the
         modal overlay (which is print:hidden), so
         the visibility rules can actually reveal it
    ========================================== --}}
    @if ($viewingDrawId && $this->viewingDraw)
        <div id="printable-draw-winners" class="hidden font-sans">
            <div class="text-center font-sans mb-4 pb-3 border-b-2 border-[var(--color-primary)]">
                <h1 class="m-0 text-2xl font-bold text-[var(--color-primary)] font-serif">TRIFECTA</h1>
                <p class="my-1 text-[11px] font-bold text-[var(--color-accent)] uppercase tracking-wider">
                    &mdash; S O L I D A R I T A S &mdash;
                </p>
                <p class="text-xs text-slate-600">
                    Spin the Wheel Winners — {{ $this->viewingDraw?->draw_date?->format('F j, Y') }}
                    ({{ $this->viewingDraw?->period_start?->format('M j') }} – {{ $this->viewingDraw?->period_end?->format('M j, Y') }})
                    &nbsp;|&nbsp; Generated {{ now()->format('M d, Y h:i A') }}
                </p>
            </div>

            <table class="w-full border-collapse font-sans text-[10px]">
                <thead>
                    <tr class="bg-[var(--color-primary)] text-white font-bold text-center h-[22px]">
                        <th class="border border-slate-900 p-1">#</th>
                        <th class="border border-slate-900 p-1">Winner</th>
                        <th class="border border-slate-900 p-1">Referral Code</th>
                        <th class="border border-slate-900 p-1">Prize</th>
                        <th class="border border-slate-900 p-1">Amount</th>
                        <th class="border border-slate-900 p-1">Qualifying Activations</th>
                        <th class="border border-slate-900 p-1">Claim Status</th>
                        <th class="border border-slate-900 p-1">Date Drawn</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->viewingWinners as $winner)
                        <tr>
                            <td class="border border-slate-900 p-1 text-center">{{ $winner->spin_order }}</td>
                            <td class="border border-slate-900 p-1">{{ trim(($winner->user->firstname ?? '').' '.($winner->user->lastname ?? '')) ?: ($winner->user->username ?? 'Unknown') }}</td>
                            <td class="border border-slate-900 p-1 text-center">{{ $winner->user->referralCode }}</td>
                            <td class="border border-slate-900 p-1">{{ $winner->prize_name }}</td>
                            <td class="border border-slate-900 p-1 text-right">₱{{ number_format($winner->prize_amount, 2) }}</td>
                            <td class="border border-slate-900 p-1 text-center">{{ $winner->qualified_invites }}</td>
                            <td class="border border-slate-900 p-1 text-center">{{ ($winner->ledger?->status ?? 'Pending') === 'Completed' ? 'Claimed' : 'Pending claim' }}</td>
                            <td class="border border-slate-900 p-1 text-center">{{ $winner->created_at?->format('M d, Y h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border border-slate-900 p-2 text-center">No winners found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

</div>
