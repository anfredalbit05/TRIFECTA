<div>
    @if($open)
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

                    <button
                        wire:click="closeModal"
                        type="button"
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-lg font-bold text-slate-500 transition hover:bg-red-100 hover:text-red-600"
                        title="Close"
                    >
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
                                    {{
                                        trim(
                                            ($member?->firstname ?? '')
                                            .' '
                                            .($member?->middlename ?? '')
                                            .' '
                                            .($member?->lastname ?? '')
                                        ) ?: '-'
                                    }}
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
                                    {{ $member?->birthdate ?? '-' }}
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
                                    {{
                                        $activation?->type === 'W'
                                            ? 'Walk-in'
                                            : ($activation?->type ? 'Online' : '-')
                                    }}
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
                                    <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">
                                        {{ $activation?->status ?? 'Pending' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>

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
                                    {{
                                        trim(
                                            ($referralUser?->firstname ?? '')
                                            .' '
                                            .($referralUser?->lastname ?? '')
                                        ) ?: 'No referral member found'
                                    }}
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

                        @if(!empty($beneficiaries) && count($beneficiaries) > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-100">
                                        <tr>
                                            <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                                Full Name
                                            </th>
                                            <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                                Birthdate
                                            </th>
                                            <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                                Relationship
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 bg-white">
                                        @foreach($beneficiaries as $beneficiary)
                                            <tr class="transition hover:bg-slate-50">
                                                <td class="whitespace-nowrap px-5 py-4 text-sm font-bold text-slate-800">
                                                    {{
                                                        is_array($beneficiary)
                                                            ? ($beneficiary['fullname'] ?? '-')
                                                            : ($beneficiary->fullname ?? '-')
                                                    }}
                                                </td>
                                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                                    {{
                                                        is_array($beneficiary)
                                                            ? ($beneficiary['birthdate'] ?? '-')
                                                            : ($beneficiary->birthdate ?? '-')
                                                    }}
                                                </td>
                                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                                    {{
                                                        is_array($beneficiary)
                                                            ? ($beneficiary['relationship'] ?? '-')
                                                            : ($beneficiary->relationship ?? '-')
                                                    }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center px-5 py-10 text-center">
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl">
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
                <div class="flex shrink-0 flex-col-reverse justify-between gap-3 border-t border-slate-200 bg-white p-4 sm:flex-row sm:items-center">
                    <button
                        wire:click="closeModal"
                        type="button"
                        class="rounded-xl border border-slate-300 px-6 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100"
                    >
                        Close
                    </button>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        {{-- REJECT --}}
                        <button
                            type="button"
                            wire:loading.attr="disabled"
                            wire:target="reject"
                            onclick="confirmReject()"
                            class="rounded-xl bg-red-500 px-6 py-2.5 text-xs font-bold text-white transition hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="reject">
                                Reject
                            </span>
                            <span wire:loading wire:target="reject">
                                Rejecting...
                            </span>
                        </button>

                        {{-- APPROVE --}}
                        <button
                            type="button"
                            wire:loading.attr="disabled"
                            wire:target="approve"
                            onclick="confirmApprove()"
                            class="rounded-xl bg-[#0B2A5B] px-6 py-2.5 text-xs font-bold text-white transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-60"
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
        window.confirmApprove = function () {
            Swal.fire({
                title: 'Approve Activation?',
                text: 'This member will be activated and the activation request will be approved.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#0B2A5B',
                cancelButtonColor: '#64748b',
                reverseButtons: true,
                didOpen: () => {
                    const swalContainer = document.querySelector('.swal2-container');
                    if (swalContainer) {
                        swalContainer.classList.add('!z-[10000]');
                    }
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.approve();
                }
            });
        };

        window.confirmReject = function () {
            Swal.fire({
                title: 'Reject Activation?',
                text: 'The activation request will be rejected. This action may need a new activation request.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Reject',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                reverseButtons: true,
                didOpen: () => {
                    const swalContainer = document.querySelector('.swal2-container');
                    if (swalContainer) {
                        swalContainer.classList.add('!z-[10000]');
                    }
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.reject();
                }
            });
        };
    </script>
    @endscript
</div>
