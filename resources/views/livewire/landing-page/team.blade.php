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
<section class="team-ceo-section">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">

        <div class="team-ceo-card">

            <div class="team-ceo-image">
                <img
                    src="{{ asset('images/user.png') }}"
                    alt="CEO"
                    class="team-ceo-avatar">
            </div>

            <div class="team-ceo-content">

                <span class="team-ceo-subtitle">
                    <x-editable-text
                        key="team_ceo_subtitle"
                        :value="$contexts['team_ceo_subtitle'] ?? 'Chief Executive Officer'"
                        tag="span"
                        class="inline-block"
                    />
                </span>

                <h2 class="team-ceo-name">
                    <x-editable-text
                        key="team_ceo_name"
                        :value="$contexts['team_ceo_name'] ?? 'Juan Dela Cruz'"
                        tag="span"
                        class="inline-block"
                    />
                </h2>

                <p class="team-ceo-position">
                    <x-editable-text
                        key="team_ceo_position"
                        :value="$contexts['team_ceo_position'] ?? 'President & Chief Executive Officer'"
                        tag="span"
                        class="inline-block"
                    />
                </p>

                <p class="team-ceo-description">
                    <x-editable-text
                        key="team_ceo_description"
                        :value="$contexts['team_ceo_description'] ?? 'Leading TrueLife Dayongan Solidaritas Social Welfare Services, Inc. with a vision of empowering Filipino families through accessible protection, compassionate service, and community-driven programs. Our mission is to build trust, strengthen communities, and provide dependable support where it matters most.'"
                        tag="span"
                        class="block"
                    />
                </p>

                <div class="mt-8 flex flex-wrap gap-4">

                    <button
                        wire:click="openCeoModal"
                        class="team-ceo-btn">

                        Read Full Story →

                    </button>

                </div>

            </div>

        </div>

    </div>
</section>

@if($showCeoModal)

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

@endif


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
                ['name'=>'Princes Manliguez','position'=>'Secretary'],
                ['name'=>'Dr. Maria Carmelita','position'=>'Auditor', 'contact' => '09565003849'],
                ['name'=>'Dr. Cherry Lynn Nierras-Dagala','position'=>'Treasurer'],
                ['name'=>'Mr. Alfe Duron','position'=>'Board Member'],
                ['name'=>'Ms. Ma. Daisy Colegado','position'=>'Board Member'],
                ['name'=>'Mr. Bernard Laurente','position'=>'Board Member'],
                ['name'=>'Mr. Pierre Hordista','position'=>'Board Member'],
                ['name'=>'Mrs. Cheryl Eliot, RN','position'=>'Board Member'],
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
                <span class="team-field-card-role">
                    <span class="inline-block">
                        {{ $branch->type }} ({{ $branch->branchCode }})
                    </span>
                </span>
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

            <a href="#contact" class="team-partner-btn">
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
</section>

</div>
