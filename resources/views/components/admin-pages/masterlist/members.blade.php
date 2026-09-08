<div class="members-container">

    {{-- ============================================================ --}}
    {{-- HEADER --}}
    {{-- ============================================================ --}}
    <div class="members-header">

        <div class="members-header-content">
            <h2 class="masterlist-title">
                Members
            </h2>

            <p class="masterlist-description">
                Member management content and directory.
            </p>
        </div>

        @if ($this->canCreate)
            <button
                type="button"
                onclick="Livewire.dispatch('open-application-modal')"
                class="masterlist-btn"
            >
                <svg class="masterlist-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>

                <span>Add Member</span>
            </button>
        @endif

    </div>


    {{-- ============================================================ --}}
    {{-- SEARCH / FILTERS / REFRESH --}}
    {{-- ============================================================ --}}
    <div class="members-toolbar">

        {{-- SEARCH --}}
        <div class="masterlist-search">

            <span class="masterlist-search-icon">
                <svg
                    class="masterlist-search-svg"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"
                    />
                </svg>
            </span>

            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by fullname or username..."
            >

        </div>


        {{-- FILTERS --}}
        <div class="members-toolbar-actions">

            {{-- DATE RANGE --}}
            <div wire:ignore class="date-range-wrapper">

                <span class="date-range-icon">
                    <svg
                        class="date-range-svg"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"
                        />
                    </svg>
                </span>

                <input
                    type="text"
                    id="memberDateRange"
                    readonly
                    class="date-range-input"
                    placeholder="Select date range"
                >

            </div>


            {{-- CLEAR DATE --}}
            @if ($dateRange)
                <button
                    type="button"
                    wire:click="clearDateFilter"
                    title="Clear date filter"
                    class="clear-date-btn"
                >
                    <span>&times;</span>
                    <span class="clear-date-text">Clear Date</span>
                </button>
            @endif


            {{-- REFRESH --}}
            <button
                type="button"
                wire:click="resetTable"
                wire:loading.attr="disabled"
                title="Refresh Table"
                class="refresh-btn"
            >

                <span
                    wire:loading.remove
                    wire:target="resetTable"
                    class="refresh-icon"
                >
                    <svg
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 4v5h5M20 20v-5h-5M5.5 9A7 7 0 0 1 18 6.5L20 9M18.5 15A7 7 0 0 1 6 17.5L4 15"
                        />
                    </svg>
                </span>

                <span
                    wire:loading
                    wire:target="resetTable"
                    class="refresh-loading"
                >
                    <svg
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                            stroke-width="3"
                            class="refresh-circle"
                        />
                        <path
                            d="M21 12a9 9 0 0 1-9 9"
                            stroke-width="3"
                            stroke-linecap="round"
                        />
                    </svg>
                </span>

                <span>Refresh</span>

            </button>

        </div>

    </div>


    {{-- ============================================================ --}}
    {{-- MEMBERS TABLE --}}
    {{-- ============================================================ --}}
    <div class="member-table">

        <div class="member-table-scroll">

            <table>

                <thead>
                    <tr>

                        <th>#</th>

                        <th>Name</th>

                        <th>Contact</th>

                        <th>Gender</th>

                        <th>Birthday</th>

                        <th>Status</th>

                        @if ($this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate)
                            <th class="member-actions-header">
                                Action
                            </th>
                        @endif

                    </tr>
                </thead>


                <tbody>

                    @forelse($members as $index => $member)

                        <tr wire:key="member-row-{{ $member->UserID }}">

                            {{-- NUMBER --}}
                            <td class="member-number">
                                {{ $members->firstItem() + $index }}
                            </td>


                            {{-- NAME --}}
                            <td class="member-name-cell">

                                <div class="member-name">
                                    {{ $member->firstname }}
                                    {{ $member->middlename }}
                                    {{ $member->lastname }}
                                </div>

                            </td>


                            {{-- CONTACT --}}
                            <td class="member-contact">
                                {{ $member->contactno ?: '-' }}
                            </td>


                            {{-- GENDER --}}
                            <td>

                                <span class="gender-badge">
                                    {{ $member->gender ?: '-' }}
                                </span>

                            </td>


                            {{-- BIRTHDAY --}}
                            <td class="member-birthday">
                                {{ $member->birthdate
                                    ? \Carbon\Carbon::parse($member->birthdate)->format('F d, Y')
                                    : '-'
                                }}
                            </td>


                            {{-- STATUS --}}
                            <td>

                                @if (strtolower($member->status) === 'active')

                                    <span class="status-badge status-active">
                                        Active
                                    </span>

                                @else

                                    <span class="status-badge status-inactive">
                                        Inactive
                                    </span>

                                @endif

                            </td>


                            {{-- ACTIONS --}}
                            @if ($this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate)

                                <td class="member-actions-cell">

                                    <div class="member-actions">

                                        {{-- VIEW --}}
                                        @if ($this->canRead)

                                            <button
                                                type="button"
                                                wire:click="viewMember({{ $member->UserID }})"
                                                title="View Member"
                                                class="member-action member-action-view"
                                            >
                                                <svg
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.8"
                                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5s8.577 3.01 9.964 7.183a1.012 1.012 0 0 1 0 .639C20.577 16.49 16.64 19.5 12 19.5s-8.577-3.01-9.964-7.178Z"
                                                    />
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.8"
                                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                                    />
                                                </svg>
                                            </button>

                                        @endif


                                        {{-- EDIT --}}
                                        @if ($this->canUpdate)

                                            <button
                                                type="button"
                                                wire:click="editMember({{ $member->UserID }})"
                                                title="Update Member"
                                                class="member-action member-action-edit"
                                            >
                                                <svg
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.8"
                                                        d="m16.862 3.487 3.65 3.65M4 20l3.75-.75L19.5 7.5a2.121 2.121 0 0 0-3-3L4.75 16.25 4 20Z"
                                                    />
                                                </svg>
                                            </button>

                                        @endif


                                        {{-- PASSWORD --}}
                                        @if ($this->canUpdate)

                                            <button
                                                type="button"
                                                wire:click="promptMemberPasswordReset({{ $member->UserID }})"
                                                title="Set New Password"
                                                class="member-action member-action-password"
                                            >
                                                <svg
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.8"
                                                        d="M15.75 5.25a3.75 3.75 0 1 1-6.694 2.32L3 13.625V17h3v2h3v-2h2v-3h1.875l2.625-2.625a3.75 3.75 0 0 1 .25-6.125ZM17.25 7.5h.008v.008h-.008V7.5Z"
                                                    />
                                                </svg>
                                            </button>

                                        @endif


                                        {{-- STATUS --}}
                                        @if ($this->canUpdate)

                                            @if (strtolower($member->status) === 'active')

                                                <button
                                                    type="button"
                                                    wire:click="confirmStatus({{ $member->UserID }})"
                                                    title="Deactivate Member"
                                                    class="member-action member-action-deactivate"
                                                >
                                                    Deactivate
                                                </button>

                                            @elseif($member->has_pending_activation)

                                                <button
                                                    type="button"
                                                    wire:click="openReview({{ $member->UserID }})"
                                                    title="Review Activation"
                                                    class="member-action member-action-pending"
                                                >
                                                    Pending
                                                </button>

                                            @else

                                                <button
                                                    type="button"
                                                    wire:click="openActivation({{ $member->UserID }})"
                                                    title="Activate Member"
                                                    class="member-action member-action-activate"
                                                >
                                                    Activate
                                                </button>

                                            @endif

                                        @endif


                                        {{-- WITHDRAWAL --}}
                                        @if ($member->has_pending_withdrawal)

                                            <button
                                                type="button"
                                                wire:click="openWithdrawalApproval({{ $member->UserID }})"
                                                title="Approve Withdrawal Request"
                                                class="member-action member-action-pending"
                                            >
                                                Pending
                                            </button>

                                        @elseif ($isWithdrawalActive)

                                            @if ($this->canCreate)

                                                <button
                                                    type="button"
                                                    wire:click="openWithdrawalModal({{ $member->UserID }})"
                                                    title="Create Withdrawal Request"
                                                    class="member-action member-action-withdraw"
                                                >
                                                    Withdraw
                                                </button>

                                            @endif

                                        @else

                                            @if ($this->canCreate)

                                                <span
                                                    title="Withdrawals are currently unavailable"
                                                    class="member-action member-action-off"
                                                >
                                                    <span class="off-icon">🔒</span>
                                                    <span>Off</span>
                                                </span>

                                            @endif

                                        @endif

                                    </div>

                                </td>

                            @endif

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="{{ $this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate ? 7 : 6 }}"
                                class="empty-members"
                            >
                                No members found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ========================================================= --}}
        {{-- WITHDRAWAL APPROVAL MODAL --}}
        {{-- ========================================================= --}}
        @if ($isWithdrawalModalOpen && $selectedWithdrawalUser && $selectedWithdrawal)

            <div class="member-modal-overlay">

                <div class="member-modal">

                    {{-- HEADER --}}
                    <div class="member-modal-header">

                        <h3 class="member-modal-title">
                            Approve &amp; Release Withdrawal
                        </h3>

                        <button
                            type="button"
                            wire:click="closeWithdrawalModal"
                            class="member-modal-close"
                        >
                            &times;
                        </button>

                    </div>


                    {{-- BODY --}}
                    <div class="member-modal-body">

                        {{-- SELECTED MEMBER --}}
                        <div class="withdrawal-member-card">

                            <span class="withdrawal-label">
                                Withdrawal Member
                            </span>

                            <p class="withdrawal-member-name">

                                {{ $selectedWithdrawalUser->firstname }}
                                {{ $selectedWithdrawalUser->middlename }}
                                {{ $selectedWithdrawalUser->lastname }}

                            </p>

                        </div>


                        {{-- WITHDRAWAL DETAILS --}}
                        <div class="withdrawal-details-card">

                            <div class="withdrawal-details-title">

                                <span>⏳</span>

                                <h4>
                                    Pending Withdrawal Request
                                </h4>

                            </div>


                            <div class="withdrawal-details">

                                <div class="withdrawal-row">
                                    <span>Requested Amount</span>

                                    <strong>
                                        ₱{{ number_format($selectedWithdrawal->requested_amount ?? 0, 2) }}
                                    </strong>
                                </div>


                                <div class="withdrawal-row">
                                    <span>Tax Amount</span>

                                    <strong>
                                        - ₱{{ number_format($selectedWithdrawal->tax_amount ?? 0, 2) }}
                                    </strong>
                                </div>


                                <div class="withdrawal-row">
                                    <span>Release Amount</span>

                                    <strong>
                                        - ₱{{ number_format($selectedWithdrawal->amount ?? 0, 2) }}
                                    </strong>
                                </div>


                                <div class="withdrawal-row">
                                    <span>Method</span>

                                    <strong>
                                        {{ $selectedWithdrawal->withdrawal_method ?? '-' }}
                                    </strong>
                                </div>


                                @if ($selectedWithdrawal->withdrawal_method === 'Bank Transfer' && $selectedWithdrawal->bank_name)

                                    <div class="withdrawal-row">
                                        <span>Bank Name</span>

                                        <strong>
                                            {{ $selectedWithdrawal->bank_name }}
                                        </strong>
                                    </div>

                                @endif


                                @if ($selectedWithdrawal->account_name)

                                    <div class="withdrawal-row">
                                        <span>Account Name</span>

                                        <strong>
                                            {{ $selectedWithdrawal->account_name }}
                                        </strong>
                                    </div>

                                @endif


                                @if ($selectedWithdrawal->account_number)

                                    <div class="withdrawal-row">
                                        <span>Account Number</span>

                                        <strong>
                                            {{ $selectedWithdrawal->account_number }}
                                        </strong>
                                    </div>

                                @endif


                                @if ($selectedWithdrawal->payment_reference)

                                    <div class="withdrawal-row">
                                        <span>Payment Reference</span>

                                        <strong>
                                            {{ $selectedWithdrawal->payment_reference }}
                                        </strong>
                                    </div>

                                @endif

                            </div>

                        </div>


                        {{-- WARNING --}}
                        <div class="withdrawal-warning">

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


                    {{-- FOOTER --}}
                    <div class="member-modal-footer">

                        <button
                            type="button"
                            wire:click="closeWithdrawalModal"
                            wire:loading.attr="disabled"
                            class="modal-btn modal-btn-cancel"
                        >
                            Cancel
                        </button>


                        <button
                            type="button"
                            wire:click="confirmWithdrawalRejection"
                            wire:loading.attr="disabled"
                            class="modal-btn modal-btn-reject"
                        >
                            <span wire:loading.remove wire:target="confirmWithdrawalRejection">
                                Reject
                            </span>

                            <span wire:loading wire:target="confirmWithdrawalRejection">
                                Processing...
                            </span>
                        </button>


                        <button
                            type="button"
                            wire:click="processWithdrawalApproval"
                            wire:loading.attr="disabled"
                            class="modal-btn modal-btn-approve"
                        >
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

            <div class="member-pagination">

                @if ($members->onFirstPage())

                    <span class="pagination-disabled">
                        Previous
                    </span>

                @else

                    <button
                        wire:click="previousPage"
                        wire:loading.attr="disabled"
                        class="pagination-btn pagination-prev"
                    >
                        Previous
                    </button>

                @endif


                <span class="pagination-info">
                    Page {{ $members->currentPage() }}
                    of
                    {{ $members->lastPage() }}
                </span>


                @if ($members->hasMorePages())

                    <button
                        wire:click="nextPage"
                        wire:loading.attr="disabled"
                        class="pagination-btn pagination-next"
                    >
                        Next
                    </button>

                @else

                    <span class="pagination-disabled">
                        Next
                    </span>

                @endif

            </div>

        @endif

    </div>


    {{-- ============================================================ --}}
    {{-- LIVEWIRE COMPONENTS --}}
    {{-- ============================================================ --}}
    <livewire:member-pages.my-profile.activation-modal />
    <livewire:components.withdrawal-modal />


    {{-- ============================================================ --}}
    {{-- SWEETALERT --}}
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

                const actionText =
                    data.action === 'deactivate'
                        ? "I'm sure to deactivate"
                        : "I'm sure to activate";


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

                            Swal.showValidationMessage(
                                'Please type: ' + actionText
                            );

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


            $wire.on('set-member-password', (data) => {

                const name =
                    data?.name || 'this member';

                const policy =
                    data?.policy || {
                        min: 8,
                        mixedCase: false,
                        letters: false,
                        numbers: false,
                        symbols: false
                    };

                const hint =
                    data?.hint ||
                    'Must contain at least ' +
                    policy.min +
                    ' characters.';


                Swal.fire({

                    title: 'Set New Password',

                    width: 460,

                    padding: '1.25rem 1.5rem 1.5rem',

                    html:

                        '<div style="text-align: left;">' +

                            '<p style="margin: 0 0 14px; font-size: 13px; line-height: 1.5; color: #475569;">' +

                                'Setting a new password for ' +

                                '<b style="color: #0B2A5B;">' +

                                    name +

                                '</b>.' +

                            '</p>' +


                            '<div style="margin-bottom: 12px;">' +

                                '<label for="swal-new-password" style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; color: #334155;">' +

                                    'New Password' +

                                '</label>' +

                                '<div class="relative">' +

                                    '<input id="swal-new-password" type="password" autocomplete="new-password" ' +

                                        'placeholder="••••••••" ' +

                                        'style="width: 100%; box-sizing: border-box; margin: 0;" ' +

                                        'class="w-full border border-slate-300 rounded-xl px-4 py-3 pr-10 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:border-[#0B2A5B] focus:outline-none transition">' +

                                    '<button type="button" class="swal-pw-toggle absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer" ' +

                                        'data-target="swal-new-password" tabindex="-1" aria-label="Show or hide password">' +

                                        '<svg data-ico="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">' +

                                            '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />' +

                                            '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />' +

                                        '</svg>' +

                                        '<svg data-ico="hide" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">' +

                                            '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />' +

                                        '</svg>' +

                                    '</button>' +

                                '</div>' +

                            '</div>' +


                            '<div style="margin-bottom: 14px;">' +

                                '<label for="swal-confirm-password" style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; color: #334155;">' +

                                    'Confirm New Password' +

                                '</label>' +

                                '<div class="relative">' +

                                    '<input id="swal-confirm-password" type="password" autocomplete="new-password" ' +

                                        'placeholder="••••••••" ' +

                                        'style="width: 100%; box-sizing: border-box; margin: 0;" ' +

                                        'class="w-full border border-slate-300 rounded-xl px-4 py-3 pr-10 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:border-[#0B2A5B] focus:outline-none transition">' +

                                    '<button type="button" class="swal-pw-toggle absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer" ' +

                                        'data-target="swal-confirm-password" tabindex="-1" aria-label="Show or hide password">' +

                                        '<svg data-ico="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">' +

                                            '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />' +

                                            '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />' +

                                        '</svg>' +

                                        '<svg data-ico="hide" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">' +

                                            '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />' +

                                        '</svg>' +

                                    '</button>' +

                                '</div>' +

                            '</div>' +


                            '<p style="margin: 0; font-size: 11px; line-height: 1.5; color: #64748b;">' +

                                hint +

                            '</p>' +

                        '</div>',


                    showCancelButton: true,

                    confirmButtonText: 'Update Password',

                    confirmButtonColor: '#0C2C6B',

                    cancelButtonColor: '#D79B10',

                    focusConfirm: false,

                    allowOutsideClick: false,


                    didOpen: (modal) => {

                        modal
                            .querySelectorAll('.swal-pw-toggle')
                            .forEach((btn) => {

                                btn.addEventListener('click', () => {

                                    const input =
                                        modal.querySelector(
                                            '#' +
                                            btn.getAttribute(
                                                'data-target'
                                            )
                                        );

                                    const show =
                                        input.type === 'password';

                                    input.type =
                                        show
                                            ? 'text'
                                            : 'password';


                                    const showIcon =
                                        btn.querySelector(
                                            '[data-ico="show"]'
                                        );

                                    const hideIcon =
                                        btn.querySelector(
                                            '[data-ico="hide"]'
                                        );


                                    if (showIcon) {
                                        showIcon.style.display =
                                            show
                                                ? 'none'
                                                : '';
                                    }


                                    if (hideIcon) {
                                        hideIcon.style.display =
                                            show
                                                ? ''
                                                : 'none';
                                    }

                                });

                            });

                    },


                    preConfirm: () => {

                        const password =
                            document.getElementById(
                                'swal-new-password'
                            ).value;

                        const confirmation =
                            document.getElementById(
                                'swal-confirm-password'
                            ).value;


                        const messages = [];


                        if (!password) {

                            messages.push(
                                'Please enter a new password.'
                            );

                        } else {

                            if (password.length < policy.min) {

                                messages.push(
                                    'Password must be at least ' +
                                    policy.min +
                                    ' characters long.'
                                );

                            }


                            if (
                                policy.mixedCase &&
                                !(
                                    /[a-z]/.test(password) &&
                                    /[A-Z]/.test(password)
                                )
                            ) {

                                messages.push(
                                    'Include both uppercase and lowercase letters.'
                                );

                            } else if (
                                policy.letters &&
                                !/[a-zA-Z]/.test(password)
                            ) {

                                messages.push(
                                    'Include at least one letter.'
                                );

                            }


                            if (
                                policy.numbers &&
                                !/\d/.test(password)
                            ) {

                                messages.push(
                                    'Include at least one number.'
                                );

                            }


                            if (
                                policy.symbols &&
                                !/[^A-Za-z0-9]/.test(password)
                            ) {

                                messages.push(
                                    'Include at least one symbol.'
                                );

                            }

                        }


                        if (confirmation !== password) {

                            messages.push(
                                'Passwords do not match.'
                            );

                        }


                        if (messages.length > 0) {

                            Swal.showValidationMessage(
                                messages.join(' ')
                            );

                            return false;

                        }


                        return password;

                    }

                }).then((result) => {

                    if (result.isConfirmed && result.value) {

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


                        $wire.resetMemberPassword(
                            data.id,
                            result.value
                        );

                    }

                });

            });

        </script>

    @endscript


    @script

        <script>

            // =====================================================
            // DATE RANGE PICKER
            // =====================================================

            const dateInput =
                $wire.$el.querySelector(
                    '#memberDateRange'
                );


            const picker = flatpickr(
                dateInput,
                {

                    mode: 'range',

                    dateFormat: 'Y-m-d',

                    altInput: true,

                    altFormat: 'M d, Y',

                    maxDate: 'today',

                    defaultDate:
                        @js(
                            $dateRange
                                ? explode(' to ', $dateRange)
                                : []
                        ),


                    onChange:
                        (
                            selectedDates,
                            dateStr,
                            instance
                        ) => {

                            if (selectedDates.length === 2) {

                                const start =
                                    instance.formatDate(
                                        selectedDates[0],
                                        'Y-m-d'
                                    );

                                const end =
                                    instance.formatDate(
                                        selectedDates[1],
                                        'Y-m-d'
                                    );


                                $wire.set(
                                    'dateRange',
                                    `${start} to ${end}`
                                );

                            } else if (
                                selectedDates.length === 0
                            ) {

                                $wire.set(
                                    'dateRange',
                                    ''
                                );

                            }

                        },


                    onClose:
                        (
                            selectedDates,
                            dateStr,
                            instance
                        ) => {

                            if (selectedDates.length === 1) {

                                const day =
                                    instance.formatDate(
                                        selectedDates[0],
                                        'Y-m-d'
                                    );


                                instance.setDate(
                                    [
                                        selectedDates[0],
                                        selectedDates[0]
                                    ],
                                    true
                                );


                                $wire.set(
                                    'dateRange',
                                    `${day} to ${day}`
                                );

                            }

                        }

                }
            );


            // =====================================================
            // RESET DATE RANGE
            // =====================================================

            $wire.on(
                'ranges-reset',
                () => {

                    picker.clear();

                }
            );


            // =====================================================
            // REJECT WITHDRAWAL
            // =====================================================

            $wire.on(
                'confirm-withdrawal-rejection',
                () => {

                    Swal.fire({

                        title: 'Reject Withdrawal?',

                        text:
                            'This withdrawal request will be rejected. This action cannot be undone.',

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

                }
            );

        </script>

    @endscript

</div>
