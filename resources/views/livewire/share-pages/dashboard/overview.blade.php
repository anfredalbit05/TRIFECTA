<div class="dashboard-grid">

    <!-- =========================
         RIGHT COLUMN / WALLET
    ========================== -->
    <div class="dashboard-sidebar order-1 lg:order-2">

        <!-- REFRESH BUTTON HEADER -->
        <div class="flex items-center justify-between bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div>
                <h3 class="font-bold text-gray-800 text-base">
                    Dashboard Actions
                </h3>

                <p class="text-xs text-gray-500">
                    Sync latest data feeds.
                </p>
            </div>

            {{-- REFRESH --}}
            <button type="button" wire:click="refreshDashboard" wire:loading.attr="disabled" title="Refresh Dashboard"
                class="refresh-btn">
                {{-- NORMAL STATE --}}
                <span wire:loading.remove wire:target="refreshDashboard" class="refresh-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h5M20 20v-5h-5M5.5 9A7 7 0 0 1 18 6.5L20 9M18.5 15A7 7 0 0 1 6 17.5L4 15" />
                    </svg>
                </span>

                {{-- LOADING STATE --}}
                <span wire:loading wire:target="refreshDashboard" class="refresh-loading">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke-width="3" class="refresh-circle" />
                        <path d="M21 12a9 9 0 0 1-9 9" stroke-width="3" stroke-linecap="round" />
                    </svg>
                </span>

                <span>Refresh</span>
            </button>
        </div>

        @if (!in_array(auth()->user()->RoleID, [4, 8]))

            @if ($isKabuhayanSpinActive)

                {{-- ACTIVE BUTTON --}}
                <button type="button"
                    wire:click="$dispatch('open-spin-wheel')"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-70"
                    class="wallet-btn-spin cursor-pointer">

                    <span class="wallet-btn-spin-icon">
                        🎡
                    </span>

                    <span class="min-w-0 flex-1">

                        <span class="wallet-btn-spin-title">
                            Spin the Wheel
                        </span>

                        <span class="wallet-btn-spin-hint">
                            Pangkabuhayan raffle · drawn every {{ \App\Services\SpinWheelService::ordinal(app(\App\Services\SpinWheelService::class)->drawDay()) }}
                        </span>

                    </span>

                    <svg xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="h-4 w-4 shrink-0 text-white/70">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m8.25 4.5 7.5 7.5-7.5 7.5" />

                    </svg>

                </button>

            @else

                {{-- DISABLED BUTTON --}}
                <div
                    class="w-full px-4 py-2.5 rounded-lg
                        bg-gray-100 border border-gray-200
                        text-gray-500 text-sm font-semibold
                        mt-6 text-center cursor-not-allowed
                        wallet-btn-spin">

                    <span class="wallet-btn-spin-icon">
                        🎡
                    </span>

                    <span class="min-w-0 flex-1">
                        Spin the Wheel is currently unavailable
                    </span>

                </div>

            @endif

        @endif


        <!-- =========================
             WALLET CARD
        ========================== -->
        <aside class="wallet-card">

            <!-- CASH WALLET -->
            <div class="wallet-block">

                <span class="wallet-label">
                    Cash Wallet
                </span>

                <h2 class="wallet-value">
                    ₱{{ number_format($totalAmount, 2) }}
                </h2>


                <!-- WITHDRAWAL STATUS -->
                @if ($isWithdrawalActive)
                    <button type="button" wire:click="openWithdrawalModal" class="wallet-btn-withdraw cursor-pointer">
                        Withdraw Cash Wallet
                    </button>
                @else
                    <div
                        class="w-full px-4 py-2.5 rounded-lg
                        bg-gray-100 border border-gray-200
                        text-gray-500 text-sm font-semibold
                        mt-6 text-center cursor-not-allowed">
                        Withdrawals are currently unavailable
                    </div>
                @endif

            </div>


            <hr>


            <!-- PROVIDENT FUND -->
            <div class="wallet-block">

                <span class="wallet-label">
                    Provident Fund
                </span>

                <h2 class="wallet-value">
                    ₱{{ number_format($providentFundAmount, 2) }}
                </h2>

            </div>


            <hr>


            <!-- WALLET SUMMARY -->
            <div class="wallet-summary">

                <div>
                    <span>
                        Total Earnings
                    </span>

                    <strong>
                        ₱{{ number_format($totalEarnings, 2) }}
                    </strong>
                </div>


                <div>
                    <span>
                        Total Withdrawals
                    </span>

                    <strong>
                        ₱{{ number_format($totalWithdrawals, 2) }}
                    </strong>
                </div>

            </div>


            <!-- EARNINGS DISTRIBUTION -->
            <div class="distribution-card">


                <div>
                    <h4>
                        Earnings Distribution
                    </h4>

                    <p>
                        Categorized income based on transaction source.
                    </p>
                </div>

            </div>

        </aside>
    </div>


    <!-- =========================
         LEFT COLUMN / NEWS FEED
    ========================== -->
    <div class="feed-column order-2 lg:order-1">

        <!-- NEWS ITEMS -->
        @forelse ($newsUpdates as $news)
            <article class="member-card feed-card" wire:key="news-{{ $news->ID }}">

                <!-- CARD HEADER -->
                <header class="card-header">

                    <div class="user-info">

                        <img src="{{ asset('images/user.png') }}" alt="Profile Picture" class="user-avatar">


                        <div class="user-details">

                            <!-- USER NAME -->
                            <div class="user-name-row">

                                <span class="user-name">
                                    {{ $news->user ? $news->user->firstname . ' ' . $news->user->lastname : 'TRUELIFE' }}
                                </span>


                                <!-- VERIFIED ICON -->
                                <span class="verified-icon">

                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="#1877F2">
                                        <path
                                            d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-1.9 14.7L6 12.6l1.5-1.5 2.6 2.6 6.3-6.3 1.5 1.5-7.8 7.8z" />
                                    </svg>

                                </span>

                            </div>


                            <!-- POST META -->
                            <div class="post-meta">

                                <span>
                                    {{ $news->created_at->diffForHumans() }}
                                </span>

                                <span class="dot-separator">
                                    •
                                </span>

                                <span class="privacy-icon">
                                    🌐
                                </span>

                                <span class="news-category">
                                    {{ $news->category }}
                                </span>

                            </div>

                        </div>

                    </div>


                    <!-- ADMIN ACTION -->
                    @if (auth()->check() && (int) auth()->user()->RoleID === 1)
                        <div class="card-actions">

                            <button class="action-btn" type="button" aria-label="More options">
                                •••
                            </button>

                        </div>
                    @endif

                </header>


                <!-- =========================
                     POST BODY
                ========================== -->
                <div class="card-body">

                    <!-- TITLE -->
                    <h3 class="news-title">
                        {{ $news->title }}
                    </h3>


                    <!-- DETAILS -->
                    <p class="post-text">
                        {!! nl2br(e($news->details)) !!}
                    </p>


                    <!-- POST IMAGE -->
                    @if ($news->image)
                        <div class="news-image-container">

                            <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}"
                                class="news-post-image" loading="lazy">

                        </div>
                    @endif

                </div>

            </article>

        @empty

            <!-- =========================
                 EMPTY NEWS STATE
            ========================== -->
            <div class="empty-news">

                <div class="empty-news-icon">
                    📰
                </div>

                <h3>
                    No news updates yet
                </h3>

                <p>
                    There are currently no published news updates.
                </p>

            </div>
        @endforelse

    </div>


    <!-- =========================
         WITHDRAWAL MODAL
    ========================== -->
    <livewire:components.withdrawal-modal />


    <!-- =========================
         SPIN THE WHEEL MODAL
    ========================== -->
    <livewire:components.spin-wheel-modal />

    <script>
    window.addEventListener('insufficient-withdrawal-balance', event => {
        const data = event.detail;

        Swal.fire({
            icon: 'warning',
            title: 'Insufficient Balance',
            html: `
                <div class="text-center">
                    <p class="text-sm text-gray-600 mb-4">
                        Your Cash Wallet balance is not enough to make a withdrawal.
                    </p>

                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-left">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-500">
                                Cash Wallet
                            </span>

                            <strong class="text-[#0C2C6B]">
                                ₱${data.balance}
                            </strong>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">
                                Minimum Withdrawal
                            </span>

                            <strong class="text-red-600">
                                ₱${data.minimum}
                            </strong>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 mt-4">
                        You need at least ₱${data.minimum} in your Cash Wallet to withdraw.
                    </p>
                </div>
            `,
            confirmButtonText: 'Okay',
            confirmButtonColor: '#0C2C6B'
        });
    });
</script>

</div>
