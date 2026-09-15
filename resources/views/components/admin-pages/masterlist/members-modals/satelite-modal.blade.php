<div>
    @if ($open)
   <div
    x-data="{
        step: 1,

        async nextStep() {
            if (this.step >= 3) return;

            try {
                const result = await $wire.validateStep(this.step);

                if (!result.valid) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Please Check Your Information',
                        html: `
                            <div style='text-align: left;'>
                                <ul style='margin: 0; padding-left: 20px;'>
                                    ${result.errors
                                        .map(error => `<li>${error}</li>`)
                                        .join('')}
                                </ul>
                            </div>
                        `,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#0B2A5B'
                    });

                    this.scrollTop();
                    return;
                }

                this.step++;
                this.scrollTop();

            } catch (error) {
                console.error(error);

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Unable to validate the form. Please try again.',
                    confirmButtonColor: '#0B2A5B'
                });
            }
        },

        previousStep() {
            if (this.step > 1) {
                this.step--;
                this.scrollTop();
            }
        },

        scrollTop() {
            this.$nextTick(() => {
                const body = document.getElementById(
                    'membership-wizard-body'
                );

                if (body) {
                    body.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });
        }
    }"
    class="satellite-modal"
>
            <div class="satellite-modal__container">

                {{-- ============ HEADER + STEPPER ============ --}}
                <header class="satellite-modal__header">
                    <div class="satellite-modal__glow satellite-modal__glow--right"></div>
                    <div class="satellite-modal__glow satellite-modal__glow--left"></div>

                    <div class="satellite-modal__header-content">
                        <div>
                            <div class="satellite-modal__eyebrow">
                                <span class="satellite-modal__header-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                                    </svg>
                                </span>

                                <span>Membership</span>
                            </div>

                            <h2 class="satellite-modal__title">Membership Application</h2>
                            <p class="satellite-modal__subtitle">True Life Solidaritas &bull; Vitae Auxilium</p>
                        </div>

                        <button type="button" wire:click="close" class="satellite-modal__close" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <nav class="satellite-stepper">
                        <div class="satellite-stepper__line"></div>

                        <div
                            class="satellite-stepper__progress"
                            :style="`width: ${ step === 1 ? '0%' : step === 2 ? '33.33%' : '66.67%' }`"
                        ></div>

                        <div class="satellite-stepper__items">
                            @foreach ([1 => 'Plan', 2 => 'Account', 3 => 'Payment'] as $number => $label)
                                <div class="satellite-stepper__item">
                                    <span
                                        class="satellite-stepper__number"
                                        :class="step >= {{ $number }} ? 'satellite-stepper__number--active' : ''"
                                    >{{ $number }}</span>

                                    <span
                                        class="satellite-stepper__label"
                                        :class="step >= {{ $number }} ? 'satellite-stepper__label--active' : ''"
                                    >{{ $label }}</span>
                                </div>
                            @endforeach
                        </div>
                    </nav>
                </header>

                {{-- ============ BODY ============ --}}
                <div id="membership-wizard-body" class="satellite-modal__body">

                    {{-- ================= STEP 1 — PLAN & SATELLITE DETAILS ================= --}}
                    <div x-show="step === 1" x-transition class="satellite-step">
                        <div class="satellite-step__content satellite-step__content--wide">

                            <div class="satellite-step__heading">
                                <span class="satellite-step__number-label">Step 01</span>
                                <h3>Choose Your Membership</h3>
                                <p>Select the Satellite membership type that best fits your needs.</p>
                            </div>

                            {{-- Plan cards --}}
                            <div class="satellite-plans">
                                @foreach ($this->planMeta as $plan => $meta)
                                    <label class="satellite-plan">
                                        <input
                                            type="radio"
                                            name="satellite_type"
                                            wire:model.live="satellite_type"
                                            value="{{ $plan }}"
                                            class="satellite-plan__input"
                                        >

                                        <div class="satellite-plan__card" style="--plan-color: {{ $meta['color'] }}">
                                            <span class="satellite-plan__check">&#10003;</span>

                                            <span
                                                class="satellite-plan__icon"
                                                style="background: {{ $meta['color'] }}15; color: {{ $meta['color'] }};"
                                            >{{ $meta['icon'] }}</span>

                                            <span class="satellite-plan__label" style="color: {{ $meta['color'] }}">
                                                {{ $meta['label'] }}
                                            </span>

                                            <h4>{{ $plan }}</h4>

                                            <p>
                                                @if ($plan === 'Bronze')
                                                    An affordable membership option for essential protection and support.
                                                @elseif ($plan === 'Silver')
                                                    Enhanced membership benefits with broader protection and support.
                                                @else
                                                    Premium membership with the highest level of benefits and protection.
                                                @endif
                                            </p>

                                            <div class="satellite-plan__divider"></div>

                                            <ul>
                                                @if ($plan === 'Bronze')
                                                    <li>&#10003; Basic membership benefits</li>
                                                    <li>&#10003; Member support</li>
                                                    <li>&#10003; Standard coverage</li>
                                                @elseif ($plan === 'Silver')
                                                    <li>&#10003; Enhanced benefits</li>
                                                    <li>&#10003; Extended support</li>
                                                    <li>&#10003; Improved coverage</li>
                                                @else
                                                    <li>&#10003; Premium benefits</li>
                                                    <li>&#10003; Priority support</li>
                                                    <li>&#10003; Maximum coverage</li>
                                                @endif
                                            </ul>

                                            <div class="satellite-plan__footer">
                                                <span
                                                    class="satellite-plan__select"
                                                    style="background: {{ $meta['color'] }}15; color: {{ $meta['color'] }};"
                                                >Select {{ $plan }}</span>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            @error('satellite_type')
                                <div class="form-error form-error--box">{{ $message }}</div>
                            @enderror

                            {{-- Selection summary — shows once a plan is picked --}}
                            <div x-show="$wire.satellite_type" x-transition class="satellite-selection-info">
                                <div class="selection-card selection-card--gold">
                                    <p>Selected Membership</p>
                                    <span x-text="$wire.satellite_type"></span>
                                </div>

                                <div class="selection-card selection-card--blue">
                                    <p>Membership Amount</p>
                                    <span>&#8369;{{ number_format($this->membershipAmount, 2) }}</span>
                                    <small>Valid for {{ $this->membershipValidity }} months</small>
                                </div>
                            </div>

                            {{-- Satellite details: name, address, map pin --}}
                            <div
                                x-show="$wire.satellite_type"
                                x-transition
                                class="satellite-details-card"
                            >
                                <h4 class="satellite-details-card__title">Satellite Details</h4>

                                <div class="satellite-details-card__grid">
                                    {{-- Satellite name --}}
                                    <div class="form-group">
                                        <label for="satellite-name">Satellite Name <span>*</span></label>

                                        <input
                                            id="satellite-name"
                                            type="text"
                                            name="satellite_name"
                                            wire:model.live="satellite_name"
                                            placeholder="e.g. Poblacion Satellite Office"
                                        >

                                        @error('satellite_name')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Address + map pin --}}
                                    <div class="form-group">
                                        <label for="satellite-address">Address <span>*</span></label>

                                        <div class="satellite-address-row">
                                            <input
                                                id="satellite-address"
                                                type="text"
                                                name="address"
                                                wire:model.live="address"
                                                placeholder="House No., Street, Barangay, City/Municipality, Province"
                                            >

                                            <button type="button" wire:click="toggleMap" class="satellite-map-toggle">
                                                {{ $showMapModal ? 'Close Map' : 'Pin Map' }}
                                            </button>
                                        </div>

                                        @error('address')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror

                                        {{-- Leaflet map panel --}}
                                        <div x-show="$wire.showMapModal" x-cloak class="satellite-map-panel">
                                            <div class="satellite-map-panel__search">
                                                <input
                                                    id="satellite-leaflet-search-input"
                                                    type="text"
                                                    placeholder="Search location..."
                                                >
                                                <button id="satellite-leaflet-search-btn" type="button">Search</button>
                                            </div>

                                            <div class="satellite-map-panel__map">
                                                <div id="satellite-map" wire:ignore></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ================= STEP 2 — ACCOUNT CREDENTIALS ================= --}}
                    <div x-show="step === 2" x-cloak x-transition class="satellite-step">
                        <div class="satellite-step__content satellite-step__content--account">

                            <div class="satellite-step__heading">
                                <span class="satellite-step__number-label">Step 02</span>
                                <h3>Create Your Account</h3>
                                <p>Enter the login credentials for the new Satellite membership account.</p>
                            </div>

                            <div class="account-card">
                                {{-- Selected plan recap --}}
                                <div class="account-membership">
                                    <div>
                                        <p>Membership Selected</p>
                                        <strong x-text="$wire.satellite_type || 'Not selected'"></strong>
                                        <span>
                                            &#8369;{{ number_format($this->membershipAmount, 2) }} / {{ $this->membershipValidity }} months
                                        </span>
                                    </div>

                                    <button type="button" @click="step = 1">Change</button>
                                </div>

                                {{-- Credential inputs --}}
                                    <div class="account-form">

                                    <div class="form-group">
                                        <label for="satellite-username">Username<span>*</span></label>

                                        <input
                                            id="satellite-username"
                                            type="username"
                                            name="username"
                                            wire:model.live="username"
                                            autocomplete="username"
                                            placeholder="username123"
                                        >

                                        @error('username')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="satellite-email">Email Address <span>*</span></label>

                                        <input
                                            id="satellite-email"
                                            type="email"
                                            name="email"
                                            wire:model.live="email"
                                            autocomplete="email"
                                            placeholder="you@example.com"
                                        >

                                        @error('email')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group" x-data="{ show: false }">
                                        <label for="satellite-password">Password <span>*</span></label>

                                        <div class="password-field">
                                            <input
                                                id="satellite-password"
                                                type="password"
                                                name="password"
                                                wire:model="password"
                                                :type="show ? 'text' : 'password'"
                                                autocomplete="new-password"
                                                placeholder="Enter your password"
                                            >

                                            <button type="button" @click="show = !show" x-text="show ? 'Hide' : 'Show'"></button>
                                        </div>

                                        @error('password')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group" x-data="{ show: false }">
                                        <label for="satellite-password-confirmation">Confirm Password <span>*</span></label>

                                        <div class="password-field">
                                            <input
                                                id="satellite-password-confirmation"
                                                type="password"
                                                name="password_confirmation"
                                                wire:model="password_confirmation"
                                                :type="show ? 'text' : 'password'"
                                                autocomplete="new-password"
                                                placeholder="Confirm your password"
                                            >

                                            <button type="button" @click="show = !show" x-text="show ? 'Hide' : 'Show'"></button>
                                        </div>

                                        @error('password_confirmation')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                @if ($member)
                                    {{-- Account name recap (copied from sponsor member) --}}
                                    <div class="member-info">
                                        <p>Account Name</p>
                                        <strong>{{ $member->firstname }} {{ $member->middlename }} {{ $member->lastname }}</strong>
                                        <span>This information is copied from the selected existing member.</span>
                                    </div>
                                @endif

                                <div class="info-message">
                                    Your account credentials will be used to access your Satellite membership account.
                                    Please keep your password secure.
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ================= STEP 3 — PAYMENT ================= --}}
                    <div x-show="step === 3" x-cloak x-transition class="satellite-step">
                        <div class="satellite-step__content satellite-step__content--payment">

                            <div class="satellite-step__heading">
                                <span class="satellite-step__number-label">Step 03</span>
                                <h3>Choose Payment Method</h3>
                                <p>Select your preferred payment method.</p>
                            </div>

                            <div class="payment-layout">

                                {{-- Payment methods + reference/cash info --}}
                                <div class="payment-card">
                                    <h4>Payment Method</h4>

                                    <div class="payment-methods">
                                        @foreach ($this->availablePaymentMethods as $pm)
                                            <label class="payment-method">
                                                <input
                                                    type="radio"
                                                    name="payment_method"
                                                    wire:model.live="payment_method"
                                                    value="{{ $pm['method'] }}"
                                                    class="payment-method__input"
                                                >

                                                <div class="payment-method__content {{ $pm['method'] === 'Cash' ? 'payment-method__content--cash' : '' }}">
                                                    <span class="payment-method__icon {{ $pm['method'] === 'GCash' ? 'payment-method__icon--gcash' : ($pm['method'] === 'Maya' ? 'payment-method__icon--maya' : ($pm['method'] === 'Bank Transfer' ? 'payment-method__icon--bank' : '')) }}">
                                                        {{ $pm['icon'] }}
                                                    </span>

                                                    <div>
                                                        <p>{{ $pm['method'] }}</p>
                                                        <span>{{ $pm['desc'] }}</span>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>

                                    @error('payment_method')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror

                                    {{-- Online payment: reference code input --}}
                                    @if ($this->paymentIsOnline)
                                        <div class="reference-card">
                                            <label for="satellite-reference">Payment Reference Code <span>*</span></label>

                                            <input
                                                id="satellite-reference"
                                                type="text"
                                                name="reference_code"
                                                wire:model="reference_code"
                                                placeholder="Enter payment reference code"
                                            >

                                            @error('reference_code')
                                                <p class="form-error">{{ $message }}</p>
                                            @enderror

                                            <small>Enter the reference number provided by your payment transaction.</small>
                                        </div>
                                    @endif

                                    {{-- Cash payment notice --}}
                                    @if ($payment_method === 'Cash')
                                        <div class="cash-message">
                                            <strong>Cash Payment</strong>
                                            <p>Please pay the membership amount at the authorized office or designated payment location.</p>
                                            <small>This application will be recorded as Walk-in (W).</small>
                                        </div>
                                    @endif
                                </div>

                                {{-- Application summary --}}
                                <div class="summary-card">
                                    <h4>Application Summary</h4>

                                    <div class="summary-list">
                                        <div class="summary-item">
                                            <p>Membership</p>

                                            <div>
                                                <strong x-text="$wire.satellite_type"></strong>
                                                <strong>&#8369;{{ number_format($this->membershipAmount, 2) }}</strong>
                                            </div>

                                            <small>Valid for {{ $this->membershipValidity }} months</small>
                                        </div>

                                        @if ($member)
                                            <div class="summary-item">
                                                <p>Member</p>
                                                <strong>{{ $member->firstname }} {{ $member->middlename }} {{ $member->lastname }}</strong>
                                            </div>
                                        @endif

                                        <div class="summary-item">
                                            <p>Account Email</p>
                                            <strong class="summary-email">{{ $email ?: 'No email provided' }}</strong>
                                        </div>

                                        <div x-show="$wire.payment_method" class="summary-payment">
                                            <p>Payment Method</p>

                                            <strong x-text="$wire.payment_method"></strong>

                                            <small :class="$wire.paymentIsOnline ? 'summary-payment__online' : 'summary-payment__cash'">
                                                Activation Type: <span x-text="$wire.paymentIsOnline ? 'O — Online' : 'W — Walk-in'"></span>
                                            </small>

                                            <template x-if="$wire.paymentIsOnline && $wire.reference_code">
                                                <small>Reference: <span x-text="$wire.reference_code"></span></small>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- Terms --}}
                                    <div class="terms">
                                        <label>
                                            <input type="checkbox" wire:model.live="agree">

                                            <span>
                                                I confirm that the information provided is accurate and complete,
                                                and I agree to the membership terms and conditions.
                                            </span>
                                        </label>

                                        @error('agree')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                            {{-- Payment instruction banner --}}
                            <div x-show="$wire.payment_method" x-transition class="payment-instruction">
                                <span class="payment-instruction__icon">i</span>

                                <div>
                                    <strong>Payment Instructions</strong>

                                    @if ($payment_method === 'Cash')
                                        <p>
                                            Please settle the membership amount through the authorized office.
                                            Your application will be recorded as a Walk-in (W) activation and will
                                            remain pending until verified.
                                        </p>
                                    @else
                                        <p>
                                            Make your payment using the selected payment method and provide the
                                            transaction reference code. Your application will remain pending until
                                            payment is verified.
                                        </p>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- ============ FOOTER ============ --}}
                <footer class="satellite-modal__footer">
                    <div class="satellite-modal__footer-left">
                        <button
                            type="button"
                            x-show="step > 1"
                            x-cloak
                            @click="previousStep()"
                            class="wizard-button wizard-button--back"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </button>

                        <button type="button" wire:click="close" class="wizard-button wizard-button--cancel">Cancel</button>
                    </div>

                    <div class="satellite-modal__footer-right">
                        {{-- Next step --}}
                        <button
                            type="button"
                            x-show="step < 3"
                            @click="nextStep()"
                            wire:loading.attr="disabled"
                            wire:target="validateStep"
                            class="wizard-button wizard-button--next"
                        >
                            Next Step

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        {{-- Submit --}}
                        <button
                            type="button"
                            wire:click="submit"
                            wire:loading.attr="disabled"
                            wire:target="submit"
                            x-show="step === 3"
                            x-cloak
                            :disabled="!$wire.agree"
                            class="wizard-button wizard-button--submit"
                        >
                            <span wire:loading.remove wire:target="submit">Submit Application</span>

                            <span wire:loading.flex wire:target="submit" class="submit-loading">
                                <svg class="submit-loading__icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="submit-loading__circle" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="submit-loading__path" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                Submitting...
                            </span>
                        </button>
                    </div>
                </footer>

            </div>
        </div>
    @endif
</div>

{{-- Leaflet map wiring: init on open, cleanup on close, search via Nominatim --}}
<script>
    let satelliteMap = null;
    let satelliteMarker = null;

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('satellite-destroy-map', () => {
            if (satelliteMap) {
                satelliteMap.remove();
                satelliteMap = null;
                satelliteMarker = null;
            }
        });

        Livewire.on('satellite-map-opened', (data) => {
            setTimeout(() => {
                const lat = data.latitude ?? 7.442294461028405;
                const lng = data.longitude ?? 125.81370503321095;

                if (satelliteMap) {
                    satelliteMap.remove();
                    satelliteMap = null;
                    satelliteMarker = null;
                }

                satelliteMap = L.map('satellite-map').setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap',
                }).addTo(satelliteMap);

                if (data.latitude != null && data.longitude != null) {
                    satelliteMarker = L.marker([lat, lng]).addTo(satelliteMap);
                    satelliteMap.setView([lat, lng], 16);
                }

                const searchInput = document.getElementById('satellite-leaflet-search-input');
                const searchBtn = document.getElementById('satellite-leaflet-search-btn');

                const setPin = (lat, lng) => {
                    const center = [parseFloat(lat), parseFloat(lng)];
                    satelliteMap.setView(center, 16);

                    if (satelliteMarker) {
                        satelliteMarker.setLatLng(center);
                    } else {
                        satelliteMarker = L.marker(center).addTo(satelliteMap);
                    }

                    Livewire.dispatch('satellite-set-coordinates', { lat: center[0], lng: center[1] });
                };

                const performSearch = () => {
                    if (!searchInput) return;

                    const query = searchInput.value.trim();
                    if (!query) return;

                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                        .then((response) => response.json())
                        .then((results) => {
                            if (results && results.length > 0) {
                                setPin(results[0].lat, results[0].lon);
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Location Not Found',
                                    text: 'Please try another location.',
                                    confirmButtonColor: '#0B2A5B',
                                });
                            }
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Search Error',
                                text: 'Unable to search for this location. Please try again.',
                                confirmButtonColor: '#0B2A5B',
                            });
                        });
                };

                if (searchInput) {
                    searchInput.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            performSearch();
                        }
                    });
                }

                if (searchBtn) {
                    searchBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        performSearch();
                    });
                }

                satelliteMap.on('click', (e) => setPin(e.latlng.lat, e.latlng.lng));

                setTimeout(() => satelliteMap?.invalidateSize(), 100);
            }, 300);
        });
    });
</script>
