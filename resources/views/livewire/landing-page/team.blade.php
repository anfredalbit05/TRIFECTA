<div class="tmt-page">

    <!-- =========================
         HERO
    ========================== -->
    <section class="tmt-hero">

        <div class="tmt-hero-grid"></div>

        <div class="tmt-hero-inner">

            <span class="tmt-kicker tmt-kicker-light">
                <x-editable-text key="team_hero_badge" :value="$contexts['team_hero_badge'] ?? 'TRIFECTA Leadership'" tag="span" class="inline-block" />
            </span>

            <h1 class="tmt-hero-title">
                <x-editable-text key="team_hero_title_1" :value="$contexts['team_hero_title_1'] ?? 'The people behind'" tag="span" class="inline-block" />
                <span class="tmt-hero-title-accent">
                    <x-editable-text key="team_hero_title_highlight" :value="$contexts['team_hero_title_highlight'] ?? 'TRIFECTA'" tag="span" class="inline-block" />
                </span>
            </h1>

            <p class="tmt-hero-lead">
                <x-editable-text key="team_hero_description" :value="$contexts['team_hero_description'] ??
                    'Six offices, one standard of service. Meet the executives and branch managers who stand behind every member file, every release, and every promise we make.'" tag="span"
                    class="block" />
            </p>

            <div class="tmt-hero-facts">
                <div>
                    <span class="tmt-hero-fact-value">01</span>
                    <span class="tmt-hero-fact-label">Executive Office</span>
                </div>
                <div>
                    <span class="tmt-hero-fact-value">02</span>
                    <span class="tmt-hero-fact-label">Agency &amp; Admin</span>
                </div>
                <div>
                    <span class="tmt-hero-fact-value">03</span>
                    <span class="tmt-hero-fact-label">Branch Managers</span>
                </div>
            </div>

        </div>

    </section>


    <!-- =========================
         CHIEF EXECUTIVE
    ========================== -->
    <section class="tmt-exec">

        <div class="tmt-exec-wrap">

            <div class="tmt-exec-photo">
                <span class="tmt-exec-photo-rule"></span>
                <img src="{{ asset('images/ceo-teams.png') }}" alt="Chief Executive Officer" class="tmt-exec-img">
                <span class="tmt-exec-photo-tag">President &amp; CEO</span>
            </div>

            <div class="tmt-exec-body">

                <span class="tmt-kicker">Office of the President</span>

                {{-- <h2 class="tmt-exec-name">
                    <x-editable-text key="team_ceo_name" :value="$contexts['team_ceo_name'] ?? 'Anfred Albit'" tag="span" />
                </h2> --}}

                <p class="tmt-exec-role">President &amp; Chief Executive Officer</p>

                <p class="tmt-exec-lead">
                    <x-editable-text key="team_ceo_description" :value="$contexts['team_ceo_description'] ??
                        'Driven by purpose and dedicated to service, our leadership continues to inspire communities through integrity, professionalism, and compassion.'" tag="span" />
                </p>

                <ul class="tmt-exec-chips">
                    <li>Government-Licensed Financial Advisor</li>
                    <li>Former City Councilor, Tagum City</li>
                    <li>Professor of Business &amp; Marketing</li>
                </ul>

            </div>

        </div>

    </section>


    <!-- =========================
         LEADERSHIP ROSTER
    ========================== -->
    <section class="tmt-roster">

        <div class="tmt-roster-wrap">

            <header class="tmt-roster-head">
                <span class="tmt-kicker">The Leadership Circle</span>
                <h2 class="tmt-roster-title">Agency, administration &amp; the branches</h2>
                <p class="tmt-roster-lead">
                    Each office owns a part of the member journey — from agency partnerships and
                    records administration to the three branches that serve members face to face.
                </p>
            </header>

            <div class="tmt-roster-grid">

                <article class="tmt-person">
                    <span class="tmt-person-avatar">TA</span>
                    <span class="tmt-person-role">Agency</span>
                    <h3 class="tmt-person-name">TRI CARE AGENCY</h3>
                    <p class="tmt-person-meta">Partner agency · licensing &amp; field coordination</p>
                </article>

                <article class="tmt-person">
                    <span class="tmt-person-avatar">BD</span>
                    <span class="tmt-person-role">Administration</span>
                    <h3 class="tmt-person-name">Bobby Paulo Dagala, LFA</h3>
                    <p class="tmt-person-meta">Founder · records, releases &amp; member support</p>
                </article>

                @foreach ($branches->take(3) as $branch)
                    <article class="tmt-person" wire:key="branch-{{ $branch->BranchID }}">
                        <span class="tmt-person-avatar">
                            {{ Str::upper(Str::substr(preg_replace('/[^A-Za-z ]/', '', $branch->branchname), 0, 2)) }}
                        </span>
                        <span class="tmt-person-role">Branch Manager</span>
                        <h3 class="tmt-person-name">{{ preg_replace('/\s+Branch$/i', '', $branch->branchname) }}</h3>
                        <p class="tmt-person-meta">{{ $branch->address }}</p>
                    </article>
                @endforeach

            </div>

        </div>

    </section>

</div>
