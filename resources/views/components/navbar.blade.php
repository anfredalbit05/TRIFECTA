<!-- =========================================================
     TRUELIFE NAVBAR
========================================================= -->

@php
    $joinAllow = \Illuminate\Support\Facades\DB::table('settings')
        ->where('BranchID', 0)
        ->where('name', 'join_allow')
        ->value('value');

    $joinAllow = filter_var($joinAllow, FILTER_VALIDATE_BOOLEAN);
@endphp

<nav
    class="fixed inset-x-0 top-0 z-[1000] border-b border-[#E5B83B]/20 bg-[#0C2C6B]/95 shadow-lg shadow-black/10 backdrop-blur-xl lg:sticky lg:top-0">

    <!-- Gold Accent Line -->
    <div
        class="absolute inset-x-0 bottom-0 h-[2px] bg-gradient-to-r from-transparent via-[#D79B10] to-transparent">
    </div>


    <!-- =====================================================
         NAVBAR CONTAINER
    ====================================================== -->

    <div class="mx-auto max-w-7xl px-3 sm:px-6">

        <div class="flex h-16 items-center justify-between lg:h-20">


            <!-- =================================================
                 LOGO
            ================================================== -->

            <a
                href="{{ route('home') }}"
                class="group flex shrink-0 items-center gap-2 lg:gap-3">

                <div
                    class="rounded-full bg-white p-1 shadow-lg transition duration-300 group-hover:scale-105">

                    <img
                        src="{{ asset('images/truelife-logo.png') }}"
                        alt="TRUELIFE Logo"
                        class="h-9 w-9 object-contain lg:h-12 lg:w-12">

                </div>

                <h1 class="text-lg font-black tracking-wide text-white lg:text-xl">
                    TRUELIFE
                </h1>

            </a>


            <!-- =================================================
                 DESKTOP NAVIGATION
            ================================================== -->

            <div
                class="hidden items-center rounded-full border border-white/10 bg-white/5 p-1.5 backdrop-blur-md lg:flex">

                <!-- HOME -->
                <a
                    href="{{ route('home') }}"
                    class="rounded-full px-5 py-2 text-sm transition-all duration-200
                    {{ request()->is('/')
                        ? 'bg-[#D79B10] font-bold text-white shadow-md'
                        : 'font-semibold text-white hover:bg-white/10 hover:text-[#E5B83B]' }}">
                    Home
                </a>


                <!-- ABOUT -->
                <a
                    href="{{ route('about') }}"
                    class="rounded-full px-5 py-2 text-sm transition-all duration-200
                    {{ request()->is('about*')
                        ? 'bg-[#D79B10] font-bold text-white shadow-md'
                        : 'font-semibold text-white hover:bg-white/10 hover:text-[#E5B83B]' }}">
                    About
                </a>


                <!-- TEAM -->
                <a
                    href="{{ route('team') }}"
                    class="rounded-full px-5 py-2 text-sm transition-all duration-200
                    {{ request()->is('team*')
                        ? 'bg-[#D79B10] font-bold text-white shadow-md'
                        : 'font-semibold text-white hover:bg-white/10 hover:text-[#E5B83B]' }}">
                    Team
                </a>


                <!-- HOW IT WORKS -->
                <a
                    href="{{ route('how-it-works') }}"
                    class="rounded-full px-5 py-2 text-sm transition-all duration-200
                    {{ request()->is('how-it-works*')
                        ? 'bg-[#D79B10] font-bold text-white shadow-md'
                        : 'font-semibold text-white hover:bg-white/10 hover:text-[#E5B83B]' }}">
                    How It Works
                </a>


                <!-- TESTIMONIALS -->
                <a
                    href="{{ route('testimonials') }}"
                    class="rounded-full px-5 py-2 text-sm transition-all duration-200
                    {{ request()->is('testimonials*')
                        ? 'bg-[#D79B10] font-bold text-white shadow-md'
                        : 'font-semibold text-white hover:bg-white/10 hover:text-[#E5B83B]' }}">
                    Testimonials
                </a>


                <!-- NEWS -->
                <a
                    href="{{ route('news') }}"
                    class="rounded-full px-5 py-2 text-sm transition-all duration-200
                    {{ request()->is('news*')
                        ? 'bg-[#D79B10] font-bold text-white shadow-md'
                        : 'font-semibold text-white hover:bg-white/10 hover:text-[#E5B83B]' }}">
                    News & Updates
                </a>


                <!-- CONTACT -->
                <a
                    href="{{ route('contact') }}"
                    class="rounded-full px-5 py-2 text-sm transition-all duration-200
                    {{ request()->is('contact*')
                        ? 'bg-[#D79B10] font-bold text-white shadow-md'
                        : 'font-semibold text-white hover:bg-white/10 hover:text-[#E5B83B]' }}">
                    Contact Us
                </a>

            </div>


            <!-- =================================================
                 AUTHENTICATION
                 ONE SINGLE AUTH SECTION
            ================================================== -->

            @guest

                <!-- =============================================
                     GUEST
                ============================================== -->

                <div class="flex items-center gap-2 lg:gap-3">

                @if($joinAllow)
                    <button
                        type="button"
                        onclick="Livewire.dispatch('open-application-modal')"
                        class="cursor-pointer whitespace-nowrap rounded-full bg-[#D79B10]
                            px-2.5 py-1 text-[9px] font-extrabold text-white shadow-md
                            transition hover:bg-[#E5B83B]
                            lg:px-6 lg:py-2.5 lg:text-sm">
                        JOIN
                    </button>
                @else
                    <button
                        type="button"
                        disabled
                        class="cursor-not-allowed whitespace-nowrap rounded-full bg-[#D79B10]
                            px-2.5 py-1 text-[9px] font-extrabold text-white opacity-60
                            shadow-md transition hover:bg-[#E5B83B] lg:px-6 lg:py-2.5 lg:text-sm">
                        JOIN Unavailable
                    </button>
                @endif

                    <!-- LOGIN -->
                    <button
                        type="button"
                        onclick="Livewire.dispatch('open-login-modal')"
                        class="cursor-pointer rounded-full border border-[#D79B10]/70 bg-white/5 px-3 py-1.5 text-[10px] font-bold text-[#E5B83B] transition hover:bg-[#D79B10] hover:text-[#0C2C6B] lg:px-6 lg:py-2.5 lg:text-sm">

                        Login

                    </button>

                </div>


            @else

                <!-- =============================================
                     LOGGED-IN USER
                ============================================== -->

                @php

                    $user = Auth::user();

                    $displayName = trim(
                        ($user->firstname ?? '') . ' ' .
                        ($user->lastname ?? '')
                    );

                    if (empty($displayName)) {
                        $displayName = $user->username ?? 'User';
                    }

                    $avatarUrl =
                        'https://ui-avatars.com/api/?name=' .
                        urlencode($displayName) .
                        '&background=D79B10&color=fff';

                @endphp


                <!-- =============================================
                     PROFILE DROPDOWN
                     WORKS ON MOBILE + DESKTOP
                ============================================== -->

                <div
                    x-data="{ profileOpen: false }"
                    @click.away="profileOpen = false"
                    @keydown.escape.window="profileOpen = false"
                    class="relative">

                    <!-- PROFILE BUTTON -->
                    <button
                        type="button"
                        @click="profileOpen = !profileOpen"
                        :aria-expanded="profileOpen"
                        aria-label="Open profile menu"
                        class="flex cursor-pointer items-center gap-1.5 rounded-full border border-white/10 bg-white/10 px-2 py-1.5 transition hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-[#D79B10] lg:gap-3 lg:px-3.5 lg:py-2">

                        <!-- Avatar -->
                        <img
                            src="{{ $avatarUrl }}"
                            alt="{{ $displayName }}"
                            class="h-7 w-7 shrink-0 rounded-full border-2 border-[#D79B10] object-cover lg:h-9 lg:w-9">


                        <!-- Name -->
                        <span
                            class="hidden max-w-[130px] truncate text-[10px] font-bold text-white sm:block lg:max-w-[160px] lg:text-sm">

                            {{ $displayName }}

                        </span>


                        <!-- Arrow -->
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            class="h-3.5 w-3.5 shrink-0 text-[#E5B83B] transition-transform duration-200 lg:h-4 lg:w-4"
                            :class="{ 'rotate-180': profileOpen }">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7" />

                        </svg>

                    </button>


                    <!-- =========================================
                         PROFILE DROPDOWN
                    ========================================== -->

                    <div
                        x-show="profileOpen"
                        x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                        class="absolute right-0 top-full z-[1100] mt-3 w-56 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl lg:w-64">


                        <!-- USER HEADER -->
                        <div
                            class="border-b border-slate-100 bg-slate-50 px-4 py-3 lg:px-5 lg:py-4">

                            <div class="flex items-center gap-3">

                                <img
                                    src="{{ $avatarUrl }}"
                                    alt="{{ $displayName }}"
                                    class="h-10 w-10 shrink-0 rounded-full border-2 border-[#D79B10] object-cover lg:h-11 lg:w-11">


                                <div class="min-w-0">

                                    <p class="truncate text-sm font-bold text-slate-800">
                                        {{ $displayName }}
                                    </p>

                                    <p class="truncate text-xs text-slate-500">
                                        {{ $user->email }}
                                    </p>

                                </div>

                            </div>

                        </div>


                        <!-- MENU -->
                        <div class="p-2">

                            <!-- DASHBOARD -->
                            <a
                                href="{{ route('dashboard') }}"
                                @click="profileOpen = false"
                                class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">

                                <!-- Dashboard Icon -->
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-[#0C2C6B]"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 10.5L12 3l9 7.5M5 9.5V21h14V9.5M9 21v-6h6v6" />

                                </svg>

                                <span>
                                    My Dashboard
                                </span>

                            </a>


                            <!-- LOGOUT -->
                            <form
                                id="navbarLogoutForm"
                                action="{{ route('logout') }}"
                                method="POST">

                                @csrf

                                <button
                                    type="button"
                                    @click="profileOpen = false"
                                    onclick="showLogoutConfirmation('navbarLogoutForm')"
                                    class="flex w-full cursor-pointer items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-50">

                                    <!-- Logout Icon -->
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 12H3m0 0l4-4m-4 4l4 4m5-9V5a2 2 0 012-2h5a2 2 0 012 2v14a2 2 0 01-2 2h-5a2 2 0 01-2-2v-2" />

                                    </svg>

                                    <span>
                                        Logout
                                    </span>

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            @endguest

        </div>

    </div>

</nav>


<!-- =========================================================
     MOBILE BOTTOM NAVIGATION
     ALL LINKS ALWAYS VISIBLE
========================================================= -->

<div class="lg:hidden">

    <div
        class="fixed inset-x-0 bottom-0 z-[9999] px-1 pb-[calc(env(safe-area-inset-bottom)+5px)]">

        <div
            class="relative mx-auto w-full rounded-2xl border border-white/10 bg-[#071D3F]/98 px-0.5 py-1 shadow-2xl shadow-black/40 backdrop-blur-xl">

            <!-- Gold Accent -->
            <div
                class="absolute inset-x-8 -top-px h-px bg-gradient-to-r from-transparent via-[#D79B10] to-transparent">
            </div>


            <div class="flex w-full items-stretch">


                <!-- =========================================
                     HOME
                ========================================== -->

                <a
                    href="{{ route('home') }}"
                    class="group flex min-w-0 flex-1 flex-col items-center justify-center rounded-xl px-0.5 py-1 transition">

                    <div
                        class="flex h-6 w-6 items-center justify-center rounded-lg transition
                        {{ request()->is('/')
                            ? 'bg-[#D79B10] text-white'
                            : 'text-slate-400 group-hover:bg-white/10 group-hover:text-[#E5B83B]' }}">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-3.5 w-3.5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 11l9-8 9 8M5 10v10h14V10" />

                        </svg>

                    </div>

                    <span
                        class="mt-0.5 w-full truncate text-center text-[8px] font-semibold leading-tight
                        {{ request()->is('/')
                            ? 'text-[#E5B83B]'
                            : 'text-slate-400' }}">

                        Home

                    </span>

                </a>


                <!-- =========================================
                     ABOUT
                ========================================== -->

                <a
                    href="{{ route('about') }}"
                    class="group flex min-w-0 flex-1 flex-col items-center justify-center rounded-xl px-0.5 py-1 transition">

                    <div
                        class="flex h-6 w-6 items-center justify-center rounded-lg transition
                        {{ request()->is('about*')
                            ? 'bg-[#D79B10] text-white'
                            : 'text-slate-400 group-hover:bg-white/10 group-hover:text-[#E5B83B]' }}">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-3.5 w-3.5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-8a9 9 0 110 18 9 9 0 010-18zm0 4h.01" />

                        </svg>

                    </div>

                    <span
                        class="mt-0.5 w-full truncate text-center text-[8px] font-semibold leading-tight
                        {{ request()->is('about*')
                            ? 'text-[#E5B83B]'
                            : 'text-slate-400' }}">

                        About

                    </span>

                </a>


                <!-- =========================================
                     TEAM
                ========================================== -->

                <a
                    href="{{ route('team') }}"
                    class="group flex min-w-0 flex-1 flex-col items-center justify-center rounded-xl px-0.5 py-1 transition">

                    <div
                        class="flex h-6 w-6 items-center justify-center rounded-lg transition
                        {{ request()->is('team*')
                            ? 'bg-[#D79B10] text-white'
                            : 'text-slate-400 group-hover:bg-white/10 group-hover:text-[#E5B83B]' }}">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-3.5 w-3.5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-8a4 4 0 110 8 4 4 0 010-8zm6 1a3 3 0 11-6 0 3 3 0 016 0z" />

                        </svg>

                    </div>

                    <span
                        class="mt-0.5 w-full truncate text-center text-[8px] font-semibold leading-tight
                        {{ request()->is('team*')
                            ? 'text-[#E5B83B]'
                            : 'text-slate-400' }}">

                        Team

                    </span>

                </a>


                <!-- =========================================
                     HOW IT WORKS
                ========================================== -->

                <a
                    href="{{ route('how-it-works') }}"
                    class="group flex min-w-0 flex-1 flex-col items-center justify-center rounded-xl px-0.5 py-1 transition">

                    <div
                        class="flex h-6 w-6 items-center justify-center rounded-lg transition
                        {{ request()->is('how-it-works*')
                            ? 'bg-[#D79B10] text-white'
                            : 'text-slate-400 group-hover:bg-white/10 group-hover:text-[#E5B83B]' }}">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-3.5 w-3.5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-8a9 9 0 110 18 9 9 0 010-18zm0 4h.01" />

                        </svg>

                    </div>

                    <span
                        class="mt-0.5 w-full truncate text-center text-[8px] font-semibold leading-tight
                        {{ request()->is('how-it-works*')
                            ? 'text-[#E5B83B]'
                            : 'text-slate-400' }}">

                        How It Works

                    </span>

                </a>


                <!-- =========================================
                     TESTIMONIALS
                ========================================== -->

                <a
                    href="{{ route('testimonials') }}"
                    class="group flex min-w-0 flex-1 flex-col items-center justify-center rounded-xl px-0.5 py-1 transition">

                    <div
                        class="flex h-6 w-6 items-center justify-center rounded-lg transition
                        {{ request()->is('testimonials*')
                            ? 'bg-[#D79B10] text-white'
                            : 'text-slate-400 group-hover:bg-white/10 group-hover:text-[#E5B83B]' }}">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-3.5 w-3.5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 10h8M8 14h5m-8 6l3-3h7a5 5 0 005-5V7a5 5 0 00-5-5H6a5 5 0 00-5 5v5a5 5 0 005 5v3z" />

                        </svg>

                    </div>

                    <span
                        class="mt-0.5 w-full truncate text-center text-[8px] font-semibold leading-tight
                        {{ request()->is('testimonials*')
                            ? 'text-[#E5B83B]'
                            : 'text-slate-400' }}">

                        Testimonials

                    </span>

                </a>


                <!-- =========================================
                     NEWS
                ========================================== -->

                <a
                    href="{{ route('news') }}"
                    class="group flex min-w-0 flex-1 flex-col items-center justify-center rounded-xl px-0.5 py-1 transition">

                    <div
                        class="flex h-6 w-6 items-center justify-center rounded-lg transition
                        {{ request()->is('news*')
                            ? 'bg-[#D79B10] text-white'
                            : 'text-slate-400 group-hover:bg-white/10 group-hover:text-[#E5B83B]' }}">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-3.5 w-3.5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2zM7 8h10M7 12h10M7 16h6" />

                        </svg>

                    </div>

                    <span
                        class="mt-0.5 w-full truncate text-center text-[8px] font-semibold leading-tight
                        {{ request()->is('news*')
                            ? 'text-[#E5B83B]'
                            : 'text-slate-400' }}">

                        News

                    </span>

                </a>


                <!-- =========================================
                     CONTACT
                ========================================== -->

                <a
                    href="{{ route('contact') }}"
                    class="group flex min-w-0 flex-1 flex-col items-center justify-center rounded-xl px-0.5 py-1 transition">

                    <div
                        class="flex h-6 w-6 items-center justify-center rounded-lg transition
                        {{ request()->is('contact*')
                            ? 'bg-[#D79B10] text-white'
                            : 'text-slate-400 group-hover:bg-white/10 group-hover:text-[#E5B83B]' }}">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-3.5 w-3.5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5v10a2 2 0 002 2h14" />

                        </svg>

                    </div>

                    <span
                        class="mt-0.5 w-full truncate text-center text-[8px] font-semibold leading-tight
                        {{ request()->is('contact*')
                            ? 'text-[#E5B83B]'
                            : 'text-slate-400' }}">

                        Contact

                    </span>

                </a>

            </div>

        </div>

    </div>

</div>


<!-- =========================================================
     LOGOUT CONFIRMATION
========================================================= -->

<script>
function showLogoutConfirmation(formId) {

    Swal.fire({
        title: 'Logout?',
        text: 'Are you sure you want to logout?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0B2A5B',
        cancelButtonColor: '#D79B10',
        confirmButtonText: 'Yes, Logout',
        cancelButtonText: 'Cancel'
    }).then((result) => {

        if (result.isConfirmed) {

            const form = document.getElementById(formId);

            if (form) {
                form.submit();
            }

        }

    });

}
</script>
