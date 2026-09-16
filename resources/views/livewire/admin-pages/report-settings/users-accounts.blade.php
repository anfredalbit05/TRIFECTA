<div class="branches-container w-full px-4 sm:px-6 lg:px-8 py-6">

    <!-- HEADER -->
    <div class="members-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="masterlist-title text-2xl font-bold text-slate-800">
                Users Accounts & Financial Overview
            </h2>
            <p class="masterlist-description text-sm text-slate-500 mt-0.5">
                Manage system users, check wallet funds, provident funds, and ledger funds.
            </p>
        </div>
    </div>

    <!-- Success Message Notification -->
    @if (session()->has('message'))
        <div class="p-4 mb-4 text-[#0C2C6B] bg-[#0C2C6B]/10 border border-[#0C2C6B]/30 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <!-- SEARCH INPUT, STATUS FILTER, DATE FILTER & REFRESH BUTTON CONTAINER -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <!-- SEARCH INPUT -->
        <div class="relative max-w-sm w-full">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                </svg>
            </span>

            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Name, email, or referral code..."
                class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl
                focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]
                focus:border-[#0C2C6B]
                bg-white shadow-sm transition">
        </div>

        <!-- FILTERS & REFRESH CONTAINER -->
        <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto justify-end">

            <!-- DATE RANGE FILTER -->
            <div wire:ignore class="relative w-72">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none z-10">
                    📅
                </span>
                <input type="text" id="userDateRange" readonly
                    class="date-range-input pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white text-slate-700 shadow-sm cursor-pointer w-full text-center font-medium"
                    placeholder="Select date range">
            </div>

            <!-- Status Filter -->
            <select wire:model.live="statusFilter"
                class="bg-white border border-slate-200 text-slate-700 text-sm rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] cursor-pointer shadow-sm">
                <option value="">All Statuses</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Pending">Pending</option>
            </select>

            @if ($dateRange)
                <button type="button" wire:click="clearDateFilter" title="Clear date filter"
                    class="inline-flex items-center px-3 py-2 text-sm text-slate-500 hover:text-[#D79B10] hover:border-[#D79B10]/30 hover:bg-[#D79B10]/10 rounded-xl border border-slate-200 bg-white shadow-sm cursor-pointer transition">
                    ✕
                </button>
            @endif

            <!-- PRINT BUTTON (prints the full filtered table) -->
            <button type="button" onclick="window.print()" title="Print Report"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:text-[#0C2C6B] text-sm font-semibold rounded-xl shadow-sm transition cursor-pointer">
                <span>🖨️</span>
                <span class="hidden sm:inline">Print</span>
            </button>

            <!-- REFRESH / CLEAR BUTTON -->
            {{-- REFRESH TABLE --}}
            <button type="button" wire:click="resetTable" wire:loading.attr="disabled" title="Refresh Table"
                class="refresh-btn">
                {{-- NORMAL STATE --}}
                <span wire:loading.remove wire:target="resetTable" class="refresh-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h5M20 20v-5h-5M5.5 9A7 7 0 0 1 18 6.5L20 9M18.5 15A7 7 0 0 1 6 17.5L4 15" />
                    </svg>
                </span>

                {{-- LOADING STATE --}}
                <span wire:loading wire:target="resetTable" class="refresh-loading">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke-width="3" class="refresh-circle" />
                        <path d="M21 12a9 9 0 0 1-9 9" stroke-width="3" stroke-linecap="round" />
                    </svg>
                </span>

                <span class="hidden sm:inline">Refresh</span>
            </button>
        </div>
    </div>

    <!-- USERS TABLE (screen only) -->
    <div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-sm print:hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr
                    class="border-b border-slate-200 bg-[#0C2C6B] text-xs font-semibold uppercase tracking-wider text-white">
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">User Profile</th>
                    <th class="py-3 px-4">Role</th>
                    <th class="py-3 px-4">Direct Referral (DR)</th>
                    <th class="py-3 px-4">Provident Fund (PF)</th>
                    <th class="py-3 px-4">Overrides (OV)</th>
                    <th class="py-3 px-4">Withdrawals (WD)</th>
                    <th class="py-3 px-4">Balance</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                @forelse($users as $index => $user)
                    @php
                        $walletDR = $user->wallets->where('status', 'Active')->where('source', 'DR')->sum('amount');
                        $providentFund = $user->wallets
                            ->where('status', 'Active')
                            ->where('source', 'PF')
                            ->sum('amount');
                        $walletOV = $user->wallets->where('status', 'Active')->where('source', 'OV')->sum('amount');
                        $walletWD = $user->wallets->where('status', 'Active')->where('source', 'WD')->sum('amount');
                        $ledgerFund = $user->ledgers->sum('amount');
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-4 font-medium text-slate-500">
                            {{ $users->firstItem() + $index }}
                        </td>

                        <td class="py-4 px-4">
                            <div class="font-semibold text-slate-900">
                                {{ trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? '')) ?: 'Unnamed User' }}
                            </div>
                        </td>

                        <td class="py-4 px-4">
                            <span class="font-medium text-slate-700 block">
                                {{ $user->role->rolename ?? 'Member' }}
                            </span>
                        </td>

                        <td class="py-4 px-4 font-bold text-[#0C2C6B]">
                            ₱{{ number_format($walletDR, 2) }}
                        </td>

                        <td class="py-4 px-4 font-bold text-[#0C2C6B]">
                            ₱{{ number_format($providentFund, 2) }}
                        </td>

                        <td class="py-4 px-4 font-bold text-[#0C2C6B]">
                            ₱{{ number_format($walletOV, 2) }}
                        </td>

                        <td class="py-4 px-4 font-bold text-[#D79B10]">
                            ₱{{ number_format($walletWD, 2) }}
                        </td>

                        <td class="py-4 px-4 font-bold text-[#0B2A5B]">
                            ₱{{ number_format($walletDR + $providentFund + $walletOV - $walletWD, 2) }}
                        </td>

                        <td class="py-4 px-4">
                            @php
                                $statusClasses = match ($user->status) {
                                    'Active' => 'bg-[#0C2C6B]/10 text-[#0C2C6B] border-[#0C2C6B]/30',
                                    'Pending' => 'bg-[#D79B10]/10 text-[#D79B10] border-[#D79B10]/30',
                                    default => 'bg-slate-100 text-slate-600 border-slate-300',
                                };
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                                {{ $user->status }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-right">
                            <button wire:click="viewLedger({{ $user->UserID }})" title="View Financials"
                                class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-[#0C2C6B] text-white hover:bg-[#09204f] transition cursor-pointer text-xs font-semibold shadow">
                                👁️
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-slate-500">
                            No user accounts found matching your query.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- PAGINATION -->
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

    <!-- PRINTABLE FULL-TABLE REPORT (hidden on screen, shown only when printing) -->
    <div id="printable-users" class="hidden print:block font-sans">
        <div class="text-center font-sans mb-5 pb-3 border-b-2 border-[#0C2C6B]">
            <h1 class="m-0 text-2xl font-bold text-[#0C2C6B] font-serif">TRUE LIFE</h1>
            <p class="my-1 text-[11px] font-bold text-[#D79B10] uppercase tracking-wider">
                &mdash; S O L I D A R I T A S &mdash;
            </p>
            <h2 class="mt-2.5 mb-1 text-sm uppercase text-slate-800 font-bold">USER ACCOUNTS & FINANCIAL OVERVIEW</h2>
            <p class="text-xs text-slate-600">
                Period: {{ $dateRangeLabel }}
                @if ($statusFilter)
                    &nbsp;|&nbsp; Status: {{ $statusFilter }}
                @endif
                @if ($search)
                    &nbsp;|&nbsp; Search: "{{ $search }}"
                @endif
            </p>
        </div>

        <table class="w-full border-collapse font-sans text-[10px]">
            <thead>
                <tr class="bg-[#0C2C6B] text-white font-bold text-center h-[22px]">
                    <th class="border border-slate-900 p-1">#</th>
                    <th class="border border-slate-900 p-1">Name</th>
                    <th class="border border-slate-900 p-1">Role</th>
                    <th class="border border-slate-900 p-1">Direct Referrals</th>
                    <th class="border border-slate-900 p-1">Provident Funds</th>
                    <th class="border border-slate-900 p-1">Overrides</th>
                    <th class="border border-slate-900 p-1">Withdrawals</th>
                    <th class="border border-slate-900 p-1">Balance</th>
                    <th class="border border-slate-900 p-1">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($printUsers as $index => $user)
                    @php
                        $pDirectReferrals = $user->wallets
                            ->where('status', 'Active')
                            ->where('source', 'DR')
                            ->sum('amount');
                        $pProvidentFunds = $user->wallets
                            ->where('status', 'Active')
                            ->where('source', 'PF')
                            ->sum('amount');
                        $pOverrides = $user->wallets->where('status', 'Active')->where('source', 'OV')->sum('amount');
                        $pWithdrawals = $user->wallets->where('status', 'Active')->where('source', 'WD')->sum('amount');
                        // $pLedgerFund = $user->ledgers->sum('amount');
                    @endphp
                    <tr>
                        <td class="border border-slate-900 p-1 text-center">{{ $index + 1 }}</td>
                        <td class="border border-slate-900 p-1 font-bold">
                            {{ trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? '')) ?: 'Unnamed User' }}
                        </td>
                        <td class="border border-slate-900 p-1 text-center">{{ $user->role->rolename ?? 'Member' }}
                        </td>
                        <td class="border border-slate-900 p-1 text-right">₱{{ number_format($pDirectReferrals, 2) }}
                        </td>
                        <td class="border border-slate-900 p-1 text-right">₱{{ number_format($pProvidentFunds, 2) }}
                        </td>
                        <td class="border border-slate-900 p-1 text-right">₱{{ number_format($pOverrides, 2) }}</td>
                        <td class="border border-slate-900 p-1 text-right">₱{{ number_format($pWithdrawals, 2) }}</td>
                        <td class="border border-slate-900 p-1 text-right">₱{{ number_format((($pDirectReferrals + $pProvidentFunds + $pOverrides) - $pWithdrawals), 2) }}</td>
                        <td class="border border-slate-900 p-1 text-center">{{ $user->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="border border-slate-900 p-2.5 text-center">
                            No user accounts found for the selected filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- VIEW FINANCIALS / LEDGER MODAL -->
    @if ($isLedgerModalOpen && $selectedUser)
        <div
            class="fixed inset-0 z-[9999] w-screen h-screen bg-slate-950/70 backdrop-blur-sm overflow-y-auto print:hidden">

            <!-- CENTER MODAL -->
            <div class="min-h-screen w-full flex items-center justify-center p-4">

                <div
                    class="w-full max-w-3xl max-h-[calc(100vh-2rem)] overflow-hidden rounded-2xl bg-white shadow-2xl flex flex-col">

                    <!-- MODAL HEADER -->
                    <div class="bg-[#0C2C6B] px-6 py-4 text-white flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold">
                                User Financial Overview:
                                {{ trim(($selectedUser->firstname ?? '') . ' ' . ($selectedUser->lastname ?? '')) }}
                            </h3>
                            <p class="text-xs text-white/70">Reviewing wallets, provident funds, and individual ledger
                                records.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            {{-- <button onclick="printUserFinancials()" title="Print this user's financial overview"
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 transition hover:bg-white/30 text-white cursor-pointer text-sm">
                                🖨️
                            </button> --}}
                            <button wire:click="closeLedgerModal"
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 transition hover:bg-white/30 text-white font-bold cursor-pointer">
                                ✕
                            </button>
                        </div>
                    </div>

                    <!-- MODAL BODY -->
                    <div class="p-6 space-y-6 overflow-y-auto flex-1 min-h-0">
                        <!-- User Quick Info -->
                        <div
                            class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <div>
                                <span class="block text-xs font-semibold text-slate-400 uppercase">Email</span>
                                <span class="text-sm font-medium text-slate-800">{{ $selectedUser->email }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold text-slate-400 uppercase">Referral Code</span>
                                <span
                                    class="text-sm font-medium text-slate-800">{{ $selectedUser->referralCode ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold text-slate-400 uppercase">Status</span>
                                <span class="text-sm font-medium text-slate-800">{{ $selectedUser->status }}</span>
                            </div>
                        </div>

                        <!-- Wallets & Provident Funds Section -->
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 mb-2">Wallets & Provident Funds</h4>
                            {{-- <div class="bg-white border border-slate-200 rounded-xl overflow-hidden"> --}}
                            <div
                                class="bg-white border border-slate-200 rounded-xl overflow-hidden max-h-60 overflow-y-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-100 text-slate-600 sticky top-0">
                                        <tr>
                                            <th class="py-2 px-3">Wallet ID</th>
                                            <th class="py-2 px-3">Source Type</th>
                                            <th class="py-2 px-3">Amount</th>
                                            <th class="py-2 px-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($selectedUser->wallets as $wallet)
                                            <tr>
                                                <td class="py-2 px-3">{{ $wallet->WalletID ?? $wallet->id }}</td>
                                                <td class="py-2 px-3 font-semibold">

                                                    @if ($wallet->source == 'DR')
                                                        <span
                                                            class="text-[#0C2C6B] bg-[#0C2C6B]/10 px-2 py-0.5 rounded">
                                                            Direct Referral ({{ $wallet->source }})
                                                        </span>
                                                    @endif
                                                    @if ($wallet->source == 'PF')
                                                        <span
                                                            class="text-[#0C2C6B] bg-[#0C2C6B]/10 px-2 py-0.5 rounded">
                                                            Provident Fund (PF)
                                                        </span>
                                                    @endif
                                                    @if ($wallet->source == 'OV')
                                                        <span
                                                            class="text-[#0C2C6B] bg-[#0C2C6B]/10 px-2 py-0.5 rounded">
                                                            Overrides ({{ $wallet->source }})
                                                        </span>
                                                    @endif
                                                    @if ($wallet->source == 'WD')
                                                        <span
                                                            class="text-[#0C2C6B] bg-[#0C2C6B]/10 px-2 py-0.5 rounded">
                                                            Withdrawal (WD)
                                                        </span>
                                                    @endif

                                                    @if (!in_array($wallet->source, ['OV', 'DR', 'PF', 'WD']))
                                                        <span class="text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                                            {{ $wallet->source }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-2 px-3 font-semibold text-slate-900">
                                                    ₱{{ number_format($wallet->amount, 2) }}</td>
                                                <td class="py-2 px-3">{{ $wallet->status }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-3 px-3 text-center text-slate-400">No
                                                    records
                                                    found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Ledgers Section -->
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 mb-2">Ledger Entries</h4>
                            <div
                                class="bg-white border border-slate-200 rounded-xl overflow-hidden max-h-60 overflow-y-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-100 text-slate-600 sticky top-0">
                                        <tr>
                                            <th class="py-2 px-3">ID</th>
                                            <th class="py-2 px-3">Ref ID / Source</th>
                                            <th class="py-2 px-3">Detail</th>
                                            <th class="py-2 px-3">Amount</th>
                                            <th class="py-2 px-3">Status</th>
                                            <th class="py-2 px-3">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($statementLedgers as $ledger)
                                            <tr>
                                                <td class="py-2 px-3 font-medium text-slate-500">
                                                    {{ $ledger->TransactionID }}</td>
                                                <td class="py-2 px-3">
                                                    <span
                                                        class="font-semibold text-slate-800">{{ $ledger->RefID }}</span>
                                                    <span class="text-slate-400 text-[10px] block">Source:
                                                        {{ $ledger->Source }}</span>
                                                </td>
                                                <td class="py-2 px-3 text-[10px]">
                                                    @if ($ledger->Source === 'PK')
                                                        <span
                                                            class="font-semibold text-[#D79B10]">🎡 Pangkabuhayan Prize</span>
                                                        <span class="text-slate-400 block">
                                                            {{ $ledger->draw?->prize_name }}
                                                            {{ $ledger->draw ? '· draw of '.$ledger->draw->draw_date->format('M j, Y') : '' }}
                                                        </span>
                                                    @else
                                                        <span class="text-slate-400">—</span>
                                                    @endif
                                                </td>
                                                <td class="py-2 px-3 font-semibold text-[#0B2A5B]">
                                                    ₱{{ number_format($ledger->amount ?? 0, 2) }}</td>
                                                <td class="py-2 px-3">
                                                    <span
                                                        class="px-2 py-0.5 text-[10px] font-semibold bg-[#0C2C6B]/10 text-[#0C2C6B] rounded-full">
                                                        {{ $ledger->status }}
                                                    </span>
                                                </td>
                                                <td class="py-2 px-3 text-slate-400">
                                                    {{ $ledger->created_at?->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-3 px-3 text-center text-slate-400">No
                                                    ledger
                                                    entries found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- MODAL FOOTER -->
                    <div class="flex justify-end gap-2 border-t border-slate-200 px-6 py-3 bg-slate-50">
                        <button type="button" wire:click="closeLedgerModal"
                            class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-200 rounded-lg hover:bg-slate-300 transition cursor-pointer">
                            Close
                        </button>
                    </div>

                </div>
            </div>

            <!-- PRINTABLE SINGLE-USER FINANCIAL REPORT -->
            <div id="printable-user-financials" class="hidden font-sans">
                <div class="text-center font-sans mb-5 pb-3 border-b-2 border-[#0C2C6B]">
                    <h1 class="m-0 text-2xl font-bold text-[#0C2C6B] font-serif">
                        TRUE LIFE
                    </h1>

                    <p class="my-1 text-[11px] font-bold text-[#D79B10] uppercase tracking-wider">
                        &mdash; S O L I D A R I T A S &mdash;
                    </p>

                    <p class="text-xs text-slate-600">
                        {{ trim(($selectedUser->firstname ?? '') . ' ' . ($selectedUser->lastname ?? '')) }}
                        &nbsp;|&nbsp; {{ $selectedUser->email }}
                        &nbsp;|&nbsp; Status: {{ $selectedUser->status }}
                    </p>
                </div>

                <h3 class="text-xs font-bold uppercase text-slate-700 mb-1">Wallets & Provident Funds</h3>
                <table class="w-full border-collapse font-sans text-[10px] mb-4">
                    <thead>
                        <tr class="bg-[#0C2C6B] text-white font-bold text-center h-[22px]">
                            <th class="border border-slate-900 p-1">Wallet ID</th>
                            <th class="border border-slate-900 p-1">Source Type</th>
                            <th class="border border-slate-900 p-1">Amount</th>
                            <th class="border border-slate-900 p-1">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($selectedUser->wallets as $wallet)
                            <tr>
                                <td class="border border-slate-900 p-1 text-center">
                                    {{ $wallet->WalletID ?? $wallet->id }}</td>
                                <td class="border border-slate-900 p-1 text-center">{{ $wallet->source }}</td>
                                <td class="border border-slate-900 p-1 text-right">
                                    ₱{{ number_format($wallet->amount, 2) }}</td>
                                <td class="border border-slate-900 p-1 text-center">{{ $wallet->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="border border-slate-900 p-2 text-center">No records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <h3 class="text-xs font-bold uppercase text-slate-700 mb-1">Ledger Entries</h3>
                <table class="w-full border-collapse font-sans text-[10px]">
                    <thead>
                        <tr class="bg-[#0C2C6B] text-white font-bold text-center h-[22px]">
                            <th class="border border-slate-900 p-1">ID</th>
                            <th class="border border-slate-900 p-1">Ref ID</th>
                            <th class="border border-slate-900 p-1">Source</th>
                            <th class="border border-slate-900 p-1">Detail</th>
                            <th class="border border-slate-900 p-1">Amount</th>
                            <th class="border border-slate-900 p-1">Status</th>
                            <th class="border border-slate-900 p-1">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($statementLedgers as $ledger)
                            <tr>
                                <td class="border border-slate-900 p-1 text-center">{{ $ledger->TransactionID }}</td>
                                <td class="border border-slate-900 p-1 text-center">{{ $ledger->RefID }}</td>
                                <td class="border border-slate-900 p-1 text-center">{{ $ledger->Source }}</td>
                                <td class="border border-slate-900 p-1 text-center">
                                    @if ($ledger->Source === 'PK')
                                        {{ $ledger->draw?->prize_name }}{{ $ledger->draw ? ' · draw of '.$ledger->draw->draw_date->format('M j, Y') : ' · Spin the Wheel' }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="border border-slate-900 p-1 text-right">
                                    ₱{{ number_format($ledger->amount ?? 0, 2) }}</td>
                                <td class="border border-slate-900 p-1 text-center">{{ $ledger->status }}</td>
                                <td class="border border-slate-900 p-1 text-center">
                                    {{ $ledger->created_at?->format('M d, Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="border border-slate-900 p-2 text-center">No ledger entries
                                    found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
    @endif

    <style>
        @media print {
            @page {
                size: landscape;
                margin: 8mm;
            }

            /* Default state (no button clicked): hide everything except the full table */
            body:not([data-print-target]) * {
                visibility: hidden !important;
            }

            body:not([data-print-target]) #printable-users,
            body:not([data-print-target]) #printable-users * {
                visibility: visible !important;
            }

            body:not([data-print-target]) #printable-users {
                display: block !important;
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
            }

            /* Single-user print target: hide everything except that user's report */
            body[data-print-target="user-financials"] * {
                visibility: hidden !important;
            }

            body[data-print-target="user-financials"] #printable-user-financials,
            body[data-print-target="user-financials"] #printable-user-financials * {
                visibility: visible !important;
            }

            body[data-print-target="user-financials"] #printable-user-financials {
                display: block !important;
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
            }

            table {
                border-collapse: collapse !important;
                width: 100% !important;
            }

            th,
            td {
                border: 1px solid #000 !important;
                padding: 4px !important;
            }

            th {
                background-color: #0C2C6B !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>

    <script>
        function printUserFinancials() {
            document.body.setAttribute('data-print-target', 'user-financials');
            window.print();
        }
        window.addEventListener('afterprint', () => {
            document.body.removeAttribute('data-print-target');
        });
    </script>

    @script
        <script>
            const dateInput = $wire.$el.querySelector('#userDateRange');

            const picker = flatpickr(dateInput, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'M d, Y',
                maxDate: 'today',
                defaultDate: @js($dateRange ? explode(' to ', $dateRange) : []),
                onChange: (selectedDates, dateStr, instance) => {
                    if (selectedDates.length === 2) {
                        const start = instance.formatDate(selectedDates[0], 'Y-m-d');
                        const end = instance.formatDate(selectedDates[1], 'Y-m-d');
                        $wire.set('dateRange', `${start} to ${end}`);
                    } else if (selectedDates.length === 0) {
                        $wire.set('dateRange', '');
                    }
                },
                onClose: (selectedDates, dateStr, instance) => {
                    if (selectedDates.length === 1) {
                        const day = instance.formatDate(selectedDates[0], 'Y-m-d');
                        instance.setDate([selectedDates[0], selectedDates[0]], false);
                        $wire.set('dateRange', `${day} to ${day}`);
                    }
                }
            });

            $wire.on('ranges-reset', () => {
                picker.clear();
            });
        </script>
    @endscript

</div>
