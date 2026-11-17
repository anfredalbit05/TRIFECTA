<section class="home-why">

    <div class="home-why-wrap">

        {{-- SECTION HEADER --}}
        <div class="home-why-header">

            <x-editable-text
                key="why_badge"
                :value="$contexts['why_badge'] ?? ''"
                tag="span"
                class="section-tag"
            />

            <h2 class="home-why-title">
                <x-editable-text key="why_title_line1" :value="$contexts['why_title_line1'] ?? ''" tag="span" />
                <span class="home-why-title-accent">
                    <x-editable-text key="why_title_highlight" :value="$contexts['why_title_highlight'] ?? ''" tag="span" />
                </span>
            </h2>

            <x-editable-text
                key="why_description"
                :value="$contexts['why_description'] ?? ''"
                tag="div"
                class="home-why-lead"
            />

        </div>

        {{-- REASONS --}}
        <div class="home-why-grid">

            {{-- CARD 1 --}}
            <article class="home-why-card">
                <span class="home-why-icon">
                    <flux:icon.shield-check class="h-6 w-6" />
                </span>

                <x-editable-text
                    key="why_card1_title"
                    :value="$contexts['why_card1_title'] ?? ''"
                    tag="h3"
                    class="home-why-card-title"
                />

                <x-editable-text
                    key="why_card1_description"
                    :value="$contexts['why_card1_description'] ?? ''"
                    tag="div"
                    class="home-why-card-text"
                />

                <x-editable-text
                    key="why_card1_footer"
                    :value="$contexts['why_card1_footer'] ?? ''"
                    tag="div"
                    class="home-why-card-footer"
                />
            </article>

            {{-- CARD 2 --}}
            <article class="home-why-card">
                <span class="home-why-icon">
                    <flux:icon.home-modern class="h-6 w-6" />
                </span>

                <x-editable-text
                    key="why_card2_title"
                    :value="$contexts['why_card2_title'] ?? ''"
                    tag="h3"
                    class="home-why-card-title"
                />

                <x-editable-text
                    key="why_card2_description"
                    :value="$contexts['why_card2_description'] ?? ''"
                    tag="div"
                    class="home-why-card-text"
                />

                <x-editable-text
                    key="why_card2_footer"
                    :value="$contexts['why_card2_footer'] ?? ''"
                    tag="div"
                    class="home-why-card-footer"
                />
            </article>

            {{-- CARD 3 --}}
            <article class="home-why-card">
                <span class="home-why-icon">
                    <flux:icon.user-group class="h-6 w-6" />
                </span>

                <x-editable-text
                    key="why_card3_title"
                    :value="$contexts['why_card3_title'] ?? ''"
                    tag="h3"
                    class="home-why-card-title"
                />

                <x-editable-text
                    key="why_card3_description"
                    :value="$contexts['why_card3_description'] ?? ''"
                    tag="div"
                    class="home-why-card-text"
                />

                <x-editable-text
                    key="why_card3_footer"
                    :value="$contexts['why_card3_footer'] ?? ''"
                    tag="div"
                    class="home-why-card-footer"
                />
            </article>

        </div>

        {{-- FIGURES --}}
        <div class="home-why-stats">
            @for ($i = 1; $i <= 4; $i++)
                <div>
                    <x-editable-text
                        key="why_stat{{ $i }}_value"
                        :value="$contexts['why_stat'.$i.'_value'] ?? ''"
                        tag="p"
                        class="home-why-stat-value"
                    />

                    <x-editable-text
                        key="why_stat{{ $i }}_label"
                        :value="$contexts['why_stat'.$i.'_label'] ?? ''"
                        tag="p"
                        class="home-why-stat-label"
                    />
                </div>
            @endfor
        </div>

    </div>

</section>
