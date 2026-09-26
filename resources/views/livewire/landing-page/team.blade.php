{{--
    Import the merged team stylesheet in your CSS entry point,
    e.g. resources/css/app.css:

        @import '../../team.css';

    The detailed CEO/COO leadership cards below use
    "team-leadership-ceo-*" / "team-leadership-coo-*" classes so they
    don't collide with the original "team-ceo-*" hero-style block used
    elsewhere on the site.
--}}
<div>

    <!-- =========================
     TEAM HERO SECTION
========================== -->
    <section class="team-hero">

        <div class="team-hero-glow-top"></div>
        <div class="team-hero-glow-bottom"></div>

        <div class="relative max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 text-center">

            <span class="team-hero-badge">
                <x-editable-text key="team_hero_badge" :value="$contexts['team_hero_badge'] ??
                    'TrueLife Dayongan Solidaritas Social Welfare Services, Inc.'" tag="span" class="inline-block" />
            </span>

            <h1 class="team-hero-title">
                <x-editable-text key="team_hero_title_1" :value="$contexts['team_hero_title_1'] ?? 'The Hearts & Minds Behind'" tag="span" class="inline-block" />
                <br class="hidden sm:block">

                <span class="team-hero-title-highlight">
                    <x-editable-text key="team_hero_title_highlight" :value="$contexts['team_hero_title_highlight'] ?? 'DAYONGAN'" tag="span"
                        class="inline-block" />
                </span>
            </h1>

            <p class="team-hero-description">
                <x-editable-text key="team_hero_description" :value="$contexts['team_hero_description'] ??
                    'Dedicated leaders, compassionate community officers, and insurance professionals working together to safeguard every family we serve. Together, we build stronger communities through trust, commitment, and reliable support. Our team is driven by a shared mission to provide meaningful assistance, protect what matters most, and create a safer future for everyone.'" tag="span" class="block" />
            </p>

        </div>

    </section>


    <!-- =========================
     CEO SECTION
========================== -->

    <section class="team-leadership-ceo-section">

        <!-- background decoration -->

        <div class="team-leadership-ceo-decor-blue"></div>

        <div class="team-leadership-ceo-decor-yellow"></div>

        <div class="team-leadership-ceo-container">

            <div class="team-leadership-ceo-card">

                <div class="team-leadership-ceo-grid">

                    <!-- ========================= -->
                    <!-- LEFT CONTENT -->
                    <!-- ========================= -->

                    <div class="team-leadership-ceo-left">

                        <!-- Badge -->

                        <div>

                            <span class="team-leadership-ceo-badge">

                                The Leadership Team

                            </span>

                        </div>

                        <!-- Heading -->

                        <h1 class="team-leadership-ceo-heading">

                            STRONG

                            <span class="team-leadership-ceo-heading-blue">

                                LEADERSHIP.

                            </span>

                            <br>

                            GREATER

                            <span class="team-leadership-ceo-heading-yellow">

                                IMPACT.

                            </span>

                        </h1>

                        <!-- Description -->

                        <p class="team-leadership-ceo-description">

                            <x-editable-text key="team_ceo_description" :value="$contexts['team_ceo_description'] ??
                                'Driven by purpose and dedicated to service, our leadership continues to inspire communities through integrity, professionalism, and compassion.'" tag="span" />

                        </p>

                        <!-- CEO Card -->

                        <div class="team-leadership-ceo-info-card">

                            <p class="team-leadership-ceo-info-label">

                                THE CEO

                            </p>

                            <h2 class="team-leadership-ceo-info-name">

                                <x-editable-text key="team_ceo_name" :value="$contexts['team_ceo_name'] ?? 'RENANTE SUAYBAGUIO-ROBLES, MPA, LFA'" tag="span" />

                            </h2>

                            <p class="team-leadership-ceo-info-subtitle">

                                Leader • Servant • Professional

                            </p>

                        </div>

                        <!-- Experience -->

                        <div class="team-leadership-ceo-experience-grid">

                            <!-- Public -->

                            <div class="team-leadership-ceo-exp-card">

                                <div class="team-leadership-ceo-exp-header">

                                    <div class="team-leadership-ceo-exp-icon-box team-leadership-ceo-exp-icon-blue">

                                        <flux:icon.building-library class="w-7 h-7 sm:w-8 sm:h-8" />

                                    </div>

                                    <div>

                                        <h3 class="team-leadership-ceo-exp-title">

                                            Public Service

                                        </h3>

                                        <p class="team-leadership-ceo-exp-label">

                                            Experience

                                        </p>

                                    </div>

                                </div>

                                <ul class="team-leadership-ceo-exp-list">

                                    <li>• Former SK Chairman</li>

                                    <li>• Former Barangay Kagawad</li>

                                    <li>• Former Barangay Captain</li>

                                    <li>• Former City Councilor of Tagum City</li>


                                </ul>

                            </div>

                            <!-- Professional -->

                            <div class="team-leadership-ceo-exp-card">

                                <div class="team-leadership-ceo-exp-header">

                                    <div class="team-leadership-ceo-exp-icon-box team-leadership-ceo-exp-icon-yellow">

                                        <flux:icon.briefcase class="w-7 h-7 sm:w-8 sm:h-8" />

                                    </div>

                                    <div>

                                        <h3 class="team-leadership-ceo-exp-title">

                                            Professional

                                        </h3>

                                        <p class="team-leadership-ceo-exp-label">

                                            Experience

                                        </p>

                                    </div>

                                </div>

                                <ul class="team-leadership-ceo-exp-list">

                                    <li>• Government-Licensed Financial Advisor</li>

                                    <li>• Unit Head, Manulife Philippines</li>

                                    <li>• Marketing Director, IMG (International Marketing Group)</li>


                                </ul>

                            </div>

                        </div>

                        <!-- Academic -->

                        <div class="team-leadership-ceo-academic-box">

                            <div class="team-leadership-ceo-academic-header">

                                <div class="team-leadership-ceo-academic-icon">

                                    <flux:icon.academic-cap class="w-7 h-7 sm:w-8 sm:h-8" />

                                </div>

                                <div>

                                    <h3 class="team-leadership-ceo-academic-title">

                                        Academic Background

                                    </h3>

                                    <p class="team-leadership-ceo-academic-text">

                                        College Professor specializing in

                                        <strong>Business and Marketing</strong>

                                    </p>

                                </div>

                            </div>

                        </div>

                        <!-- Leadership Profile -->

                        <div class="team-leadership-ceo-profile-box">

                            <h3 class="team-leadership-ceo-profile-title">

                                Leadership Profile

                            </h3>

                            <div class="team-leadership-ceo-profile-divider"></div>

                            <p class="team-leadership-ceo-profile-text">

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

                    <div class="team-leadership-ceo-right">

                        <!-- decorative -->

                        <div class="team-leadership-ceo-right-decor-yellow"></div>

                        <div class="team-leadership-ceo-right-decor-blue"></div>


                        <!-- image -->

                        <div class="team-leadership-ceo-image-wrap">

                            <img src="{{ asset('images/ceo.png') }}" alt="CEO" class="team-leadership-ceo-image">

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>



    <!-- =========================
     COO SECTION
========================== -->

    <section class="team-leadership-coo-section">

        <div class="team-leadership-coo-container">

            <div class="team-leadership-coo-card">

                <div class="team-leadership-coo-grid">

                    <!-- ========================= -->
                    <!-- RIGHT IMAGE -->
                    <!-- ========================= -->

                    <div class="team-leadership-coo-image-col">

                        <!-- Decorations -->
                        <div class="team-leadership-coo-decor-yellow"></div>
                        <div class="team-leadership-coo-decor-blue"></div>

                        <!-- Gold line -->
                        <div class="team-leadership-coo-gold-line"></div>

                        <!-- Image -->
                        <div class="team-leadership-coo-image-wrap">
                            <img src="{{ asset('images/coo.png') }}" alt="COO" class="team-leadership-coo-image">
                        </div>

                    </div>

                    <div class="team-leadership-coo-content">

                        <!-- Badge -->

                        <div>

                            <span class="team-leadership-coo-badge">

                                Vice President for

                            </span>

                        </div>

                        <!-- Position -->

                        <h2 class="team-leadership-coo-position">

                            Marketing and Operations

                        </h2>

                        <!-- Name -->

                        <h1 class="team-leadership-coo-name">

                            Bobby Paulo Dagala, LFA

                        </h1>

                        <!-- Subtitle -->

                        <p class="team-leadership-coo-subtitle">

                            A Government Licensed Financial Advisor

                        </p>

                        <!-- Divider -->

                        <div class="team-leadership-coo-divider"></div>

                        <!-- Cards -->

                        <div class="team-leadership-coo-cards">

                            <!-- Experience -->

                            <div class="team-leadership-coo-info-card">

                                <div class="team-leadership-coo-info-row">

                                    <div class="team-leadership-coo-info-icon team-leadership-coo-info-icon-blue">

                                        <flux:icon.shield-check class="w-7 h-7 sm:w-8 sm:h-8" />

                                    </div>

                                    <div>

                                        <h3 class="team-leadership-coo-info-title">

                                            20 Years

                                        </h3>

                                        <p class="team-leadership-coo-info-text">

                                            in Insurance Industry

                                        </p>

                                    </div>

                                </div>

                            </div>

                            <!-- Owner/Founder -->

                            <div class="team-leadership-coo-info-card">

                                <div class="team-leadership-coo-info-row">

                                    <div class="team-leadership-coo-info-icon team-leadership-coo-info-icon-blue">

                                        <flux:icon.user-group class="w-7 h-7 sm:w-8 sm:h-8" />

                                    </div>

                                    <div>

                                        <h3 class="team-leadership-coo-info-title">

                                            Owner/Founder

                                        </h3>

                                        <p class="team-leadership-coo-info-text-tight">

                                            TRI-CARE Community Development
                                            and Consultancy Services

                                        </p>

                                    </div>

                                </div>

                            </div>

                            <!-- Business -->

                            <div class="team-leadership-coo-info-card">

                                <div class="team-leadership-coo-info-row">

                                    <div class="team-leadership-coo-info-icon team-leadership-coo-info-icon-yellow">

                                        <flux:icon.building-office class="w-7 h-7 sm:w-8 sm:h-8" />

                                    </div>

                                    <div>

                                        <h3 class="team-leadership-coo-info-title">

                                            Business Owner

                                        </h3>

                                        <p class="team-leadership-coo-info-text-tight">

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

    {{-- @if ($showCeoModal)

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
                    <x-editable-text key="team_board_subtitle" :value="$contexts['team_board_subtitle'] ?? 'Dayongan Leadership'" tag="span"
                        class="inline-block" />
                </span>

                <h2 class="team-board-title">
                    <x-editable-text key="team_board_title" :value="$contexts['team_board_title'] ?? 'Board Members'" tag="span" class="inline-block" />
                </h2>

                <p class="team-board-description">
                    <x-editable-text key="team_board_description" :value="$contexts['team_board_description'] ??
                        'Dedicated leaders committed to serving the organization with integrity, transparency, and excellence.'" tag="span" class="block" />
                </p>
            </div>

            <div class="team-board-grid">

                @php
                    $boardMembers = [
                        [
                            'name' => 'Jeorge Leemart Alfeche',
                            'position' => 'Position 1',
                            'contact' => '',
                            'image' => 'images/BOD/Member1.png',
                        ],
                        [
                            'name' => 'Benito Igadna',
                            'position' => 'Position 2',
                            'contact' => '',
                            'image' => 'images/BOD/Member2.jpg',
                        ],
                        [
                            'name' => 'Pierre Hordista',
                            'position' => 'Position 3',
                            'contact' => '',
                            'image' => 'images/BOD/Member3.png',
                        ],
                        [
                            'name' => 'Daisy Maghuyop Colegado',
                            'position' => 'Position 4',
                            'contact' => '',
                            'image' => 'images/BOD/Member4.png',
                        ],
                        [
                            'name' => 'Mrs. Cheryl Eliot, RN',
                            'position' => 'Position 5',
                            'contact' => '',
                            'image' => 'images/BOD/Member5.png',
                        ],
                        [
                            'name' => 'Bernard Laurente',
                            'position' => 'Position 6',
                            'contact' => '',
                            'image' => 'images/BOD/Member6.png',
                        ],
                        [
                            'name' => 'Alfe Duron',
                            'position' => 'Position 7',
                            'contact' => '',
                            'image' => 'images/BOD/Member7.png',
                        ],
                        [
                            'name' => 'Cherry Lynn Nierras-Dagala, MD',
                            'position' => 'Position 8',
                            'contact' => '',
                            'image' => 'images/BOD/Member8.png',
                        ],
                    ];
                @endphp

                @foreach ($boardMembers as $member)
                    <div class="team-board-card">
                        <div class="team-board-card-body">
                            <img src="{{ asset($member['image'] ?? 'images/user.png') }}" alt="{{ $member['name'] }}"
                                class="team-board-avatar"
                                {{-- onclick="openBoardModal('{{ asset($member['image'] ?? 'images/user.png') }}', '{{ $member['name'] }}', '{{ $member['position'] }}')" --}}
                                >

                            <h3 class="team-board-name">
                                {{ $member['name'] }}
                            </h3>

                            {{-- @isset($member['contact'])
                                <p>{{ $member['contact'] }}</p>
                            @endisset --}}

                            {{-- <p class="team-board-position">
                                {{ $member['position'] }}
                            </p> --}}
                        </div>
                    </div>
                @endforeach

            </div>

        </div>
    </section>

    <!-- =========================
     BOARD MEMBER PREVIEW MODAL
========================== -->
    <div id="boardModalOverlay" class="board-modal-overlay" onclick="closeBoardModal(event)">
        <div class="board-modal-inner">
            <div class="board-modal-content">
                <img id="boardModalImage" src="" alt="" class="board-modal-image">

                <button type="button" class="board-modal-close-btn" onclick="closeBoardModal(event)">
                    Close
                </button>
            </div>
        </div>
    </div>


    <!-- =========================
     BRANCH & FIELD SUPPORT
========================== -->
    <section class="team-field-section">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
            <div class="team-field-header">
                <span class="team-field-subtitle">
                    <x-editable-text key="team_field_subtitle" :value="$contexts['team_field_subtitle'] ?? 'On The Ground'" tag="span"
                        class="inline-block" />
                </span>
                <h2 class="team-field-title">
                    <x-editable-text key="team_field_title" :value="$contexts['team_field_title'] ?? 'Branch & Field Support'" tag="span" class="inline-block" />
                </h2>
                <p class="team-field-description">
                    <x-editable-text key="team_field_description" :value="$contexts['team_field_description'] ??
                        'Connecting directly with communities in Tagum City, Kapalong, and nearby municipalities.'" tag="span" class="block" />
                </p>
            </div>

            <div class="team-field-grid">

                @foreach ($branches as $branch)
                    <div class="team-field-card">
                        <div class="team-field-icon-box team-icon-blue">
                            <span class="inline-block">
                                <flux:icon.map-pin class="w-8 h-8 sm:w-10 sm:h-10" />
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
                        <x-editable-text key="team_partner_subtitle" :value="$contexts['team_partner_subtitle'] ?? 'Strategic Alliance'" tag="span"
                            class="inline-block" />
                    </span>
                    <h3 class="team-partner-title">
                        <x-editable-text key="team_partner_title" :value="$contexts['team_partner_title'] ?? 'Backed by COCOGEN Insurance'" tag="span"
                            class="inline-block" />
                    </h3>
                    <p class="team-partner-text">
                        <x-editable-text key="team_partner_text" :value="$contexts['team_partner_text'] ??
                            'Our team works hand-in-hand with licensed underwriters and advisors from COCOGEN to guarantee financial strength, underwriting accuracy, and prompt claim settlement.'" tag="span" class="block" />
                    </p>
                </div>

                <a href="{{ route('contact') }}" class="team-partner-btn">
                    <x-editable-text key="team_partner_btn" :value="$contexts['team_partner_btn'] ?? 'Contact Partner Desk'" tag="span" class="inline-block" />
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


    <script>
        function openBoardModal(imageSrc, name, position) {
            document.getElementById('boardModalImage').src = imageSrc;
            document.getElementById('boardModalImage').alt = name;
            // document.getElementById('boardModalName').textContent = name;
            // document.getElementById('boardModalPosition').textContent = position;
            document.getElementById('boardModalOverlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeBoardModal(event) {
            if (event.target.id === 'boardModalOverlay' || event.target.classList.contains('board-modal-close-btn')) {
                document.getElementById('boardModalOverlay').classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('boardModalOverlay').classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    </script>

</div>
