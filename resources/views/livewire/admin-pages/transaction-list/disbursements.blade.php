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
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50 p-4">
        <div class="my-auto w-full max-w-5xl overflow-hidden rounded-2xl bg-white shadow-2xl">

            {{-- HEADER --}}
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h3 class="text-base font-bold text-[var(--color-primary)]">
                        New Disbursement
                    </h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Enter the disbursement details and review the source fund.
                    </p>
                </div>

                <button type="button" wire:click="closeModal"
                    class="text-xl text-slate-400 hover:text-slate-700">
                    ✕
                </button>
            </div>

            {{-- TWO-COLUMN CONTENT --}}
            <div class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-2">

                {{-- LEFT COLUMN: DISBURSEMENT DETAILS --}}
                <div class="space-y-4">

                    <div class="border-b border-slate-100 pb-3">
                        <h4 class="text-sm font-bold text-slate-800">
                            Disbursement Details
                        </h4>
                        <p class="mt-1 text-xs text-slate-500">
                            Provide the fund, amount, date, and remarks.
                        </p>
                    </div>

                    {{-- SOURCE FUND --}}
                    <div>
                        <label class="mb-1 block text-xs font-bold text-slate-700">
                            Source Fund *
                        </label>

                        <select wire:model.live="fee_id"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]">
                            <option value="">— Select fee fund —</option>
                            @foreach ($this->feeOptions as $fee)
                                <option value="{{ $fee['FeeID'] }}">
                                    {{ $fee['FeeName'] }} ({{ $fee['FeeID'] }})
                                </option>
                            @endforeach
                        </select>

                        @error('fee_id')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- AVAILABLE FUND --}}
                    <div>
                        <label class="mb-1 block text-xs font-bold text-slate-700">
                            Available Fund
                        </label>

                        @php($availableFund = $this->selectedFund['available'] ?? 0)

                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-xl font-bold {{ $availableFund < 0 ? 'text-red-600' : 'text-slate-800' }}">
                                ₱{{ number_format($availableFund, 2) }}
                            </p>
                            <p class="mt-1 text-[10px] text-slate-500">
                                Current available balance
                            </p>
                        </div>
                    </div>

                    {{-- AMOUNT --}}
                    <div>
                        <label class="mb-1 block text-xs font-bold text-slate-700">
                            Amount Out *
                        </label>

                        <input wire:model.live.debounce.300ms="amount"
                            type="number"
                            step="0.01"
                            min="0.01"
                            @if ($this->isInsuranceFund) readonly @endif
                            class="w-full rounded-xl border px-3 py-2.5 text-sm focus:outline-none focus:ring-2
                                {{ $this->insufficientFund
                                    ? 'border-red-500 focus:ring-red-500'
                                    : 'border-slate-300 focus:ring-[var(--color-primary)]' }}
                                {{ $this->isInsuranceFund ? 'cursor-not-allowed bg-slate-100 font-bold' : 'bg-white' }}">

                        @if ($this->insufficientFund)
                            <p class="mt-1 text-xs font-semibold text-red-600">
                                Insufficient fund available.
                            </p>
                        @else
                            @error('amount')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    {{-- DATE --}}
                    <div>
                        <label class="mb-1 block text-xs font-bold text-slate-700">
                            Date *
                        </label>

                        <input wire:model.live="disbursement_date"
                            type="date"
                            max="{{ now()->format('Y-m-d') }}"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]">

                        @error('disbursement_date')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- REMARKS --}}
                    <div>
                        <label class="mb-1 block text-xs font-bold text-slate-700">
                            Remarks
                        </label>

                        <textarea wire:model="remarks"
                            rows="3"
                            maxlength="1000"
                            placeholder="Optional — e.g. payout recipient or reference"
                            class="w-full resize-none rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"></textarea>

                        @error('remarks')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- RIGHT COLUMN: INSURANCE INFORMATION --}}
                <div class="space-y-4">

                    <div class="border-b border-slate-100 pb-3">
                        <h4 class="text-sm font-bold text-slate-800">
                            Insurance / Coverage
                        </h4>
                        <p class="mt-1 text-xs text-slate-500">
                            Select members if this is an insurance fund.
                        </p>
                    </div>

                    @if ($this->isInsuranceFund)

                        <div class="space-y-4 rounded-xl border border-blue-200 bg-blue-50 p-4">

                            <div>
                                <p class="text-sm font-bold text-blue-900">
                                    Insurance Registration
                                </p>
                                <p class="mt-1 text-xs text-blue-700">
                                    Select active members who will receive insurance coverage.
                                </p>
                            </div>

                            <button type="button"
                                wire:click="openInsuranceMembersModal"
                                class="w-full rounded-xl bg-blue-700 px-4 py-3 text-sm font-bold text-white hover:bg-blue-800">
                                {{ count($selectedMemberIds) > 0 ? 'Edit Selected Members' : 'Select Members' }}
                            </button>

                            @if (count($selectedMemberIds) > 0)

                                <div class="grid grid-cols-2 gap-3">

                                    <div class="rounded-xl border border-blue-200 bg-white p-3">
                                        <p class="text-[10px] font-bold uppercase text-slate-500">
                                            Selected Members
                                        </p>
                                        <p class="mt-1 text-2xl font-bold text-slate-800">
                                            {{ $this->insuranceMemberCount }}
                                        </p>
                                    </div>

                                    <div class="rounded-xl border border-blue-200 bg-white p-3">
                                        <p class="text-[10px] font-bold uppercase text-slate-500">
                                            Total Coverage
                                        </p>
                                        <p class="mt-1 text-xl font-bold text-blue-800">
                                            ₱{{ number_format($this->insuranceTotalCoverage, 2) }}
                                        </p>
                                    </div>

                                </div>

                                <div class="rounded-xl border border-blue-200 bg-white p-3 text-xs text-slate-600">
                                    <div class="flex justify-between">
                                        <span>Insurance Plan</span>
                                        <strong>{{ self::INSURANCE_PLAN ?? 'Economy/Standard' }}</strong>
                                    </div>
                                    <div class="mt-2 flex justify-between">
                                        <span>Coverage per member</span>
                                        <strong>₱400.00</strong>
                                    </div>
                                </div>

                            @else
                                <div class="rounded-xl border border-dashed border-blue-300 bg-white p-6 text-center">
                                    <p class="text-sm font-semibold text-slate-600">
                                        No members selected
                                    </p>
                                    <p class="mt-1 text-xs text-slate-400">
                                        Select members to calculate insurance coverage.
                                    </p>
                                </div>
                            @endif

                            @error('selectedMemberIds')
                                <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror

                        </div>

                    @else

                        <div class="flex min-h-52 flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center">
                            <p class="text-sm font-semibold text-slate-600">
                                Regular Disbursement
                            </p>
                            <p class="mt-1 max-w-xs text-xs text-slate-400">
                                Member selection and insurance coverage will appear here when an insurance fund is selected.
                            </p>
                        </div>

                    @endif

                    {{-- DISBURSEMENT SUMMARY --}}
                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <h4 class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            Disbursement Summary
                        </h4>

                        <div class="mt-3 flex justify-between text-xs text-slate-600">
                            <span>Available Fund</span>
                            <strong>₱{{ number_format($availableFund, 2) }}</strong>
                        </div>

                        <div class="mt-2 flex justify-between text-xs text-slate-600">
                            <span>Amount Out</span>
                            <strong>₱{{ number_format((float) $amount, 2) }}</strong>
                        </div>

                        <div class="mt-3 border-t border-slate-100 pt-3">
                            <div class="flex justify-between">
                                <span class="text-sm font-bold text-slate-700">
                                    Remaining Balance
                                </span>
                                <strong class="text-base {{ ($availableFund - (float) $amount) < 0 ? 'text-red-600' : 'text-[var(--color-primary)]' }}">
                                    ₱{{ number_format($availableFund - (float) $amount, 2) }}
                                </strong>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- FOOTER --}}
            <div class="flex flex-wrap justify-end gap-2 border-t border-slate-200 bg-slate-50 px-6 py-4">

                <button type="button"
                    wire:click="closeModal"
                    class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100">
                    Cancel
                </button>

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
                    class="rounded-xl bg-[var(--color-primary)] px-5 py-2.5 text-sm font-bold text-white hover:bg-[var(--color-primary-mid)] disabled:cursor-not-allowed disabled:opacity-50">
                    Save Disbursement
                </button>

            </div>
        </div>
    </div>
@endif

    {{-- INSURANCE MEMBER SELECTION MODAL --}}
    @if ($showInsuranceMembersModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4">

            <div class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

                {{-- HEADER --}}
                <div class="flex items-center justify-between border-b border-slate-200 p-5">
                    <div>
                        <h3 class="text-base font-bold text-[var(--color-primary)]">
                            Select Insurance Members
                        </h3>
                        <p class="mt-1 text-xs text-slate-500">
                            Only active members who do not yet have insurance are displayed.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="closeInsuranceMembersModal"
                        class="text-xl text-slate-500 hover:text-slate-700"
                    >
                        ✕
                    </button>
                </div>

                {{-- SELECTION SUMMARY --}}
                <div class="grid grid-cols-2 gap-3 border-b border-slate-200 bg-slate-50 p-4 sm:grid-cols-3">

                    <div>
                        <p class="text-[10px] font-bold uppercase text-slate-500">
                            Selected Members
                        </p>
                        <p class="mt-1 text-lg font-bold text-slate-800">
                            {{ $this->insuranceMemberCount }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold uppercase text-slate-500">
                            Coverage Each
                        </p>
                        <p class="mt-1 text-lg font-bold text-slate-800">
                            ₱400.00
                        </p>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <p class="text-[10px] font-bold uppercase text-slate-500">
                            Total Coverage
                        </p>
                        <p class="mt-1 text-lg font-bold text-blue-700">
                            ₱{{ number_format($this->insuranceTotalCoverage, 2) }}
                        </p>
                    </div>

                </div>

                {{-- SELECT ALL / CLEAR --}}
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 p-4">

                    <p class="text-xs font-semibold text-slate-600">
                        {{ $this->eligibleInsuranceMembers->count() }} eligible members
                    </p>

                    <div class="flex gap-2">

                        <button
                            type="button"
                            wire:click="selectAllInsuranceMembers"
                            class="rounded-lg border border-blue-200 px-3 py-2 text-xs font-bold text-blue-700 hover:bg-blue-50"
                        >
                            Select All
                        </button>

                        <button
                            type="button"
                            wire:click="clearInsuranceMembers"
                            class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50"
                        >
                            Clear Selection
                        </button>

                    </div>
                </div>

                {{-- MEMBER LIST --}}
                <div class="min-h-0 flex-1 overflow-y-auto p-4">

                    @forelse ($this->eligibleInsuranceMembers as $member)
                        <label
                            wire:key="insurance-member-{{ $member->UserID }}"
                            class="mb-2 flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3 transition hover:border-blue-300 hover:bg-blue-50
                                {{ in_array((string) $member->UserID, array_map('strval', $selectedMemberIds), true)
                                    ? 'border-blue-400 bg-blue-50'
                                    : 'bg-white' }}"
                        >

                            <input
                                type="checkbox"
                                wire:model.live="selectedMemberIds"
                                value="{{ $member->UserID }}"
                                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            >

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-2">

                                    <p class="truncate text-sm font-bold text-slate-800">
                                        {{ trim(implode(' ', array_filter([
                                            $member->firstname,
                                            $member->middlename,
                                            $member->lastname,
                                        ]))) }}
                                    </p>

                                    <span class="font-mono text-[10px] text-slate-400">
                                        ID: {{ $member->UserID }}
                                    </span>

                                </div>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $member->address ?: 'No address provided' }}
                                </p>
                            </div>

                            <span class="whitespace-nowrap text-xs font-bold text-blue-700">
                                ₱400.00
                            </span>

                        </label>
                    @empty
                        <div class="py-12 text-center">
                            <p class="text-sm font-semibold text-slate-600">
                                No eligible members found.
                            </p>
                            <p class="mt-1 text-xs text-slate-400">
                                All active members may already have insurance records.
                            </p>
                        </div>
                    @endforelse

                </div>

                {{-- FOOTER --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 bg-white p-4">

                    <div>
                        <p class="text-sm font-bold text-slate-800">
                            {{ $this->insuranceMemberCount }} members selected
                        </p>
                        <p class="text-xs text-slate-500">
                            Total: ₱{{ number_format($this->insuranceTotalCoverage, 2) }}
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="closeInsuranceMembersModal"
                        class="rounded-xl bg-[var(--color-primary)] px-5 py-2.5 text-sm font-bold text-white hover:bg-[var(--color-primary-mid)]"
                    >
                        Done Selecting
                    </button>

                </div>

            </div>
        </div>
    @endif
</div>
