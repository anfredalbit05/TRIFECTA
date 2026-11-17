<div>
    @if ($open)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 p-3 backdrop-blur-sm">
            <div class="flex max-h-[92vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

                {{-- ================================================= --}}
                {{-- HEADER --}}
                {{-- ================================================= --}}
                <div class="flex shrink-0 items-center justify-between border-b border-slate-200 bg-white px-5 py-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-extrabold text-[var(--color-primary)]">
                                Activation Review
                            </h2>
                            <span class="inline-flex items-center rounded-full bg-[var(--color-accent)] px-2.5 py-0.5 text-[0.65rem] font-bold uppercase tracking-wide text-white">
                                Satellite Member
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">
                            Review the member's activation request and beneficiary information.
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
                {{-- SATELLITE INFORMATION --}}
                {{-- ================================================= --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="bg-[var(--color-primary)] px-5 py-3">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-white">
                            Satellite Information
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 lg:grid-cols-3">

                        {{-- SATELLITE NAME --}}
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                Satellite Name
                            </p>

                            <p class="mt-1 font-bold text-slate-800">
                                {{ $satellite?->satellite_name ?? '-' }}
                            </p>
                        </div>

                        {{-- SATELLITE TYPE --}}
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                Membership Type
                            </p>

                            <div class="mt-2">
                                <span
                                    @class([
                                        'inline-flex rounded-full px-3 py-1 text-xs font-bold',
                                        'bg-[var(--color-gold-tint)] text-[var(--color-gold-darker)]' => $satellite?->satellite_typeID == 6,
                                        'bg-slate-200 text-slate-700' => $satellite?->satellite_typeID == 7,
                                        'bg-[var(--color-accent-tint)] text-[var(--color-accent-dark)]' => $satellite?->satellite_typeID == 8,
                                    ])
                                >
                                    {{
                                        match ((int) ($satellite?->satellite_typeID ?? 0)) {
                                            6 => 'Bronze',
                                            7 => 'Silver',
                                            8 => 'Gold',
                                            default => '-',
                                        }
                                    }}
                                </span>
                            </div>
                        </div>

                        {{-- ADDRESS --}}
                        <div class="sm:col-span-2 lg:col-span-1">
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                Satellite Address
                            </p>

                            <p class="mt-1 font-bold text-slate-800">
                                {{ $satellite?->address ?? '-' }}
                            </p>
                        </div>


                    </div>
                </section>

                    {{-- ================================================= --}}
                    {{-- ACTIVATION INFORMATION --}}
                    {{-- ================================================= --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="bg-[var(--color-primary)] px-5 py-3">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-white">
                                Activation Information
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 lg:grid-cols-3">
                            {{-- ACTIVATION ID --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Activation ID
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    #{{ $activation?->ActivationID }}
                                </p>
                            </div>

                            {{-- AMOUNT --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Amount
                                </p>
                                <p class="mt-1 text-lg font-extrabold text-[var(--color-primary)]">
                                    ₱{{ number_format($activation?->amount ?? 0, 2) }}
                                </p>
                            </div>

                            {{-- PAYMENT METHOD --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Payment Method
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    {{ $activation?->payment_method ?? '-' }}
                                </p>
                            </div>

                            {{-- ACTIVATION TYPE --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Activation Type
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    {{ $activation?->type === 'W' ? 'Walk-in' : ($activation?->type ? 'Online' : '-') }}
                                </p>
                            </div>

                            {{-- PAYMENT REFERENCE --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Payment Reference
                                </p>
                                <p class="mt-1 break-all font-bold text-slate-800">
                                    {{ $activation?->payment_reference ?? 'Cash Payment' }}
                                </p>
                            </div>

                            {{-- REQUEST STATUS --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Request Status
                                </p>
                                <div class="mt-2">
                                    <span
                                        class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">
                                        {{ $activation?->status ?? 'Pending' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- ================================================= --}}
                    {{-- PAYMENT BREAKDOWN --}}
                    {{-- ================================================= --}}
                    @if ($activation)
                        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="bg-[var(--color-primary)] px-5 py-3">
                                <h3 class="text-sm font-bold uppercase tracking-wider text-white">
                                    Payment Breakdown
                                </h3>
                            </div>

                            <div class="p-5">

                                {{-- MEMBERSHIP FEE --}}
                                <div class="flex items-center justify-between gap-4 py-2">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-700">
                                            Satellite Membership Fee
                                        </p>
                                    </div>

                                    <span class="text-sm font-bold text-slate-800">
                                        ₱{{ number_format($activation->amount ?? 0, 2) }}
                                    </span>
                                </div>

                                {{-- GCASH CONVENIENCE FEE --}}
                                @if (
                                    strtolower(trim($activation->payment_method ?? '')) === 'gcash'
                                    && $gcashConvenienceFeeEnabled
                                )
                                    <div class="flex items-center justify-between gap-4 border-t border-slate-100 py-2">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-700">
                                                GCash Convenience Fee
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                1% convenience fee
                                            </p>
                                        </div>

                                        <span class="text-sm font-bold text-slate-800">
                                            ₱{{ number_format(($activation->amount ?? 0) * 0.01, 2) }}
                                        </span>
                                    </div>
                                @endif

                                {{-- PAYMAYA CONVENIENCE FEE --}}
                                @if (
                                    strtolower(trim($activation->payment_method ?? '')) === 'paymaya'
                                    && $paymayaConvenienceFeeEnabled
                                )
                                    <div class="flex items-center justify-between gap-4 border-t border-slate-100 py-2">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-700">
                                                Paymaya Convenience Fee
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                1% convenience fee
                                            </p>
                                        </div>

                                        <span class="text-sm font-bold text-slate-800">
                                            ₱{{ number_format(($activation->amount ?? 0) * 0.01, 2) }}
                                        </span>
                                    </div>
                                @endif

                                {{-- TOTAL --}}
                                @php
                                    $activationAmount = (float) ($activation->amount ?? 0);

                                    $hasConvenienceFee =
                                        (
                                            strtolower(trim($activation->payment_method ?? '')) === 'gcash'
                                            && $gcashConvenienceFeeEnabled
                                        )
                                        ||
                                        (
                                            strtolower(trim($activation->payment_method ?? '')) === 'paymaya'
                                            && $paymayaConvenienceFeeEnabled
                                        );

                                    $convenienceFee = $hasConvenienceFee
                                        ? $activationAmount * 0.01
                                        : 0;

                                    $totalAmount = $activationAmount + $convenienceFee;
                                @endphp

                                <div
                                    class="mt-3 flex items-center justify-between gap-4 rounded-xl bg-slate-50 px-4 py-3"
                                >
                                    <div>
                                        <p class="text-sm font-extrabold text-[var(--color-primary)]">
                                            Total Amount to Pay
                                        </p>

                                        @if ($hasConvenienceFee)
                                            <p class="mt-0.5 text-[11px] text-slate-500">
                                                Includes 1% convenience fee
                                            </p>
                                        @endif
                                    </div>

                                    <span class="text-xl font-extrabold text-[var(--color-primary)]">
                                        ₱{{ number_format($totalAmount, 2) }}
                                    </span>
                                </div>

                                {{-- ADMIN CHECK NOTE --}}
                                @if (strtolower(trim($activation->payment_method ?? '')) === 'gcash')
                                    <div class="mt-4 rounded-lg border border-[var(--color-primary-tint-border)] bg-[var(--color-primary-tint)] px-4 py-3">
                                        <p class="text-xs font-semibold text-[var(--color-primary-dark)]">
                                            Payment Verification
                                        </p>

                                        <p class="mt-1 text-[11px] leading-relaxed text-[var(--color-primary)]">
                                            Verify that the amount sent through GCash matches the
                                            total amount above. You may use the payment reference
                                            shown in the activation request to verify the transaction
                                            on the GCash account.
                                        </p>
                                    </div>
                                @endif

                            </div>
                        </section>
                    @endif

                    {{-- ================================================= --}}
                    {{-- REFERRAL INFORMATION --}}
                    {{-- ================================================= --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="bg-[var(--color-primary)] px-5 py-3">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-white">
                                Referral Information
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2">
                            {{-- REFERRAL CODE --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Upline Code
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    {{ $activation?->upline_code ?? '-' }}
                                </p>
                            </div>

                            {{-- REFERRAL MEMBER --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                    Referral Member
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    {{ trim(($referralUser?->firstname ?? '') . ' ' . ($referralUser?->lastname ?? '')) ?:
                                        'No referral member found' }}
                                </p>
                            </div>
                        </div>
                    </section>
                </div>

                {{-- ================================================= --}}
                {{-- FOOTER --}}
                {{-- ================================================= --}}
                <div
                    class="flex shrink-0 flex-col-reverse justify-between gap-3 border-t border-slate-200 bg-white p-4 sm:flex-row sm:items-center {{ $isPrivileged ? '' : 'justify-center sm:justify-center' }}">
                    <button wire:click="closeModal" type="button"
                        class="rounded-xl border border-slate-300 px-6 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100">
                        Close
                    </button>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        {{-- RESUBMIT — needs can_change_status (or can_delete)
                             on Transaction List - Activations. Opens the
                             satellite reapply wizard pre-filled from the
                             rejected application; the typed confirmation
                             happens on submit inside it. --}}
                        @if (($this->canChangeStatus || $this->canDelete) && $activation?->status === 'Rejected')
                            <button type="button" wire:click="openResubmitForm"
                                class="rounded-xl border border-[var(--color-primary)] bg-white px-6 py-2.5 text-xs font-bold text-[var(--color-primary)] transition hover:bg-[var(--color-primary)] hover:text-white">
                                ↺ Resubmit
                            </button>
                        @endif

                        {{-- REJECT — needs can_change_status (or can_delete)
                             on Transaction List - Activations --}}
                        @if (($this->canChangeStatus || $this->canDelete) && $activation?->status === 'Pending')
                        <button type="button" wire:click="confirmReject"
                            class="rounded-xl bg-[var(--color-accent)] hover:bg-[var(--color-accent-dark)] px-6 py-2.5 text-xs font-bold text-white transition cursor-pointer">
                            Reject
                        </button>

                        {{-- APPROVE — needs can_change_status (or can_delete)
                             on Transaction List - Activations --}}
                        <button type="button" wire:click="confirmApprove"
                            class="rounded-xl bg-[var(--color-primary)] px-6 py-2.5 text-xs font-bold text-white transition hover:bg-[var(--color-primary-dark)] cursor-pointer">
                            Approve Activation
                        </button>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    @endif

    {{-- swal:success, swal:error and the typed APPROVE/REJECT/RESUBMIT
         confirmations are handled globally by the shared sweet-alert-handler
         component (plain livewire:init script — inline @script handlers
         proved unreliable in this app). --}}
</div>
