@extends('layouts.app')

@section('title', 'Testimonials')

@section('content')

<!-- ======================================
     HERO SECTION
====================================== -->
<section class="testimonials-hero">

    <div class="testimonials-hero-glow-top"></div>
    <div class="testimonials-hero-glow-bottom"></div>

    <div class="testimonials-container-center">

        <span class="testimonials-tag">
            Testimonials
        </span>

        <h1 class="testimonials-title">
            What Our
            <br class="hidden sm:block">
            <span class="testimonials-title-highlight">
                Members Say
            </span>
        </h1>

        <p class="testimonials-description">
            Hear inspiring stories from members who have experienced the
            compassion, protection, and support provided by
            <strong>TRUELIFE DAYONGAN SOLIDARITAS.</strong>
            Their experiences reflect our commitment to serving families,
            providing reliable assistance, and strengthening communities
            through care, trust, and meaningful support.
        </p>

    </div>

</section>

<!-- =========================
     INTRODUCTION
========================= -->
<section class="testimonials-intro">
    <div class="testimonials-intro-container">
        <span class="testimonials-small-title">Real Experiences</span>

        <h2 class="testimonials-heading">Stories That Inspire</h2>

        <p class="testimonials-text">
            Every family we serve has a unique story. These testimonials
            reflect the trust and confidence our members have in DAYONGAN's
            commitment to protecting Filipino families.
        </p>
    </div>
</section>

<!-- =========================
     TESTIMONIALS
========================= -->
<section class="testimonials-section">
    <div class="testimonials-container">
        <div class="testimonials-grid">

            <!-- Testimonial 1 -->
            <div class="testimonial-card">
                <div class="testimonial-quote">❝</div>

                <p class="testimonial-body">
                    DAYONGAN helped our family during a difficult time.
                    Their support gave us peace of mind and financial relief.
                </p>

                <div class="testimonial-footer">
                    <h4 class="testimonial-name">Maria Santos</h4>
                    <p class="testimonial-role">Member</p>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="testimonial-card">
                <div class="testimonial-quote">❝</div>

                <p class="testimonial-body">
                    I appreciate the kindness and professionalism of the
                    DAYONGAN team. They truly care about every member.
                </p>

                <div class="testimonial-footer">
                    <h4 class="testimonial-name">Juan Dela Cruz</h4>
                    <p class="testimonial-role">Member</p>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="testimonial-card">
                <div class="testimonial-quote">❝</div>

                <p class="testimonial-body">
                    Affordable protection and genuine concern for the community.
                    I highly recommend DAYONGAN to every Filipino family.
                </p>

                <div class="testimonial-footer">
                    <h4 class="testimonial-name">Ana Rodriguez</h4>
                    <p class="testimonial-role">Member</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- =========================
     HIGHLIGHTS
========================= -->
<section class="testimonials-highlights">
    <div class="testimonials-container">
        <div class="testimonials-highlights-grid">

            <div>
                <div class="testimonial-highlight-icon">❤️</div>
                <h3 class="testimonial-highlight-title">Compassion</h3>
            </div>

            <div>
                <div class="testimonial-highlight-icon">🤝</div>
                <h3 class="testimonial-highlight-title">Unity</h3>
            </div>

            <div>
                <div class="testimonial-highlight-icon">🛡️</div>
                <h3 class="testimonial-highlight-title">Protection</h3>
            </div>

            <div>
                <div class="testimonial-highlight-icon">⭐</div>
                <h3 class="testimonial-highlight-title">Hope</h3>
            </div>

        </div>
    </div>
</section>

<!-- =========================
     CALL TO ACTION
========================= -->
<section class="testimonials-cta">
    <div class="testimonials-intro-container">
        <span class="testimonials-small-title">Join Us Today</span>

        <h2 class="testimonials-heading">
            Be Part of the DAYONGAN Family
        </h2>

        <p class="testimonials-text">
            Experience community-based protection and financial assistance
            designed to help Filipino families face life's challenges
            with confidence and hope.
        </p>

        <div class="testimonials-buttons">
            <button
                onclick="Livewire.dispatch('open-application-modal')"
                class="testimonial-btn-primary">
                Become a Member
            </button>

            <a
                href="{{ route('contact') }}"
                class="testimonial-btn-secondary">
                Contact Us
            </a>
        </div>
    </div>
</section>

@endsection
