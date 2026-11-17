
@php
    $joinAllow = \Illuminate\Support\Facades\DB::table('settings')
        ->where('BranchID', 0)
        ->where('name', 'join_allow')
        ->value('value');

    $joinAllow = filter_var($joinAllow, FILTER_VALIDATE_BOOLEAN);
@endphp

<div>

<!-- ======================================
     HERO SECTION
====================================== -->
<section class="about-hero">

    <div class="about-hero-glow-top"></div>
    <div class="about-hero-glow-bottom"></div>

    <div class="about-container-center">

        <span class="about-hero-badge">
            <x-editable-text
                key="about_hero_badge"
                :value="$contexts['about_hero_badge'] ?? 'TRIFECTA · Health, Wellness & Protection'"
                tag="span"
                class="inline-block"
            />
        </span>

        <h1 class="about-hero-title">
            <x-editable-text
                key="about_hero_title_1"
                :value="$contexts['about_hero_title_1'] ?? 'Building a'"
                tag="span"
                class="inline-block"
            />
            <br class="hidden sm:block">
            <span class="about-hero-title-highlight">
                <x-editable-text
                    key="about_hero_title_highlight"
                    :value="$contexts['about_hero_title_highlight'] ?? 'Loving & Protected'"
                    tag="span"
                    class="inline-block"
                />
            </span>
            <x-editable-text
                key="about_hero_title_2"
                :value="$contexts['about_hero_title_2'] ?? 'Community'"
                tag="span"
                class="inline-block"
            />
        </h1>

        <p class="about-hero-description">
            <x-editable-text
                key="about_hero_description"
                :value="$contexts['about_hero_description'] ?? 'TRIFECTA is a community-rooted protection programme built on three simple promises: health, wellness and protection. We help Filipino families prepare for life\'s hardest moments with affordable cover and honest service.'"
                tag="span"
                class="block"
            />
        </p>

    </div>

</section>

<!-- =========================
     ABOUT TRIFECTA
========================= -->
<section class="about-band bg-white">
    <div class="about-container">
        <div class="about-container-center">
            <span class="about-section-label-gold">
                <x-editable-text
                    key="about_who_we_are_label"
                    :value="$contexts['about_who_we_are_label'] ?? 'Who We Are'"
                    tag="span"
                    class="inline-block"
                />
            </span>

            <h2 class="about-section-title-dark">
                <x-editable-text
                    key="about_who_we_are_title"
                    :value="$contexts['about_who_we_are_title'] ?? 'About TRIFECTA'"
                    tag="span"
                    class="inline-block"
                />
            </h2>

            <p class="mt-6 text-slate-600 text-lg leading-8">
                <x-editable-text
                    key="about_who_we_are_text"
                    :value="$contexts['about_who_we_are_text'] ?? 'TRIFECTA believes that every Filipino family deserves affordable protection, dignity, and hope. Through community cooperation and financial literacy, we provide accessible welfare services that strengthen families during life\'s most difficult moments.'"
                    tag="span"
                    class="block"
                />
            </p>
        </div>
    </div>
</section>


<!-- =========================
     DTI REGISTRATION CERTIFICATE
========================= -->
<section class="about-band bg-[#F4F7FC]">
    <div class="about-container">

        <!-- Section Header -->
        <div class="about-container-center mb-14">

            <span class="about-section-label-gold">
                <x-editable-text
                    key="about_dti_label"
                    :value="$contexts['about_dti_label'] ?? 'Official Registration'"
                    tag="span"
                    class="inline-block"
                />
            </span>

            <h2 class="about-section-title-dark mt-4">
                <x-editable-text
                    key="about_dti_title"
                    :value="$contexts['about_dti_title'] ?? 'DTI Certificate of Business Registration'"
                    tag="span"
                    class="inline-block"
                />
            </h2>

            <p class="mt-5 max-w-3xl mx-auto text-lg text-slate-600 leading-8">
                <x-editable-text
                    key="about_dti_description"
                    :value="$contexts['about_dti_description'] ?? 'TRIFECTA is officially registered with the Department of Trade and Industry (DTI). This certificate demonstrates our legal compliance, commitment to transparency, and dedication to providing trusted community-based protection for Filipino families.'"
                    tag="span"
                    class="block"
                />
            </p>

        </div>

        <!-- Certificate -->
        <div class="max-w-5xl mx-auto">

            <!-- Information Cards -->
            <div class="grid md:grid-cols-3 gap-5 mb-8">

                <div class="about-fact-card">
                    <p class="text-xs uppercase tracking-widest text-slate-500">
                        Business Name
                    </p>
                    <p class="mt-2 font-bold text-[var(--color-primary)]">
                        TRIFECTA
                    </p>
                </div>

                <div class="about-fact-card">
                    <p class="text-xs uppercase tracking-widest text-slate-500">
                        Registration No.
                    </p>
                    <p class="mt-2 font-bold text-[var(--color-primary)]">
                        8404829
                    </p>
                </div>

                <div class="about-fact-card">
                    <p class="text-xs uppercase tracking-widest text-slate-500">
                        Valid Until
                    </p>
                    <p class="mt-2 font-bold text-[var(--color-primary)]">
                        August 06, 2026 to August 06, 2031
                    </p>
                </div>

            </div>

            <!-- Certificate Image -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden">
                <img
                    src="{{ asset('images/dti-certificate.jpg') }}"
                    alt="DTI Certificate"
                    class="w-full h-auto">
            </div>

            <!-- Download Button -->
            <div class="mt-8 flex justify-center">
                <a
                    href="{{ asset('images/dti-certificate.jpg') }}"
                    download
                    class="inline-flex items-center gap-3 px-8 py-4 rounded-xl bg-[var(--color-primary)] hover:bg-[var(--color-primary-mid)] text-white font-semibold shadow-lg transition">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 4v12m0 0l-4-4m4 4l4-4"/>
                    </svg>

                    Download Official Certificate
                </a>
            </div>

        </div>

    </div>
</section>


<!-- =========================
     VISION & MISSION
========================= -->
<section class="about-band bg-white">
    <div class="about-container">
        <div class="about-vm-grid">

            <!-- Vision -->
            <div class="about-vm-card-vision">
                <div class="about-vm-icon">
                    <flux:icon.eye class="w-12 h-12" />
                </div>

                <h2 class="about-vm-title">
                    <x-editable-text
                        key="about_vision_heading"
                        :value="$contexts['about_vision_heading'] ?? 'PANAN-AWON (VISION)'"
                        tag="span"
                        class="inline-block"
                    />
                </h2>

                <p class="about-vm-text">
                    <x-editable-text
                        key="about_vision_text"
                        :value="$contexts['about_vision_text'] ?? 'Ang pagtataguyod ng isang mapagmahal at protektadong komunidad kung saan ang bawat pamilyang Pilipino, lalo na ang mga kapos-palad, ay may abot-kaya at kayang tustusan na proteksyon, dignidad, at pag-asa sa panahon ng pagsubok.'"
                        tag="span"
                        class="block"
                    />
                </p>
            </div>

            <!-- Mission -->
            <div class="about-vm-card-mission">
                <div class="about-vm-icon">
                    <flux:icon.flag class="w-12 h-12" />
                </div>

                <h2 class="about-vm-title">
                    <x-editable-text
                        key="about_mission_heading"
                        :value="$contexts['about_mission_heading'] ?? 'MISYON (MISSION)'"
                        tag="span"
                        class="inline-block"
                    />
                </h2>

                <p class="about-vm-text">
                    <x-editable-text
                        key="about_mission_text"
                        :value="$contexts['about_mission_text'] ?? 'Ang TRIFECTA ay nagsisikap na magbigay ng abot-kaya at nakabatay sa komunidad na proteksyon at mga benepisyo upang matulungan, maprotektahan, at mapatatag ang buhay ng bawat pamilya sa pamamagitan ng diwa ng Pagtutulungan at Pagkakaisa.'"
                        tag="span"
                        class="block"
                    />
                </p>
            </div>

        </div>
    </div>
</section>

<!-- =========================
     CORE VALUES
========================= -->
<section class="about-band bg-[#F4F7FC]">
    <div class="about-container">

        <div class="text-center">
            <span class="about-section-label-gold">
                <x-editable-text
                    key="about_values_label"
                    :value="$contexts['about_values_label'] ?? 'Our Foundation'"
                    tag="span"
                    class="inline-block"
                />
            </span>

            <h2 class="about-section-title-dark">
                <x-editable-text
                    key="about_values_title"
                    :value="$contexts['about_values_title'] ?? 'Core Values'"
                    tag="span"
                    class="inline-block"
                />
            </h2>

            <p class="mt-4 text-slate-600 max-w-2xl mx-auto">
                <x-editable-text
                    key="about_values_subtitle"
                    :value="$contexts['about_values_subtitle'] ?? 'These values guide every service and every decision we make for Filipino families and communities.'"
                    tag="span"
                    class="block"
                />
            </p>
        </div>

        <div class="about-values-grid">

            <div class="about-value-card">
                <span class="about-value-number">01</span>
                <div class="about-value-icon-blue">
                    <flux:icon.user-group class="w-8 h-8" />
                </div>
                <h3 class="about-value-title">
                    <x-editable-text key="about_val_title_1" :value="$contexts['about_val_title_1'] ?? 'Pagtutulungan'" tag="span" class="inline-block" />
                </h3>
                <p class="about-value-text">
                    <x-editable-text key="about_val_text_1" :value="$contexts['about_val_text_1'] ?? 'Tulungan ang Kapwa'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-value-card">
                <span class="about-value-number">02</span>
                <div class="about-value-icon-gold">
                    <flux:icon.shield-check class="w-8 h-8" />
                </div>
                <h3 class="about-value-title">
                    <x-editable-text key="about_val_title_2" :value="$contexts['about_val_title_2'] ?? 'Proteksyon'" tag="span" class="inline-block" />
                </h3>
                <p class="about-value-text">
                    <x-editable-text key="about_val_text_2" :value="$contexts['about_val_text_2'] ?? 'Paunang Pag-alaga'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-value-card">
                <span class="about-value-number">03</span>
                <div class="about-value-icon-blue">
                    <flux:icon.home-modern class="w-8 h-8" />
                </div>
                <h3 class="about-value-title">
                    <x-editable-text key="about_val_title_3" :value="$contexts['about_val_title_3'] ?? 'Pamilya'" tag="span" class="inline-block" />
                </h3>
                <p class="about-value-text">
                    <x-editable-text key="about_val_text_3" :value="$contexts['about_val_text_3'] ?? 'Una sa Lahat'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-value-card">
                <span class="about-value-number">04</span>
                <div class="about-value-icon-gold">
                    <flux:icon.check-badge class="w-8 h-8" />
                </div>
                <h3 class="about-value-title">
                    <x-editable-text key="about_val_title_4" :value="$contexts['about_val_title_4'] ?? 'Integridad'" tag="span" class="inline-block" />
                </h3>
                <p class="about-value-text">
                    <x-editable-text key="about_val_text_4" :value="$contexts['about_val_text_4'] ?? 'Tapat at Totoo'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-value-card">
                <span class="about-value-number">05</span>
                <div class="about-value-icon-blue">
                    <flux:icon.heart class="w-8 h-8" />
                </div>
                <h3 class="about-value-title">
                    <x-editable-text key="about_val_title_5" :value="$contexts['about_val_title_5'] ?? 'Pagkakaisa'" tag="span" class="inline-block" />
                </h3>
                <p class="about-value-text">
                    <x-editable-text key="about_val_text_5" :value="$contexts['about_val_text_5'] ?? 'Magkaisa, Magtagumpay'" tag="span" class="inline-block" />
                </p>
            </div>

        </div>
    </div>
</section>

<!-- =========================
     OBJECTIVES
========================= -->
<section class="about-objectives-section">
    <div class="about-container">
        <div class="about-objectives-grid">

            <!-- Left -->
            <div>
                <span class="about-section-label-yellow">
                    <x-editable-text
                        key="about_obj_label"
                        :value="$contexts['about_obj_label'] ?? 'Our Objectives'"
                        tag="span"
                        class="inline-block"
                    />
                </span>

                <h2 class="about-section-title-white">
                    <x-editable-text
                        key="about_obj_title"
                        :value="$contexts['about_obj_title'] ?? 'Mga Katuyoan (Objectives)'"
                        tag="span"
                        class="inline-block"
                    />
                </h2>

                <div class="about-objectives-list">

                    <div class="about-objective-item">
                        <div class="about-objective-check"><flux:icon.check class="w-6 h-6" /></div>
                        <p>
                            <x-editable-text key="about_obj_1" :value="$contexts['about_obj_1'] ?? 'Magbigay ng barato ug maabot nga serbisyo sa proteksyon alang sa ordinaryong pamilya Pilipino.'" tag="span" class="inline-block" />
                        </p>
                    </div>

                    <div class="about-objective-item">
                        <div class="about-objective-check"><flux:icon.check class="w-6 h-6" /></div>
                        <p>
                            <x-editable-text key="about_obj_2" :value="$contexts['about_obj_2'] ?? 'Magtabang dayon sa panahon sa aksidente, pagkasakit, ug kamatayon.'" tag="span" class="inline-block" />
                        </p>
                    </div>

                    <div class="about-objective-item">
                        <div class="about-objective-check"><flux:icon.check class="w-6 h-6" /></div>
                        <p>
                            <x-editable-text key="about_obj_3" :value="$contexts['about_obj_3'] ?? 'Magpahiug-ot sa panaghiusa, pagtinabangay, ug pag-amuma sa komunidad.'" tag="span" class="inline-block" />
                        </p>
                    </div>

                    <div class="about-objective-item">
                        <div class="about-objective-check"><flux:icon.check class="w-6 h-6" /></div>
                        <p>
                            <x-editable-text key="about_obj_4" :value="$contexts['about_obj_4'] ?? 'Maghatag og paglaum ug seguridad sa mga kabus ug nanginahanglan.'" tag="span" class="inline-block" />
                        </p>
                    </div>

                    <div class="about-objective-item">
                        <div class="about-objective-check"><flux:icon.check class="w-6 h-6" /></div>
                        <p>
                            <x-editable-text key="about_obj_5" :value="$contexts['about_obj_5'] ?? 'Magsiguro nga walay pamilya nga biyaan sa kalisod.'" tag="span" class="inline-block" />
                        </p>
                    </div>

                </div>
            </div>

            <!-- Right -->
            <div>
                <img
                    src="{{ asset('images/dayongan-objectives.jpg') }}"
                    alt="TRIFECTA Objectives"
                    class="about-hero-img">
            </div>

        </div>
    </div>
</section>

<!-- =========================
     WHY CHOOSE TRIFECTA
========================= -->
<section class="about-band bg-white">
    <div class="about-container">

        <div class="about-container-center">
            <span class="about-section-label-gold">
                <x-editable-text
                    key="about_why_label"
                    :value="$contexts['about_why_label'] ?? 'Why Choose TRIFECTA'"
                    tag="span"
                    class="inline-block"
                />
            </span>

            <h2 class="about-section-title-dark">
                <x-editable-text
                    key="about_why_title"
                    :value="$contexts['about_why_title'] ?? 'Protecting Filipino Families with Care'"
                    tag="span"
                    class="inline-block"
                />
            </h2>

            <p class="mt-5 text-lg text-slate-600 leading-8">
                <x-editable-text
                    key="about_why_desc"
                    :value="$contexts['about_why_desc'] ?? 'TRIFECTA is more than a membership program. It is a community built on compassion, protection, and solidarity. We strive to ensure that every Filipino family has access to affordable assistance during life\'s most difficult moments.'"
                    tag="span"
                    class="block"
                />
            </p>
        </div>

        <div class="about-feature-grid">

            <div class="about-feature-card">
                <div class="about-feature-icon-blue"><flux:icon.shield-check class="w-8 h-8" /></div>
                <h3 class="about-feature-title">
                    <x-editable-text key="about_feat_title_1" :value="$contexts['about_feat_title_1'] ?? 'Affordable Protection'" tag="span" class="inline-block" />
                </h3>
                <p class="about-feature-text">
                    <x-editable-text key="about_feat_text_1" :value="$contexts['about_feat_text_1'] ?? 'Community-based protection programs that every Filipino family can afford.'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-feature-card">
                <div class="about-feature-icon-gold"><flux:icon.heart class="w-8 h-8" /></div>
                <h3 class="about-feature-title">
                    <x-editable-text key="about_feat_title_2" :value="$contexts['about_feat_title_2'] ?? 'Caring Community'" tag="span" class="inline-block" />
                </h3>
                <p class="about-feature-text">
                    <x-editable-text key="about_feat_text_2" :value="$contexts['about_feat_text_2'] ?? 'We strengthen families through cooperation, compassion, and mutual support.'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-feature-card">
                <div class="about-feature-icon-blue"><flux:icon.user-group class="w-8 h-8" /></div>
                <h3 class="about-feature-title">
                    <x-editable-text key="about_feat_title_3" :value="$contexts['about_feat_title_3'] ?? 'Community Solidarity'" tag="span" class="inline-block" />
                </h3>
                <p class="about-feature-text">
                    <x-editable-text key="about_feat_text_3" :value="$contexts['about_feat_text_3'] ?? 'We believe every member contributes to a stronger, united, and resilient community.'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-feature-card">
                <div class="about-feature-icon-gold"><flux:icon.sparkles class="w-8 h-8" /></div>
                <h3 class="about-feature-title">
                    <x-editable-text key="about_feat_title_4" :value="$contexts['about_feat_title_4'] ?? 'Hope for the Future'" tag="span" class="inline-block" />
                </h3>
                <p class="about-feature-text">
                    <x-editable-text key="about_feat_text_4" :value="$contexts['about_feat_text_4'] ?? 'Together we build a future where no family is left behind during times of hardship.'" tag="span" class="inline-block" />
                </p>
            </div>

        </div>
    </div>
</section>

<!-- =========================
     TRIFECTA HIGHLIGHTS
========================= -->
<section class="about-highlights-section">
    <div class="about-container">

        <div class="text-center">
            <span class="about-section-label-yellow">
                <x-editable-text
                    key="about_glance_label"
                    :value="$contexts['about_glance_label'] ?? 'TRIFECTA at a Glance'"
                    tag="span"
                    class="inline-block"
                />
            </span>

            <h2 class="about-section-title-white">
                <x-editable-text
                    key="about_glance_title"
                    :value="$contexts['about_glance_title'] ?? 'Our Commitment'"
                    tag="span"
                    class="inline-block"
                />
            </h2>
        </div>

        <div class="about-highlights-grid">

            <div class="about-highlight-card">
                <div class="about-highlight-icon"><flux:icon.home-modern class="w-12 h-12" /></div>
                <h3 class="about-highlight-title">
                    <x-editable-text key="about_high_title_1" :value="$contexts['about_high_title_1'] ?? 'Family'" tag="span" class="inline-block" />
                </h3>
                <p class="about-highlight-text">
                    <x-editable-text key="about_high_text_1" :value="$contexts['about_high_text_1'] ?? 'Every decision begins with protecting Filipino families.'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-highlight-card">
                <div class="about-highlight-icon"><flux:icon.shield-check class="w-12 h-12" /></div>
                <h3 class="about-highlight-title">
                    <x-editable-text key="about_high_title_2" :value="$contexts['about_high_title_2'] ?? 'Protection'" tag="span" class="inline-block" />
                </h3>
                <p class="about-highlight-text">
                    <x-editable-text key="about_high_text_2" :value="$contexts['about_high_text_2'] ?? 'Affordable community-based welfare and financial assistance.'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-highlight-card">
                <div class="about-highlight-icon"><flux:icon.user-group class="w-12 h-12" /></div>
                <h3 class="about-highlight-title">
                    <x-editable-text key="about_high_title_3" :value="$contexts['about_high_title_3'] ?? 'Unity'" tag="span" class="inline-block" />
                </h3>
                <p class="about-highlight-text">
                    <x-editable-text key="about_high_text_3" :value="$contexts['about_high_text_3'] ?? 'Working together through solidarity and cooperation.'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-highlight-card">
                <div class="about-highlight-icon"><flux:icon.star class="w-12 h-12" /></div>
                <h3 class="about-highlight-title">
                    <x-editable-text key="about_high_title_4" :value="$contexts['about_high_title_4'] ?? 'Hope'" tag="span" class="inline-block" />
                </h3>
                <p class="about-highlight-text">
                    <x-editable-text key="about_high_text_4" :value="$contexts['about_high_text_4'] ?? 'Bringing dignity, confidence, and hope during difficult times.'" tag="span" class="inline-block" />
                </p>
            </div>

        </div>
    </div>
</section>

<!-- =========================
     CALL TO ACTION
========================= -->
<section class="about-cta-section">
    <div class="about-cta-glow-left"></div>
    <div class="about-cta-glow-right"></div>

    <div class="about-cta-container">

        <span class="about-section-label-yellow tracking-[0.3em]">
            <x-editable-text
                key="about_cta_label"
                :value="$contexts['about_cta_label'] ?? 'Join Our Community'"
                tag="span"
                class="inline-block"
            />
        </span>

        <h2 class="about-cta-title">
            <x-editable-text
                key="about_cta_title_1"
                :value="$contexts['about_cta_title_1'] ?? 'Together We Can Build a'"
                tag="span"
                class="inline-block"
            />
            <span class="text-white">
                <x-editable-text
                    key="about_cta_title_highlight"
                    :value="$contexts['about_cta_title_highlight'] ?? 'Safer and Stronger Future'"
                    tag="span"
                    class="inline-block"
                />
            </span>
        </h2>

        <p class="about-cta-description">
            <x-editable-text
                key="about_cta_description"
                :value="$contexts['about_cta_description'] ?? 'Become part of the TRIFECTA family and experience community-based protection, financial literacy, and compassionate assistance that helps every Filipino family move forward with confidence and hope.'"
                tag="span"
                class="block"
            />
        </p>

        <div class="about-btn-group-center">
            @if($joinAllow)
                <button
                    onclick="Livewire.dispatch('open-application-modal')"
                    class="about-btn-primary cursor-pointer">

                    <x-editable-text
                        key="about_cta_btn_primary"
                        :value="$contexts['about_cta_btn_primary'] ?? 'Become a Member'"
                        tag="span"
                        class="inline-block"
                    />

                </button>
            @else
                <button
                    type="button"
                    disabled
                    class="about-btn-primary cursor-not-allowed opacity-60">

                    <span class="inline-block">
                        Join Unavailable
                    </span>

                </button>
            @endif

            <a
                href="{{ route('contact') }}"
                class="about-btn-secondary text-center">
                <x-editable-text
                    key="about_cta_btn_secondary"
                    :value="$contexts['about_cta_btn_secondary'] ?? 'Contact Us'"
                    tag="span"
                    class="inline-block"
                />
            </a>
        </div>

    </div>
</section>

<!-- =========================
     QUOTE SECTION
========================= -->
<section class="about-quote-section">
    <div class="about-quote-container">

        <div class="about-quote-icon">❝</div>

        <blockquote class="about-quote-text">
            <x-editable-text
                key="about_quote_text"
                :value="$contexts['about_quote_text'] ?? '&quot;Sa TRIFECTA, naniniwala kami na ang tunay na lakas ng isang komunidad ay nagmumula sa pagtutulungan, pagkakaisa, at malasakit sa bawat pamilyang Pilipino.&quot;'"
                tag="span"
                class="block"
            />
        </blockquote>

        <div class="about-quote-divider"></div>

    </div>
</section>

</div>
