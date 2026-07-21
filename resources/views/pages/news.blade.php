@extends('layouts.app')

@section('title', 'News & Updates')

@section('content')

<!-- HERO SECTION / FEATURED STORY -->
<section class="relative bg-gradient-to-b from-[#0B2A5B] to-[#071D3F] py-20 lg:py-28 text-white overflow-hidden">
    <!-- Decorative Ambient Glows -->
    <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-[#D4A017]/20 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-[#163E7A]/50 blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">

        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-block px-4 py-1.5 rounded-full bg-[#D4A017]/20 text-[#E8B923] font-bold tracking-widest uppercase text-xs sm:text-sm border border-[#D4A017]/30">
                Solidaritas • Vitae Auxilium
            </span>
            <h1 class="mt-4 text-3xl sm:text-5xl font-black tracking-tight leading-tight">
                News, Stories & <span class="text-[#E8B923]">Community Updates</span>
            </h1>
            <p class="mt-4 text-base sm:text-lg text-slate-300">
                Stay informed with the latest announcements, branch updates, claim stories, and official partnerships from DAYONGAN.
            </p>
        </div>

        <!-- FEATURED POST BANNER -->
        <div class="bg-white/10 backdrop-blur-md rounded-3xl border border-white/20 overflow-hidden grid grid-cols-1 lg:grid-cols-12 shadow-2xl">
            <div class="lg:col-span-7 relative h-64 sm:h-80 lg:h-auto">
                <img src="{{ asset('images/hero-bg.jpg') }}" alt="COCOGEN Partnership Expansion" class="w-full h-full object-cover">
                <div class="absolute top-4 left-4 bg-[#D4A017] text-[#0B2A5B] text-xs font-extrabold uppercase px-3 py-1 rounded-full shadow">
                    Featured Update
                </div>
            </div>

            <div class="lg:col-span-5 p-6 sm:p-8 lg:p-10 flex flex-col justify-between">
                <div>
                    <span class="text-xs text-[#E8B923] font-semibold uppercase tracking-wider block">July 2026 • Partnership</span>
                    <h2 class="text-xl sm:text-2xl font-bold mt-2 leading-snug">
                        DAYONGAN Expands Community Micro-Insurance Coverage in Davao Region with COCOGEN
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-300 mt-3 leading-relaxed">
                        Through reinforced underwriting cooperation, DAYONGAN and COCOGEN Insurance bring expanded medical reimbursement and family death assistance capabilities to thousands of households in Tagum City and Kapalong.
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between">
                    <span class="text-xs text-slate-400">By Dayongan Media Team</span>
                    <a href="#article-1" class="text-xs font-bold text-[#E8B923] hover:underline flex items-center gap-1">
                        Read Full Story &rarr;
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>


<!-- NEWS CATEGORY FILTER BAR -->
<section class="py-6 bg-slate-100 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 flex flex-wrap items-center justify-between gap-4">

        <div class="flex items-center gap-2 overflow-x-auto pb-2 sm:pb-0">
            <button class="px-4 py-2 bg-[#0B2A5B] text-white text-xs font-bold rounded-full shadow-sm">
                All Articles
            </button>
            <button class="px-4 py-2 bg-white text-slate-600 hover:bg-slate-200 text-xs font-bold rounded-full border border-slate-200 transition">
                Announcements
            </button>
            <button class="px-4 py-2 bg-white text-slate-600 hover:bg-slate-200 text-xs font-bold rounded-full border border-slate-200 transition">
                Partnerships
            </button>
            <button class="px-4 py-2 bg-white text-slate-600 hover:bg-slate-200 text-xs font-bold rounded-full border border-slate-200 transition">
                Community Stories
            </button>
            <button class="px-4 py-2 bg-white text-slate-600 hover:bg-slate-200 text-xs font-bold rounded-full border border-slate-200 transition">
                Health & Wellness
            </button>
        </div>

        <div class="text-xs text-slate-500 font-medium">
            Showing <span class="font-bold text-[#0B2A5B]">6 Latest Updates</span>
        </div>

    </div>
</section>


<!-- ARTICLE GRID SECTION -->
<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            <!-- Article 1 -->
            <article class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
                <div>
                    <div class="relative h-48 bg-slate-100">
                        <img src="{{ asset('images/hero-bg.jpg') }}" alt="Health Perks Launch" class="w-full h-full object-cover">
                        <span class="absolute top-3 left-3 bg-emerald-600 text-white text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full">
                            Health Perks
                        </span>
                    </div>
                    <div class="p-6">
                        <span class="text-xs font-medium text-slate-400">June 28, 2026</span>
                        <h3 class="text-lg font-bold text-[#0B2A5B] mt-1 leading-snug">
                            Annual Physical Exam & Dental Checkup Reimbursements Now Active
                        </h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Primary account holders can now present accredited receipts at our branch desks to claim physical exam and dental perks.
                        </p>
                    </div>
                </div>
                <div class="p-6 pt-0 border-t border-slate-100 mt-4 flex items-center justify-between">
                    <span class="text-xs font-semibold text-[#D4A017]">3 min read</span>
                    <a href="#read" class="text-xs font-bold text-[#0B2A5B] hover:text-[#D4A017]">Read More &rarr;</a>
                </div>
            </article>

            <!-- Article 2 -->
            <article class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
                <div>
                    <div class="relative h-48 bg-slate-100">
                        <img src="{{ asset('images/hero-bg.jpg') }}" alt="Kapalong Desk Opening" class="w-full h-full object-cover">
                        <span class="absolute top-3 left-3 bg-blue-600 text-white text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full">
                            Branch Expansion
                        </span>
                    </div>
                    <div class="p-6">
                        <span class="text-xs font-medium text-slate-400">June 15, 2026</span>
                        <h3 class="text-lg font-bold text-[#0B2A5B] mt-1 leading-snug">
                            Kapalong Helpdesk Opens to Support Rural Micro-Insurance Enrollment
                        </h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Dedicated support staff in Kapalong are now ready to assist walk-in applicants with family member registrations and claim filings.
                        </p>
                    </div>
                </div>
                <div class="p-6 pt-0 border-t border-slate-100 mt-4 flex items-center justify-between">
                    <span class="text-xs font-semibold text-[#D4A017]">2 min read</span>
                    <a href="#read" class="text-xs font-bold text-[#0B2A5B] hover:text-[#D4A017]">Read More &rarr;</a>
                </div>
            </article>

            <!-- Article 3 -->
            <article class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
                <div>
                    <div class="relative h-48 bg-slate-100">
                        <img src="{{ asset('images/hero-bg.jpg') }}" alt="Fast Track Claims" class="w-full h-full object-cover">
                        <span class="absolute top-3 left-3 bg-amber-500 text-white text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full">
                            Service Improvement
                        </span>
                    </div>
                    <div class="p-6">
                        <span class="text-xs font-medium text-slate-400">May 30, 2026</span>
                        <h3 class="text-lg font-bold text-[#0B2A5B] mt-1 leading-snug">
                            Fast-Track Claim Verification System Reduced Turnaround Time
                        </h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Our streamlined digital integration with COCOGEN underwriters allows natural death and accident payouts to be processed even faster.
                        </p>
                    </div>
                </div>
                <div class="p-6 pt-0 border-t border-slate-100 mt-4 flex items-center justify-between">
                    <span class="text-xs font-semibold text-[#D4A017]">4 min read</span>
                    <a href="#read" class="text-xs font-bold text-[#0B2A5B] hover:text-[#D4A017]">Read More &rarr;</a>
                </div>
            </article>

            <!-- Article 4 -->
            <article class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
                <div>
                    <div class="relative h-48 bg-slate-100">
                        <img src="{{ asset('images/hero-bg.jpg') }}" alt="Community Outreach" class="w-full h-full object-cover">
                        <span class="absolute top-3 left-3 bg-indigo-600 text-white text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full">
                            Outreach
                        </span>
                    </div>
                    <div class="p-6">
                        <span class="text-xs font-medium text-slate-400">May 12, 2026</span>
                        <h3 class="text-lg font-bold text-[#0B2A5B] mt-1 leading-snug">
                            Barangay Financial Literacy & Micro-Insurance Awareness Drive
                        </h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Community officers conducted informational workshops across Tagum City to educate household heads about emergency protection.
                        </p>
                    </div>
                </div>
                <div class="p-6 pt-0 border-t border-slate-100 mt-4 flex items-center justify-between">
                    <span class="text-xs font-semibold text-[#D4A017]">3 min read</span>
                    <a href="#read" class="text-xs font-bold text-[#0B2A5B] hover:text-[#D4A017]">Read More &rarr;</a>
                </div>
            </article>

            <!-- Article 5 -->
            <article class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
                <div>
                    <div class="relative h-48 bg-slate-100">
                        <img src="{{ asset('images/hero-bg.jpg') }}" alt="Beneficiary Updates" class="w-full h-full object-cover">
                        <span class="absolute top-3 left-3 bg-rose-600 text-white text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full">
                            Member Advisory
                        </span>
                    </div>
                    <div class="p-6">
                        <span class="text-xs font-medium text-slate-400">April 22, 2026</span>
                        <h3 class="text-lg font-bold text-[#0B2A5B] mt-1 leading-snug">
                            Important Advisory: How to Keep Your Beneficiary Information Updated
                        </h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Ensure all three of your declared dependents (aged 0–100) are accurately listed on your policy before your annual renewal.
                        </p>
                    </div>
                </div>
                <div class="p-6 pt-0 border-t border-slate-100 mt-4 flex items-center justify-between">
                    <span class="text-xs font-semibold text-[#D4A017]">2 min read</span>
                    <a href="#read" class="text-xs font-bold text-[#0B2A5B] hover:text-[#D4A017]">Read More &rarr;</a>
                </div>
            </article>

            <!-- Article 6 -->
            <article class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
                <div>
                    <div class="relative h-48 bg-slate-100">
                        <img src="{{ asset('images/hero-bg.jpg') }}" alt="Gold Shield Tier" class="w-full h-full object-cover">
                        <span class="absolute top-3 left-3 bg-[#D4A017] text-[#0B2A5B] text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full">
                            Coverage Tiers
                        </span>
                    </div>
                    <div class="p-6">
                        <span class="text-xs font-medium text-slate-400">April 05, 2026</span>
                        <h3 class="text-lg font-bold text-[#0B2A5B] mt-1 leading-snug">
                            Understanding Your Gold Shield Coverage Maximum Benefits (₱250,000)
                        </h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            A breakdown of critical illness support, disability assistance, and hospitalization coverage under the premier Gold tier.
                        </p>
                    </div>
                </div>
                <div class="p-6 pt-0 border-t border-slate-100 mt-4 flex items-center justify-between">
                    <span class="text-xs font-semibold text-[#D4A017]">5 min read</span>
                    <a href="#read" class="text-xs font-bold text-[#0B2A5B] hover:text-[#D4A017]">Read More &rarr;</a>
                </div>
            </article>

        </div>
    </div>
</section>


<!-- NEWSLETTER / UPDATES SUBSCRIPTION -->
<section class="py-20 bg-[#0B2A5B] text-white">
    <div class="max-w-4xl mx-auto px-6 sm:px-8 lg:px-12 text-center">
        <span class="text-xs font-bold uppercase tracking-widest text-[#E8B923]">Never Miss an Update</span>
        <h2 class="mt-2 text-2xl sm:text-4xl font-extrabold">
            Subscribe to Official Bulletins
        </h2>
        <p class="mt-3 text-slate-300 text-xs sm:text-sm max-w-xl mx-auto leading-relaxed">
            Get official announcements, policy advisories, and community health perk updates sent directly to your email inbox or phone.
        </p>

        <form class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3 max-w-lg mx-auto">
            <input type="email" placeholder="Enter your email address" class="w-full px-5 py-3.5 rounded-xl bg-white/10 border border-white/20 text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#D4A017]">
            <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-[#D4A017] hover:bg-[#E8B923] text-[#0B2A5B] font-bold rounded-xl shadow-lg transition duration-200 text-sm flex-shrink-0">
                Subscribe Now
            </button>
        </form>
    </div>
</section>

@endsection
