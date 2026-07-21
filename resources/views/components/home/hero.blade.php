<section class="hero-section">

    <!-- Background Image & Overlays -->
    <div class="hero-bg-container">
        <img src="{{ asset('images/hero-bg.jpg') }}" alt="Dayongan Community" class="hero-image">
        <div class="hero-overlay"></div>

        <!-- Gold Glow Accent -->
        <div class="hero-glow-gold"></div>

        <!-- Blue Glow Accent -->
        <div class="hero-glow-blue"></div>
    </div>

    <!-- Content Area (Pulled Up) -->
    <div class="hero-content-wrapper">
        <div class="hero-container">
            <div class="hero-grid">

                <!-- LEFT COLUMN -->
                <div class="hero-left-col">
                    <!-- Badge -->
                    <div class="hero-badge">
                        <span class="hero-badge-dot"></span>
                        <span class="hero-badge-text">
                            Community Support System
                        </span>
                    </div>

                    <!-- Heading -->
                    <h1 class="hero-title">
                        <span class="block">
                            United in <span class="hero-title-highlight">Service.</span>
                        </span>
                        <span class="block mt-1">
                            Committed to <span class="hero-title-highlight">Excellence.</span>
                        </span>
                    </h1>

                    <!-- Decorative Accent Line -->
                    <div class="hero-accent-line"></div>

                    <!-- Description -->
                    <p class="hero-description">
                        DAYONGAN is a trusted community support platform connecting families, strengthening communities, and delivering meaningful public service through innovation, transparency, and compassion.
                    </p>

                    <!-- CTA Action Buttons -->
                    <div class="hero-btn-group">
                        <a onclick="Livewire.dispatch('open-application-modal')" class="hero-btn-primary">
                            Join Now
                        </a>
                        <a href="#learn-more" class="hero-btn-secondary">
                            Learn More
                        </a>
                    </div>
                </div>

                <!-- RIGHT COLUMN (Graphic & Floating Cards) -->
                <div class="hero-right-col">
                    <div class="relative">
                        <!-- Brand Logo Container -->
                        <div class="hero-logo-box">
                            <img src="{{ asset('images/dayongan-logo.png') }}" alt="Dayongan Logo" class="hero-logo-img">
                        </div>

                        <!-- Members Floating Card -->
                        <div class="hero-card-members">
                            <p class="hero-card-title">500+</p>
                            <p class="hero-card-subtext">Community Members</p>
                        </div>

                        <!-- Support Floating Card -->
                        <div class="hero-card-support">
                            <p class="hero-card-title">24/7</p>
                            <p class="hero-card-subtext">Community Support</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bottom Gradient Accent Bar -->
    <div class="hero-bottom-bar"></div>

</section>
