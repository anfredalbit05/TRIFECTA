<div
    class="dashboard-grid flex flex-col lg:flex-row"
    x-data="{
        showWithdrawModal: false,
        balance: {{ (float) $totalAmount }},
        minimumWithdrawalAmount: {{ (float) $minimumWithdrawalAmount }},
        withdrawAmount: @entangle('withdrawAmount').live,

        get amount() {
            let value = parseFloat(this.withdrawAmount);
            return isNaN(value) ? 0 : value;
        },

        get remainingBalance() {
            return Math.max(0, this.balance - this.amount);
        },

        get isBelowMinimum() {
            return this.withdrawAmount !== ''
                && this.amount > 0
                && this.amount < this.minimumWithdrawalAmount;
        },

        get exceedsBalance() {
            return this.amount > this.balance;
        },

        formatMoney(value) {
            return new Intl.NumberFormat('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value);
        },

        sanitizeAmount() {
            let value = this.withdrawAmount ?? '';

            // Remove everything except numbers and decimal point
            value = value.replace(/[^0-9.]/g, '');

            // Allow only one decimal point
            let parts = value.split('.');

            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            // Limit to 2 decimal places
            if (value.includes('.')) {
                let decimalParts = value.split('.');
                value = decimalParts[0] + '.' + decimalParts[1].slice(0, 2);
            }

            this.withdrawAmount = value;
        }
    }"
    x-on:open-withdraw-modal.window="showWithdrawModal = true"
    x-on:close-withdraw-modal.window="showWithdrawModal = false">

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
                @if ($isWithdrawalActive)

                <button
                    type="button"
                    wire:click="checkPendingWithdrawal"
                    class="wallet-btn-withdraw cursor-pointer"
                >
                    Withdraw Cash Wallets
                </button>

            @else

                <div
                    class="w-full px-4 py-2.5 rounded-lg
                        bg-gray-100 border border-gray-200
                        text-gray-500 text-sm font-semibold
                        mt-2
                        text-center cursor-not-allowed"
                >
                    🔒 Withdrawals are currently unavailable
                </div>

            @endif
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

   <x-withdrawal-modal
    :total-amount="$totalAmount"
    :minimum-withdrawal-amount="$minimumWithdrawalAmount"
/>

    <script>
        document.addEventListener('livewire:init', () => {

            Livewire.on('pending-withdrawal', (event) => {
                const data = event[0] ?? event;

                let details = `
                    <div style="text-align: left; line-height: 1.8;">
                        <p>
                            <strong>Amount:</strong>
                            ₱${data.amount}
                        </p>

                        <p>
                            <strong>Method:</strong>
                            ${data.method}
                        </p>
                `;

                // Show online account details only
                if (data.method !== 'Cash') {
                    details += `
                        <p>
                            <strong>Account Name:</strong>
                            ${data.accountName}
                        </p>

                        <p>
                            <strong>Account Number:</strong>
                            ${data.accountNumber}
                        </p>

                        <p>
                            <strong>Reference:</strong>
                            ${data.reference}
                        </p>
                    `;
                }

                details += `
                        <p>
                            <strong>Submitted:</strong>
                            ${data.date}
                        </p>

                        <hr style="margin: 12px 0;">

                        <p style="color: #dc2626;">
                            You cannot submit another withdrawal until this
                            request has been processed.
                        </p>
                    </div>
                `;

                Swal.fire({
                    icon: 'warning',
                    title: 'You Have a Pending Withdrawal',
                    html: details,
                    confirmButtonText: 'Okay'
                });
            });


            // SUCCESS ALERT
            Livewire.on('withdrawal-submitted', (event) => {
                const data = event[0] ?? event;

                // Close modal
                window.dispatchEvent(
                    new CustomEvent('close-withdraw-modal')
                );

                Swal.fire({
                    icon: 'success',
                    title: 'Withdrawal Submitted!',
                    html: `
                        <div style="line-height: 1.7;">
                            <p>
                                Your withdrawal request has been submitted successfully.
                            </p>

                            <p>
                                <strong>Amount:</strong>
                                ₱${data.amount}
                            </p>

                            <p style="font-size: 13px; color: #64748b;">
                                Your request is now pending for processing.
                            </p>
                        </div>
                    `,
                    confirmButtonText: 'Okay',
                    confirmButtonColor: '#0C2C6B'
                });
            });


            // ERROR ALERT
            Livewire.on('withdrawal-error', (event) => {
                const data = event[0] ?? event;

                Swal.fire({
                    icon: 'error',
                    title: 'Withdrawal Failed',
                    text: data.message ?? 'Something went wrong.'
                });
            });

        });


        // CONFIRM WITHDRAWAL
        window.confirmWithdrawal = function () {

            Swal.fire({
                icon: 'question',
                title: 'Confirm Withdrawal?',
                text: 'Are you sure you want to submit this withdrawal request?',
                showCancelButton: true,
                confirmButtonText: 'Yes, Submit Request',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#0C2C6B',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {

                if (result.isConfirmed) {

                    // Only submit if user clicks YES
                    @this.call('submitWithdrawal');

                }

                // If user clicks Cancel, do nothing.
                // SweetAlert closes but withdrawal modal remains open.
            });
        };
    </script>

</div>


