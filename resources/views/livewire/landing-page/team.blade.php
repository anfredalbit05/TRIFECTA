<div class="tmt-page">

    <!-- =========================
         HERO
    ========================== -->
    <section class="tmt-hero">

        <div class="tmt-hero-inner">

            <span class="tmt-kicker tmt-kicker-light">
                <x-editable-text key="team_hero_badge" :value="$contexts['team_hero_badge'] ?? 'TRIFECTA Board of Directors'" tag="span" class="inline-block" />
            </span>

            <h1 class="tmt-hero-title">
                <x-editable-text key="team_hero_title_1" :value="$contexts['team_hero_title_1'] ?? 'The people behind'" tag="span" class="inline-block" />
                <span class="tmt-hero-title-accent">
                    <x-editable-text key="team_hero_title_highlight" :value="$contexts['team_hero_title_highlight'] ?? 'TRIFECTA'" tag="span" class="inline-block" />
                </span>
            </h1>

            <p class="tmt-hero-lead">
                <x-editable-text key="team_hero_description" :value="$contexts['team_hero_description'] ??
                    'The board members and officers of TRIFECTA — insurance professionals and community leaders working together so that every TRIFECTA family is looked after with competence and care.'" tag="span"
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
         PRESIDENT / OWNER
    ========================== -->
    <section class="tmt-exec">

        <div class="tmt-exec-wrap">

            <div class="tmt-exec-photo">
                <span class="tmt-exec-photo-rule"></span>
                <img src="{{ asset('images/team-cherry-lynn-dagala.jpg') }}"
                     alt="Dr. Cherry Lynn Dagala, LFA — Owner and President"
                     class="tmt-exec-img">
                <span class="tmt-exec-photo-tag">Owner &amp; President</span>
            </div>

            <div class="tmt-exec-body">

                <span class="tmt-kicker">Office of the President</span>

                <h2 class="tmt-exec-name">Dr. Cherry Lynn Dagala, LFA</h2>

                <p class="tmt-exec-role">Owner / President</p>

                <p class="tmt-exec-lead">
                    <x-editable-text key="team_ceo_description" :value="$contexts['team_ceo_description'] ??
                        'Driven by purpose and dedicated to service, our leadership continues to inspire communities through integrity, professionalism, and compassion.'" tag="span" />
                </p>

                <ul class="tmt-exec-chips">
                    <li>Radiologist</li>
                    <li>Licensed Financial Advisor</li>
                    <li>Three Pillars · One Commitment</li>
                </ul>

            </div>

        </div>

    </section>


    <!-- =========================
         BOARD OF DIRECTORS
    ========================== -->
    <section class="tmt-roster">

        <div class="tmt-roster-wrap">

            <header class="tmt-roster-head">
                <span class="tmt-kicker">Board Members</span>
                <h2 class="tmt-roster-title">The board &amp; the offices it leads</h2>
                <p class="tmt-roster-lead">
                    The board sets the direction of TRIFECTA. Each seat owns a part of the member
                    journey — from executive management and finance to the branches that serve
                    members face to face.
                </p>
            </header>

            <div class="tmt-roster-grid">

                <article class="tmt-person">
                    <div class="tmt-person-media" style="--photo: url('{{ asset('images/team-bobby-dagala.jpg') }}');">
                        <img src="{{ asset('images/team-bobby-dagala.jpg') }}"
                             alt="Mr. Bobby P. Dagala, LFA — Vice President and Chief Operating Officer"
                             class="tmt-person-photo">
                    </div>
                    <span class="tmt-person-role">Vice President / COO</span>
                    <h3 class="tmt-person-name">Mr. Bobby P. Dagala, LFA</h3>
                    <p class="tmt-person-meta">Management &amp; operations · 20 years in the insurance industry · Founder of Tri Care Community</p>
                </article>

                <article class="tmt-person">
                    <div class="tmt-person-media" style="--photo: url('{{ asset('images/team-alfe-duron.jpg') }}');">
                        <img src="{{ asset('images/team-alfe-duron.jpg') }}"
                             alt="Mr. Alfe S. Duron, LFA — HR Head"
                             class="tmt-person-photo">
                    </div>
                    <span class="tmt-person-role">People</span>
                    <h3 class="tmt-person-name">Mr. Alfe S. Duron, LFA</h3>
                    <p class="tmt-person-meta">HR Head · recruitment, training &amp; staff welfare</p>
                </article>

                <article class="tmt-person">
                    <div class="tmt-person-media" style="--photo: url('{{ asset('images/team-pierre-hordista.jpg') }}');">
                        <img src="{{ asset('images/team-pierre-hordista.jpg') }}"
                             alt="Mr. Pierre Hordista, LFA — IT In-Charge"
                             class="tmt-person-photo">
                    </div>
                    <span class="tmt-person-role">Technology</span>
                    <h3 class="tmt-person-name">Mr. Pierre Hordista, LFA</h3>
                    <p class="tmt-person-meta">IT In-Charge · systems, records security &amp; member data</p>
                </article>

                <article class="tmt-person">
                    <div class="tmt-person-media" style="--photo: url('{{ asset('images/team-george-vargas.jpg') }}');">
                        <img src="{{ asset('images/team-george-vargas.jpg') }}"
                             alt="Mr. George Vargas — Area Manager, San Franz, Agusan"
                             class="tmt-person-photo">
                    </div>
                    <span class="tmt-person-role">Branch</span>
                    <h3 class="tmt-person-name">Mr. George Vargas</h3>
                    <p class="tmt-person-meta">Area Manager · San Franz, Agusan</p>
                </article>

                <article class="tmt-person">
                    <div class="tmt-person-media" style="--photo: url('{{ asset('images/team-princes-manliguez.jpg') }}');">
                        <img src="{{ asset('images/team-princes-manliguez.jpg') }}"
                             alt="Ms. Princes Manliguez, LFA — Finance Officer"
                             class="tmt-person-photo">
                    </div>
                    <span class="tmt-person-role">Finance</span>
                    <h3 class="tmt-person-name">Ms. Princes Manliguez, LFA</h3>
                    <p class="tmt-person-meta">Finance Officer · membership fees, releases &amp; cashiering</p>
                </article>

                <article class="tmt-person">
                    <div class="tmt-person-media" style="--photo: url('{{ asset('images/team-jeralyn-gonzales.jpg') }}');">
                        <img src="{{ asset('images/team-jeralyn-gonzales.jpg') }}"
                             alt="Mrs. Jeralyn B. Gonzales — Cashier and Encoder"
                             class="tmt-person-photo">
                    </div>
                    <span class="tmt-person-role">Administration</span>
                    <h3 class="tmt-person-name">Mrs. Jeralyn B. Gonzales</h3>
                    <p class="tmt-person-meta">Cashier / Encoder · payments, receipts &amp; member records</p>
                </article>

                <article class="tmt-person">
                    <div class="tmt-person-media" style="--photo: url('{{ asset('images/team-georgelyn-angeles.jpg') }}');">
                        <img src="{{ asset('images/team-georgelyn-angeles.jpg') }}"
                             alt="Ms. Georgelyn Angeles — SFX Branch Secretary"
                             class="tmt-person-photo">
                    </div>
                    <span class="tmt-person-role">Branch</span>
                    <h3 class="tmt-person-name">Ms. Georgelyn Angeles</h3>
                    <p class="tmt-person-meta">SFX Branch Secretary · walk-in inquiries &amp; renewals</p>
                </article>

            </div>

        </div>

    </section>

</div>
