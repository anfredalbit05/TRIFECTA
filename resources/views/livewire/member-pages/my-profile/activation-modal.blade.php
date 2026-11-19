<div>
    @if ($open)
        <div
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 p-2 sm:p-4 backdrop-blur-sm overflow-y-auto">
            <!-- MODAL CONTAINER -->
            <div
                class="bg-white rounded-2xl w-full max-w-4xl my-auto max-h-[92vh] sm:max-h-[90vh] flex flex-col shadow-2xl border border-slate-100 overflow-hidden">

                <!-- =====================================================
                    HEADER
                ====================================================== -->
                <div
                    class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 px-4 sm:px-6 py-4 border-b border-slate-200 sticky top-0 bg-white z-10 shadow-sm">
                    <div class="w-full sm:w-auto">
                        <h2 class="font-extrabold text-base sm:text-xl text-[var(--color-primary)]">
                            Account Activation
                        </h2>
                        <p class="mt-0.5 text-[11px] sm:text-xs text-slate-500">
                            TRIFECTA • Membership Activation
                        </p>

                        @if ($isRenewal)
                            {{-- RENEWAL NOTICE — the member's previous activation expired --}}
                            <div class="mt-3 flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2">
                                <span class="text-lg leading-none">⟳</span>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-semibold uppercase tracking-wide text-amber-700">
                                        Renewal Mode
                                    </p>
                                    <p class="text-[11px] sm:text-xs text-amber-800">
                                        This member's previous activation has <strong>expired</strong>.
                                        Submitting will file a <strong>new activation</strong> for review —
                                        the expired record stays as history.
                                    </p>
                                </div>
                            </div>
                        @endif
                        <div
                            class="mt-3 flex items-center gap-2.5 rounded-xl border border-[var(--color-primary-tint)] bg-[var(--color-primary-tint)] px-3 py-2">
                            <div
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[var(--color-primary)] text-xs font-bold text-white">
                                {{ strtoupper(substr($selectedUserRecord?->firstname ?? 'M', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                    Activating Member
                                </p>
                                <p class="text-xs sm:text-sm font-extrabold text-[var(--color-primary)] truncate">
                                    {{ trim(($selectedUserRecord?->firstname ?? '') . ' ' . ($selectedUserRecord?->middlename ?? '') . ' ' . ($selectedUserRecord?->lastname ?? '')) ?: 'Member not selected' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <button wire:click="closeModal" type="button"
                        class="absolute top-4 right-4 sm:relative sm:top-0 sm:right-0 w-9 h-9 shrink-0 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-rose-100 hover:text-rose-600 font-bold transition cursor-pointer">
                        ✕
                    </button>
                </div>

                <!-- =====================================================
                    MODAL BODY
                ====================================================== -->
                <div class="p-4 sm:p-6 space-y-6 overflow-y-auto">

                    <!-- =================================================
                        REMARKS
                    ================================================== -->
                    @if (!empty($rejectionRemarks))
                        <section class="space-y-4">
                            <h3
                                class="bg-[var(--color-primary)] text-white px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm tracking-wider uppercase shadow-sm">
                                Reason For Rejections
                            </h3>
                            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                                <p class="text-xs sm:text-sm text-red-600">
                                    {{ $rejectionRemarks }}
                                </p>
                            </div>
                        </section>
                    @endif

                    <!-- =================================================
                        BENEFICIARY INFORMATION
                    ================================================== -->
                    <section class="space-y-4">
                        <h3
                            class="bg-[var(--color-primary)] text-white px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm tracking-wider uppercase shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <span>Beneficiary Information</span>
                            <span class="text-[10px] font-normal text-slate-200 uppercase">Maximum 3 Dependents</span>
                        </h3>

                        <div class="space-y-4">
                            @foreach ($beneficiaries as $index => $beneficiary)
                                <div
                                    class="p-3 sm:p-4 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- FULL NAME -->
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Beneficiary {{ $index + 1 }} Full Name
                                        </label>
                                        <input wire:model="beneficiaries.{{ $index }}.fullname" type="text"
                                            placeholder="Full name of beneficiary"
                                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none bg-white">
                                        @error("beneficiaries.$index.fullname")
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- BIRTHDATE -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Birthdate
                                        </label>
                                        <input wire:model="beneficiaries.{{ $index }}.birthdate" type="date"
                                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none bg-white">
                                        @error("beneficiaries.$index.birthdate")
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- RELATIONSHIP -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Relationship
                                        </label>
                                        <input wire:model="beneficiaries.{{ $index }}.relationship"
                                            type="text" placeholder="Mother, Father, Spouse..."
                                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none bg-white">
                                        @error("beneficiaries.$index.relationship")
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                {{-- =================================================
                    0. SELECT MEMBERSHIP VARIANT
                ================================================== --}}

                <section class="space-y-5">
                    <div class="rounded-xl bg-[var(--color-primary)] px-4 py-2.5 shadow-sm">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-white sm:text-sm">
                            Select Membership Variant
                        </h3>
                        <p class="mt-0.5 text-[10px] text-slate-200">
                            Please select the variant for this membership application.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-3 pt-1 sm:grid-cols-2 lg:grid-cols-2">
                        @forelse ($variants as $variant)
                            <div
                                wire:key="variant-option-{{ $variant['VID'] }}"
                                class="rounded-xl border p-4 transition
                                    {{ (string) $selectedVariantId === (string) $variant['VID']
                                        ? 'border-[var(--color-primary)] bg-[var(--color-primary-tint)] ring-2 ring-[var(--color-primary)]/20'
                                        : 'border-slate-200 bg-white hover:border-slate-400' }}"
                            >
                                <label class="flex cursor-pointer items-center gap-3">
                                    <input
                                        type="radio"
                                        wire:model.live="selectedVariantId"
                                        value="{{ $variant['VID'] }}"
                                        class="h-4 w-4 shrink-0 border-slate-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)]"
                                    >

                                    <div class="min-w-0 flex-1">
                                        <span class="block text-sm font-semibold text-slate-700">
                                            {{ $variant['name'] }}
                                        </span>

                                        <span class="mt-1 block text-lg font-extrabold text-[var(--color-primary)]">
                                            ₱{{ number_format(
                                                (float) \App\Models\MembershipFee::where('VID', $variant['VID'])->sum('Cost'),
                                                2
                                            ) }}
                                        </span>

                                        <span class="mt-0.5 block text-[10px] text-slate-500">
                                            Total Membership Fees
                                        </span>
                                    </div>
                                </label>

                                <div class="mt-3 flex justify-end border-t border-slate-200 pt-3">
                                    <button
                                        type="button"
                                        wire:click="viewBenefits({{ $variant['VID'] }})"
                                        class="inline-flex items-center gap-1 text-xs font-bold text-[var(--color-primary)] hover:text-[var(--color-primary-dark)] hover:underline cursor-pointer"
                                    >
                                        View Benefits
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-3.5 w-3.5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="col-span-full rounded-xl border border-dashed border-slate-300 p-4 text-sm text-slate-500">
                                No variants are available.
                            </p>
                        @endforelse
                    </div>

                    @error('selectedVariantId')
                        <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </section>



                    <!-- =================================================
                        PAYMENT INFORMATION
                    ================================================== -->
                    <section class="space-y-5">
                        <h3
                            class="bg-[var(--color-primary)] text-white px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm tracking-wider uppercase shadow-sm">
                            Payment Information
                        </h3>

                        <!-- PAYMENT METHOD SELECTION -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-3">
                                Select Payment Method *
                            </label>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <!-- CASH -->
                                <label
                                    class="flex items-center gap-3 cursor-pointer p-3.5 bg-white border border-slate-300 rounded-xl hover:border-[var(--color-primary)] hover:bg-slate-50 transition">
                                    <input type="radio" wire:model.live="payment_method" value="Cash"
                                        class="w-4 h-4 text-[var(--color-primary)] shrink-0">
                                    <div>
                                        <span class="block text-sm font-bold text-slate-700">Cash</span>
                                        <span class="block text-[11px] text-slate-500">Walk-in Payment</span>
                                    </div>
                                </label>

                                <!-- GCASH -->
                                <label
                                    class="flex items-center gap-3 p-3.5 rounded-xl border transition
                                        {{ $gcashEnabled
                                            ? 'cursor-pointer bg-white border-slate-300 hover:border-[var(--color-primary)] hover:bg-slate-50'
                                            : 'cursor-not-allowed bg-slate-100 border-slate-200 opacity-60' }}">
                                    <input type="radio" wire:model.live="payment_method" value="GCash"
                                        @disabled(!$gcashEnabled) class="w-4 h-4 text-[var(--color-primary)] shrink-0">

                                    <div>
                                        <span class="block text-sm font-bold text-slate-700">
                                            GCash
                                            @if (!$gcashEnabled)
                                                <span class="text-[10px] text-red-500">(Unavailable)</span>
                                            @endif
                                        </span>

                                        <span class="block text-[11px] text-slate-500">
                                            {{ $gcashEnabled ? 'Online Payment' : 'Currently unavailable' }}
                                        </span>
                                    </div>
                                </label>

                                <!-- PAYMAYA -->
                                <label
                                    class="flex items-center gap-3 p-3.5 rounded-xl border transition
                                        {{ $paymayaEnabled
                                            ? 'cursor-pointer bg-white border-slate-300 hover:border-[var(--color-primary)] hover:bg-slate-50'
                                            : 'cursor-not-allowed bg-slate-100 border-slate-200 opacity-60' }}">
                                    <input type="radio" wire:model.live="payment_method" value="Maya"
                                        @disabled(!$paymayaEnabled) class="w-4 h-4 text-[var(--color-primary)] shrink-0">

                                    <div>
                                        <span class="block text-sm font-bold text-slate-700">
                                            Maya
                                            @if (!$paymayaEnabled)
                                                <span class="text-[10px] text-red-500">(Unavailable)</span>
                                            @endif
                                        </span>

                                        <span class="block text-[11px] text-slate-500">
                                            {{ $paymayaEnabled ? 'Online Payment' : 'Currently unavailable' }}
                                        </span>
                                    </div>
                                </label>
                                <!-- BANK TRANSFER -->
                                <label
                                    class="flex items-center gap-3 p-3.5 rounded-xl border transition
                                        {{ $bankEnabled
                                            ? 'cursor-pointer bg-white border-slate-300 hover:border-[var(--color-primary)] hover:bg-slate-50'
                                            : 'cursor-not-allowed bg-slate-100 border-slate-200 opacity-60' }}">
                                    <input type="radio" wire:model.live="payment_method" value="Bank Transfer"
                                        @disabled(!$bankEnabled) class="w-4 h-4 text-[var(--color-primary)] shrink-0">

                                    <div>
                                        <span class="block text-sm font-bold text-slate-700">
                                            Bank Transfer
                                            @if (!$bankEnabled)
                                                <span class="text-[10px] text-red-500">(Unavailable)</span>
                                            @endif
                                        </span>

                                        <span class="block text-[11px] text-slate-500">
                                            {{ $bankEnabled ? 'Online Payment' : 'Currently unavailable' }}
                                        </span>
                                    </div>
                                </label>
                            </div>

                            @error('payment_method')
                                <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ACTIVATION AMOUNT AND REFERRAL CODE -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- ACTIVATION AMOUNT -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Activation Amount
                                </label>
                                <input
                                    value="₱{{ number_format((float) $amount, 2) }}"
                                    readonly
                                    type="text"
                                    class="w-full bg-slate-100 border border-slate-300 rounded-xl px-3 py-2.5 text-sm font-bold text-[var(--color-primary)]"
                                >
                            </div>

                            <!-- REFERRAL CODE -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Upline Code *
                                </label>
                                <input wire:model.live.debounce.500ms="upline_code" type="text" placeholder="Enter upline code"
                                    @readonly($selectedUserRecord?->status === 'Active')
                                    class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm
        focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none
        {{ $selectedUserRecord?->status === 'Active' ? 'bg-slate-100 text-slate-600 cursor-not-allowed' : 'bg-white' }}">
                                @error('upline_code')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- CASH PAYMENT MESSAGE -->
                        @if ($payment_method === 'Cash')
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                                <p class="text-sm font-bold text-amber-800">Cash Payment — Walk-in</p>
                                <p class="mt-1 text-xs text-amber-700">Please pay the ₱1,999 activation fee at the
                                    authorized office or designated payment location.</p>
                                <p class="mt-2 text-[11px] text-amber-600">Your activation will be recorded as Walk-in
                                    (W).</p>
                            </div>
                        @endif

                        <!-- ONLINE PAYMENT REFERENCE -->
                        @if ($payment_method === 'GCash' || $payment_method === 'Maya' || $payment_method === 'Bank Transfer')
                            <div class="space-y-3 rounded-xl border border-[var(--color-primary-tint-border)] bg-[var(--color-primary-tint)] p-4">
                                <div>
                                    <p class="text-sm font-bold text-[var(--color-primary-dark)]">Online Payment</p>
                                    <p class="mt-1 text-xs text-[var(--color-primary)]">Complete your payment first, then enter the
                                        transaction reference number below.</p>
                                </div>

                                {{-- GCASH DETAILS --}}
                                @if ($payment_method === 'GCash')
                                    <div class="rounded-lg border border-[var(--color-primary-tint-border)] bg-white p-3 text-sm space-y-3">

                                        <!-- GCASH ACCOUNT -->
                                        <div>
                                            <p class="mb-1">
                                                <span class="font-bold text-slate-700">Send to (GCash Name):</span>
                                                {{ $gcashName }}
                                            </p>

                                            <p>
                                                <span class="font-bold text-slate-700">GCash Number:</span>
                                                {{ $gcashNumber }}
                                            </p>
                                        </div>

                                        <!-- PAYMENT BREAKDOWN -->
                                        <div class="border-t border-slate-200 pt-3">

                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                                                Payment Breakdown
                                            </p>

                                            <div class="space-y-1.5 text-sm">

                                                <!-- MEMBERSHIP FEE -->
                                                <div class="flex justify-between gap-4">
                                                    <span class="text-slate-600">
                                                        Membership Fee
                                                    </span>

                                                    <span class="font-semibold text-slate-700">
                                                        ₱{{ number_format($totalMembershipFeesCost, 2) }}
                                                    </span>
                                                </div>

                                                @if ($gcashConvenienceFeeEnabled)
                                                    <!-- GCASH CF -->
                                                    <div class="flex justify-between gap-4">
                                                        <span class="text-slate-600">
                                                            GCash Convenience Fee (1%)
                                                        </span>

                                                        <span class="font-semibold text-slate-700">
                                                            ₱{{ number_format($totalMembershipFeesCost * 0.01, 2) }}
                                                        </span>
                                                    </div>

                                                    <!-- TOTAL -->
                                                    <div
                                                        class="flex justify-between gap-4 border-t border-slate-200 pt-2 mt-2">
                                                        <span class="font-extrabold text-[var(--color-primary)]">
                                                            Total Amount to Pay
                                                        </span>

                                                        <span class="font-extrabold text-[var(--color-primary)]">
                                                            ₱{{ number_format($totalMembershipFeesCost + $totalMembershipFeesCost * 0.01, 2) }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <!-- TOTAL WITHOUT CF -->
                                                    <div
                                                        class="flex justify-between gap-4 border-t border-slate-200 pt-2 mt-2">
                                                        <span class="font-extrabold text-[var(--color-primary)]">
                                                            Total Amount to Pay
                                                        </span>

                                                        <span class="font-extrabold text-[var(--color-primary)]">
                                                            ₱{{ number_format($totalMembershipFeesCost, 2) }}
                                                        </span>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>

                                        @if ($gcashConvenienceFeeEnabled)
                                            <div class="rounded-lg bg-amber-50 border border-amber-200 px-3 py-2">
                                                <p class="text-[11px] text-amber-700">
                                                    1% GCash convenience fee (₱19.99) is included in the total
                                                    payment.
                                                </p>
                                            </div>
                                        @endif

                                    </div>
                                @endif
                                {{-- PAYMAYA DETAILS --}}
                                @if ($payment_method === 'Maya')
                                    <div class="rounded-lg border border-[var(--color-primary-tint-border)] bg-white p-3 text-sm space-y-3">

                                        <!-- PAYMAYA ACCOUNT -->
                                        <div>
                                            <p class="mb-1">
                                                <span class="font-bold text-slate-700">Send to (PayMaya Name):</span>
                                                {{ $paymayaName }}
                                            </p>

                                            <p>
                                                <span class="font-bold text-slate-700">PayMaya Number:</span>
                                                {{ $paymayaNumber }}
                                            </p>
                                        </div>

                                        <!-- PAYMENT BREAKDOWN -->
                                        <div class="border-t border-slate-200 pt-3">

                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                                                Payment Breakdown
                                            </p>

                                            <div class="space-y-1.5 text-sm">

                                                <!-- MEMBERSHIP FEE -->
                                                <div class="flex justify-between gap-4">
                                                    <span class="text-slate-600">
                                                        Membership Fee
                                                    </span>

                                                    <span class="font-semibold text-slate-700">
                                                        ₱{{ number_format($totalMembershipFeesCost, 2) }}
                                                    </span>
                                                </div>

                                                @if ($paymayaConvenienceFeeEnabled)
                                                    <!-- PAYMAYA CF -->
                                                    <div class="flex justify-between gap-4">
                                                        <span class="text-slate-600">
                                                            PayMaya Convenience Fee (1%)
                                                        </span>

                                                        <span class="font-semibold text-slate-700">
                                                            ₱{{ number_format($totalMembershipFeesCost * 0.01, 2) }}
                                                        </span>
                                                    </div>

                                                    <!-- TOTAL -->
                                                    <div
                                                        class="flex justify-between gap-4 border-t border-slate-200 pt-2 mt-2">
                                                        <span class="font-extrabold text-[var(--color-primary)]">
                                                            Total Amount to Pay
                                                        </span>

                                                        <span class="font-extrabold text-[var(--color-primary)]">
                                                            ₱{{ number_format($totalMembershipFeesCost + $totalMembershipFeesCost * 0.01, 2) }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <!-- TOTAL WITHOUT CF -->
                                                    <div
                                                        class="flex justify-between gap-4 border-t border-slate-200 pt-2 mt-2">
                                                        <span class="font-extrabold text-[var(--color-primary)]">
                                                            Total Amount to Pay
                                                        </span>

                                                        <span class="font-extrabold text-[var(--color-primary)]">
                                                            ₱{{ number_format($totalMembershipFeesCost, 2) }}
                                                        </span>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>

                                        @if ($paymayaConvenienceFeeEnabled)
                                            <div class="rounded-lg bg-amber-50 border border-amber-200 px-3 py-2">
                                                <p class="text-[11px] text-amber-700">
                                                    1% PayMaya convenience fee (₱19.99) is included in the total
                                                    payment.
                                                </p>
                                            </div>
                                        @endif

                                    </div>
                                @endif

                                {{-- BANK DETAILS --}}
                                @if ($payment_method === 'Bank Transfer')
                                    <div class="rounded-lg border border-[var(--color-primary-tint-border)] bg-white p-3 text-sm">
                                        <p class="mb-1"><span class="font-bold text-slate-700">Bank:</span>
                                            {{ $bankName }}</p>
                                        <p class="mb-1"><span class="font-bold text-slate-700">Account Name:</span>
                                            {{ $bankAccountName }}</p>
                                        <p><span class="font-bold text-slate-700">Account Number:</span>
                                            {{ $bankAccountNumber }}</p>
                                    </div>

                                    <!-- TOTAL WITHOUT CF -->
                                    <div class="flex justify-between gap-4 border-t border-slate-200 pt-2 mt-2">
                                        <span class="font-extrabold text-[var(--color-primary)]">
                                            Total Amount to Pay
                                        </span>

                                        <span class="font-extrabold text-[var(--color-primary)]">
                                            ₱{{ number_format($totalMembershipFeesCost, 2) }}
                                        </span>
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">
                                        Payment Reference Code *
                                    </label>
                                    <input wire:model="reference_code" type="text"
                                        placeholder="Enter reference code where you sent your payment"
                                        class="w-full bg-white border border-[var(--color-primary-light)] rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                                    @error('reference_code')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <p class="text-[11px] text-[var(--color-primary)]">Your activation will be recorded as Online (O).
                                </p>
                            </div>
                        @endif
                    </section>

                    <section class="space-y-5">
                        <h3 class="bg-[var(--color-primary)] text-white px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm tracking-wider uppercase shadow-sm">
                            Member Classification
                        </h3>

                        {{-- EMPLOYMENT TYPE --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                               Employment Type
                            @if ($lockEmploymentType)
                                <span class="text-red-500">*</span>
                            @else
                                <span class="font-normal text-slate-400">(optional)</span>
                            @endif
                            </label>
                            <select wire:model="employment_type_id"
                                class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm bg-white focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                                @unless ($lockEmploymentType)
                                    <option value="">No Employment Type</option>
                                @endunless
                                @foreach ($employmentTypes as $type)
                                    <option value="{{ $type['employment_type_id'] }}">{{ $type['name'] }}</option>
                                @endforeach
                            </select>
                            @error('employment_type_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- MEMBER TYPE (only Active ones are loaded) --}}
                        @if (count($memberTypes))
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-3">Member Type</label>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach ($memberTypes as $type)
                                        <label wire:key="member-type-{{ $type['member_type_id'] }}"
                                            class="flex items-center gap-3 cursor-pointer p-3.5 rounded-xl border transition
                                            {{ (string) $member_type_id === (string) $type['member_type_id']
                                                ? 'border-[var(--color-primary)] bg-[var(--color-primary-tint)] ring-2 ring-[var(--color-primary)]/20'
                                                : 'bg-white border-slate-300 hover:border-[var(--color-primary)] hover:bg-slate-50' }}">
                                            <input type="radio" wire:model.live="member_type_id"
                                                value="{{ $type['member_type_id'] }}"
                                                class="w-4 h-4 text-[var(--color-primary)] shrink-0">
                                            <span class="block text-sm font-bold text-slate-700">{{ $type['name'] }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                @error('member_type_id')
                                    <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </section>

                    <!-- =================================================
                        CONFIRMATION
                    ================================================== -->
                    <section class="space-y-3">
                        <h3
                            class="bg-[var(--color-primary)] text-white px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm tracking-wider uppercase shadow-sm">
                            Confirmation
                        </h3>
                        <div
                            class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs sm:text-sm text-slate-600">
                            Please verify all beneficiary information and payment details before
                            @if ($isRenewal) renewing @else activating @endif
                            your account.
                        </div>
                    </section>
                </div>

                <!-- =====================================================
                    FOOTER
                ====================================================== -->
                <div
                    class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <span class="text-xs text-slate-500 text-center sm:text-left">
                        * Activation fee is fixed at {{ $amount }}
                    </span>

                    <div class="flex flex-col-reverse sm:flex-row gap-2.5 w-full sm:w-auto">
                        <!-- CANCEL BUTTON -->
                        <button wire:click="closeModal" type="button"
                            class="w-full sm:w-auto px-5 py-2.5 border0 bg-[var(--color-accent)] text-white rounded-xl shadow-md transition cursor-pointer">
                            Cancel
                        </button>

                        <!-- ACTIVATE BUTTON -->
                        <button type="button" wire:click="confirmActivate" wire:loading.attr="disabled"
                            class="w-full sm:w-auto px-6 py-2.5 bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center cursor-pointer">
                            <span wire:loading.remove wire:target="confirmActivate">
                                @if ($isRenewal)
                                    Renew Activation
                                @elseif ($isReactivation)
                                    ReActivate Account
                                @else
                                    Activate Account
                                @endif
                            </span>
                            <span wire:loading wire:target="confirmActivate">
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif



@if ($showBenefitsModal)
    <div
        class="fixed inset-0 z-[10000] flex items-center justify-center bg-slate-950/70 p-3 backdrop-blur-sm sm:p-6"
        wire:key="benefits-modal-{{ $benefitsVariantId }}"
        wire:click.self="closeBenefitsModal"
    >
        <div
            class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl sm:rounded-3xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="benefits-modal-title"
        >
            {{-- HEADER --}}
            <div class="relative shrink-0 overflow-hidden bg-[var(--color-primary)] px-5 py-5 sm:px-7 sm:py-6">
                <div class="absolute -right-10 -top-12 h-40 w-40 rounded-full bg-white/5"></div>
                <div class="absolute -bottom-16 right-24 h-32 w-32 rounded-full bg-[var(--color-accent)]/10"></div>

                <div class="relative flex items-start justify-between gap-4">
                    <div class="flex min-w-0 items-start gap-3 sm:gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-white/15 bg-white/10 text-white sm:h-14 sm:w-14">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 3l2.4 4.86L20 8.67l-4 3.9.94 5.5L12 15.47l-4.94 2.6.94-5.5-4-3.9 5.6-.81L12 3Z" />
                            </svg>
                        </div>

                        <div class="min-w-0 pt-0.5">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-white sm:text-xs">
                                Membership Plan
                            </p>

                            <h2 id="benefits-modal-title"
                                class="mt-1 text-xl font-extrabold leading-tight text-white sm:text-2xl">
                                Membership Benefits
                            </h2>

                            <p class="mt-1.5 break-words text-xs text-[var(--color-primary-tint)] sm:text-sm">
                                {{ $benefitsVariantName }}
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        wire:click="closeBenefitsModal"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-white/15 bg-white/10 text-white transition hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)]"
                        aria-label="Close benefits"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="relative mt-5 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-[11px] font-semibold text-white">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-3.5 w-3.5 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m5 12 4 4L19 6" />
                        </svg>
                        Included benefits
                    </span>

                    <span class="inline-flex items-center rounded-full bg-[var(--color-accent)] px-3 py-1.5 text-[11px] font-bold text-white">
                        Variant #{{ $benefitsVariantId }}
                    </span>
                </div>
            </div>


            {{-- BODY --}}
            <div class="min-h-0 flex-1 space-y-6 overflow-y-auto bg-slate-50 p-4 sm:p-6">

                @php
                    $benefitGroups = collect($variantBenefits)->groupBy('holdertype');
                    $totalBenefits = collect($variantBenefits)->count();
                @endphp

                {{-- INTRODUCTION --}}
                <div class="flex items-start gap-3 rounded-xl border border-[var(--color-primary-tint)] bg-white p-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[var(--color-primary-tint)] text-[var(--color-primary)]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586A1 1 0 0 1 13.293 3.293L18.707 8.707A1 1 0 0 1 19 9.414V19a2 2 0 0 1-2 2Z" />
                        </svg>
                    </div>

                    <div>
                        <h3 class="text-sm font-extrabold text-[var(--color-primary)]">
                            Membership Benefits Breakdown
                        </h3>
                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Review the benefits and corresponding amounts included in this membership variant.
                        </p>
                    </div>
                </div>

                {{-- BENEFIT TABLES --}}
                @forelse ($benefitGroups as $holdertype => $benefits)
                    <section wire:key="benefit-group-{{ $benefitsVariantId }}-{{ Str::slug($holdertype) }}"
                        class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

                        {{-- TABLE SECTION HEADER --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-[var(--color-primary)] px-4 py-3 sm:px-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10 text-white">
                                    @if ($holdertype === 'Primary Owner')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m12-13a4 4 0 1 1-8 0 4 4 0 0 1 8 0Zm2 4a4 4 0 0 1 4 4v2" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M17 20h5v-2a4 4 0 0 0-5-3.87M9 20H2v-2a4 4 0 0 1 4-4h3m6-9a4 4 0 1 1-8 0 4 4 0 0 1 8 0Zm5 4a3 3 0 1 0 0-6" />
                                        </svg>
                                    @endif
                                </div>

                                <div>
                                    <h3 class="text-sm font-extrabold text-white">
                                        {{ $holdertype }}
                                    </h3>
                                    <p class="mt-0.5 text-[11px] text-white">
                                        {{ $benefits->count() }}
                                        {{ $benefits->count() === 1 ? 'benefit' : 'benefits' }}
                                    </p>
                                </div>
                            </div>

                            <span class="rounded-full bg-[var(--color-accent)] px-3 py-1 text-xs font-extrabold text-white">
                                {{ $benefits->count() }} items
                            </span>
                        </div>

                        {{-- RESPONSIVE TABLE --}}
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[560px] border-collapse text-left">
                                <thead>
                                    <tr class="border-b border-slate-200 bg-slate-100">
                                        <th class="w-12 px-4 py-3 text-center text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                                            #
                                        </th>
                                        <th class="px-4 py-3 text-xs font-extrabold uppercase tracking-wider text-slate-600">
                                            Benefit Name
                                        </th>
                                        <th class="px-4 py-3 text-xs font-extrabold uppercase tracking-wider text-slate-600">
                                            Details
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-extrabold uppercase tracking-wider text-slate-600">
                                            Amount
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($benefits as $index => $benefit)
                                        <tr wire:key="benefit-{{ $benefit['BentID'] }}"
                                            class="transition hover:bg-[var(--color-primary-tint)]/60">

                                            <td class="px-4 py-4 text-center text-xs font-semibold text-slate-400">
                                                {{ $index + 1 }}
                                            </td>

                                            <td class="px-4 py-4">
                                                <div class="flex items-start gap-2.5">
                                                    <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-[var(--color-primary-tint)] text-[var(--color-primary)]">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m5 12 4 4L19 6" />
                                                        </svg>
                                                    </div>

                                                    <span class="pt-1 text-sm font-bold text-slate-800">
                                                        {{ $benefit['name'] }}
                                                    </span>
                                                </div>
                                            </td>

                                            <td class="max-w-xs whitespace-normal break-words px-4 py-4 text-xs leading-5 text-slate-500">
                                                {{ !empty($benefit['details']) ? $benefit['details'] : 'No details provided' }}
                                            </td>

                                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                                <span class="inline-flex rounded-lg bg-[var(--color-primary-tint)] px-3 py-1.5 text-sm font-extrabold tabular-nums text-[var(--color-primary)]">
                                                    ₱{{ number_format((float) ($benefit['amount'] ?? 0), 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                {{-- TABLE TOTAL --}}
                                <tfoot>
                                    <tr class="border-t-2 border-slate-200 bg-slate-50">
                                        <td colspan="3" class="px-4 py-4 text-right text-xs font-extrabold uppercase tracking-wide text-slate-600">
                                            Total listed benefit amounts
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-4 text-right text-sm font-extrabold tabular-nums text-[var(--color-primary)]">
                                            ₱{{ number_format($benefits->sum(fn ($benefit) => (float) ($benefit['amount'] ?? 0)), 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </section>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-300 bg-white px-5 py-12 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586A1 1 0 0 1 13.293 3.293L18.707 8.707A1 1 0 0 1 19 9.414V19a2 2 0 0 1-2 2Z" />
                            </svg>
                        </div>

                        <h3 class="mt-4 text-sm font-extrabold text-slate-800">
                            No active benefits available
                        </h3>
                        <p class="mt-1 text-xs text-slate-500">
                            No active benefits have been configured for this variant.
                        </p>
                    </div>
                @endforelse
            </div>


            {{-- FOOTER --}}
            <div class="flex shrink-0 flex-col gap-3 border-t border-slate-200 bg-white px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-7">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[var(--color-primary-tint)] text-[var(--color-primary)]">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>
                    <span>
                        {{ $totalBenefits }} total
                        {{ $totalBenefits === 1 ? 'benefit' : 'benefits' }} listed
                    </span>
                </div>

                <button
                    type="button"
                    wire:click="closeBenefitsModal"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[var(--color-primary)] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[var(--color-primary-dark)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:ring-offset-2 sm:w-auto"
                >
                    Close Details
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m9 18 6-6-6-6" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endif


</div>
