
@php
    $joinAllow = \Illuminate\Support\Facades\DB::table('settings')
        ->where('name', 'join_allow')
        ->value('value');
    $joinAllow = filter_var($joinAllow, FILTER_VALIDATE_BOOLEAN);
@endphp

<div class="trifecta-testimonials">

    {{-- HERO / BRAND INTRODUCTION --}}
    <section class="tm-hero">
        <div class="tm-hero-decoration tm-decoration-one"></div>
        <div class="tm-hero-decoration tm-decoration-two"></div>
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
            <div class="tm-hero-grid">
                <div class="tm-hero-content">
                    <span class="tm-eyebrow">
                        <span class="tm-eyebrow-line"></span>
                        <x-editable-text key="testimonials_hero_tag" :value="$contexts['testimonials_hero_tag'] ?? 'TRIFECTA MEMBER STORIES'" tag="span"/>
                    </span>
                    <h1 class="tm-hero-title">
                        <x-editable-text key="testimonials_hero_title_1" :value="$contexts['testimonials_hero_title_1'] ?? 'Trust Built on'" tag="span"/>
                        <span><x-editable-text key="testimonials_hero_title_highlight" :value="$contexts['testimonials_hero_title_highlight'] ?? 'Real Experiences'" tag="span"/></span>
                    </h1>
                    <p class="tm-hero-description">
                        <x-editable-text key="testimonials_hero_description" :value="$contexts['testimonials_hero_description'] ?? 'Discover the experiences of members whose lives have been touched by our commitment to care, protection, and community support.' " tag="span"/>
                    </p>
                    <a href="#member-stories" class="tm-hero-button">
                        Discover Their Stories <span>↓</span>
                    </a>
                    <div class="tm-hero-brand">
                        <span>TRIFECTA</span>
                        <i></i>
                        <small>HEALTH • WELLNESS • PROTECTION</small>
                    </div>
                </div>

                <div class="tm-hero-visual">
                    <div class="tm-visual-main">
                        <div class="tm-visual-top">
                            <span class="tm-visual-icon"><flux:icon.heart class="w-6 h-6"/></span>
                            <span>THE TRIFECTA PROMISE</span>
                        </div>
                        <div class="tm-visual-quote">“</div>
                        <h2>Every Story Matters.</h2>
                        <p>Every experience represents a relationship built on trust, care, and meaningful support.</p>
                        <div class="tm-visual-bottom">
                            <span class="tm-visual-line"></span>
                            <span>CARE THAT CONNECTS US</span>
                        </div>
                    </div>
                    <div class="tm-visual-accent tm-visual-gold">
                        <flux:icon.shield-check class="w-8 h-8"/>
                    </div>
                    <div class="tm-visual-accent tm-visual-navy">
                        <flux:icon.user-group class="w-8 h-8"/>
                    </div>
                    <div class="tm-visual-caption">
                        <span class="tm-caption-dot"></span>
                        PEOPLE. STORIES. TRUST.
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- MEMBER STORIES --}}
    <section class="tm-stories" id="member-stories">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
            <div class="tm-section-heading">
                <div>
                    <span class="tm-section-label">
                        <x-editable-text key="testimonials_intro_small_title" :value="$contexts['testimonials_intro_small_title'] ?? 'REAL MEMBER EXPERIENCES'" tag="span"/>
                    </span>
                    <h2 class="tm-section-title">
                        <x-editable-text key="testimonials_intro_heading" :value="$contexts['testimonials_intro_heading'] ?? 'Voices of Our Community'" tag="span"/>
                    </h2>
                </div>
                <p class="tm-section-description">
                    <x-editable-text key="testimonials_intro_text" :value="$contexts['testimonials_intro_text'] ?? 'Every family has a story. Explore the experiences and perspectives of our members.' " tag="span"/>
                </p>
            </div>

            @if($testimonials->count())
                <div class="tm-stories-grid">
                    @foreach($testimonials as $index => $testimonial)
                        @if($index === 0)
                            {{-- FEATURED MEMBER TESTIMONIAL --}}
                            <article class="tm-featured-story" wire:key="testimonial-featured-{{ $testimonial->id ?? $testimonial->ID ?? $index }}">
                                <div class="tm-featured-top">
                                    <span class="tm-featured-label"><span></span> FEATURED MEMBER STORY</span>
                                    <span class="tm-featured-quote-mark">“</span>
                                </div>
                                <div class="tm-featured-rating" aria-label="{{ $testimonial->rate }} out of 5 stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span>{{ $i <= $testimonial->rate ? '★' : '☆' }}</span>
                                    @endfor
                                </div>
                                <blockquote class="tm-featured-testimony">
                                    “{{ $testimonial->testimony }}”
                                </blockquote>
                                <div class="tm-member-profile">
                                    <div class="tm-member-avatar">{{ strtoupper(substr($testimonial->fullname ?? 'M', 0, 1)) }}</div>
                                    <div>
                                        <h3>{{ $testimonial->fullname }}</h3>
                                        <p>{{ $testimonial->designation ?? 'TRIFECTA Member' }}</p>
                                    </div>
                                    <div class="tm-member-verified"><flux:icon.shield-check class="w-5 h-5"/></div>
                                </div>
                            </article>
                        @else
                            {{-- MEMBER TESTIMONIAL CARD --}}
                            <article class="tm-story-card" wire:key="testimonial-{{ $testimonial->id ?? $testimonial->ID ?? $index }}">
                                <div class="tm-story-card-top">
                                    <span class="tm-story-quote">“</span>
                                    <div class="tm-story-rating" aria-label="{{ $testimonial->rate }} out of 5 stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span>{{ $i <= $testimonial->rate ? '★' : '☆' }}</span>
                                        @endfor
                                    </div>
                                </div>
                                <p class="tm-story-testimony">“{{ $testimonial->testimony }}”</p>
                                <div class="tm-story-profile">
                                    <div class="tm-story-avatar">{{ strtoupper(substr($testimonial->fullname ?? 'M', 0, 1)) }}</div>
                                    <div>
                                        <h3>{{ $testimonial->fullname }}</h3>
                                        <span>{{ $testimonial->designation ?? 'TRIFECTA Member' }}</span>
                                    </div>
                                </div>
                            </article>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="tm-empty-state">
                    <flux:icon.chat-bubble-left-right class="w-12 h-12"/>
                    <h3>Member Stories Coming Soon</h3>
                    <p>We look forward to sharing the experiences of our community with you.</p>
                </div>
            @endif

            {{-- PAGINATION --}}
            @if($testimonials->hasPages())
                <div class="tm-pagination">
                    @if($testimonials->onFirstPage())
                        <span class="tm-page-disabled">← Previous</span>
                    @else
                        <button type="button" wire:click="previousPage" wire:loading.attr="disabled" class="tm-page-button">← Previous</button>
                    @endif
                    <span class="tm-page-indicator">PAGE <strong>{{ $testimonials->currentPage() }}</strong> OF {{ $testimonials->lastPage() }}</span>
                    @if($testimonials->hasMorePages())
                        <button type="button" wire:click="nextPage" wire:loading.attr="disabled" class="tm-page-button">Next →</button>
                    @else
                        <span class="tm-page-disabled">Next →</span>
                    @endif
                </div>
            @endif
        </div>
    </section>

    {{-- TRIFECTA VALUES --}}
    <section class="tm-values">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
            <div class="tm-values-heading">
                <span>WHAT DEFINES US</span>
                <h2>Our Commitment, Your Confidence.</h2>
            </div>
            <div class="tm-values-grid">
                <div class="tm-value-item">
                    <div class="tm-value-icon tm-icon-gold"><flux:icon.heart class="w-7 h-7"/></div>
                    <h3><x-editable-text key="testimonials_highlight_1" :value="$contexts['testimonials_highlight_1'] ?? 'Compassion'" tag="span"/></h3>
                    <p>Putting people and families at the heart of everything we do.</p>
                </div>
                <div class="tm-value-item">
                    <div class="tm-value-icon tm-icon-green"><flux:icon.user-group class="w-7 h-7"/></div>
                    <h3><x-editable-text key="testimonials_highlight_2" :value="$contexts['testimonials_highlight_2'] ?? 'Unity'" tag="span"/></h3>
                    <p>Building stronger communities through shared support.</p>
                </div>
                <div class="tm-value-item">
                    <div class="tm-value-icon tm-icon-blue"><flux:icon.shield-check class="w-7 h-7"/></div>
                    <h3><x-editable-text key="testimonials_highlight_3" :value="$contexts['testimonials_highlight_3'] ?? 'Protection'" tag="span"/></h3>
                    <p>Standing beside members when protection matters most.</p>
                </div>
                <div class="tm-value-item">
                    <div class="tm-value-icon tm-icon-gold"><flux:icon.star class="w-7 h-7"/></div>
                    <h3><x-editable-text key="testimonials_highlight_4" :value="$contexts['testimonials_highlight_4'] ?? 'Hope'" tag="span"/></h3>
                    <p>Creating opportunities for a more confident future.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- MEMBERSHIP CTA --}}
    <section class="tm-cta">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
            <div class="tm-cta-panel">
                <div class="tm-cta-decoration"></div>
                <div class="tm-cta-content">
                    <span class="tm-cta-label">
                        <x-editable-text key="testimonials_cta_small_title" :value="$contexts['testimonials_cta_small_title'] ?? 'BECOME PART OF OUR COMMUNITY'" tag="span"/>
                    </span>
                    <h2>
                        <x-editable-text key="testimonials_cta_heading" :value="$contexts['testimonials_cta_heading'] ?? 'Your Story Starts With TRIFECTA'" tag="span"/>
                    </h2>
                    <p>
                        <x-editable-text key="testimonials_cta_text" :value="$contexts['testimonials_cta_text'] ?? 'Experience community-based protection and meaningful support designed to help Filipino families.' " tag="span"/>
                    </p>
                </div>
                <div class="tm-cta-action">
                    @if($joinAllow)
                        <button type="button" onclick="Livewire.dispatch('open-application-modal')" class="tm-cta-button">
                            <x-editable-text key="testimonials_btn_primary" :value="$contexts['testimonials_btn_primary'] ?? 'Become a Member'" tag="span"/>
                            <span>↗</span>
                        </button>
                    @else
                        <button type="button" disabled class="tm-cta-button tm-cta-disabled">Join Unavailable</button>
                    @endif
                    <span class="tm-cta-note">HEALTH • WELLNESS • PROTECTION</span>
                </div>
            </div>
        </div>
    </section>
</div>
