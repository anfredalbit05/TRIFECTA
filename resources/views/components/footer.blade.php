@php
    $ctx = \App\Models\Context::merged();
@endphp

<footer class="tf-footer">

    <div class="tf-footer-wrap">

        <div class="tf-footer-grid">

            {{-- BRAND --}}
            <div>
                <div class="tf-footer-brand">
                    <span class="tf-nav-logo">
                        <img src="{{ asset('images/truelife-logo.png') }}" alt="TRIFECTA">
                    </span>
                    <strong>
                        <x-editable-text
                            key="footer_brand_name"
                            :value="$ctx['footer_brand_name'] ?? 'TRIFECTA'"
                            tag="span"
                            class="inline-block"
                        />
                    </strong>
                </div>

                <span class="tf-footer-tagline">
                    <x-editable-text
                        key="footer_brand_tagline"
                        :value="$ctx['footer_brand_tagline'] ?? 'Health · Wellness · Protection'"
                        tag="span"
                        class="inline-block"
                    />
                </span>

                <p class="tf-footer-text" style="margin-top:14px;">
                    One annual membership. Three pillars of cover. A community that
                    shows up for Filipino families when life gets hard.
                </p>
            </div>

            {{-- EXPLORE --}}
            <div>
                <h4>Explore</h4>
                <ul>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('about') }}">Our Story</a></li>
                    <li><a href="{{ route('team') }}">Our People</a></li>
                    <li><a href="{{ route('news') }}">Announcements</a></li>
                </ul>
            </div>

            {{-- MEMBERSHIP --}}
            <div>
                <h4>Membership</h4>
                <ul>
                    <li><a href="{{ route('how-it-works') }}">How to Join</a></li>
                    <li><a href="{{ route('testimonials') }}">Member Stories</a></li>
                    <li><a href="{{ route('contact') }}">Get in Touch</a></li>
                    @guest
                        <li><button type="button" onclick="Livewire.dispatch('open-login-modal')" style="background:none;border:0;padding:0;color:inherit;font:inherit;cursor:pointer;">Member Login</button></li>
                    @endguest
                </ul>
            </div>

            {{-- MEMBER CARE --}}
            <div>
                <h4>Member Care</h4>
                <ul>
                    <li>{{ $ctx['contact_phone_1'] ?? '0917 555 0199' }}</li>
                    <li>{{ $ctx['contact_email_address'] ?? 'care@trifecta.ph' }}</li>
                    <li>Mon – Sat · 8:00am – 5:00pm</li>
                    <li>Tagum City · Kapalong</li>
                </ul>
            </div>

        </div>

        <div class="tf-footer-bottom">
            <p>
                <x-editable-text
                    key="footer_copyright"
                    :value="$ctx['footer_copyright'] ?? ('© ' . date('Y') . ' TRIFECTA. All rights reserved.')"
                    tag="span"
                    class="inline-block"
                />
            </p>

            <span class="tf-footer-chip">Underwritten with Cocogen Insurance</span>
        </div>

    </div>

</footer>
