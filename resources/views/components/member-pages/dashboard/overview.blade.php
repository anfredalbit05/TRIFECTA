<div class="dashboard-grid">

    <!-- LEFT FEED COLUMN -->
    <div class="feed-column">
        @for($i = 0; $i < 6; $i++)
            <article class="member-card feed-card">

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
                                <span class="user-name">Mga pusa sa daan</span>
                                <span class="verified-icon">
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="#1877F2">
                                        <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-1.9 14.7L6 12.6l1.5-1.5 2.6 2.6 6.3-6.3 1.5 1.5-7.8 7.8z"/>
                                    </svg>
                                </span>
                                <span class="dot-separator">•</span>
                                <button class="follow-btn" type="button">Follow</button>
                            </div>
                            <div class="post-meta">
                                <span>16h</span>
                                <span class="dot-separator">•</span>
                                <span class="privacy-icon">🌐</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-actions">
                        <button class="action-btn" aria-label="More options">•••</button>
                        <button class="action-btn" aria-label="Close">✕</button>
                    </div>
                </header>

                <!-- POST BODY -->
                <div class="card-body">
                    <p class="post-text">minggo</p>

                    <div class="media-container">
                        <img
                            src="{{ asset('images/hero-bg.jpg') }}"
                            alt="Cat resting on a fruit scale"
                            class="post-image"
                        >
                    </div>
                </div>

                <!-- CARD FOOTER (STATS & REACTIONS) -->
                <footer class="card-footer">
                    <div class="post-stats">
                        <div class="reactions-count">
                            <span class="reaction-badge laugh">😆</span>
                            <span class="reaction-badge heart">❤️</span>
                            <span class="reaction-badge like">👍</span>
                            <span class="count-text">6.5K</span>
                        </div>
                        <div class="comments-shares">
                            <span>93 comments</span>
                            <span class="dot-separator">•</span>
                            <span>465 shares</span>
                        </div>
                    </div>
                </footer>

            </article>
        @endfor
    </div>


    <!-- ==========================================
        RIGHT CARD (STICKY WALLET)
    =========================================== -->
    <aside class="wallet-card">

        <div class="wallet-block">
            <span class="wallet-label">Cash Wallet</span>
            <h2 class="wallet-value">₱360.00</h2>
        </div>

        <hr>

        <div class="wallet-block">
            <span class="wallet-label">Fidelity Wallet</span>
            <h2 class="wallet-value">₱100.00</h2>
        </div>

        <hr>

        <div class="wallet-summary">
            <div>
                <span>Total Earnings</span>
                <strong>₱500.00</strong>
            </div>
            <div>
                <span>Total Withdrawals</span>
                <strong>₱0.00</strong>
            </div>
        </div>

        <div class="membership-card">
            🏅 Standard Membership
        </div>

        <div class="distribution-card">
            <div class="distribution-icon">📊</div>
            <div>
                <h4>Earnings Distribution</h4>
                <p>Categorized income based on transaction source.</p>
            </div>
        </div>

        <div class="wallet-footer">
            <span>✔ UNILEVEL L1</span>
            <strong>₱360.00</strong>
        </div>

    </aside>

</div>
