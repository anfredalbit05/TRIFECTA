<?php
/**
 * Disbursement tab (Transaction List).
 *
 * Top cards: one total card plus one card per membership fee. Each shows the
 * remaining balance = SUM of Completed 'MF' ledger rows (RefID = FeeID)
 * minus the disbursements recorded for that fee.
 * Disbursements are plain records inserted into the disbursement table, after
 * a SweetAlert review + confirmation checkbox.
 */
?>

<div class="space-y-4">

    {{-- TOTAL CARD + ONE CARD PER MEMBERSHIP FEE --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
        <div class="rounded-xl border border-[var(--color-primary-tint-border)] bg-[var(--color-primary-tint)] px-4 py-3">
            <p class="text-[10px] font-bold uppercase tracking-wide text-[var(--color-primary)]">Total Membership Fees</p>
            <p class="mt-1 text-base font-bold text-[var(--color-primary)]">
                ₱{{ number_format($this->totalAvailable, 2) }}
            </p>
            <p class="text-[10px] text-[var(--color-primary)]">
                collected ₱{{ number_format($this->totalCollected, 2) }}
                − disbursed ₱{{ number_format($this->totalDisbursed, 2) }}
            </p>
        </div>

        @foreach ($this->feeCards as $card)
            <div wire:key="fee-card-{{ $card['FeeID'] }}" class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">
                    {{ $card['name'] }}
                </p>
                <p class="mt-1 text-base font-bold {{ $card['available'] < 0 ? 'text-red-600' : 'text-slate-700' }}">
                    ₱{{ number_format($card['available'], 2) }}
                </p>
                <p class="text-[10px] text-slate-500">
                    {{ $card['FeeID'] }} · collected ₱{{ number_format($card['collected'], 2) }}
                    − disbursed ₱{{ number_format($card['disbursed'], 2) }}
                </p>
            </div>
        @endforeach
    </div>

    {{-- FILTER BAR --}}
    <div class="flex flex-wrap items-center gap-2">
        <input
            wire:model.live.debounce.400ms="search"
            type="text"
            placeholder="Search remark, DID or fee name…"
            class="w-full max-w-xs rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
        >

        <select
            wire:model.live="creatorFilter"
            class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
        >
            <option value="">All Creators</option>
            @foreach ($this->creatorOptions as $creator)
                <option value="{{ $creator['UserID'] }}">{{ $creator['name'] }}</option>
            @endforeach
        </select>

        <div class="ml-auto">
            @if ($this->canCreate)
                <button
                    type="button"
                    wire:click="openCreate"
                    class="rounded-xl bg-[var(--color-primary)] px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-[var(--color-primary-mid)]"
                >
                    + New Disbursement
                </button>
            @endif
        </div>
    </div>

    {{-- TABLE --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-[11px] uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 font-bold">DID</th>
                        <th class="px-4 py-3 font-bold">Fee / Source Fund</th>
                        <th class="px-4 py-3 text-right font-bold">Amount Out</th>
                        <th class="px-4 py-3 font-bold">Remarks</th>
                        <th class="px-4 py-3 font-bold">Created By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($this->rows as $row)
                        <tr wire:key="disb-row-{{ $row->DID }}" class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">
                                #{{ $row->DID }}
                                <div class="font-sans text-[10px] text-slate-500">
                                    {{ $row->created_at?->format('M j, Y') }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if ($row->fee)
                                    <div class="font-semibold text-slate-700">{{ $row->fee->FeeName }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $row->fee->FeeID }}</div>
                                @else
                                    <div class="font-semibold text-amber-600">No fund</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-slate-800">
                                ₱{{ number_format((float) $row->amount, 2) }}
                            </td>
                            <td class="max-w-[16rem] px-4 py-3 text-xs text-slate-500">
                                <span class="line-clamp-2 whitespace-pre-line">{{ $row->remarks ?: '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">
                                {{ trim(($row->creator?->firstname ?? '').' '.($row->creator?->lastname ?? '')) ?: '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">
                                No disbursements yet — create the first one with “New Disbursement”.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- NEW DISBURSEMENT MODAL --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" x-data>
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-base font-bold text-[var(--color-primary)]">New Disbursement</h3>
                    <button type="button" wire:click="closeModal" class="text-slate-500 hover:text-slate-600">✕</button>
                </div>

                <div class="space-y-4">

                    {{-- 1. SOURCE FUND --}}
                    <div>
                        <label class="mb-1 block text-xs font-bold text-slate-700">Source Fund (Membership Fee) *</label>
                        <select
                            wire:model.live="fee_id"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                        >
                            <option value="">— Select fee fund —</option>
                            @foreach ($this->feeOptions as $fee)
                                <option value="{{ $fee['FeeID'] }}">
                                    {{ $fee['FeeName'] }} ({{ $fee['FeeID'] }})
                                </option>
                            @endforeach
                        </select>
                        @error('fee_id') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- 2. AVAILABLE FUND (read-only) --}}
                    <div>
                        <label class="mb-1 block text-xs font-bold text-slate-700">Available Fund</label>
                        @php($availableFund = $this->selectedFund['available'] ?? 0)
                        <input
                            type="text"
                            readonly
                            tabindex="-1"
                            value="₱{{ number_format($availableFund, 2) }}"
                            class="w-full cursor-not-allowed rounded-xl border border-slate-300 bg-slate-100 px-3 py-2 text-sm font-bold {{ $availableFund < 0 ? 'text-red-600' : 'text-slate-700' }} focus:outline-none"
                        >
                    </div>

                    {{-- 3. AMOUNT --}}
                    <div>
                        <label class="mb-1 block text-xs font-bold text-slate-700">Amount Out *</label>
                        <input
                            wire:model.live.debounce.300ms="amount"
                            type="number"
                            step="0.01"
                            min="0.01"
                            class="w-full rounded-xl border bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2
                                {{ $this->insufficientFund
                                    ? 'border-red-500 focus:ring-red-500'
                                    : 'border-slate-300 focus:ring-[var(--color-primary)]' }}"
                        >
                        @if ($this->insufficientFund)
                            <p class="mt-1 text-xs font-semibold text-red-600">Insufficient fund available.</p>
                        @else
                            @error('amount') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    {{-- 4. DATE --}}
                    <div>
                        <label class="mb-1 block text-xs font-bold text-slate-700">Date *</label>
                        <input
                            wire:model.live="disbursement_date"
                            type="date"
                            max="{{ now()->format('Y-m-d') }}"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                        >
                        @error('disbursement_date') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- 5. REMARKS --}}
                    <div>
                        <label class="mb-1 block text-xs font-bold text-slate-700">Remarks</label>
                        <textarea
                            wire:model="remarks"
                            rows="3"
                            maxlength="1000"
                            placeholder="Optional — e.g. payout recipient or reference"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                        ></textarea>
                        @error('remarks') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-5 flex justify-end gap-2">
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50"
                    >Cancel</button>

                    {{-- Save: validate -> SweetAlert review + checkbox -> insert --}}
                    <button
                        type="button"
                        wire:loading.attr="disabled"
                        wire:target="prepareReview,save"
                        @disabled($this->insufficientFund)
                        @click="
                            if (! window.Swal) return;
                            $wire.call('prepareReview').then((r) => {
                                if (! r) return;
                                const row = (label, value, strong) =>
                                    '<tr>' +
                                    '<td style=\'padding:6px 10px;color:#64748b;font-size:12px;vertical-align:top;white-space:nowrap\'>' + label + '</td>' +
                                    '<td style=\'padding:6px 10px;font-size:13px;' + (strong ? 'font-weight:700;' : '') + '\'>' + value + '</td>' +
                                    '</tr>';
                                Swal.fire({
                                    icon: 'question',
                                    title: 'Review Disbursement',
                                    html:
                                        '<table style=\'width:100%;text-align:left;border-collapse:collapse;border:1px solid #e2e8f0;border-radius:8px\'>' +
                                        row('Source Fund', r.fund, true) +
                                        row('Available Fund', r.available, false) +
                                        row('Amount Out', r.amount, true) +
                                        row('Fund After', r.remaining, false) +
                                        row('Date', r.date, false) +
                                        row('Remarks', r.remarks, false) +
                                        '</table>',
                                    input: 'checkbox',
                                    inputValue: 0,
                                    inputPlaceholder: 'I am sure the information above is correct.',
                                    inputValidator: (checked) => checked ? undefined : 'Please tick the box to confirm.',
                                    showCancelButton: true,
                                    confirmButtonText: 'Confirm & Save',
                                    cancelButtonText: 'Go back',
                                    showLoaderOnConfirm: true,
                                    allowOutsideClick: () => ! Swal.isLoading(),
                                    preConfirm: () => $wire.call('save', true),
                                });
                            });
                        "
                        class="rounded-xl bg-[var(--color-primary)] px-4 py-2 text-sm font-bold text-white hover:bg-[var(--color-primary-mid)] disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Save Disbursement
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
