<nav
    x-data="{ open: false }"
    class="sticky top-0 z-50 border-b border-[#E5B83B]/20 bg-[#0C2C6B]/95 backdrop-blur-xl shadow-lg shadow-black/10">

    <!-- Gold Accent Line -->
    <div class="absolute inset-x-0 bottom-0 h-[2px] bg-gradient-to-r from-transparent via-[#D79B10] to-transparent"></div>

    <div class="max-w-7xl mx-auto px-6">

        <div class="flex h-20 items-center justify-between">

            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">

                <div class="rounded-full bg-white p-1 shadow-lg transition duration-300 group-hover:scale-105">
                    <img
                        src="{{ asset('images/dayongan-logo.png') }}"
                        alt="DAYONGAN Logo"
                        class="h-12 w-12 object-contain">
                </div>

                <div>
                    <p class="text-xs tracking-[0.25em] uppercase text-[#E5B83B]">
                        TrueLife
                    </p>
                    <h1 class="text-xl font-black tracking-wide text-white">
                        DAYONGAN
                    </h1>
                </div>

            </a>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center rounded-full bg-white/5 p-1.5 backdrop-blur-md border border-white/10">

                <!-- Home Link -->
                <a href="{{ route('home') }}"
                   class="rounded-full px-5 py-2 text-sm transition-all duration-200 {{ request()->is('/') ? 'bg-[#D79B10] font-bold text-white shadow-md' : 'font-semibold text-white hover:bg-white/10 hover:text-[#E5B83B]' }}">
                    Home
                </a>

                <!-- About Link -->
                <a href="{{ route('about') }}"
                   class="rounded-full px-5 py-2 text-sm transition-all duration-200 {{ request()->is('about*') ? 'bg-[#D79B10] font-bold text-white shadow-md' : 'font-semibold text-white hover:bg-white/10 hover:text-[#E5B83B]' }}">
                    About
                </a>

                <!-- Team Link -->
                <a href="{{ route('team') }}"
                   class="rounded-full px-5 py-2 text-sm transition-all duration-200 {{ request()->is('team*') ? 'bg-[#D79B10] font-bold text-white shadow-md' : 'font-semibold text-white hover:bg-white/10 hover:text-[#E5B83B]' }}">
                    Team
                </a>

                <!-- How It Works Link -->
                <a href="{{ route('how-it-works') }}"
                   class="rounded-full px-5 py-2 text-sm transition-all duration-200 {{ request()->is('how-it-works*') ? 'bg-[#D79B10] font-bold text-white shadow-md' : 'font-semibold text-white hover:bg-white/10 hover:text-[#E5B83B]' }}">
                    How It Works
                </a>

                <!-- Testimonials Link -->
                <a href="{{ route('testimonials') }}"
                   class="rounded-full px-5 py-2 text-sm transition-all duration-200 {{ request()->is('testimonials*') ? 'bg-[#D79B10] font-bold text-white shadow-md' : 'font-semibold text-white hover:bg-white/10 hover:text-[#E5B83B]' }}">
                    Testimonials
                </a>

                <!-- News Link -->
                <a href="{{ route('news') }}"
                   class="rounded-full px-5 py-2 text-sm transition-all duration-200 {{ request()->is('news*') ? 'bg-[#D79B10] font-bold text-white shadow-md' : 'font-semibold text-white hover:bg-white/10 hover:text-[#E5B83B]' }}">
                    News & Updates
                </a>

                <!-- Contact Link -->
                <a href="{{ route('contact') }}"
                   class="rounded-full px-5 py-2 text-sm transition-all duration-200 {{ request()->is('contact*') ? 'bg-[#D79B10] font-bold text-white shadow-md' : 'font-semibold text-white hover:bg-white/10 hover:text-[#E5B83B]' }}">
                    Contact Us
                </a>

            </div>

            <!-- Right CTA & Auth Controls (Desktop) -->
            <div class="hidden lg:flex items-center gap-3">

                @guest

                    <button
                        type="button"
                        onclick="Livewire.dispatch('open-application-modal')"
                        class="rounded-full bg-[#D79B10] px-6 py-2.5 text-sm font-extrabold text-white shadow-lg shadow-[#D79B10]/20 transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#E5B83B] hover:shadow-xl cursor-pointer">
                        JOIN
                    </button>

                    <button
                        type="button"
                        onclick="Livewire.dispatch('open-login-modal')"
                        class="rounded-full border border-[#D79B10]/60 bg-white/5 px-6 py-2.5 text-sm font-bold text-[#E5B83B] backdrop-blur transition-all duration-300 hover:border-[#E5B83B] hover:bg-[#D79B10] hover:text-[#0C2C6B] cursor-pointer">
                        Login
                    </button>

                @else

                    @php
                        $u = Auth::user();
                        $fullName = trim(($u->firstname ?? '') . ' ' . ($u->lastname ?? ''));
                        if (empty($fullName)) {
                            $fullName = $u->name ?? $u->username ?? 'User';
                        }

                        $roleID = (int)($u->RoleID ?? $u->roleID ?? 0);
                        $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($fullName) . '&background=D79B10&color=fff';
                    @endphp

                    <!-- User Profile Dropdown -->
                    <div x-data="{ profileMenu: false }"
                         @keydown.escape.window="profileMenu = false"
                         class="relative">

                        <!-- Trigger Button -->
                        <button
                            @click="profileMenu = !profileMenu"
                            :aria-expanded="profileMenu"
                            type="button"
                            class="flex items-center gap-3 rounded-full bg-white/10 px-3.5 py-2 border border-white/10 hover:bg-white/15 transition w-auto max-w-xs focus:outline-none focus:ring-2 focus:ring-[#D79B10]">

                            <img
                                src="{{ $avatarUrl }}"
                                alt="{{ $fullName }}'s Avatar"
                                class="w-9 h-9 rounded-full border-2 border-[#D79B10] object-cover shrink-0">

                            <div class="hidden xl:block text-left min-w-0 pr-1">
                                <p class="text-white text-sm font-bold leading-tight whitespace-nowrap overflow-hidden text-ellipsis">
                                    {{ $fullName }}
                                </p>
                            </div>

                            <svg class="w-4 h-4 text-white transition-transform duration-200 shrink-0"
                                 :class="{ 'rotate-180': profileMenu }"
                                 xmlns="http://www.w3.org/2000/svg"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            x-show="profileMenu"
                            @click.away="profileMenu = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            x-cloak
                            class="absolute right-0 mt-3 w-64 rounded-2xl bg-white shadow-2xl overflow-hidden border border-slate-200 z-50">

                            <!-- User Header -->
                            <div class="px-5 py-4 bg-slate-50 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <img
                                        src="{{ $avatarUrl }}"
                                        alt="{{ $fullName }}"
                                        class="w-11 h-11 rounded-full object-cover shrink-0 border border-slate-200">

                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-800 text-sm truncate">
                                            {{ $fullName }}
                                        </p>
                                        <p class="text-xs text-slate-500 truncate">
                                            {{ $u->email }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Links -->
                            <div class="py-1">


                                <a href="{{ route('dashboard') }}"
                                   class="flex items-center gap-3 px-5 py-2.5 text-sm text-slate-700 hover:bg-slate-100 transition">
                                    <span>🗂️</span>
                                    <span>My Dashboard</span>
                                </a>
                            </div>

                            <!-- Logout Form -->
                            <div class="border-t border-slate-100 py-1">
                            <form id="navbarLogoutForm" action="{{ route('logout') }}" method="POST">
                                @csrf

                                <button
                                    type="button"
                                    onclick="confirmNavbarLogout()"
                                    class="flex w-full items-center gap-3 px-5 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition cursor-pointer cursor-pointer">
                                    <span>🚪</span>
                                    <span>Logout</span>
                                </button>
                            </form>
                            </div>

                        </div>

                    </div>

                @endguest

            </div>

            <!-- Mobile Menu Toggle Button -->
            <button
                type="button"
                @click="open = !open"
                class="rounded-lg p-2 text-[#E5B83B] transition hover:bg-white/10 lg:hidden focus:outline-none">

                <svg x-show="!open"
                     xmlns="http://www.w3.org/2000/svg"
                     class="h-8 w-8"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>

                <svg x-show="open"
                     x-cloak
                     xmlns="http://www.w3.org/2000/svg"
                     class="h-8 w-8"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>

            </button>

        </div>

        <!-- Mobile Drawer -->
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            x-cloak
            class="pb-6 lg:hidden">

            <div class="mt-2 space-y-2 rounded-3xl border border-white/10 bg-[#071D3F]/90 p-4 backdrop-blur-xl">

                <a href="{{ route('home') }}"
                   class="block rounded-xl py-3 text-center transition {{ request()->is('/') ? 'bg-[#D79B10] font-bold text-white' : 'font-semibold text-white hover:bg-white/10' }}">
                    Home
                </a>

                <a href="{{ route('about') }}"
                   class="block rounded-xl py-3 text-center transition {{ request()->is('about*') ? 'bg-[#D79B10] font-bold text-white' : 'font-semibold text-white hover:bg-white/10' }}">
                    About Us
                </a>

                <a href="{{ route('team') }}"
                   class="block rounded-xl py-3 text-center transition {{ request()->is('team*') ? 'bg-[#D79B10] font-bold text-white' : 'font-semibold text-white hover:bg-white/10' }}">
                    Team
                </a>

                <a href="{{ route('how-it-works') }}"
                   class="block rounded-xl py-3 text-center transition {{ request()->is('how-it-works*') ? 'bg-[#D79B10] font-bold text-white' : 'font-semibold text-white hover:bg-white/10' }}">
                    How It Works
                </a>

                <a href="{{ route('testimonials') }}"
                   class="block rounded-xl py-3 text-center transition {{ request()->is('testimonials*') ? 'bg-[#D79B10] font-bold text-white' : 'font-semibold text-white hover:bg-white/10' }}">
                    Testimonials
                </a>

                <a href="{{ route('news') }}"
                   class="block rounded-xl py-3 text-center transition {{ request()->is('news*') ? 'bg-[#D79B10] font-bold text-white' : 'font-semibold text-white hover:bg-white/10' }}">
                    News & Updates
                </a>

                <a href="{{ route('contact') }}"
                   class="block rounded-xl py-3 text-center transition {{ request()->is('contact*') ? 'bg-[#D79B10] font-bold text-white' : 'font-semibold text-white hover:bg-white/10' }}">
                    Contact Us
                </a>

                <div class="pt-3 border-t border-white/10 flex flex-col gap-2">

                    @guest

                        <button
                            type="button"
                            onclick="Livewire.dispatch('open-application-modal')"
                            class="w-full rounded-xl bg-[#D79B10] py-3 font-bold text-white transition hover:bg-[#E5B83B] cursor-pointer">
                            JOIN
                        </button>

                        <button
                            type="button"
                            onclick="Livewire.dispatch('open-login-modal')"
                            class="w-full rounded-xl border border-[#D79B10] py-3 font-bold text-[#E5B83B] transition hover:bg-[#D79B10] hover:text-[#0C2C6B] cursor-pointer">
                            Login
                        </button>

                    @else

                        @php
                            $mUser = Auth::user();
                            $mFullName = trim(($mUser->firstname ?? '') . ' ' . ($mUser->lastname ?? ''));
                            if (empty($mFullName)) {
                                $mFullName = $mUser->name ?? $mUser->username ?? 'User';
                            }

                            $mRoleID = (int)($mUser->RoleID ?? $mUser->roleID ?? 0);

                            $mAvatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($mFullName) . '&background=D79B10&color=fff';
                        @endphp

                        <!-- Mobile User Info Block -->
                        <div class="flex items-center gap-3 px-3 py-2.5 bg-white/5 rounded-xl border border-white/10 mb-2">
                            <img src="{{ $mAvatarUrl }}" alt="{{ $mFullName }}" class="w-10 h-10 rounded-full border border-[#D79B10] shrink-0">
                            <div class="min-w-0">
                                <p class="text-white font-bold text-sm truncate">{{ $mFullName }}</p>
                                <p class="text-xs text-slate-300 truncate">{{ $mUser->email }}</p>
                            </div>
                        </div>


                            <a href="{{ route('dashboard') }}"
                               class="block rounded-xl bg-[#D79B10] py-3 text-center font-bold text-white hover:bg-[#E5B83B] transition">
                                📊 My Dashboard
                            </a>


                    <form id="mobileLogoutForm" action="{{ route('logout') }}" method="POST">
                        @csrf

                        <button
                            type="button"
                            onclick="confirmMobileLogout()"
                            class="w-full rounded-xl border border-red-400/50 py-3 font-bold text-red-300 transition hover:bg-red-500/20 hover:text-white cursor-pointer">
                            🚪 Logout
                        </button>
                    </form>

                    @endguest

                </div>

            </div>

        </div>

    </div>

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
        // Removed reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}

function confirmSidebarLogout() {
    showLogoutConfirmation('sidebarLogoutForm');
}

function confirmNavbarLogout() {
    showLogoutConfirmation('navbarLogoutForm');
}

function confirmMobileLogout() {
    showLogoutConfirmation('mobileLogoutForm');
}
</script>

</nav>
