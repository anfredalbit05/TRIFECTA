<div class="dashboard-grid flex flex-col lg:flex-row" x-data="{ showWithdrawModal: false }">

    <!-- RIGHT COLUMN (REFRESH HEADER + STICKY WALLET) -->
    <!-- order-1 makes this appear FIRST (at the top) on mobile -->
    <!-- lg:order-2 moves it back to the RIGHT side on large screens -->
    <div class="flex flex-col gap-4 w-full lg:w-80 shrink-0 order-1 lg:order-2">

        <!-- REFRESH BUTTON HEADER -->
        <div class="flex items-center justify-between bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div>
                <h3 class="font-bold text-gray-800 text-base">Dashboard Actions</h3>
                <p class="text-xs text-gray-500">Sync latest data feeds.</p>
            </div>
            <button wire:click="refreshDashboard" wire:loading.attr="disabled" title="Refresh Dashboard"
                class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded-lg shadow-sm transition cursor-pointer">
                <span wire:loading.remove wire:target="refreshDashboard">🔄</span>
                <span wire:loading wire:target="refreshDashboard" class="animate-spin">⏳</span>
                <span>Refresh</span>
            </button>
        </div>

        <!-- STICKY WALLET CARD -->
        <aside class="wallet-card">

            <div class="wallet-block">
                <span class="wallet-label">Cash Wallet</span>
                <h2 class="wallet-value">₱{{ number_format($totalAmount, 2) }}</h2>
                <!-- Withdraw button triggers the modal -->
                <button type="button" @click="showWithdrawModal = true" class="wallet-btn-withdraw cursor-pointer">Withdraw Cash Wallets</button>
            </div>

            <hr>

            <div class="wallet-block">
                <span class="wallet-label">Provident Fund</span>
                <h2 class="wallet-value">₱{{ number_format($providentFundAmount, 2) }}</h2>
            </div>

            <hr>

            <div class="wallet-summary">
                <div>
                    <span>Total Earnings</span>
                    ₱{{ number_format($totalEarnings, 2) }}
                </div>
                <div>
                    <span>Total Withdrawals</span>
                    <strong>₱0.00</strong>
                </div>
            </div>

            <div class="distribution-card">
                <div class="distribution-icon">📊</div>
                <div>
                    <h4>Earnings Distribution</h4>
                    <p>Categorized income based on transaction source.</p>
                </div>
            </div>

        </aside>

    </div>

    <!-- LEFT FEED COLUMN (Contains Feed Items Only) -->
    <!-- order-2 makes this appear BELOW the wallet on mobile -->
    <!-- lg:order-1 moves it to the LEFT side on large screens -->
    <div class="feed-column flex-1 order-2 lg:order-1">

        <!-- NEWS / FEED ITEMS -->
        @forelse($newsUpdates as $news)
            <article class="member-card feed-card" wire:key="news-{{ $news->ID }}">

                <!-- CARD HEADER -->
                <header class="card-header">
                    <div class="user-info">
                        <img src="{{ asset('images/user.png') }}" alt="Profile Picture" class="user-avatar">
                        <div class="user-details">
                            <div class="user-name-row">
                                <span class="user-name">
                                    {{ $news->user ? $news->user->firstname . ' ' . $news->user->lastname : 'TRUELIFE' }}
                                </span>
                                <span class="verified-icon">
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="#1877F2">
                                        <path
                                            d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-1.9 14.7L6 12.6l1.5-1.5 2.6 2.6 6.3-6.3 1.5 1.5-7.8 7.8z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="post-meta">
                                <span>{{ $news->created_at->diffForHumans() }}</span>
                                <span class="dot-separator">•</span>
                                <span class="privacy-icon">🌐</span>
                                <span class="news-category">{{ $news->category }}</span>
                            </div>
                        </div>
                    </div>

                    @if (auth()->check() && (int) auth()->user()->RoleID === 1)
                        <div class="card-actions">
                            <button class="action-btn" type="button" aria-label="More options">
                                •••
                            </button>
                        </div>
                    @endif
                </header>

                <!-- POST BODY -->
                <div class="card-body">
                    <h3 class="news-title">
                        {{ $news->title }}
                    </h3>

                    <p class="post-text">
                        {!! nl2br(e($news->details)) !!}
                    </p>

                    <!-- OPTIONAL POST IMAGE -->
                    @if ($news->image)
                        <div class="news-image-container">
                            <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}"
                                class="news-post-image" loading="lazy">
                        </div>
                    @endif
                </div>

            </article>

        @empty

            <div class="empty-news">
                <div class="empty-news-icon">📰</div>
                <h3>No news updates yet</h3>
                <p>There are currently no published news updates.</p>
            </div>
        @endforelse

    </div>

    <!-- WITHDRAWAL MODAL -->
    <div x-show="showWithdrawModal"
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
         x-transition.opacity>

        <div @click.away="showWithdrawModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden border border-gray-100">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-slate-50">
                <h3 class="font-bold text-gray-800 text-lg">Cash Wallet Withdrawal</h3>
                <button @click="showWithdrawModal = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl cursor-pointer">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                <!-- Available Balance Highlight Box -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex items-center justify-between">
                    <div>
                        <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Available Cash Wallet</span>
                        <span class="text-lg font-bold text-[#0C2C6B]">₱{{ number_format($totalAmount, 2) }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-900 text-base font-bold">
                        💳
                    </div>
                </div>

                <!-- Schedule Info Box -->
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-xs text-blue-900 space-y-2">
                    <div class="font-bold flex items-center gap-1.5 text-blue-950 text-sm">
                        <span>ℹ️</span> Payout Schedule &amp; Cut-off Rules
                    </div>
                    <p>• <strong>Monday 12:00 AM to Friday 5:59 PM:</strong> Requests processed and released on the upcoming Monday.</p>
                    <p>• <strong>Friday 6:00 PM to Sunday 11:59 PM:</strong> Requests processed and released on the following Monday (1 week interval).</p>
                </div>

                <!-- Amount Input -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-gray-700">Enter Withdrawal Amount</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold">₱</span>
                        <input type="number" placeholder="0.00" class="w-full pl-8 pr-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0C2C6B] text-sm font-semibold" />
                    </div>
                    <p class="text-[11px] text-gray-500">Minimum withdrawal amount is <strong>₱500.00</strong></p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50">
                <button @click="showWithdrawModal = false" type="button" class="px-4 py-2 border border-gray-200 text-gray-600 hover:bg-gray-100 text-xs font-semibold rounded-xl transition cursor-pointer">
                    Cancel
                </button>
                <button type="button" class="px-4 py-2 bg-[#0C2C6B] hover:bg-[#09204f] text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">
                    Submit Request
                </button>
            </div>
        </div>
    </div>

</div>
