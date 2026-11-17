
<div class="contact-page">
    {{-- HERO --}}
    <section class="contact-hero">
        <div class="contact-hero-glow-left"></div>
        <div class="contact-hero-glow-right"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 text-center">
            <span class="contact-hero-badge">
                <x-editable-text key="contact_hero_badge" :value="$contexts['contact_hero_badge'] ?? ''" tag="span" class="inline-block"/>
            </span>
            <h1 class="contact-hero-title">
                <x-editable-text key="contact_hero_title_1" :value="$contexts['contact_hero_title_1'] ?? ''" tag="span" class="inline-block"/>
                <br class="hidden sm:block">
                <span class="contact-hero-title-highlight">
                    <x-editable-text key="contact_hero_title_highlight" :value="$contexts['contact_hero_title_highlight'] ?? ''" tag="span" class="inline-block"/>
                </span>
            </h1>
            <p class="contact-hero-description">
                <x-editable-text key="contact_hero_description" :value="$contexts['contact_hero_description'] ?? ''" tag="span" class="block"/>
            </p>
        </div>
    </section>

    {{-- CONTACT INFORMATION --}}
    <section class="contact-details-section">
        <div class="max-w-7xl mx-auto px-6">
            <div class="contact-details-header">
                <span class="contact-details-subtitle">
                    <x-editable-text key="contact_details_label" :value="$contexts['contact_details_label'] ?? ''" tag="span" class="inline-block"/>
                </span>
                <h2 class="contact-details-title">
                    <x-editable-text key="contact_details_title" :value="$contexts['contact_details_title'] ?? ''" tag="span" class="inline-block"/>
                </h2>
                <p class="contact-details-description">
                    <x-editable-text key="contact_details_description" :value="$contexts['contact_details_description'] ?? ''" tag="span" class="inline-block"/>
                </p>
            </div>

            <div class="contact-details-grid">
                {{-- MAIN OFFICE --}}
                <div class="contact-detail-card">
                    <div class="contact-card-icon">
                        <flux:icon.map-pin/>
                    </div>
                    <h3 class="contact-detail-title">
                        <x-editable-text key="contact_main_office_title" :value="$contexts['contact_main_office_title'] ?? ''" tag="span" class="inline-block"/>
                    </h3>
                    <p class="contact-card-text">
                        <x-editable-text key="contact_main_office_address" :value="$contexts['contact_main_office_address'] ?? ''" tag="span" class="inline-block"/>
                    </p>
                </div>

                {{-- SUB OFFICE --}}
                <div class="contact-detail-card">
                    <div class="contact-card-icon">
                        <flux:icon.building-office-2/>
                    </div>
                    <h3 class="contact-detail-title">
                        <x-editable-text key="contact_sub_office_title" :value="$contexts['contact_sub_office_title'] ?? ''" tag="span" class="inline-block"/>
                    </h3>
                    <p class="contact-card-text">
                        <x-editable-text key="contact_sub_office_address" :value="$contexts['contact_sub_office_address'] ?? ''" tag="span" class="inline-block"/>
                    </p>
                </div>

                {{-- PHONE --}}
                <div class="contact-detail-card">
                    <div class="contact-card-icon">
                        <flux:icon.phone/>
                    </div>
                    <h3 class="contact-detail-title">
                        <x-editable-text key="contact_phone_title" :value="$contexts['contact_phone_title'] ?? ''" tag="span" class="inline-block"/>
                    </h3>
                    <p class="contact-card-text-bold">
                        <x-editable-text key="contact_phone_1" :value="$contexts['contact_phone_1'] ?? ''" tag="span" class="inline-block"/>
                    </p>
                    <p class="contact-card-text-bold">
                        <x-editable-text key="contact_phone_2" :value="$contexts['contact_phone_2'] ?? ''" tag="span" class="inline-block"/>
                    </p>
                </div>

                {{-- EMAIL --}}
                <div class="contact-detail-card">
                    <div class="contact-card-icon">
                        <flux:icon.envelope/>
                    </div>
                    <h3 class="contact-detail-title">
                        <x-editable-text key="contact_email_title" :value="$contexts['contact_email_title'] ?? ''" tag="span" class="inline-block"/>
                    </h3>
                    <p class="contact-card-text break-all">
                        <x-editable-text key="contact_email_address" :value="$contexts['contact_email_address'] ?? ''" tag="span" class="inline-block"/>
                    </p>
                </div>
            </div>

            {{-- FACEBOOK COMMUNITY --}}
            <div class="contact-facebook-banner">
                <div class="contact-facebook-icon">
                    <flux:icon.hand-thumb-up/>
                </div>
                <h3 class="contact-facebook-title">
                    <x-editable-text key="contact_fb_title" :value="$contexts['contact_fb_title'] ?? ''" tag="span" class="inline-block"/>
                </h3>
                <p class="contact-facebook-text">
                    <strong>
                        <x-editable-text key="contact_fb_name" :value="$contexts['contact_fb_name'] ?? ''" tag="span" class="inline-block"/>
                    </strong>
                    <br>
                    <x-editable-text key="contact_fb_sub" :value="$contexts['contact_fb_sub'] ?? ''" tag="span" class="inline-block"/>
                </p>
                <a href="{{ $contexts['contact_fb_url'] ?? 'https://facebook.com' }}" target="_blank" rel="noopener noreferrer" class="contact-facebook-btn">
                    <flux:icon.globe-alt class="w-5 h-5"/>
                    <x-editable-text key="contact_fb_btn_text" :value="$contexts['contact_fb_btn_text'] ?? 'Visit Our Facebook'" tag="span" class="inline-block"/>
                    <flux:icon.arrow-up-right class="w-4 h-4"/>
                </a>
            </div>
        </div>
    </section>

    {{-- OFFICE LOCATION --}}
    <section class="contact-map-section">
        <div class="max-w-7xl mx-auto px-6">
            <div class="contact-map-header">
                <span class="contact-map-subtitle">
                    <x-editable-text key="contact_map_subtitle" :value="$contexts['contact_map_subtitle'] ?? ''" tag="span" class="inline-block"/>
                </span>
                <h2 class="contact-map-title">
                    <x-editable-text key="contact_map_title" :value="$contexts['contact_map_title'] ?? ''" tag="span" class="inline-block"/>
                </h2>
                <p class="contact-map-location">
                    <x-editable-text key="contact_map_location" :value="$contexts['contact_map_location'] ?? ''" tag="span" class="inline-block"/>
                </p>
            </div>

            <div class="contact-map-container">
                <iframe
                    src="https://maps.google.com/maps?q=7.449762432011926,125.80273268264297&z=17&output=embed"
                    class="contact-map-iframe"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <div class="mt-7 text-center">
                <a href="https://www.google.com/maps/search/?api=1&query=7.449762432011926,125.80273268264297"
                   target="_blank" rel="noopener noreferrer" class="contact-facebook-btn">
                    <flux:icon.map-pin class="w-5 h-5"/>
                    Open Location in Google Maps
                    <flux:icon.arrow-up-right class="w-4 h-4"/>
                </a>
            </div>
        </div>
    </section>

    {{-- INQUIRY FORM --}}
    <section class="contact-form-section">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="contact-form-card">
                <div class="contact-form-header">
                    <span class="contact-form-label">TRIFECTA SUPPORT</span>
                    <h2 class="contact-form-title">Get In Touch With Us</h2>
                    <p class="contact-form-desc">Have questions or concerns? Our team is ready to assist you.</p>
                </div>

                @if (session()->has('inquiry_success'))
                    <div class="contact-form-alert">
                        <flux:icon.check-circle class="inline w-5 h-5 mr-1"/>
                        {{ session('inquiry_success') }}
                    </div>
                @endif

                <form wire:submit.prevent="confirmInquiry" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="contact-name" class="contact-form-label-text">Your Name *</label>
                            <input id="contact-name" type="text" wire:model="name" class="contact-form-input" placeholder="Enter your full name">
                            @error('name') <span class="contact-form-error">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="contact-email" class="contact-form-label-text">Email Address *</label>
                            <input id="contact-email" type="email" wire:model="email" class="contact-form-input" placeholder="Enter your email address">
                            @error('email') <span class="contact-form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="contact-subject" class="contact-form-label-text">Subject *</label>
                            <input id="contact-subject" type="text" wire:model="subject" class="contact-form-input" placeholder="Subject of your inquiry">
                            @error('subject') <span class="contact-form-error">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="contact-category" class="contact-form-label-text">Inquiry Category</label>
                            <select id="contact-category" wire:model="category" class="contact-form-select">
                                <option value="">Select a category</option>
                                <option value="Membership">Membership</option>
                                <option value="Health Perks">Health Perks</option>
                                <option value="Claims">Claims</option>
                                <option value="General Inquiry">General Inquiry</option>
                            </select>
                            @error('category') <span class="contact-form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="contact-details" class="contact-form-label-text">Message Details *</label>
                        <textarea id="contact-details" wire:model="details" rows="5" class="contact-form-textarea" placeholder="Tell us how we can help you..."></textarea>
                        @error('details') <span class="contact-form-error">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled" wire:target="confirmInquiry" class="contact-form-submit-btn">
                        <span wire:loading.remove wire:target="confirmInquiry" class="inline-flex items-center justify-center gap-2">
                            <flux:icon.paper-airplane class="w-5 h-5"/>
                            Submit Inquiry
                        </span>
                        <span wire:loading wire:target="confirmInquiry" class="inline-flex items-center justify-center gap-2">
                            <flux:icon.arrow-path class="w-5 h-5 animate-spin"/>
                            Submitting...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </section>
</div>
