
@php
    $joinAllow = filter_var(
        \Illuminate\Support\Facades\DB::table('settings')
            ->where('BranchID', 0)
            ->where('name', 'join_allow')
            ->value('value'),
        FILTER_VALIDATE_BOOLEAN
    );

    $steps = [
        ['number'=>'hiw_step1_number','title'=>'hiw_step1_title','text'=>'hiw_step1_text','footer'=>'hiw_step1_footer'],
        ['number'=>'hiw_step2_number','title'=>'hiw_step2_title','text'=>'hiw_step2_text','footer'=>'hiw_step2_footer'],
        ['number'=>'hiw_step3_number','title'=>'hiw_step3_title','text'=>'hiw_step3_text','footer'=>'hiw_step3_footer'],
        ['number'=>'hiw_step4_number','title'=>'hiw_step4_title','text'=>'hiw_step4_text','footer'=>'hiw_step4_footer'],
    ];

    $claims = [
        ['number'=>'hiw_claim1_number','title'=>'hiw_claim1_title','text'=>'hiw_claim1_text'],
        ['number'=>'hiw_claim2_number','title'=>'hiw_claim2_title','text'=>'hiw_claim2_text'],
        ['number'=>'hiw_claim3_number','title'=>'hiw_claim3_title','text'=>'hiw_claim3_text'],
    ];

    $tiers = [
        ['title'=>'hiw_tier_bronze_title','desc'=>'hiw_tier_bronze_desc','price'=>'hiw_tier_bronze_price','class'=>'bronze'],
        ['title'=>'hiw_tier_silver_title','desc'=>'hiw_tier_silver_desc','price'=>'hiw_tier_silver_price','class'=>'silver'],
        ['title'=>'hiw_tier_gold_title','desc'=>'hiw_tier_gold_desc','price'=>'hiw_tier_gold_price','class'=>'gold'],
    ];
@endphp

<div class="hiw-page">
    {{-- HERO --}}
    <section class="hiw-hero">
        <div class="hiw-hero-orb hiw-orb-one"></div>
        <div class="hiw-hero-orb hiw-orb-two"></div>
        <div class="hiw-hero-grid"></div>

        <div class="hiw-wrap hiw-hero-layout">
            <div class="hiw-hero-content">
                <span class="hiw-eyebrow hiw-hero-eyebrow">
                    <span class="hiw-eyebrow-dot"></span>
                    <x-editable-text key="hiw_hero_badge" :value="$contexts['hiw_hero_badge'] ?? ''" tag="span" class="inline-block"/>
                </span>

                <h1 class="hiw-hero-title">
                    <x-editable-text key="hiw_hero_title_1" :value="$contexts['hiw_hero_title_1'] ?? ''" tag="span" class="inline-block"/>
                    <span class="hiw-hero-highlight">
                        <x-editable-text key="hiw_hero_title_highlight" :value="$contexts['hiw_hero_title_highlight'] ?? ''" tag="span" class="inline-block"/>
                    </span>
                </h1>

                <p class="hiw-hero-description">
                    <x-editable-text key="hiw_hero_desc_part1" :value="$contexts['hiw_hero_desc_part1'] ?? ''" tag="span" class="inline-block"/>
                    <strong><x-editable-text key="hiw_hero_desc_cocogen" :value="$contexts['hiw_hero_desc_cocogen'] ?? ''" tag="span" class="inline-block"/></strong>
                    <x-editable-text key="hiw_hero_desc_part2" :value="$contexts['hiw_hero_desc_part2'] ?? ''" tag="span" class="inline-block"/>
                </p>

                <a href="#hiw-process" class="hiw-hero-link">
                    Discover the process <span aria-hidden="true">↓</span>
                </a>
            </div>

            <div class="hiw-hero-visual" aria-hidden="true">
                <div class="hiw-visual-ring hiw-ring-one"></div>
                <div class="hiw-visual-ring hiw-ring-two"></div>
                <div class="hiw-visual-card">
                    <span class="hiw-visual-kicker">TRIFECTA</span>
                    <span class="hiw-visual-mark">T</span>
                    <span class="hiw-visual-caption">Together, we grow stronger.</span>
                </div>
                <div class="hiw-floating-note hiw-note-top"><span>01</span> Become a member</div>
                <div class="hiw-floating-note hiw-note-bottom"><span>04</span> Enjoy your benefits</div>
            </div>
        </div>
        <div class="hiw-hero-bottom"></div>
    </section>

    {{-- MEMBERSHIP PROCESS --}}
    <section class="hiw-process section-space" id="hiw-process">
        <div class="hiw-wrap">
            <div class="hiw-section-heading">
                <span class="hiw-eyebrow">
                    <x-editable-text key="hiw_process_label" :value="$contexts['hiw_process_label'] ?? ''" tag="span" class="inline-block"/>
                </span>
                <h2 class="hiw-heading">
                    <x-editable-text key="hiw_process_title" :value="$contexts['hiw_process_title'] ?? ''" tag="span" class="inline-block"/>
                </h2>
                <p class="hiw-subheading">
                    <x-editable-text key="hiw_process_description" :value="$contexts['hiw_process_description'] ?? ''" tag="span" class="inline-block"/>
                </p>
            </div>

            <div class="hiw-steps">
                @foreach($steps as $index => $step)
                    <article class="hiw-step" wire:key="hiw-step-{{ $index }}">
                        <div class="hiw-step-top">
                            <span class="hiw-step-index">STEP {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="hiw-step-icon" aria-hidden="true">{{ ['01'=>'↗','02'=>'✓','03'=>'▤','04'=>'✦'][str_pad($index + 1, 2, '0', STR_PAD_LEFT)] }}</span>
                        </div>
                        <div class="hiw-step-number">
                            <x-editable-text :key="$step['number']" :value="$contexts[$step['number']] ?? ''" tag="span" class="inline-block"/>
                        </div>
                        <h3 class="hiw-step-title">
                            <x-editable-text :key="$step['title']" :value="$contexts[$step['title']] ?? ''" tag="span" class="inline-block"/>
                        </h3>
                        <p class="hiw-step-text">
                            <x-editable-text :key="$step['text']" :value="$contexts[$step['text']] ?? ''" tag="span" class="inline-block"/>
                        </p>
                        <div class="hiw-step-footer">
                            <x-editable-text :key="$step['footer']" :value="$contexts[$step['footer']] ?? ''" tag="span" class="inline-block"/>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CLAIMS AND COVERAGE --}}
    <section class="hiw-claims-section section-space">
        <div class="hiw-wrap hiw-claims-layout">
            <div class="hiw-claims-content">
                <span class="hiw-eyebrow">
                    <x-editable-text key="hiw_claims_label" :value="$contexts['hiw_claims_label'] ?? ''" tag="span" class="inline-block"/>
                </span>
                <h2 class="hiw-heading hiw-claims-heading">
                    <x-editable-text key="hiw_claims_title" :value="$contexts['hiw_claims_title'] ?? ''" tag="span" class="inline-block"/>
                </h2>
                <p class="hiw-claims-intro">
                    <x-editable-text key="hiw_claims_text" :value="$contexts['hiw_claims_text'] ?? ''" tag="span" class="inline-block"/>
                </p>

                <div class="hiw-claim-timeline">
                    @foreach($claims as $index => $claim)
                        <article class="hiw-claim" wire:key="hiw-claim-{{ $index }}">
                            <div class="hiw-claim-marker">
                                <x-editable-text :key="$claim['number']" :value="$contexts[$claim['number']] ?? ''" tag="span" class="inline-block"/>
                            </div>
                            <div class="hiw-claim-body">
                                <h3>
                                    <x-editable-text :key="$claim['title']" :value="$contexts[$claim['title']] ?? ''" tag="span" class="inline-block"/>
                                </h3>
                                <p>
                                    <x-editable-text :key="$claim['text']" :value="$contexts[$claim['text']] ?? ''" tag="span" class="inline-block"/>
                                </p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <aside class="hiw-coverage">
                <div class="hiw-coverage-header">
                    <span class="hiw-coverage-kicker">
                        <x-editable-text key="hiw_coverage_label" :value="$contexts['hiw_coverage_label'] ?? ''" tag="span" class="inline-block"/>
                    </span>
                    <h3>
                        <x-editable-text key="hiw_coverage_title" :value="$contexts['hiw_coverage_title'] ?? ''" tag="span" class="inline-block"/>
                    </h3>
                    <p>
                        <x-editable-text key="hiw_coverage_description" :value="$contexts['hiw_coverage_description'] ?? ''" tag="span" class="inline-block"/>
                    </p>
                </div>

                <div class="hiw-tier-list">
                    @foreach($tiers as $index => $tier)
                        <article class="hiw-tier hiw-tier-{{ $tier['class'] }}" wire:key="hiw-tier-{{ $tier['class'] }}">
                            <div class="hiw-tier-symbol">{{ $index + 1 }}</div>
                            <div class="hiw-tier-info">
                                <h4><x-editable-text :key="$tier['title']" :value="$contexts[$tier['title']] ?? ''" tag="span" class="inline-block"/></h4>
                                <p><x-editable-text :key="$tier['desc']" :value="$contexts[$tier['desc']] ?? ''" tag="span" class="inline-block"/></p>
                            </div>
                            <div class="hiw-tier-price">
                                <x-editable-text :key="$tier['price']" :value="$contexts[$tier['price']] ?? ''" tag="span" class="inline-block"/>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="hiw-coverage-note"><span aria-hidden="true">✓</span> Membership benefits at a glance</div>
            </aside>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="hiw-faq-section section-space">
        <div class="hiw-wrap hiw-faq-wrap">
            <div class="hiw-section-heading">
                <span class="hiw-eyebrow">
                    <x-editable-text key="hiw_faq_label" :value="$contexts['hiw_faq_label'] ?? ''" tag="span" class="inline-block"/>
                </span>
                <h2 class="hiw-heading">
                    <x-editable-text key="hiw_faq_title" :value="$contexts['hiw_faq_title'] ?? ''" tag="span" class="inline-block"/>
                </h2>
            </div>

            <div class="hiw-faq-list">
                @forelse($faqs as $faq)
                    <article class="hiw-faq-card" wire:key="faq-{{ $faq->FaqID }}">
                        <span class="hiw-faq-mark" aria-hidden="true">?</span>
                        <div>
                            <h3>{{ $faq->question }}</h3>
                            <p>{{ $faq->answer }}</p>
                        </div>
                        <span class="hiw-faq-arrow" aria-hidden="true">↗</span>
                    </article>
                @empty
                    <div class="hiw-faq-empty">No FAQs available at the moment.</div>
                @endforelse
            </div>

            @if($faqs->hasPages())
                <div class="hiw-pagination">
                    <button wire:click="previousPage" wire:loading.attr="disabled" @disabled($faqs->onFirstPage()) class="hiw-page-btn">
                        ← Previous
                    </button>
                    <span>Page {{ $faqs->currentPage() }} of {{ max($faqs->lastPage(), 1) }}</span>
                    <button wire:click="nextPage" wire:loading.attr="disabled" @disabled(!$faqs->hasMorePages()) class="hiw-page-btn">
                        Next →
                    </button>
                </div>
            @endif
        </div>
    </section>

    {{-- CTA --}}
    <section class="hiw-cta">
        <div class="hiw-cta-pattern"></div>
        <div class="hiw-wrap hiw-cta-inner">
            <span class="hiw-cta-kicker">YOUR NEXT CHAPTER STARTS HERE</span>
            <h2>
                <x-editable-text key="hiw_cta_title" :value="$contexts['hiw_cta_title'] ?? ''" tag="span" class="inline-block"/>
            </h2>
            <p>
                <x-editable-text key="hiw_cta_text" :value="$contexts['hiw_cta_text'] ?? ''" tag="span" class="inline-block"/>
            </p>
            <div class="hiw-cta-actions">
                @if($joinAllow)
                    <a href="#register" onclick="Livewire.dispatch('open-application-modal'); return false;" class="hiw-btn hiw-btn-primary">
                        <x-editable-text key="hiw_cta_btn_primary" :value="$contexts['hiw_cta_btn_primary'] ?? ''" tag="span" class="inline-block"/>
                        <span aria-hidden="true">→</span>
                    </a>
                @else
                    <span class="hiw-btn hiw-btn-primary hiw-btn-disabled" aria-disabled="true">Join Unavailable</span>
                @endif
                <a href="{{ route('contact') }}" class="hiw-btn hiw-btn-outline">
                    <x-editable-text key="hiw_cta_btn_secondary" :value="$contexts['hiw_cta_btn_secondary'] ?? ''" tag="span" class="inline-block"/>
                </a>
            </div>
        </div>
    </section>
</div>
