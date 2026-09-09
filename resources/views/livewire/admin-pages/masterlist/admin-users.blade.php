<div class="w-full px-4 sm:px-6 lg:px-8 py-6">

    {{-- =====================================================
         HEADER
    ===================================================== --}}
    <div class="au-header">
        <div>
            <h2 class="au-title">All Users</h2>
            <p class="au-subtitle">List of all registered users.</p>
        </div>

        @if (auth()->user()->RoleID == 1)
            <button type="button" wire:click="openAddModal" title="Add User"
                class="masterlist-btn au-btn-add">
                <svg class="au-icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add User
            </button>
        @endif
    </div>

    {{-- =====================================================
         FLASH MESSAGES
    ===================================================== --}}
    @if (session()->has('message'))
        <div class="au-alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('success'))
        <div class="au-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="au-alert-error">
            {{ session('error') }}
        </div>
    @endif

    {{-- =====================================================
         SEARCH + REFRESH
    ===================================================== --}}
    <div class="au-toolbar">
        <div class="au-search-wrap">
            <span class="au-search-icon">🔍</span>
            <input type="text" wire:model.live="search" placeholder="Search users..."
                class="au-search-input">
        </div>

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

    {{-- =====================================================
         USERS TABLE
    ===================================================== --}}
    <div class="au-table-wrap">
        <table class="au-table">
            <thead>
                <tr class="au-thead-row">
                    <th class="au-th">#</th>
                    <th class="au-th">Name</th>
                    <th class="au-th">Username</th>
                    <th class="au-th">Contact</th>
                    <th class="au-th">Gender</th>
                    <th class="au-th">Birthday</th>
                    <th class="au-th">Status</th>
                    <th class="au-th">Role ID</th>
                    @if ($this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate)
                        <th class="au-th-center">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="au-tbody">
                @forelse ($users as $index => $user)
                    <tr class="au-row">
                        <td class="au-td">{{ $users->firstItem() + $index }}</td>
                        <td class="au-td-name">
                            {{ $user->firstname }} {{ $user->middlename }} {{ $user->lastname }}
                        </td>
                        <td class="au-td">{{ $user->username }}</td>
                        <td class="au-td">{{ $user->contactno ?? '-' }}</td>
                        <td class="au-td">{{ $user->gender ?? '-' }}</td>
                        <td class="au-td">
                            {{ $user->birthdate ? \Carbon\Carbon::parse($user->birthdate)->format('F d, Y') : '-' }}
                        </td>
                        <td class="au-td">
                            @if (strtolower($user->status) === 'active')
                                <span class="au-badge-active">Active</span>
                            @else
                                <span class="au-badge-inactive">Inactive</span>
                            @endif
                        </td>
                        <td class="au-td">{{ $user->RoleID }}</td>

                        @if ($this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate)
                            <td class="au-td">
                                <div class="au-actions">

                                    @if ($this->canCreate)
                                        <button wire:click="openAccessModal({{ $user->UserID }})" title="User Access"
                                            class="au-action-btn au-action-blue">
                                            <svg class="au-icon" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </button>
                                    @endif

                                    @if ($this->canRead)
                                        <button wire:click="viewUser({{ $user->UserID }})" title="View"
                                            class="au-action-btn au-action-emerald">
                                            <svg class="au-icon" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    @endif

                                    @if ($this->canUpdate)
                                        <button wire:click="editUser({{ $user->UserID }})" title="Edit"
                                            class="au-action-btn au-action-amber">
                                            <svg class="au-icon" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    @endif

                                    {{-- SET NEW PASSWORD --}}
                                    @if ($this->canUpdate)
                                        <button wire:click="promptUserPasswordReset({{ $user->UserID }})"
                                            title="Set New Password"
                                            class="au-action-password">
                                            🔑
                                        </button>
                                    @endif

                                    @if ($this->canDelete)
                                        <button wire:click="confirmDelete({{ $user->UserID }})" title="Delete"
                                            class="au-action-btn au-action-rose">
                                            <svg class="au-icon" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif

                                    {{-- ===============================
                                        WITHDRAWAL ACTION
                                    ================================ --}}

                                    @if ($user->has_pending_withdrawal)
                                        {{-- Pending: Admin can approve.
                                            Approval always stays available even
                                            when new withdrawals are disabled — this
                                            just processes a request that already
                                            exists, it isn't creating a new one. --}}
                                        @if ($this->canUpdate)
                                            <button type="button"
                                                wire:click="openWithdrawalApproval({{ $user->UserID }})"
                                                title="Approve Withdrawal Request"
                                                class="au-wd-pending">
                                                Pending
                                            </button>
                                        @endif
                                    @elseif ($isWithdrawalActive)
                                        {{-- No pending withdrawal, and withdrawals
                                            are currently enabled:
                                            Approved withdrawal will also return here
                                            and show Withdraw again --}}
                                        @if ($this->canCreate)
                                            <button type="button"
                                                wire:click="openWithdrawalModal({{ $user->UserID }})"
                                                title="Create Withdrawal Request"
                                                class="au-wd-create">
                                                Withdraw
                                            </button>
                                        @endif
                                    @else
                                    @endif

                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate ? 9 : 8 }}"
                            class="au-empty">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINATION --}}
        @if ($users->hasPages())
            <div class="au-pagination">

                @if ($users->onFirstPage())
                    <span class="au-page-btn-disabled">
                        Previous
                    </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled"
                        class="au-page-btn"
                        style="background-color: #D79B10;" onmouseover="this.style.backgroundColor='#c58d0f'"
                        onmouseout="this.style.backgroundColor='#D79B10'">
                        Previous
                    </button>
                @endif

                <span class="au-page-text">
                    Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
                </span>

                @if ($users->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled"
                        class="au-page-btn"
                        style="background-color: #0C2C6B;" onmouseover="this.style.backgroundColor='#0a2559'"
                        onmouseout="this.style.backgroundColor='#0C2C6B'">
                        Next
                    </button>
                @else
                    <span class="au-page-btn-disabled">
                        Next
                    </span>
                @endif

            </div>
        @endif
    </div>

    <livewire:components.withdrawal-modal />

    {{-- =====================================================
         WITHDRAWAL APPROVAL MODAL
    ===================================================== --}}
    @if ($isWithdrawalModalOpen && $selectedWithdrawalUser && $selectedWithdrawal)
        <div class="au-modal-overlay">
            <div class="au-modal-box-md">

                {{-- Header --}}
                <div class="au-modal-header">
                    <div class="au-modal-header-title-wrap">
                        <svg class="au-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a4 4 0 00-8 0v2m-2 0h12a2 2 0 012 2v7a2 2 0 01-2 2H7a2 2 0 01-2-2v-7a2 2 0 012-2z" />
                        </svg>

                        <h3 class="au-modal-title">
                            Approve &amp; Release Withdrawal
                        </h3>
                    </div>

                    <button type="button" wire:click="closeWithdrawalModal"
                        class="au-modal-close">
                        &times;
                    </button>
                </div>

                {{-- Body --}}
                <div class="au-modal-body">

                    {{-- Selected User --}}
                    <div class="au-detail-card">
                        <span class="au-detail-card-label">
                            Withdrawal User
                        </span>

                        <p class="au-detail-card-value">
                            {{ $selectedWithdrawalUser->firstname }}
                            {{ $selectedWithdrawalUser->middlename }}
                            {{ $selectedWithdrawalUser->lastname }}
                        </p>
                    </div>

                    {{-- Pending Withdrawal Details --}}
                    <div class="au-pending-box">
                        <div class="au-pending-header">
                            <span class="text-xl">⏳</span>
                            <h4 class="au-pending-title">
                                Pending Withdrawal Request
                            </h4>
                        </div>

                        <div class="au-pending-rows">

                            {{-- Requested Amount --}}
                            <div class="au-pending-row">
                                <span class="au-pending-label">Requested Amount</span>
                                <span class="au-pending-value">
                                    ₱{{ number_format($selectedWithdrawal->requested_amount ?? 0, 2) }}
                                </span>
                            </div>

                              {{-- Tax Amount --}}
                            <div class="au-pending-row">
                                <span class="au-pending-label">Tax Amount</span>
                                <span class="au-pending-value">
                                    - ₱{{ number_format($selectedWithdrawal->tax_amount ?? 0, 2) }}
                                </span>
                            </div>

                              {{-- Release Amount --}}
                            <div class="au-pending-row">
                                <span class="au-pending-label">Release Amount</span>
                                <span class="au-pending-value">
                                    ₱{{ number_format($selectedWithdrawal->amount ?? 0, 2) }}
                                </span>
                            </div>

                            {{-- Withdrawal Method --}}
                            <div class="au-pending-row">
                                <span class="au-pending-label">Method</span>
                                <span class="au-pending-value-sm">
                                    {{ $selectedWithdrawal->withdrawal_method ?? '-' }}
                                </span>
                            </div>

                            {{-- Bank Name (Only shown if Bank Transfer and bank_name exists) --}}
                            @if ($selectedWithdrawal->withdrawal_method === 'Bank Transfer' && $selectedWithdrawal->bank_name)
                                <div class="au-pending-row">
                                    <span class="au-pending-label">Bank Name</span>
                                    <span class="au-pending-value-sm">
                                        {{ $selectedWithdrawal->bank_name }}
                                    </span>
                                </div>
                            @endif

                            {{-- Account Name --}}
                            @if ($selectedWithdrawal->account_name)
                                <div class="au-pending-row">
                                    <span class="au-pending-label">Account Name</span>
                                    <span class="au-pending-value-sm">
                                        {{ $selectedWithdrawal->account_name }}
                                    </span>
                                </div>
                            @endif

                            {{-- Account Number --}}
                            @if ($selectedWithdrawal->account_number)
                                <div class="au-pending-row">
                                    <span class="au-pending-label">Account Number</span>
                                    <span class="au-pending-value-sm">
                                        {{ $selectedWithdrawal->account_number }}
                                    </span>
                                </div>
                            @endif

                            {{-- Payment Reference --}}
                            @if ($selectedWithdrawal->payment_reference)
                                <div class="au-pending-row">
                                    <span class="au-pending-label">
                                        Payment Reference
                                    </span>

                                    <span class="au-pending-value-sm">
                                        {{ $selectedWithdrawal->payment_reference }}
                                    </span>
                                </div>
                            @endif

                        </div>
                    </div>

                    {{-- Confirmation Warning --}}
                    <div class="au-info-box">
                        <div class="au-info-flex">
                            <span>ℹ️</span>

                            <p>
                                Approving this request will mark the withdrawal as
                                <strong>Approved</strong> and deduct
                                <strong>
                                    ₱{{ number_format($selectedWithdrawal->requested_amount ?? 0, 2) }}
                                </strong>
                                from the user's wallet.
                            </p>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="au-modal-footer">

                    {{-- Reject --}}
                    <button type="button" wire:click="confirmWithdrawalRejection" wire:loading.attr="disabled"
                        class="au-btn-secondary">
                        Reject
                    </button>

                    {{-- Approve --}}
                    <button type="button" wire:click="confirmWithdrawalApproval" wire:loading.attr="disabled"
                        class="au-btn-primary">

                        <span wire:loading.remove wire:target="confirmWithdrawalApproval">
                            Approve &amp; Release
                        </span>

                        <span wire:loading wire:target="confirmWithdrawalApproval">
                            Processing...
                        </span>

                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- =====================================================
         VIEW USER MODAL
    ===================================================== --}}
    @if ($isViewModalOpen && $this->selectedUser)
        <div class="au-modal-overlay">
            <div class="au-modal-box-lg">

                <div class="au-modal-header">
                    <div class="au-modal-header-title-wrap">
                        <svg class="au-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <h3 class="au-modal-title">User Details</h3>
                    </div>
                    <button type="button" wire:click="$set('isViewModalOpen', false)"
                        class="au-modal-close">&times;</button>
                </div>

                <div class="au-modal-body-lg">

                    <div class="au-profile-row">
                        @if (!empty($this->selectedUser->profilepic))
                            <img src="{{ Str::startsWith($this->selectedUser->profilepic, 'storage/') ? asset($this->selectedUser->profilepic) : asset('storage/' . $this->selectedUser->profilepic) }}"
                                class="au-avatar"
                                alt="profile-image">
                        @else
                            <img src="{{ asset('images/profiles/default-avatar.png') }}"
                                class="au-avatar"
                                alt="default-profile-image">
                        @endif

                        <div class="au-profile-info">
                            <h4 class="au-profile-name">
                                {{ $this->selectedUser->firstname }} {{ $this->selectedUser->middlename }}
                                {{ $this->selectedUser->lastname }}
                            </h4>
                            <p class="au-profile-username">@<span>{{ $this->selectedUser->username }}</span></p>
                            <p class="au-profile-email">{{ $this->selectedUser->email ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Personal Details --}}
                    <div>
                        <h4 class="au-section-title">
                            <svg class="au-icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Personal Details</span>
                        </h4>
                        <div class="au-detail-grid">
                            <div class="au-detail-item">
                                <span class="au-detail-label">Contact
                                    No</span>
                                <span class="au-detail-value">{{ $this->selectedUser->contactno ?? '-' }}</span>
                            </div>
                            <div class="au-detail-item">
                                <span class="au-detail-label">Gender</span>
                                <span class="au-detail-value">{{ $this->selectedUser->gender ?? '-' }}</span>
                            </div>
                            <div class="au-detail-item">
                                <span class="au-detail-label">Birthdate</span>
                                <span class="au-detail-value">
                                    {{ $this->selectedUser->birthdate ? \Carbon\Carbon::parse($this->selectedUser->birthdate)->format('F d, Y') : '-' }}
                                </span>
                            </div>
                            <div class="au-detail-item-full">
                                <span class="au-detail-label">Address</span>
                                <span class="au-detail-value">{{ $this->selectedUser->address ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Account & Security Details --}}
                    <div>
                        <h4 class="au-section-title">
                            <svg class="au-icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Account & Security Details</span>
                        </h4>
                        <div class="au-detail-grid">
                            <div class="au-detail-item">
                                <span class="au-detail-label">Role
                                    Name</span>
                                <span class="au-detail-value">
                                    {{ optional($this->selectedUser->role)->rolename ?? (\App\Models\Role::where('RoleID', $this->selectedUser->RoleID)->value('rolename') ?? 'N/A') }}
                                </span>
                            </div>
                            <div class="au-detail-item">
                                <span class="au-detail-label">Position</span>
                                <span class="au-detail-value">{{ $this->selectedUser->position ?? '-' }}</span>
                            </div>
                            <div class="au-detail-item">
                                <span class="au-detail-label">Referral
                                    Code</span>
                                <span class="au-detail-value font-mono">{{ $this->selectedUser->referralCode ?? '-' }}</span>
                            </div>
                            <div class="au-detail-item">
                                <span class="au-detail-label">Upline
                                    Code</span>
                                <span class="au-detail-value font-mono">{{ $this->selectedUser->uplineCode ?? '-' }}</span>
                            </div>
                            <div class="au-status-row">
                                <span class="au-status-label">Account
                                    Status</span>
                                @if (strtolower($this->selectedUser->status) === 'active')
                                    <span class="au-badge-active-lg">Active</span>
                                @elseif (strtolower($this->selectedUser->status) === 'inactive')
                                    <span class="au-badge-inactive-lg">Inactive</span>
                                @else
                                    <span class="au-badge-danger-lg">{{ ucfirst($this->selectedUser->status) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                <div class="au-modal-footer justify-end">
                    <button type="button" wire:click="$set('isViewModalOpen', false)"
                        class="au-btn-secondary">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- =====================================================
         ADD / EDIT USER MODAL
    ===================================================== --}}
    @if ($isAddModalOpen || $isEditModalOpen)
        <div class="au-modal-overlay">
            <div class="au-modal-box-lg">

                <div class="au-modal-header">
                    <div class="au-modal-header-title-wrap">
                        <svg class="au-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <h3 class="au-modal-title">
                            {{ $selectedUserId ? 'Edit User' : 'Add New User' }}
                        </h3>
                    </div>
                    <button type="button" wire:click="closeModal"
                        class="au-modal-close">&times;</button>
                </div>

                <div class="au-modal-body-lg">

                    {{-- SECTION 1: Personal Details --}}
                    <div>
                        <h4 class="au-section-title">
                            <svg class="au-icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Personal Details</span>
                        </h4>
                        <div class="au-form-grid">

                            <div>
                                <label class="au-form-label">First
                                    Name</label>
                                <input type="text" wire:model="firstname"
                                    class="au-form-input">
                                @error('firstname')
                                    <span class="au-form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="au-form-label">Middle
                                    Name</label>
                                <input type="text" wire:model="middlename"
                                    class="au-form-input">
                            </div>

                            <div>
                                <label class="au-form-label">Last
                                    Name</label>
                                <input type="text" wire:model="lastname"
                                    class="au-form-input">
                                @error('lastname')
                                    <span class="au-form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="au-form-label">Contact
                                    No</label>
                                <input type="text" wire:model="contactno"
                                    class="au-form-input">
                            </div>

                            <div>
                                <label class="au-form-label">Gender</label>
                                <select wire:model="gender"
                                    class="au-form-select">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            {{-- Birthdate (Custom Alpine Picker) --}}
                            <div wire:key="birthdate-picker" x-data="{
                                openPicker: false,
                                step: 'year',
                                selectedYear: '2000',
                                selectedMonth: '01',
                                selectedDay: '01',
                                years: Array.from({ length: 80 }, (_, i) => 2026 - i),
                                months: [
                                    { name: 'January', val: '01' }, { name: 'February', val: '02' }, { name: 'March', val: '03' },
                                    { name: 'April', val: '04' }, { name: 'May', val: '05' }, { name: 'June', val: '06' },
                                    { name: 'July', val: '07' }, { name: 'August', val: '08' }, { name: 'September', val: '09' },
                                    { name: 'October', val: '10' }, { name: 'November', val: '11' }, { name: 'December', val: '12' }
                                ],
                                get daysInMonth() {
                                    return Array.from({ length: new Date(this.selectedYear, this.selectedMonth, 0).getDate() }, (_, i) => String(i + 1).padStart(2, '0'));
                                },
                                init() {
                                    this.$watch('$wire.birthdate', (value) => {
                                        if (!value) return;
                                        let parts = value.split('-');
                                        if (parts.length === 3) {
                                            this.selectedYear = parts[0];
                                            this.selectedMonth = parts[1];
                                            this.selectedDay = parts[2];
                                        }
                                    });
                                    if (this.$wire.birthdate) {
                                        let parts = this.$wire.birthdate.split('-');
                                        if (parts.length === 3) {
                                            this.selectedYear = parts[0];
                                            this.selectedMonth = parts[1];
                                            this.selectedDay = parts[2];
                                        }
                                    }
                                },
                                confirmDate() {
                                    let formatted = `${this.selectedYear}-${this.selectedMonth}-${this.selectedDay}`;
                                    @this.set('birthdate', formatted);
                                    this.openPicker = false;
                                    this.step = 'year';
                                }
                            }" class="au-bday-wrap">
                                <label class="au-form-label">Birthdate</label>

                                <div @click="openPicker = !openPicker"
                                    class="au-bday-trigger">
                                    <span x-text="$wire.birthdate || 'Select birthdate'"></span>
                                    <svg class="au-bday-trigger-icon" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>

                                <div x-show="openPicker" @click.away="openPicker = false" style="display: none;"
                                    class="au-bday-panel">
                                    <div class="au-bday-panel-header">
                                        <span
                                            x-text="step === 'year' ? 'Select Year' : (step === 'month' ? 'Select Month' : 'Select Day')"></span>
                                        <button type="button" x-show="step !== 'year'"
                                            @click="step = step === 'day' ? 'month' : 'year'"
                                            class="au-bday-back">&larr; Back</button>
                                    </div>

                                    <div x-show="step === 'year'"
                                        class="au-bday-grid-year">
                                        <template x-for="yr in years">
                                            <button type="button" @click="selectedYear = yr; step = 'month'"
                                                class="au-bday-item"
                                                :class="selectedYear == yr ? 'au-bday-item-active' :
                                                    'au-bday-item-default'"
                                                x-text="yr"></button>
                                        </template>
                                    </div>

                                    <div x-show="step === 'month'" class="au-bday-grid-month">
                                        <template x-for="m in months">
                                            <button type="button" @click="selectedMonth = m.val; step = 'day'"
                                                class="au-bday-item-month"
                                                :class="selectedMonth == m.val ? 'au-bday-item-active' :
                                                    'au-bday-item-default'"
                                                x-text="m.name.substring(0,3)"></button>
                                        </template>
                                    </div>

                                    <div x-show="step === 'day'"
                                        class="au-bday-grid-day">
                                        <template x-for="d in daysInMonth">
                                            <button type="button" @click="selectedDay = d; confirmDate()"
                                                class="au-bday-item"
                                                :class="selectedDay == d ? 'au-bday-item-active' :
                                                    'au-bday-item-default'"
                                                x-text="d"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="au-col-span-2">
                                <label class="au-form-label">Address</label>
                                <input type="text" wire:model="address"
                                    class="au-form-input">
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 2: Account & Security --}}
                    <div>
                        <h4 class="au-section-title">
                            <svg class="au-icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Account & Security Details</span>
                        </h4>
                        <div class="au-form-grid">

                            <div>
                                <label class="au-form-label">Email</label>
                                <input type="email" wire:model="email"
                                    class="au-form-input">
                                @error('email')
                                    <span class="au-form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="au-form-label">Username</label>
                                <input type="text" wire:model="username"
                                    class="au-form-input">
                                @error('username')
                                    <span class="au-form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div x-data="{ showPassword: false }">
                                <label class="au-form-label">Password</label>
                                <div class="au-password-wrap">
                                    <input wire:model="password" :type="showPassword ? 'text' : 'password'"
                                        placeholder="••••••••"
                                        class="au-form-input-password">

                                    <button type="button" @click="showPassword = !showPassword"
                                        class="au-password-toggle">
                                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            class="au-icon">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            class="au-icon" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="au-form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="au-form-label">Role
                                    Name</label>
                                <select wire:model="RoleID"
                                    class="au-form-select">
                                    <option value="">Select Role</option>
                                    @foreach (\App\Models\Role::where('RoleID', '!=', 4)->get() as $role)
                                        <option value="{{ $role->RoleID }}">{{ $role->rolename }}</option>
                                    @endforeach
                                </select>
                                @error('RoleID')
                                    <span class="au-form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="au-form-label">Position</label>
                                <select wire:model="position"
                                    class="au-form-select">
                                    <option value="">Select Position</option>
                                    <option value="CEO">Chief Executive Officer (CEO)</option>
                                    <option value="COO">Chief Operating Officer (COO)</option>
                                    <option value="Auditor">Auditor</option>
                                    <option value="Board of Directors">Board of Directors</option>
                                    <option value="PIO">PIO</option>
                                    <option value="Secretary">Secretary</option>
                                    <option value="Treasurer">Treasurer</option>
                                </select>
                                @error('position')
                                    <span class="au-form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="au-col-span-2">
                                <label class="au-form-label">Status</label>
                                <select wire:model="status"
                                    class="au-form-select">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="au-modal-footer">
                    <button type="button" wire:click="closeModal"
                        class="au-btn-secondary">
                        Cancel
                    </button>
                    <button type="button" wire:click="{{ $selectedUserId ? 'updateUser' : 'storeUser' }}"
                        class="au-btn-primary">
                        {{ $selectedUserId ? 'Update User' : 'Save User' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- =====================================================
         USER ACCESS MODAL
    ===================================================== --}}
    @if ($isAccessModalOpen && $this->selectedUser)
        <div class="au-modal-overlay">
            <div class="au-modal-box-xl">

                <div class="au-modal-header">
                    <div class="au-modal-header-title-wrap">
                        <svg class="au-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10
                                0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7
                                20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3
                                3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="au-modal-title">User Access
                            Control</h3>
                    </div>
                    <button wire:click="$set('isAccessModalOpen', false)"
                        class="au-modal-close">&times;</button>
                </div>

                <div class="au-access-subheader">
                    <div class="au-access-user-wrap">
                        <div class="au-avatar-circle">
                            {{ strtoupper(substr($this->selectedUser->firstname, 0, 1) . substr($this->selectedUser->lastname, 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="au-access-user-name">
                                {{ $this->selectedUser->firstname }} {{ $this->selectedUser->middlename }}
                                {{ $this->selectedUser->lastname }}
                            </h4>
                            <p class="au-profile-username">@<span>{{ $this->selectedUser->username }}</span></p>
                        </div>
                    </div>
                    <div class="au-role-badge">
                        <span class="au-role-badge-label">Role
                            ID:</span>
                        <span>{{ $this->selectedUser->RoleID }}</span>
                    </div>
                </div>

                <div class="au-access-body">
                    <div class="au-access-table-wrap">
                        <table class="au-access-table">
                            <thead>
                                <tr class="au-access-thead-row">
                                    <th class="au-access-th">Module</th>
                                    <th class="au-access-th-center">⊕ Create</th>
                                    <th class="au-access-th-center">📥 Retrieve</th>
                                    <th class="au-access-th-center">✏️ Update</th>
                                    <th class="au-access-th-center">🗑️ Delete</th>
                                    <th class="au-access-th-center">🏷️ Status</th>
                                    <th class="au-access-th-center">🖨️ PrintAll</th>
                                </tr>
                            </thead>
                            <tbody class="au-access-tbody">
                                @foreach ($userAccess as $moduleName => $permissions)
                                    <tr class="au-access-row">
                                        <td class="au-access-module-td">
                                            <input type="checkbox"
                                                class="au-checkbox">
                                            <span>{{ $moduleName }}</span>
                                        </td>
                                        <td class="au-access-perm-td">
                                            <input type="checkbox" wire:model="userAccess.{{ $moduleName }}.create"
                                                class="au-checkbox">
                                        </td>
                                        <td class="au-access-perm-td">
                                            <input type="checkbox"
                                                wire:model="userAccess.{{ $moduleName }}.retrieve"
                                                class="au-checkbox">
                                        </td>
                                        <td class="au-access-perm-td">
                                            <input type="checkbox" wire:model="userAccess.{{ $moduleName }}.update"
                                                class="au-checkbox">
                                        </td>
                                        <td class="au-access-perm-td">
                                            <input type="checkbox" wire:model="userAccess.{{ $moduleName }}.delete"
                                                class="au-checkbox">
                                        </td>
                                        <td class="au-access-perm-td">
                                            <input type="checkbox" wire:model="userAccess.{{ $moduleName }}.status"
                                                class="au-checkbox">
                                        </td>
                                        <td class="au-access-perm-td">
                                            <input type="checkbox"
                                                wire:model="userAccess.{{ $moduleName }}.printall"
                                                class="au-checkbox">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="au-modal-footer">
                    <button wire:click="$set('isAccessModalOpen', false)"
                        class="au-btn-secondary">
                        Close
                    </button>

                    <button wire:click="saveAccess"
                        class="au-btn-primary">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- =====================================================
         DELETE CONFIRMATION MODAL
    ===================================================== --}}
    @if ($isDeleteModalOpen && $this->selectedUser)
        <div class="au-modal-overlay">
            <div class="au-modal-box-md">
                <button wire:click="$set('isDeleteModalOpen', false)"
                    class="au-delete-close">&times;</button>

                <div class="au-delete-body">
                    <div class="au-delete-icon-wrap">
                        <svg class="au-delete-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0
                                00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="au-delete-text-wrap">
                        <h3 class="au-delete-title">Delete User Account</h3>
                        <p class="au-delete-message">
                            Are you sure you want to delete
                            <strong class="text-slate-700 font-semibold">{{ $this->selectedUser->firstname }}
                                {{ $this->selectedUser->lastname }}</strong>?
                            This action cannot be undone.
                        </p>
                    </div>
                </div>

                <div class="au-delete-footer">
                    <button wire:click="$set('isDeleteModalOpen', false)"
                        class="au-btn-secondary">
                        Cancel
                    </button>

                    <button wire:click="deleteUser"
                        class="au-btn-primary">
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    @endif

    @script
        <script>
            /*
            |--------------------------------------------------------------------------
            | CONFIRM WITHDRAWAL APPROVAL
            |--------------------------------------------------------------------------
            */

            $wire.on('confirm-withdrawal-approval', (event) => {

                Swal.fire({
                    title: 'Approve Withdrawal?',
                    html: `
                <div style="font-size: 14px; color: #64748b;">
                    Are you sure you want to approve and release this withdrawal?

                    <br><br>

                    <strong style="font-size: 20px; color: #0B2A5B;">
                        ₱${event.amount}
                    </strong>

                    <br><br>

                    This amount will be deducted from the user's wallet.

                    <br><br>

                    <div style="
                        background: #FFF8E7;
                        border: 1px solid #D79B10;
                        padding: 12px;
                        border-radius: 8px;
                        color: #0B2A5B;
                    ">
                        Type <strong>APPROVE</strong> below to confirm.
                    </div>
                </div>
            `,
                    icon: 'warning',
                    input: 'text',
                    inputPlaceholder: 'Type APPROVE',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Approve',
                    cancelButtonText: 'Cancel',

                    // YOUR PALETTE
                    confirmButtonColor: '#0B2A5B',
                    cancelButtonColor: '#D79B10',

                    reverseButtons: true,
                    focusCancel: true,

                    inputValidator: (value) => {
                        if (!value || value.trim().toUpperCase() !== 'APPROVE') {
                            return 'You must type APPROVE to confirm.';
                        }
                    }

                }).then((result) => {

                    if (result.isConfirmed) {
                        $wire.call('processWithdrawalApproval');
                    }

                });

            });

            /*
            |--------------------------------------------------------------------------
            | CONFIRM WITHDRAWAL REJECTION
            |--------------------------------------------------------------------------
            */

            $wire.on('confirm-withdrawal-rejection', (event) => {

                Swal.fire({
                    title: 'Reject Withdrawal?',
                    html: `
                <div style="font-size: 14px; color: #64748b;">

                    Are you sure you want to reject this withdrawal?

                    <br><br>

                    <strong style="font-size: 20px; color: #0B2A5B;">
                        ₱${event.amount}
                    </strong>

                    <br><br>

                    <div style="
                        background: #FFF8E7;
                        border: 1px solid #D79B10;
                        padding: 12px;
                        border-radius: 8px;
                        color: #0B2A5B;
                    ">
                        Type <strong>REJECT</strong> below to confirm.
                    </div>

                </div>
            `,
                    icon: 'warning',
                    input: 'text',
                    inputPlaceholder: 'Type REJECT',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Reject',
                    cancelButtonText: 'Cancel',

                    // YOUR PALETTE
                    confirmButtonColor: '#0B2A5B',
                    cancelButtonColor: '#D79B10',

                    reverseButtons: true,
                    focusCancel: true,

                    inputValidator: (value) => {

                        if (!value || value.trim().toUpperCase() !== 'REJECT') {
                            return 'You must type REJECT to confirm.';
                        }

                    }

                }).then((result) => {

                    if (result.isConfirmed) {
                        $wire.call('processWithdrawalRejection');
                    }

                });

            });

            /*
            |--------------------------------------------------------------------------
            | WITHDRAWAL APPROVED SUCCESS
            |--------------------------------------------------------------------------
            */

            $wire.on('withdrawal-approved-success', () => {

                Swal.fire({
                    icon: 'success',
                    title: 'Withdrawal Approved!',
                    text: 'The withdrawal has been successfully approved, released, and deducted from the user wallet.',

                    // YOUR PALETTE
                    confirmButtonColor: '#0B2A5B',

                    confirmButtonText: 'OK'
                });

            });

            /*
            |--------------------------------------------------------------------------
            | WITHDRAWAL APPROVAL ERROR
            |--------------------------------------------------------------------------
            */

            $wire.on('withdrawal-approved-error', (event) => {

                Swal.fire({
                    icon: 'error',
                    title: 'Approval Failed',
                    text: event.message ||
                        'Something went wrong while approving the withdrawal.',

                    // YOUR PALETTE
                    confirmButtonColor: '#0B2A5B',

                    confirmButtonText: 'OK'
                });

            });
        </script>
    @endscript

    @script
        <script>
            /*
            |--------------------------------------------------------------------------
            | SET NEW PASSWORD (dialog)
            |--------------------------------------------------------------------------
            */

            $wire.on('set-user-password', (data) => {
                const name = data?.name || 'this user';
                const policy = data?.policy || { min: 8, mixedCase: false, letters: false, numbers: false, symbols: false };
                const hint = data?.hint || 'Must contain at least ' + policy.min + ' characters.';

                Swal.fire({
                    title: 'Set New Password',
                    width: 460,
                    padding: '1.25rem 1.5rem 1.5rem',
                    html:
                        '<div style="text-align: left;">' +
                            '<p class="au-swal-intro">' +
                                'Setting a new password for <b style="color: #0B2A5B;">' + name + '</b>.' +
                            '</p>' +

                            '<div class="au-swal-field">' +
                                '<label for="swal-user-new-password" class="au-swal-label">New Password</label>' +
                                '<div class="au-swal-input-wrap">' +
                                    '<input id="swal-user-new-password" type="password" autocomplete="new-password" ' +
                                        'placeholder="••••••••" ' +
                                        'style="width: 100%; box-sizing: border-box; margin: 0;" ' +
                                        'class="au-swal-input">' +
                                    '<button type="button" class="swal-pw-toggle au-swal-toggle-btn" ' +
                                        'data-target="swal-user-new-password" tabindex="-1" aria-label="Show or hide password">' +
                                        '<svg data-ico="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="au-icon">' +
                                            '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />' +
                                            '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />' +
                                        '</svg>' +
                                        '<svg data-ico="hide" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="au-icon" style="display: none;">' +
                                            '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />' +
                                        '</svg>' +
                                    '</button>' +
                                '</div>' +
                            '</div>' +

                            '<div class="au-swal-field-last">' +
                                '<label for="swal-user-confirm-password" class="au-swal-label">Confirm New Password</label>' +
                                '<div class="au-swal-input-wrap">' +
                                    '<input id="swal-user-confirm-password" type="password" autocomplete="new-password" ' +
                                        'placeholder="••••••••" ' +
                                        'style="width: 100%; box-sizing: border-box; margin: 0;" ' +
                                        'class="au-swal-input">' +
                                    '<button type="button" class="swal-pw-toggle au-swal-toggle-btn" ' +
                                        'data-target="swal-user-confirm-password" tabindex="-1" aria-label="Show or hide password">' +
                                        '<svg data-ico="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="au-icon">' +
                                            '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />' +
                                            '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />' +
                                        '</svg>' +
                                        '<svg data-ico="hide" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="au-icon" style="display: none;">' +
                                            '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />' +
                                        '</svg>' +
                                    '</button>' +
                                '</div>' +
                            '</div>' +

                            '<p class="au-swal-hint">' + hint + '</p>' +
                        '</div>',
                    showCancelButton: true,
                    confirmButtonText: 'Update Password',
                    confirmButtonColor: '#0C2C6B',
                    cancelButtonColor: '#D79B10',
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

                                if (showIcon) {
                                    showIcon.style.display = show ? 'none' : '';
                                }
                                if (hideIcon) {
                                    hideIcon.style.display = show ? '' : 'none';
                                }
                            });
                        });
                    },
                    preConfirm: () => {
                        const password = document.getElementById('swal-user-new-password').value;
                        const confirmation = document.getElementById('swal-user-confirm-password').value;

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
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $wire.resetUserPassword(data.id, result.value);
                    }
                });
            });

            /*
            |--------------------------------------------------------------------------
            | SET NEW PASSWORD ERROR
            |--------------------------------------------------------------------------
            */

            $wire.on('user-password-error', (event) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Update Failed',
                    text: event.message || 'Something went wrong while updating the password.',
                    confirmButtonColor: '#0C2C6B',
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endscript

</div>
