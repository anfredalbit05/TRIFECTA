<section class="hero-section">


    {{-- =========================
        BACKGROUND
    ========================== --}}

    <div class="hero-bg-container">

        <img
            src="{{ asset('images/hero-bg.jpg') }}"
            alt="Dayongan Community"
            class="hero-image"
        >

        <div class="hero-overlay"></div>

        <div class="hero-glow-gold"></div>

        <div class="hero-glow-blue"></div>

    </div>




    {{-- =========================
        CONTENT
    ========================== --}}

    <div class="hero-content-wrapper">

        <div class="hero-container">

            <div class="hero-grid">


                {{-- LEFT COLUMN --}}

                <div class="hero-left-col pt-10">


                    {{-- Badge --}}

                    <div class="hero-badge">

                        <span class="hero-badge-dot"></span>

                        <x-editable-text
                            key="hero_badge"
                            :value="$contexts['hero_badge'] ?? ''"
                            tag="span"
                            class="hero-badge-text"
                        />

                    </div>



                    {{-- Heading --}}

                    <h1 class="hero-title">


                        <span class="block">

                            <x-editable-text
                                key="hero_title_line1"
                                :value="$contexts['hero_title_line1'] ?? ''"
                                tag="span"
                            />


                            <span class="hero-title-highlight">

                                <x-editable-text
                                    key="hero_title_highlight1"
                                    :value="$contexts['hero_title_highlight1'] ?? ''"
                                    tag="span"
                                />

                            </span>

                        </span>




                        <span class="block">

                            <x-editable-text
                                key="hero_title_line2"
                                :value="$contexts['hero_title_line2'] ?? ''"
                                tag="span"
                            />


                            <span class="hero-title-highlight">

                                <x-editable-text
                                    key="hero_title_highlight2"
                                    :value="$contexts['hero_title_highlight2'] ?? ''"
                                    tag="span"
                                />

                            </span>

                        </span>




                        <span class="block mt-1">


                            <x-editable-text
                                key="hero_title_line3"
                                :value="$contexts['hero_title_line3'] ?? ''"
                                tag="span"
                            />


                            <span class="hero-title-highlight">

                                <x-editable-text
                                    key="hero_title_highlight3"
                                    :value="$contexts['hero_title_highlight3'] ?? ''"
                                    tag="span"
                                />

                            </span>


                        </span>


                    </h1>




                    <div class="hero-accent-line"></div>



                    {{-- Description --}}

                    <x-editable-text
                        key="hero_description"
                        :value="$contexts['hero_description'] ?? ''"
                        tag="p"
                        class="hero-description"
                    />




                    {{-- Buttons --}}

                    <div class="hero-btn-group">

                        <a
                            onclick="Livewire.dispatch('open-application-modal')"
                            class="hero-btn-primary">

                            Join Now

                        </a>


                        <a
                            href="#learn-more"
                            class="hero-btn-secondary">

                            Learn More

                        </a>

                    </div>


                </div>





                {{-- RIGHT COLUMN --}}

                <div class="hero-right-col">

                    <div class="relative">


                        <div class="hero-logo-box">

                            <img
                                src="{{ asset('images/dayongan-logo.png') }}"
                                alt="Dayongan Logo"
                                class="hero-logo-img"
                            >

                        </div>

                        {{-- Members Card --}}

                        <div class="hero-card-members">

                            <p class="hero-card-title">
                                {{ number_format($totalMembers) }}
                            </p>

                            <x-editable-text
                                key="hero_members_label"
                                :value="$contexts['hero_members_label'] ?? 'Active Members'"
                                tag="p"
                                class="hero-card-subtext"
                            />

                        </div>





                        {{-- Support Card --}}

                        <div class="hero-card-support">


                            <x-editable-text
                                key="hero_support_count"
                                :value="$contexts['hero_support_count'] ?? ''"
                                tag="p"
                                class="hero-card-title"
                            />


                            <x-editable-text
                                key="hero_support_label"
                                :value="$contexts['hero_support_label'] ?? ''"
                                tag="p"
                                class="hero-card-subtext"
                            />


                        </div>



                    </div>


                </div>



            </div>

        </div>

    </div>



    <div class="hero-bottom-bar"></div>


</section>
