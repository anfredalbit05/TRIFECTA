<div>
    @if ($open)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 p-3 backdrop-blur-sm">
            <div class="flex max-h-[92vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

                {{-- ================================================= --}}
                {{-- HEADER --}}
                {{-- ================================================= --}}
                <div class="flex shrink-0 items-center justify-between border-b border-slate-200 bg-white px-5 py-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-[#0B2A5B]">
                            Activation Review
                        </h2>
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
                    {{-- MEMBER INFORMATION --}}
                    {{-- ================================================= --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="bg-[#0B2A5B] px-5 py-3">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-[#E8B923]">
                                Member Information
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 lg:grid-cols-3">
                            {{-- FULL NAME --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                    Full Name
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    {{ trim(($member?->firstname ?? '') . ' ' . ($member?->middlename ?? '') . ' ' . ($member?->lastname ?? '')) ?:
                                        '-' }}
                                </p>
                            </div>

                            {{-- USERNAME --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                    Username
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    {{ $member?->username ?? '-' }}
                                </p>
                            </div>

                            {{-- CONTACT NUMBER --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                    Contact Number
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    {{ $member?->contactno ?? '-' }}
                                </p>
                            </div>

                            {{-- GENDER --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                    Gender
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    {{ $member?->gender ?? '-' }}
                                </p>
                            </div>

                            {{-- BIRTHDATE --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                    Birthdate
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    {{ $member?->birthdate ? \Carbon\Carbon::parse($member->birthdate)->format('F d, Y') : '-' }}
                                </p>
                            </div>

                            {{-- MEMBER STATUS --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                    Current Status
                                </p>
                                <div class="mt-2">
                                    <span @class([
                                        'inline-flex rounded-full px-3 py-1 text-xs font-bold',
                                        'bg-emerald-100 text-emerald-700' => $member?->status === 'Active',
                                        'bg-red-100 text-red-700' => $member?->status !== 'Active',
                                    ])>
                                        {{ $member?->status ?? 'Unknown' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>

{{-- ================================================= --}}
{{-- ACTIVATION INFORMATION --}}
{{-- ================================================= --}}
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

    {{-- HEADER --}}
    <div class="bg-[#0B2A5B] px-5 py-3">
        <h3 class="text-sm font-bold uppercase tracking-wider text-[#E8B923]">
            Activation Information
        </h3>
    </div>

    {{-- CONTENT --}}
    <div class="space-y-5 p-5">

        {{-- BASIC INFORMATION --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

            {{-- ACTIVATION ID --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Activation ID
                </p>

                <p class="mt-1 font-bold text-slate-800">
                    #{{ $activation?->ActivationID ?? '-' }}
                </p>
            </div>


            {{-- AMOUNT --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Amount
                </p>

                <p class="mt-1 text-lg font-extrabold text-emerald-600">
                    ₱{{ number_format($activation?->amount ?? 0, 2) }}
                </p>
            </div>


            {{-- PAYMENT METHOD --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Payment Method
                </p>

                <p class="mt-1 font-bold text-slate-800">
                    {{ $activation?->payment_method ?? '-' }}
                </p>
            </div>


            {{-- ACTIVATION TYPE --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Activation Type
                </p>

                <p class="mt-1 font-bold text-slate-800">
                    {{ $activation?->type === 'W'
                        ? 'Walk-in'
                        : ($activation?->type ? 'Online' : '-') }}
                </p>
            </div>


            {{-- PAYMENT REFERENCE --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Payment Reference
                </p>

                <p class="mt-1 break-all font-bold text-slate-800">
                    {{ $activation?->payment_reference ?? 'Cash Payment' }}
                </p>
            </div>


            {{-- REQUEST STATUS --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Request Status
                </p>

                <div class="mt-2">
                    @php
                        $status = $activation?->status ?? 'Pending';

                        $statusClass = match (strtolower($status)) {
                            'approved' => 'bg-emerald-100 text-emerald-700',
                            'rejected' => 'bg-red-100 text-red-700',
                            'cancelled' => 'bg-slate-200 text-slate-700',
                            default => 'bg-amber-100 text-amber-700',
                        };
                    @endphp

                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">
                        {{ $status }}
                    </span>
                </div>
            </div>

        </div>


        {{-- POLICY + RECEIPT --}}
        <div class="grid grid-cols-1 gap-5 border-t border-slate-100 pt-5 lg:grid-cols-2">

            {{-- POLICY NUMBER --}}
            <div>
                <div class="mb-1.5 flex items-center justify-between gap-2">

                    <label class="text-xs font-bold text-slate-700">
                        Policy Number
                        <span class="text-red-500">*</span>
                    </label>

                    <button
                        type="button"
                        wire:click="generatePolicyNumber"
                        class="rounded-lg bg-[#0B2A5B] px-3 py-1.5 text-[11px] font-bold text-white transition hover:bg-blue-800"
                    >
                        Generate
                    </button>

                </div>

                <input
                    id="policy-number-input"
                    wire:model.live="policy_number"
                    type="text"
                    maxlength="100"
                    placeholder="TRI-00001-TS2026-00001"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm uppercase focus:border-[#0B2A5B] focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                >

                @error('policy_number')
                    <p class="mt-1 text-xs font-semibold text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                <p class="mt-1 text-[10px] text-slate-400">
                    Next:
                    <span
                        id="next-policy-number"
                        data-sequence="{{ $this->nextPolicySequenceForJs }}"
                        class="font-bold text-slate-600"
                    >
                        {{ $this->nextPolicyNumber }}
                    </span>
                    — backlog numbers below this are allowed if unused.
                </p>
            </div>


            {{-- RECEIPT NUMBER --}}
            <div>
                <div class="mb-1.5 flex items-center justify-between gap-2">

                    <label class="text-xs font-bold text-slate-700">
                        Receipt Number
                        <span class="text-red-500">*</span>
                    </label>

                    <button
                        type="button"
                        wire:click="generateReceiptNumber"
                        class="rounded-lg bg-[#0B2A5B] px-3 py-1.5 text-[11px] font-bold text-white transition hover:bg-blue-800"
                    >
                        Generate
                    </button>

                </div>

                <input
                    id="receipt-number-input"
                    wire:model.live="receipt_number"
                    type="text"
                    inputmode="numeric"
                    maxlength="6"
                    placeholder="000560"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-[#0B2A5B] focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                >

                @error('receipt_number')
                    <p class="mt-1 text-xs font-semibold text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                <p class="mt-1 text-[10px] text-slate-400">
                    Next:
                    <span
                        id="next-receipt-number"
                        data-sequence="{{ (int) $this->nextReceiptNumber }}"
                        class="font-bold text-slate-600"
                    >
                        {{ $this->nextReceiptNumber }}
                    </span>
                    — backlog numbers below this are allowed if unused.
                </p>
            </div>

        </div>

    </div>
</section>


                    {{-- ================================================= --}}
                    {{-- PAYMENT BREAKDOWN --}}
                    {{-- ================================================= --}}
                    @if ($activation)
                        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="bg-[#0B2A5B] px-5 py-3">
                                <h3 class="text-sm font-bold uppercase tracking-wider text-[#E8B923]">
                                    Payment Breakdown
                                </h3>
                            </div>

                            <div class="p-5">

                                {{-- MEMBERSHIP FEE --}}
                                <div class="flex items-center justify-between gap-4 py-2">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-700">
                                            Membership Fee
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            Base activation fee
                                        </p>
                                    </div>

                                    <span class="text-sm font-bold text-slate-800">
                                        ₱{{ number_format($totalMembershipFeesCost, 2) }}
                                    </span>
                                </div>

                                {{-- GCASH CONVENIENCE FEE --}}
                                @if (strtolower(trim($activation->payment_method ?? '')) === 'gcash' && $gcashConvenienceFeeEnabled)
                                    <div class="flex items-center justify-between gap-4 border-t border-slate-100 py-2">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-700">
                                                GCash Convenience Fee
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                1% convenience fee
                                            </p>
                                        </div>

                                        <span class="text-sm font-bold text-slate-800">
                                            ₱{{ number_format($totalMembershipFeesCost * 0.01, 2) }}
                                        </span>
                                    </div>
                                @endif
                                @if (strtolower(trim($activation->payment_method ?? '')) === 'paymaya' && $paymayaConvenienceFeeEnabled)
                                    <div class="flex items-center justify-between gap-4 border-t border-slate-100 py-2">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-700">
                                                Paymaya Convenience Fee
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                1% convenience fee
                                            </p>
                                        </div>

                                        <span class="text-sm font-bold text-slate-800">
                                            ₱{{ number_format($totalMembershipFeesCost * 0.01, 2) }}
                                        </span>
                                    </div>
                                @endif

                                {{-- TOTAL --}}
                                <div
                                    class="mt-3 flex items-center justify-between gap-4 rounded-xl bg-slate-50 px-4 py-3">
                                    <div>
                                        <p class="text-sm font-extrabold text-[#0B2A5B]">
                                            Total Amount to Pay
                                        </p>

                                        @if (
                                            (strtolower(trim($activation->payment_method ?? '')) === 'gcash' && $gcashConvenienceFeeEnabled) ||
                                                (strtolower(trim($activation->payment_method ?? '')) === 'paymaya' && $paymayaConvenienceFeeEnabled))
                                            <p class="mt-0.5 text-[11px] text-slate-500">
                                                Includes 1% convenience fee
                                            </p>
                                        @endif
                                    </div>

                                    <span class="text-xl font-extrabold text-emerald-600">
                                        ₱{{ number_format(
                                            $totalMembershipFeesCost +
                                                ((strtolower(trim($activation->payment_method ?? '')) === 'gcash' && $gcashConvenienceFeeEnabled) ||
                                                (strtolower(trim($activation->payment_method ?? '')) === 'paymaya' && $paymayaConvenienceFeeEnabled)
                                                    ? number_format($totalMembershipFeesCost * 0.01, 2)
                                                    : 0),
                                            2,
                                        ) }}
                                    </span>
                                </div>

                                {{-- ADMIN CHECK NOTE --}}
                                @if (strtolower(trim($activation->payment_method ?? '')) === 'gcash')
                                    <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3">
                                        <p class="text-xs font-semibold text-blue-800">
                                            Payment Verification
                                        </p>

                                        <p class="mt-1 text-[11px] leading-relaxed text-blue-700">
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
                        <div class="bg-[#0B2A5B] px-5 py-3">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-[#E8B923]">
                                Referral Information
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2">
                            {{-- REFERRAL CODE --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                    Upline Code
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    {{ $activation?->upline_code ?? '-' }}
                                </p>
                            </div>

                            {{-- REFERRAL MEMBER --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                    Referral Member
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    {{ trim(($referralUser?->firstname ?? '') . ' ' . ($referralUser?->lastname ?? '')) ?:
                                        'No referral member found' }}
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- ================================================= --}}
                    {{-- BENEFICIARIES --}}
                    {{-- ================================================= --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between bg-[#0B2A5B] px-5 py-3">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-[#E8B923]">
                                Beneficiaries
                            </h3>

                            <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-bold text-white">
                                {{ count($beneficiaries ?? []) }}
                                {{ count($beneficiaries ?? []) === 1 ? 'Person' : 'People' }}
                            </span>
                        </div>

                        @if (!empty($beneficiaries) && count($beneficiaries) > 0)
                            <div class="overflow-x-auto">
                                {{-- w-full + table-fixed: the table ALWAYS spans the full
                                     section width, columns share it evenly --}}
                                <table class="w-full table-fixed divide-y divide-slate-200">
                                    <thead class="bg-slate-100">
                                        <tr>
                                            <th
                                                class="w-1/2 px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                                Full Name
                                            </th>
                                            <th
                                                class="w-1/4 px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                                Birthdate
                                            </th>
                                            <th
                                                class="w-1/4 px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                                Relationship
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 bg-white">
                                        @foreach ($beneficiaries as $beneficiary)
                                            <tr class="transition hover:bg-slate-50">
                                                <td class="break-words px-5 py-4 text-sm font-bold text-slate-800">
                                                    {{ is_array($beneficiary) ? $beneficiary['fullname'] ?? '-' : $beneficiary->fullname ?? '-' }}
                                                </td>
                                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                                    {{ is_array($beneficiary) ? $beneficiary['birthdate'] ?? '-' : $beneficiary->birthdate ?? '-' }}
                                                </td>
                                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                                    {{ is_array($beneficiary) ? $beneficiary['relationship'] ?? '-' : $beneficiary->relationship ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center px-5 py-10 text-center">
                                <div
                                    class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl">
                                    👥
                                </div>
                                <p class="mt-3 font-bold text-slate-700">
                                    No beneficiaries found
                                </p>
                                <p class="mt-1 text-sm text-slate-500">
                                    This member has no registered beneficiaries.
                                </p>
                            </div>
                        @endif
                    </section>

                </div>

                {{-- ================================================= --}}
                {{-- FOOTER --}}
                {{-- ================================================= --}}
                <div
                    class="flex shrink-0 flex-col-reverse justify-between gap-3 border-t border-slate-200 bg-white p-4 sm:flex-row sm:items-center">
                    <button wire:click="closeModal" type="button"
                        class="rounded-xl border border-slate-300 px-6 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100">
                        Close
                    </button>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        {{-- REJECT --}}
                        <button type="button" wire:loading.attr="disabled" wire:target="rejectActivation"
                            onclick="openActivationRejectAlert()"
                            class="rounded-xl bg-[#D79B10] hover:bg-[#B8830D] px-6 py-2.5 text-xs font-bold text-white transition disabled:cursor-not-allowed disabled:opacity-60 cursor-pointer">
                            <span wire:loading.remove wire:target="rejectActivation">
                                Reject
                            </span>
                            <span wire:loading wire:target="rejectActivation">
                                Rejecting...
                            </span>
                        </button>

                        {{-- APPROVE --}}
                     <button
    type="button"
    wire:loading.attr="disabled"
    wire:target="approve"
    x-on:click="openActivationApproveAlert()"
    class="rounded-xl bg-[#0B2A5B] px-6 py-2.5 text-xs font-bold text-white transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-60 cursor-pointer"
>
                            <span wire:loading.remove wire:target="approve">
                                Approve Activation
                            </span>
                            <span wire:loading wire:target="approve">
                                Approving...
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif


@script
<script>
    // ==========================================================
    // VALIDATE POLICY + RECEIPT BEFORE APPROVAL
    // ==========================================================

    window.validateActivationNumbersBeforeApprove = async function () {

        const policyInput = document.getElementById('policy-number-input');
        const receiptInput = document.getElementById('receipt-number-input');

        const policy = policyInput?.value?.trim().toUpperCase() ?? '';
        const receipt = receiptInput?.value?.trim() ?? '';

        const year = new Date().getFullYear();

        console.log('==========================================');
        console.log('Approve validation started');
        console.log('Policy:', policy);
        console.log('Receipt:', receipt);
        console.log('Current year:', year);
        console.log('==========================================');

        // ======================================================
        // POLICY NUMBER REQUIRED
        // ======================================================

        if (!policy) {

            await Swal.fire({
                icon: 'error',
                title: 'Policy Number Required',
                text: 'Please enter or generate a policy number.'
            });

            policyInput?.focus();

            return false;
        }

        // ======================================================
        // POLICY NUMBER FORMAT
        // Expected:
        // TRI-00001-TS2026-00001
        // ======================================================

        const policyMatch = policy.match(
            /^TRI-(\d+)-TS(\d{4})-(\d+)$/
        );

        if (!policyMatch) {

            await Swal.fire({
                icon: 'error',
                title: 'Invalid Policy Number',
                text: `The policy number must use the format TRI-00001-TS${year}-00001.`
            });

            policyInput?.focus();

            return false;
        }

        const firstSequence = parseInt(policyMatch[1], 10);
        const policyYear = parseInt(policyMatch[2], 10);
        const finalSequence = parseInt(policyMatch[3], 10);

        const nextPolicyElement =
            document.getElementById('next-policy-number');

        const nextPolicy = parseInt(
            nextPolicyElement?.dataset.sequence ?? '0',
            10
        );

        console.log('First policy sequence:', firstSequence);
        console.log('Policy year:', policyYear);
        console.log('Final policy sequence:', finalSequence);
        console.log('Next available policy sequence:', nextPolicy);

        // ======================================================
        // POLICY YEAR
        // ======================================================

        if (policyYear !== year) {

            await Swal.fire({
                icon: 'error',
                title: 'Invalid Policy Year',
                text: `The policy number must use the current year ${year}.`
            });

            policyInput?.focus();

            return false;
        }

        // ======================================================
        // POLICY SEQUENCES MUST MATCH
        // ======================================================

        if (firstSequence !== finalSequence) {

            await Swal.fire({
                icon: 'error',
                title: 'Invalid Policy Number',
                text: 'The first and last sequence numbers of the policy number must match.'
            });

            policyInput?.focus();

            return false;
        }

        // ======================================================
        // POLICY CANNOT JUMP AHEAD
        // ======================================================

        if (firstSequence > nextPolicy) {

            await Swal.fire({
                icon: 'error',
                title: 'Invalid Policy Number',
                text:
                    `The policy number cannot jump ahead. ` +
                    `The next available sequence is ` +
                    `${String(nextPolicy).padStart(5, '0')}.`
            });

            policyInput?.focus();

            return false;
        }

        // ======================================================
        // RECEIPT NUMBER REQUIRED
        // ======================================================

        if (!receipt) {

            await Swal.fire({
                icon: 'error',
                title: 'Receipt Number Required',
                text: 'Please enter or generate a receipt number.'
            });

            receiptInput?.focus();

            return false;
        }

        // ======================================================
        // RECEIPT FORMAT
        // Exactly 6 digits
        // ======================================================

        if (!/^\d{6}$/.test(receipt)) {

            await Swal.fire({
                icon: 'error',
                title: 'Invalid Receipt Number',
                text: 'The receipt number must contain exactly 6 digits.'
            });

            receiptInput?.focus();

            return false;
        }

        const receiptSequence = parseInt(receipt, 10);

        const nextReceiptElement =
            document.getElementById('next-receipt-number');

        const nextReceipt = parseInt(
            nextReceiptElement?.dataset.sequence ?? '0',
            10
        );

        console.log('Receipt sequence:', receiptSequence);
        console.log('Next available receipt sequence:', nextReceipt);

        // ======================================================
        // RECEIPT CANNOT JUMP AHEAD
        // ======================================================

        if (receiptSequence > nextReceipt) {

            await Swal.fire({
                icon: 'error',
                title: 'Invalid Receipt Number',
                text:
                    `The receipt number cannot jump ahead. ` +
                    `The next available receipt number is ` +
                    `${String(nextReceipt).padStart(6, '0')}.`
            });

            receiptInput?.focus();

            return false;
        }

        // ======================================================
        // CHECK DATABASE DUPLICATES
        // ======================================================

        console.log('Checking duplicate policy/receipt numbers...');

        try {

            await $wire.checkActivationNumberDuplicates();

        } catch (error) {

            console.error(
                'Duplicate validation failed:',
                error
            );

            await Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Unable to verify the policy and receipt numbers. Please try again.'
            });

            return false;
        }

        // ======================================================
        // DUPLICATE POLICY NUMBER
        // ======================================================

        if ($wire.policyNumberExists) {

            await Swal.fire({
                icon: 'error',
                title: 'Duplicate Policy Number',
                text:
                    'This policy number has already been used. ' +
                    'Please enter or generate another policy number.'
            });

            policyInput?.focus();

            return false;
        }

        // ======================================================
        // DUPLICATE RECEIPT NUMBER
        // ======================================================

        if ($wire.receiptNumberExists) {

            await Swal.fire({
                icon: 'error',
                title: 'Duplicate Receipt Number',
                text:
                    'This receipt number has already been issued. ' +
                    'Please enter or generate another receipt number.'
            });

            receiptInput?.focus();

            return false;
        }

        // ======================================================
        // ALL VALIDATIONS PASSED
        // ======================================================

        console.log('==========================================');
        console.log('Approve validation PASSED');
        console.log('==========================================');

        return true;
    };


    // ==========================================================
    // APPROVE CONFIRMATION
    // ==========================================================

    window.openActivationApproveAlert = async function () {

        console.log('Approve button clicked');

        // IMPORTANT:
        // Validation is async, so we MUST await it.
        const isValid =
            await window.validateActivationNumbersBeforeApprove();

        // Stop immediately if validation failed.
        if (!isValid) {

            console.log(
                'Approval blocked by validation'
            );

            return;
        }

        console.log(
            'Validation passed. Opening approval confirmation.'
        );

        // ======================================================
        // APPROVE CONFIRMATION
        // ======================================================

        const result = await Swal.fire({

            title: 'Approve Activation?',

            html: `
                <div style="text-align:center">
                    This will activate the member account
                    and send email notifications.

                    <br><br>

                    Type <strong>APPROVE</strong> to confirm.
                </div>
            `,

            icon: 'question',

            input: 'text',

            inputPlaceholder: 'Type APPROVE',

            inputAttributes: {
                autocomplete: 'off',
                autocapitalize: 'characters',
                spellcheck: 'false'
            },

            showCancelButton: true,

            confirmButtonText: 'Yes, APPROVE!',

            cancelButtonText: 'Cancel',

            confirmButtonColor: '#0B2A5B',

            allowOutsideClick: false,

            allowEscapeKey: true,

            preConfirm: (value) => {

                if (value !== 'APPROVE') {

                    Swal.showValidationMessage(
                        'Please type APPROVE exactly to continue.'
                    );

                    return false;
                }

                return true;
            }
        });

        // ======================================================
        // USER CANCELLED
        // ======================================================

        if (!result.isConfirmed) {

            console.log(
                'Approval confirmation cancelled.'
            );

            return;
        }

        // ======================================================
        // FINAL APPROVAL
        // ======================================================

        console.log(
            'APPROVE confirmed. Calling Livewire approve().'
        );

        await $wire.approve();
    };
</script>
@endscript


</div>
