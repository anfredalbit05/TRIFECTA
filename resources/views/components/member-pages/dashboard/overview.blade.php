<div class="dashboard-grid">

   <!-- LEFT CONTENT -->
<section class="member-card">

    <!-- NEWS FEED -->
    <section class="news-feed-card">


        <!-- NEWS HEADER -->
        <header class="news-feed-header">

            <div class="news-feed-title">

                <h3>
                    📰 News & Updates
                </h3>

                <p>
                    Latest announcements from Dayongan
                </p>

            </div>


            <span class="news-feed-live">
                ● Live
            </span>


        </header>


        <!-- TIMELINE CONTENT -->
<!-- TIMELINE CONTENT -->
<div class="news-feed-body">

    <div class="news-feed-scroll">

        @for($i = 0; $i < 6; $i++)

            <article class="timeline-item">


                <div class="timeline-dot"></div>


                <div class="timeline-content">

                    <time class="timeline-date">
                        April 22, 2026
                    </time>


                    <img
                        src="{{ asset('images/hero-bg.jpg') }}"
                        alt="Announcement Image"
                        class="timeline-image"
                    >


                    <span class="timeline-badge">
                        Member Advisory
                    </span>


                    <h4>
                        Important Advisory:
                        Keep Your Beneficiary Information Updated
                    </h4>


                    <p>
                        Ensure all declared dependents are updated
                        before your annual renewal to avoid claim delays.
                    </p>


                    <a href="#">
                        Read More →
                    </a>

                </div>


            </article>


        @endfor

    </div>

</div>


    </section>


</section>



    <!-- ==========================================
        RIGHT CARD
    =========================================== -->

    <aside class="wallet-card">

        <div class="wallet-block">

            <span class="wallet-label">
                Cash Wallet
            </span>

            <h2 class="wallet-value">
                ₱360.00
            </h2>

        </div>

        <hr>

        <div class="wallet-block">

            <span class="wallet-label">
                Fidelity Wallet
            </span>

            <h2 class="wallet-value">
                ₱100.00
            </h2>

        </div>

        <hr>

        <div class="wallet-summary">

            <div>

                <span>
                    Total Earnings
                </span>

                <strong>
                    ₱500.00
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

        <div class="membership-card">

            🏅 Standard Membership

        </div>

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

        <div class="wallet-footer">

            <span>
                ✔ UNILEVEL L1
            </span>

            <strong>
                ₱360.00
            </strong>

        </div>

    </aside>

</div>
