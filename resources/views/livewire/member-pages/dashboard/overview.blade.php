<div class="dashboard-grid flex flex-col lg:flex-row">

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
            </div>

            <hr>

            <div class="wallet-block">
                <span class="wallet-label">Provident Fund</span>
                <h2 class="wallet-value">₱100.00</h2>
            </div>

            <hr>

            <div class="wallet-summary">
                <div>
                    <span>Total Earnings</span>
                    ₱{{ number_format($totalAmount, 2) }}
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
                        <img
                            src="{{ asset('images/user.png') }}"
                            alt="Profile Picture"
                            class="user-avatar"
                        >
                        <div class="user-details">
                            <div class="user-name-row">
                                <span class="user-name">
                                    {{ $news->user ? $news->user->firstname . ' ' . $news->user->lastname : 'TrueLife Dayongan' }}
                                </span>
                                <span class="verified-icon">
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="#1877F2">
                                        <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-1.9 14.7L6 12.6l1.5-1.5 2.6 2.6 6.3-6.3 1.5 1.5-7.8 7.8z"/>
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

                    @if(auth()->check() && (int) auth()->user()->RoleID === 1)
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
                            <img
                                src="{{ asset('storage/' . $news->image) }}"
                                alt="{{ $news->title }}"
                                class="news-post-image"
                                loading="lazy"
                            >
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

</div>
