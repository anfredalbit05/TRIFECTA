@extends('layouts.app')

@section('title', 'About Us')

@section('content')

<!-- ABOUT HERO SECTION -->
<section class="relative bg-gradient-to-b from-[#0B2A5B] to-[#071D3F] py-16 sm:py-20 lg:py-28 text-white overflow-hidden">
    <!-- Decorative Glows -->
    <div class="absolute -top-24 -right-24 h-72 w-72 sm:h-96 sm:w-96 rounded-full bg-[#D4A017]/20 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 h-72 w-72 sm:h-96 sm:w-96 rounded-full bg-[#163E7A]/50 blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-block px-3.5 py-1.5 rounded-full bg-[#D4A017]/20 text-[#E8B923] font-bold tracking-widest uppercase text-[11px] sm:text-xs border border-[#D4A017]/30">
            Solidaritas • Vitae Auxilium
        </span>

        <h1 class="mt-4 sm:mt-6 text-2xl xs:text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-snug sm:leading-tight">
            Empowering Communities Through <br class="hidden sm:block" />
            <span class="text-[#E8B923]">Protection & Compassion</span>
        </h1>

        <p class="mt-4 sm:mt-6 max-w-3xl mx-auto text-sm sm:text-base lg:text-lg text-slate-300 leading-relaxed px-2">
            DAYONGAN is a dedicated community support platform operating in official partnership with <strong>COCOGEN Insurance</strong>. We bridge the gap between families and essential financial, medical, and micro-insurance support.
        </p>
    </div>
</section>


<!-- MISSION, VISION & STORY SECTION -->
<section class="py-12 sm:py-16 lg:py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">

            <!-- Left Text Content -->
            <div class="space-y-4 sm:space-y-6">
                <span class="text-xs sm:text-sm font-bold uppercase tracking-widest text-[#D4A017] block">Who We Are</span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#0B2A5B] leading-tight">
                    Built to protect families when they need it most.
                </h2>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    Founded on the principles of community solidarity and mutual aid, DAYONGAN provides structured, accessible micro-insurance and health assistance for primary account holders and their families.
                </p>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    Whether facing unexpected medical emergencies, accidents, or life events, our mission is to ensure every member moves forward with dignity and financial peace of mind.
                </p>

                <!-- Mission & Vision Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 sm:pt-4">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm transition hover:shadow-md">
                        <div class="text-2xl mb-2">🎯</div>
                        <h3 class="font-bold text-[#0B2A5B] text-base">Our Mission</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">To deliver transparent, accessible, and fast assistance to every Filipino household.</p>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm transition hover:shadow-md">
                        <div class="text-2xl mb-2">👁️</div>
                        <h3 class="font-bold text-[#0B2A5B] text-base">Our Vision</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">A resilient community where no family faces financial hardship alone.</p>
                    </div>
                </div>
            </div>

            <!-- Right Visual Box -->
            <div class="relative mt-6 lg:mt-0">
                <div class="relative mx-auto rounded-3xl overflow-hidden shadow-2xl border-2 sm:border-4 border-white max-w-lg lg:max-w-none">
                    <img src="{{ asset('images/hero-bg.jpg') }}" alt="Dayongan Community Support" class="w-full h-[320px] sm:h-[400px] lg:h-[450px] object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0B2A5B]/90 via-[#0B2A5B]/20 to-transparent"></div>

                    <div class="absolute bottom-5 sm:bottom-6 left-5 sm:left-6 right-5 sm:right-6 text-white">
                        <p class="text-[10px] sm:text-xs uppercase tracking-wider text-[#E8B923] font-bold">Official Partner</p>
                        <h3 class="text-lg sm:text-xl font-bold mt-0.5">COCOGEN Insurance</h3>
                        <p class="text-xs text-slate-200 mt-1 leading-normal">Providing trusted micro-insurance and assistance solutions.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- CORE VALUES SECTION -->
<section class="py-12 sm:py-16 lg:py-20 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto">
            <span class="text-xs sm:text-sm font-bold uppercase tracking-widest text-[#D4A017] block">Guiding Principles</span>
            <h2 class="mt-1 sm:mt-2 text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#0B2A5B]">Our Core Values</h2>
            <p class="mt-2 text-sm sm:text-base text-slate-600">The standards that guide every decision and claim we process.</p>
        </div>

        <div class="mt-8 sm:mt-12 grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">

            <div class="p-5 sm:p-6 bg-slate-50 rounded-2xl border border-slate-200/80 transition hover:border-[#0B2A5B]/30">
                <div class="h-11 w-11 sm:h-12 sm:w-12 rounded-xl bg-[#0B2A5B]/10 text-[#0B2A5B] flex items-center justify-center font-bold text-xl mb-3 sm:mb-4">
                    🤝
                </div>
                <h3 class="font-bold text-base sm:text-lg text-[#0B2A5B]">Solidarity</h3>
                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">Standing together with our members through life’s triumphs and challenges.</p>
            </div>

            <div class="p-5 sm:p-6 bg-slate-50 rounded-2xl border border-slate-200/80 transition hover:border-[#D4A017]/30">
                <div class="h-11 w-11 sm:h-12 sm:w-12 rounded-xl bg-[#D4A017]/10 text-[#D4A017] flex items-center justify-center font-bold text-xl mb-3 sm:mb-4">
                    🛡️
                </div>
                <h3 class="font-bold text-base sm:text-lg text-[#0B2A5B]">Integrity</h3>
                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">Maintaining total transparency in our benefits, coverage, and operations.</p>
            </div>

            <div class="p-5 sm:p-6 bg-slate-50 rounded-2xl border border-slate-200/80 transition hover:border-emerald-500/30">
                <div class="h-11 w-11 sm:h-12 sm:w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl mb-3 sm:mb-4">
                    ⚡
                </div>
                <h3 class="font-bold text-base sm:text-lg text-[#0B2A5B]">Accessibility</h3>
                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">Ensuring protection is affordable and simple to apply for every household.</p>
            </div>

            <div class="p-5 sm:p-6 bg-slate-50 rounded-2xl border border-slate-200/80 transition hover:border-indigo-500/30">
                <div class="h-11 w-11 sm:h-12 sm:w-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xl mb-3 sm:mb-4">
                    ❤️
                </div>
                <h3 class="font-bold text-base sm:text-lg text-[#0B2A5B]">Compassion</h3>
                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">Delivering support with care, empathy, and speed when claims are made.</p>
            </div>

        </div>
    </div>
</section>


<!-- STATS BAR -->
<section class="py-12 sm:py-16 bg-[#0B2A5B] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 text-center">
            <div class="p-2">
                <div class="text-2xl sm:text-4xl font-black text-[#E8B923]">500+</div>
                <div class="text-xs sm:text-sm text-slate-300 mt-1 font-medium">Active Members</div>
            </div>
            <div class="p-2">
                <div class="text-2xl sm:text-4xl font-black text-[#E8B923]">13+</div>
                <div class="text-xs sm:text-sm text-slate-300 mt-1 font-medium">Benefit Categories</div>
            </div>
            <div class="p-2">
                <div class="text-2xl sm:text-4xl font-black text-[#E8B923]">0–100</div>
                <div class="text-xs sm:text-sm text-slate-300 mt-1 font-medium">Age Eligibility</div>
            </div>
            <div class="p-2">
                <div class="text-2xl sm:text-4xl font-black text-[#E8B923]">24/7</div>
                <div class="text-xs sm:text-sm text-slate-300 mt-1 font-medium">Community Support</div>
            </div>
        </div>
    </div>
</section>


<!-- CALL TO ACTION -->
<section class="py-12 sm:py-16 lg:py-20 bg-slate-50 text-center">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#0B2A5B] leading-tight">
            Ready to Protect What Matters Most?
        </h2>
        <p class="mt-3 sm:mt-4 text-sm sm:text-base lg:text-lg text-slate-600 max-w-2xl mx-auto">
            Join DAYONGAN today and give your family the financial protection and care they deserve.
        </p>

        <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 max-w-md sm:max-w-none mx-auto">
            <button
                onclick="Livewire.dispatch('open-application-modal')"
                class="w-full sm:w-auto px-8 py-3.5 bg-[#D4A017] hover:bg-[#E8B923] text-[#0B2A5B] font-extrabold rounded-xl shadow-lg shadow-[#D4A017]/20 transition duration-200 text-sm">
                Become a Member
            </button>
            <a
                href="{{ route('news') }}"
                class="w-full sm:w-auto px-8 py-3.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-bold rounded-xl transition duration-200 text-sm text-center">
                Read Latest News
            </a>
        </div>
    </div>
</section>

@endsection
