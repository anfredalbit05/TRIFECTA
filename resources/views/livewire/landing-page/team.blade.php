<div>

<!-- =========================
     TEAM HERO SECTION
========================== -->
<section class="team-hero">

    <div class="team-hero-glow-top"></div>
    <div class="team-hero-glow-bottom"></div>

    <div class="relative max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 text-center">

        <span class="team-hero-badge">
            TrueLife Dayongan Solidaritas Social Welfare Services, Inc.
        </span>

        <h1 class="team-hero-title">
            The Hearts & Minds Behind
            <br class="hidden sm:block">

            <span class="team-hero-title-highlight">
                DAYONGAN
            </span>
        </h1>

       <p class="team-hero-description">
            Dedicated leaders, compassionate community officers,
            and insurance professionals working together to safeguard
            every family we serve. Together, we build stronger communities
            through trust, commitment, and reliable support. Our team is
            driven by a shared mission to provide meaningful assistance,
            protect what matters most, and create a safer future for everyone.
       </p>

    </div>

</section>

<!-- =========================
     BOARD MEMBERS SECTION
========================== -->
<section class="team-board-section">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">

        <div class="team-board-header">
            <span class="team-board-subtitle">
                Dayongan Leadership
            </span>

            <h2 class="team-board-title">
                Board Members
            </h2>

            <p class="team-board-description">
                Dedicated leaders committed to serving the organization with integrity, transparency, and excellence.
            </p>
        </div>

        <div class="team-board-grid">

            @php
            $boardMembers = [
                ['name'=>'Princes Manliguez','position'=>'Secretary'],
                ['name'=>'Dr. Ma. Carnelita Robles','position'=>'Auditor'],
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
            <span class="team-field-subtitle">On The Ground</span>
            <h2 class="team-field-title">Branch & Field Support</h2>
            <p class="team-field-description">Connecting directly with communities in Tagum City, Kapalong, and nearby municipalities.</p>
        </div>

        <div class="team-field-grid">

            <div class="team-field-card">
                <div class="team-field-icon-box team-icon-blue">
                    📍
                </div>
                <h3 class="team-field-card-title">Tagum City Office</h3>
                <span class="team-field-card-role">Main Branch Team</span>
                <p class="team-field-card-text">Handles walk-in inquiries, membership renewals, and local health perk reimbursements.</p>
            </div>

            <div class="team-field-card">
                <div class="team-field-icon-box team-icon-amber">
                    📍
                </div>
                <h3 class="team-field-card-title">Kapalong Branch</h3>
                <span class="team-field-card-role">Municipal Helpdesk</span>
                <p class="team-field-card-text">Assists rural communities with micro-insurance registration and family beneficiary updates.</p>
            </div>

            <div class="team-field-card">
                <div class="team-field-icon-box team-icon-emerald">
                    🚑
                </div>
                <h3 class="team-field-card-title">Medical Assistance Officers</h3>
                <span class="team-field-card-role">Health Support Team</span>
                <p class="team-field-card-text">Coordinates hospital claim validations, P.E. reimbursements, and ultrasound support.</p>
            </div>

            <div class="team-field-card">
                <div class="team-field-icon-box team-icon-indigo">
                    🤝
                </div>
                <h3 class="team-field-card-title">Community Organizers</h3>
                <span class="team-field-card-role">Field Coordinators</span>
                <p class="team-field-card-text">Engaging barangays and groups to bring affordable insurance education directly to households.</p>
            </div>

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
                <span class="team-partner-subtitle">Strategic Alliance</span>
                <h3 class="team-partner-title">Backed by COCOGEN Insurance</h3>
                <p class="team-partner-text">
                    Our team works hand-in-hand with licensed underwriters and advisors from COCOGEN to guarantee financial strength, underwriting accuracy, and prompt claim settlement.
                </p>
            </div>

            <a href="#contact" class="team-partner-btn">
                Contact Partner Desk
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
            Want to Join Our Mission?
        </h2>
        <p class="team-cta-description">
            We are always looking for passionate community advocates, field coordinators, and partner organizations.
        </p>

        <div class="team-cta-buttons">
            <a href="#register" class="team-cta-btn-primary">
                Become a Community Representative
            </a>
            <a href="#contact" class="team-cta-btn-secondary">
                Visit Branch Office
            </a>
        </div>
    </div>
</section>


