<div>
<!-- =========================
     HERO SECTION
========================== -->
<section class="contact-hero">

    <!-- Ambient Glow Effects -->
    <div class="contact-hero-glow-left"></div>
    <div class="contact-hero-glow-right"></div>

    <div class="relative max-w-7xl mx-auto px-6 text-center">

        <span class="contact-hero-badge">
            <x-editable-text
                key="contact_hero_badge"
                :value="$contexts['contact_hero_badge'] ?? ''"
                tag="span"
                class="inline-block"
            />
        </span>

        <h1 class="contact-hero-title">
            <x-editable-text
                key="contact_hero_title_1"
                :value="$contexts['contact_hero_title_1'] ?? ''"
                tag="span"
                class="inline-block"
            />
            <br class="hidden sm:block">
            <span class="contact-hero-title-highlight">
                <x-editable-text
                    key="contact_hero_title_highlight"
                    :value="$contexts['contact_hero_title_highlight'] ?? ''"
                    tag="span"
                    class="inline-block"
                />
            </span>
        </h1>

        <p class="contact-hero-description">
            <x-editable-text
                key="contact_hero_description"
                :value="$contexts['contact_hero_description'] ?? ''"
                tag="span"
                class="block"
            />
        </p>

    </div>

</section>

<!-- =========================
     FULL CONTACT DETAILS
========================== -->
<section class="contact-details-section">

    <div class="max-w-7xl mx-auto px-6">

        <div class="contact-details-header">
            <span class="contact-section-label">
                <x-editable-text
                    key="contact_details_label"
                    :value="$contexts['contact_details_label'] ?? ''"
                    tag="span"
                    class="inline-block"
                />
            </span>

            <h2 class="contact-section-title">
                <x-editable-text
                    key="contact_details_title"
                    :value="$contexts['contact_details_title'] ?? ''"
                    tag="span"
                    class="inline-block"
                />
            </h2>

            <p class="contact-section-description">
                <x-editable-text
                    key="contact_details_description"
                    :value="$contexts['contact_details_description'] ?? ''"
                    tag="span"
                    class="block"
                />
            </p>
        </div>

        <div class="contact-details-grid">

            <!-- Main Office -->
            <div class="contact-detail-card">
                <div class="contact-card-icon">
                    📍
                </div>

                <h3 class="contact-detail-title">
                    <x-editable-text
                        key="contact_main_office_title"
                        :value="$contexts['contact_main_office_title'] ?? ''"
                        tag="span"
                        class="inline-block"
                    />
                </h3>

                <p class="contact-card-text whitespace-pre-line">
                    <x-editable-text
                        key="contact_main_office_address"
                        :value="$contexts['contact_main_office_address'] ?? ''"
                        tag="span"
                        class="inline-block"
                    />
                </p>
            </div>

            <!-- Sub Office -->
            <div class="contact-detail-card">
                <div class="contact-card-icon">
                    🏢
                </div>

                <h3 class="contact-detail-title">
                    <x-editable-text
                        key="contact_sub_office_title"
                        :value="$contexts['contact_sub_office_title'] ?? ''"
                        tag="span"
                        class="inline-block"
                    />
                </h3>

                <p class="contact-card-text whitespace-pre-line">
                    <x-editable-text
                        key="contact_sub_office_address"
                        :value="$contexts['contact_sub_office_address'] ?? ''"
                        tag="span"
                        class="inline-block"
                    />
                </p>
            </div>

            <!-- Contact Numbers -->
            <div class="contact-detail-card">
                <div class="contact-card-icon">
                    📞
                </div>

                <h3 class="contact-detail-title">
                    <x-editable-text
                        key="contact_phone_title"
                        :value="$contexts['contact_phone_title'] ?? ''"
                        tag="span"
                        class="inline-block"
                    />
                </h3>

                <p class="contact-card-text font-semibold mt-4">
                    <x-editable-text
                        key="contact_phone_1"
                        :value="$contexts['contact_phone_1'] ?? ''"
                        tag="span"
                        class="inline-block"
                    />
                </p>

                <p class="contact-card-text font-semibold">
                    <x-editable-text
                        key="contact_phone_2"
                        :value="$contexts['contact_phone_2'] ?? ''"
                        tag="span"
                        class="inline-block"
                    />
                </p>
            </div>

            <!-- Email -->
            <div class="contact-detail-card">
                <div class="contact-card-icon">
                    ✉️
                </div>

                <h3 class="contact-detail-title">
                    <x-editable-text
                        key="contact_email_title"
                        :value="$contexts['contact_email_title'] ?? ''"
                        tag="span"
                        class="inline-block"
                    />
                </h3>

                <p class="contact-card-text break-all">
                    <x-editable-text
                        key="contact_email_address"
                        :value="$contexts['contact_email_address'] ?? ''"
                        tag="span"
                        class="inline-block"
                    />
                </p>
            </div>

        </div>

        <!-- Facebook Banner -->
        <div class="contact-facebook-banner">
            <div class="contact-facebook-icon">
                👍
            </div>

            <h3 class="contact-facebook-title">
                <x-editable-text
                    key="contact_fb_title"
                    :value="$contexts['contact_fb_title'] ?? ''"
                    tag="span"
                    class="inline-block"
                />
            </h3>

            <p class="contact-facebook-[#0B2A5B]-text">
                <strong>
                    <x-editable-text
                        key="contact_fb_name"
                        :value="$contexts['contact_fb_name'] ?? ''"
                        tag="span"
                        class="inline-block"
                    />
                </strong>
                <br>
                <x-editable-text
                    key="contact_fb_sub"
                    :value="$contexts['contact_fb_sub'] ?? ''"
                    tag="span"
                    class="inline-block"
                />
            </p>

            <a href="{{ $contexts['contact_fb_url'] ?? 'https://facebook.com' }}" target="_blank" rel="noopener noreferrer" class="contact-facebook-btn">
                <x-editable-text
                    key="contact_fb_btn_text"
                    :value="$contexts['contact_fb_btn_text'] ?? ''"
                    tag="span"
                    class="inline-block"
                />
            </a>
        </div>

    </div>

</section>


<!-- =========================
     MAP SECTION
========================== -->
<section class="contact-map-section">

    <div class="max-w-7xl mx-auto px-6">

        <div class="contact-map-header">
            <span class="contact-map-subtitle">
                <x-editable-text
                    key="contact_map_subtitle"
                    :value="$contexts['contact_map_subtitle'] ?? ''"
                    tag="span"
                    class="inline-block"
                />
            </span>

            <h2 class="contact-map-title">
                <x-editable-text
                    key="contact_map_title"
                    :value="$contexts['contact_map_title'] ?? ''"
                    tag="span"
                    class="inline-block"
                />
            </h2>

            <p class="contact-map-location">
                <x-editable-text
                    key="contact_map_location"
                    :value="$contexts['contact_map_location'] ?? ''"
                    tag="span"
                    class="inline-block"
                />
            </p>
        </div>

        <div class="contact-map-container">
            <iframe
                src="{{ $contexts['contact_map_iframe_url'] ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15814.708236166589!2d125.7950!3d7.4478!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32f932e6fffff!2sTagum%20City!5e0!3m2!1sen!2sph!4v1680000000000!5m2!1sen!2sph' }}"
                class="contact-map-iframe"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

    </div>

</section>

</div>
