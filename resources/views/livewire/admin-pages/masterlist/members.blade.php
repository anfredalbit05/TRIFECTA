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

           {{-- ============================================================ --}}
{{-- MEMBER TOTALS --}}
{{-- ============================================================ --}}

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">

    {{-- ALL MEMBERS --}}
    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
        <p class="text-sm font-medium text-slate-500">
            Total Members
        </p>

        <h3 class="text-3xl font-bold text-slate-800 mt-2">
            {{ number_format($totalMembers) }}
        </h3>

        <p class="text-xs text-slate-400 mt-1">
            Across all branches
        </p>
    </div>

    {{-- MEMBERS PER BRANCH --}}
    @foreach ($branchMemberCounts as $branch)
        <div
            wire:key="branch-total-{{ $branch->BranchID }}"
            class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm"
        >
            <p class="text-sm font-medium text-slate-500">
                {{ $branch->branchName }}
            </p>

            <h3 class="text-3xl font-bold text-slate-800 mt-2">
                {{ number_format($branch->total_members) }}
            </h3>

            <p class="text-xs text-slate-400 mt-1">
                Total members
            </p>
        </div>
    @endforeach

</div>

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">



        {{-- SEARCH --}}
        <div class="relative max-w-sm w-full sm:w-auto flex-1">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 pointer-events-none">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                placeholder="Search members..."
                title="Search by fullname, username, referral code, receipt number, cocogen policy number, or policy number"
                class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl
                    focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]
                    focus:border-[var(--color-primary)] bg-white shadow-sm transition"
            >
        </div>

        {{-- FILTERS AND REFRESH --}}
        <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto justify-end">

            {{-- BRANCH FILTER --}}
            <select
                wire:model.live="branchFilter"
                title="Filter by branch"
                class="border border-slate-200 rounded-xl px-3 py-2.5 text-sm
                    focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]
                    focus:border-[var(--color-primary)] bg-white font-medium text-slate-700
                    shadow-sm cursor-pointer transition"
            >
                <option value="all">All Branches</option>

                @foreach ($branches as $branch)
                    <option value="{{ $branch->BranchID }}">
                        {{ $branch->branchName }}
                    </option>
                @endforeach
            </select>

            {{-- STATUS FILTER --}}
            <select
                wire:model.live="statusFilter"
                title="Filter by status"
                class="border border-slate-200 rounded-xl px-3 py-2.5 text-sm
                    focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]
                    focus:border-[var(--color-primary)] bg-white font-medium text-slate-700
                    shadow-sm cursor-pointer transition"
            >
                <option value="all">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>

            {{-- DATE RANGE --}}
            <div wire:ignore class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-500 pointer-events-none z-10">
                    📅
                </span>

                <input
                    type="text"
                    id="memberDateRange"
                    readonly
                    class="date-range-input pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-xl
                        focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]
                        bg-white text-slate-700 shadow-sm cursor-pointer
                        w-full text-center font-medium"
                    placeholder="Select date range"
                >
            </div>

            {{-- CLEAR DATE --}}
            @if ($dateRange)
                <button
                    type="button"
                    wire:click="clearDateFilter"
                    title="Clear date filter"
                    class="inline-flex items-center justify-center px-3 py-2 text-sm
                        text-slate-500 hover:text-red-500 hover:border-red-200
                        hover:bg-red-50 rounded-xl border border-slate-200
                        bg-white shadow-sm cursor-pointer transition"
                >
                    ✕
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
                <span wire:loading.remove wire:target="resetTable" class="refresh-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 4v5h5M20 20v-5h-5M5.5 9A7 7 0 0 1 18 6.5L20 9M18.5 15A7 7 0 0 1 6 17.5L4 15"
                        />
                    </svg>
                </span>

                <span wire:loading wire:target="resetTable" class="refresh-loading">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke-width="3" class="refresh-circle" />
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

                        <th>Branch</th>

                        <th>Name</th>

                        <th>Contact</th>

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

                            {{-- BRANCH --}}
                            <td class="member-branch-cell">
                                {{ $member->branch_name ?? 'Unassigned' }}
                            </td>


                            {{-- NAME --}}
                            <td class="member-name-cell">
                                <div class="member-name">
                                    {{ $member->firstname }}
                                    {{ $member->middlename }}
                                    {{ $member->lastname }}
                                    ({{ $member->referralCode }})
                                </div>
                                <small class="text-slate-500">Upline Code: {{ filled($member->uplineCode) ? $member->uplineCode : '—' }}</small>
                            </td>


                            {{-- CONTACT --}}
                            <td class="member-contact">
                                {{ $member->contactno ?: '-' }}
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
                            @if ($this->canUpdate || $this->canRead || $this->canCreate)

                                <td class="member-actions-cell">

                                    <div class="member-actions">

                                        {{-- VIEW --}}
                                        @if ($this->canRead)

                                            <x-action-btn icon="view" tone="view"
                                                wire:click="viewMember({{ $member->UserID }})"
                                                title="View Member" />

                                        @endif


                                        {{-- EDIT --}}
                                        @if ($this->canUpdate)

                                            <x-action-btn icon="edit" tone="edit"
                                                wire:click="editMember({{ $member->UserID }})"
                                                title="Update Member" />

                                        @endif


                                        {{-- PASSWORD --}}
                                        @if ($this->canUpdate)

                                            <x-action-btn icon="key" tone="key"
                                                wire:click="promptMemberPasswordReset({{ $member->UserID }})"
                                                title="Set New Password" />

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
                                                    @disabled(! $withdrawalWindow['open'])
                                                    title="{{ $withdrawalWindow['open'] ? 'Create Withdrawal Request' : $withdrawalWindow['message'] }}"
                                                    @style(['opacity: .5' => ! $withdrawalWindow['open'], 'cursor: not-allowed' => ! $withdrawalWindow['open']])
                                                    class="member-action member-action-withdraw member-action-badge-anchor" >
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

                                                    {{-- Pending withdrawal count badge --}}
                                                    @if (($member->pending_withdrawals_count ?? 0) > 0)
                                                        <span class="member-action-badge"
                                                            title="{{ $member->pending_withdrawals_count }} pending withdrawal request(s)">{{ $member->pending_withdrawals_count }}</span>
                                                    @endif
                                                </button>
                                                 @endif
                                            @endif

                                        {{-- STATUS --}}
                                        @if ($this->canCreate)

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

                                            @elseif (strtolower($member->status) === 'suspended' && auth()->user()->RoleID == 1)

                                                {{-- Suspended → unsuspend via the same confirmation dialog --}}
                                                <button
                                                    type="button"
                                                    wire:click="confirmStatus({{ $member->UserID }})"
                                                    title="Reactivate Suspended Account"
                                                    class="member-action member-action-activate"
                                                >
                                                    Activate Account
                                                </button>

                                            @elseif ($member->has_rejected_activation)

                                                {{-- Latest activation REJECTED → reopen the
                                                     ActivationModal in reactivate mode: the same
                                                     form is pre-filled from the rejected request,
                                                     and submitting resubmits the SAME row — no
                                                     duplicate activation is created. --}}
                                                <button
                                                    type="button"
                                                    wire:click="openReactivate({{ $member->UserID }})"
                                                    title="Reactivate Membership"
                                                    class="member-action member-action-renew"
                                                >
                                                    Reactivate Membership
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

                                            @if(auth()->user()->RoleID == 1)
                                                {{-- Active → Suspend (replaces Deactivate) --}}
                                                <button
                                                    type="button"
                                                    wire:click="confirmStatus({{ $member->UserID }})"
                                                    title="Suspend Member"
                                                    class="member-action member-action-suspend"
                                                >
                                                    Suspend Account
                                                </button>
                                                @else

                                                <button
                                                    type="button"
                                                    class="member-action border border-[var(--color-primary)] bg-transparent text-black transition-colors truelife-member"
                                                >
                                                    ACTIVATED
                                                </button>
                                                @endif

                                            @endif

                                        @endif


                                        {{-- WITHDRAWAL --}}
                                        {{-- Pending withdrawals are surfaced as a count
                                             badge on the withdraw icon button above —
                                             no separate Pending button anymore. --}}

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

                        @if (! $this->approvalWindow()['open'])
                            <p
                                style="flex-basis:100%;width:100%;margin:0 0 10px;font-size:12px;font-weight:600;line-height:1.4;color:#b45309;">
                                {{ $this->approvalWindow()['message'] }}
                            </p>
                        @endif

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
                            @disabled(! $this->approvalWindow()['open'])
                            title="{{ $this->approvalWindow()['message'] }}"
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
                            @disabled(! $this->approvalWindow()['open'])
                            title="{{ $this->approvalWindow()['message'] }}"
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

    const initMemberDateRange = () => {

        // Lazy components run this block before Livewire morphs the
        // real markup in, so the input is not in the DOM on the first run.
        const dateInput = document.getElementById('memberDateRange');

        if (!dateInput) {
            return false;
        }

        if (dateInput._flatpickr) {
            return true;
        }

        if (typeof flatpickr === 'undefined') {
            return false;
        }

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

        return true;

    };

    initMemberDateRange();

    // Finish the job once Livewire has morphed the lazy component in.
    const stopWaitingForMemberDateRange = Livewire.hook('morphed', ({ component }) => {
        if (component.id !== $wire.$id) {
            return;
        }

        if (initMemberDateRange()) {
            stopWaitingForMemberDateRange();
        }
    });


    // {{-- STATUS CONFIRMATION (activate / deactivate) — alert shows the member's full name --}}
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
                    + '<strong style="color: var(--color-primary);">' + (d.name || 'this member') + '</strong>.'
                    + ' They will lose access until an administrator reactivates them.'
                    + '<br><br>Type the confirmation text below to continue.'
                : 'You are about to <strong>activate</strong> the account of '
                    + '<strong style="color: var(--color-primary);">' + (d.name || 'this member') + '</strong>.'
                    + '<br><br>Type the confirmation text below to continue.',
            icon: 'warning',
            requireConfirmWord: actionText,
            inputPlaceholder: actionText,
            confirmText: 'Yes, ' + (isSuspend ? 'suspend member' : 'activate member'),
            loadingTitle: 'Updating...',
            loadingText: 'Please wait.',
        }, () => $wire.updateStatus(d.id));

    });


    // =====================================================
    // SET NEW PASSWORD (password icon in the actions column)
    // Same dialog as the satellite-members page.
    // =====================================================
    $wire.on('set-member-password', (data) => {

        const d = Array.isArray(data) ? data[0] : data;

        const name = d?.name || 'this member';

        const policy = d?.policy || {
            min: 8,
            mixedCase: false,
            letters: false,
            numbers: false,
            symbols: false
        };

        const hint = d?.hint || 'Must contain at least ' + policy.min + ' characters.';

        const pwField = (id, label) =>
            '<div style="margin-bottom: 12px;">'
            + '<label for="' + id + '" style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; color: #334155;">'
            + label
            + '</label>'
            + '<div class="relative">'
            + '<input id="' + id + '" type="password" autocomplete="new-password" placeholder="••••••••" '
            + 'style="width: 100%; box-sizing: border-box; margin: 0;" '
            + 'class="w-full border border-slate-300 rounded-xl px-4 py-3 pr-10 text-sm focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] focus:outline-none transition">'
            + '<button type="button" class="swal-pw-toggle absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-600 focus:outline-none cursor-pointer" '
            + 'data-target="' + id + '" tabindex="-1" aria-label="Show or hide password">'
            + '<svg data-ico="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">'
            + '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />'
            + '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />'
            + '</svg>'
            + '<svg data-ico="hide" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">'
            + '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />'
            + '</svg>'
            + '</button>'
            + '</div>'
            + '</div>';

        Swal.fire({

            title: 'Set New Password',

            width: 460,

            padding: '1.25rem 1.5rem 1.5rem',

            html:
                '<div style="text-align: left;">'
                + '<p style="margin: 0 0 14px; font-size: 13px; line-height: 1.5; color: #475569;">'
                + 'Setting a new password for <b style="color: var(--color-primary);">' + name + '</b>.'
                + '</p>'
                + pwField('swal-new-password', 'New Password')
                + pwField('swal-confirm-password', 'Confirm New Password')
                + '<p style="margin: 0; font-size: 11px; line-height: 1.5; color: #64748b;">' + hint + '</p>'
                + '</div>',

            showCancelButton: true,

            confirmButtonText: 'Update Password',
            confirmButtonColor: 'var(--color-primary)',
            cancelButtonColor: 'var(--color-accent)',
            focusConfirm: false,
            allowOutsideClick: false,

            didOpen: (modal) => {

                modal.querySelectorAll('.swal-pw-toggle').forEach((btn) => {

                    btn.addEventListener('click', () => {

                        const input = modal.querySelector('#' + btn.getAttribute('data-target'));

                        const show = input.type === 'password';

                        input.type = show ? 'text' : 'password';

                        const showIcon = btn.querySelector('[data-ico="show"]');
                        const hideIcon = btn.querySelector('[data-ico="hide"]');

                        if (showIcon) showIcon.style.display = show ? 'none' : '';
                        if (hideIcon) hideIcon.style.display = show ? '' : 'none';

                    });

                });

            },

            preConfirm: () => {

                const password = document.getElementById('swal-new-password').value;
                const confirmation = document.getElementById('swal-confirm-password').value;

                const messages = [];

                if (!password) {

                    messages.push('Please enter a new password.');

                } else {

                    if (password.length < policy.min) {
                        messages.push('Password must be at least ' + policy.min + ' characters long.');
                    }

                    if (policy.mixedCase && !(/[a-z]/.test(password) && /[A-Z]/.test(password))) {
                        messages.push('Include both uppercase and lowercase letters.');
                    } else if (policy.letters && !/[a-zA-Z]/.test(password)) {
                        messages.push('Include at least one letter.');
                    }

                    if (policy.numbers && !/\d/.test(password)) {
                        messages.push('Include at least one number.');
                    }

                    if (policy.symbols && !/[^A-Za-z0-9]/.test(password)) {
                        messages.push('Include at least one symbol.');
                    }

                }

                if (confirmation !== password) {
                    messages.push('Passwords do not match.');
                }

                if (messages.length > 0) {
                    Swal.showValidationMessage(messages.join(' '));
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
                    didOpen: () => Swal.showLoading()
                });

                $wire.resetMemberPassword(d.id, result.value);

            }

        });

    });

</script>
@endscript

</div>
