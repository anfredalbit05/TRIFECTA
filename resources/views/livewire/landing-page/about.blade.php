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
                :value="$contexts['about_hero_badge'] ?? 'TrueLife Dayongan Solidaritas Social Welfare Services, Inc.'"
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
                :value="$contexts['about_hero_description'] ?? 'TRUELIFE DAYONGAN SOLIDARITAS Social Welfare Services and Financial Literacy Inc. is committed to providing affordable community-based protection and financial assistance for Filipino families. Through solidarity, compassion, protection, and hope, we empower communities to build a safer and stronger future.'"
                tag="span"
                class="block"
            />
        </p>

    </div>

</section>

<!-- =========================
     ABOUT DAYONGAN
========================= -->
<section class="py-20 bg-white">
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
                    :value="$contexts['about_who_we_are_title'] ?? 'About DAYONGAN'"
                    tag="span"
                    class="inline-block"
                />
            </h2>

            <p class="mt-6 text-slate-600 text-lg leading-8">
                <x-editable-text
                    key="about_who_we_are_text"
                    :value="$contexts['about_who_we_are_text'] ?? 'DAYONGAN believes that every Filipino family deserves affordable protection, dignity, and hope. Through community cooperation and financial literacy, we provide accessible welfare services that strengthen families during life\'s most difficult moments.'"
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
<section class="py-20 bg-slate-50 border-y border-slate-200">
    <div class="about-container">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

            <!-- Left Side: Information -->
            <div class="lg:col-span-6 text-left space-y-6">
                <span class="about-section-label-gold">
                    <x-editable-text
                        key="about_cert_label"
                        :value="$contexts['about_cert_label'] ?? 'Official Registration'"
                        tag="span"
                        class="inline-block"
                    />
                </span>

                <h2 class="about-section-title-dark">
                    <x-editable-text
                        key="about_cert_title"
                        :value="$contexts['about_cert_title'] ?? 'Certified & Registered DTI Enterprise'"
                        tag="span"
                        class="inline-block"
                    />
                </h2>

                <p class="text-slate-600 text-lg leading-relaxed">
                    <x-editable-text
                        key="about_cert_text"
                        :value="$contexts['about_cert_text'] ?? 'TrueLife Dayongan Solidaritas is officially registered under the Department of Trade and Industry (DTI), ensuring legal compliance, transparency, and formal commitment to serving Filipino families nationwide.'"
                        tag="span"
                        class="block"
                    />
                </p>

                <div class="pt-4 grid grid-cols-2 gap-4 text-sm text-slate-700 font-medium">
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                        <span class="block text-xs text-slate-400 uppercase tracking-wider">Business Name No.</span>
                        <span class="font-bold text-[#0B2A5B] text-base">8368575</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                        <span class="block text-xs text-slate-400 uppercase tracking-wider">Validity</span>
                        <span class="font-bold text-[#0B2A5B] text-base">July 22, 2031</span>
                    </div>
                </div>
            </div>

            <!-- Right Side: Certificate Preview Card -->
            <div class="lg:col-span-6 flex justify-center">
                <div class="relative w-full max-w-lg bg-white p-4 sm:p-6 rounded-2xl shadow-xl border border-slate-200 group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-[#D4A017] to-blue-500 rounded-2xl blur opacity-25 group-hover:opacity-40 transition duration-300"></div>

                    <div class="relative bg-slate-900 rounded-xl overflow-hidden p-6 text-center text-white flex flex-col items-center justify-center min-h-[320px]">
                        <!-- Replace with your actual asset path for the certificate image/pdf display -->
                        <div class="absolute inset-0 opacity-20 bg-cover bg-center" style="background-image: url('{{ asset('images/certificate-bg.jpg') }}');"></div>

                        <div class="relative z-10 space-y-4">
                            <span class="px-3 py-1 bg-[#D4A017] text-slate-950 font-bold text-xs uppercase tracking-widest rounded-full">
                                DTI Philippines
                            </span>
                            <h3 class="text-xl font-bold text-white">
                                Certificate of Business Name Registration
                            </h3>
                            <p class="text-sm text-slate-300 max-w-sm mx-auto">
                                TRUELIFE DAYONGAN SOLIDARITAS SOCIAL WELFARE SERVICES
                            </p>
                            <div class="pt-2">
                                <a href="{{ asset('images/dti-certificate.pdf') }}" target="_blank" class="inline-flex items-center justify-center px-5 py-2.5 bg-[#D4A017] hover:bg-[#b88b13] text-slate-950 font-semibold rounded-xl text-sm transition-colors shadow-md">
                                    View Full Certificate PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- =========================
     VISION & MISSION
========================= -->
<section class="py-20 bg-slate-50">
    <div class="about-container">
        <div class="about-vm-grid">

            <!-- Vision -->
            <div class="about-vm-card-vision">
                <div class="about-vm-icon">
                    <x-editable-text
                        key="about_vision_icon"
                        :value="$contexts['about_vision_icon'] ?? '👁️'"
                        tag="span"
                        class="inline-block"
                    />
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
                    <x-editable-text
                        key="about_mission_icon"
                        :value="$contexts['about_mission_icon'] ?? '🎯'"
                        tag="span"
                        class="inline-block"
                    />
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
                        :value="$contexts['about_mission_text'] ?? 'Ang DAYONGAN ay nagsisikap na magbigay ng abot-kaya at nakabatay sa komunidad na proteksyon at mga benepisyo upang matulungan, maprotektahan, at mapatatag ang buhay ng bawat pamilya sa pamamagitan ng diwa ng Pagtutulungan at Pagkakaisa.'"
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
<section class="py-20 bg-white">
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
                <div class="about-value-icon-blue">
                    <x-editable-text key="about_val_icon_1" :value="$contexts['about_val_icon_1'] ?? '🤝'" tag="span" class="inline-block" />
                </div>
                <h3 class="about-value-title">
                    <x-editable-text key="about_val_title_1" :value="$contexts['about_val_title_1'] ?? 'Pagtutulungan'" tag="span" class="inline-block" />
                </h3>
                <p class="about-value-text">
                    <x-editable-text key="about_val_text_1" :value="$contexts['about_val_text_1'] ?? 'Tulungan ang Kapwa'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-value-card">
                <div class="about-value-icon-gold">
                    <x-editable-text key="about_val_icon_2" :value="$contexts['about_val_icon_2'] ?? '🛡️'" tag="span" class="inline-block" />
                </div>
                <h3 class="about-value-title">
                    <x-editable-text key="about_val_title_2" :value="$contexts['about_val_title_2'] ?? 'Proteksyon'" tag="span" class="inline-block" />
                </h3>
                <p class="about-value-text">
                    <x-editable-text key="about_val_text_2" :value="$contexts['about_val_text_2'] ?? 'Paunang Pag-alaga'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-value-card">
                <div class="about-value-icon-blue">
                    <x-editable-text key="about_val_icon_3" :value="$contexts['about_val_icon_3'] ?? '👨‍👩‍👧‍👦'" tag="span" class="inline-block" />
                </div>
                <h3 class="about-value-title">
                    <x-editable-text key="about_val_title_3" :value="$contexts['about_val_title_3'] ?? 'Pamilya'" tag="span" class="inline-block" />
                </h3>
                <p class="about-value-text">
                    <x-editable-text key="about_val_text_3" :value="$contexts['about_val_text_3'] ?? 'Una sa Lahat'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-value-card">
                <div class="about-value-icon-gold">
                    <x-editable-text key="about_val_icon_4" :value="$contexts['about_val_icon_4'] ?? '✔️'" tag="span" class="inline-block" />
                </div>
                <h3 class="about-value-title">
                    <x-editable-text key="about_val_title_4" :value="$contexts['about_val_title_4'] ?? 'Integridad'" tag="span" class="inline-block" />
                </h3>
                <p class="about-value-text">
                    <x-editable-text key="about_val_text_4" :value="$contexts['about_val_text_4'] ?? 'Tapat at Totoo'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-value-card">
                <div class="about-value-icon-blue">
                    <x-editable-text key="about_val_icon_5" :value="$contexts['about_val_icon_5'] ?? '❤️'" tag="span" class="inline-block" />
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
                        <div class="about-objective-check">✔</div>
                        <p>
                            <x-editable-text key="about_obj_1" :value="$contexts['about_obj_1'] ?? 'Magbigay ng barato ug maabot nga serbisyo sa proteksyon alang sa ordinaryong pamilya Pilipino.'" tag="span" class="inline-block" />
                        </p>
                    </div>

                    <div class="about-objective-item">
                        <div class="about-objective-check">✔</div>
                        <p>
                            <x-editable-text key="about_obj_2" :value="$contexts['about_obj_2'] ?? 'Magtabang dayon sa panahon sa aksidente, pagkasakit, ug kamatayon.'" tag="span" class="inline-block" />
                        </p>
                    </div>

                    <div class="about-objective-item">
                        <div class="about-objective-check">✔</div>
                        <p>
                            <x-editable-text key="about_obj_3" :value="$contexts['about_obj_3'] ?? 'Magpahiug-ot sa panaghiusa, pagtinabangay, ug pag-amuma sa komunidad.'" tag="span" class="inline-block" />
                        </p>
                    </div>

                    <div class="about-objective-item">
                        <div class="about-objective-check">✔</div>
                        <p>
                            <x-editable-text key="about_obj_4" :value="$contexts['about_obj_4'] ?? 'Maghatag og paglaum ug seguridad sa mga kabus ug nanginahanglan.'" tag="span" class="inline-block" />
                        </p>
                    </div>

                    <div class="about-objective-item">
                        <div class="about-objective-check">✔</div>
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
                    alt="DAYONGAN Objectives"
                    class="about-hero-img">
            </div>

        </div>
    </div>
</section>

<!-- =========================
     WHY CHOOSE DAYONGAN
========================= -->
<section class="py-20 bg-slate-50">
    <div class="about-container">

        <div class="about-container-center">
            <span class="about-section-label-gold">
                <x-editable-text
                    key="about_why_label"
                    :value="$contexts['about_why_label'] ?? 'Why Choose DAYONGAN'"
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
                    :value="$contexts['about_why_desc'] ?? 'DAYONGAN is more than a membership program. It is a community built on compassion, protection, and solidarity. We strive to ensure that every Filipino family has access to affordable assistance during life\'s most difficult moments.'"
                    tag="span"
                    class="block"
                />
            </p>
        </div>

        <div class="about-feature-grid">

            <div class="about-feature-card">
                <div class="about-feature-icon-blue">🛡️</div>
                <h3 class="about-feature-title">
                    <x-editable-text key="about_feat_title_1" :value="$contexts['about_feat_title_1'] ?? 'Affordable Protection'" tag="span" class="inline-block" />
                </h3>
                <p class="about-feature-text">
                    <x-editable-text key="about_feat_text_1" :value="$contexts['about_feat_text_1'] ?? 'Community-based protection programs that every Filipino family can afford.'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-feature-card">
                <div class="about-feature-icon-gold">❤️</div>
                <h3 class="about-feature-title">
                    <x-editable-text key="about_feat_title_2" :value="$contexts['about_feat_title_2'] ?? 'Caring Community'" tag="span" class="inline-block" />
                </h3>
                <p class="about-feature-text">
                    <x-editable-text key="about_feat_text_2" :value="$contexts['about_feat_text_2'] ?? 'We strengthen families through cooperation, compassion, and mutual support.'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-feature-card">
                <div class="about-feature-icon-blue">🤝</div>
                <h3 class="about-feature-title">
                    <x-editable-text key="about_feat_title_3" :value="$contexts['about_feat_title_3'] ?? 'Community Solidarity'" tag="span" class="inline-block" />
                </h3>
                <p class="about-feature-text">
                    <x-editable-text key="about_feat_text_3" :value="$contexts['about_feat_text_3'] ?? 'We believe every member contributes to a stronger, united, and resilient community.'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-feature-card">
                <div class="about-feature-icon-gold">🌱</div>
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
     DAYONGAN HIGHLIGHTS
========================= -->
<section class="about-highlights-section">
    <div class="about-container">

        <div class="text-center">
            <span class="about-section-label-yellow">
                <x-editable-text
                    key="about_glance_label"
                    :value="$contexts['about_glance_label'] ?? 'DAYONGAN at a Glance'"
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
                <div class="about-highlight-icon">👨‍👩‍👧‍👦</div>
                <h3 class="about-highlight-title">
                    <x-editable-text key="about_high_title_1" :value="$contexts['about_high_title_1'] ?? 'Family'" tag="span" class="inline-block" />
                </h3>
                <p class="about-highlight-text">
                    <x-editable-text key="about_high_text_1" :value="$contexts['about_high_text_1'] ?? 'Every decision begins with protecting Filipino families.'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-highlight-card">
                <div class="about-highlight-icon">🛡️</div>
                <h3 class="about-highlight-title">
                    <x-editable-text key="about_high_title_2" :value="$contexts['about_high_title_2'] ?? 'Protection'" tag="span" class="inline-block" />
                </h3>
                <p class="about-highlight-text">
                    <x-editable-text key="about_high_text_2" :value="$contexts['about_high_text_2'] ?? 'Affordable community-based welfare and financial assistance.'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-highlight-card">
                <div class="about-highlight-icon">🤝</div>
                <h3 class="about-highlight-title">
                    <x-editable-text key="about_high_title_3" :value="$contexts['about_high_title_3'] ?? 'Unity'" tag="span" class="inline-block" />
                </h3>
                <p class="about-highlight-text">
                    <x-editable-text key="about_high_text_3" :value="$contexts['about_high_text_3'] ?? 'Working together through solidarity and cooperation.'" tag="span" class="inline-block" />
                </p>
            </div>

            <div class="about-highlight-card">
                <div class="about-highlight-icon">⭐</div>
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
            <span class="text-[#FFD54A]">
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
                :value="$contexts['about_cta_description'] ?? 'Become part of the DAYONGAN family and experience community-based protection, financial literacy, and compassionate assistance that helps every Filipino family move forward with confidence and hope.'"
                tag="span"
                class="block"
            />
        </p>

        <div class="about-btn-group-center">
            <button
                onclick="Livewire.dispatch('open-application-modal')"
                class="about-btn-primary">
                <x-editable-text
                    key="about_cta_btn_primary"
                    :value="$contexts['about_cta_btn_primary'] ?? 'Become a Member'"
                    tag="span"
                    class="inline-block"
                />
            </button>

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
                :value="$contexts['about_quote_text'] ?? '&quot;Sa DAYONGAN, naniniwala kami na ang tunay na lakas ng isang komunidad ay nagmumula sa pagtutulungan, pagkakaisa, at malasakit sa bawat pamilyang Pilipino.&quot;'"
                tag="span"
                class="block"
            />
        </blockquote>

        <div class="about-quote-divider"></div>

    </div>
</section>

</div>
