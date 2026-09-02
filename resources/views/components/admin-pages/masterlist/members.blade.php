<div class="members-container w-full px-4 sm:px-6 lg:px-8 py-6">

    {{-- ============================================================ --}}
    {{-- HEADER --}}
    {{-- ============================================================ --}}
    <div class="members-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="masterlist-title text-2xl font-bold text-slate-800">Members</h2>
            <p class="masterlist-description text-sm text-slate-500 mt-0.5">Member management content and directory.</p>
        </div>

        @if ($this->canCreate)
            <button type="button" onclick="Livewire.dispatch('open-application-modal')"
                class="masterlist-btn inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition cursor-pointer shadow bg-[#0C2C6B] text-white hover:bg-[#09204f] w-full sm:w-auto">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Member
            </button>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- SEARCH INPUT, FILTERS & REFRESH BUTTON CONTAINER --}}
    {{-- ============================================================ --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">

        {{-- SEARCH INPUT --}}
        <div class="relative max-w-sm w-full sm:w-auto flex-1">
            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                🔍
            </span>
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Search by fullname or username..."
                class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
        </div>

        {{-- FILTERS AND REFRESH GROUP --}}
        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">

            {{-- DATE RANGE FILTER --}}
            <div wire:ignore class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none z-10">
                    📅
                </span>
                <input type="text" id="memberDateRange" readonly
                    class="date-range-input pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white text-slate-700 shadow-sm cursor-pointer w-72 text-center font-medium"
                    placeholder="Select date range">
            </div>

            @if ($dateRange)
                <button type="button" wire:click="clearDateFilter" title="Clear date filter"
                    class="inline-flex items-center px-3 py-2 text-sm text-slate-500 hover:text-red-500 hover:border-red-200 hover:bg-red-50 rounded-xl border border-slate-200 bg-white shadow-sm cursor-pointer transition">
                    ✕
                </button>
            @endif

            {{-- REFRESH BUTTON --}}
            <button wire:click="resetTable" wire:loading.attr="disabled" title="Refresh Table"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl shadow-sm transition cursor-pointer">
                <span wire:loading.remove wire:target="resetTable">🔄</span>
                <span wire:loading wire:target="resetTable" class="animate-spin">⏳</span>
                <span class="hidden sm:inline">Refresh</span>
            </button>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MEMBERS TABLE --}}
    {{-- ============================================================ --}}
    <div class="member-table overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-200 bg-[#0C2C6B] text-xs font-semibold uppercase tracking-wider text-white">
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Contact</th>
                    <th class="py-3 px-4">Gender</th>
                    <th class="py-3 px-4">Birthday</th>
                    <th class="py-3 px-4">Status</th>
                    @if ($this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate)
                        <th class="py-3 px-4 text-center">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                @forelse($members as $index => $member)
                    <tr wire:key="member-row-{{ $member->UserID }}" class="hover:bg-slate-50 transition-colors">

                        <td class="py-4 px-4 font-medium text-slate-500">
                            {{ $members->firstItem() + $index }}
                        </td>

                        <td class="py-4 px-4 font-semibold text-slate-900">
                            {{ $member->firstname }}
                            {{ $member->middlename }}
                            {{ $member->lastname }}
                        </td>

                        <td class="py-4 px-4 text-slate-500">
                            {{ $member->contactno }}
                        </td>

                        <td class="py-4 px-4">
                            <span class="inline-flex justify-center items-center px-3 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-semibold">
                                {{ $member->gender }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-slate-500">
                            {{ $member->birthdate ? \Carbon\Carbon::parse($member->birthdate)->format('F d, Y') : '-' }}
                        </td>

                        <td class="py-4 px-4">
                            @if (strtolower($member->status) === 'active')
                                <span class="inline-flex justify-center items-center w-24 px-3 py-1 rounded-md bg-[#0C2C6B] text-white text-xs font-semibold shadow-sm">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex justify-center items-center w-24 px-3 py-1 rounded-md bg-slate-600 text-white text-xs font-semibold shadow-sm">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        {{-- ACTIONS COLUMN --}}
                        @if ($this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate)
                            <td class="py-4 px-4 text-center">
                                <div class="flex gap-2 justify-center items-center">

                                    {{-- VIEW --}}
                                    @if ($this->canRead)
                                        <button wire:click="viewMember({{ $member->UserID }})" title="View Member"
                                            class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-slate-600 text-white hover:bg-slate-700 transition cursor-pointer text-xs font-semibold shadow">
                                            👁️
                                        </button>
                                    @endif

                                    {{-- EDIT / UPDATE --}}
                                    @if ($this->canUpdate)
                                        <button wire:click="editMember({{ $member->UserID }})" title="Update Member"
                                            class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-[#0C2C6B] text-white hover:bg-[#09204f] transition cursor-pointer text-xs font-semibold shadow">
                                            ✏️
                                        </button>
                                    @endif

                                    {{-- STATUS / ACTIVATION ACTIONS (Requires Update Permission) --}}
                                    @if ($this->canUpdate)
                                        @if (strtolower($member->status) === 'active')
                                            <button wire:click="confirmStatus({{ $member->UserID }})"
                                                title="Deactivate Member"
                                                class="inline-flex items-center justify-center w-24 px-3 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition cursor-pointer text-xs font-semibold shadow">
                                                Deactivate
                                            </button>
                                        @elseif($member->has_pending_activation)
                                            <button wire:click="openReview({{ $member->UserID }})"
                                                title="Review Activation"
                                                class="inline-flex items-center justify-center w-24 px-3 py-2 rounded-lg bg-amber-500 text-white hover:bg-amber-600 transition cursor-pointer text-xs font-semibold shadow">
                                                Pending
                                            </button>
                                        @else
                                            <button wire:click="openActivation({{ $member->UserID }})"
                                                title="Activate Member"
                                                class="inline-flex items-center justify-center w-24 px-3 py-2 rounded-lg bg-[#0C2C6B] text-white hover:bg-[#09204f] transition cursor-pointer text-xs font-semibold shadow">
                                                Activate
                                            </button>
                                        @endif
                                    @endif

                                    {{-- WITHDRAWAL ACTION --}}
                                    @if ($member->has_pending_withdrawal)
                                        <button wire:click="openWithdrawalApproval({{ $member->UserID }})"
                                            title="Approve Withdrawal Request"
                                            class="inline-flex items-center justify-center w-24 px-3 py-2 rounded-lg bg-amber-500 text-white hover:bg-amber-600 transition cursor-pointer text-xs font-semibold shadow">
                                            Pending
                                        </button>
                                    @elseif ($isWithdrawalActive)
                                        @if ($this->canCreate)
                                            <button wire:click="openWithdrawalModal({{ $member->UserID }})"
                                                title="Create Withdrawal Request"
                                                class="inline-flex items-center justify-center w-24 px-3 py-2 rounded-lg bg-[#0C2C6B] text-white hover:bg-[#09204f] transition cursor-pointer text-xs font-semibold shadow">
                                                Withdraw
                                            </button>
                                        @endif
                                    @else
                                        @if ($this->canCreate)
                                            <span title="Withdrawals are currently unavailable"
                                                class="inline-flex items-center justify-center w-24 px-3 py-2 rounded-lg bg-gray-100 border border-gray-200 text-gray-400 text-xs font-semibold cursor-not-allowed">
                                                🔒 Off
                                            </span>
                                        @endif
                                    @endif

                                </div>
                            </td>
                        @endif

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-slate-500">
                            No members found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ============================================================ --}}
        {{-- WITHDRAWAL APPROVAL MODAL --}}
        {{-- ============================================================ --}}
        @if ($isWithdrawalModalOpen && $selectedWithdrawalUser && $selectedWithdrawal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden relative border border-slate-100">

                    {{-- Header --}}
                    <div class="bg-[#0C2C6B] px-6 py-4 flex items-center justify-between text-white">
                        <h3 class="text-base font-bold uppercase tracking-wider">
                            Approve &amp; Release Withdrawal
                        </h3>

                        <button type="button" wire:click="closeWithdrawalModal"
                            class="cursor-pointer text-white/80 hover:text-white text-xl font-bold transition">
                            &times;
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">

                        {{-- Selected Member --}}
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5">
                            <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                                Withdrawal Member
                            </span>

                            <p class="mt-1 font-bold text-slate-800">
                                {{ $selectedWithdrawalUser->firstname }}
                                {{ $selectedWithdrawalUser->middlename }}
                                {{ $selectedWithdrawalUser->lastname }}
                            </p>
                        </div>

                        {{-- Pending Withdrawal Details --}}
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-xl">⏳</span>
                                <h4 class="font-bold text-amber-800">
                                    Pending Withdrawal Request
                                </h4>
                            </div>

                            <div class="space-y-2 text-sm">

                                <div class="flex justify-between gap-4">
                                    <span class="text-gray-500">Requested Amount</span>
                                    <span class="font-bold text-slate-800 text-right">
                                        ₱{{ number_format($selectedWithdrawal->requested_amount ?? 0, 2) }}
                                    </span>
                                </div>

                                <div class="flex justify-between gap-4">
                                    <span class="text-gray-500">Tax Amount</span>
                                    <span class="font-bold text-slate-800 text-right">
                                       - ₱{{ number_format($selectedWithdrawal->tax_amount ?? 0, 2) }}
                                    </span>
                                </div>

                                <div class="flex justify-between gap-4">
                                    <span class="text-gray-500">Release Amount</span>
                                    <span class="font-bold text-slate-800 text-right">
                                       - ₱{{ number_format($selectedWithdrawal->amount ?? 0, 2) }}
                                    </span>
                                </div>

                                <div class="flex justify-between gap-4">
                                    <span class="text-gray-500">Method</span>
                                    <span class="font-semibold text-slate-800 text-right">
                                        {{ $selectedWithdrawal->withdrawal_method ?? '-' }}
                                    </span>
                                </div>

                                @if ($selectedWithdrawal->withdrawal_method === 'Bank Transfer' && $selectedWithdrawal->bank_name)
                                    <div class="flex justify-between gap-4">
                                        <span class="text-gray-500">Bank Name</span>
                                        <span class="font-semibold text-slate-800 text-right">
                                            {{ $selectedWithdrawal->bank_name }}
                                        </span>
                                    </div>
                                @endif

                                @if ($selectedWithdrawal->account_name)
                                    <div class="flex justify-between gap-4">
                                        <span class="text-gray-500">Account Name</span>
                                        <span class="font-semibold text-slate-800 text-right">
                                            {{ $selectedWithdrawal->account_name }}
                                        </span>
                                    </div>
                                @endif

                                @if ($selectedWithdrawal->account_number)
                                    <div class="flex justify-between gap-4">
                                        <span class="text-gray-500">Account Number</span>
                                        <span class="font-semibold text-slate-800 text-right">
                                            {{ $selectedWithdrawal->account_number }}
                                        </span>
                                    </div>
                                @endif

                                @if ($selectedWithdrawal->payment_reference)
                                    <div class="flex justify-between gap-4">
                                        <span class="text-gray-500">Payment Reference</span>
                                        <span class="font-semibold text-slate-800 text-right">
                                            {{ $selectedWithdrawal->payment_reference }}
                                        </span>
                                    </div>
                                @endif

                            </div>
                        </div>

                        {{-- Confirmation Warning --}}
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-900">
                            <div class="flex items-start gap-2">
                                <span>ℹ️</span>
                                <p>
                                    Approving this request will mark the withdrawal as
                                    <strong>Approved</strong> and deduct
                                    <strong>
                                        ₱{{ number_format($selectedWithdrawal->requested_amount ?? 0, 2) }}
                                    </strong>
                                    from the member's wallet.
                                </p>
                            </div>
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-2">

                        {{-- Cancel --}}
                        <button type="button" wire:click="closeWithdrawalModal" wire:loading.attr="disabled"
                            class="cursor-pointer px-4 py-2 bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-300 transition">
                            Cancel
                        </button>

                        {{-- Reject --}}
                        <button type="button" wire:click="confirmWithdrawalRejection" wire:loading.attr="disabled"
                            class="cursor-pointer px-4 py-2 bg-[#D79B10] text-white rounded-xl text-sm font-semibold hover:bg-[#c58d0f] transition shadow-sm">
                            <span wire:loading.remove wire:target="confirmWithdrawalRejection">
                                Reject
                            </span>
                            <span wire:loading wire:target="confirmWithdrawalRejection">
                                Processing...
                            </span>
                        </button>

                        {{-- Approve --}}
                        <button type="button" wire:click="processWithdrawalApproval" wire:loading.attr="disabled"
                            class="cursor-pointer px-4 py-2 bg-[#0C2C6B] text-white rounded-xl text-sm font-semibold hover:bg-[#09204f] transition shadow-sm">
                            <span wire:loading.remove wire:target="processWithdrawalApproval">
                                Approve &amp; Release
                            </span>
                            <span wire:loading wire:target="processWithdrawalApproval">
                                Processing...
                            </span>
                        </button>

                    </div>
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- PAGINATION --}}
        {{-- ============================================================ --}}
        @if ($members->hasPages())
            <div class="px-4 py-4 border-t border-slate-200 bg-slate-50 flex justify-center items-center space-x-4">

                {{-- Previous Page Link --}}
                @if ($members->onFirstPage())
                    <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed">
                        Previous
                    </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded transition cursor-pointer"
                        style="background-color: #D79B10;" onmouseover="this.style.backgroundColor='#c58d0f'"
                        onmouseout="this.style.backgroundColor='#D79B10'">
                        Previous
                    </button>
                @endif

                <span class="text-sm text-gray-600">
                    Page {{ $members->currentPage() }} of {{ $members->lastPage() }}
                </span>

                {{-- Next Page Link --}}
                @if ($members->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded transition cursor-pointer"
                        style="background-color: #0C2C6B;" onmouseover="this.style.backgroundColor='#0a2559'"
                        onmouseout="this.style.backgroundColor='#0C2C6B'">
                        Next
                    </button>
                @else
                    <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed">
                        Next
                    </span>
                @endif

            </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- LIVEWIRE MODAL COMPONENTS --}}
    {{-- ============================================================ --}}
    <livewire:member-pages.my-profile.activation-modal />
    <livewire:components.withdrawal-modal />

    {{-- ============================================================ --}}
    {{-- SWEETALERT SCRIPTS --}}
    {{-- ============================================================ --}}
    @script
        <script>
            $wire.on('swal:error', (data) => {
                Swal.fire({
                    icon: 'error',
                    title: data.title ?? 'Error',
                    text: data.text ?? 'Something went wrong.',
                    confirmButtonColor: '#0C2C6B',
                });
            });

            $wire.on('confirm-status', (data) => {
                const actionText = data.action === 'deactivate' ?
                    "I'm sure to deactivate" :
                    "I'm sure to activate";

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Type the confirmation text below to continue.',
                    icon: 'warning',
                    input: 'text',
                    inputPlaceholder: actionText,
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#0C2C6B',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Confirm ' + data.action,
                    cancelButtonText: 'Cancel',
                    preConfirm: (value) => {
                        if (value !== actionText) {
                            Swal.showValidationMessage('Please type: ' + actionText);
                            return false;
                        }
                        return true;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Updating...',
                            text: 'Please wait.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $wire.updateStatus(data.id);
                    }
                });
            });

            $wire.on('status-updated', () => {
                Swal.fire({
                    title: 'Updated!',
                    text: 'Member status has been updated.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        </script>
    @endscript

    @script
        <script>
            // =====================================================
            // DATE RANGE PICKER
            // =====================================================
            const dateInput = $wire.$el.querySelector('#memberDateRange');

            const picker = flatpickr(dateInput, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'M d, Y',
                maxDate: 'today',
                defaultDate: @js($dateRange ? explode(' to ', $dateRange) : []),

                onChange: (selectedDates, dateStr, instance) => {
                    // Full range picked (2 different days) — send immediately
                    if (selectedDates.length === 2) {
                        const start = instance.formatDate(selectedDates[0], 'Y-m-d');
                        const end = instance.formatDate(selectedDates[1], 'Y-m-d');

                        $wire.set('dateRange', `${start} to ${end}`);
                    } else if (selectedDates.length === 0) {
                        $wire.set('dateRange', '');
                    }
                },

                onClose: (selectedDates, dateStr, instance) => {
                    // User picked exactly ONE day and closed the calendar
                    if (selectedDates.length === 1) {
                        const day = instance.formatDate(selectedDates[0], 'Y-m-d');

                        // Force the picker to redraw as a "day to day" range
                        instance.setDate(
                            [selectedDates[0], selectedDates[0]],
                            true
                        );

                        $wire.set('dateRange', `${day} to ${day}`);
                    }
                }
            });

            // =====================================================
            // RESET DATE RANGE
            // =====================================================
            $wire.on('ranges-reset', () => {
                picker.clear();
            });

            // =====================================================
            // REJECT WITHDRAWAL CONFIRMATION
            // =====================================================
            $wire.on('confirm-withdrawal-rejection', () => {
                Swal.fire({
                    title: 'Reject Withdrawal?',
                    text: 'This withdrawal request will be rejected. This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,

                    confirmButtonColor: '#0C2C6B',
                    cancelButtonColor: '#D79B10',

                    confirmButtonText: 'Yes, Reject',
                    cancelButtonText: 'Cancel',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Rejecting...',
                            text: 'Please wait.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,

                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $wire.rejectWithdrawal();
                    }
                });
            });
        </script>
    @endscript

</div>
