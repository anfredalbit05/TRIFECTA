<div>
    @if ($open)
        <div x-data="membershipWizard" class="satellite-modal">
            <div class="satellite-modal__container">

                {{-- HEADER --}}
                <header class="satellite-modal__header">
                    <div class="satellite-modal__glow satellite-modal__glow--right"></div>
                    <div class="satellite-modal__glow satellite-modal__glow--left"></div>

                    <div class="satellite-modal__header-content">
                        <div>
                            <div class="satellite-modal__eyebrow">
                                <span class="satellite-modal__header-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/>
                                    </svg>
                                </span>
                                <span>Membership</span>
                            </div>

                            <h2 class="satellite-modal__title">
                                {{ $isReapply ? 'Satellite Membership Reapplication' : 'Satellite Membership Application' }}
                            </h2>

                            <p class="satellite-modal__subtitle">Health &middot; Wellness &middot; Protection</p>

                            @if ($isReapply)
                                <div class="satellite-reapply-banner">
                                    ⟳ Reapply mode — this satellite account was rejected.
                                    The existing account will be <strong>updated</strong>, not duplicated.
                                    Review the pre-filled details before submitting.
                                </div>
                            @endif
                        </div>

                        <button type="button" wire:click="close" class="satellite-modal__close" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </header>

                {{-- WIZARD --}}
                <div class="satellite-modal__wizard">

                    {{-- STEPPER --}}
                    <aside class="satellite-modal__sidebar">
                        <div class="satellite-stepper">
                            <div class="satellite-stepper__line"></div>

                            <div
                                class="satellite-stepper__progress"
                                :style="`
                                    height: ${
                                        step === 1 ? '0%' :
                                        step === 2 ? '50%' : '100%'
                                    }
                                `"
                            ></div>

                            <div class="satellite-stepper__items">
                                <div
                                    class="satellite-stepper__item"
                                    :class="step >= 1 ? 'satellite-stepper__item--active' : ''"
                                >
                                    <span
                                        class="satellite-stepper__number"
                                        :class="step >= 1 ? 'satellite-stepper__number--active' : ''"
                                    >1</span>

                                    <div class="satellite-stepper__details">
                                        <span
                                            class="satellite-stepper__label"
                                            :class="step >= 1 ? 'satellite-stepper__label--active' : ''"
                                        >Plan</span>
                                        <small>Membership details</small>
                                    </div>
                                </div>

                                <div
                                    class="satellite-stepper__item"
                                    :class="step >= 2 ? 'satellite-stepper__item--active' : ''"
                                >
                                    <span
                                        class="satellite-stepper__number"
                                        :class="step >= 2 ? 'satellite-stepper__number--active' : ''"
                                    >2</span>

                                    <div class="satellite-stepper__details">
                                        <span
                                            class="satellite-stepper__label"
                                            :class="step >= 2 ? 'satellite-stepper__label--active' : ''"
                                        >Account</span>
                                        <small>Login credentials</small>
                                    </div>
                                </div>

                                <div
                                    class="satellite-stepper__item"
                                    :class="step >= 3 ? 'satellite-stepper__item--active' : ''"
                                >
                                    <span
                                        class="satellite-stepper__number"
                                        :class="step >= 3 ? 'satellite-stepper__number--active' : ''"
                                    >3</span>

                                    <div class="satellite-stepper__details">
                                        <span
                                            class="satellite-stepper__label"
                                            :class="step >= 3 ? 'satellite-stepper__label--active' : ''"
                                        >Payment</span>
                                        <small>Payment & confirmation</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>

                    {{-- BODY --}}
                    <main id="membership-wizard-body" class="satellite-modal__body">

                        {{-- STEP 1 --}}
                        <div x-show="step === 1" x-transition class="satellite-step">
                            <div class="satellite-step__content satellite-step__content--wide">

                                <div class="satellite-step__heading">
                                    <span class="satellite-step__number-label">Step 01</span>
                                    <h3>Choose Your Membership</h3>
                                    <p>Select the Satellite membership type that best fits your needs.</p>
                                </div>

                                {{-- PLANS --}}
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

                                            <div
                                                class="satellite-plan__card"
                                                style="--plan-color: {{ $meta['color'] }}"
                                            >
                                                <span class="satellite-plan__check">&#10003;</span>

                                                <span
                                                    class="satellite-plan__icon"
                                                    style="background: color-mix(in srgb, {{ $meta['color'] }} 10%, transparent); color: {{ $meta['color'] }}"
                                                >
                                                    {{ $meta['icon'] }}
                                                </span>

                                                <span
                                                    class="satellite-plan__label"
                                                    style="color: {{ $meta['color'] }}"
                                                >
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
                                                        style="background: color-mix(in srgb, {{ $meta['color'] }} 10%, transparent); color: {{ $meta['color'] }}"
                                                    >
                                                        Select {{ $plan }}
                                                    </span>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                @error('satellite_type')
                                    <div class="form-error form-error--box">{{ $message }}</div>
                                @enderror

                                {{-- SELECTION --}}
                                <div
                                    x-show="$wire.satellite_type"
                                    x-transition
                                    class="satellite-selection-info"
                                >
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

                                {{-- SATELLITE DETAILS --}}
                                <div
                                    x-show="$wire.satellite_type"
                                    x-transition
                                    class="satellite-details-card"
                                >
                                    <h4 class="satellite-details-card__title">Satellite Details</h4>

                                    <div class="satellite-details-card__grid">

                                        <div class="form-group">
                                            <label for="satellite-name">
                                                Satellite Name <span>*</span>
                                            </label>

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

                                        <div class="form-group">
                                            <label for="satellite-address">
                                                Address <span>*</span>
                                            </label>

                                            <div class="satellite-address-row">
                                                <input
                                                    id="satellite-address"
                                                    type="text"
                                                    name="address"
                                                    wire:model.live="address"
                                                    placeholder="House No., Street, Barangay, City/Municipality, Province"
                                                >

                                                <button
                                                    type="button"
                                                    wire:click="toggleMap"
                                                    class="satellite-map-toggle"
                                                >
                                                    {{ $showMapModal ? 'Close Map' : 'Pin Map' }}
                                                </button>
                                            </div>

                                            @error('address')
                                                <p class="form-error">{{ $message }}</p>
                                            @enderror

                                            <div
                                                x-show="$wire.showMapModal"
                                                x-cloak
                                                class="satellite-map-panel"
                                            >
                                                <div class="satellite-map-panel__search">
                                                    <input
                                                        id="satellite-leaflet-search-input"
                                                        type="text"
                                                        placeholder="Search location..."
                                                    >

                                                    <button id="satellite-leaflet-search-btn" type="button">
                                                        Search
                                                    </button>
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

                        {{-- STEP 2 --}}
                        <div
                            x-show="step === 2"
                            x-cloak
                            x-transition
                            class="satellite-step"
                        >
                            <div class="satellite-step__content satellite-step__content--account">

                                <div class="satellite-step__heading">
                                    <span class="satellite-step__number-label">Step 02</span>
                                    <h3>Create Your Account</h3>
                                    <p>Enter the login credentials for the new Satellite membership account.</p>
                                </div>

                                <div class="account-card">

                                    <div class="account-membership">
                                        <div>
                                            <p>Membership Selected</p>
                                            <strong x-text="$wire.satellite_type || 'Not selected'"></strong>
                                            <span>
                                                &#8369;{{ number_format($this->membershipAmount, 2) }}
                                                / {{ $this->membershipValidity }} months
                                            </span>
                                        </div>

                                        <button type="button" @click="step = 1">Change</button>
                                    </div>

                                    <div class="account-form">

                                        <div class="form-group">
                                            <label for="satellite-username">Username <span>*</span></label>

                                            <input
                                                id="satellite-username"
                                                type="text"
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
                                            <label for="satellite-password">
                                                Password
                                                @if (! $isReapply)<span>*</span>@endif
                                            </label>

                                            <div class="password-field">
                                                <input
                                                    id="satellite-password"
                                                    name="password"
                                                    wire:model="password"
                                                    :type="show ? 'text' : 'password'"
                                                    autocomplete="new-password"
                                                    placeholder="{{ $isReapply ? 'Leave blank to keep current password' : 'Enter your password' }}"
                                                >

                                                <button type="button" @click="show = !show" x-text="show ? 'Hide' : 'Show'"></button>
                                            </div>

                                            @error('password')
                                                <p class="form-error">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="form-group" x-data="{ show: false }">
                                            <label for="satellite-password-confirmation">
                                                Confirm Password
                                                @if (! $isReapply)<span>*</span>@endif
                                            </label>

                                            <div class="password-field">
                                                <input
                                                    id="satellite-password-confirmation"
                                                    name="password_confirmation"
                                                    wire:model="password_confirmation"
                                                    :type="show ? 'text' : 'password'"
                                                    autocomplete="new-password"
                                                    placeholder="{{ $isReapply ? 'Leave blank to keep current password' : 'Confirm your password' }}"
                                                >

                                                <button type="button" @click="show = !show" x-text="show ? 'Hide' : 'Show'"></button>
                                            </div>

                                            @error('password_confirmation')
                                                <p class="form-error">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    @if ($member)
                                        <div class="member-info">
                                            <p>Account Name</p>
                                            <strong>
                                                {{ $member->firstname }}
                                                {{ $member->middlename }}
                                                {{ $member->lastname }}
                                            </strong>
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

                        {{-- STEP 3 --}}
                        <div
                            x-show="step === 3"
                            x-cloak
                            x-transition
                            class="satellite-step"
                        >
                            <div class="satellite-step__content satellite-step__content--payment">

                                <div class="satellite-step__heading">
                                    <span class="satellite-step__number-label">Step 03</span>
                                    <h3>Choose Payment Method</h3>
                                    <p>Select your preferred payment method.</p>
                                </div>

                                <div class="payment-layout">

                                    {{-- PAYMENT CARD --}}
                                    <div class="payment-card">
                                        <h4>Payment Method</h4>

                                        <div class="payment-methods">
                                            @forelse ($this->availablePaymentMethods as $pm)
                                                <label class="payment-method">
                                                    <input
                                                        type="radio"
                                                        name="payment_method"
                                                        wire:model.live="payment_method"
                                                        value="{{ $pm['method'] }}"
                                                        class="payment-method__input"
                                                    >

                                                    <div
                                                        class="payment-method__content {{ $pm['method'] === 'Cash' ? 'payment-method__content--cash' : '' }}"
                                                    >
                                                        {{-- IMAGE --}}
                                                        <div class="payment-method__image">
                                                            @switch($pm['method'])
                                                                @case('Bank Transfer')
                                                                    <img
                                                                        src="{{ asset('images/satellite/bank-transfer.jpg') }}"
                                                                        alt="Bank Transfer"
                                                                    >
                                                                    @break

                                                                @case('Cash')
                                                                    <img
                                                                        src="{{ asset('images/satellite/cash.jpg') }}"
                                                                        alt="Cash"
                                                                    >
                                                                    @break

                                                                @case('GCash')
                                                                    <img
                                                                        src="{{ asset('images/satellite/gcash.jpg') }}"
                                                                        alt="GCash"
                                                                    >
                                                                    @break

                                                                @case('Maya')
                                                                    <img
                                                                        src="{{ asset('images/satellite/maya.jpg') }}"
                                                                        alt="Maya"
                                                                    >
                                                                    @break
                                                            @endswitch
                                                        </div>

                                                        {{-- PAYMENT DETAILS --}}
                                                        <div class="payment-method__details">
                                                            <p>{{ $pm['method'] }}</p>
                                                            <span>{{ $pm['desc'] ?? '' }}</span>

                                                            {{-- ACCOUNT INFORMATION --}}
                                                            @if (
                                                                $pm['method'] === 'GCash' ||
                                                                $pm['method'] === 'Maya'
                                                            )
                                                                @if (!empty($pm['account_name']) || !empty($pm['account_number']))
                                                                    <div class="payment-account-details">

                                                                        @if (!empty($pm['account_name']))
                                                                            <div class="payment-account-detail">
                                                                                <small>Account Name:</small>
                                                                                <small>
                                                                                <strong>
                                                                                    {{ $pm['account_name'] }}
                                                                                </strong>
                                                                                </small>

                                                                            </div>
                                                                        @endif

                                                                        @if (!empty($pm['account_number']))
                                                                            <div class="payment-account-detail">
                                                                                <small>
                                                                                    {{ $pm['method'] === 'GCash' ? 'GCash Number' : 'Maya Number' }}
                                                                                </small>
                                                                                <small>
                                                                                <strong>
                                                                                    {{ $pm['account_number'] }}
                                                                                </strong>
                                                                                </small>
                                                                            </div>
                                                                        @endif

                                                                    </div>
                                                                @endif
                                                            @elseif ($pm['method'] === 'Bank Transfer')
                                                                @if (
                                                                    !empty($pm['bank_name']) ||
                                                                    !empty($pm['account_name']) ||
                                                                    !empty($pm['account_number'])
                                                                )
                                                                    <div class="payment-account-details">

                                                                        @if (!empty($pm['bank_name']))
                                                                            <div class="payment-account-detail">
                                                                                <small>Bank:</small>
                                                                                <small>
                                                                                <strong>
                                                                                    {{ $pm['bank_name'] }}
                                                                                </strong>
                                                                                </small>
                                                                            </div>
                                                                        @endif

                                                                        @if (!empty($pm['account_name']))
                                                                            <div class="payment-account-detail">
                                                                                <small>Account Name:</small>
                                                                                <small>
                                                                                <strong>
                                                                                    {{ $pm['account_name'] }}
                                                                                </strong>
                                                                                </small>
                                                                            </div>
                                                                        @endif

                                                                        @if (!empty($pm['account_number']))
                                                                            <div class="payment-account-detail">
                                                                                <small>Account Number:</small>
                                                                                <small>
                                                                                <strong>
                                                                                    {{ $pm['account_number'] }}
                                                                                </strong>
                                                                                </small>

                                                                            </div>
                                                                        @endif

                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                </label>
                                            @empty
                                                <div class="form-error form-error--box">
                                                    No payment methods are currently available.
                                                </div>
                                            @endforelse
                                        </div>

                                        @error('payment_method')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror

                                        {{-- CASH --}}
                                        @if ($payment_method === 'Cash')
                                            <div class="cash-message">
                                                <strong>Cash Payment</strong>
                                                <p>
                                                    Please pay the membership amount at the authorized office
                                                    or designated payment location.
                                                </p>
                                                <small>
                                                    This application will be recorded as Walk-in (W).
                                                </small>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- SUMMARY --}}
                                    <div class="summary-card">
                                        <h4>Application Summary</h4>

                                        <div class="summary-list">

                                            <div class="summary-item">
                                                <p>Membership</p>

                                                <div>
                                                    <strong x-text="$wire.satellite_type"></strong>
                                                    <strong>
                                                        &#8369;{{ number_format($this->membershipAmount, 2) }}
                                                    </strong>
                                                </div>

                                                <small>
                                                    Valid for {{ $this->membershipValidity }} months
                                                </small>
                                            </div>

                                            @if ($member)
                                                <div class="summary-item">
                                                    <p>Member</p>
                                                    <strong>
                                                        {{ $member->firstname }}
                                                        {{ $member->middlename }}
                                                        {{ $member->lastname }}
                                                    </strong>
                                                </div>
                                            @endif

                                            <div class="summary-item">
                                                <p>Account Email</p>
                                                <strong class="summary-email">
                                                    {{ $email ?: 'No email provided' }}
                                                </strong>
                                            </div>

                                                 {{-- ONLINE PAYMENT --}}
                                            @if ($this->paymentIsOnline)
                                            <div x-show="$wire.payment_method" class="reference-card">
                                                <p>Payment Method: <strong x-text="$wire.payment_method"></strong> </p>
                                                <label for="satellite-reference">
                                                    Payment Reference Code <span>*</span>
                                                </label>

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

                                                <small>
                                                    Enter the reference number provided by your payment transaction.
                                                </small>
                                            </div>
                                            @endif

                                            <div class="form-group">
                                                <label for="satellite-uplineCode">
                                                    Upline Code <span>*</span>
                                                </label>

                                                <input
                                                    id="satellite-uplineCode"
                                                    type="text"
                                                    name="upline"
                                                    wire:model.live="uplineCode"
                                                    autocomplete="off"
                                                    placeholder="FDR1234"
                                                >

                                                @error('uplineCode')
                                                    <p class="form-error">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- TERMS --}}
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

                                {{-- PAYMENT INSTRUCTION --}}
                                <div
                                    x-show="$wire.payment_method"
                                    x-transition
                                    class="payment-instruction"
                                >
                                    <span class="payment-instruction__icon">i</span>

                                    <div>
                                        <strong>Payment Instructions</strong>

                                        @if ($payment_method === 'Cash')
                                            <p>
                                                Please settle the membership amount through the authorized office.
                                                Your application will be recorded as a Walk-in (W) activation
                                                and will remain pending until verified.
                                            </p>
                                        @else
                                            <p>
                                                Make your payment using the selected payment method and provide
                                                the transaction reference code. Your application will remain
                                                pending until payment is verified.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>

                {{-- FOOTER --}}
                <footer class="satellite-modal__footer">
                    <div class="satellite-modal__footer-left">
                        <button
                            type="button"
                            wire:click="close"
                            class="wizard-button wizard-button--cancel"
                        >
                            Cancel
                        </button>
                    </div>

                    <div class="satellite-modal__footer-right">

                        <button
                            type="button"
                            x-show="step > 1"
                            x-cloak
                            @click="previousStep()"
                            class="wizard-button wizard-button--back"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Back
                        </button>

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
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>

                        <button
                            type="button"
                            wire:click="validateAndSubmit"
                            wire:loading.attr="disabled"
                            wire:target="validateAndSubmit"
                            x-show="step === 3"
                            x-cloak
                            class="wizard-button wizard-button--submit"
                        >
                            <span wire:loading.remove wire:target="validateAndSubmit">
                                Submit Application
                            </span>

                            <span wire:loading.flex wire:target="validateAndSubmit" class="submit-loading">
                                <svg
                                    class="submit-loading__icon"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="submit-loading__circle"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>

                                    <path
                                        class="submit-loading__path"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                    ></path>
                                </svg>
                                Submitting...
                            </span>
                        </button>
                    </div>
                </footer>
            </div>
        </div>
    @endif

    {{-- LEAFLET MAP --}}
    @vite('resources/js/livewire/admin-pages/masterlist/members-modals/satelite-modal.js')
</div>

