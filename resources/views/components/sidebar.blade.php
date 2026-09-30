@php
    use Illuminate\Support\Str;

    $user = auth()->user();
    $isAdmin = $user && !in_array($user->RoleID, [4, 8]);

    /*
    | Satellite nav visibility:
    | - Satellite users (RoleID 8) always see it.
    | - Other members see it once at least ONE satellite member used
    |   their referralCode as the upline code (a live DB check, cached
    |   for the request).
    */
    $isSatelliteMember = $user?->RoleID == 8
        || ($user
            && $user->RoleID != 8
            && filled($user->referralCode)
            && \App\Models\Activation::where('upline_code', $user->referralCode)
                ->whereHas('user', fn ($q) => $q->where('RoleID', 8))
                ->exists());

    $profilePic = $user?->profilepic;
    $defaultAvatar = 'images/profiles/default-avatar.png';

    /*
    | Section visibility follows the global module system
    | (App\Support\Modules): a section link shows only when the signed-in
    | role holds can_read on at least one module inside that section —
    | on top of the existing isAdmin role gate.
    */
    $readableModuleIds = $user
        ? collect([
            \App\Support\Modules::ADMIN_USERS,
            \App\Support\Modules::MEMBERS,
            \App\Support\Modules::SATELLITE_MEMBERS,
            \App\Support\Modules::BRANCHES,
            \App\Support\Modules::BENEFITS,
            \App\Support\Modules::FAQS,
            \App\Support\Modules::INQUIRIES,
            \App\Support\Modules::TXN_WITHDRAWALS,
            \App\Support\Modules::TXN_ACTIVATIONS,
            \App\Support\Modules::POST_NEWS,
            \App\Support\Modules::POST_TESTIMONIALS,
            \App\Support\Modules::RS_GENERAL,
            \App\Support\Modules::RS_USERS_REPORTS,
            \App\Support\Modules::RS_LEDGERS,
            \App\Support\Modules::RS_PANGKABUHAYAN,
        ])->filter(fn ($id) => \App\Support\Modules::can($id, 'read'))->all()
        : [];

    $canReadMasterlist = count(array_intersect($readableModuleIds, [
        \App\Support\Modules::ADMIN_USERS,
        \App\Support\Modules::MEMBERS,
        \App\Support\Modules::SATELLITE_MEMBERS,
        \App\Support\Modules::BRANCHES,
        \App\Support\Modules::BENEFITS,
        \App\Support\Modules::FAQS,
        \App\Support\Modules::INQUIRIES,
    ])) > 0;

    $canReadTransactions = count(array_intersect($readableModuleIds, [
        \App\Support\Modules::TXN_WITHDRAWALS,
        \App\Support\Modules::TXN_ACTIVATIONS,
    ])) > 0;

    $canReadPost = count(array_intersect($readableModuleIds, [
        \App\Support\Modules::POST_NEWS,
        \App\Support\Modules::POST_TESTIMONIALS,
    ])) > 0;

    $canReadReports = count(array_intersect($readableModuleIds, [
        \App\Support\Modules::RS_GENERAL,
        \App\Support\Modules::RS_USERS_REPORTS,
        \App\Support\Modules::RS_LEDGERS,
        \App\Support\Modules::RS_PANGKABUHAYAN,
    ])) > 0;

    /*
    | Pending transaction counts (admin sidebar badges). Only queried for
    | admins — members never see these two links.
    */
    $pendingActivations = $isAdmin ? \App\Models\Activation::where('status', 'Pending')->count() : 0;
    $pendingWithdrawals = $isAdmin ? \App\Models\Withdrawal::where('status', 'Pending')->count() : 0;
    $pendingTransactions = $pendingActivations + $pendingWithdrawals;

    /* Masterlist badge: satellite members awaiting activation review. */
    $pendingSatelliteActivations = $isAdmin
        ? \App\Models\Activation::where('status', 'Pending')
            ->whereHas('user', fn ($q) => $q->where('RoleID', 8))
            ->count()
        : 0;
@endphp

<div x-data x-on:profile-updated.window="window.location.reload()" class="sidebar-wrapper">

    {{-- MOBILE HEADER --}}
    <header class="mobile-header">
        <a href="{{ route('my-profile') }}" class="mobile-profile">
            <div class="mobile-profile-image-box">
                @if (!$profilePic || $profilePic === $defaultAvatar)
                    <img src="{{ asset($defaultAvatar) }}" class="mobile-profile-image" alt="Profile picture">
                @else
                    <img
                        src="{{ Str::startsWith($profilePic, 'storage/') ? asset($profilePic) : asset('storage/' . $profilePic) }}"
                        class="mobile-profile-image"
                        alt="Profile picture">
                @endif
            </div>

            <div class="mobile-profile-info">
                <span class="mobile-profile-name">
                    {{ $user?->firstname ?? 'Member' }} {{ $user?->lastname ?? '' }}
                </span>
                <span class="mobile-profile-status">
                    {{ $user?->status ?? 'Inactive' }} Member
                </span>
            </div>
        </a>

        <a href="{{ route('home') }}" class="mobile-truelife">
            <img src="{{ asset('images/truelife-logo.png') }}" class="mobile-truelife-logo" alt="TRUELIFE Logo">
        </a>
    </header>

    {{-- DESKTOP SIDEBAR --}}
    <aside class="sidebar">

        <a href="{{ route('home') }}" class="sidebar-logo">
            <img src="{{ asset('images/truelife-logo.png') }}" class="logo-image" alt="TRUELIFE Logo">
        </a>

        <div class="sidebar-content">
            <div class="profile-card sidebar-scroll">

                {{-- PROFILE --}}
                <div class="profile-section">
                    <div class="profile-image-box">
                        @if (!$profilePic || $profilePic === $defaultAvatar)
                            <img src="{{ asset($defaultAvatar) }}" class="profile-image" alt="Default profile picture">
                        @else
                            <img
                                src="{{ Str::startsWith($profilePic, 'storage/') ? asset($profilePic) : asset('storage/' . $profilePic) }}"
                                class="profile-image"
                                alt="Profile picture">
                        @endif
                    </div>

                    <h2 class="profile-name">
                        <span>{{ $user?->firstname ?? 'Member' }}</span>
                        <span>{{ $user?->lastname ?? '' }}</span>
                    </h2>

                    <p class="profile-status">
                        {{ $user?->status ?? 'Inactive' }} Member
                    </p>
                </div>

                {{-- DESKTOP MENU --}}
                <nav class="menu-list">

                    <a href="{{ route('dashboard') }}"
                       class="menu-item {{ request()->routeIs('dashboard') ? 'active-menu' : '' }}">
                        <svg class="menu-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M3 10.5L12 3l9 7.5M5 9.5V21h14V9.5M9 21v-6h6v6"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('genealogy-tree') }}"
                       class="menu-item {{ request()->routeIs('genealogy-tree') ? 'active-menu' : '' }}">
                        <svg class="menu-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M12 4v16M12 8H7a3 3 0 0 1-3-3M12 8h5a3 3 0 0 0 3-3M12 16H7a3 3 0 0 0-3 3M12 16h5a3 3 0 0 1 3 3"/>
                            <circle cx="12" cy="4" r="2"/>
                            <circle cx="4" cy="5" r="2"/>
                            <circle cx="20" cy="5" r="2"/>
                            <circle cx="4" cy="19" r="2"/>
                            <circle cx="20" cy="19" r="2"/>
                        </svg>
                        <span>Genealogy</span>
                    </a>

                    @if ($isAdmin && $canReadMasterlist)

                        <a href="{{ route('masterlist') }}"
                           class="menu-item {{ request()->routeIs('masterlist') ? 'active-menu' : '' }}">
                            <svg class="menu-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                            </svg>
                            <span>Masterlist</span>
                            @if ($pendingSatelliteActivations > 0)
                                <span class="sidebar-badge" title="{{ $pendingSatelliteActivations }} satellite activation(s) pending review">{{ $pendingSatelliteActivations }}</span>
                            @endif
                        </a>

                        @if ($canReadTransactions)
                        <a href="{{ route('transaction-list') }}"
                           class="menu-item {{ request()->routeIs('transaction-list') ? 'active-menu' : '' }}">
                            <svg class="menu-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M3 6h18M3 12h18M3 18h18M7 3v2M12 3v2M17 3v2"/>
                            </svg>
                            <span>Transaction List</span>
                            @if ($pendingTransactions > 0)
                                <span class="sidebar-badge" title="{{ $pendingActivations }} pending activation(s), {{ $pendingWithdrawals }} pending withdrawal(s)">{{ $pendingTransactions }}</span>
                            @endif
                        </a>
                        @endif

                        @if ($canReadPost)
                        <a href="{{ route('post') }}"
                           class="menu-item {{ request()->routeIs('post') ? 'active-menu' : '' }}">
                            <svg class="menu-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4 11.5-11.5Z"/>
                            </svg>
                            <span>Post</span>
                        </a>
                        @endif

                        @if ($canReadReports)
                        <a href="{{ route('report-settings') }}"
                           class="menu-item {{ request()->routeIs('report-settings') ? 'active-menu' : '' }}">
                            <svg class="menu-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M4 19V5M4 19h16M8 16v-5M12 16V8M16 16v-3M20 16V6"/>
                            </svg>
                            <span>Report & Settings</span>
                        </a>
                        @endif



                    @endif

                    <a href="{{ route('my-profile') }}"
                       class="menu-item {{ request()->routeIs('my-profile') ? 'active-menu' : '' }}">
                        <svg class="menu-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M20 21a8 8 0 0 0-16 0M12 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z"/>
                        </svg>
                        <span>My Profile</span>
                    </a>

                    @if ($isSatelliteMember)
                        <a href="{{ route('satellite.portal') }}"
                           class="menu-item {{ request()->routeIs('satellite.portal') ? 'active-menu' : '' }}">
                            <svg class="menu-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-1.42 1.42-.06-.06A1.7 1.7 0 0 0 16.44 18a1.7 1.7 0 0 0-1.03 1.56V19.7h-2.02v-.14A1.7 1.7 0 0 0 12.36 18a1.7 1.7 0 0 0-1.88.34l-.06.06L9 16.98l.06-.06A1.7 1.7 0 0 0 9.4 15a1.7 1.7 0 0 0-1.56-1.03H7.7v-2.02h.14A1.7 1.7 0 0 0 9.4 10.92a1.7 1.7 0 0 0-.34-1.88L9 8.98 10.42 7.56l.06.06a1.7 1.7 0 0 0 1.88.34A1.7 1.7 0 0 0 13.39 6.4v-.14h2.02v.14A1.7 1.7 0 0 0 16.44 8a1.7 1.7 0 0 0 1.88-.34l.06-.06 1.42 1.42-.06.06A1.7 1.7 0 0 0 19.4 10.92a1.7 1.7 0 0 0 1.56 1.03h.14v2.02h-.14A1.7 1.7 0 0 0 19.4 15Z"/>
                            </svg>
                            <span>Satellite</span>
                        </a>
                    @endif

                </nav>
            </div>
        </div>

        <div class="logout-area">
            <livewire:logout-button />
        </div>
    </aside>

    {{-- MOBILE BOTTOM NAVIGATION --}}
    <nav class="member-mobile-nav" aria-label="Member navigation">

        <a href="{{ route('dashboard') }}"
           class="member-mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg class="member-mobile-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M3 10.5L12 3l9 7.5M5 9.5V21h14V9.5M9 21v-6h6v6"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('genealogy-tree') }}"
           class="member-mobile-nav-item {{ request()->routeIs('genealogy-tree') ? 'active' : '' }}">
            <svg class="member-mobile-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M12 4v16M12 8H7a3 3 0 0 1-3-3M12 8h5a3 3 0 0 0 3-3M12 16H7a3 3 0 0 0-3 3M12 16h5a3 3 0 0 1 3 3"/>
            </svg>
            <span>Genealogy</span>
        </a>

        @if ($isAdmin && $canReadMasterlist)

            <a href="{{ route('masterlist') }}"
               class="member-mobile-nav-item {{ request()->routeIs('masterlist') ? 'active' : '' }}">
                <svg class="member-mobile-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                </svg>
                <span>Masterlist</span>
            </a>

            @if ($canReadTransactions)
            <a href="{{ route('transaction-list') }}"
               class="member-mobile-nav-item {{ request()->routeIs('transaction-list') ? 'active' : '' }}">
                <span class="member-mobile-nav-badge-anchor">
                    <svg class="member-mobile-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M3 6h18M3 12h18M3 18h18M7 3v2M12 3v2M17 3v2"/>
                    </svg>
                    @if ($pendingTransactions > 0)
                        <span class="sidebar-badge sidebar-badge-mobile">{{ $pendingTransactions }}</span>
                    @endif
                </span>
                <span>Transactions</span>
            </a>
            @endif

            @if ($canReadPost)
            <a href="{{ route('post') }}"
               class="member-mobile-nav-item {{ request()->routeIs('post') ? 'active' : '' }}">
                <svg class="member-mobile-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4 11.5-11.5Z"/>
                </svg>
                <span>Post</span>
            </a>
            @endif

            @if ($canReadReports)
            <a href="{{ route('report-settings') }}"
               class="member-mobile-nav-item {{ request()->routeIs('report-settings') ? 'active' : '' }}">
                <svg class="member-mobile-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M4 19V5M4 19h16M8 16v-5M12 16V8M16 16v-3M20 16V6"/>
                </svg>
                <span>Reports</span>
            </a>
            @endif



        @endif

        <a href="{{ route('my-profile') }}"
           class="member-mobile-nav-item {{ request()->routeIs('my-profile') ? 'active' : '' }}">
            <svg class="member-mobile-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M20 21a8 8 0 0 0-16 0M12 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z"/>
            </svg>
            <span>Profile</span>
        </a>

        @if ($isSatelliteMember)
            <a href="{{ route('satellite.portal') }}"
               class="member-mobile-nav-item {{ request()->routeIs('satellite.portal') ? 'active' : '' }}">
                <svg class="member-mobile-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M19 5l-2 2m-10 0L5 5m0 14 2-2m10 0 2 2M12 2v3m0 14v3M2 12h3m14 0h3"/>
                </svg>
                <span>Satellite</span>
            </a>
        @endif

        {{-- LOGOUT --}}
        <div class="member-mobile-logout">
            <livewire:logout-button />
        </div>

    </nav>
</div>

