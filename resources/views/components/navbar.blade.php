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
                    <h1 class="text-xl font-black tracking-wide text-white">
                        DAYONGAN
                    </h1>
                    <p class="text-xs tracking-[0.25em] uppercase text-[#E5B83B]">
                        Community Support
                    </p>
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

                   <!-- How It Works Link -->
                <a href="{{ route('testimonials') }}"
                   class="rounded-full px-5 py-2 text-sm transition-all duration-200 {{ request()->is('testimonials*') ? 'bg-[#D79B10] font-bold text-white shadow-md' : 'font-semibold text-white hover:bg-white/10 hover:text-[#E5B83B]' }}">
                    Testimonials
                </a>

                <!-- News Link -->
                <a href="{{ route('news') }}"
                   class="rounded-full px-5 py-2 text-sm transition-all duration-200 {{ request()->is('news*') ? 'bg-[#D79B10] font-bold text-white shadow-md' : 'font-semibold text-white hover:bg-white/10 hover:text-[#E5B83B]' }}">
                    News & Updates
                </a>

                   <!-- News Link -->
                <a href="{{ route('contact') }}"
                   class="rounded-full px-5 py-2 text-sm transition-all duration-200 {{ request()->is('contact*') ? 'bg-[#D79B10] font-bold text-white shadow-md' : 'font-semibold text-white hover:bg-white/10 hover:text-[#E5B83B]' }}">
                    Contact Us
                </a>

            </div>

            <!-- Right CTA & Auth Buttons -->
            <div class="hidden lg:flex items-center gap-3">

                <button
                    onclick="Livewire.dispatch('open-application-modal')"
                    class="rounded-full bg-[#D79B10] px-6 py-2.5 text-sm font-extrabold text-white shadow-lg shadow-[#D79B10]/20 transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#E5B83B] hover:shadow-xl">
                    JOIN
                </button>

                @if (Route::has('login'))
                    <a
                        onclick="Livewire.dispatch('open-login-modal')"
                        class="rounded-full border border-[#D79B10]/60 bg-white/5 px-6 py-2.5 text-sm font-bold text-[#E5B83B] backdrop-blur transition-all duration-300 hover:border-[#E5B83B] hover:bg-[#D79B10] hover:text-[#0C2C6B]">
                        Login
                    </a>
                @endif

            </div>

            <!-- Mobile Toggle -->
            <button
                @click="open=!open"
                class="rounded-lg p-2 text-[#E5B83B] transition hover:bg-white/10 lg:hidden">

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

        <!-- Mobile Menu -->
        <div
            x-show="open"
            x-transition
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

                <a href="{{ route('news') }}"
                   class="block rounded-xl py-3 text-center transition {{ request()->is('news*') ? 'bg-[#D79B10] font-bold text-white' : 'font-semibold text-white hover:bg-white/10' }}">
                    News & Updates
                </a>

                <div class="pt-2 border-t border-white/10 flex flex-col gap-2">
                    <button
                        onclick="Livewire.dispatch('open-application-modal')"
                        class="w-full rounded-xl bg-[#D79B10] py-3 font-bold text-white transition hover:bg-[#E5B83B]">
                        JOIN
                    </button>

                    @if (Route::has('login'))
                        <a
                            onclick="Livewire.dispatch('open-application-modal')"
                            class="block rounded-xl border border-[#D79B10] py-3 text-center font-bold text-[#E5B83B] transition hover:bg-[#D79B10] hover:text-[#0C2C6B]">
                            Login
                        </a>
                    @endif
                </div>

            </div>

        </div>

    </div>

</nav>
