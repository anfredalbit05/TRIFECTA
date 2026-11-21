<div>
    @if ($open)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 p-3 backdrop-blur-sm">
            <div class="flex max-h-[92vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

                {{-- ================================================= --}}
                {{-- HEADER --}}
                {{-- ================================================= --}}
                <div class="flex shrink-0 items-center justify-between border-b border-slate-200 bg-white px-5 py-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-extrabold text-[var(--color-primary)]">
                                Withdrawal Review
                            </h2>
                            @if ((int) ($withdrawal->user?->RoleID ?? 0) === 8)
                                <span class="inline-flex items-center rounded-full bg-[var(--color-accent)] px-2.5 py-0.5 text-[0.65rem] font-bold uppercase tracking-wide text-white">
                                    Satellite Member
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-[var(--color-primary)] px-2.5 py-0.5 text-[0.65rem] font-bold uppercase tracking-wide text-white">
                                    TRIFECTA Member
                                </span>
                            @endif
                        </div>
                        <p class="mt-1 text-xs text-slate-500">
                            Review the withdrawal request in full before approving or rejecting it.
                        </p>
                    </div>

                    <button wire:click="closeModal" type="button"
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-lg font-bold text-slate-500 transition hover:bg-red-100 hover:text-red-600"
                        title="Close">
                        ✕
                    </button>
                </div>

                {{-- ================================================= --}}
                {{-- BODY --}}
                {{-- ================================================= --}}
                <div class="flex-1 space-y-6 overflow-y-auto bg-slate-50 p-4 sm:p-6">

                    {{-- ================================================= --}}
                    {{-- MEMBER INFORMATION --}}
                    {{-- ================================================= --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="bg-[var(--color-primary)] px-5 py-3">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-white">
                                Member Information
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 lg:grid-cols-3">
                            {{-- FULL NAME --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Full Name
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    {{ trim(($withdrawal->user?->firstname ?? '').' '.($withdrawal->user?->middlename ?? '').' '.($withdrawal->user?->lastname ?? '')) ?: '-' }}
                                </p>
                            </div>

                            {{-- USERNAME --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Username
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    {{ $withdrawal->user?->username ?? '-' }}
                                </p>
                            </div>

                            {{-- CONTACT NUMBER --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Contact Number
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    {{ $withdrawal->user?->contactno ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- ================================================= --}}
                    {{-- WITHDRAWAL INFORMATION --}}
                    {{-- ================================================= --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="bg-[var(--color-primary)] px-5 py-3">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-white">
                                Withdrawal Information
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 lg:grid-cols-3">

                            {{-- WITHDRAWAL ID --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Withdrawal ID
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    #{{ $withdrawal->WithdrawID }}
                                </p>
                            </div>

                            {{-- WITHDRAWAL TYPE --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Type
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    {{ $withdrawal->type ?: '-' }}
                                </p>
                            </div>

                            {{-- REQUEST STATUS --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Request Status
                                </p>
                                <div class="mt-2">
                                    @php
                                        $statusClass = match (strtolower($withdrawal->status ?? 'Pending')) {
                                            'approved' => 'bg-[var(--color-primary-tint)] text-[var(--color-primary)]',
                                            'rejected' => 'bg-red-100 text-red-700',
                                            default => 'bg-amber-100 text-amber-700',
                                        };
                                    @endphp

                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">
                                        {{ $withdrawal->status ?? 'Pending' }}
                                    </span>
                                </div>
                            </div>

                            {{-- WITHDRAWAL METHOD --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Withdrawal Method
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    {{ ucfirst($withdrawal->withdrawal_method ?: '-') }}
                                </p>
                            </div>

                            {{-- BANK NAME (bank transfers only) --}}
                            @if (filled($withdrawal->bank_name))
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                        Bank Name
                                    </p>
                                    <p class="mt-1 font-bold text-slate-800">
                                        {{ $withdrawal->bank_name }}
                                    </p>
                                </div>
                            @endif

                            {{-- ACCOUNT NAME --}}
                            @if (filled($withdrawal->account_name))
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                        Account Name
                                    </p>
                                    <p class="mt-1 font-bold text-slate-800">
                                        {{ $withdrawal->account_name }}
                                    </p>
                                </div>
                            @endif

                            {{-- ACCOUNT NUMBER --}}
                            @if (filled($withdrawal->account_number))
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                        Account Number
                                    </p>
                                    <p class="mt-1 break-all font-bold text-slate-800">
                                        {{ $withdrawal->account_number }}
                                    </p>
                                </div>
                            @endif

                            {{-- PAYMENT REFERENCE --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Reference No.
                                </p>
                                <p class="mt-1 break-all font-bold text-slate-800">
                                    {{ $withdrawal->payment_reference ?: '-' }}
                                </p>
                            </div>

                            {{-- REQUEST DATE --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Requested On
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    {{ $withdrawal->created_at?->format('F d, Y h:i A') ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- ================================================= --}}
                    {{-- RELEASE BREAKDOWN --}}
                    {{-- ================================================= --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="bg-[var(--color-primary)] px-5 py-3">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-white">
                                Release Breakdown
                            </h3>
                        </div>

                        <div class="p-5">

                            {{-- REQUESTED AMOUNT --}}
                            <div class="flex items-center justify-between gap-4 py-2">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">
                                        Requested Amount
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        Gross withdrawal request
                                    </p>
                                </div>

                                <span class="text-sm font-bold text-slate-800">
                                    ₱{{ number_format((float) $withdrawal->requested_amount, 2) }}
                                </span>
                            </div>

                            {{-- 10% TAX --}}
                            <div class="flex items-center justify-between gap-4 border-t border-slate-100 py-2">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">
                                        Withholding Tax (10%)
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        Returned to the member's ledger
                                    </p>
                                </div>

                                <span class="text-sm font-bold text-slate-800">
                                    ₱{{ number_format((float) $withdrawal->tax_amount, 2) }}
                                </span>
                            </div>

                            {{-- NET RELEASE --}}
                            <div class="mt-3 flex items-center justify-between gap-4 rounded-xl bg-slate-50 px-4 py-3">
                                <div>
                                    <p class="text-sm font-extrabold text-[var(--color-primary)]">
                                        Net Amount to Release
                                    </p>
                                    <p class="mt-0.5 text-[11px] text-slate-500">
                                        Requested amount minus the 10% withholding tax
                                    </p>
                                </div>

                                <span class="text-xl font-extrabold text-[var(--color-primary)]">
                                    ₱{{ number_format((float) $withdrawal->amount, 2) }}
                                </span>
                            </div>

                            {{-- APPROVAL NOTE --}}
                            @if ($withdrawal->status === 'Pending')
                                <div class="mt-4 rounded-lg border border-[var(--color-primary-tint-border)] bg-[var(--color-primary-tint)] px-4 py-3">
                                    <p class="text-xs font-semibold text-[var(--color-primary-dark)]">
                                        Before You Approve
                                    </p>
                                    <p class="mt-1 text-[11px] leading-relaxed text-[var(--color-primary)]">
                                        Approving releases the net amount above: a wallet debit of
                                        ₱{{ number_format((float) $withdrawal->requested_amount, 2) }} and ledger
                                        records for the net release and the tax are written immediately.
                                        Verify the payout details and reference number first.
                                    </p>
                                </div>
                            @endif

                        </div>
                    </section>

                </div>

                {{-- ================================================= --}}
                {{-- FOOTER --}}
                {{-- ================================================= --}}
                <div class="flex shrink-0 flex-col-reverse justify-between gap-3 border-t border-slate-200 bg-white p-4 sm:flex-row sm:items-center">
                    <button wire:click="closeModal" type="button"
                        class="rounded-xl border border-slate-300 px-6 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100">
                        Close
                    </button>

                    <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
                        @if (! $this->approvalWindow()['open'])
                            <p class="text-[11px] font-semibold leading-relaxed text-amber-700 sm:w-full sm:text-right">
                                {{ $this->approvalWindow()['message'] }}
                            </p>
                        @endif

                        {{-- REJECT — needs can_change_status (or can_delete)
                             on Transaction List - Withdrawal --}}
                        @if (($this->canChangeStatus || $this->canDelete) && $withdrawal->status === 'Pending')
                            <button type="button" wire:click="confirmReject"
                                @disabled(! $this->approvalWindow()['open'])
                                title="{{ $this->approvalWindow()['message'] }}"
                                class="rounded-xl bg-[var(--color-accent)] hover:bg-[var(--color-accent-dark)] px-6 py-2.5 text-xs font-bold text-white transition disabled:cursor-not-allowed disabled:opacity-60 cursor-pointer">
                                Reject
                            </button>

                            {{-- APPROVE — needs can_change_status (or can_delete)
                                 on Transaction List - Withdrawal --}}
                            <button type="button" wire:click="confirmApprove"
                                @disabled(! $this->approvalWindow()['open'])
                                title="{{ $this->approvalWindow()['message'] }}"
                                class="rounded-xl bg-[var(--color-primary)] px-6 py-2.5 text-xs font-bold text-white transition hover:bg-[var(--color-primary-dark)] disabled:cursor-not-allowed disabled:opacity-60 cursor-pointer">
                                Approve & Release
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
