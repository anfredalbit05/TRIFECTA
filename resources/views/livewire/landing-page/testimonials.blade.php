<div>

<!-- ======================================
     HERO SECTION
====================================== -->
<section class="testimonials-hero">

    <div class="testimonials-hero-glow-top"></div>
    <div class="testimonials-hero-glow-bottom"></div>

    <div class="testimonials-container-center">

        <span class="testimonials-tag">
            <x-editable-text
                key="testimonials_hero_tag"
                :value="$contexts['testimonials_hero_tag'] ?? 'TrueLife Dayongan Solidaritas Social Welfare Services, Inc.'"
                tag="span"
                class="inline-block"
            />
        </span>

        <h1 class="testimonials-title">
            <x-editable-text
                key="testimonials_hero_title_1"
                :value="$contexts['testimonials_hero_title_1'] ?? 'What Our'"
                tag="span"
                class="inline-block"
            />
            <br class="hidden sm:block">
            <span class="testimonials-title-highlight">
                <x-editable-text
                    key="testimonials_hero_title_highlight"
                    :value="$contexts['testimonials_hero_title_highlight'] ?? 'Members Say'"
                    tag="span"
                    class="inline-block"
                />
            </span>
        </h1>

        <p class="testimonials-description">
            <x-editable-text
                key="testimonials_hero_description"
                :value="$contexts['testimonials_hero_description'] ?? 'Hear inspiring stories from members who have experienced the compassion, protection, and support provided by TRUELIFE DAYONGAN SOLIDARITAS. Their experiences reflect our commitment to serving families, providing reliable assistance, and strengthening communities through care, trust, and meaningful support.'"
                tag="span"
                class="block"
            />
        </p>

    </div>

</section>

<!-- =========================
     INTRODUCTION
========================= -->
<section class="testimonials-intro">
    <div class="testimonials-intro-container">
        <span class="testimonials-small-title">
            <x-editable-text
                key="testimonials_intro_small_title"
                :value="$contexts['testimonials_intro_small_title'] ?? 'Real Experiences'"
                tag="span"
                class="inline-block"
            />
        </span>

        <h2 class="testimonials-heading">
            <x-editable-text
                key="testimonials_intro_heading"
                :value="$contexts['testimonials_intro_heading'] ?? 'Stories That Inspire'"
                tag="span"
                class="inline-block"
            />
        </h2>

        <p class="testimonials-text">
            <x-editable-text
                key="testimonials_intro_text"
                :value="$contexts['testimonials_intro_text'] ?? 'Every family we serve has a unique story. These testimonials reflect the trust and confidence our members have in DAYONGAN\'s commitment to protecting Filipino families.'"
                tag="span"
                class="block"
            />
        </p>
    </div>
</section>

<!-- =========================
     TESTIMONIALS
========================= -->
<section class="testimonials-section">
    <div class="testimonials-container">
        <div class="testimonials-grid">

            <!-- Testimonial 1 -->
            <div class="testimonial-card">
                <div class="testimonial-quote">❝</div>

                <p class="testimonial-body">
                    DAYONGAN helped our family during a difficult time.
                    Their support gave us peace of mind and financial relief.
                </p>

                <div class="testimonial-footer">
                    <h4 class="testimonial-name">Maria Santos</h4>
                    <p class="testimonial-role">Member</p>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="testimonial-card">
                <div class="testimonial-quote">❝</div>

                <p class="testimonial-body">
                    I appreciate the kindness and professionalism of the
                    DAYONGAN team. They truly care about every member.
                </p>

                <div class="testimonial-footer">
                    <h4 class="testimonial-name">Juan Dela Cruz</h4>
                    <p class="testimonial-role">Member</p>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="testimonial-card">
                <div class="testimonial-quote">❝</div>

                <p class="testimonial-body">
                    Affordable protection and genuine concern for the community.
                    I highly recommend DAYONGAN to every Filipino family.
                </p>

                <div class="testimonial-footer">
                    <h4 class="testimonial-name">Ana Rodriguez</h4>
                    <p class="testimonial-role">Member</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- =========================
     HIGHLIGHTS
========================= -->
<section class="testimonials-highlights">
    <div class="testimonials-container">
        <div class="testimonials-highlights-grid">

            <div>
                <div class="testimonial-highlight-icon">❤️</div>
                <h3 class="testimonial-highlight-title">
                    <x-editable-text
                        key="testimonials_highlight_1"
                        :value="$contexts['testimonials_highlight_1'] ?? 'Compassion'"
                        tag="span"
                        class="inline-block"
                    />
                </h3>
            </div>

            <div>
                <div class="testimonial-highlight-icon">🤝</div>
                <h3 class="testimonial-highlight-title">
                    <x-editable-text
                        key="testimonials_highlight_2"
                        :value="$contexts['testimonials_highlight_2'] ?? 'Unity'"
                        tag="span"
                        class="inline-block"
                    />
                </h3>
            </div>

            <div>
                <div class="testimonial-highlight-icon">🛡️</div>
                <h3 class="testimonial-highlight-title">
                    <x-editable-text
                        key="testimonials_highlight_3"
                        :value="$contexts['testimonials_highlight_3'] ?? 'Protection'"
                        tag="span"
                        class="inline-block"
                    />
                </h3>
            </div>

            <div>
                <div class="testimonial-highlight-icon">⭐</div>
                <h3 class="testimonial-highlight-title">
                    <x-editable-text
                        key="testimonials_highlight_4"
                        :value="$contexts['testimonials_highlight_4'] ?? 'Hope'"
                        tag="span"
                        class="inline-block"
                    />
                </h3>
            </div>

        </div>
    </div>
</section>

<!-- =========================
     CALL TO ACTION
========================= -->
<section class="testimonials-cta">
    <div class="testimonials-intro-container">
        <span class="testimonials-small-title">
            <x-editable-text
                key="testimonials_cta_small_title"
                :value="$contexts['testimonials_cta_small_title'] ?? 'Join Us Today'"
                tag="span"
                class="inline-block"
            />
        </span>

        <h2 class="testimonials-heading">
            <x-editable-text
                key="testimonials_cta_heading"
                :value="$contexts['testimonials_cta_heading'] ?? 'Be Part of the DAYONGAN Family'"
                tag="span"
                class="inline-block"
            />
        </h2>

        <p class="testimonials-text">
            <x-editable-text
                key="testimonials_cta_text"
                :value="$contexts['testimonials_cta_text'] ?? 'Experience community-based protection and financial assistance designed to help Filipino families face life\'s challenges with confidence and hope.'"
                tag="span"
                class="block"
            />
        </p>

        <div class="testimonials-buttons">
            <button
                onclick="Livewire.dispatch('open-application-modal')"
                class="testimonial-btn-primary">
                <x-editable-text
                    key="testimonials_btn_primary"
                    :value="$contexts['testimonials_btn_primary'] ?? 'Become a Member'"
                    tag="span"
                    class="inline-block"
                />
            </button>


        </div>
    </div>
</section>

</div>
