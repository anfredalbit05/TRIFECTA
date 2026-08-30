<div class="dashboard-grid">

    <!-- =========================
         RIGHT COLUMN / WALLET
    ========================== -->
    <div class="dashboard-sidebar order-1 lg:order-2">

        <!-- REFRESH BUTTON HEADER -->
        <div
            class="flex items-center justify-between bg-white p-4 rounded-xl shadow-sm border border-gray-100"
        >
            <div>
                <h3 class="font-bold text-gray-800 text-base">
                    Dashboard Actions
                </h3>

                <p class="text-xs text-gray-500">
                    Sync latest data feeds.
                </p>
            </div>

            <button
                type="button"
                wire:click="refreshDashboard"
                wire:loading.attr="disabled"
                title="Refresh Dashboard"
                class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded-lg shadow-sm transition cursor-pointer"
            >
                <span
                    wire:loading.remove
                    wire:target="refreshDashboard"
                >
                    🔄
                </span>

                <span
                    wire:loading
                    wire:target="refreshDashboard"
                    class="animate-spin"
                >
                    ⏳
                </span>

                <span>
                    Refresh
                </span>
            </button>
        </div>


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

                <button
                    type="button"
                    wire:click="openWithdrawalModal"
                    class="wallet-btn-withdraw cursor-pointer"
                >
                    Withdraw Cash Wallet
                </button>

                @else

                    <div
                        class="w-full px-4 py-2.5 rounded-lg
                        bg-gray-100 border border-gray-200
                        text-gray-500 text-sm font-semibold
                        mt-6 text-center cursor-not-allowed"
                    >
                        🔒 Withdrawals are currently unavailable
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
                        ₱0.00
                    </strong>
                </div>

            </div>


            <!-- EARNINGS DISTRIBUTION -->
            <div class="distribution-card">

                <div class="distribution-icon">
                    📊
                </div>

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

            <article
                class="member-card feed-card"
                wire:key="news-{{ $news->ID }}"
            >

                <!-- CARD HEADER -->
                <header class="card-header">

                    <div class="user-info">

                        <img
                            src="{{ asset('images/user.png') }}"
                            alt="Profile Picture"
                            class="user-avatar"
                        >


                        <div class="user-details">

                            <!-- USER NAME -->
                            <div class="user-name-row">

                                <span class="user-name">
                                    {{
                                        $news->user
                                            ? $news->user->firstname . ' ' . $news->user->lastname
                                            : 'TRUELIFE'
                                    }}
                                </span>


                                <!-- VERIFIED ICON -->
                                <span class="verified-icon">

                                    <svg
                                        viewBox="0 0 24 24"
                                        width="14"
                                        height="14"
                                        fill="#1877F2"
                                    >
                                        <path
                                            d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-1.9 14.7L6 12.6l1.5-1.5 2.6 2.6 6.3-6.3 1.5 1.5-7.8 7.8z"
                                        />
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

                            <button
                                class="action-btn"
                                type="button"
                                aria-label="More options"
                            >
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

</div>
