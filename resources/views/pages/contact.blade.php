@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')

<!-- =========================
     HERO SECTION
========================== -->
<section class="contact-hero">

    <!-- Ambient Glow Effects -->
    <div class="contact-hero-glow-left"></div>
    <div class="contact-hero-glow-right"></div>

    <div class="relative max-w-7xl mx-auto px-6 text-center">

        <span class="contact-hero-badge">
            Contact DAYONGAN
        </span>

        <h1 class="contact-hero-title">
            We're Here to
            <br class="hidden sm:block">
            <span class="contact-hero-title-highlight">Help You</span>
        </h1>

        <p class="contact-hero-description">
            Have questions about membership, protection benefits,
            financial literacy, or other DAYONGAN services? Our
            friendly team is ready to assist you with reliable
            information, guidance, and support. We are committed
            to helping you understand our services and ensuring
            that every member receives the assistance they need.
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
                Get In Touch
            </span>

            <h2 class="contact-section-title">
                Contact Information
            </h2>

            <p class="contact-section-description">
                Visit our offices, call our support team, or send us an email.
                We're committed to providing timely assistance and answering your
                questions about memberships, benefits, and other DAYONGAN services.
            </p>
        </div>

        <div class="contact-details-grid">

            <!-- Main Office -->
            <div class="contact-detail-card">
                <div class="contact-card-icon">
                    📍
                </div>

                <h3 class="contact-detail-title">
                    Main Office
                </h3>

                <p class="contact-card-text">
                    001 Liwayway Village<br>
                    Tagum City
                </p>
            </div>

            <!-- Sub Office -->
            <div class="contact-detail-card">
                <div class="contact-card-icon">
                    🏢
                </div>

                <h3 class="contact-detail-title">
                    Sub Office
                </h3>

                <p class="contact-card-text">
                    J&R Bldg., 2nd Floor<br>
                    Mankilam, Kapalong
                </p>
            </div>

            <!-- Contact Numbers -->
            <div class="contact-detail-card">
                <div class="contact-card-icon">
                    📞
                </div>

                <h3 class="contact-detail-title">
                    Contact Numbers
                </h3>

                <p class="contact-card-text font-semibold mt-4">
                    0927 838 1647
                </p>

                <p class="contact-card-text font-semibold">
                    0997 614 8392
                </p>
            </div>

            <!-- Email -->
            <div class="contact-detail-card">
                <div class="contact-card-icon">
                    ✉️
                </div>

                <h3 class="contact-detail-title">
                    Email Address
                </h3>

                <p class="contact-card-text break-all">
                    dayongansolidaritas26@gmail.com
                </p>
            </div>

        </div>

        <!-- Facebook Banner -->
        <div class="contact-facebook-banner">
            <div class="contact-facebook-icon">
                👍
            </div>

            <h3 class="contact-facebook-title">
                Follow Us on Facebook
            </h3>

            <p class="contact-facebook-[#0B2A5B]-text">
                <strong>TRUELIFE DAYONGAN SOLIDARITAS</strong><br>
                Social Welfare Services and Financial Literacy Inc.
            </p>

            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="contact-facebook-btn">
                Visit Our Facebook Page &rarr;
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
                Visit Us
            </span>

            <h2 class="contact-map-title">
                Find Our Main Office
            </h2>

            <p class="contact-map-location">
                001 Liwayway Village, Tagum City
            </p>
        </div>

        <div class="contact-map-container">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15814.708236166589!2d125.7950!3d7.4478!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32f932e6fffff!2sTagum%20City!5e0!3m2!1sen!2sph!4v1680000000000!5m2!1sen!2sph"
                class="contact-map-iframe"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

    </div>

</section>

@endsection
