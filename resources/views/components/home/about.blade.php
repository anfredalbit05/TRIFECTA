<section class="home-about">

    <div class="home-about-wrap">

        {{-- SECTION HEADER --}}
        <div class="home-about-header">

            <x-editable-text
                key="about_badge"
                :value="$contexts['about_badge'] ?? ''"
                tag="span"
                class="section-tag"
            />

            <h2 class="home-about-title">
                <x-editable-text key="about_title_line1" :value="$contexts['about_title_line1'] ?? ''" tag="span" />
                <span class="home-about-title-accent">
                    <x-editable-text key="about_title_line2" :value="$contexts['about_title_line2'] ?? ''" tag="span" />
                </span>
                <x-editable-text key="about_title_line3" :value="$contexts['about_title_line3'] ?? ''" tag="span" />
            </h2>

            <x-editable-text
                key="about_description"
                :value="$contexts['about_description'] ?? ''"
                tag="div"
                class="home-about-lead"
            />

        </div>

        {{-- PILLARS --}}
        <div class="home-about-grid">

            {{-- MISSION --}}
            <article class="home-about-card">
                <div class="home-about-card-head">
                    <span class="home-about-number">01</span>
                    <span class="home-about-icon">
                        <flux:icon.shield-check class="h-5 w-5" />
                    </span>
                </div>

                <x-editable-text
                    key="mission_title"
                    :value="$contexts['mission_title'] ?? ''"
                    tag="h3"
                    class="home-about-card-title"
                />

                <x-editable-text
                    key="mission_description"
                    :value="$contexts['mission_description'] ?? ''"
                    tag="div"
                    class="home-about-card-text"
                />
            </article>

            {{-- VISION --}}
            <article class="home-about-card">
                <div class="home-about-card-head">
                    <span class="home-about-number">02</span>
                    <span class="home-about-icon">
                        <flux:icon.user-group class="h-5 w-5" />
                    </span>
                </div>

                <x-editable-text
                    key="vision_title"
                    :value="$contexts['vision_title'] ?? ''"
                    tag="h3"
                    class="home-about-card-title"
                />

                <x-editable-text
                    key="vision_description"
                    :value="$contexts['vision_description'] ?? ''"
                    tag="div"
                    class="home-about-card-text"
                />
            </article>

            {{-- CORE VALUES --}}
            <article class="home-about-card">
                <div class="home-about-card-head">
                    <span class="home-about-number">03</span>
                    <span class="home-about-icon">
                        <flux:icon.heart class="h-5 w-5" />
                    </span>
                </div>

                <x-editable-text
                    key="values_title"
                    :value="$contexts['values_title'] ?? ''"
                    tag="h3"
                    class="home-about-card-title"
                />

                <x-editable-text
                    key="values_subtitle"
                    :value="$contexts['values_subtitle'] ?? ''"
                    tag="p"
                    class="home-about-subtitle mt-1"
                />

                <ul class="home-about-list">
                    @foreach (['value_1', 'value_2', 'value_3', 'value_4', 'value_5'] as $valueKey)
                        <li>
                            <x-editable-text
                                :key="$valueKey"
                                :value="$contexts[$valueKey] ?? ''"
                                tag="span"
                            />
                        </li>
                    @endforeach
                </ul>
            </article>

        </div>

    </div>

</section>
