@php
    use Illuminate\Support\Str;

    $user = auth()->user();
    $isAdmin = $user && !in_array($user->RoleID, [8, 9]); // 8 = Satellite, 9 = Satellite Admin

    /*
    | Satellite nav visibility:
    | - Satellite users (RoleID 8) always see it.
    | - Other members see it once at least ONE satellite member used
    |   their referralCode as the upline code (a live DB check, cached
    |   for the request).
    */
    // $isSatelliteMember = $user?->RoleID == 8
    //     || ($user
    //         && $user->RoleID != 8
    //         && filled($user->referralCode)
    //         && \App\Models\Activation::where('upline_code', $user->referralCode)
    //             ->whereHas('user', fn ($q) => $q->where('RoleID', 8))
    //             ->exists());

    $defaultAvatar = asset('images/profiles/default-avatar.png');

    $avatarSrc = $user
        ? route('profile.picture') . '?v=' . ($user->updated_at?->timestamp ?? time())
        : $defaultAvatar;
    /*
    | Pending transaction counts (admin sidebar badges). Only queried for
    | admins — members never see these two links.
    */
    // $pendingActivations = $isAdmin ? \App\Models\Activation::where('status', 'Pending')->count() : 0;
    // $pendingWithdrawals = $isAdmin ? \App\Models\Withdrawal::where('status', 'Pending')->count() : 0;
    // $pendingTransactions = $pendingActivations + $pendingWithdrawals;

    /* Masterlist badge: satellite members awaiting activation review. */
    // $pendingSatelliteActivations = $isAdmin
    //     ? \App\Models\Activation::where('status', 'Pending')
    //         ->whereHas('user', fn ($q) => $q->where('RoleID', 8))
    //         ->count()
    //     : 0;

    $roleLine = match (true) {
        $user?->RoleID == 8 => ($user?->status ?? 'Inactive') . ' Member',
        // $user?->RoleID == 8 => ($user?->status ?? 'Inactive') . ' Satellite Member',
        (bool) $user        => ($user->status ?? 'Inactive') . ' · ' . ($user->position ?? '--'),
        default             => '',
    };
@endphp

<div x-data x-on:profile-updated.window="window.location.reload()" class="sidebar-wrapper">

    {{-- =========================================================
         MOBILE TOP BAR
    ========================================================== --}}
    <header class="tf-topbar">
        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}" class="tf-topbar-logo">
                <img src="{{ asset('images/truelife-logo.png') }}" alt="TRIFECTA">
            </a>
            <div class="leading-tight">
                <b class="block text-[12px] font-black tracking-[.16em] text-[var(--tf-ink)]">TRIFECTA</b>
                <small class="block text-[7.5px] font-extrabold uppercase tracking-[.16em] text-[var(--tf-ink-mute)]">
                    {{ $isAdmin ? 'Operations' : 'Member Portal' }}
                </small>
            </div>
        </div>

        <a href="{{ route('my-profile') }}" class="tf-topbar-user">
            <div class="text-right">
                <b>{{ $user?->firstname ?? 'Member' }} {{ $user?->lastname ?? '' }}</b>
                <small>{{ $user?->branch?->branchname ?? 'Member' }}</small>
            </div>
            <img
                src="{{ $avatarSrc }}"
                alt="Profile picture"
                onerror="this.onerror=null;this.src='{{ $defaultAvatar }}';"
            >
        </a>
    </header>


    {{-- =========================================================
         DESKTOP RAIL
    ========================================================== --}}
    <aside class="tf-side">

        <a href="{{ route('home') }}" class="tf-side-brand">
            <span class="tf-nav-logo">
                <img src="{{ asset('images/truelife-logo.png') }}" alt="TRIFECTA">
            </span>
            <span>
                <strong>TRIFECTA</strong>
                <small>Health · Wellness · Protection</small>
            </span>
        </a>

        <div class="tf-side-scroll">

            {{-- PROFILE CARD --}}
            <div class="tf-side-profile">
                <div class="tf-side-avatar">
                    <img src="{{ $avatarSrc }}" alt="Profile picture">
                </div>

                <p class="tf-side-name">
                    {{ $user?->firstname ?? 'Member' }} {{ $user?->lastname ?? '' }}
                </p>

                @if ($roleLine)
                    <span class="tf-side-status">{{ $roleLine }}</span>
                @endif

                <span class="tf-side-branch">{{ $user?->branch?->branchname ?? 'TRIFECTA' }}</span>
            </div>


            {{-- WORKSPACE --}}
            <div class="tf-side-group">
                <div class="tf-side-label">Workspace</div>

                <a href="{{ route('dashboard') }}"
                   class="tf-side-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 10.5L12 3l9 7.5M5 9.5V21h14V9.5M9 21v-6h6v6" />
                    </svg>
                    <span>Command Center</span>
                </a>

                <a href="{{ route('genealogy-tree') }}"
                   class="tf-side-link {{ request()->routeIs('genealogy-tree') ? 'is-active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 4v16M12 8H7a3 3 0 0 1-3-3M12 8h5a3 3 0 0 0 3-3M12 16H7a3 3 0 0 0-3 3M12 16h5a3 3 0 0 1 3 3" />
                        <circle cx="12" cy="4" r="2" />
                        <circle cx="4" cy="5" r="2" />
                        <circle cx="20" cy="5" r="2" />
                        <circle cx="4" cy="19" r="2" />
                        <circle cx="20" cy="19" r="2" />
                    </svg>
                    <span>Genealogy Network</span>
                </a>
            </div>


            {{-- ADMINISTRATION --}}
            @if ($isAdmin)
                <div class="tf-side-group">
                    <div class="tf-side-label">Administration</div>

                    <a href="{{ route('masterlist') }}"
                       class="tf-side-link {{ request()->routeIs('masterlist') ? 'is-active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                        </svg>
                        <span>Member Registry</span>
                        {{-- @if ($pendingSatelliteActivations > 0)
                            <span class="tf-side-badge"
                                  title="{{ $pendingSatelliteActivations }} satellite activation(s) pending review">
                                {{ $pendingSatelliteActivations }}
                            </span>
                        @endif --}}
                    </a>

                    <a href="{{ route('transaction-list') }}"
                       class="tf-side-link {{ request()->routeIs('transaction-list') ? 'is-active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18M3 12h18M3 18h18M7 3v2M12 3v2M17 3v2" />
                        </svg>
                        <span>Payment Desk</span>
                        {{-- @if ($pendingTransactions > 0)
                            <span class="tf-side-badge"
                                  title="{{ $pendingActivations }} pending activation(s), {{ $pendingWithdrawals }} pending withdrawal(s)">
                                {{ $pendingTransactions }}
                            </span>
                        @endif --}}
                    </a>

                    <a href="{{ route('post') }}"
                       class="tf-side-link {{ request()->routeIs('post') ? 'is-active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4 11.5-11.5Z" />
                        </svg>
                        <span>Publishing Studio</span>
                    </a>

                    <a href="{{ route('report-settings') }}"
                       class="tf-side-link {{ request()->routeIs('report-settings') ? 'is-active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19V5M4 19h16M8 16v-5M12 16V8M16 16v-3M20 16V6" />
                        </svg>
                        <span>Operations Desk</span>
                    </a>
                </div>
            @endif


            {{-- PERSONAL --}}
            <div class="tf-side-group">
                <div class="tf-side-label">Personal</div>

                <a href="{{ route('my-profile') }}"
                   class="tf-side-link {{ request()->routeIs('my-profile') ? 'is-active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21a8 8 0 0 0-16 0M12 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" />
                    </svg>
                    <span>Account Settings</span>
                </a>

                {{-- @if ($isSatelliteMember)
                    <a href="{{ route('satellite.portal') }}"
                       class="tf-side-link {{ request()->routeIs('satellite.portal') ? 'is-active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3" />
                            <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-1.42 1.42-.06-.06A1.7 1.7 0 0 0 16.44 18a1.7 1.7 0 0 0-1.03 1.56V19.7h-2.02v-.14A1.7 1.7 0 0 0 12.36 18a1.7 1.7 0 0 0-1.88.34l-.06.06L9 16.98l.06-.06A1.7 1.7 0 0 0 9.4 15a1.7 1.7 0 0 0-1.56-1.03H7.7v-2.02h.14A1.7 1.7 0 0 0 9.4 10.92a1.7 1.7 0 0 0-.34-1.88L9 8.98 10.42 7.56l.06.06a1.7 1.7 0 0 0 1.88.34A1.7 1.7 0 0 0 13.39 6.4v-.14h2.02v.14A1.7 1.7 0 0 0 16.44 8a1.7 1.7 0 0 0 1.88-.34l.06-.06 1.42 1.42-.06.06A1.7 1.7 0 0 0 19.4 10.92a1.7 1.7 0 0 0 1.56 1.03h.14v2.02h-.14A1.7 1.7 0 0 0 19.4 15Z" />
                        </svg>
                        <span>Satellite Hub</span>
                    </a>
                @endif --}}
            </div>

        </div>

        <div class="tf-side-foot">
            <livewire:logout-button />
        </div>

    </aside>


    {{-- =========================================================
         MOBILE DOCK
    ========================================================== --}}
    <nav class="tf-dock" aria-label="Member navigation">
        <div class="tf-dock-inner">

            <a href="{{ route('dashboard') }}"
               class="tf-dock-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 10.5L12 3l9 7.5M5 9.5V21h14V9.5M9 21v-6h6v6" />
                </svg>
                <span>Home</span>
            </a>

            <a href="{{ route('genealogy-tree') }}"
               class="tf-dock-link {{ request()->routeIs('genealogy-tree') ? 'is-active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 4v16M12 8H7a3 3 0 0 1-3-3M12 8h5a3 3 0 0 0 3-3M12 16H7a3 3 0 0 0-3 3M12 16h5a3 3 0 0 1 3 3" />
                </svg>
                <span>Network</span>
            </a>

            @if ($isAdmin)
                <a href="{{ route('masterlist') }}"
                   class="tf-dock-link {{ request()->routeIs('masterlist') ? 'is-active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                    </svg>
                    <span>Members</span>
                </a>

                <a href="{{ route('transaction-list') }}"
                   class="tf-dock-link {{ request()->routeIs('transaction-list') ? 'is-active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18M3 12h18M3 18h18M7 3v2M12 3v2M17 3v2" />
                    </svg>
                    <span>Payments</span>
                    {{-- @if ($pendingTransactions > 0)
                        <span class="tf-side-badge">{{ $pendingTransactions }}</span>
                    @endif --}}
                </a>

                <a href="{{ route('post') }}"
                   class="tf-dock-link {{ request()->routeIs('post') ? 'is-active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4 11.5-11.5Z" />
                    </svg>
                    <span>Studio</span>
                </a>

                <a href="{{ route('report-settings') }}"
                   class="tf-dock-link {{ request()->routeIs('report-settings') ? 'is-active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19V5M4 19h16M8 16v-5M12 16V8M16 16v-3M20 16V6" />
                    </svg>
                    <span>Desk</span>
                </a>
            @endif

            <a href="{{ route('my-profile') }}"
               class="tf-dock-link {{ request()->routeIs('my-profile') ? 'is-active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21a8 8 0 0 0-16 0M12 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" />
                </svg>
                <span>Account</span>
            </a>

            {{-- @if ($isSatelliteMember)
                <a href="{{ route('satellite.portal') }}"
                   class="tf-dock-link {{ request()->routeIs('satellite.portal') ? 'is-active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3" />
                        <path d="M19 5l-2 2m-10 0L5 5m0 14 2-2m10 0 2 2M12 2v3m0 14v3M2 12h3m14 0h3" />
                    </svg>
                    <span>Satellite</span>
                </a>
            @endif --}}

        </div>
    </nav>

</div>
