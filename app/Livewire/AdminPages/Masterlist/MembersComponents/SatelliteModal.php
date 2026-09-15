<?php

namespace App\Livewire\AdminPages\Masterlist\MembersComponents;

use App\Models\Satellite;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
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

    public ?int $UserID = null;

    public ?User $member = null;

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
    */

    #[On('open-satellite-modal')]
    public function openModal(int|string $UserID): void
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
        $this->open = true;
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
            'satellite_type', 'satellite_name', 'address', 'latitude', 'longitude', 'showMapModal',
            'username','email', 'password', 'password_confirmation',
            'payment_method', 'reference_code', 'agree',
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

        } catch (\Illuminate\Validation\ValidationException $e) {

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
            return [
                'username' => [
                    'required',
                    'string',
                    'min:3',
                    'max:50',
                    'alpha_dash',
                    'unique:users,username',
                ],

                'email' => [
                    'required',
                    'email',
                    'max:255',
                    'unique:users,email',
                ],

                'password' => [
                    'required',
                    'string',
                    'confirmed',
                    Password::default(),
                ],
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
            'payment_method' => ['required', Rule::in($this->getAvailablePaymentMethodNamesProperty())],
            'reference_code' => ['nullable', 'string', 'max:255', 'required_unless:payment_method,Cash'],
            'agree' => ['accepted'],
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
            'reference_code.required_unless' => 'Please enter the payment reference code.',
            'reference_code.max' => 'The payment reference code may not exceed 255 characters.',
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

    public function submit(): void
    {
        $this->validate([
            'UserID' => ['required', 'integer', 'exists:users,UserID'],
            ...$this->satelliteDetailsRules(),
            ...$this->accountRules(),
            ...$this->paymentRules(),
        ], [
            'UserID.required' => 'The selected member is required.',
            ...$this->satelliteDetailsMessages(),
            ...$this->accountMessages(),
            ...$this->paymentMessages(),
        ]);

        try {
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

            if (User::where('email', $this->email)->exists()) {
                $this->addError('email', 'This email address is already registered.');

                return;
            }

            $activationType = $this->getPaymentIsOnlineProperty() ? 'O' : 'W';
            $paymentReference = $activationType === 'O' ? trim($this->reference_code) : null;
            $uplineCode = $member->referralCode ?: $member->uplineCode ?: '';

            $newUser = DB::transaction(function () use (
                $member, $satellite, $satelliteRoleID, $amount,
                $uplineCode, $processedBy, $activationType, $paymentReference
            ) {
                $newUser = new User;
                $newUser->username = $this->username;
                $newUser->password = Hash::make($this->password);
                $newUser->lastname = $member->lastname;
                $newUser->firstname = $member->firstname;
                $newUser->middlename = $member->middlename;

                /** @var string|null $occupation */
                $occupation = $member->occupation;
                $newUser->occupation = $occupation;
                $newUser->gender = $member->gender;
                $newUser->civilstatus = $member->civilstatus;
                $newUser->birthdate = $member->birthdate;
                $newUser->contactno = $member->contactno;
                $newUser->address = $this->address;
                $newUser->latitude = $this->latitude;
                $newUser->longitude = $this->longitude;
                $newUser->email = $this->email;
                $newUser->referralCode = $this->generateUplineCode();
                $newUser->uplineCode = $member->referralCode;
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
            });

            Log::info('Satellite membership application created', [
                'original_UserID' => $member->UserID,
                'new_UserID' => $newUser->UserID,
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
                'title' => 'Application Submitted!',
                'text' => 'The Satellite membership application has been created successfully and is pending payment verification.',
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
    | View
    |--------------------------------------------------------------------------
    */

    public function render(): View
    {
        return view('components.admin-pages.masterlist.members-modals.satelite-modal');
    }
}
