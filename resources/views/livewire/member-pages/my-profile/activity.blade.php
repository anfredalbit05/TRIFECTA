{{--
=====================================================================
 ACCOUNT ACTIVITY PAGE
=====================================================================
 Styling lives in profile.css (section 12 — reuses shared classes
 from earlier sections, e.g. .card, .account-page, .notice-icon).
 Sections:
   1. Header (icon + title)
   2. Information notice
   3. Activity list (empty state)
   4. Activity types grid
=====================================================================
--}}

<div class="account-page">

    {{-- ============================= 1. HEADER ============================= --}}
    <div class="page-header">
        <div class="page-header-icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="page-header-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <div>
            <h2 class="profile-header-title">Account Activity</h2>
            <p class="profile-header-subtitle">
                View recent activity and important changes made to your account.
            </p>
        </div>
    </div>

    {{-- ========================= 2. INFORMATION NOTICE ========================= --}}
    <div class="notice-info">
        <div class="notice-icon-wrap-info">
            <svg xmlns="http://www.w3.org/2000/svg" class="notice-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01 M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <div>
            <h3 class="notice-title-info">Activity Monitoring</h3>
            <p class="notice-text-info">
                Your account activity will be recorded here to help you monitor important changes and maintain account security.
            </p>
        </div>
    </div>

    {{-- ========================= 3. ACTIVITY LIST (EMPTY STATE) ========================= --}}
    <div class="card">

        <div class="activity-list-header">
            <div>
                <h3 class="card-title">Recent Activity</h3>
                <p class="card-subtitle">Your recent account events will be displayed below.</p>
            </div>

            <span class="activity-count-badge">No activity yet</span>
        </div>

        {{-- Empty Activity --}}
        <div class="empty-state">
            <div class="empty-state-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3 a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h3 class="empty-state-title">No account activity yet</h3>

            <p class="empty-state-text">
                Activities such as profile updates, password changes, account logins, and other important actions will appear here.
            </p>
        </div>
    </div>

    {{-- ========================= 4. ACTIVITY TYPES GRID ========================= --}}
    <div>
        <h3 class="section-title">Activities that may be recorded</h3>

        <div class="activity-type-grid">

            {{-- Login --}}
            <div class="activity-type-card">
                <div class="activity-type-icon-wrap activity-type-icon-wrap--emerald">
                    <svg xmlns="http://www.w3.org/2000/svg" class="notice-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3h4a2 2 0 012 2v14 a2 2 0 01-2 2h-4 M10 17l5-5-5-5 M15 12H3" />
                    </svg>
                </div>

                <h4 class="activity-type-title">Account Login</h4>
                <p class="activity-type-text">Successful account sign-ins may be recorded.</p>
            </div>

            {{-- Profile --}}
            <div class="activity-type-card">
                <div class="activity-type-icon-wrap activity-type-icon-wrap--blue">
                    <svg xmlns="http://www.w3.org/2000/svg" class="notice-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804 A9 9 0 1118.88 17.804 M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>

                <h4 class="activity-type-title">Profile Updates</h4>
                <p class="activity-type-text">Changes to personal information may be recorded.</p>
            </div>

            {{-- Password --}}
            <div class="activity-type-card">
                <div class="activity-type-icon-wrap activity-type-icon-wrap--amber">
                    <svg xmlns="http://www.w3.org/2000/svg" class="notice-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2 m-6 4h12 a2 2 0 002-2v-7 a2 2 0 00-2-2h-1V7 a5 5 0 00-10 0v3H6 a2 2 0 00-2 2v7 a2 2 0 002 2z" />
                    </svg>
                </div>

                <h4 class="activity-type-title">Password Changes</h4>
                <p class="activity-type-text">Password updates may be recorded for security.</p>
            </div>

            {{-- Account --}}
            <div class="activity-type-card">
                <div class="activity-type-icon-wrap activity-type-icon-wrap--violet">
                    <svg xmlns="http://www.w3.org/2000/svg" class="notice-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4 M12 22 C17.523 22 22 17.523 22 12S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" />
                    </svg>
                </div>

                <h4 class="activity-type-title">Account Changes</h4>
                <p class="activity-type-text">Important account changes may appear here.</p>
            </div>

        </div>
    </div>

</div>
