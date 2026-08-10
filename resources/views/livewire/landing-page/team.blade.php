<div>

<!-- =========================
     TEAM HERO SECTION
========================== -->
<section class="team-hero">

    <div class="team-hero-glow-top"></div>
    <div class="team-hero-glow-bottom"></div>

    <div class="relative max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 text-center">

        <span class="team-hero-badge">
            <x-editable-text
                key="team_hero_badge"
                :value="$contexts['team_hero_badge'] ?? 'TrueLife Dayongan Solidaritas Social Welfare Services, Inc.'"
                tag="span"
                class="inline-block"
            />
        </span>

        <h1 class="team-hero-title">
            <x-editable-text
                key="team_hero_title_1"
                :value="$contexts['team_hero_title_1'] ?? 'The Hearts & Minds Behind'"
                tag="span"
                class="inline-block"
            />
            <br class="hidden sm:block">

            <span class="team-hero-title-highlight">
                <x-editable-text
                    key="team_hero_title_highlight"
                    :value="$contexts['team_hero_title_highlight'] ?? 'DAYONGAN'"
                    tag="span"
                    class="inline-block"
                />
            </span>
        </h1>

       <p class="team-hero-description">
            <x-editable-text
                key="team_hero_description"
                :value="$contexts['team_hero_description'] ?? 'Dedicated leaders, compassionate community officers, and insurance professionals working together to safeguard every family we serve. Together, we build stronger communities through trust, commitment, and reliable support. Our team is driven by a shared mission to provide meaningful assistance, protect what matters most, and create a safer future for everyone.'"
                tag="span"
                class="block"
            />
       </p>

    </div>

</section>


<!-- =========================
     CEO SECTION
========================== -->

<section class="relative overflow-hidden bg-slate-100 py-24">

    <!-- background decoration -->


    <div class="absolute top-0 left-0 h-96 w-96 rounded-full bg-blue-100 blur-3xl opacity-40"></div>

    <div class="absolute bottom-0 right-0 h-96 w-96 rounded-full bg-yellow-100 blur-3xl opacity-40"></div>

    <div class="max-w-7xl mx-auto px-6">

        <div class="overflow-hidden rounded-[40px] bg-white shadow-2xl">

            <div class="grid lg:grid-cols-12 min-h-[950px]">

                <!-- ========================= -->
                <!-- LEFT CONTENT -->
                <!-- ========================= -->

                <div class="lg:col-span-7 p-8 lg:p-14 flex flex-col">

                    <!-- Badge -->

                    <div>

                        <span class="inline-flex items-center gap-2 rounded-full bg-blue-900 px-5 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white">

                            The Leadership Team

                        </span>

                    </div>

                    <!-- Heading -->

                    <h1 class="mt-8 text-5xl lg:text-7xl font-black leading-[0.95] text-slate-900">

                        STRONG

                        <span class="text-blue-900">

                            LEADERSHIP.

                        </span>

                        <br>

                        GREATER

                        <span class="text-yellow-500">

                            IMPACT.

                        </span>

                    </h1>

                    <!-- Description -->

                    <p class="mt-8 max-w-2xl text-lg leading-relaxed text-slate-600">

                        <x-editable-text
                            key="team_ceo_description"
                            :value="$contexts['team_ceo_description'] ?? 'Driven by purpose and dedicated to service, our leadership continues to inspire communities through integrity, professionalism, and compassion.'"
                            tag="span"
                        />

                    </p>

                    <!-- CEO Card -->

                    <div class="mt-10 rounded-[30px] border border-slate-200 bg-slate-50 p-8">

                        <p class="text-sm font-bold uppercase tracking-[0.3em] text-blue-900">

                             THE CEO

                        </p>

                        <h2 class="mt-3 text-4xl font-black text-slate-900">

                            <x-editable-text
                                key="team_ceo_name"
                                :value="$contexts['team_ceo_name'] ?? 'RENANTE SUAYBAGUIO-ROBLES, MPA, LFA'"
                                tag="span"
                            />

                        </h2>

                        <p class="mt-4 text-xl font-bold text-yellow-600">

                           Leader • Servant • Professional

                        </p>

                    </div>

                    <!-- Experience -->

                    <div class="mt-10 grid gap-6 md:grid-cols-2">

                        <!-- Public -->

                        <div class="rounded-3xl border border-slate-200 p-6 shadow-sm">

                            <div class="mb-4 flex items-center gap-4">

                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-900 text-2xl text-white">

                                    🏛️

                                </div>

                                <div>

                                    <h3 class="font-bold uppercase text-slate-900">

                                        Public Service

                                    </h3>

                                    <p class="text-sm text-slate-500">

                                        Experience

                                    </p>

                                </div>

                            </div>

                            <ul class="space-y-2 text-slate-600">

                                <li>• Former SK Chairman</li>

                                <li>• Former Barangay Kagawad</li>

                                <li>• Former Barangay Captain</li>

                                <li>• Former City Councilor of Tagum City</li>


                            </ul>

                        </div>

                        <!-- Professional -->

                        <div class="rounded-3xl border border-slate-200 p-6 shadow-sm">

                            <div class="mb-4 flex items-center gap-4">

                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-yellow-500 text-2xl text-white">

                                    💼

                                </div>

                                <div>

                                    <h3 class="font-bold uppercase text-slate-900">

                                        Professional

                                    </h3>

                                    <p class="text-sm text-slate-500">

                                        Experience

                                    </p>

                                </div>

                            </div>

                            <ul class="space-y-2 text-slate-600">

                                <li>• Government-Licensed Financial Advisor</li>

                                <li>• Unit Head, Manulife Philippines</li>

                                <li>• Marketing Director, IMG (International Marketing Group)</li>


                            </ul>

                        </div>

                    </div>

                    <!-- Academic -->

                    <div class="mt-6 rounded-3xl bg-blue-900 p-8 text-white">

                        <div class="flex items-center gap-4">

                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white text-2xl">

                                🎓

                            </div>

                            <div>

                                <h3 class="text-2xl font-bold">

                                    Academic Background

                                </h3>

                                <p class="text-blue-100">

                                    College Professor specializing in

                                    <strong>Business and Marketing</strong>

                                </p>

                            </div>

                        </div>

                    </div>

                    <!-- Leadership Profile -->

                    <div class="mt-6 rounded-[30px] bg-gradient-to-r from-slate-900 to-blue-950 p-8 text-white">

                        <h3 class="text-2xl font-bold text-yellow-400">

                            Leadership Profile

                        </h3>

                        <div class="mt-4 h-1 w-24 rounded bg-yellow-500"></div>

                        <p class="mt-5 leading-relaxed text-slate-300">

                            With extensive experience in public service,
                            financial advisory, leadership development,
                            and higher education, our leaders empower
                            communities through service, financial literacy,
                            and professional excellence.

                        </p>

                    </div>

                    <!-- Button -->
{{--
                    <button
                        wire:click="openCeoModal"
                        class="mt-8 w-fit rounded-2xl bg-yellow-500 px-8 py-4 font-bold text-slate-900 transition hover:scale-105">

                        Learn More →

                    </button> --}}

                </div>

                <!-- ========================= -->
                <!-- RIGHT SIDE -->
                <!-- ========================= -->

                <div class="relative lg:col-span-5 overflow-hidden bg-gradient-to-b from-blue-950 via-blue-900 to-slate-900">

                    <!-- decorative -->

                    <div class="absolute top-0 right-0 h-96 w-96 rounded-full bg-yellow-400/20 blur-3xl"></div>

                    <div class="absolute bottom-0 left-0 h-80 w-80 rounded-full bg-blue-400/20 blur-3xl"></div>


                    <!-- image -->

                    <div class="flex h-full items-end justify-center">

                        <img
                            src="{{ asset('images/ceo.png') }}"
                            alt="CEO"
                            class="relative z-10 h-[1200px] object-contain object-bottom"
                        >

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- =========================
     COO SECTION
========================== -->

<section class="py-24 bg-slate-100">

    <div class="max-w-7xl mx-auto px-6">

        <div class="overflow-hidden rounded-[40px] bg-white shadow-2xl">

            <div class="grid lg:grid-cols-12 min-h-[950px]">

                   <!-- ========================= -->
                <!-- RIGHT IMAGE -->
                <!-- ========================= -->

                <div class="relative lg:col-span-5 overflow-hidden bg-gradient-to-b from-blue-950 to-slate-900">

                    <!-- Decorations -->

                    <div class="absolute top-10 right-10 h-40 w-40 rounded-full bg-yellow-400/20 blur-3xl"></div>

                    <div class="absolute bottom-10 left-10 h-40 w-40 rounded-full bg-blue-400/20 blur-3xl"></div>

                    <!-- Gold line -->

                    <div class="absolute left-0 top-0 h-full w-1 bg-yellow-500"></div>

                    <!-- Image -->

                    <div class="flex h-full items-end justify-center">

                        <img
                            src="{{ asset('images/coo.png') }}"
                            alt="COO"
                            class="relative z-10 h-[800px] object-contain object-bottom"
                        >

                    </div>


                </div>

                <div class="lg:col-span-7 p-10 lg:p-14 flex flex-col justify-center">

                    <!-- Badge -->

                    <div>

                        <span class="inline-flex rounded-full bg-blue-900 px-5 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white">

                            Vice President for

                        </span>

                    </div>

                    <!-- Position -->

                    <h2 class="mt-5 text-4xl lg:text-5xl font-black uppercase leading-tight text-slate-900">

                        Marketing and Operations

                    </h2>

                    <!-- Name -->

                    <h1 class="mt-6 text-5xl font-black uppercase text-blue-900">

                        Bobby Paulo Dagala, LFA

                    </h1>

                    <!-- Subtitle -->

                    <p class="mt-4 text-2xl font-semibold text-yellow-600">

                        A Government Licensed Financial Advisor

                    </p>

                    <!-- Divider -->

                    <div class="mt-8 h-1 w-32 rounded-full bg-yellow-500"></div>

                    <!-- Cards -->

                    <div class="mt-10 grid gap-6">

                        <!-- Experience -->

                        <div class="rounded-3xl border border-slate-200 p-6 shadow-sm">

                            <div class="flex items-center gap-4">

                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-900 text-white text-2xl">

                                    🛡️

                                </div>

                                <div>

                                    <h3 class="text-2xl font-bold text-slate-900">

                                        20 Years

                                    </h3>

                                    <p class="text-slate-500">

                                        in Insurance Industry

                                    </p>

                                </div>

                            </div>

                        </div>

                        <!-- Owner/Founder -->

                        <div class="rounded-3xl border border-slate-200 p-6 shadow-sm">

                            <div class="flex items-center gap-4">

                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-900 text-white text-2xl">

                                    👥

                                </div>

                                <div>

                                    <h3 class="text-2xl font-bold text-slate-900">

                                        Owner/Founder

                                    </h3>

                                    <p class="text-slate-500 leading-relaxed">

                                        TRI-CARE Community Development
                                        and Consultancy Services

                                    </p>

                                </div>

                            </div>

                        </div>

                        <!-- Business -->

                        <div class="rounded-3xl border border-slate-200 p-6 shadow-sm">

                            <div class="flex items-center gap-4">

                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-yellow-500 text-white text-2xl">

                                    🏢

                                </div>

                                <div>

                                    <h3 class="text-2xl font-bold text-slate-900">

                                        Business Owner

                                    </h3>

                                    <p class="text-slate-500 leading-relaxed">

                                        Ultrasound Clinics in Davao
                                        Region

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

{{-- @if($showCeoModal)

<div
    class="fixed inset-0 z-[9999] bg-black/70 backdrop-blur-sm p-4 flex items-center justify-center">

    <div
        class="relative w-full max-w-7xl max-h-[95vh] overflow-y-auto rounded-3xl bg-white shadow-2xl">

        <!-- Close -->

        <button
            wire:click="closeCeoModal"
            class="absolute right-6 top-6 z-50 w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200">

            ✕

        </button>

        @include('livewire.landing-page.partials.ceo-story')

    </div>

</div>

@endif --}}


<!-- =========================
     BOARD MEMBERS SECTION
========================== -->
<section class="team-board-section">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">

        <div class="team-board-header">
            <span class="team-board-subtitle">
                <x-editable-text
                    key="team_board_subtitle"
                    :value="$contexts['team_board_subtitle'] ?? 'Dayongan Leadership'"
                    tag="span"
                    class="inline-block"
                />
            </span>

            <h2 class="team-board-title">
                <x-editable-text
                    key="team_board_title"
                    :value="$contexts['team_board_title'] ?? 'Board Members'"
                    tag="span"
                    class="inline-block"
                />
            </h2>

            <p class="team-board-description">
                <x-editable-text
                    key="team_board_description"
                    :value="$contexts['team_board_description'] ?? 'Dedicated leaders committed to serving the organization with integrity, transparency, and excellence.'"
                    tag="span"
                    class="block"
                />
            </p>
        </div>

        <div class="team-board-grid">

            @php
            $boardMembers = [
                ['name'=>'Princes Manliguez','position'=>'Secretary', 'contact' => '09266958032'],
                ['name'=>'Dr. Maria Carmelita','position'=>'Auditor', 'contact' => '09565003849'],
                ['name'=>'Dr. Cherry Lynn Nierras-Dagala','position'=>'Treasurer', 'contact' => '09055596558'],
                ['name'=>'Mr. Alfe Duron','position'=>'Board of Directors', 'contact' => '09933668594'],
            ];
            @endphp

            @foreach($boardMembers as $member)
            <div class="team-board-card">
                <div class="team-board-card-body">
                    <img
                        src="{{ asset('images/user.png') }}"
                        alt="{{ $member['name'] }}"
                        class="team-board-avatar">

                    <h3 class="team-board-name">
                        {{ $member['name'] }}
                    </h3>

                    @isset($member['contact'])
                        <p>{{ $member['contact'] }}</p>
                    @endisset

                    <p class="team-board-position">
                        {{ $member['position'] }}
                    </p>
                </div>
            </div>
            @endforeach

        </div>

    </div>
</section>


<!-- =========================
     BRANCH & FIELD SUPPORT
========================== -->
<section class="team-field-section">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
        <div class="team-field-header">
            <span class="team-field-subtitle">
                <x-editable-text
                    key="team_field_subtitle"
                    :value="$contexts['team_field_subtitle'] ?? 'On The Ground'"
                    tag="span"
                    class="inline-block"
                />
            </span>
            <h2 class="team-field-title">
                <x-editable-text
                    key="team_field_title"
                    :value="$contexts['team_field_title'] ?? 'Branch & Field Support'"
                    tag="span"
                    class="inline-block"
                />
            </h2>
            <p class="team-field-description">
                <x-editable-text
                    key="team_field_description"
                    :value="$contexts['team_field_description'] ?? 'Connecting directly with communities in Tagum City, Kapalong, and nearby municipalities.'"
                    tag="span"
                    class="block"
                />
            </p>
        </div>

        <div class="team-field-grid">

            @foreach($branches as $branch)
            <div class="team-field-card">
                <div class="team-field-icon-box team-icon-blue">
                    <span class="inline-block">
                        📍
                    </span>
                </div>
                <h3 class="team-field-card-title">
                    <span class="inline-block">
                        {{ $branch->branchname }}
                    </span>
                </h3>
                <p class="team-field-card-text">
                    <span class="block">
                        {{ $branch->address }}
                    </span>
                </p>
            </div>
            @endforeach

        </div>
    </div>
</section>


<!-- =========================
     STRATEGIC ALLIANCE BANNER
========================== -->
<section class="team-partner-section">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
        <div class="team-partner-card">
            <div class="team-partner-content">
                <span class="team-partner-subtitle">
                    <x-editable-text
                        key="team_partner_subtitle"
                        :value="$contexts['team_partner_subtitle'] ?? 'Strategic Alliance'"
                        tag="span"
                        class="inline-block"
                    />
                </span>
                <h3 class="team-partner-title">
                    <x-editable-text
                        key="team_partner_title"
                        :value="$contexts['team_partner_title'] ?? 'Backed by COCOGEN Insurance'"
                        tag="span"
                        class="inline-block"
                    />
                </h3>
                <p class="team-partner-text">
                    <x-editable-text
                        key="team_partner_text"
                        :value="$contexts['team_partner_text'] ?? 'Our team works hand-in-hand with licensed underwriters and advisors from COCOGEN to guarantee financial strength, underwriting accuracy, and prompt claim settlement.'"
                        tag="span"
                        class="block"
                    />
                </p>
            </div>

            <a href="{{ route('contact') }}" class="team-partner-btn">
                <x-editable-text
                    key="team_partner_btn"
                    :value="$contexts['team_partner_btn'] ?? 'Contact Partner Desk'"
                    tag="span"
                    class="inline-block"
                />
            </a>
        </div>
    </div>
</section>

{{--
<!-- =========================
     CALL TO ACTION
========================== -->
<section class="team-cta-section">
    <div class="team-cta-container">
        <h2 class="team-cta-title">
            <x-editable-text
                key="team_cta_title"
                :value="$contexts['team_cta_title'] ?? 'Want to Join Our Mission?'"
                tag="span"
                class="inline-block"
            />
        </h2>
        <p class="team-cta-description">
            <x-editable-text
                key="team_cta_description"
                :value="$contexts['team_cta_description'] ?? 'We are always looking for passionate community advocates, field coordinators, and partner organizations.'"
                tag="span"
                class="block"
            />
        </p>

        <div class="team-cta-buttons">
            <a href="#register" class="team-cta-btn-primary">
                <x-editable-text
                    key="team_cta_btn_primary"
                    :value="$contexts['team_cta_btn_primary'] ?? 'Become a Community Representative'"
                    tag="span"
                    class="inline-block"
                />
            </a>
            <a href="#contact" class="team-cta-btn-secondary">
                <x-editable-text
                    key="team_cta_btn_secondary"
                    :value="$contexts['team_cta_btn_secondary'] ?? 'Visit Branch Office'"
                    tag="span"
                    class="inline-block"
                />
            </a>
        </div>
    </div>
</section> --}}

</div>
