@php
    /* Six-point health benefit grid. Each card keeps its number, headline,
       value and short explanation editable from the context editor. */
    $points = [
        [
            'n' => '1',
            'title_key' => 'benefit_1_title',
            'title' => $contexts['benefit_1_title'] ?? 'Preventive Healthcare',
            'amount_key' => 'benefit_1_amount',
            'amount' => $contexts['benefit_1_amount'] ?? 'Reimbursement of ₱500',
            'text_key' => 'benefit_1_text',
            'text' => $contexts['benefit_1_text'] ?? 'CBC, urinalysis, fecalysis and chest x-ray.',
            'badge_key' => null,
            'badge' => null,
        ],
        [
            'n' => '2',
            'title_key' => 'benefit_2_title',
            'title' => $contexts['benefit_2_title'] ?? 'In-Patient Care',
            'amount_key' => 'benefit_2_amount',
            'amount' => $contexts['benefit_2_amount'] ?? '₱15,000 consumable',
            'text_key' => 'benefit_2_text',
            'text' => $contexts['benefit_2_text'] ?? 'Stay in a private or semi-private room, plus doctors\' fees.',
            'badge_key' => null,
            'badge' => null,
        ],
        [
            'n' => '3',
            'title_key' => 'benefit_3_title',
            'title' => $contexts['benefit_3_title'] ?? 'Out-Patient Care',
            'amount_key' => 'benefit_3_amount',
            'amount' => $contexts['benefit_3_amount'] ?? '₱5,000 consumable',
            'text_key' => 'benefit_3_text',
            'text' => $contexts['benefit_3_text'] ?? 'Check-ups and medicine allowance.',
            'badge_key' => null,
            'badge' => null,
        ],
        [
            'n' => '4',
            'title_key' => 'benefit_4_title',
            'title' => $contexts['benefit_4_title'] ?? 'Emergency Care',
            'amount_key' => 'benefit_4_amount',
            'amount' => $contexts['benefit_4_amount'] ?? '₱13,000 per incident, through accident',
            'text_key' => 'benefit_4_text',
            'text' => $contexts['benefit_4_text'] ?? 'Ambulance (actual cost), physician services, minor surgery and admission.',
            'badge_key' => 'benefit_4_badge',
            'badge' => $contexts['benefit_4_badge'] ?? 'Usable within 72 hours of policy approval',
        ],
        [
            'n' => '5',
            'title_key' => 'benefit_5_title',
            'title' => $contexts['benefit_5_title'] ?? 'Dental Care',
            'amount_key' => 'benefit_5_amount',
            'amount' => $contexts['benefit_5_amount'] ?? 'Reimbursement of ₱1,000',
            'text_key' => 'benefit_5_text',
            'text' => $contexts['benefit_5_text'] ?? 'Consultation and examination, and single tooth extraction.',
            'badge_key' => null,
            'badge' => null,
        ],
        [
            'n' => '6',
            'title_key' => 'benefit_6_title',
            'title' => $contexts['benefit_6_title'] ?? 'Maternity & Ultrasound',
            'amount_key' => 'benefit_6_amount',
            'amount' => $contexts['benefit_6_amount'] ?? 'Reimbursement up to ₱2,000',
            'text_key' => 'benefit_6_text',
            'text' => $contexts['benefit_6_text'] ?? 'Physician\'s fee per visit, tetanus injection, ultrasound, routine laboratory and urinalysis, and vitamins and medicines.',
            'badge_key' => null,
            'badge' => null,
        ],
    ];
@endphp

<section class="benefit-section" id="benefits">

    <div class="benefit-wrap">

        {{-- =========================
            SECTION HEADER
        ========================== --}}
        <div class="benefit-header">

            <x-editable-text
                key="overview_badge"
                :value="$contexts['overview_badge'] ?? ''"
                tag="span"
                class="section-tag"
            />

            <h2 class="benefit-title">
                <x-editable-text key="overview_title" :value="$contexts['overview_title'] ?? ''" tag="span" />
                <span class="benefit-title-highlight">
                    <x-editable-text key="overview_title_highlight" :value="$contexts['overview_title_highlight'] ?? ''" tag="span" />
                </span>
            </h2>

            <x-editable-text
                key="overview_description"
                :value="$contexts['overview_description'] ?? ''"
                tag="div"
                class="benefit-lead"
            />

        </div>

        {{-- =========================
            SIX-POINT GRID
        ========================== --}}
        <div class="benefit-grid">

            @foreach ($points as $point)
                <article class="benefit-card">

                    <div class="benefit-card-head">
                        <span class="benefit-number">{{ $point['n'] }}</span>

                        <div class="benefit-heading">
                            <h3 class="benefit-card-title">
                                <x-editable-text :key="$point['title_key']" :value="$point['title']" tag="span" />
                            </h3>

                            <x-editable-text
                                :key="$point['amount_key']"
                                :value="$point['amount']"
                                tag="p"
                                class="benefit-amount"
                            />
                        </div>
                    </div>

                    <x-editable-text
                        :key="$point['text_key']"
                        :value="$point['text']"
                        tag="p"
                        class="benefit-text"
                    />

                    @if ($point['badge_key'])
                        <div class="benefit-badge-wrap">
                            <x-editable-text
                                :key="$point['badge_key']"
                                :value="$point['badge']"
                                tag="span"
                                class="benefit-badge"
                            />
                        </div>
                    @endif

                </article>
            @endforeach

        </div>

        {{-- =========================
            TOTAL BAND
        ========================== --}}
        <div class="benefit-total">
            <x-editable-text
                key="benefit_total_label"
                :value="$contexts['benefit_total_label'] ?? 'Total amount of services'"
                tag="p"
                class="benefit-total-label"
            />

            <x-editable-text
                key="benefit_total_amount"
                :value="$contexts['benefit_total_amount'] ?? '₱200,000+ worth of protection!'"
                tag="p"
                class="benefit-total-amount"
            />
        </div>

    </div>

</section>
