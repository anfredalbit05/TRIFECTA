<div class="w-full px-4 sm:px-6 lg:px-8 py-6">

    {{-- =====================================================
         HEADER
    ===================================================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">All Users</h2>
            <p class="text-sm text-slate-500 mt-0.5">List of all registered users.</p>
        </div>

        @if (auth()->user()->RoleID == 1)
            <button type="button" wire:click="openAddModal" title="Add User"
                class="masterlist-btn inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition cursor-pointer shadow bg-[#0C2C6B] text-white hover:bg-[#09204f] w-full sm:w-auto">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-xl border border-green-200">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('success'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-xl border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 text-sm text-rose-700 bg-rose-100 rounded-xl border border-rose-200">
            {{ session('error') }}
        </div>
    @endif

    {{-- =====================================================
         SEARCH + REFRESH
    ===================================================== --}}
    <div class="flex items-center justify-between gap-4 mb-6">
        <div class="relative max-w-sm w-full">
            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">🔍</span>
            <input type="text" wire:model.live="search" placeholder="Search users..."
                class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
        </div>

        <button wire:click="resetTable" wire:loading.attr="disabled" title="Refresh Table"
            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl shadow-sm transition cursor-pointer">
            <span wire:loading.remove wire:target="resetTable">🔄</span>
            <span wire:loading wire:target="resetTable" class="animate-spin">⏳</span>
            <span class="hidden sm:inline">Refresh</span>
        </button>
    </div>

    {{-- =====================================================
         USERS TABLE
    ===================================================== --}}
    <div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[#0C2C6B] text-white text-xs uppercase">
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Username</th>
                    <th class="py-3 px-4">Contact</th>
                    <th class="py-3 px-4">Gender</th>
                    <th class="py-3 px-4">Birthday</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Role ID</th>
                    @if ($this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate)
                        <th class="py-3 px-4 text-center">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @forelse ($users as $index => $user)
                    <tr class="hover:bg-slate-50">
                        <td class="py-4 px-4">{{ $users->firstItem() + $index }}</td>
                        <td class="py-4 px-4 font-semibold">
                            {{ $user->firstname }} {{ $user->middlename }} {{ $user->lastname }}
                        </td>
                        <td class="py-4 px-4">{{ $user->username }}</td>
                        <td class="py-4 px-4">{{ $user->contactno ?? '-' }}</td>
                        <td class="py-4 px-4">{{ $user->gender ?? '-' }}</td>
                        <td class="py-4 px-4">
                            {{ $user->birthdate ? \Carbon\Carbon::parse($user->birthdate)->format('F d, Y') : '-' }}
                        </td>
                        <td class="py-4 px-4">
                            @if (strtolower($user->status) === 'active')
                                <span class="px-3 py-1 rounded-md bg-[#0C2C6B] text-white text-xs">Active</span>
                            @else
                                <span
                                    class="px-3 py-1 rounded-md border border-amber-400 bg-amber-500 text-white text-xs">Inactive</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">{{ $user->RoleID }}</td>

                        @if ($this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate)
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center space-x-2">

                                    @if ($this->canCreate)
                                        <button wire:click="openAccessModal({{ $user->UserID }})" title="User Access"
                                            class="cursor-pointer p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </button>
                                    @endif

                                    @if ($this->canRead)
                                        <button wire:click="viewUser({{ $user->UserID }})" title="View"
                                            class="cursor-pointer p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
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
                                            class="cursor-pointer p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    @endif

                                    @if ($this->canDelete)
                                        <button wire:click="confirmDelete({{ $user->UserID }})" title="Delete"
                                            class="cursor-pointer p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
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
                                                class="inline-flex items-center justify-center w-24 px-3 py-2
                                                    rounded-lg bg-amber-500 text-white
                                                    hover:bg-amber-600 transition
                                                    cursor-pointer text-xs font-semibold shadow">
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
                                                class="inline-flex items-center justify-center w-24 px-3 py-2
                                                    rounded-lg bg-[#0C2C6B] text-white
                                                    hover:bg-[#09204f]
                                                    transition cursor-pointer text-xs
                                                    font-semibold shadow">
                                                Withdraw
                                            </button>
                                        @endif
                                    @else
                                        {{-- No pending withdrawal, but withdrawals
                                            are globally disabled: show a locked
                                            state instead of a clickable button,
                                            matching the dashboard's own disabled
                                            message. --}}
                                        @if ($this->canCreate)
                                            <span title="Withdrawals are currently unavailable"
                                                class="inline-flex items-center justify-center w-24 px-3 py-2
                                                    rounded-lg bg-gray-100 border border-gray-200
                                                    text-gray-400 text-xs font-semibold
                                                    cursor-not-allowed">
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
                        <td colspan="{{ $this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate ? 9 : 8 }}"
                            class="text-center py-8 text-slate-500">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINATION --}}
        @if ($users->hasPages())
            <div class="px-4 py-4 border-t border-slate-200 bg-slate-50 flex justify-center items-center space-x-4">

                @if ($users->onFirstPage())
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
                    Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
                </span>

                @if ($users->hasMorePages())
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

    <livewire:components.withdrawal-modal />

    {{-- =====================================================
         WITHDRAWAL APPROVAL MODAL
    ===================================================== --}}
    @if ($isWithdrawalModalOpen && $selectedWithdrawalUser && $selectedWithdrawal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div
                class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden relative border border-slate-100">

                {{-- Header --}}
                <div class="bg-[#0C2C6B] px-6 py-4 flex items-center justify-between text-white">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a4 4 0 00-8 0v2m-2 0h12a2 2 0 012 2v7a2 2 0 01-2 2H7a2 2 0 01-2-2v-7a2 2 0 012-2z" />
                        </svg>

                        <h3 class="text-base font-bold uppercase tracking-wider">
                            Approve &amp; Release Withdrawal
                        </h3>
                    </div>

                    <button type="button" wire:click="closeWithdrawalModal"
                        class="cursor-pointer text-white/80 hover:text-white text-xl font-bold transition">
                        &times;
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">

                    {{-- Selected User --}}
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5">
                        <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            Withdrawal User
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

                            {{-- Requested Amount --}}
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Requested Amount</span>
                                <span class="font-bold text-slate-800 text-right">
                                    ₱{{ number_format($selectedWithdrawal->requested_amount ?? 0, 2) }}
                                </span>
                            </div>

                              {{-- Tax Amount --}}
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Tax Amount</span>
                                <span class="font-bold text-slate-800 text-right">
                                    - ₱{{ number_format($selectedWithdrawal->tax_amount ?? 0, 2) }}
                                </span>
                            </div>

                              {{-- Release Amount --}}
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Release Amount</span>
                                <span class="font-bold text-slate-800 text-right">
                                    ₱{{ number_format($selectedWithdrawal->amount ?? 0, 2) }}
                                </span>
                            </div>

                            {{-- Withdrawal Method --}}
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Method</span>
                                <span class="font-semibold text-slate-800 text-right">
                                    {{ $selectedWithdrawal->withdrawal_method ?? '-' }}
                                </span>
                            </div>

                            {{-- Bank Name (Only shown if Bank Transfer and bank_name exists) --}}
                            @if ($selectedWithdrawal->withdrawal_method === 'Bank Transfer' && $selectedWithdrawal->bank_name)
                                <div class="flex justify-between gap-4">
                                    <span class="text-gray-500">Bank Name</span>
                                    <span class="font-semibold text-slate-800 text-right">
                                        {{ $selectedWithdrawal->bank_name }}
                                    </span>
                                </div>
                            @endif

                            {{-- Account Name --}}
                            @if ($selectedWithdrawal->account_name)
                                <div class="flex justify-between gap-4">
                                    <span class="text-gray-500">Account Name</span>
                                    <span class="font-semibold text-slate-800 text-right">
                                        {{ $selectedWithdrawal->account_name }}
                                    </span>
                                </div>
                            @endif

                            {{-- Account Number --}}
                            @if ($selectedWithdrawal->account_number)
                                <div class="flex justify-between gap-4">
                                    <span class="text-gray-500">Account Number</span>
                                    <span class="font-semibold text-slate-800 text-right">
                                        {{ $selectedWithdrawal->account_number }}
                                    </span>
                                </div>
                            @endif

                            {{-- Payment Reference --}}
                            @if ($selectedWithdrawal->payment_reference)
                                <div class="flex justify-between gap-4">
                                    <span class="text-gray-500">
                                        Payment Reference
                                    </span>

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
                                from the user's wallet.
                            </p>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex justify-end space-x-2">

                    {{-- Reject --}}
                    <button type="button" wire:click="confirmWithdrawalRejection" wire:loading.attr="disabled"
                        class="cursor-pointer px-4 py-2 bg-[#D79B10] text-white rounded-xl text-sm font-semibold hover:bg-[#BE850D] transition shadow-sm">
                        Reject
                    </button>

                    {{-- Approve --}}
                    <button type="button" wire:click="confirmWithdrawalApproval" wire:loading.attr="disabled"
                        class="cursor-pointer px-4 py-2 bg-[#0B2A5B] text-white rounded-xl text-sm font-semibold hover:bg-[#081F45] transition shadow-sm">

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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div
                class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden relative border border-slate-100">

                <div class="bg-[#0C2C6B] px-6 py-4 flex items-center justify-between text-white">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <h3 class="text-base font-bold uppercase tracking-wider">User Details</h3>
                    </div>
                    <button type="button" wire:click="$set('isViewModalOpen', false)"
                        class="cursor-pointer text-white/80 hover:text-white text-xl font-bold transition">&times;</button>
                </div>

                <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">

                    <div class="flex items-center space-x-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                        @if (!empty($this->selectedUser->profilepic))
                            <img src="{{ Str::startsWith($this->selectedUser->profilepic, 'storage/') ? asset($this->selectedUser->profilepic) : asset('storage/' . $this->selectedUser->profilepic) }}"
                                class="w-14 h-14 rounded-full object-cover border border-slate-200 shadow-sm shrink-0"
                                alt="profile-image">
                        @else
                            <img src="{{ asset('images/profiles/default-avatar.png') }}"
                                class="w-14 h-14 rounded-full object-cover border border-slate-200 shadow-sm shrink-0"
                                alt="default-profile-image">
                        @endif

                        <div class="overflow-hidden">
                            <h4 class="text-base font-bold text-slate-800 truncate">
                                {{ $this->selectedUser->firstname }} {{ $this->selectedUser->middlename }}
                                {{ $this->selectedUser->lastname }}
                            </h4>
                            <p class="text-xs text-slate-500">@<span>{{ $this->selectedUser->username }}</span></p>
                            <p class="text-xs text-slate-400 truncate">{{ $this->selectedUser->email ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Personal Details --}}
                    <div>
                        <h4
                            class="text-xs font-bold text-[#0C2C6B] uppercase tracking-wider mb-3 pb-1 border-b border-slate-100 flex items-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Personal Details</span>
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                                <span
                                    class="block text-xs font-semibold text-slate-400 uppercase tracking-wide">Contact
                                    No</span>
                                <span
                                    class="font-medium text-slate-700">{{ $this->selectedUser->contactno ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                                <span
                                    class="block text-xs font-semibold text-slate-400 uppercase tracking-wide">Gender</span>
                                <span
                                    class="font-medium text-slate-700">{{ $this->selectedUser->gender ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                                <span
                                    class="block text-xs font-semibold text-slate-400 uppercase tracking-wide">Birthdate</span>
                                <span class="font-medium text-slate-700">
                                    {{ $this->selectedUser->birthdate ? \Carbon\Carbon::parse($this->selectedUser->birthdate)->format('F d, Y') : '-' }}
                                </span>
                            </div>
                            <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100 sm:col-span-2">
                                <span
                                    class="block text-xs font-semibold text-slate-400 uppercase tracking-wide">Address</span>
                                <span
                                    class="font-medium text-slate-700">{{ $this->selectedUser->address ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Account & Security Details --}}
                    <div>
                        <h4
                            class="text-xs font-bold text-[#0C2C6B] uppercase tracking-wider mb-3 pb-1 border-b border-slate-100 flex items-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Account & Security Details</span>
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wide">Role
                                    Name</span>
                                <span class="font-medium text-slate-700">
                                    {{ optional($this->selectedUser->role)->rolename ?? (\App\Models\Role::where('RoleID', $this->selectedUser->RoleID)->value('rolename') ?? 'N/A') }}
                                </span>
                            </div>
                            <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                                <span
                                    class="block text-xs font-semibold text-slate-400 uppercase tracking-wide">Position</span>
                                <span
                                    class="font-medium text-slate-700">{{ $this->selectedUser->position ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                                <span
                                    class="block text-xs font-semibold text-slate-400 uppercase tracking-wide">Referral
                                    Code</span>
                                <span
                                    class="font-medium font-mono text-slate-700">{{ $this->selectedUser->referralCode ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wide">Upline
                                    Code</span>
                                <span
                                    class="font-medium font-mono text-slate-700">{{ $this->selectedUser->uplineCode ?? '-' }}</span>
                            </div>
                            <div
                                class="bg-slate-50/50 p-3 rounded-xl border border-slate-100 sm:col-span-2 flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Account
                                    Status</span>
                                @if (strtolower($this->selectedUser->status) === 'active')
                                    <span
                                        class="px-3 py-1 rounded-md bg-[#0C2C6B] text-white text-xs font-semibold">Active</span>
                                @elseif (strtolower($this->selectedUser->status) === 'inactive')
                                    <span
                                        class="px-3 py-1 rounded-md border border-amber-400 bg-amber-500 text-white text-xs font-semibold">Inactive</span>
                                @else
                                    <span
                                        class="px-3 py-1 rounded-md border border-red-400 bg-red-500 text-white text-xs font-semibold">{{ ucfirst($this->selectedUser->status) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex justify-end">
                    <button type="button" wire:click="$set('isViewModalOpen', false)"
                        class="cursor-pointer px-4 py-2 bg-[#D79B10] text-white rounded-xl text-sm font-semibold hover:bg-[#BE850D] transition">
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div
                class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden relative border border-slate-100">

                <div class="bg-[#0C2C6B] px-6 py-4 flex items-center justify-between text-white">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <h3 class="text-base font-bold uppercase tracking-wider">
                            {{ $selectedUserId ? 'Edit User' : 'Add New User' }}
                        </h3>
                    </div>
                    <button type="button" wire:click="closeModal"
                        class="cursor-pointer text-white/80 hover:text-white text-xl font-bold transition">&times;</button>
                </div>

                <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">

                    {{-- SECTION 1: Personal Details --}}
                    <div>
                        <h4
                            class="text-xs font-bold text-[#0C2C6B] uppercase tracking-wider mb-3 pb-1 border-b border-slate-100 flex items-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Personal Details</span>
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">First
                                    Name</label>
                                <input type="text" wire:model="firstname"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
                                @error('firstname')
                                    <span
                                        class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Middle
                                    Name</label>
                                <input type="text" wire:model="middlename"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Last
                                    Name</label>
                                <input type="text" wire:model="lastname"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
                                @error('lastname')
                                    <span
                                        class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Contact
                                    No</label>
                                <input type="text" wire:model="contactno"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Gender</label>
                                <select wire:model="gender"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm cursor-pointer">
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
                            }" class="relative">
                                <label
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Birthdate</label>

                                <div @click="openPicker = !openPicker"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm cursor-pointer flex items-center justify-between">
                                    <span x-text="$wire.birthdate || 'Select birthdate'"></span>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>

                                <div x-show="openPicker" @click.away="openPicker = false" style="display: none;"
                                    class="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg p-3">
                                    <div
                                        class="flex justify-between items-center mb-2 pb-2 border-b border-slate-100 text-xs font-bold text-[#0C2C6B]">
                                        <span
                                            x-text="step === 'year' ? 'Select Year' : (step === 'month' ? 'Select Month' : 'Select Day')"></span>
                                        <button type="button" x-show="step !== 'year'"
                                            @click="step = step === 'day' ? 'month' : 'year'"
                                            class="text-slate-400 hover:text-slate-600">&larr; Back</button>
                                    </div>

                                    <div x-show="step === 'year'"
                                        class="grid grid-cols-4 gap-1 max-h-40 overflow-y-auto">
                                        <template x-for="yr in years">
                                            <button type="button" @click="selectedYear = yr; step = 'month'"
                                                class="py-1 text-xs rounded hover:bg-slate-100 font-medium"
                                                :class="selectedYear == yr ? 'bg-[#0C2C6B] text-white hover:bg-[#0C2C6B]' :
                                                    'text-slate-700'"
                                                x-text="yr"></button>
                                        </template>
                                    </div>

                                    <div x-show="step === 'month'" class="grid grid-cols-3 gap-1">
                                        <template x-for="m in months">
                                            <button type="button" @click="selectedMonth = m.val; step = 'day'"
                                                class="py-2 text-xs rounded hover:bg-slate-100 font-medium"
                                                :class="selectedMonth == m.val ? 'bg-[#0C2C6B] text-white hover:bg-[#0C2C6B]' :
                                                    'text-slate-700'"
                                                x-text="m.name.substring(0,3)"></button>
                                        </template>
                                    </div>

                                    <div x-show="step === 'day'"
                                        class="grid grid-cols-7 gap-1 max-h-40 overflow-y-auto">
                                        <template x-for="d in daysInMonth">
                                            <button type="button" @click="selectedDay = d; confirmDate()"
                                                class="py-1 text-xs rounded hover:bg-slate-100 font-medium"
                                                :class="selectedDay == d ? 'bg-[#0C2C6B] text-white hover:bg-[#0C2C6B]' :
                                                    'text-slate-700'"
                                                x-text="d"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Address</label>
                                <input type="text" wire:model="address"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 2: Account & Security --}}
                    <div>
                        <h4
                            class="text-xs font-bold text-[#0C2C6B] uppercase tracking-wider mb-3 pb-1 border-b border-slate-100 flex items-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Account & Security Details</span>
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Email</label>
                                <input type="email" wire:model="email"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
                                @error('email')
                                    <span
                                        class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Username</label>
                                <input type="text" wire:model="username"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
                                @error('username')
                                    <span
                                        class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div x-data="{ showPassword: false }">
                                <label
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Password</label>
                                <div class="relative">
                                    <input wire:model="password" :type="showPassword ? 'text' : 'password'"
                                        placeholder="••••••••"
                                        class="w-full border border-slate-200 rounded-xl px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">

                                    <button type="button" @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer">
                                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            class="w-5 h-5" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <span
                                        class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Role
                                    Name</label>
                                <select wire:model="RoleID"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm cursor-pointer">
                                    <option value="">Select Role</option>
                                    @foreach (\App\Models\Role::where('RoleID', '!=', 4)->get() as $role)
                                        <option value="{{ $role->RoleID }}">{{ $role->rolename }}</option>
                                    @endforeach
                                </select>
                                @error('RoleID')
                                    <span
                                        class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Position</label>
                                <select wire:model="position"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm cursor-pointer">
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
                                    <span
                                        class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label
                                    class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Status</label>
                                <select wire:model="status"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm cursor-pointer">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex justify-end space-x-2">
                    <button type="button" wire:click="closeModal"
                        class="cursor-pointer px-4 py-2 bg-[#D79B10] text-white rounded-xl text-sm font-semibold hover:bg-[#BE850D] transition">
                        Cancel
                    </button>
                    <button type="button" wire:click="{{ $selectedUserId ? 'updateUser' : 'storeUser' }}"
                        class="cursor-pointer px-4 py-2 bg-[#0B2A5B] text-white rounded-xl text-sm font-semibold hover:bg-[#081F45] transition shadow-sm">
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div
                class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full overflow-hidden relative border border-slate-100 max-h-[90vh] flex flex-col">

                <div class="bg-[#0C2C6B] px-6 py-4 flex items-center justify-between text-white shrink-0">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10
                                0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7
                                20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3
                                3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="text-base font-bold uppercase tracking-wider">User Access
                            Control</h3>
                    </div>
                    <button wire:click="$set('isAccessModalOpen', false)"
                        class="cursor-pointer text-white/80 hover:text-white text-xl font-bold transition">&times;</button>
                </div>

                <div
                    class="bg-slate-50 px-6 py-3 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 shrink-0">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 rounded-full bg-[#0C2C6B] text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                            {{ strtoupper(substr($this->selectedUser->firstname, 0, 1) . substr($this->selectedUser->lastname, 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">
                                {{ $this->selectedUser->firstname }} {{ $this->selectedUser->middlename }}
                                {{ $this->selectedUser->lastname }}
                            </h4>
                            <p
                                class="text-xs text-slate-500">@<span>{{ $this->selectedUser->username }}</span></p>
                        </div>
                    </div>
                    <div
                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 shadow-sm self-start sm:self-auto">
                        <span class="text-slate-400 uppercase tracking-wide text-[10px]">Role
                            ID:</span>
                        <span>{{ $this->selectedUser->RoleID }}</span>
                    </div>
                </div>

                <div class="overflow-y-auto flex-1 p-6">
                    <div
                        class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr
                                    class="bg-slate-100 text-xs text-slate-600 uppercase border-b border-slate-200 font-semibold tracking-wider">
                                    <th class="py-3 px-4">Module</th>
                                    <th class="py-3 px-3 text-center">⊕ Create</th>
                                    <th class="py-3 px-3 text-center">📥 Retrieve</th>
                                    <th class="py-3 px-3 text-center">✏️ Update</th>
                                    <th class="py-3 px-3 text-center">🗑️ Delete</th>
                                    <th class="py-3 px-3 text-center">🏷️ Status</th>
                                    <th class="py-3 px-3 text-center">🖨️ PrintAll</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($userAccess as $moduleName => $permissions)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td
                                            class="py-3.5 px-4 font-medium text-slate-700 flex items-center space-x-2.5">
                                            <input type="checkbox"
                                                class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B] w-4 h-4">
                                            <span>{{ $moduleName }}</span>
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <input type="checkbox" wire:model="userAccess.{{ $moduleName }}.create"
                                                class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B] w-4 h-4">
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <input type="checkbox"
                                                wire:model="userAccess.{{ $moduleName }}.retrieve"
                                                class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B] w-4 h-4">
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <input type="checkbox" wire:model="userAccess.{{ $moduleName }}.update"
                                                class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B] w-4 h-4">
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <input type="checkbox" wire:model="userAccess.{{ $moduleName }}.delete"
                                                class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B] w-4 h-4">
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <input type="checkbox" wire:model="userAccess.{{ $moduleName }}.status"
                                                class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B] w-4 h-4">
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <input type="checkbox"
                                                wire:model="userAccess.{{ $moduleName }}.printall"
                                                class="cursor-pointer rounded border-slate-300 text-[#0C2C6B] focus:ring-[#0C2C6B] w-4 h-4">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div
                    class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex justify-end space-x-2 shrink-0">
                    <button wire:click="$set('isAccessModalOpen', false)"
                        class="cursor-pointer px-4 py-2 bg-[#D79B10] text-white rounded-xl text-sm font-semibold hover:bg-[#BE850D] transition">
                        Close
                    </button>

                    <button wire:click="saveAccess"
                        class="cursor-pointer px-4 py-2 bg-[#0B2A5B] text-white rounded-xl text-sm font-semibold hover:bg-[#081F45] transition shadow-sm">
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div
                class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden relative border border-slate-100">
                <button wire:click="$set('isDeleteModalOpen', false)"
                    class="absolute top-4 right-4 cursor-pointer text-slate-400 hover:text-slate-600 text-xl font-bold transition z-10">&times;</button>

                <div class="p-6 pt-8 text-center space-y-4">
                    <div
                        class="w-16 h-16 bg-[#FFF8E7] text-[#D79B10] rounded-2xl flex items-center justify-center mx-auto shadow-sm border border-[#D79B10]/30 rotate-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0
                                00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-lg font-bold text-slate-800">Delete User Account</h3>
                        <p class="text-sm text-slate-500 leading-relaxed px-2">
                            Are you sure you want to delete
                            <strong class="text-slate-700 font-semibold">{{ $this->selectedUser->firstname }}
                                {{ $this->selectedUser->lastname }}</strong>?
                            This action cannot be undone.
                        </p>
                    </div>
                </div>

                <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button wire:click="$set('isDeleteModalOpen', false)"
                        class="cursor-pointer px-4 py-2 bg-[#D79B10] text-white rounded-xl text-sm font-semibold hover:bg-[#BE850D] transition">
                        Cancel
                    </button>

                    <button wire:click="deleteUser"
                        class="cursor-pointer px-4 py-2 bg-[#0B2A5B] text-white rounded-xl text-sm font-semibold hover:bg-[#081F45] transition shadow-sm">
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

</div>
