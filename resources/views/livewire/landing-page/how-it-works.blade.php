<div>

<!-- ======================================
     HERO SECTION
====================================== -->
<section class="hiw-hero">

    <div class="hiw-hero-glow-top"></div>
    <div class="hiw-hero-glow-bottom"></div>

    <div class="hiw-container-center">

        <span class="hiw-badge">
            <x-editable-text
                key="hiw_hero_badge"
                :value="$contexts['hiw_hero_badge'] ?? ''"
                tag="span"
                class="inline-block"
            />
        </span>

        <h1 class="hiw-title">
            <x-editable-text
                key="hiw_hero_title_1"
                :value="$contexts['hiw_hero_title_1'] ?? ''"
                tag="span"
                class="inline-block"
            />
            <br class="hidden sm:block">

            <span class="hiw-title-highlight">
                <x-editable-text
                    key="hiw_hero_title_highlight"
                    :value="$contexts['hiw_hero_title_highlight'] ?? ''"
                    tag="span"
                    class="inline-block"
                />
            </span>
        </h1>

        <p class="hiw-description">
            <x-editable-text
                key="hiw_hero_desc_part1"
                :value="$contexts['hiw_hero_desc_part1'] ?? ''"
                tag="span"
                class="inline-block"
            />
            <strong>
                <x-editable-text
                    key="hiw_hero_desc_cocogen"
                    :value="$contexts['hiw_hero_desc_cocogen'] ?? ''"
                    tag="span"
                    class="inline-block"
                />
            </strong>
            <x-editable-text
                key="hiw_hero_desc_part2"
                :value="$contexts['hiw_hero_desc_part2'] ?? ''"
                tag="span"
                class="inline-block"
            />
        </p>

    </div>

</section>

<!-- ======================================
     MEMBERSHIP PROCESS
====================================== -->
<section class="hiw-section-light">
    <div class="hiw-container">

        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="hiw-section-label">
                <x-editable-text
                    key="hiw_process_label"
                    :value="$contexts['hiw_process_label'] ?? ''"
                    tag="span"
                    class="inline-block"
                />
            </span>

            <h2 class="hiw-section-title">
                <x-editable-text
                    key="hiw_process_title"
                    :value="$contexts['hiw_process_title'] ?? ''"
                    tag="span"
                    class="inline-block"
                />
            </h2>

            <p class="hiw-section-description">
                <x-editable-text
                    key="hiw_process_description"
                    :value="$contexts['hiw_process_description'] ?? ''"
                    tag="span"
                    class="block"
                />
            </p>
        </div>

        <div class="hiw-steps-grid">

            <!-- STEP 1 -->
            <div class="hiw-step-card">
                <div>
                    <div class="hiw-step-number">
                        <x-editable-text
                            key="hiw_step1_number"
                            :value="$contexts['hiw_step1_number'] ?? ''"
                            tag="span"
                            class="inline-block"
                        />
                    </div>

                    <h3 class="hiw-step-title">
                        <x-editable-text
                            key="hiw_step1_title"
                            :value="$contexts['hiw_step1_title'] ?? ''"
                            tag="span"
                            class="inline-block"
                        />
                    </h3>

                    <p class="hiw-step-text">
                        <x-editable-text
                            key="hiw_step1_text"
                            :value="$contexts['hiw_step1_text'] ?? ''"
                            tag="span"
                            class="block"
                        />
                    </p>
                </div>

                <div class="hiw-step-footer">
                    <x-editable-text
                        key="hiw_step1_footer"
                        :value="$contexts['hiw_step1_footer'] ?? ''"
                        tag="span"
                        class="inline-block"
                    />
                </div>
            </div>

            <!-- STEP 2 -->
            <div class="hiw-step-card">
                <div>
                    <div class="hiw-step-number">
                        <x-editable-text
                            key="hiw_step2_number"
                            :value="$contexts['hiw_step2_number'] ?? ''"
                            tag="span"
                            class="inline-block"
                        />
                    </div>

                    <h3 class="hiw-step-title">
                        <x-editable-text
                            key="hiw_step2_title"
                            :value="$contexts['hiw_step2_title'] ?? ''"
                            tag="span"
                            class="inline-block"
                        />
                    </h3>

                    <p class="hiw-step-text">
                        <x-editable-text
                            key="hiw_step2_text"
                            :value="$contexts['hiw_step2_text'] ?? ''"
                            tag="span"
                            class="block"
                        />
                    </p>
                </div>

                <div class="hiw-step-footer">
                    <x-editable-text
                        key="hiw_step2_footer"
                        :value="$contexts['hiw_step2_footer'] ?? ''"
                        tag="span"
                        class="inline-block"
                    />
                </div>
            </div>

            <!-- STEP 3 -->
            <div class="hiw-step-card">
                <div>
                    <div class="hiw-step-number">
                        <x-editable-text
                            key="hiw_step3_number"
                            :value="$contexts['hiw_step3_number'] ?? ''"
                            tag="span"
                            class="inline-block"
                        />
                    </div>

                    <h3 class="hiw-step-title">
                        <x-editable-text
                            key="hiw_step3_title"
                            :value="$contexts['hiw_step3_title'] ?? ''"
                            tag="span"
                            class="inline-block"
                        />
                    </h3>

                    <p class="hiw-step-text">
                        <x-editable-text
                            key="hiw_step3_text"
                            :value="$contexts['hiw_step3_text'] ?? ''"
                            tag="span"
                            class="block"
                        />
                    </p>
                </div>

                <div class="hiw-step-footer">
                    <x-editable-text
                        key="hiw_step3_footer"
                        :value="$contexts['hiw_step3_footer'] ?? ''"
                        tag="span"
                        class="inline-block"
                    />
                </div>
            </div>

            <!-- STEP 4 -->
            <div class="hiw-step-card">
                <div>
                    <div class="hiw-step-number">
                        <x-editable-text
                            key="hiw_step4_number"
                            :value="$contexts['hiw_step4_number'] ?? ''"
                            tag="span"
                            class="inline-block"
                        />
                    </div>

                    <h3 class="hiw-step-title">
                        <x-editable-text
                            key="hiw_step4_title"
                            :value="$contexts['hiw_step4_title'] ?? ''"
                            tag="span"
                            class="inline-block"
                        />
                    </h3>

                    <p class="hiw-step-text">
                        <x-editable-text
                            key="hiw_step4_text"
                            :value="$contexts['hiw_step4_text'] ?? ''"
                            tag="span"
                            class="block"
                        />
                    </p>
                </div>

                <div class="hiw-step-footer">
                    <x-editable-text
                        key="hiw_step4_footer"
                        :value="$contexts['hiw_step4_footer'] ?? ''"
                        tag="span"
                        class="inline-block"
                    />
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ======================================
     HOW CLAIMS WORK
====================================== -->
<section class="hiw-section-white">
    <div class="hiw-container">

        <div class="hiw-claims-grid">

            <!-- LEFT COLUMN -->
            <div class="hiw-claims-left">

                <span class="hiw-section-label">
                    <x-editable-text
                        key="hiw_claims_label"
                        :value="$contexts['hiw_claims_label'] ?? ''"
                        tag="span"
                        class="inline-block"
                    />
                </span>

                <h2 class="hiw-claims-title">
                    <x-editable-text
                        key="hiw_claims_title"
                        :value="$contexts['hiw_claims_title'] ?? ''"
                        tag="span"
                        class="inline-block"
                    />
                </h2>

                <p class="hiw-claims-text">
                    <x-editable-text
                        key="hiw_claims_text"
                        :value="$contexts['hiw_claims_text'] ?? ''"
                        tag="span"
                        class="block"
                    />
                </p>

                <div class="hiw-claim-list">

                    <!-- Claim Step 1 -->
                    <div class="hiw-claim-item">
                        <div class="hiw-claim-number">
                            <x-editable-text
                                key="hiw_claim1_number"
                                :value="$contexts['hiw_claim1_number'] ?? ''"
                                tag="span"
                                class="inline-block"
                            />
                        </div>

                        <div>
                            <h4 class="hiw-claim-title">
                                <x-editable-text
                                    key="hiw_claim1_title"
                                    :value="$contexts['hiw_claim1_title'] ?? ''"
                                    tag="span"
                                    class="inline-block"
                                />
                            </h4>

                            <p class="hiw-claim-text">
                                <x-editable-text
                                    key="hiw_claim1_text"
                                    :value="$contexts['hiw_claim1_text'] ?? ''"
                                    tag="span"
                                    class="block"
                                />
                            </p>
                        </div>
                    </div>

                    <!-- Claim Step 2 -->
                    <div class="hiw-claim-item">
                        <div class="hiw-claim-number">
                            <x-editable-text
                                key="hiw_claim2_number"
                                :value="$contexts['hiw_claim2_number'] ?? ''"
                                tag="span"
                                class="inline-block"
                            />
                        </div>

                        <div>
                            <h4 class="hiw-claim-title">
                                <x-editable-text
                                    key="hiw_claim2_title"
                                    :value="$contexts['hiw_claim2_title'] ?? ''"
                                    tag="span"
                                    class="inline-block"
                                />
                            </h4>

                            <p class="hiw-claim-text">
                                <x-editable-text
                                    key="hiw_claim2_text"
                                    :value="$contexts['hiw_claim2_text'] ?? ''"
                                    tag="span"
                                    class="block"
                                />
                            </p>
                        </div>
                    </div>

                    <!-- Claim Step 3 -->
                    <div class="hiw-claim-item">
                        <div class="hiw-claim-number">
                            <x-editable-text
                                key="hiw_claim3_number"
                                :value="$contexts['hiw_claim3_number'] ?? ''"
                                tag="span"
                                class="inline-block"
                            />
                        </div>

                        <div>
                            <h4 class="hiw-claim-title">
                                <x-editable-text
                                    key="hiw_claim3_title"
                                    :value="$contexts['hiw_claim3_title'] ?? ''"
                                    tag="span"
                                    class="inline-block"
                                />
                            </h4>

                            <p class="hiw-claim-text">
                                <x-editable-text
                                    key="hiw_claim3_text"
                                    :value="$contexts['hiw_claim3_text'] ?? ''"
                                    tag="span"
                                    class="block"
                                />
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="hiw-claims-right">

                <div class="hiw-coverage-card">

                    <div class="hiw-coverage-glow"></div>

                    <span class="hiw-coverage-label">
                        <x-editable-text
                            key="hiw_coverage_label"
                            :value="$contexts['hiw_coverage_label'] ?? ''"
                            tag="span"
                            class="inline-block"
                        />
                    </span>

                    <h3 class="hiw-coverage-title">
                        <x-editable-text
                            key="hiw_coverage_title"
                            :value="$contexts['hiw_coverage_title'] ?? ''"
                            tag="span"
                            class="inline-block"
                        />
                    </h3>

                    <p class="hiw-coverage-description">
                        <x-editable-text
                            key="hiw_coverage_description"
                            :value="$contexts['hiw_coverage_description'] ?? ''"
                            tag="span"
                            class="block"
                        />
                    </p>

                    <div class="hiw-tier-list">

                        <!-- Bronze -->
                        <div class="hiw-tier-card">
                            <div>
                                <h5 class="hiw-tier-name-gold">
                                    <x-editable-text
                                        key="hiw_tier_bronze_title"
                                        :value="$contexts['hiw_tier_bronze_title'] ?? ''"
                                        tag="span"
                                        class="inline-block"
                                    />
                                </h5>

                                <p class="hiw-tier-description">
                                    <x-editable-text
                                        key="hiw_tier_bronze_desc"
                                        :value="$contexts['hiw_tier_bronze_desc'] ?? ''"
                                        tag="span"
                                        class="block"
                                    />
                                </p>
                            </div>

                            <span class="hiw-tier-price">
                                <x-editable-text
                                    key="hiw_tier_bronze_price"
                                    :value="$contexts['hiw_tier_bronze_price'] ?? ''"
                                    tag="span"
                                    class="inline-block"
                                />
                            </span>
                        </div>

                        <!-- Silver -->
                        <div class="hiw-tier-card">
                            <div>
                                <h5 class="hiw-tier-name">
                                    <x-editable-text
                                        key="hiw_tier_silver_title"
                                        :value="$contexts['hiw_tier_silver_title'] ?? ''"
                                        tag="span"
                                        class="inline-block"
                                    />
                                </h5>

                                <p class="hiw-tier-description">
                                    <x-editable-text
                                        key="hiw_tier_silver_desc"
                                        :value="$contexts['hiw_tier_silver_desc'] ?? ''"
                                        tag="span"
                                        class="block"
                                    />
                                </p>
                            </div>

                            <span class="hiw-tier-price">
                                <x-editable-text
                                    key="hiw_tier_silver_price"
                                    :value="$contexts['hiw_tier_silver_price'] ?? ''"
                                    tag="span"
                                    class="inline-block"
                                />
                            </span>
                        </div>

                        <!-- Gold -->
                        <div class="hiw-tier-card">
                            <div>
                                <h5 class="hiw-tier-name-gold">
                                    <x-editable-text
                                        key="hiw_tier_gold_title"
                                        :value="$contexts['hiw_tier_gold_title'] ?? ''"
                                        tag="span"
                                        class="inline-block"
                                    />
                                </h5>

                                <p class="hiw-tier-description">
                                    <x-editable-text
                                        key="hiw_tier_gold_desc"
                                        :value="$contexts['hiw_tier_gold_desc'] ?? ''"
                                        tag="span"
                                        class="block"
                                    />
                                </p>
                            </div>

                            <span class="hiw-tier-price">
                                <x-editable-text
                                    key="hiw_tier_gold_price"
                                    :value="$contexts['hiw_tier_gold_price'] ?? ''"
                                    tag="span"
                                    class="inline-block"
                                />
                            </span>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ======================================
     FREQUENTLY ASKED QUESTIONS
====================================== -->
<section class="hiw-section-light">
    <div class="hiw-faq-container">

        <div class="text-center mb-12">
            <span class="hiw-section-label">
                <x-editable-text
                    key="hiw_faq_label"
                    :value="$contexts['hiw_faq_label'] ?? ''"
                    tag="span"
                    class="inline-block"
                />
            </span>

            <h2 class="hiw-section-title">
                <x-editable-text
                    key="hiw_faq_title"
                    :value="$contexts['hiw_faq_title'] ?? ''"
                    tag="span"
                    class="inline-block"
                />
            </h2>
        </div>

        <div class="hiw-faq-list">

            @forelse($faqs as $faq)

                <div class="hiw-faq-card">

                    <h3 class="hiw-faq-question">
                        {{ $faq->question }}
                    </h3>

                    <p class="hiw-faq-answer">
                        {{ $faq->answer }}
                    </p>

                </div>

            @empty

                <div class="hiw-faq-card">

                    <h3 class="hiw-faq-question">
                        No FAQs available.
                    </h3>

                </div>

            @endforelse

        </div>
    </div>
</section>

<!-- ======================================
     CALL TO ACTION
====================================== -->
<section class="hiw-cta">
    <div class="hiw-cta-container">

        <h2 class="hiw-cta-title">
            <x-editable-text
                key="hiw_cta_title"
                :value="$contexts['hiw_cta_title'] ?? ''"
                tag="span"
                class="inline-block"
            />
        </h2>

        <p class="hiw-cta-text">
            <x-editable-text
                key="hiw_cta_text"
                :value="$contexts['hiw_cta_text'] ?? ''"
                tag="span"
                class="block"
            />
        </p>

        <div class="hiw-cta-buttons">
            <a href="#register" class="hiw-btn-primary">
                <x-editable-text
                    key="hiw_cta_btn_primary"
                    :value="$contexts['hiw_cta_btn_primary'] ?? ''"
                    tag="span"
                    class="inline-block"
                />
            </a>

            <a href="#contact" class="hiw-btn-secondary">
                <x-editable-text
                    key="hiw_cta_btn_secondary"
                    :value="$contexts['hiw_cta_btn_secondary'] ?? ''"
                    tag="span"
                    class="inline-block"
                />
            </a>
        </div>

    </div>
</section>

</div>
