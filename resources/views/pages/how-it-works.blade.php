@extends('layouts.app')

@section('title', 'How It Works')

@section('content')

<!-- HERO SECTION -->
<section class="relative bg-gradient-to-b from-[#0B2A5B] to-[#071D3F] py-20 lg:py-28 text-white overflow-hidden">
    <!-- Decorative Ambient Glows -->
    <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-[#D4A017]/20 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-[#163E7A]/50 blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 text-center">
        <span class="inline-block px-4 py-1.5 rounded-full bg-[#D4A017]/20 text-[#E8B923] font-bold tracking-widest uppercase text-xs sm:text-sm border border-[#D4A017]/30">
            Solidaritas • Vitae Auxilium
        </span>

        <h1 class="mt-6 text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
            Simple, Transparent & Fast <br class="hidden sm:block" />
            <span class="text-[#E8B923]">Community Protection</span>
        </h1>

        <p class="mt-6 max-w-3xl mx-auto text-base sm:text-lg text-slate-300 leading-relaxed">
            Discover how DAYONGAN and <strong>COCOGEN Insurance</strong> make micro-insurance and health assistance effortless for primary members and their families.
        </p>
    </div>
</section>


<!-- 4-STEP MEMBERSHIP PROCESS -->
<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs sm:text-sm font-bold uppercase tracking-widest text-[#D4A017]">Simple Journey</span>
            <h2 class="mt-2 text-2xl sm:text-4xl font-extrabold text-[#0B2A5B]">How To Get Covered</h2>
            <p class="mt-3 text-slate-600">Start enjoying complete peace of mind in four easy steps.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

            <!-- Step 1 -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm relative flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-[#0B2A5B] text-[#E8B923] flex items-center justify-center font-black text-xl mb-4">
                        01
                    </div>
                    <h3 class="font-bold text-lg text-[#0B2A5B]">Register Account</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        Fill out the application form online or visit our Tagum City / Kapalong offices. Primary owners aged 18–65 are eligible.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 text-xs font-semibold text-[#D4A017]">
                    Takes ~5 minutes
                </div>
            </div>

            <!-- Step 2 -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm relative flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-[#0B2A5B] text-[#E8B923] flex items-center justify-center font-black text-xl mb-4">
                        02
                    </div>
                    <h3 class="font-bold text-lg text-[#0B2A5B]">Add Beneficiaries</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        Enroll up to 3 family beneficiaries (ages 0–100) to extend natural death claims, burial assistance, and health support.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 text-xs font-semibold text-[#D4A017]">
                    Includes up to 3 dependents
                </div>
            </div>

            <!-- Step 3 -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm relative flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-[#0B2A5B] text-[#E8B923] flex items-center justify-center font-black text-xl mb-4">
                        03
                    </div>
                    <h3 class="font-bold text-lg text-[#0B2A5B]">Activate Coverage</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        Pay the annual plan (₱1,999/year—only ~₱6/day). Receive your official membership card and policy document backed by COCOGEN.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 text-xs font-semibold text-[#D4A017]">
                    Instant Policy Issuance
                </div>
            </div>

            <!-- Step 4 -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm relative flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-[#0B2A5B] text-[#E8B923] flex items-center justify-center font-black text-xl mb-4">
                        04
                    </div>
                    <h3 class="font-bold text-lg text-[#0B2A5B]">Access Benefits</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        Enjoy annual physical exams, medical reimbursements, dental perks, and hassle-free claim processing anytime.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 text-xs font-semibold text-[#D4A017]">
                    24/7 Dedicated Support
                </div>
            </div>

        </div>
    </div>
</section>


<!-- HOW CLAIMS WORK -->
<section class="py-20 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

            <!-- Left Info -->
            <div class="lg:col-span-6 space-y-6">
                <span class="text-xs sm:text-sm font-bold uppercase tracking-widest text-[#D4A017]">Hassle-Free Processing</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-[#0B2A5B] leading-tight">
                    Filing a Claim or Benefit Request
                </h2>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    When emergencies happen, the last thing you need is complex paperwork. DAYONGAN streamlines claim submissions directly through our local desks or online dashboard.
                </p>

                <div class="space-y-4 pt-2">
                    <div class="flex items-start space-x-4 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#0B2A5B] text-[#E8B923] flex items-center justify-center font-bold text-sm">1</div>
                        <div>
                            <h4 class="font-bold text-[#0B2A5B] text-sm">Submit Documents</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Present your DAYONGAN ID and basic claim documents (medical certificate, receipts, or official reports).</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#0B2A5B] text-[#E8B923] flex items-center justify-center font-bold text-sm">2</div>
                        <div>
                            <h4 class="font-bold text-[#0B2A5B] text-sm">COCOGEN Underwriting Verification</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Our fast-track evaluation system verifies details directly with partner underwriters.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#0B2A5B] text-[#E8B923] flex items-center justify-center font-bold text-sm">3</div>
                        <div>
                            <h4 class="font-bold text-[#0B2A5B] text-sm">Fast Disbursement</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Approved benefit payouts are released promptly via bank transfer, e-wallet, or direct office pickup.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Box Card -->
            <div class="lg:col-span-6">
                <div class="bg-gradient-to-br from-[#0B2A5B] to-[#071D3F] rounded-3xl p-8 sm:p-10 text-white shadow-xl relative overflow-hidden">
                    <div class="absolute -top-20 -right-20 w-64 h-64 bg-[#D4A017]/20 rounded-full blur-2xl pointer-events-none"></div>

                    <span class="text-xs font-bold uppercase tracking-widest text-[#E8B923]">Coverage Tiers</span>
                    <h3 class="text-2xl font-bold mt-2">What Tier Am I Under?</h3>
                    <p class="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed">
                        DAYONGAN offers flexible shield tiers tailored to different levels of coverage requirements.
                    </p>

                    <div class="mt-6 space-y-3">
                        <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 flex items-center justify-between">
                            <div>
                                <h5 class="font-bold text-sm text-[#E8B923]">Bronze Shield</h5>
                                <p class="text-xs text-slate-300">Basic Micro-Insurance & Assistance</p>
                            </div>
                            <span class="text-lg font-black text-white">₱75,000</span>
                        </div>

                        <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 flex items-center justify-between">
                            <div>
                                <h5 class="font-bold text-sm text-slate-200">Silver Shield</h5>
                                <p class="text-xs text-slate-300">Enhanced Family & Health Perks</p>
                            </div>
                            <span class="text-lg font-black text-white">₱150,000</span>
                        </div>

                        <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 flex items-center justify-between">
                            <div>
                                <h5 class="font-bold text-sm text-[#E8B923]">Gold Shield</h5>
                                <p class="text-xs text-slate-300">Maximum Protection & High Hospital Support</p>
                            </div>
                            <span class="text-lg font-black text-white">₱250,000</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- FREQUENTLY ASKED QUESTIONS -->
<section class="py-20 bg-slate-50">
    <div class="max-w-4xl mx-auto px-6 sm:px-8 lg:px-12">
        <div class="text-center mb-12">
            <span class="text-xs sm:text-sm font-bold uppercase tracking-widest text-[#D4A017]">Got Questions?</span>
            <h2 class="mt-2 text-2xl sm:text-4xl font-extrabold text-[#0B2A5B]">Frequently Asked Questions</h2>
        </div>

        <div class="space-y-4">

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="font-bold text-[#0B2A5B] text-base">Who can apply as the Primary Owner?</h3>
                <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                    Any individual aged 18 to 65 years old can register as a Primary Owner. Once enrolled, coverage remains active as long as annual renewals are maintained.
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="font-bold text-[#0B2A5B] text-base">Who can be declared as beneficiaries?</h3>
                <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                    You can register up to 3 immediate family members or dependents aged 0 to 100 years old. Beneficiaries receive natural death claims, burial assistance, and medical perk eligibility.
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="font-bold text-[#0B2A5B] text-base">How does the COCOGEN Insurance partnership work?</h3>
                <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                    DAYONGAN operates in official partnership with COCOGEN Insurance, ensuring all micro-insurance policies are legally backed, underwritten, and guaranteed by one of the country's leading insurance institutions.
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="font-bold text-[#0B2A5B] text-base">Where are the official offices located?</h3>
                <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                    We have dedicated walk-in branch offices in Tagum City and Kapalong, Davao del Norte, open Monday through Saturday for claims, registration, and consultations.
                </p>
            </div>

        </div>
    </div>
</section>


<!-- CALL TO ACTION -->
<section class="py-20 bg-[#0B2A5B] text-white text-center">
    <div class="max-w-3xl mx-auto px-6 sm:px-8 lg:px-12">
        <h2 class="text-2xl sm:text-4xl font-extrabold">
            Ready to Protect Your Family?
        </h2>
        <p class="mt-4 text-slate-300 text-base sm:text-lg">
            Join thousands of covered households today for as low as ₱1,999 per year.
        </p>

        <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
            <a href="#register" class="px-8 py-3.5 bg-[#D4A017] hover:bg-[#E8B923] text-[#0B2A5B] font-bold rounded-xl shadow-lg transition duration-200">
                Apply Online Now
            </a>
            <a href="#contact" class="px-8 py-3.5 bg-white/10 hover:bg-white/20 text-white border border-white/30 font-bold rounded-xl transition duration-200">
                Contact Customer Support
            </a>
        </div>
    </div>
</section>

@endsection
