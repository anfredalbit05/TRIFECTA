@extends('layouts.app')

@section('title', 'Our Team')

@section('content')

<!-- TEAM HERO SECTION -->
<section class="relative bg-gradient-to-b from-[#0B2A5B] to-[#071D3F] py-20 lg:py-28 text-white overflow-hidden">
    <!-- Decorative Ambient Glows -->
    <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-[#D4A017]/20 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-[#163E7A]/50 blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 text-center">
        <span class="inline-block px-4 py-1.5 rounded-full bg-[#D4A017]/20 text-[#E8B923] font-bold tracking-widest uppercase text-xs sm:text-sm border border-[#D4A017]/30">
            Solidaritas • Vitae Auxilium
        </span>

        <h1 class="mt-6 text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
            The Hearts & Minds Behind <br class="hidden sm:block" />
            <span class="text-[#E8B923]">DAYONGAN</span>
        </h1>

        <p class="mt-6 max-w-3xl mx-auto text-base sm:text-lg text-slate-300 leading-relaxed">
            Dedicated leaders, compassionate community officers, and insurance professionals working together to safeguard every family we serve.
        </p>
    </div>
</section>


<!-- LEADERSHIP & EXECUTIVE BOARD -->
<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs sm:text-sm font-bold uppercase tracking-widest text-[#D4A017]">Leadership</span>
            <h2 class="mt-2 text-2xl sm:text-4xl font-extrabold text-[#0B2A5B]">Executive Leadership</h2>
            <p class="mt-3 text-slate-600">Guiding Dayongan with integrity, vision, and deep community commitment.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

            <!-- Leader 1 -->
            <div class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition duration-200 text-center p-6">
                <div class="w-32 h-32 mx-auto rounded-2xl overflow-hidden bg-slate-100 mb-4 border-2 border-[#D4A017]/30">
                    <img src="https://ui-avatars.com/api/?name=Executive+Director&background=0B2A5B&color=ffffff&size=256" alt="Executive Director" class="w-full h-full object-cover">
                </div>
                <h3 class="font-bold text-xl text-[#0B2A5B]">Executive Director</h3>
                <p class="text-xs font-bold uppercase text-[#D4A017] tracking-wider mt-1">Founder & Managing Head</p>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Leading strategic initiatives, insurance partner relations, and community outreach operations across Mindanao.
                </p>
            </div>

            <!-- Leader 2 -->
            <div class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition duration-200 text-center p-6">
                <div class="w-32 h-32 mx-auto rounded-2xl overflow-hidden bg-slate-100 mb-4 border-2 border-[#D4A017]/30">
                    <img src="https://ui-avatars.com/api/?name=Operations+Head&background=0B2A5B&color=ffffff&size=256" alt="Head of Operations" class="w-full h-full object-cover">
                </div>
                <h3 class="font-bold text-xl text-[#0B2A5B]">Head of Operations</h3>
                <p class="text-xs font-bold uppercase text-[#D4A017] tracking-wider mt-1">Membership & Claims</p>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Overseeing fast claim distributions, member onboarding, and compliance with COCOGEN guidelines.
                </p>
            </div>

            <!-- Leader 3 -->
            <div class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition duration-200 text-center p-6">
                <div class="w-32 h-32 mx-auto rounded-2xl overflow-hidden bg-slate-100 mb-4 border-2 border-[#D4A017]/30">
                    <img src="https://ui-avatars.com/api/?name=Finance+Officer&background=0B2A5B&color=ffffff&size=256" alt="Finance Officer" class="w-full h-full object-cover">
                </div>
                <h3 class="font-bold text-xl text-[#0B2A5B]">Chief Finance Officer</h3>
                <p class="text-xs font-bold uppercase text-[#D4A017] tracking-wider mt-1">Treasury & Auditing</p>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Ensuring strict financial transparency, fund sustainability, and secure benefit disbursement.
                </p>
            </div>

        </div>
    </div>
</section>


<!-- REGIONAL FIELD & BRANCH OFFICERS -->
<section class="py-20 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs sm:text-sm font-bold uppercase tracking-widest text-[#D4A017]">On The Ground</span>
            <h2 class="mt-2 text-2xl sm:text-4xl font-extrabold text-[#0B2A5B]">Branch & Field Support</h2>
            <p class="mt-3 text-slate-600">Connecting directly with communities in Tagum City, Kapalong, and nearby municipalities.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-blue-100 text-[#0B2A5B] flex items-center justify-center font-bold text-2xl mb-4">
                    📍
                </div>
                <h3 class="font-bold text-lg text-[#0B2A5B]">Tagum City Office</h3>
                <p class="text-xs font-semibold text-[#D4A017] mt-0.5">Main Branch Team</p>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">Handles walk-in inquiries, membership renewals, and local health perk reimbursements.</p>
            </div>

            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-2xl mb-4">
                    📍
                </div>
                <h3 class="font-bold text-lg text-[#0B2A5B]">Kapalong Branch</h3>
                <p class="text-xs font-semibold text-[#D4A017] mt-0.5">Municipal Helpdesk</p>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">Assists rural communities with micro-insurance registration and family beneficiary updates.</p>
            </div>

            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-2xl mb-4">
                    🚑
                </div>
                <h3 class="font-bold text-lg text-[#0B2A5B]">Medical Assistance Officers</h3>
                <p class="text-xs font-semibold text-[#D4A017] mt-0.5">Health Support Team</p>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">Coordinates hospital claim validations, P.E. reimbursements, and ultrasound support.</p>
            </div>

            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-2xl mb-4">
                    🤝
                </div>
                <h3 class="font-bold text-lg text-[#0B2A5B]">Community Organizers</h3>
                <p class="text-xs font-semibold text-[#D4A017] mt-0.5">Field Coordinators</p>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">Engaging barangays and groups to bring affordable insurance education directly to households.</p>
            </div>

        </div>
    </div>
</section>


<!-- COCOGEN PARTNERSHIP BANNER -->
<section class="py-16 bg-[#0B2A5B] text-white">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
        <div class="bg-white/10 backdrop-blur-md rounded-3xl p-8 sm:p-12 border border-white/20 flex flex-col lg:flex-row items-center justify-between gap-8">
            <div class="space-y-2 text-center lg:text-left">
                <span class="text-xs font-bold uppercase tracking-widest text-[#E8B923]">Strategic Alliance</span>
                <h3 class="text-2xl sm:text-3xl font-black">Backed by COCOGEN Insurance</h3>
                <p class="text-slate-300 text-sm max-w-2xl leading-relaxed">
                    Our team works hand-in-hand with licensed underwriters and advisors from COCOGEN to guarantee financial strength, underwriting accuracy, and prompt claim settlement.
                </p>
            </div>

            <div class="flex-shrink-0">
                <a href="#contact" class="inline-flex items-center px-6 py-3.5 bg-[#D4A017] hover:bg-[#E8B923] text-[#0B2A5B] font-bold rounded-xl shadow-lg transition duration-200 text-sm">
                    Contact Partner Desk
                </a>
            </div>
        </div>
    </div>
</section>


<!-- CALL TO ACTION -->
<section class="py-20 bg-slate-50 text-center">
    <div class="max-w-3xl mx-auto px-6 sm:px-8 lg:px-12">
        <h2 class="text-2xl sm:text-4xl font-extrabold text-[#0B2A5B]">
            Want to Join Our Mission?
        </h2>
        <p class="mt-4 text-slate-600 text-base sm:text-lg">
            We are always looking for passionate community advocates, field coordinators, and partner organizations.
        </p>

        <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
            <a href="#register" class="px-8 py-3.5 bg-[#0B2A5B] hover:bg-blue-900 text-white font-bold rounded-xl shadow-lg transition duration-200">
                Become a Community Representative
            </a>
            <a href="#contact" class="px-8 py-3.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-bold rounded-xl transition duration-200">
                Visit Branch Office
            </a>
        </div>
    </div>
</section>

@endsection
