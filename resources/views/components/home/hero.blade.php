@php
    $joinAllow = \Illuminate\Support\Facades\DB::table('settings')
        ->where('BranchID', 0)
        ->where('name', 'join_allow')
        ->value('value');

    $joinAllow = filter_var($joinAllow, FILTER_VALIDATE_BOOLEAN);

    /* Annual figure advertised on the card. Falls back to the seeded value. */
    $priceRows = [
        [
            'label_key' => 'hero_card_row1_label',
            'label' => $contexts['hero_card_row1_label'] ?? 'Hospital confinement support',
            'value_key' => 'hero_card_row1_value',
            'value' => $contexts['hero_card_row1_value'] ?? '₱15,000',
        ],
        [
            'label_key' => 'hero_card_row2_label',
            'label' => $contexts['hero_card_row2_label'] ?? 'Natural death assistance',
            'value_key' => 'hero_card_row2_value',
            'value' => $contexts['hero_card_row2_value'] ?? '₱20,000',
        ],
        [
            'label_key' => 'hero_card_row3_label',
            'label' => $contexts['hero_card_row3_label'] ?? 'Accident and disability cover',
            'value_key' => 'hero_card_row3_value',
            'value' => $contexts['hero_card_row3_value'] ?? '₱65,000',
        ],
        [
            'label_key' => 'hero_card_row4_label',
            'label' => $contexts['hero_card_row4_label'] ?? 'Emergency care per incident',
            'value_key' => 'hero_card_row4_value',
            'value' => $contexts['hero_card_row4_value'] ?? '₱13,000',
        ],
    ];

    $heroChecks = [
        ['key' => 'hero_check_1', 'text' => $contexts['hero_check_1'] ?? 'You plus three beneficiaries'],
        ['key' => 'hero_check_2', 'text' => $contexts['hero_check_2'] ?? 'Beneficiaries from 0 to 100 years old'],
        ['key' => 'hero_check_3', 'text' => $contexts['hero_check_3'] ?? 'Renewable every year'],
    ];
@endphp

<section class="tf-hero">

    <span class="tf-hero-blob b1" aria-hidden="true"></span>
    <span class="tf-hero-blob b2" aria-hidden="true"></span>

    <div class="tf-hero-inner">
        <div class="tf-hero-grid">

            {{-- =====================================================
                 COPY
            ====================================================== --}}
            <div class="tf-hero-copy">

                <span class="tf-eyebrow">
                    <x-editable-text
                        key="hero_badge"
                        :value="$contexts['hero_badge'] ?? 'Three pillars · one membership'"
                        tag="span"
                        class="inline-block"
                    />
                </span>

                <x-editable-text
                    key="hero_scriptline"
                    :value="$contexts['hero_scriptline'] ?? 'Health. Wellness. Protection. One promise for your family.'"
                    tag="p"
                    class="tf-hero-script"
                />

                <h1 class="tf-hero-title">
                    <span class="block">
                        <x-editable-text key="hero_title_line1" :value="$contexts['hero_title_line1'] ?? 'A stronger'" tag="span" />
                        <span class="tf-hero-accent">
                            <x-editable-text key="hero_title_highlight1" :value="$contexts['hero_title_highlight1'] ?? 'safety net'" tag="span" />
                        </span>
                    </span>

                    <span class="block">
                        <x-editable-text key="hero_title_line2" :value="$contexts['hero_title_line2'] ?? 'for the people'" tag="span" />
                        <span class="tf-hero-accent">
                            <x-editable-text key="hero_title_highlight2" :value="$contexts['hero_title_highlight2'] ?? 'you love'" tag="span" />
                        </span>
                    </span>

                    <span class="block">
                        <x-editable-text key="hero_title_line3" :value="$contexts['hero_title_line3'] ?? 'every single day.'" tag="span" />
                        <span class="tf-hero-accent">
                            <x-editable-text key="hero_title_highlight3" :value="$contexts['hero_title_highlight3'] ?? ''" tag="span" />
                        </span>
                    </span>
                </h1>

                <x-editable-text
                    key="hero_description"
                    :value="$contexts['hero_description'] ?? 'TRIFECTA brings health support, everyday wellness perks and personal accident protection together in one annual membership.'"
                    tag="p"
                    class="tf-hero-desc"
                />

                <div class="tf-hero-actions">

                    @if ($joinAllow)
                        <a onclick="Livewire.dispatch('open-application-modal')" class="tf-btn-gold cursor-pointer">
                            Apply for Membership
                        </a>
                    @else
                        <a class="tf-btn-gold cursor-not-allowed opacity-60" aria-disabled="true">
                            Applications Paused
                        </a>
                    @endif

                    <a href="{{ route('how-it-works') }}" class="tf-btn-ghost">
                        See how it works
                    </a>

                </div>

                <ul class="tf-hero-checks">
                    @foreach ($heroChecks as $check)
                        <li>
                            <x-editable-text :key="$check['key']" :value="$check['text']" tag="span" />
                        </li>
                    @endforeach
                </ul>

            </div>


            {{-- =====================================================
                 FLOATING PLAN CARD
            ====================================================== --}}
            <div class="tf-hero-stage">

                <div class="tf-hero-chip c1">
                    <span class="tf-hero-chip-dot">✦</span>
                    <span>
                        <strong>
                            <x-editable-text key="hero_members_count" :value="$contexts['hero_members_count'] ?? '2,300+'" tag="span" />
                        </strong>
                        <small>
                            <x-editable-text key="hero_members_label" :value="$contexts['hero_members_label'] ?? 'Protected Members'" tag="span" />
                        </small>
                    </span>
                </div>

                <div class="tf-hero-chip c2">
                    <span class="tf-hero-chip-dot">◷</span>
                    <span>
                        <strong>
                            <x-editable-text key="hero_support_count" :value="$contexts['hero_support_count'] ?? '24/7'" tag="span" />
                        </strong>
                        <small>
                            <x-editable-text key="hero_support_label" :value="$contexts['hero_support_label'] ?? 'Member Care'" tag="span" />
                        </small>
                    </span>
                </div>

                <aside class="tf-plan">

                    <div class="tf-plan-top">
                        <x-editable-text
                            key="hero_card_label"
                            :value="$contexts['hero_card_label'] ?? 'Annual membership'"
                            tag="span"
                            class="tf-plan-label"
                        />
                        <span class="tf-hero-chip-dot">◆</span>
                    </div>

                    <x-editable-text
                        key="hero_card_amount"
                        :value="$contexts['hero_card_amount'] ?? '₱1,999'"
                        tag="p"
                        class="tf-plan-amount"
                    />

                    <x-editable-text
                        key="hero_card_meta"
                        :value="$contexts['hero_card_meta'] ?? 'one payment · 12 months of cover · renewable'"
                        tag="p"
                        class="tf-plan-meta"
                    />

                    <x-editable-text
                        key="hero_card_highlight"
                        :value="$contexts['hero_card_highlight'] ?? 'More than ₱200,000 in combined benefits'"
                        tag="p"
                        class="tf-plan-highlight"
                    />

                    <div class="tf-plan-rows">
                        @foreach ($priceRows as $row)
                            <div class="tf-plan-row">
                                <x-editable-text
                                    :key="$row['label_key']"
                                    :value="$row['label']"
                                    tag="span"
                                />
                                <x-editable-text
                                    :key="$row['value_key']"
                                    :value="$row['value']"
                                    tag="span"
                                />
                            </div>
                        @endforeach
                    </div>

                    @if ($joinAllow)
                        <a onclick="Livewire.dispatch('open-application-modal')" class="tf-plan-cta cursor-pointer">
                            Join now
                        </a>
                    @else
                        <span class="tf-plan-cta cursor-not-allowed opacity-60">Join unavailable</span>
                    @endif

                    <x-editable-text
                        key="hero_card_note"
                        :value="$contexts['hero_card_note'] ?? 'Health benefits begin after the 6-month waiting period from approval.'"
                        tag="p"
                        class="tf-plan-note"
                    />

                </aside>

            </div>

        </div>
    </div>

</section>
