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
        <div class="p-4 mb-4 text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <!-- SEARCH INPUT, STATUS FILTER & REFRESH BUTTON CONTAINER -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <!-- SEARCH INPUT -->
        <div class="relative max-w-sm w-full">
            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                🔍
            </span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Name, email, or referral code..."
                class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
        </div>

        <!-- FILTERS & REFRESH CONTAINER -->
        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <!-- Status Filter -->
            <select wire:model.live="statusFilter"
                class="bg-white border border-slate-200 text-slate-700 text-sm rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] cursor-pointer shadow-sm">
                <option value="">All Statuses</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Pending">Pending</option>
            </select>

            <!-- REFRESH / CLEAR BUTTON -->
            <button wire:click="resetTable" wire:loading.attr="disabled" title="Refresh Table"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl shadow-sm transition cursor-pointer">
                <span wire:loading.remove wire:target="resetTable">🔄</span>
                <span wire:loading wire:target="resetTable" class="animate-spin">⏳</span>
                <span class="hidden sm:inline">Refresh</span>
            </button>
        </div>
    </div>

    <!-- USERS TABLE -->
    <div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr
                    class="border-b border-slate-200 bg-[#0C2C6B] text-xs font-semibold uppercase tracking-wider text-white">
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">User Profile</th>
                    <th class="py-3 px-4">Role</th>
                    <th class="py-3 px-4">Wallet Fund (OV/DR)</th>
                    <th class="py-3 px-4">Provident Fund (PF)</th>
                    <th class="py-3 px-4">Ledger Fund</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                @forelse($users as $index => $user)
                    @php
                        // Wallet Fund includes OV and DR sources
                        $walletFund = $user->wallets
                            ->where('status', 'Active')
                            ->whereIn('source', ['OV', 'DR'])
                            ->sum('amount');
                        // Provident Fund includes PF source
                        $providentFund = $user->wallets
                            ->where('status', 'Active')
                            ->where('source', 'PF')
                            ->sum('amount');
                        // Ledger Fund total amount
                        $ledgerFund = $user->ledgers->sum('amount');
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <!-- Index Column -->
                        <td class="py-4 px-4 font-medium text-slate-500">
                            {{ $users->firstItem() + $index }}
                        </td>

                        <!-- Name & Email -->
                        <td class="py-4 px-4">
                            <div class="font-semibold text-slate-900">
                                {{ trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? '')) ?: 'Unnamed User' }}
                            </div>
                        </td>

                        <!-- Role & Referral Code -->
                        <td class="py-4 px-4">
                            <span class="font-medium text-slate-700 block">
                                {{ $user->role->rolename ?? 'Member' }}
                            </span>
                        </td>

                        <!-- Wallet Fund (OV & DR) -->
                        <td class="py-4 px-4 font-bold text-emerald-600">
                            ₱{{ number_format($walletFund, 2) }}
                        </td>

                        <!-- Provident Fund (PF) -->
                        <td class="py-4 px-4 font-bold text-purple-600">
                            ₱{{ number_format($providentFund, 2) }}
                        </td>

                        <!-- Ledger Fund -->
                        <td class="py-4 px-4 font-bold text-indigo-600">
                            ₱{{ number_format($ledgerFund, 2) }}
                        </td>

                        <!-- Status Badge -->
                        <td class="py-4 px-4">
                            @php
                                $statusClasses = match ($user->status) {
                                    'Active' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                    'Pending' => 'bg-amber-50 text-amber-600 border-amber-200',
                                    default => 'bg-rose-50 text-rose-600 border-rose-200',
                                };
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                                {{ $user->status }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="py-4 px-4 text-right">
                            <button wire:click="viewLedger({{ $user->UserID }})" title="View Financials"
                                class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-slate-600 text-white hover:bg-slate-700 transition cursor-pointer text-xs font-semibold shadow">
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

    <!-- VIEW FINANCIALS / LEDGER MODAL -->
    @if ($isLedgerModalOpen && $selectedUser)
        <div
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm overflow-y-auto">
            <div class="w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl my-8">

                <!-- MODAL HEADER -->
                <div class="bg-[#0C2C6B] px-6 py-4 text-white flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold">
                            User Financial Overview:
                            {{ trim(($selectedUser->firstname ?? '') . ' ' . ($selectedUser->lastname ?? '')) }}
                        </h3>
                        <p class="text-xs text-blue-100">Reviewing wallets, provident funds, and individual ledger
                            records.</p>
                    </div>
                    <button wire:click="closeLedgerModal"
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 transition hover:bg-white/30 text-white font-bold cursor-pointer">
                        ✕
                    </button>
                </div>

                <!-- MODAL BODY -->
                <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
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
                        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-100 text-slate-600">
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
                                                @if (in_array($wallet->source, ['OV', 'DR']))
                                                    <span
                                                        class="text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Wallet
                                                        Fund ({{ $wallet->source }})</span>
                                                @elseif($wallet->source == 'PF')
                                                    <span
                                                        class="text-purple-600 bg-purple-50 px-2 py-0.5 rounded">Provident
                                                        Fund (PF)</span>
                                                @else
                                                    <span
                                                        class="text-slate-600 bg-slate-100 px-2 py-0.5 rounded">{{ $wallet->source }}</span>
                                                @endif
                                            </td>
                                            <td class="py-2 px-3 font-semibold text-slate-900">
                                                ₱{{ number_format($wallet->amount, 2) }}</td>
                                            <td class="py-2 px-3">{{ $wallet->status }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-3 px-3 text-center text-slate-400">No records
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
                                        <th class="py-2 px-3">Amount</th>
                                        <th class="py-2 px-3">Status</th>
                                        <th class="py-2 px-3">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($selectedUser->ledgers as $ledger)
                                        <tr>
                                            <td class="py-2 px-3 font-medium text-slate-500">
                                                {{ $ledger->TransactionID }}</td>
                                            <td class="py-2 px-3">
                                                <span class="font-semibold text-slate-800">{{ $ledger->RefID }}</span>
                                                <span class="text-slate-400 text-[10px] block">Source:
                                                    {{ $ledger->Source }}</span>
                                            </td>
                                            <td class="py-2 px-3 font-semibold text-indigo-600">
                                                ₱{{ number_format($ledger->amount ?? 0, 2) }}</td>
                                            <td class="py-2 px-3">
                                                <span
                                                    class="px-2 py-0.5 text-[10px] font-semibold bg-emerald-50 text-emerald-600 rounded-full">
                                                    {{ $ledger->status }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-3 text-slate-400">
                                                {{ $ledger->created_at?->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-3 px-3 text-center text-slate-400">No ledger
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
    @endif

</div>
