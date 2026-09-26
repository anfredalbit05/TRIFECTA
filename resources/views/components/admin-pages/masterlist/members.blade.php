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

            {{-- STATUS FILTER --}}
            <select
                wire:model.live="statusFilter"
                title="Filter by status"
                class="text-xs border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-slate-50 font-semibold text-slate-700 shadow-sm cursor-pointer"
            >
                <option value="all">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Suspended">Suspended</option>
            </select>

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



                                @if (strtolower($member->status) === 'suspended')

                                    <span class="status-badge status-suspended" title="Account suspended by an administrator">
                                        Suspended
                                    </span>

                                @elseif (strtolower($member->status) === 'active')

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

                                        {{-- SATELLITE --}}
                                        @if ($isSatelliteApplicationActive)
                                        {{-- SATELLITE --}}
                                        <button
                                            type="button"
                                            wire:click="openSatellite({{ $member->UserID }})"
                                            title="{{ $isSatelliteApplicationActive && strtolower(trim((string) $member->status)) === 'active'
                                                ? 'Manage Satellite Subscription'
                                                : 'Satellite Subscription Unavailable' }}"
                                            class="member-action member-action-satellite
                                                {{ !($isSatelliteApplicationActive && strtolower(trim((string) $member->status)) === 'active')
                                                    ? 'member-action-disabled'
                                                    : '' }}"
                                            @disabled(!(
                                                strtolower(trim((string) $member->status)) === 'active'
                                            ))
                                        >
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M13.5 6.5l4 4m-7.5 7.5 4-4"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M6.5 10.5l7-7 4 4-7 7-4-4Z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M8 20a8 8 0 0 0 12-12"
                                                />
                                            </svg>
                                        </button>
                                        @endif


                                        @if($isWithdrawalActive)

                                            @if ($this->canCreate)

                                                <button type="button" wire:click="openWithdrawalModal({{ $member->UserID }})"
                                                    title="Create Withdrawal Request"
                                                    class="member-action member-action-withdraw" >
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.8"
                                                        d="M13.5 6.5l4 4m-7.5 7.5 4-4"
                                                    />
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.8"
                                                        d="M6.5 10.5l7-7 4 4-7 7-4-4Z"
                                                    />
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.8"
                                                        d="M8 20a8 8 0 0 0 12-12"
                                                    />
                                                </svg>
                                                </button>
                                                 @endif
                                            @endif



                                        {{-- STATUS --}}
                                        @if ($this->canUpdate)

                                            @if ($member->has_expired_activation)

                                                {{-- Latest activation EXPIRED → file a brand-new one (Renew) --}}
                                                <button
                                                    type="button"
                                                    wire:click="openRenewal({{ $member->UserID }})"
                                                    title="Renew Membership"
                                                    class="member-action member-action-renew"
                                                >
                                                    Renew Membership
                                                </button>

                                            @elseif($member->has_pending_activation)

                                                <button
                                                    type="button"
                                                    wire:click="openReview({{ $member->UserID }})"
                                                    title="Review Activation"
                                                    class="member-action member-action-pending"
                                                >
                                                    Approve Membership
                                                </button>

                                            @elseif (strtolower($member->status) === 'suspended')

                                                {{-- Suspended → unsuspend via the same confirmation dialog --}}
                                                <button
                                                    type="button"
                                                    wire:click="confirmStatus({{ $member->UserID }})"
                                                    title="Reactivate Suspended Account"
                                                    class="member-action member-action-activate"
                                                >
                                                    Activate Account
                                                </button>

                                            @elseif (strtolower($member->status) === 'inactive')

                                                <button
                                                    type="button"
                                                    wire:click="openActivation({{ $member->UserID }})"
                                                    title="Activate Member"
                                                    class="member-action member-action-activate"
                                                >
                                                    Activate Membership
                                                </button>

                                            @else

                                                {{-- Active → Suspend (replaces Deactivate) --}}
                                                <button
                                                    type="button"
                                                    wire:click="confirmStatus({{ $member->UserID }})"
                                                    title="Suspend Member"
                                                    class="member-action member-action-suspend"
                                                >
                                                    Suspend Account
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



                                        @else
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

<livewire:components.withdrawal-modal />
{{-- activation-modal + satellite-modal are mounted once at page level (masterlist.blade.php) --}}


{{-- ============================================================ --}}
{{-- DATE RANGE PICKER --}}
{{-- ============================================================ --}}

@script
<script>

    const dateInput = $wire.$el.querySelector('#memberDateRange');

    if (dateInput) {

        const picker = flatpickr(dateInput, {

            mode: 'range',

            dateFormat: 'Y-m-d',

            altInput: true,

            altFormat: 'M d, Y',

            maxDate: 'today',

            defaultDate: @js(
                $dateRange
                    ? explode(' to ', $dateRange)
                    : []
            ),

            onChange: (
                selectedDates,
                dateStr,
                instance
            ) => {

                if (selectedDates.length === 2) {

                    const start = instance.formatDate(
                        selectedDates[0],
                        'Y-m-d'
                    );

                    const end = instance.formatDate(
                        selectedDates[1],
                        'Y-m-d'
                    );

                    $wire.set(
                        'dateRange',
                        `${start} to ${end}`
                    );

                } else if (selectedDates.length === 0) {

                    $wire.set(
                        'dateRange',
                        ''
                    );

                }

            },

            onClose: (
                selectedDates,
                dateStr,
                instance
            ) => {

                if (selectedDates.length === 1) {

                    const day = instance.formatDate(
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

        });


        // RESET DATE RANGE
        $wire.on('ranges-reset', () => {

            picker.clear();

        });

    }


    {{-- STATUS CONFIRMATION (activate / deactivate) — alert shows the member's full name --}}
    $wire.on('members-confirm-status', (data) => {

        const d = Array.isArray(data) ? data[0] : data;

        // Three verbs: suspend (Active), activate (Suspended → back), activate (Inactive)
        const isSuspend = d.action === 'suspend';
        const actionText = isSuspend
            ? "I'm sure to suspend"
            : "I'm sure to activate";

        // Uses the shared SwalFlow typed-confirmation dialog
        SwalFlow.confirm({
            title: (isSuspend ? 'Suspend' : 'Activate') + ' Member?',
            html: isSuspend
                ? 'You are about to <strong>suspend</strong> the account of '
                    + '<strong style="color: #0B2A5B;">' + (d.name || 'this member') + '</strong>.'
                    + ' They will lose access until an administrator reactivates them.'
                    + '<br><br>Type the confirmation text below to continue.'
                : 'You are about to <strong>activate</strong> the account of '
                    + '<strong style="color: #0B2A5B;">' + (d.name || 'this member') + '</strong>.'
                    + '<br><br>Type the confirmation text below to continue.',
            icon: 'warning',
            requireConfirmWord: actionText,
            inputPlaceholder: actionText,
            confirmText: 'Yes, ' + (isSuspend ? 'suspend member' : 'activate member'),
            loadingTitle: 'Updating...',
            loadingText: 'Please wait.',
        }, () => $wire.updateStatus(d.id));

    });

</script>
@endscript

</div>
