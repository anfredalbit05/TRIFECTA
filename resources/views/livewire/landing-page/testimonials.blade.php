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

            @forelse($testimonials as $testimonial)
                <div class="testimonial-card">
                    <div class="testimonial-quote">❝</div>

                    <!-- Rating Stars Output -->
                    <div class="testimonial-rating mb-2" style="color: #f59e0b; font-size: 0.9rem;">
                        @for($i = 1; $i <= 5; $i++)
                            <span>{{ $i <= $testimonial->rate ? '★' : '☆' }}</span>
                        @endfor
                    </div>

                    <p class="testimonial-body">
                        "{{ $testimonial->testimony }}"
                    </p>

                    <div class="testimonial-footer">
                        <h4 class="testimonial-name">{{ $testimonial->fullname }}</h4>
                        <p class="testimonial-role">{{ $testimonial->designation ?? 'Member' }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-8 text-gray-500">
                    <p>No testimonials available at the moment.</p>
                </div>
            @endforelse

        </div>


<!-- Custom Pagination Controls -->
<div class="mt-8 flex justify-center items-center space-x-4">

    {{-- Previous Page Link --}}
    @if ($testimonials->onFirstPage())
        <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed">
            Previous
        </span>
    @else
        <button
            wire:click="previousPage"
            wire:loading.attr="disabled"
            class="px-4 py-2 text-white rounded transition cursor-pointer"
            style="background-color: #D79B10;"
            onmouseover="this.style.backgroundColor='#c58d0f'"
            onmouseout="this.style.backgroundColor='#D79B10'"
        >
            Previous
        </button>
    @endif

    <span class="text-sm text-gray-600">
        Page {{ $testimonials->currentPage() }} of {{ $testimonials->lastPage() }}
    </span>

    {{-- Next Page Link --}}
    @if ($testimonials->hasMorePages())
        <button
            wire:click="nextPage"
            wire:loading.attr="disabled"
            class="px-4 py-2 text-white rounded transition cursor-pointer"
            style="background-color: #0C2C6B;"
            onmouseover="this.style.backgroundColor='#0a2559'"
            onmouseout="this.style.backgroundColor='#0C2C6B'"
        >
            Next
        </button>
    @else
        <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed">
            Next
        </span>
    @endif

</div>

<!-- =========================
     HIGHLIGHTS
========================= -->
<section class="testimonials-highlights">
    <div class="testimonials-container">
        <div class="testimonials-highlights-grid">

            <div>
                <div class="testimonial-highlight-icon"><flux:icon.heart class="w-11 h-11" /></div>
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
                <div class="testimonial-highlight-icon"><flux:icon.user-group class="w-11 h-11" /></div>
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
                <div class="testimonial-highlight-icon"><flux:icon.shield-check class="w-11 h-11" /></div>
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
                <div class="testimonial-highlight-icon"><flux:icon.star class="w-11 h-11" /></div>
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
