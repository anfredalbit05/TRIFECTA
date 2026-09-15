<div>
    @if ($open)
        <div
            x-data="{
                step: 1,

                async nextStep() {
                    if (this.step >= 3) return;

                    try {
                        await $wire.validateStep(this.step);
                        this.step++;
                        this.scrollTop();
                    } catch (error) {
                        console.error(error);
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
                        const body = document.getElementById('membership-wizard-body');

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

                {{-- HEADER --}}
                <div class="satellite-modal__header">
                    <div class="satellite-modal__glow satellite-modal__glow--right"></div>
                    <div class="satellite-modal__glow satellite-modal__glow--left"></div>

                    <div class="satellite-modal__header-content">
                        <div>
                            <div class="satellite-modal__eyebrow">
                                <div class="satellite-modal__header-icon">
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
                                </div>

                                <span>Membership</span>
                            </div>

                            <h2 class="satellite-modal__title">
                                Membership Application
                            </h2>

                            <p class="satellite-modal__subtitle">
                                True Life Solidaritas &bull; Vitae Auxilium
                            </p>
                        </div>

                        <button
                            wire:click="close"
                            type="button"
                            class="satellite-modal__close"
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

                    {{-- STEPPER --}}
                    <div class="satellite-stepper">
                        <div class="satellite-stepper__line"></div>

                        <div
                            class="satellite-stepper__progress"
                            :style="`
                                width: ${
                                    step === 1
                                        ? '0%'
                                        : step === 2
                                            ? '33.33%'
                                            : '66.67%'
                                }
                            `"
                        ></div>

                        <div class="satellite-stepper__items">
                            @foreach ([
                                1 => 'Plan',
                                2 => 'Account',
                                3 => 'Payment'
                            ] as $number => $label)

                                <div class="satellite-stepper__item">
                                    <div
                                        class="satellite-stepper__number"
                                        :class="
                                            step >= {{ $number }}
                                                ? 'satellite-stepper__number--active'
                                                : ''
                                        "
                                    >
                                        {{ $number }}
                                    </div>

                                    <span
                                        class="satellite-stepper__label"
                                        :class="
                                            step >= {{ $number }}
                                                ? 'satellite-stepper__label--active'
                                                : ''
                                        "
                                    >
                                        {{ $label }}
                                    </span>
                                </div>

                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- BODY --}}
                <div
                    id="membership-wizard-body"
                    class="satellite-modal__body"
                >

                    {{-- STEP 1 --}}
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
                                    Select the Satellite membership type that
                                    best fits your needs.
                                </p>
                            </div>

                            <div class="satellite-plans">

                                @foreach ([
                                    'Bronze' => [
                                        'label' => 'Basic',
                                        'color' => '#CD7F32',
                                        'icon' => '♢',
                                        'description' => 'An affordable membership option for essential protection and support.',
                                        'benefits' => [
                                            'Basic membership benefits',
                                            'Member support',
                                            'Standard coverage'
                                        ]
                                    ],

                                    'Silver' => [
                                        'label' => 'Popular',
                                        'color' => '#64748B',
                                        'icon' => '◇',
                                        'description' => 'Enhanced membership benefits with broader protection and support.',
                                        'benefits' => [
                                            'Enhanced benefits',
                                            'Extended support',
                                            'Improved coverage'
                                        ]
                                    ],

                                    'Gold' => [
                                        'label' => 'Premium',
                                        'color' => '#D4A017',
                                        'icon' => '✦',
                                        'description' => 'Premium membership with the highest level of benefits and protection.',
                                        'benefits' => [
                                            'Premium benefits',
                                            'Priority support',
                                            'Maximum coverage'
                                        ]
                                    ]
                                ] as $plan => $data)

                                    <label class="satellite-plan">
                                        <input
                                            type="radio"
                                            wire:model.live="satellite_type"
                                            value="{{ $plan }}"
                                            class="satellite-plan__input"
                                        >

                                        <div
                                            class="satellite-plan__card"
                                            style="--plan-color: {{ $data['color'] }}"
                                        >
                                            <div class="satellite-plan__check">
                                                ✓
                                            </div>

                                            <div
                                                class="satellite-plan__icon"
                                                style="
                                                    background: {{ $data['color'] }}15;
                                                    color: {{ $data['color'] }};
                                                "
                                            >
                                                {{ $data['icon'] }}
                                            </div>

                                            <span
                                                class="satellite-plan__label"
                                                style="color: {{ $data['color'] }}"
                                            >
                                                {{ $data['label'] }}
                                            </span>

                                            <h4>
                                                {{ $plan }}
                                            </h4>

                                            <p>
                                                {{ $data['description'] }}
                                            </p>

                                            <div class="satellite-plan__divider"></div>

                                            <ul>
                                                @foreach ($data['benefits'] as $benefit)
                                                    <li>
                                                        ✓ {{ $benefit }}
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <div class="satellite-plan__footer">
                                                <div
                                                    class="satellite-plan__select"
                                                    style="
                                                        background: {{ $data['color'] }}15;
                                                        color: {{ $data['color'] }};
                                                    "
                                                >
                                                    Select {{ $plan }}
                                                </div>
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
                                        ₱{{ number_format($this->membershipAmount, 2) }}
                                    </span>

                                    <small>
                                        Valid for {{ $this->membershipValidity }} months
                                    </small>
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

                                <div class="account-membership">
                                    <div>
                                        <p>Membership Selected</p>

                                        <strong
                                            x-text="$wire.satellite_type || 'Not selected'"
                                        ></strong>

                                        <span>
                                            ₱{{ number_format($this->membershipAmount, 2) }}
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

                                <div class="account-form">

                                    {{-- EMAIL --}}
                                    <div class="form-group">
                                        <label>
                                            Email Address
                                            <span>*</span>
                                        </label>

                                        <input
                                            wire:model.live="email"
                                            type="email"
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
                                        <label>
                                            Password
                                            <span>*</span>
                                        </label>

                                        <div class="password-field">
                                            <input
                                                wire:model="password"
                                                :type="show ? 'text' : 'password'"
                                                autocomplete="new-password"
                                                placeholder="Enter your password"
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
                                        <label>
                                            Confirm Password
                                            <span>*</span>
                                        </label>

                                        <div class="password-field">
                                            <input
                                                wire:model="password_confirmation"
                                                :type="show ? 'text' : 'password'"
                                                autocomplete="new-password"
                                                placeholder="Confirm your password"
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

                                @if ($member)
                                    <div class="member-info">
                                        <p>Account Name</p>

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

                                <div class="info-message">
                                    Your account credentials will be used to
                                    access your Satellite membership account.
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

                                {{-- PAYMENT METHODS --}}
                                <div class="payment-card">

                                    <h4>
                                        Payment Method
                                    </h4>

                                    <div class="payment-methods">

                                        {{-- CASH --}}
                                        <label class="payment-method">
                                            <input
                                                type="radio"
                                                wire:model.live="payment_method"
                                                value="Cash"
                                                class="payment-method__input"
                                            >

                                            <div class="payment-method__content payment-method__content--cash">
                                                <div class="payment-method__icon">
                                                    ₱
                                                </div>

                                                <div>
                                                    <p>Cash</p>

                                                    <span>
                                                        Pay directly at the
                                                        authorized office.
                                                    </span>
                                                </div>
                                            </div>
                                        </label>

                                        {{-- GCASH --}}
                                        @if ($this->gcashEnabled)
                                            <label class="payment-method">
                                                <input
                                                    type="radio"
                                                    wire:model.live="payment_method"
                                                    value="GCash"
                                                    class="payment-method__input"
                                                >

                                                <div class="payment-method__content">
                                                    <div class="payment-method__icon payment-method__icon--gcash">
                                                        G
                                                    </div>

                                                    <div>
                                                        <p>GCash</p>

                                                        <span>
                                                            Pay using your
                                                            GCash account.
                                                        </span>
                                                    </div>
                                                </div>
                                            </label>
                                        @endif

                                        {{-- MAYA --}}
                                        @if ($this->paymayaEnabled)
                                            <label class="payment-method">
                                                <input
                                                    type="radio"
                                                    wire:model.live="payment_method"
                                                    value="Maya"
                                                    class="payment-method__input"
                                                >

                                                <div class="payment-method__content">
                                                    <div class="payment-method__icon payment-method__icon--maya">
                                                        M
                                                    </div>

                                                    <div>
                                                        <p>Maya</p>

                                                        <span>
                                                            Pay using your
                                                            Maya account.
                                                        </span>
                                                    </div>
                                                </div>
                                            </label>
                                        @endif

                                        {{-- BANK --}}
                                        @if ($this->bankPaymentEnabled)
                                            <label class="payment-method">
                                                <input
                                                    type="radio"
                                                    wire:model.live="payment_method"
                                                    value="Bank Transfer"
                                                    class="payment-method__input"
                                                >

                                                <div class="payment-method__content">
                                                    <div class="payment-method__icon payment-method__icon--bank">
                                                        B
                                                    </div>

                                                    <div>
                                                        <p>Bank Transfer</p>

                                                        <span>
                                                            Transfer directly
                                                            to the organization's
                                                            bank account.
                                                        </span>
                                                    </div>
                                                </div>
                                            </label>
                                        @endif

                                    </div>

                                    @error('payment_method')
                                        <p class="form-error">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                    {{-- ONLINE REFERENCE --}}
                                    @if (
                                        $payment_method === 'GCash' ||
                                        $payment_method === 'Maya' ||
                                        $payment_method === 'Bank Transfer'
                                    )
                                        <div class="reference-card">
                                            <label>
                                                Payment Reference Code
                                                <span>*</span>
                                            </label>

                                            <input
                                                wire:model="reference_code"
                                                type="text"
                                                placeholder="Enter payment reference code"
                                            >

                                            @error('reference_code')
                                                <p class="form-error">
                                                    {{ $message }}
                                                </p>
                                            @enderror

                                            <small>
                                                Enter the reference number
                                                provided by your payment
                                                transaction.
                                            </small>
                                        </div>
                                    @endif

                                    {{-- CASH --}}
                                    @if ($payment_method === 'Cash')
                                        <div class="cash-message">
                                            <strong>
                                                Cash Payment
                                            </strong>

                                            <p>
                                                Please pay the membership
                                                amount at the authorized office
                                                or designated payment location.
                                            </p>

                                            <small>
                                                This application will be
                                                recorded as Walk-in (W).
                                            </small>
                                        </div>
                                    @endif

                                </div>

                                {{-- SUMMARY --}}
                                <div class="summary-card">

                                    <h4>
                                        Application Summary
                                    </h4>

                                    <div class="summary-list">

                                        <div class="summary-item">
                                            <p>Membership</p>

                                            <div>
                                                <strong
                                                    x-text="$wire.satellite_type"
                                                ></strong>

                                                <strong>
                                                    ₱{{ number_format($this->membershipAmount, 2) }}
                                                </strong>
                                            </div>

                                            <small>
                                                Valid for
                                                {{ $this->membershipValidity }}
                                                months
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

                                        <div
                                            x-show="$wire.payment_method"
                                            class="summary-payment"
                                        >
                                            <p>Payment Method</p>

                                            <strong
                                                x-text="$wire.payment_method"
                                            ></strong>

                                            <template
                                                x-if="$wire.payment_method === 'Cash'"
                                            >
                                                <small class="summary-payment__cash">
                                                    Activation Type:
                                                    W — Walk-in
                                                </small>
                                            </template>

                                            <template
                                                x-if="
                                                    $wire.payment_method === 'GCash' ||
                                                    $wire.payment_method === 'Maya' ||
                                                    $wire.payment_method === 'Bank Transfer'
                                                "
                                            >
                                                <small class="summary-payment__online">
                                                    Activation Type:
                                                    O — Online
                                                </small>
                                            </template>

                                            <template
                                                x-if="
                                                    $wire.payment_method !== 'Cash' &&
                                                    $wire.reference_code
                                                "
                                            >
                                                <small>
                                                    Reference:
                                                    <span x-text="$wire.reference_code"></span>
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
                                                and I agree to the membership
                                                terms and conditions.
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
                                <div class="payment-instruction__icon">
                                    i
                                </div>

                                <div>
                                    <strong>
                                        Payment Instructions
                                    </strong>

                                    @if ($payment_method === 'Cash')
                                        <p>
                                            Please settle the membership amount
                                            through the authorized office.
                                            Your application will be recorded
                                            as a Walk-in (W) activation and
                                            will remain pending until verified.
                                        </p>
                                    @else
                                        <p>
                                            Make your payment using the selected
                                            payment method and provide the
                                            transaction reference code. Your
                                            application will remain pending
                                            until payment is verified.
                                        </p>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="satellite-modal__footer">

                    <div class="satellite-modal__footer-left">

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

                        <button
                            wire:click="close"
                            type="button"
                            class="wizard-button wizard-button--cancel"
                        >
                            Cancel
                        </button>

                    </div>

                    <div class="satellite-modal__footer-right">

                        {{-- NEXT --}}
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
                            wire:click="submit"
                            wire:loading.attr="disabled"
                            wire:target="submit"
                            type="button"
                            x-show="step === 3"
                            x-cloak
                            :disabled="!$wire.agree"
                            class="wizard-button wizard-button--submit"
                        >
                            <span
                                wire:loading.remove
                                wire:target="submit"
                            >
                                Submit Application
                            </span>

                            <span
                                wire:loading.flex
                                wire:target="submit"
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
                </div>

            </div>
        </div>
    @endif
</div>
