<!-- =========================================================
     TRIFECTA — FLOATING COMMAND BAR
========================================================= -->

@php
    $joinAllow = \Illuminate\Support\Facades\DB::table('settings')
        ->where('name', 'join_allow')
        ->value('value');

    $joinAllow = filter_var($joinAllow, FILTER_VALIDATE_BOOLEAN);

    $publicLinks = [
        ['label' => 'Home',          'route' => 'home',         'match' => '/'],
        ['label' => 'Our Story',     'route' => 'about',        'match' => 'about*'],
        ['label' => 'Board Members', 'route' => 'team',         'match' => 'team*'],
        ['label' => 'Membership',    'route' => 'how-it-works', 'match' => 'how-it-works*'],
        ['label' => 'Member Stories','route' => 'testimonials', 'match' => 'testimonials*'],
        ['label' => 'Announcement',  'route' => 'news',         'match' => 'news*'],
        ['label' => 'Reach Us',      'route' => 'contact',      'match' => 'contact*'],
    ];
@endphp

<nav class="tf-nav">

    <div class="tf-nav-inner">

        {{-- =====================================================
             BRAND
        ====================================================== --}}
        <a href="{{ route('home') }}" class="tf-nav-brand">
            <span class="tf-nav-logo">
                <img src="{{ asset('images/truelife-logo.png') }}" alt="TRIFECTA">
            </span>

            <span class="tf-nav-wordmark">
                <strong>TRIFECTA</strong>
                <span>Health · Wellness · Protection</span>
            </span>
        </a>


        {{-- =====================================================
             DESKTOP LINKS
        ====================================================== --}}
        <div class="tf-nav-links">
            @foreach ($publicLinks as $link)
                <a href="{{ route($link['route']) }}"
                   class="tf-nav-link {{ request()->is($link['match']) ? 'is-active' : '' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>


        {{-- =====================================================
             ACTIONS
        ====================================================== --}}
        <div class="tf-nav-actions">

            @guest

                @if ($joinAllow)
                    <button type="button"
                            onclick="Livewire.dispatch('open-application-modal')"
                            class="tf-nav-join">
                        <span>Join Now</span>
                    </button>
                @else
                    <button type="button" disabled class="tf-nav-join">
                        <span>Join Unavailable</span>
                    </button>
                @endif

                <button type="button"
                        onclick="Livewire.dispatch('open-login-modal')"
                        class="tf-nav-login">
                    Login
                </button>

            @else

                @php
                    $user = Auth::user();

                    $displayName = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));

                    if (empty($displayName)) {
                        $displayName = $user->username ?? 'User';
                    }

                    $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=092F78&color=fff';
                @endphp

                <div x-data="{ profileOpen: false }"
                     @click.away="profileOpen = false"
                     @keydown.escape.window="profileOpen = false"
                     class="relative">

                    <button type="button"
                            @click="profileOpen = !profileOpen"
                            :aria-expanded="profileOpen"
                            aria-label="Open profile menu"
                            class="tf-nav-user">

                        <img src="{{ $avatarUrl }}" alt="{{ $displayName }}" class="tf-nav-avatar">

                        <span class="tf-nav-user-name">{{ $displayName }}</span>

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor"
                             class="h-3.5 w-3.5 shrink-0 text-[var(--color-primary)]/70 transition-transform duration-200"
                             :class="{ 'rotate-180': profileOpen }">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="profileOpen"
                         x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                         class="tf-nav-pop">

                        <div class="tf-nav-pop-head">
                            <img src="{{ $avatarUrl }}" alt="{{ $displayName }}" class="tf-nav-avatar">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-slate-800">{{ $displayName }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $user->username ?? 'Username' }}</p>
                            </div>
                        </div>

                        <a href="{{ route('dashboard') }}" @click="profileOpen = false">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 10.5L12 3l9 7.5M5 9.5V21h14V9.5M9 21v-6h6v6" />
                            </svg>
                            <span>My Dashboard</span>
                        </a>

                        <a href="{{ route('my-profile') }}" @click="profileOpen = false">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M20 21a8 8 0 0 0-16 0M12 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" />
                            </svg>
                            <span>Account Settings</span>
                        </a>

                        <form id="navbarLogoutForm" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="button"
                                    class="is-danger"
                                    @click="profileOpen = false"
                                    onclick="showLogoutConfirmation('navbarLogoutForm')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12H3m0 0l4-4m-4 4l4 4m5-9V5a2 2 0 012-2h5a2 2 0 012 2v14a2 2 0 01-2 2h-5a2 2 0 01-2-2v-2" />
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>

                    </div>

                </div>

            @endguest

        </div>

    </div>

</nav>


{{-- =========================================================
     MOBILE DOCK
========================================================= --}}

<div class="tf-dock">
    <div class="tf-dock-inner">

        @php
            $dockLinks = [
                ['label' => 'Home',     'route' => 'home',         'match' => '/',
                 'icon' => 'M3 11l9-8 9 8M5 10v10h14V10'],
                ['label' => 'Story',    'route' => 'about',        'match' => 'about*',
                 'icon' => 'M13 16h-1v-4h-1m1-8a9 9 0 110 18 9 9 0 010-18zm0 4h.01'],
                ['label' => 'People',   'route' => 'team',         'match' => 'team*',
                 'icon' => 'M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-8a4 4 0 110 8 4 4 0 010-8z'],
                ['label' => 'Join',     'route' => 'how-it-works', 'match' => 'how-it-works*',
                 'icon' => 'M12 5v14M5 12h14'],
                ['label' => 'Stories',  'route' => 'testimonials', 'match' => 'testimonials*',
                 'icon' => 'M8 10h8M8 14h5m-8 6l3-3h7a5 5 0 005-5V7a5 5 0 00-5-5H6a5 5 0 00-5 5v5a5 5 0 005 5v3z'],
                ['label' => 'Updates',  'route' => 'news',         'match' => 'news*',
                 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2zM7 8h10M7 12h10M7 16h6'],
                ['label' => 'Reach Us', 'route' => 'contact',      'match' => 'contact*',
                 'icon' => 'M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
            ];
        @endphp

        @foreach ($dockLinks as $link)
            <a href="{{ route($link['route']) }}"
               class="tf-dock-link {{ request()->is($link['match']) ? 'is-active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $link['icon'] }}" />
                </svg>
                <span>{{ $link['label'] }}</span>
            </a>
        @endforeach

    </div>
</div>


@vite('resources/js/components/navbar.js')
