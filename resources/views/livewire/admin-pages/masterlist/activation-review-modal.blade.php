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
                        <div class="bg-[#0B2A5B] px-5 py-3">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-[#E8B923]">
                                Activation Information
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 lg:grid-cols-3">
                            {{-- ACTIVATION ID --}}
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                    Activation ID
                                </p>
                                <p class="mt-1 font-bold text-slate-800">
                                    #{{ $activation?->ActivationID }}
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
                                    {{ $activation?->type === 'W' ? 'Walk-in' : ($activation?->type ? 'Online' : '-') }}
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
                            onclick="openActivationApproveAlert()"
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
        /* Uses the shared SwalFlow API from the shared sweet-alert-handler component */

        window.openActivationApproveAlert = function () {
            SwalFlow.confirm({
                title: 'Approve Activation?',
                html: 'This will activate the member account and send email notifications.<br><br>Type <strong>APPROVE</strong> to confirm.',
                icon: 'question',
                requireConfirmWord: 'APPROVE',
                confirmText: 'Yes, APPROVE!',
                loadingTitle: 'Approving activation...',
                loadingText: 'Sending emails and updating records.',
            }, () => $wire.approve());
        };

        window.openActivationRejectAlert = function () {
            SwalFlow.prompt({
                title: 'Reject Activation?',
                icon: 'warning',
                input: 'textarea',
                inputLabel: 'Rejection remarks',
                inputPlaceholder: 'Enter the reason for rejection...',
                maxLength: 1000,
                rows: 5,
                required: true,
                requiredMessage: 'Remarks are required.',
                confirmText: 'Reject',
                loadingTitle: 'Rejecting activation...',
                loadingText: 'Sending email and updating records.',
            }, (value) => {
                $wire.set('remarks', value);
                $wire.rejectActivation();
            });
        };
    </script>
    @endscript
</div>
