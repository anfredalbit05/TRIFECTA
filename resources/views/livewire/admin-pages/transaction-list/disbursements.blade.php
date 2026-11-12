<?php
/**
 * Disbursement tab (Transaction List).
 *
 * Shows every row of the `Disburstment` table with its mirrored ledger state:
 *   status 0 Unposted -> ledger Pending    (amber)
 *   status 1 Posted   -> ledger Completed  (green, expense live)
 *   status 2 Void     -> ledger Cancelled  (grey, money stays in the fund)
 *
 * The summary figures come from the component's `statistics` scope: every
 * disbursement created inside the signed-in user's branch grants, NOT just
 * the rows of the variant card / filters selected below.
 */
$stats = $this->statistics;
?>

<div class="space-y-4">

    {{-- SUMMARY --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Unposted</p>
            <p class="mt-1 text-base font-bold text-slate-700">
                ₱{{ number_format($stats['unposted'], 2) }}
            </p>
            <p class="text-[10px] text-slate-400">ledger: Pending</p>
        </div>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
            <p class="text-[10px] font-bold uppercase tracking-wide text-emerald-700">Posted</p>
            <p class="mt-1 text-base font-bold text-emerald-700">
                ₱{{ number_format($stats['posted'], 2) }}
            </p>
            <p class="text-[10px] text-emerald-600/70">ledger: Completed — expense live</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Voided</p>
            <p class="mt-1 text-base font-bold text-slate-500">
                ₱{{ number_format($stats['void'], 2) }}
            </p>
            <p class="text-[10px] text-slate-400">ledger: Cancelled — money stays in fund</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Net Outflow</p>
            <p class="mt-1 text-base font-bold text-[#0B2A5B]">
                ₱{{ number_format($stats['posted'], 2) }}
            </p>
            <p class="text-[10px] text-slate-400">all created disbursements · live expense</p>
        </div>
    </div>

    {{-- VARIANT FUND CARDS — one card per Active variant, always with one
         card selected (the first variant that still holds money). --}}
    <div class="flex flex-wrap gap-2">
        @forelse ($this->variantCards as $card)
            <button
                type="button"
                wire:key="variant-card-{{ $card['VID'] }}"
                wire:click="selectVariant({{ $card['VID'] }})"
                class="rounded-2xl border-2 px-4 py-3 text-left transition
                    {{ $variantFilter === $card['VID']
                        ? 'border-[#0B2A5B] bg-blue-50 ring-2 ring-[#0B2A5B]/20'
                        : 'border-slate-200 bg-white hover:border-slate-400' }}"
            >
                <span class="block text-xs font-bold text-slate-700">{{ $card['name'] }}</span>
                <span class="mt-0.5 block text-[10px] text-slate-400">
                    {{ $card['funds'] }} fund{{ $card['funds'] === 1 ? '' : 's' }} ·
                    ₱{{ number_format($card['collected'], 2) }} collected
                </span>
                @if ($card['released'] > 0 || $card['disbursed'] > 0 || $card['otherIn'] > 0)
                    <span class="mt-0.5 block text-[10px] text-slate-400">
                        ₱{{ number_format($card['released'], 2) }} released{{ $card['releasedLegacy'] > 0 ? ' · incl. ₱'.number_format($card['releasedLegacy'], 2).' legacy' : '' }} ·
                        ₱{{ number_format($card['disbursed'], 2) }} disbursed
                    </span>
                @endif
                @if ($card['otherIn'] > 0)
                    <span class="mt-0.5 block text-[10px] text-slate-400">
                        +₱{{ number_format($card['otherIn'], 2) }} Convenience Fee & Tax in{{ $card['otherInLegacy'] > 0 ? ' · incl. ₱'.number_format($card['otherInLegacy'], 2).' legacy' : '' }}
                    </span>
                @endif
                <span class="mt-0.5 block text-[10px] font-semibold {{ $card['available'] > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                    ₱{{ number_format($card['available'], 2) }} still available
                </span>
            </button>
        @empty
            <p class="rounded-xl border border-dashed border-slate-300 px-4 py-3 text-xs text-slate-400">
                No Active variant is available to your branch yet.
            </p>
        @endforelse

        <p class="w-full text-[10px] text-slate-400">
            Available = Membership Fees + Convenience Fee + TAX − Direct Referral − Overrides −
            Provident Fund − Claim − Refund − Withdrawals − Pangkabuhayan − Disbursements
            (voided rows excluded). The selected card only scopes the fund list when creating a
            new disbursement — this table always lists every disbursement of your branch.
        </p>
    </div>

    {{-- FILTER BAR --}}
    <div class="flex flex-wrap items-center gap-2">
        <input
            wire:model.live.debounce.400ms="search"
            type="text"
            placeholder="Search remark, DID or fee name…"
            class="w-full max-w-xs rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
        >

        <select
            wire:model.live="statusFilter"
            class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
        >
            <option value="">All Statuses</option>
            <option value="0">Unposted (ledger Pending)</option>
            <option value="1">Posted (ledger Completed)</option>
            <option value="2">Void (ledger Cancelled)</option>
        </select>

        <select
            wire:model.live="creatorFilter"
            class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
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
                    class="rounded-xl bg-[#0B2A5B] px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-[#123c88]"
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
                        <th class="px-4 py-3 font-bold">Status</th>
                        <th class="px-4 py-3 font-bold">Remarks</th>
                        <th class="px-4 py-3 font-bold">Created By</th>
                        <th class="px-4 py-3 text-right font-bold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($this->rows as $row)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">
                                #{{ $row->DID }}
                                <div class="font-sans text-[10px] text-slate-400">
                                    {{ $row->created_at?->format('M j, Y') }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if ($row->fee)
                                    <div class="font-semibold text-slate-700">{{ $row->fee->FeeName }}</div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ $row->fee->variant?->name ?? 'No variant' }} · {{ $row->fee->FeeID }}
                                    </div>
                                @else
                                    <div class="font-semibold text-amber-600">No fund</div>
                                    <div class="text-[11px] text-slate-400">edit this row and pick a source fund</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-slate-800">
                                ₱{{ number_format((float) $row->amount, 2) }}
                                <div class="text-[10px] font-normal text-slate-400">
                                    ledger: −{{ number_format((float) $row->amount, 2) }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if ((int) $row->status === 0)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] font-bold text-amber-700"> ● Unposted</span>
                                @elseif ((int) $row->status === 1)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700"> ● Posted</span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-200 px-2.5 py-0.5 text-[11px] font-bold text-slate-500"> ● Void</span>
                                @endif
                            </td>
                            <td class="max-w-[16rem] px-4 py-3 text-xs text-slate-500">
                                <span class="line-clamp-2 whitespace-pre-line">{{ $row->remarks ?: '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">
                                {{ trim(($row->creator?->firstname ?? '').' '.($row->creator?->lastname ?? '')) ?: '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1.5">
                                    @if ((int) $row->status === 0)
                                        @if ($this->canChangeStatus)
                                            <button
                                                type="button"
                                                  wire:click="confirmPost({{ $row->DID }})"
                                                class="rounded-lg bg-emerald-600 px-2.5 py-1.5 text-[11px] font-bold text-white hover:bg-emerald-700"
                                            >Post</button>
                                            <button
                                                type="button"
                                                wire:click="confirmVoid({{ $row->DID }})"
                                                class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-[11px] font-bold text-slate-600 hover:bg-slate-100"
                                            >Void</button>
                                        @endif
                                        @if ($this->canUpdate || $this->canChangeStatus)
                                            @if ($this->canUpdate)
                                                <button
                                                    type="button"
                                                    wire:click="openEdit({{ $row->DID }})"
                                                    class="rounded-lg bg-[#0B2A5B] px-2.5 py-1.5 text-[11px] font-bold text-white hover:bg-[#123c88]"
                                                >Edit</button>
                                            @endif
                                        @endif
                                    @elseif ((int) $row->status === 1 && $this->canChangeStatus)
                                        <button
                                            type="button"
                                            x-data
                                            @click="
                                                if (! window.SwalFlow) return;
                                                SwalFlow.prompt({
                                                    title: 'Void Disbursement #{{ $row->DID }}?',
                                                    text: 'This expense is already live in the ledger. State the reason before voiding.',
                                                    icon: 'warning',
                                                    input: 'textarea',
                                                    inputLabel: 'Void reason',
                                                    inputPlaceholder: 'Why is this posted disbursement being voided?',
                                                    required: true,
                                                    requiredMessage: 'A void reason is required.',
                                                    maxLength: 1000,
                                                    rows: 4,
                                                    confirmText: 'Void disbursement',
                                                    cancelText: 'Cancel',
                                                    loadingTitle: 'Voiding disbursement...',
                                                    loadingText: 'Cancelling the ledger entry.',
                                                }, (reason) => $wire.call('void', {{ $row->DID }}, reason));
                                            "
                                            class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-[11px] font-bold text-slate-600 hover:bg-slate-100"
                                        >Void…</button>
                                    @else
                                        <span class="text-[11px] text-slate-300">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-400">
                                No disbursements yet — create the first one with “New Disbursement”.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- CREATE / EDIT MODAL --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" x-data>
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-base font-bold text-[#0B2A5B]">
                        {{ $editingDid ? "Edit Disbursement #{$editingDid}" : 'New Disbursement' }}
                    </h3>
                    <button type="button" wire:click="closeModal" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-xs font-bold text-slate-700">Source Fund (Membership Fee) *</label>
                        <select
                            wire:model.live="fee_id"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                        >
                            <option value="">— Select fee fund —</option>
                            @foreach ($this->feeOptions as $fee)
                                <option value="{{ $fee['FeeID'] }}">
                                    {{ $fee['FeeName'] }} ({{ $fee['Variant'] }})
                                </option>
                            @endforeach
                        </select>
                        @error('fee_id') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror

                        @if ($this->selectedFund)
                            @php($fund = $this->selectedFund)
                            <p class="mt-1 text-[11px] text-slate-500">
                                Fund available:
                                <span class="font-bold {{ $fund['available'] < 0 ? 'text-red-600' : 'text-slate-700' }}">
                                    ₱{{ number_format($fund['available'], 2) }}
                                </span>
                                <span class="text-slate-400">
                                    (collected ₱{{ number_format($fund['collected'], 2) }}
                                    − released ₱{{ number_format($fund['released'], 2) }}
                                    − disbursed ₱{{ number_format($fund['disbursed'], 2) }}, excl. void)
                                </span>
                                @if ($fund['releasedLegacy'] > 0)
                                    <span class="text-amber-600">
                                        incl. ₱{{ number_format($fund['releasedLegacy'], 2) }} legacy
                                    </span>
                                @endif
                                <span class="block text-slate-400">
                                    Amounts above this balance are rejected on save.
                                </span>
                            </p>
                        @endif
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-bold text-slate-700">Date *</label>
                        <input
                            wire:model.live="disbursement_date"
                            type="date"
                            max="{{ now()->format('Y-m-d') }}"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                        >
                        @error('disbursement_date') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1 text-[11px] text-slate-400">
                            Record date — defaults to today; editable only while the disbursement is Unposted.
                        </p>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-bold text-slate-700">Amount Out *</label>
                        <input
                            wire:model="amount"
                            type="number"
                            step="0.01"
                            min="0.01"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                        >
                        @error('amount') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1 text-[11px] text-slate-400">
                            The ledger entry (Source <span class="font-mono">DB</span>) stores
                            <span class="font-semibold">−{{ $amount !== '' ? '₱'.number_format((float) $amount, 2) : '₱0.00' }}</span> as an expense.
                        </p>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-bold text-slate-700">Remarks</label>
                        <textarea
                            wire:model="remarks"
                            rows="3"
                            maxlength="1000"
                            placeholder="Optional — e.g. payout recipient, reference, or void reason"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
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
                    <button
                        type="button"
                        wire:click="save"
                        wire:loading.attr="disabled"
                        class="rounded-xl bg-[#0B2A5B] px-4 py-2 text-sm font-bold text-white hover:bg-[#123c88] disabled:opacity-50"
                    >
                        {{ $editingDid ? 'Update Disbursement' : 'Create (ledger Pending)' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
