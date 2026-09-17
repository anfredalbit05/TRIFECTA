<div>
    @if ($open)
        <div
             x-data="membershipWizard"
             class="satellite-modal"
        >
            <div class="satellite-modal__container">

                {{-- =========================================================
                    HEADER
                ========================================================== --}}
                <header class="satellite-modal__header">

                    <div class="satellite-modal__glow satellite-modal__glow--right"></div>
                    <div class="satellite-modal__glow satellite-modal__glow--left"></div>

                    <div class="satellite-modal__header-content">

                        <div>
                            <div class="satellite-modal__eyebrow">
                                <span class="satellite-modal__header-icon">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="2"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 6v12m6-6H6"
                                        />
                                    </svg>
                                </span>

                                <span>Membership</span>
                            </div>

                            <h2 class="satellite-modal__title">
                                @if ($isReapply)
                                    Satellite Membership Reapplication
                                @else
                                    Satellite Membership Application
                                @endif
                            </h2>

                            <p class="satellite-modal__subtitle">
                                True Life Solidaritas
                            </p>

                            @if ($isReapply)
                                <div class="satellite-reapply-banner">
                                    ⟳ Reapply mode — this satellite account was rejected.
                                    Their existing account will be <strong>updated</strong>,
                                    not duplicated. Review the pre-filled details below
                                    before submitting.
                                </div>
                            @endif
                        </div>

                        <button
                            type="button"
                            wire:click="close"
                            class="satellite-modal__close"
                            aria-label="Close"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>

                    </div>
                </header>


                {{-- =========================================================
                    WIZARD AREA
                    LEFT = STEPPER
                    RIGHT = SCROLLABLE CONTENT
                ========================================================== --}}
                <div class="satellite-modal__wizard">

                    {{-- =====================================================
                        LEFT SIDEBAR / VERTICAL STEPPER
                    ====================================================== --}}
                    <aside class="satellite-modal__sidebar">

                        <div class="satellite-stepper">

                            <div class="satellite-stepper__line"></div>

                            <div
                                class="satellite-stepper__progress"
                                :style="`
                                    height: ${
                                        step === 1
                                            ? '0%'
                                            : step === 2
                                                ? '50%'
                                                : '100%'
                                    }
                                `"
                            ></div>

                            <div class="satellite-stepper__items">

                                {{-- STEP 1 --}}
                                <div
                                    class="satellite-stepper__item"
                                    :class="step >= 1 ? 'satellite-stepper__item--active' : ''"
                                >
                                    <span
                                        class="satellite-stepper__number"
                                        :class="step >= 1 ? 'satellite-stepper__number--active' : ''"
                                    >
                                        1
                                    </span>

                                    <div class="satellite-stepper__details">
                                        <span
                                            class="satellite-stepper__label"
                                            :class="step >= 1 ? 'satellite-stepper__label--active' : ''"
                                        >
                                            Plan
                                        </span>

                                        <small>
                                            Membership details
                                        </small>
                                    </div>
                                </div>


                                {{-- STEP 2 --}}
                                <div
                                    class="satellite-stepper__item"
                                    :class="step >= 2 ? 'satellite-stepper__item--active' : ''"
                                >
                                    <span
                                        class="satellite-stepper__number"
                                        :class="step >= 2 ? 'satellite-stepper__number--active' : ''"
                                    >
                                        2
                                    </span>

                                    <div class="satellite-stepper__details">
                                        <span
                                            class="satellite-stepper__label"
                                            :class="step >= 2 ? 'satellite-stepper__label--active' : ''"
                                        >
                                            Account
                                        </span>

                                        <small>
                                            Login credentials
                                        </small>
                                    </div>
                                </div>


                                {{-- STEP 3 --}}
                                <div
                                    class="satellite-stepper__item"
                                    :class="step >= 3 ? 'satellite-stepper__item--active' : ''"
                                >
                                    <span
                                        class="satellite-stepper__number"
                                        :class="step >= 3 ? 'satellite-stepper__number--active' : ''"
                                    >
                                        3
                                    </span>

                                    <div class="satellite-stepper__details">
                                        <span
                                            class="satellite-stepper__label"
                                            :class="step >= 3 ? 'satellite-stepper__label--active' : ''"
                                        >
                                            Payment
                                        </span>

                                        <small>
                                            Payment & confirmation
                                        </small>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </aside>


                    {{-- =====================================================
                        RIGHT CONTENT / ONLY THIS AREA SCROLLS
                    ====================================================== --}}
                    <main
                        id="membership-wizard-body"
                        class="satellite-modal__body"
                    >

                        {{-- =================================================
                            STEP 1 — PLAN & SATELLITE DETAILS
                        ================================================== --}}
                        <div
                            x-show="step === 1"
                            x-transition
                            class="satellite-step"
                        >
                            <div class="satellite-step__content satellite-step__content--wide">

                                <div class="satellite-step__heading">
                                    <span class="satellite-step__number-label">
                                        Step 01
                                    </span>

                                    <h3>
                                        Choose Your Membership
                                    </h3>

                                    <p>
                                        Select the Satellite membership type
                                        that best fits your needs.
                                    </p>
                                </div>


                                {{-- PLAN CARDS --}}
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

                                                <span class="satellite-plan__check">
                                                    &#10003;
                                                </span>

                                                <span
                                                    class="satellite-plan__icon"
                                                    style="
                                                        background: {{ $meta['color'] }}15;
                                                        color: {{ $meta['color'] }};
                                                    "
                                                >
                                                    {{ $meta['icon'] }}
                                                </span>

                                                <span
                                                    class="satellite-plan__label"
                                                    style="color: {{ $meta['color'] }}"
                                                >
                                                    {{ $meta['label'] }}
                                                </span>

                                                <h4>
                                                    {{ $plan }}
                                                </h4>

                                                <p>
                                                    @if ($plan === 'Bronze')
                                                        An affordable membership option
                                                        for essential protection and support.
                                                    @elseif ($plan === 'Silver')
                                                        Enhanced membership benefits
                                                        with broader protection and support.
                                                    @else
                                                        Premium membership with the highest
                                                        level of benefits and protection.
                                                    @endif
                                                </p>

                                                <div class="satellite-plan__divider"></div>

                                                <ul>
                                                    @if ($plan === 'Bronze')
                                                        <li>
                                                            &#10003; Basic membership benefits
                                                        </li>
                                                        <li>
                                                            &#10003; Member support
                                                        </li>
                                                        <li>
                                                            &#10003; Standard coverage
                                                        </li>
                                                    @elseif ($plan === 'Silver')
                                                        <li>
                                                            &#10003; Enhanced benefits
                                                        </li>
                                                        <li>
                                                            &#10003; Extended support
                                                        </li>
                                                        <li>
                                                            &#10003; Improved coverage
                                                        </li>
                                                    @else
                                                        <li>
                                                            &#10003; Premium benefits
                                                        </li>
                                                        <li>
                                                            &#10003; Priority support
                                                        </li>
                                                        <li>
                                                            &#10003; Maximum coverage
                                                        </li>
                                                    @endif
                                                </ul>

                                                <div class="satellite-plan__footer">
                                                    <span
                                                        class="satellite-plan__select"
                                                        style="
                                                            background: {{ $meta['color'] }}15;
                                                            color: {{ $meta['color'] }};
                                                        "
                                                    >
                                                        Select {{ $plan }}
                                                    </span>
                                                </div>

                                            </div>
                                        </label>

                                    @endforeach

                                </div>


                                @error('satellite_type')
                                    <div class="form-error form-error--box">
                                        {{ $message }}
                                    </div>
                                @enderror


                                {{-- SELECTION SUMMARY --}}
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

                                        <span>
                                            &#8369;{{ number_format($this->membershipAmount, 2) }}
                                        </span>

                                        <small>
                                            Valid for {{ $this->membershipValidity }} months
                                        </small>
                                    </div>

                                </div>


                                {{-- SATELLITE DETAILS --}}
                                <div
                                    x-show="$wire.satellite_type"
                                    x-transition
                                    class="satellite-details-card"
                                >

                                    <h4 class="satellite-details-card__title">
                                        Satellite Details
                                    </h4>

                                    <div class="satellite-details-card__grid">

                                        {{-- SATELLITE NAME --}}
                                        <div class="form-group">

                                            <label for="satellite-name">
                                                Satellite Name
                                                <span>*</span>
                                            </label>

                                            <input
                                                id="satellite-name"
                                                type="text"
                                                name="satellite_name"
                                                wire:model.live="satellite_name"
                                                placeholder="e.g. Poblacion Satellite Office"
                                            >

                                            @error('satellite_name')
                                                <p class="form-error">
                                                    {{ $message }}
                                                </p>
                                            @enderror

                                        </div>


                                        {{-- ADDRESS + MAP --}}
                                        <div class="form-group">

                                            <label for="satellite-address">
                                                Address
                                                <span>*</span>
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
                                                <p class="form-error">
                                                    {{ $message }}
                                                </p>
                                            @enderror


                                            {{-- LEAFLET MAP --}}
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

                                                    <button
                                                        id="satellite-leaflet-search-btn"
                                                        type="button"
                                                    >
                                                        Search
                                                    </button>

                                                </div>

                                                <div class="satellite-map-panel__map">
                                                    <div
                                                        id="satellite-map"
                                                        wire:ignore
                                                    ></div>
                                                </div>

                                            </div>

                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>


                        {{-- =================================================
                            STEP 2 — ACCOUNT CREDENTIALS
                        ================================================== --}}
                        <div
                            x-show="step === 2"
                            x-cloak
                            x-transition
                            class="satellite-step"
                        >

                            <div class="satellite-step__content satellite-step__content--account">

                                <div class="satellite-step__heading">

                                    <span class="satellite-step__number-label">
                                        Step 02
                                    </span>

                                    <h3>
                                        Create Your Account
                                    </h3>

                                    <p>
                                        Enter the login credentials for the new
                                        Satellite membership account.
                                    </p>

                                </div>


                                <div class="account-card">

                                    {{-- SELECTED PLAN RECAP --}}
                                    <div class="account-membership">

                                        <div>
                                            <p>
                                                Membership Selected
                                            </p>

                                            <strong x-text="$wire.satellite_type || 'Not selected'"></strong>

                                            <span>
                                                &#8369;{{ number_format($this->membershipAmount, 2) }}
                                                /
                                                {{ $this->membershipValidity }} months
                                            </span>
                                        </div>

                                        <button
                                            type="button"
                                            @click="step = 1"
                                        >
                                            Change
                                        </button>

                                    </div>


                                    {{-- ACCOUNT FORM --}}
                                    <div class="account-form">

                                        {{-- USERNAME --}}
                                        <div class="form-group">

                                            <label for="satellite-username">
                                                Username
                                                <span>*</span>
                                            </label>

                                            <input
                                                id="satellite-username"
                                                type="text"
                                                name="username"
                                                wire:model.live="username"
                                                autocomplete="username"
                                                placeholder="username123"
                                            >

                                            @error('username')
                                                <p class="form-error">
                                                    {{ $message }}
                                                </p>
                                            @enderror

                                        </div>


                                        {{-- EMAIL --}}
                                        <div class="form-group">

                                            <label for="satellite-email">
                                                Email Address
                                                <span>*</span>
                                            </label>

                                            <input
                                                id="satellite-email"
                                                type="email"
                                                name="email"
                                                wire:model.live="email"
                                                autocomplete="email"
                                                placeholder="you@example.com"
                                            >

                                            @error('email')
                                                <p class="form-error">
                                                    {{ $message }}
                                                </p>
                                            @enderror

                                        </div>


                                        {{-- PASSWORD --}}
                                        <div
                                            class="form-group"
                                            x-data="{ show: false }"
                                        >

                                            <label for="satellite-password">
                                                Password
                                                @if (! $isReapply)
                                                    <span>*</span>
                                                @endif
                                            </label>

                                            <div class="password-field">

                                                <input
                                                    id="satellite-password"
                                                    type="password"
                                                    name="password"
                                                    wire:model="password"
                                                    :type="show ? 'text' : 'password'"
                                                    autocomplete="new-password"
                                                    placeholder="{{ $isReapply ? 'Leave blank to keep current password' : 'Enter your password' }}"
                                                >

                                                <button
                                                    type="button"
                                                    @click="show = !show"
                                                    x-text="show ? 'Hide' : 'Show'"
                                                ></button>

                                            </div>

                                            @error('password')
                                                <p class="form-error">
                                                    {{ $message }}
                                                </p>
                                            @enderror

                                        </div>


                                        {{-- CONFIRM PASSWORD --}}
                                        <div
                                            class="form-group"
                                            x-data="{ show: false }"
                                        >

                                            <label for="satellite-password-confirmation">
                                                Confirm Password
                                                @if (! $isReapply)
                                                    <span>*</span>
                                                @endif
                                            </label>

                                            <div class="password-field">

                                                <input
                                                    id="satellite-password-confirmation"
                                                    type="password"
                                                    name="password_confirmation"
                                                    wire:model="password_confirmation"
                                                    :type="show ? 'text' : 'password'"
                                                    autocomplete="new-password"
                                                    placeholder="{{ $isReapply ? 'Leave blank to keep current password' : 'Confirm your password' }}"
                                                >

                                                <button
                                                    type="button"
                                                    @click="show = !show"
                                                    x-text="show ? 'Hide' : 'Show'"
                                                ></button>

                                            </div>

                                            @error('password_confirmation')
                                                <p class="form-error">
                                                    {{ $message }}
                                                </p>
                                            @enderror

                                        </div>

                                    </div>


                                    {{-- MEMBER INFORMATION --}}
                                    @if ($member)

                                        <div class="member-info">

                                            <p>
                                                Account Name
                                            </p>

                                            <strong>
                                                {{ $member->firstname }}
                                                {{ $member->middlename }}
                                                {{ $member->lastname }}
                                            </strong>

                                            <span>
                                                This information is copied from
                                                the selected existing member.
                                            </span>

                                        </div>

                                    @endif


                                    {{-- INFORMATION MESSAGE --}}
                                    <div class="info-message">
                                        Your account credentials will be used to
                                        access your Satellite membership account.
                                        Please keep your password secure.
                                    </div>

                                </div>

                            </div>
                        </div>


                        {{-- =================================================
                            STEP 3 — PAYMENT
                        ================================================== --}}
                        <div
                            x-show="step === 3"
                            x-cloak
                            x-transition
                            class="satellite-step"
                        >

                            <div class="satellite-step__content satellite-step__content--payment">

                                <div class="satellite-step__heading">

                                    <span class="satellite-step__number-label">
                                        Step 03
                                    </span>

                                    <h3>
                                        Choose Payment Method
                                    </h3>

                                    <p>
                                        Select your preferred payment method.
                                    </p>

                                </div>


                                <div class="payment-layout">

                                    {{-- =================================================
                                        PAYMENT CARD
                                    ================================================== --}}
                                    <div class="payment-card">

                                        <h4>
                                            Payment Method
                                        </h4>


                                        {{-- PAYMENT METHODS --}}
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

                                                    <div
                                                        class="
                                                            payment-method__content
                                                            {{ $pm['method'] === 'Cash'
                                                                ? 'payment-method__content--cash'
                                                                : '' }}
                                                        "
                                                    >

                                                        <span
                                                            class="
                                                                payment-method__icon
                                                                {{
                                                                    $pm['method'] === 'GCash'
                                                                        ? 'payment-method__icon--gcash'
                                                                        : (
                                                                            $pm['method'] === 'Maya'
                                                                                ? 'payment-method__icon--maya'
                                                                                : (
                                                                                    $pm['method'] === 'Bank Transfer'
                                                                                        ? 'payment-method__icon--bank'
                                                                                        : ''
                                                                                )
                                                                        )
                                                                }}
                                                            "
                                                        >
                                                            {{ $pm['icon'] }}
                                                        </span>

                                                        <div>
                                                            <p>
                                                                {{ $pm['method'] }}
                                                            </p>

                                                            <span>
                                                                {{ $pm['desc'] }}
                                                            </span>
                                                        </div>

                                                    </div>

                                                </label>

                                            @endforeach

                                        </div>


                                        @error('payment_method')
                                            <p class="form-error">
                                                {{ $message }}
                                            </p>
                                        @enderror


                                        {{-- ONLINE PAYMENT --}}
                                        @if ($this->paymentIsOnline)

                                            <div class="reference-card">

                                                <label for="satellite-reference">
                                                    Payment Reference Code
                                                    <span>*</span>
                                                </label>

                                                <input
                                                    id="satellite-reference"
                                                    type="text"
                                                    name="reference_code"
                                                    wire:model="reference_code"
                                                    placeholder="Enter payment reference code"
                                                >

                                                @error('reference_code')
                                                    <p class="form-error">
                                                        {{ $message }}
                                                    </p>
                                                @enderror

                                                <small>
                                                    Enter the reference number
                                                    provided by your payment transaction.
                                                </small>

                                            </div>

                                        @endif


                                        {{-- CASH PAYMENT --}}
                                        @if ($payment_method === 'Cash')

                                            <div class="cash-message">

                                                <strong>
                                                    Cash Payment
                                                </strong>

                                                <p>
                                                    Please pay the membership amount
                                                    at the authorized office or
                                                    designated payment location.
                                                </p>

                                                <small>
                                                    This application will be recorded
                                                    as Walk-in (W).
                                                </small>

                                            </div>

                                        @endif

                                    </div>


                                    {{-- =================================================
                                        APPLICATION SUMMARY
                                    ================================================== --}}
                                    <div class="summary-card">

                                        <h4>
                                            Application Summary
                                        </h4>

                                        <div class="summary-list">

                                            {{-- MEMBERSHIP --}}
                                            <div class="summary-item">

                                                <p>
                                                    Membership
                                                </p>

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


                                            {{-- MEMBER --}}
                                            @if ($member)

                                                <div class="summary-item">

                                                    <p>
                                                        Member
                                                    </p>

                                                    <strong>
                                                        {{ $member->firstname }}
                                                        {{ $member->middlename }}
                                                        {{ $member->lastname }}
                                                    </strong>

                                                </div>

                                            @endif


                                            {{-- EMAIL --}}
                                            <div class="summary-item">

                                                <p>
                                                    Account Email
                                                </p>

                                                <strong class="summary-email">
                                                    {{ $email ?: 'No email provided' }}
                                                </strong>

                                            </div>


                                            {{-- UPLINE CODE --}}
                                            <div class="form-group">

                                                <label for="satellite-uplineCode">
                                                    Upline Code
                                                    <span>*</span>
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
                                                    <p class="form-error">
                                                        {{ $message }}
                                                    </p>
                                                @enderror

                                            </div>


                                            {{-- PAYMENT SUMMARY --}}
                                            <div
                                                x-show="$wire.payment_method"
                                                class="summary-payment"
                                            >

                                                <p>
                                                    Payment Method
                                                </p>

                                                <strong x-text="$wire.payment_method"></strong>

                                                <small
                                                    :class="$wire.paymentIsOnline
                                                        ? 'summary-payment__online'
                                                        : 'summary-payment__cash'"
                                                >
                                                    Activation Type:

                                                    <span
                                                        x-text="$wire.paymentIsOnline
                                                            ? 'O — Online'
                                                            : 'W — Walk-in'"
                                                    ></span>
                                                </small>

                                                <template
                                                    x-if="$wire.paymentIsOnline && $wire.reference_code"
                                                >
                                                    <small>
                                                        Reference:

                                                        <span
                                                            x-text="$wire.reference_code"
                                                        ></span>
                                                    </small>
                                                </template>

                                            </div>

                                        </div>


                                        {{-- TERMS --}}
                                        <div class="terms">

                                            <label>

                                                <input
                                                    type="checkbox"
                                                    wire:model.live="agree"
                                                >

                                                <span>
                                                    I confirm that the information
                                                    provided is accurate and complete,
                                                    and I agree to the membership terms
                                                    and conditions.
                                                </span>

                                            </label>

                                            @error('agree')
                                                <p class="form-error">
                                                    {{ $message }}
                                                </p>
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

                                    <span class="payment-instruction__icon">
                                        i
                                    </span>

                                    <div>

                                        <strong>
                                            Payment Instructions
                                        </strong>

                                        @if ($payment_method === 'Cash')

                                            <p>
                                                Please settle the membership amount
                                                through the authorized office.
                                                Your application will be recorded as
                                                a Walk-in (W) activation and will
                                                remain pending until verified.
                                            </p>

                                        @else

                                            <p>
                                                Make your payment using the selected
                                                payment method and provide the
                                                transaction reference code. Your
                                                application will remain pending until
                                                payment is verified.
                                            </p>

                                        @endif

                                    </div>

                                </div>

                            </div>
                        </div>

                    </main>

                </div>


                {{-- =========================================================
                    FOOTER
                    STAYS FIXED — NOT PART OF SCROLLABLE AREA
                ========================================================== --}}
                <footer class="satellite-modal__footer">

                    <div class="satellite-modal__footer-left">




                        {{-- CANCEL --}}
                        <button
                            type="button"
                            wire:click="close"
                            class="wizard-button wizard-button--cancel"
                        >
                            Cancel
                        </button>

                    </div>


                    <div class="satellite-modal__footer-right">

                        {{-- BACK --}}
                        <button
                            type="button"
                            x-show="step > 1"
                            x-cloak
                            @click="previousStep()"
                            class="wizard-button wizard-button--back"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15 19l-7-7 7-7"
                                />
                            </svg>

                            Back
                        </button>

                        {{-- NEXT STEP --}}
                        <button
                            type="button"
                            x-show="step < 3"
                            @click="nextStep()"
                            wire:loading.attr="disabled"
                            wire:target="validateStep"
                            class="wizard-button wizard-button--next"
                        >
                            Next Step

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 5l7 7-7 7"
                                />
                            </svg>
                        </button>


                        {{-- SUBMIT --}}
                        <button
                            type="button"
                            wire:click="validateAndSubmit"
                            wire:loading.attr="disabled"
                            wire:target="validateAndSubmit"
                            x-show="step === 3"
                            x-cloak
                            class="wizard-button wizard-button--submit"
                        >

                            <span
                                wire:loading.remove
                                wire:target="validateAndSubmit"
                            >
                                Submit Application
                            </span>

                            <span
                                wire:loading.flex
                                wire:target="validateAndSubmit"
                                class="submit-loading"
                            >

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
</div>


{{-- =========================================================
    LEAFLET MAP WIRING
========================================================= --}}
<script>
    let satelliteMap = null;
    let satelliteMarker = null;

    document.addEventListener('livewire:initialized', () => {

        {{-- =====================================================
            DESTROY MAP
        ====================================================== --}}
        Livewire.on('satellite-destroy-map', () => {

            if (satelliteMap) {
                satelliteMap.remove();
                satelliteMap = null;
                satelliteMarker = null;
            }

        });


        {{-- =====================================================
            MAP OPENED
        ====================================================== --}}
        Livewire.on('satellite-map-opened', (data) => {

            setTimeout(() => {

                const lat = data.latitude ?? 7.442294461028405;
                const lng = data.longitude ?? 125.81370503321095;

                if (satelliteMap) {
                    satelliteMap.remove();
                    satelliteMap = null;
                    satelliteMarker = null;
                }

                satelliteMap = L.map('satellite-map').setView(
                    [lat, lng],
                    15
                );


                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        attribution: '&copy; OpenStreetMap'
                    }
                ).addTo(satelliteMap);


                if (
                    data.latitude != null &&
                    data.longitude != null
                ) {

                    satelliteMarker = L.marker([
                        lat,
                        lng
                    ]).addTo(satelliteMap);

                    satelliteMap.setView(
                        [lat, lng],
                        16
                    );

                }


                const searchInput = document.getElementById(
                    'satellite-leaflet-search-input'
                );

                const searchBtn = document.getElementById(
                    'satellite-leaflet-search-btn'
                );


                {{-- =================================================
                    SET PIN
                ================================================== --}}
                const setPin = (lat, lng) => {

                    const center = [
                        parseFloat(lat),
                        parseFloat(lng)
                    ];

                    satelliteMap.setView(
                        center,
                        16
                    );

                    if (satelliteMarker) {

                        satelliteMarker.setLatLng(
                            center
                        );

                    } else {

                        satelliteMarker = L.marker(
                            center
                        ).addTo(satelliteMap);

                    }

                    Livewire.dispatch(
                        'satellite-set-coordinates',
                        {
                            lat: center[0],
                            lng: center[1]
                        }
                    );

                };


                {{-- =================================================
                    LOCATION SEARCH
                ================================================== --}}
                const performSearch = () => {

                    if (!searchInput) return;

                    const query = searchInput.value.trim();

                    if (!query) return;

                    fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`
                    )
                        .then((response) => response.json())
                        .then((results) => {

                            if (
                                results &&
                                results.length > 0
                            ) {

                                setPin(
                                    results[0].lat,
                                    results[0].lon
                                );

                            } else {

                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Location Not Found',
                                    text: 'Please try another location.',
                                    confirmButtonColor: '#0B2A5B'
                                });

                            }

                        })
                        .catch(() => {

                            Swal.fire({
                                icon: 'error',
                                title: 'Search Error',
                                text: 'Unable to search for this location. Please try again.',
                                confirmButtonColor: '#0B2A5B'
                            });

                        });

                };


                {{-- SEARCH INPUT --}}
                if (searchInput) {

                    searchInput.addEventListener(
                        'keypress',
                        (e) => {

                            if (e.key === 'Enter') {

                                e.preventDefault();

                                performSearch();

                            }

                        }
                    );

                }


                {{-- SEARCH BUTTON --}}
                if (searchBtn) {

                    searchBtn.addEventListener(
                        'click',
                        (e) => {

                            e.preventDefault();

                            performSearch();

                        }
                    );

                }


                {{-- MAP CLICK --}}
                satelliteMap.on(
                    'click',
                    (e) => setPin(
                        e.latlng.lat,
                        e.latlng.lng
                    )
                );


                {{-- INVALIDATE MAP SIZE --}}
                setTimeout(
                    () => satelliteMap?.invalidateSize(),
                    100
                );

            }, 300);

        });

        Alpine.data('membershipWizard', () => ({

        step: 1,

        async nextStep() {

            if (this.step >= 3) {
                return;
            }

            try {

                const result =
                    await this.$wire.validateStep(this.step);

                if (!result.valid) {

                    Swal.fire({

                        icon: 'warning',

                        title: 'Please Check Your Information',

                        html:
                            '<div style="text-align:left;">' +
                                '<ul style="margin:0;padding-left:20px;">' +
                                    result.errors
                                        .map(error =>
                                            '<li>' + error + '</li>'
                                        )
                                        .join('') +
                                '</ul>' +
                            '</div>',

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

                    text:
                        'Unable to validate the form. Please try again.',

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

                const body =
                    document.getElementById(
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

    }));


        {{-- =====================================================
            VALIDATION ERROR
        ====================================================== --}}
        Livewire.on('satellite-validation-error', (event) => {

            console.log(
                'Satellite validation errors:',
                event
            );

            const errors = event.errors ?? [];

            if (!errors.length) {
                return;
            }

            Swal.fire({

                icon: 'warning',

                title: 'Please Check Your Information',

                html: `
                    <div style="text-align: left;">
                        <ul style="
                            margin: 0;
                            padding-left: 22px;
                            line-height: 1.8;
                        ">
                            ${errors
                                .map(error => `<li>${error}</li>`)
                                .join('')}
                        </ul>
                    </div>
                `,

                confirmButtonText: 'OK',

                confirmButtonColor: '#0B2A5B'

            });

        });


        {{-- =====================================================
            SUBMIT CONFIRMATION — NEW APPLICATION (Members table)
            Typed phrase: SUBMIT APPLICATION
        ====================================================== --}}
        Livewire.on('satellite-submit-confirmation', () => {

            Swal.fire({

                icon: 'question',

                title: 'Confirm Application Submission',

                html: `
                    <div style="text-align: left; line-height: 1.7;">

                        <p>
                            Please review all information carefully before
                            submitting your application.
                        </p>

                        <p style="margin-top: 12px;">
                            Once submitted, the application will be processed.
                            Make sure all membership, account, payment, and
                            upline information is correct.
                        </p>

                        <div style="
                            margin-top: 18px;
                            padding: 14px;
                            background: #F3F4F6;
                            border-radius: 8px;
                        ">
                            <label
                                for="confirm-submit-text"
                                style="
                                    display: block;
                                    font-weight: 600;
                                    margin-bottom: 8px;
                                "
                            >
                                Type <strong
                                style="color: #0B2A5B;">SUBMIT APPLICATION</strong> to confirm:
                            </label>

                            <input
                                id="confirm-submit-text"
                                type="text"
                                class="swal2-input"
                                autocomplete="off"
                                style="
                                    width: 100%;
                                    margin: 0;
                                    box-sizing: border-box;
                                    color: #0B2A5B;
                                "
                            >

                            <small
                                id="confirm-submit-hint"
                                style="
                                    display: block;
                                    margin-top: 8px;
                                    color: #0B2A5B;
                                "
                            >
                                The confirmation button will be enabled once
                                the phrase is entered correctly.
                            </small>
                        </div>

                    </div>
                `,

                showCancelButton: true,

                confirmButtonText: 'Yes, Submit Application',

                cancelButtonText: 'Review Again',

                confirmButtonColor: '#0B2A5B',

                cancelButtonColor: '#D79B10',

                reverseButtons: true,

                allowOutsideClick: false,

                allowEscapeKey: false,

                focusConfirm: false,

                didOpen: () => {

                    const input =
                        document.getElementById('confirm-submit-text');

                    const confirmButton =
                        Swal.getConfirmButton();

                    const hint =
                        document.getElementById('confirm-submit-hint');

                    // Disable submit button initially
                    confirmButton.disabled = true;

                    input.focus();

                    input.addEventListener('input', () => {

                        const value =
                            input.value.trim();

                        if (value === 'SUBMIT APPLICATION') {

                            confirmButton.disabled = false;

                            confirmButton.style.opacity = '1';

                            hint.textContent =
                                'Confirmation phrase accepted.';

                            hint.style.color = '#0B2A5B';

                        } else {

                            confirmButton.disabled = true;

                            confirmButton.style.opacity = '0.5';

                            hint.textContent =
                                'Please type exactly: SUBMIT APPLICATION';

                            hint.style.color = '#6B7280';

                        }

                    });

                }

            }).then((result) => {

                if (result.isConfirmed) {

                    Livewire.dispatch(
                        'satellite-confirm-submit'
                    );

                }

            });
        });


        {{-- =====================================================
            RESUBMIT CONFIRMATION — REAPPLICATION (Satellite table)
            Typed phrase: RESUBMIT APPLICATION
        ====================================================== --}}
        Livewire.on('satellite-reapply-confirmation', () => {

            Swal.fire({

                icon: 'question',

                title: 'Confirm Reapplication Submission',

                html: `
                    <div style="text-align: left; line-height: 1.7;">

                        <p>
                            This satellite account was previously <strong>rejected</strong>.
                            Resubmitting will <strong>update the SAME satellite user</strong>
                            (no new account) and file a fresh activation for approval.
                        </p>

                        <p style="margin-top: 12px;">
                            Make sure all membership, account, payment, and upline
                            information is correct before resubmitting.
                        </p>

                        <div style="
                            margin-top: 18px;
                            padding: 14px;
                            background: #F3F4F6;
                            border-radius: 8px;
                        ">
                            <label
                                for="confirm-reapply-text"
                                style="
                                    display: block;
                                    font-weight: 600;
                                    margin-bottom: 8px;
                                "
                            >
                                Type <strong
                                style="color: #0B2A5B;">RESUBMIT APPLICATION</strong> to confirm:
                            </label>

                            <input
                                id="confirm-reapply-text"
                                type="text"
                                class="swal2-input"
                                autocomplete="off"
                                style="
                                    width: 100%;
                                    margin: 0;
                                    box-sizing: border-box;
                                    color: #0B2A5B;
                                "
                            >

                            <small
                                id="confirm-reapply-hint"
                                style="
                                    display: block;
                                    margin-top: 8px;
                                    color: #0B2A5B;
                                "
                            >
                                The confirmation button will be enabled once
                                the phrase is entered correctly.
                            </small>
                        </div>

                    </div>
                `,

                showCancelButton: true,

                confirmButtonText: 'Yes, Resubmit Application',

                cancelButtonText: 'Review Again',

                confirmButtonColor: '#0B2A5B',

                cancelButtonColor: '#D79B10',

                reverseButtons: true,

                allowOutsideClick: false,

                allowEscapeKey: false,

                focusConfirm: false,

                didOpen: () => {

                    const input =
                        document.getElementById('confirm-reapply-text');

                    const confirmButton =
                        Swal.getConfirmButton();

                    const hint =
                        document.getElementById('confirm-reapply-hint');

                    // Disable submit button initially
                    confirmButton.disabled = true;

                    input.focus();

                    input.addEventListener('input', () => {

                        const value =
                            input.value.trim();

                        if (value === 'RESUBMIT APPLICATION') {

                            confirmButton.disabled = false;

                            confirmButton.style.opacity = '1';

                            hint.textContent =
                                'Confirmation phrase accepted.';

                            hint.style.color = '#0B2A5B';

                        } else {

                            confirmButton.disabled = true;

                            confirmButton.style.opacity = '0.5';

                            hint.textContent =
                                'Please type exactly: RESUBMIT APPLICATION';

                            hint.style.color = '#6B7280';

                        }

                    });

                }

            }).then((result) => {

                if (result.isConfirmed) {

                    Livewire.dispatch(
                        'satellite-confirm-submit'
                    );

                }

            });
        });

    });
</script>
