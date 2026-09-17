<?php

namespace App\Livewire\AdminPages\Masterlist\MembersComponents;

use App\Models\Activation;
use App\Models\Satellite;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class SatelliteModal extends Component
{
    /*
    |--------------------------------------------------------------------------
    | Component State
    |--------------------------------------------------------------------------
    */

    public bool $open = false;

    /*
     * Reapply mode — a REJECTED satellite user (RoleID 8) is being resubmitted.
     * The wizard opens pre-filled with THEIR OWN account info (username/email)
     * and their last application's satellite details; submission UPDATES this
     * same satellite user (no new account) and files a fresh Pending activation.
     */
    public bool $isReapply = false;

    public ?int $UserID = null;

    public ?User $member = null;

    public string $selectedMemberReferralCode = '';

    // Step 1 — Plan & satellite details
    public string $satellite_type = '';

    public string $satellite_name = '';

    public string $address = '';

    public ?float $latitude = null;

    public ?float $longitude = null;

    public bool $showMapModal = false;

    // Step 2 — Account credentials
    public string $username = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    // Step 3 — Payment
    public string $payment_method = '';

    public string $reference_code = '';

    public string $uplineCode = '';

    public bool $agree = false;

    /*
    |--------------------------------------------------------------------------
    | Settings IDs — reference by SettingsID (PK), never by name.
    | Renaming a setting's `name` value will never break these lookups.
    | 5 = membership_validity_months | 6/7/8 = satellite coverage amounts
    | 25/26/27 = payment gateway toggles
    |--------------------------------------------------------------------------
    */
    private const SETTING_ID_MEMBERSHIP_VALIDITY_MONTHS = 5;

    private const SETTING_ID_BRONZE_COVERAGE = 6;

    private const SETTING_ID_SILVER_COVERAGE = 7;

    private const SETTING_ID_GOLD_COVERAGE = 8;

    private const SETTING_ID_GCASH_ENABLED = 25;

    private const SETTING_ID_BANK_ENABLED = 26;

    private const SETTING_ID_PAYMAYA_ENABLED = 27;

    // Satellite membership tiers
    private const TIERS = ['Bronze', 'Silver', 'Gold'];

    // Reverse map: satellites.satellite_typeID → tier name
    private const TIER_BY_SATELLITE_TYPE_ID = [6 => 'Bronze', 7 => 'Silver', 8 => 'Gold'];

    // Step 1 plan card metadata (keyed by tier)
    private const PLAN_META = [
        'Bronze' => ['label' => 'Basic', 'color' => '#CD7F32', 'icon' => '♢'],
        'Silver' => ['label' => 'Popular', 'color' => '#64748B', 'icon' => '◇'],
        'Gold' => ['label' => 'Premium', 'color' => '#D4A017', 'icon' => '✦'],
    ];

    // Payment methods (value => [icon letter, description, online?])
    private const PAYMENT_METHODS = [
        'Cash' => ['icon' => '₱', 'desc' => 'Pay directly at the authorized office.', 'online' => false],
        'GCash' => ['icon' => 'G', 'desc' => 'Pay using your GCash account.', 'online' => true],
        'Maya' => ['icon' => 'M', 'desc' => 'Pay using your Maya account.', 'online' => true],
        'Bank Transfer' => ['icon' => 'B', 'desc' => "Transfer directly to the organization's bank account.", 'online' => true],
    ];

    private const MAP_DEFAULTS = ['lat' => 7.442294461028405, 'lng' => 125.81370503321095];

    /*
    |--------------------------------------------------------------------------
    | Lifecycle
    |--------------------------------------------------------------------------
    */ #[On('open-satellite-modal')]
    public function openModal(int|string $UserID): void
    {
        $this->openInMode($UserID, false);
    }

    /**
     * Reapply — used by the Satellite Members table for a satellite user whose
     * activation was REJECTED. Pre-fills from their own account + last
     * application so the admin can correct info and resubmit WITHOUT creating
     * a new user.
     */
    #[On('open-satellite-reapply')]
    public function openForReapply(int|string $UserID): void
    {
        $this->openInMode($UserID, true);
    }

    protected function openInMode(int|string $UserID, bool $reapply): void
    {
        $member = User::query()->find($UserID);

        if (! $member) {
            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'Member not found.',
            ]);

            return;
        }

        $this->resetForm();

        $this->UserID = (int) $UserID;
        $this->member = $member;

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        |
        | Store the selected member's referral code for BOTH:
        |
        | 1. New Satellite application
        | 2. Satellite reapplication
        |
        | This referral code can NEVER be used as the uplineCode.
        |
        */
        $this->selectedMemberReferralCode = trim(
            (string) ($member->referralCode ?? '')
        );

        $this->isReapply = $reapply;
        $this->open = true;

        if ($reapply) {
            $this->loadReapplyData($member);
        }
    }

    /**
     * Pre-fills the wizard from the satellite user's most recent application
     * (plan, satellite details, upline) and their own account (username/email).
     * Password is intentionally left blank — it only changes if re-entered.
     */
    protected function loadReapplyData(User $member): void
    {

        $lastApplication = Activation::query()
            ->where('UserID', $member->UserID)
            ->latest('ActivationID')
            ->first();

        if ($lastApplication) {
            $this->uplineCode = (string) ($lastApplication->upline_code ?? '');
        }

        $satellite = Satellite::query()
            ->where('UserID', $member->UserID)
            ->latest('satelliteID')
            ->first();

        if ($satellite) {
            $this->satellite_name = (string) $satellite->satellite_name;
            $this->address = (string) ($satellite->address ?? '');
            $this->latitude = $satellite->latitude !== null ? (float) $satellite->latitude : null;
            $this->longitude = $satellite->longitude !== null ? (float) $satellite->longitude : null;

            // Tier comes from the satellite record's TYPE id (6/7/8),
            // NOT activations.satelliteID (which is the satellite row's PK).
            $this->satellite_type = self::TIER_BY_SATELLITE_TYPE_ID[$satellite->satellite_typeID] ?? '';
        }

        $this->username = (string) $member->username;
        $this->email = (string) $member->email;
    }

    public function close(): void
    {
        $this->dispatch('satellite-destroy-map');
        $this->open = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset([
            'UserID', 'member',
            'selectedMemberReferralCode', 'isReapply',
            'satellite_type', 'satellite_name', 'address', 'latitude', 'longitude', 'showMapModal',
            'username', 'email', 'password', 'password_confirmation',
            'payment_method', 'reference_code', 'uplineCode', 'agree',
        ]);
        $this->resetValidation();
    }

    public function updated(string $property, mixed $value): void
    {
        if (in_array($property, ['satellite_name', 'address'], true)) {
            $this->{$property} = ucwords(strtolower($value));
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Map / Coordinates
    |--------------------------------------------------------------------------
    */

    public function toggleMap(): void
    {
        $this->showMapModal = ! $this->showMapModal;

        if ($this->showMapModal) {
            $this->dispatch('satellite-map-opened',
                latitude: $this->latitude,
                longitude: $this->longitude
            );
        }
    }

    #[On('satellite-set-coordinates')]
    public function setCoordinates(mixed $lat, mixed $lng, ?string $address = null): void
    {
        $this->latitude = (float) $lat;
        $this->longitude = (float) $lng;

        if (! empty($address)) {
            $this->address = ucwords(strtolower($address));
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Settings Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Fetch a setting's value by its primary key (SettingsID), not by name.
     * This survives renames of the `name` value.
     */
    protected function getSettingValueById(?int $settingsID): ?string
    {
        return $settingsID === null
            ? null
            : DB::table('settings')->where('SettingsID', $settingsID)->value('value');
    }

    /**
     * Maps the chosen tier to its Satellite type PK (satellites.satellite_typeID)
     * and its coverage-amount SettingsID.
     *
     * @return array{name: string, SatelliteID: int, settingsID: int|null}
     */
    protected function getSatelliteInformation(): array
    {
        return match ($this->satellite_type) {
            'Bronze' => ['SatelliteID' => 6, 'name' => 'Bronze', 'settingsID' => self::SETTING_ID_BRONZE_COVERAGE],
            'Silver' => ['SatelliteID' => 7, 'name' => 'Silver', 'settingsID' => self::SETTING_ID_SILVER_COVERAGE],
            'Gold' => ['SatelliteID' => 8, 'name' => 'Gold', 'settingsID' => self::SETTING_ID_GOLD_COVERAGE],
            default => throw new \RuntimeException('Invalid satellite membership type.'),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Computed Properties
    |--------------------------------------------------------------------------
    */

    public function getMembershipAmountProperty(): float
    {
        if (! in_array($this->satellite_type, self::TIERS, true)) {
            return 0;
        }

        $satellite = $this->getSatelliteInformation();
        $value = $this->getSettingValueById($satellite['settingsID']);

        if ($value === null || (float) $value <= 0) {
            Log::warning('Satellite coverage setting missing or zero', [
                'satellite_type' => $this->satellite_type,
                'settingsID' => $satellite['settingsID'],
                'raw_value' => $value,
            ]);
        }

        return (float) ($value ?? 0);
    }

    public function getMembershipValidityProperty(): int
    {
        return (int) ($this->getSettingValueById(self::SETTING_ID_MEMBERSHIP_VALIDITY_MONTHS) ?? 12);
    }

    public function getGcashEnabledProperty(): bool
    {
        return $this->settingEnabled(self::SETTING_ID_GCASH_ENABLED);
    }

    public function getBankPaymentEnabledProperty(): bool
    {
        return $this->settingEnabled(self::SETTING_ID_BANK_ENABLED);
    }

    public function getPaymayaEnabledProperty(): bool
    {
        // Defaults to enabled while no Maya setting row exists.
        return $this->getSettingValueById(self::SETTING_ID_PAYMAYA_ENABLED) === null
            || $this->settingEnabled(self::SETTING_ID_PAYMAYA_ENABLED);
    }

    private function settingEnabled(?int $settingsID): bool
    {
        return filter_var(
            $this->getSettingValueById($settingsID) ?? false,
            FILTER_VALIDATE_BOOLEAN
        );
    }

    /**
     * Payment methods available per the enabled settings (always includes Cash).
     *
     * @return array<int, array{method: string, icon: string, desc: string, online: bool}>
     */
    public function getAvailablePaymentMethodsProperty(): array
    {
        return collect(self::PAYMENT_METHODS)
            ->filter(fn (array $meta, string $method) => match ($method) {
                'Cash' => true,
                'GCash' => $this->getGcashEnabledProperty(),
                'Maya' => $this->getPaymayaEnabledProperty(),
                'Bank Transfer' => $this->getBankPaymentEnabledProperty(),
                default => false,
            })
            ->map(fn (array $meta, string $method) => ['method' => $method, ...$meta])
            ->values()
            ->all();
    }

    /**
     * Plain names of the available payment methods (for validation).
     *
     * @return array<int, string>
     */
    public function getAvailablePaymentMethodNamesProperty(): array
    {
        return array_column($this->getAvailablePaymentMethodsProperty(), 'method');
    }

    /**
     * @return array<string, array{label: string, color: string, icon: string}>
     */
    public function getPlanMetaProperty(): array
    {
        return self::PLAN_META;
    }

    public function getPaymentIsOnlineProperty(): bool
    {
        $meta = self::PAYMENT_METHODS[$this->payment_method] ?? null;

        return $meta !== null && $meta['online'];
    }

    /**
     * @return array{lat: float, lng: float}
     */
    public function getDefaultCoordinatesProperty(): array
    {
        return self::MAP_DEFAULTS;
    }

    /*
    |--------------------------------------------------------------------------
    | Validation (per wizard step)
    |--------------------------------------------------------------------------
    */

    public function validateStep(int $step): array
    {
        try {

            match ($step) {
                1 => $this->validate(
                    $this->satelliteDetailsRules(),
                    $this->satelliteDetailsMessages()
                ),

                2 => $this->validate(
                    $this->accountRules(),
                    $this->accountMessages()
                ),

                3 => $this->validate(
                    $this->paymentRules(),
                    $this->paymentMessages()
                ),

                default => throw new \InvalidArgumentException('Invalid wizard step.'),
            };

            return [
                'valid' => true,
                'errors' => [],
            ];

        } catch (ValidationException $e) {

            return [
                'valid' => false,
                'errors' => $e->validator->errors()->all(),
            ];
        }
    }

    /**
     * @return array<string, array<int|string, mixed>>
     */
    protected function satelliteDetailsRules(): array
    {
        return [
            'satellite_type' => ['required', Rule::in(self::TIERS)],
            'satellite_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function satelliteDetailsMessages(): array
    {
        return [
            'satellite_type.required' => 'Please select a membership type.',
            'satellite_type.in' => 'Please select a valid membership type.',
            'satellite_name.required' => 'Please enter a satellite name.',
            'address.required' => 'Please enter an address.',
        ];
    }

    /**
     * @return array<string, array<int|string, mixed>>
     */
    protected function accountRules(): array
    {
        // Reapply: exclude this satellite user's own row from uniqueness checks
        // and make the password optional (their login stays if left blank).
        $ignoreSelf = $this->isReapply && $this->UserID ? (int) $this->UserID : null;

        return [
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($ignoreSelf, 'UserID'),
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($ignoreSelf, 'UserID'),
            ],

            'password' => array_merge(
                $this->isReapply ? ['nullable'] : ['required'],
                [
                    'string',
                    'confirmed',
                    Password::default(),
                ]
            ),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function accountMessages(): array
    {
        return [
            'username.required' => 'Please enter a username.',
            'username.min' => 'The username must be at least 3 characters.',
            'username.max' => 'The username may not exceed 50 characters.',
            'username.alpha_dash' => 'The username may only contain letters, numbers, dashes, and underscores.',
            'username.unique' => 'This username is already registered.',

            'email.required' => 'Please enter an email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',

            'password.required' => 'Please enter a password.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }

    /**
     * @return array<string, array<int|string, mixed>>
     */
    protected function paymentRules(): array
    {
        return [
            'payment_method' => [
                'required',
                Rule::in($this->getAvailablePaymentMethodNamesProperty()),
            ],

            'reference_code' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(
                    fn () => $this->payment_method !== ''
                        && $this->payment_method !== 'Cash'
                ),
            ],

            'uplineCode' => [
                'required',
                'string',
                'max:255',
            ],

            'agree' => [
                'accepted',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function paymentMessages(): array
    {
        return [
            'payment_method.required' => 'Please select a payment method.',

            'payment_method.in' => 'The selected payment method is not currently available.',

            'reference_code.required' => 'Please enter the payment reference code.',

            'reference_code.max' => 'The payment reference code may not exceed 255 characters.',

            'uplineCode.required' => 'Please enter the upline code.',

            'uplineCode.max' => 'The upline code may not exceed 255 characters.',

            'agree.accepted' => 'You must agree to the terms and conditions.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Submit
    |--------------------------------------------------------------------------
    */

    private function generateUplineCode(): string
    {
        return strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 6));
    }

    #[On('satellite-confirm-submit')]
    public function confirmSubmit(): void
    {
        $this->submit();
    }

    /**
     * Validate the submitted upline code and show a specific SweetAlert
     * depending on whether the referral code exists, is inactive, suspended,
     * or belongs to the current user.
     */
protected function validateUplineCode(): bool
{
    $uplineCode = trim($this->uplineCode);

    /*
    |--------------------------------------------------------------------------
    | EMPTY
    |--------------------------------------------------------------------------
    */
    if ($uplineCode === '') {
        $this->addError(
            'uplineCode',
            'Please enter the upline code.'
        );

        $this->dispatch('swal:error', [
            'title' => 'Upline Required',
            'text' => 'Please enter the upline code.',
        ]);

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | PREVENT SELECTED MEMBER FROM USING THEIR OWN REFERRAL CODE
    |--------------------------------------------------------------------------
    |
    | Applies to BOTH NEW APPLICATION and REAPPLY.
    |
    */
    if (
        filled($this->selectedMemberReferralCode) &&
        strcasecmp(
            $uplineCode,
            trim($this->selectedMemberReferralCode)
        ) === 0
    ) {
        $this->addError(
            'uplineCode',
            'You cannot use the selected member’s own referral code as the upline code.'
        );

        $this->dispatch('swal:error', [
            'title' => 'Cannot Use Own Referral Code',
            'text' => "Referral code '{$uplineCode}' belongs to the selected member. You cannot use your own referral code as an upline.",
        ]);

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | FIND USER BY REFERRAL CODE
    |--------------------------------------------------------------------------
    */
    $upline = User::query()
        ->where('referralCode', $uplineCode)
        ->first();
    /*
    |--------------------------------------------------------------------------
    | REFERRAL CODE DOES NOT EXIST
    |--------------------------------------------------------------------------
    */
    if (! $upline) {
        $this->addError(
            'uplineCode',
            "The referral code '{$uplineCode}' does not exist."
        );

        $this->dispatch('swal:error', [
            'title' => 'Referral Code Not Found',
            'text' => "The referral code '{$uplineCode}' does not exist in the system.",
        ]);

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | REAPPLY — PREVENT USING THE SATELLITE MEMBER'S OWN REFERRAL CODE
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | Satellite member:
    | referralCode = ABC123
    |
    | Reapply enters:
    | uplineCode = ABC123
    |
    | This must be rejected.
    |
    */
    /*
    |--------------------------------------------------------------------------
    | PREVENT SELECTED MEMBER FROM USING THEIR OWN REFERRAL CODE
    |--------------------------------------------------------------------------
    |
    | This applies to BOTH:
    |
    | NEW APPLICATION:
    |   TrueLife member referralCode cannot be the new Satellite's uplineCode.
    |
    | REAPPLY:
    |   Satellite member referralCode cannot be their own uplineCode.
    |
    */
    if (
        filled($this->selectedMemberReferralCode) &&
        strcasecmp(
            $uplineCode,
            trim($this->selectedMemberReferralCode)
        ) === 0
    ) {
        $this->addError(
            'uplineCode',
            'You cannot use the selected member’s own referral code as the upline code.'
        );

        $this->dispatch('swal:error', [
            'title' => 'Cannot Use Own Referral Code',
            'text' => "Referral code '{$uplineCode}' belongs to the selected member. You cannot use your own referral code as an upline.",
        ]);

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | INACTIVE
    |--------------------------------------------------------------------------
    */
    if ($upline->status === 'Inactive') {
        $this->addError(
            'uplineCode',
            "The upline account with referral code '{$uplineCode}' is inactive."
        );

        $this->dispatch('swal:error', [
            'title' => 'Upline Account Inactive',
            'text' => "The user with referral code '{$uplineCode}' is currently Inactive and cannot be used as an upline.",
        ]);

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | SUSPENDED
    |--------------------------------------------------------------------------
    */
    if ($upline->status === 'Suspended') {
        $this->addError(
            'uplineCode',
            "The upline account with referral code '{$uplineCode}' is suspended."
        );

        $this->dispatch('swal:error', [
            'title' => 'Upline Account Suspended',
            'text' => "The user with referral code '{$uplineCode}' is currently Suspended and cannot be used as an upline.",
        ]);

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | OTHER NON-ACTIVE STATUS
    |--------------------------------------------------------------------------
    */
    if ($upline->status !== 'Active') {
        $this->addError(
            'uplineCode',
            "The upline account with referral code '{$uplineCode}' is not active."
        );

        $this->dispatch('swal:error', [
            'title' => 'Upline Not Active',
            'text' => "The user with referral code '{$uplineCode}' is not Active and cannot be used as an upline.",
        ]);

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | VALID
    |--------------------------------------------------------------------------
    */
    return true;
}

    public function validateAndSubmit(): void
    {
        $this->uplineCode = trim($this->uplineCode);
        $this->reference_code = trim($this->reference_code);

        /*
        |--------------------------------------------------------------------------
        | Validate Payment + Upline Basic Rules
        |--------------------------------------------------------------------------
        */
        try {
            $this->validate(
                $this->paymentRules(),
                $this->paymentMessages()
            );

        } catch (ValidationException $e) {

            $this->dispatch(
                'satellite-validation-error',
                errors: $e->validator->errors()->all()
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Upline Specifically
        |--------------------------------------------------------------------------
        |
        | This produces different SweetAlerts for:
        |
        | - Referral code does not exist
        | - Own referral code
        | - Inactive user
        | - Suspended user
        | - Other non-active status
        |--------------------------------------------------------------------------
        */
        if (! $this->validateUplineCode()) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Validation Passed
        |--------------------------------------------------------------------------
        */
        if ($this->isReapply) {
            $this->dispatch('satellite-reapply-confirmation');
        } else {
            $this->dispatch('satellite-submit-confirmation');
        }
    }

    public function submit(): void
    {
        // Guard: a stale/ghost instance (e.g. from a duplicated mount listening
        // to the same global events) must never run the submission flow.
        if (! $this->open || $this->UserID === null) {
            return;
        }

        try {
            // Capture before close()/resetForm() clears the flag
            $isReapply = $this->isReapply;

            $member = User::findOrFail($this->UserID);
            $satellite = $this->getSatelliteInformation();
            $amount = $this->getMembershipAmountProperty();

            if ($amount <= 0) {
                throw new \RuntimeException(
                    "The membership amount for '{$this->satellite_type}' is not configured (SettingsID: {$satellite['settingsID']})."
                );
            }

            $satelliteRoleID = DB::table('roles')
                ->where('rolename', 'Satellite')
                ->where('status', 1)
                ->value('RoleID');

            if (! $satelliteRoleID) {
                throw new \RuntimeException('Satellite role was not found or is inactive.');
            }

            $processedBy = Auth::user()->UserID
                ?? throw new \RuntimeException('The authenticated user could not be identified.');

            // Duplicate-email guard (reapply excludes its own row — handled by validation)
            if (! $this->isReapply && User::where('email', $this->email)->exists()) {
                $this->addError('email', 'This email address is already registered.');

                return;
            }

            $activationType = $this->getPaymentIsOnlineProperty() ? 'O' : 'W';
            $paymentReference = $activationType === 'O' ? trim($this->reference_code) : null;


            $uplineCode = trim($this->uplineCode);


            /*
            |--------------------------------------------------------------------------
            | FIND THE ACTUAL UPLINE
            |--------------------------------------------------------------------------
            |
            | The entered uplineCode must belong to another user.
            |
            */
            /*
            |--------------------------------------------------------------------------
            | VALIDATE UPLINE AGAIN BEFORE SAVING
            |--------------------------------------------------------------------------
            |
            | This is the final server-side protection before the transaction.
            |--------------------------------------------------------------------------
            */
            $upline = User::query()
                ->where('referralCode', $uplineCode)
                ->first();

            if (! $upline) {
                $this->addError(
                    'uplineCode',
                    "The referral code '{$uplineCode}' does not exist."
                );

                $this->dispatch('swal:error', [
                    'title' => 'Referral Code Not Found',
                    'text' => "The referral code '{$uplineCode}' does not exist in the system.",
                ]);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | FINAL SELF-UPLINE PROTECTION
            |--------------------------------------------------------------------------
            |
            | Applies to BOTH NEW APPLICATION and REAPPLY.
            |
            | The selected member can NEVER use their own referralCode
            | as the uplineCode.
            |
            */
            if (
                $this->UserID !== null &&
                (int) $upline->UserID === (int) $this->UserID
            ) {
                $this->addError(
                    'uplineCode',
                    'You cannot use the selected member’s own referral code as the upline code.'
                );

                $this->dispatch('swal:error', [
                    'title' => 'Cannot Use Own Referral Code',
                    'text' => "Referral code '{$uplineCode}' belongs to the selected member. You cannot use your own referral code as an upline.",
                ]);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | INACTIVE
            |--------------------------------------------------------------------------
            */
            if ($upline->status === 'Inactive') {
                $this->addError(
                    'uplineCode',
                    "The upline account with referral code '{$uplineCode}' is inactive."
                );

                $this->dispatch('swal:error', [
                    'title' => 'Upline Account Inactive',
                    'text' => "The user with referral code '{$uplineCode}' is currently Inactive and cannot be used as an upline.",
                ]);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | SUSPENDED
            |--------------------------------------------------------------------------
            */
            if ($upline->status === 'Suspended') {
                $this->addError(
                    'uplineCode',
                    "The upline account with referral code '{$uplineCode}' is suspended."
                );

                $this->dispatch('swal:error', [
                    'title' => 'Upline Account Suspended',
                    'text' => "The user with referral code '{$uplineCode}' is currently Suspended and cannot be used as an upline.",
                ]);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | ANY OTHER NON-ACTIVE STATUS
            |--------------------------------------------------------------------------
            */
            if ($upline->status !== 'Active') {
                $this->addError(
                    'uplineCode',
                    "The upline account with referral code '{$uplineCode}' is not active."
                );

                $this->dispatch('swal:error', [
                    'title' => 'Upline Not Active',
                    'text' => "The user with referral code '{$uplineCode}' is not Active and cannot be used as an upline.",
                ]);

                return;
            }

            $savedUser = DB::transaction(function () use (
                $member, $satellite, $satelliteRoleID, $amount,
                $uplineCode, $processedBy, $activationType, $paymentReference
            ) {
                if ($this->isReapply) {
                    return $this->submitReapply(
                        $member, $satellite, $amount, $uplineCode, $processedBy, $activationType, $paymentReference
                    );
                }

                return $this->submitNewApplication(
                    $member, $satellite, $satelliteRoleID, $amount, $uplineCode, $processedBy, $activationType, $paymentReference
                );
            });

            Log::info($isReapply ? 'Satellite REAPPLICATION submitted' : 'Satellite membership application created', [
                'UserID' => $savedUser->UserID,
                'satellite_type' => $this->satellite_type,
                'satellite_name' => $this->satellite_name,
                'amount' => $amount,
                'membership_validity_months' => $this->getMembershipValidityProperty(),
                'payment_method' => $this->payment_method,
                'activation_type' => $activationType,
                'has_payment_reference' => $paymentReference !== null,
                'ProcessedBy' => $processedBy,
            ]);

            $this->close();
            $this->dispatch('swal:success', [
                'title' => $isReapply ? 'Reapplication Submitted!' : 'Application Submitted!',
                'text' => $isReapply
                    ? "The Satellite reapplication for {$savedUser->username} has been submitted and is pending payment verification."
                    : "A NEW Satellite application has been created for {$savedUser->username} and is pending payment verification.",
            ]);
            $this->dispatch('members-refresh');

        } catch (\Throwable $e) {
            report($e);

            Log::error('Satellite application failed', [
                'UserID' => $this->UserID,
                'satellite_type' => $this->satellite_type,
                'payment_method' => $this->payment_method,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Application Failed',
                'text' => 'Something went wrong while creating the Satellite membership application.',
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Submit — Branches (inside a DB transaction)
    |--------------------------------------------------------------------------
    */

    /**
     * Fresh application: creates a NEW satellite user account + satellite record
     * + a Pending activation (existing behavior, extracted verbatim).
     */
    private function submitNewApplication(
        User $member,
        array $satellite,
        int|string $satelliteRoleID,
        float $amount,
        string $uplineCode,
        int $processedBy,
        string $activationType,
        ?string $paymentReference
    ): User {
        $newUser = new User;
        $newUser->username = $this->username;
        $newUser->password = Hash::make($this->password);
        $newUser->lastname = $member->lastname;
        $newUser->firstname = $member->firstname;
        $newUser->middlename = $member->middlename;
        $newUser->occupation = $member->occupation;
        $newUser->gender = $member->gender;
        $newUser->civilstatus = $member->civilstatus;
        $newUser->birthdate = $member->birthdate;
        $newUser->contactno = $member->contactno;
        $newUser->address = $this->address;
        $newUser->latitude = $this->latitude;
        $newUser->longitude = $this->longitude;
        $newUser->email = $this->email;
        $newUser->referralCode = $this->generateUplineCode();
        $newUser->uplineCode = $uplineCode;
        $newUser->RoleID = $satelliteRoleID;
        $newUser->status = 'Inactive';
        $newUser->save();

        $satelliteRecord = Satellite::create([
            'satellite_name' => $this->satellite_name,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'UserID' => $newUser->UserID,
            'satellite_typeID' => $satellite['SatelliteID'],
        ]);

        DB::table('activations')->insert([
            'UserID' => $newUser->UserID,
            'satelliteID' => $satelliteRecord->satelliteID,
            'amount' => $amount,
            'type' => $activationType,
            'payment_method' => $this->payment_method,
            'payment_reference' => $paymentReference,
            'upline_code' => $uplineCode,
            'ProcessedBy' => $processedBy,
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $newUser;
    }

    /**
     * Reapplication: UPDATES this rejected satellite user's existing account
     * (no new user!) and files a fresh Pending activation against their
     * existing (or recreated) satellite record.
     */
    private function submitReapply(
        User $member,
        array $satellite,
        float $amount,
        string $uplineCode,
        int $processedBy,
        string $activationType,
        ?string $paymentReference
    ): User {
        // Refresh from DB to avoid stale in-memory state
        $member->refresh();

        // 1) Update the EXISTING account (username/email may have been corrected;
        //    password only changes when a new one is provided).
        $member->username = $this->username;
        $member->email = $this->email;
        $member->address = $this->address;
        $member->latitude = $this->latitude;
        $member->longitude = $this->longitude;

        if (filled($this->password)) {
            $member->password = Hash::make($this->password);
        }

        if (filled($uplineCode)) {
            $member->uplineCode = $uplineCode;
        }

        $member->save();

        // 2) Update their existing satellite record (plan may have changed),
        //    or create one if none exists yet.
        $satelliteRecord = Satellite::query()
            ->where('UserID', $member->UserID)
            ->latest('satelliteID')
            ->first();

        if ($satelliteRecord) {
            $satelliteRecord->update([
                'satellite_name' => $this->satellite_name,
                'address' => $this->address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'satellite_typeID' => $satellite['SatelliteID'],
            ]);

            $satelliteID = $satelliteRecord->satelliteID;
        } else {
            $satelliteRecord = Satellite::create([
                'satellite_name' => $this->satellite_name,
                'address' => $this->address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'UserID' => $member->UserID,
                'satellite_typeID' => $satellite['SatelliteID'],
            ]);

            $satelliteID = $satelliteRecord->satelliteID;
        }

        // 3) File a fresh Pending activation on the SAME user
        DB::table('activations')->insert([
            'UserID' => $member->UserID,
            'satelliteID' => $satelliteID,
            'amount' => $amount,
            'type' => $activationType,
            'payment_method' => $this->payment_method,
            'payment_reference' => $paymentReference,
            'upline_code' => $uplineCode,
            'ProcessedBy' => $processedBy,
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $member;
    }

    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

    public function render(): View
    {
        return view('components.admin-pages.masterlist.members-modals.satelite-modal');
    }
}
