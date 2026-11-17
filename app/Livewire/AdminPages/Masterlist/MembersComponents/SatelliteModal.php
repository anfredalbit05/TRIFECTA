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
    public bool $open = false;
    public bool $isReapply = false;
    public ?int $UserID = null;
    public ?User $member = null;
    public string $selectedMemberReferralCode = '';

    public string $satellite_type = '';
    public string $satellite_name = '';
    public string $address = '';
    public ?float $latitude = null;
    public ?float $longitude = null;
    public bool $showMapModal = false;

    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public string $payment_method = '';
    public string $reference_code = '';
    public string $uplineCode = '';
    public bool $agree = false;

    private const SETTING_ID_MEMBERSHIP_VALIDITY_MONTHS = 5;
    private const SETTING_ID_BRONZE_COVERAGE = 6;
    private const SETTING_ID_SILVER_COVERAGE = 7;
    private const SETTING_ID_GOLD_COVERAGE = 8;
    private const SETTING_ID_GCASH_ENABLED = 25;
    private const SETTING_ID_BANK_ENABLED = 26;
    private const SETTING_ID_PAYMAYA_ENABLED = 27;

    private const PAYMENT_SETTING_NAMES = [
        'gcash_number' => 'gcash_number1',
        'gcash_name' => 'gcash_number1_name',
        'maya_number' => 'paymaya_account_number',
        'maya_name' => 'paymaya_account_name',
        'bank_name' => 'bpi_account_name',
        'bank_number' => 'bpi_account_number',
    ];

    private const TIERS = ['Bronze', 'Silver', 'Gold'];

    private const TIER_BY_SATELLITE_TYPE_ID = [
        6 => 'Bronze',
        7 => 'Silver',
        8 => 'Gold',
    ];

    private const PLAN_META = [
        'Bronze' => ['label' => 'Basic', 'color' => 'var(--color-primary-light)', 'icon' => '♢'],
        'Silver' => ['label' => 'Popular', 'color' => 'var(--color-primary)', 'icon' => '◇'],
        'Gold' => ['label' => 'Premium', 'color' => 'var(--color-accent)', 'icon' => '✦'],
    ];

    private const PAYMENT_METHODS = [
        'Cash' => [
            'icon' => '₱',
            'desc' => 'Pay directly at the authorized office.',
            'online' => false,
        ],
        'GCash' => [
            'icon' => 'G',
            'desc' => 'Pay using the organization’s GCash account.',
            'online' => true,
        ],
        'Maya' => [
            'icon' => 'M',
            'desc' => 'Pay using the organization’s Maya account.',
            'online' => true,
        ],
        'Bank Transfer' => [
            'icon' => 'B',
            'desc' => 'Transfer directly to the organization’s bank account.',
            'online' => true,
        ],
    ];

    private const MAP_DEFAULTS = [
        'lat' => 7.442294461028405,
        'lng' => 125.81370503321095,
    ];

    #[On('open-satellite-modal')]
    public function openModal(int|string $UserID): void
    {
        $this->openInMode($UserID, false);
    }

    #[On('open-satellite-reapply')]
    public function openForReapply(int|string $UserID): void
    {
        $this->openInMode($UserID, true);
    }

    protected function openInMode(int|string $UserID, bool $reapply): void
    {
        $member = User::find($UserID);

        if (!$member) {
            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'text' => 'Member not found.',
            ]);
            return;
        }

        $this->resetForm();
        $this->UserID = (int) $UserID;
        $this->member = $member;
        $this->selectedMemberReferralCode = trim((string) ($member->referralCode ?? ''));
        $this->isReapply = $reapply;
        $this->open = true;

        if ($reapply) {
            $this->loadReapplyData($member);
        }
    }

    protected function loadReapplyData(User $member): void
    {
        $lastApplication = Activation::where('UserID', $member->UserID)
            ->latest('ActivationID')
            ->first();

        if ($lastApplication) {
            $this->uplineCode = (string) ($lastApplication->upline_code ?? '');
        }

        $satellite = Satellite::where('UserID', $member->UserID)
            ->latest('satelliteID')
            ->first();

        if ($satellite) {
            $this->satellite_name = (string) $satellite->satellite_name;
            $this->address = (string) ($satellite->address ?? '');
            $this->latitude = $satellite->latitude !== null ? (float) $satellite->latitude : null;
            $this->longitude = $satellite->longitude !== null ? (float) $satellite->longitude : null;
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
            'UserID',
            'member',
            'selectedMemberReferralCode',
            'isReapply',
            'satellite_type',
            'satellite_name',
            'address',
            'latitude',
            'longitude',
            'showMapModal',
            'username',
            'email',
            'password',
            'password_confirmation',
            'payment_method',
            'reference_code',
            'uplineCode',
            'agree',
        ]);

        $this->resetValidation();
    }

    public function updated(string $property, mixed $value): void
    {
        if (in_array($property, ['satellite_name', 'address'], true)) {
            $this->{$property} = ucwords(strtolower($value));
        }
    }

    public function toggleMap(): void
    {
        $this->showMapModal = !$this->showMapModal;

        if ($this->showMapModal) {
            $this->dispatch(
                'satellite-map-opened',
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

        if (!empty($address)) {
            $this->address = ucwords(strtolower($address));
        }
    }

    protected function getSettingValueById(?int $settingsID): ?string
    {
        return $settingsID === null
            ? null
            : DB::table('settings')
                ->where('SettingsID', $settingsID)
                ->value('value');
    }

    protected function getSettingValueByName(string $name): ?string
    {
        return DB::table('settings')
            ->where('name', $name)
            ->value('value');
    }

    protected function getSatelliteInformation(): array
    {
        return match ($this->satellite_type) {
            'Bronze' => [
                'SatelliteID' => 6,
                'name' => 'Bronze',
                'settingsID' => self::SETTING_ID_BRONZE_COVERAGE,
            ],
            'Silver' => [
                'SatelliteID' => 7,
                'name' => 'Silver',
                'settingsID' => self::SETTING_ID_SILVER_COVERAGE,
            ],
            'Gold' => [
                'SatelliteID' => 8,
                'name' => 'Gold',
                'settingsID' => self::SETTING_ID_GOLD_COVERAGE,
            ],
            default => throw new \RuntimeException('Invalid satellite membership type.'),
        };
    }

    public function getMembershipAmountProperty(): float
    {
        if (!in_array($this->satellite_type, self::TIERS, true)) {
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
        return (int) ($this->getSettingValueById(
            self::SETTING_ID_MEMBERSHIP_VALIDITY_MONTHS
        ) ?? 12);
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
        return $this->settingEnabled(self::SETTING_ID_PAYMAYA_ENABLED);
    }

    protected function settingEnabled(?int $settingsID): bool
    {
        return filter_var(
            $this->getSettingValueById($settingsID) ?? false,
            FILTER_VALIDATE_BOOLEAN
        );
    }

    /**
     * Payment methods + actual organization payment account details.
     *
     * GCash:
     * gcash_number1
     * gcash_number1_name
     *
     * Maya:
     * paymaya_account_number
     * paymaya_account_name
     *
     * Bank:
     * bpi_account_name
     * bpi_account_number
     */
    public function getAvailablePaymentMethodsProperty(): array
    {
        return collect(self::PAYMENT_METHODS)
            ->filter(fn(array $meta, string $method) => match ($method) {
                'Cash' => true,
                'GCash' => $this->getGcashEnabledProperty(),
                'Maya' => $this->getPaymayaEnabledProperty(),
                'Bank Transfer' => $this->getBankPaymentEnabledProperty(),
                default => false,
            })
            ->map(function (array $meta, string $method) {
                $details = match ($method) {
                    'GCash' => [
                        'account_name' => $this->getSettingValueByName(
                            self::PAYMENT_SETTING_NAMES['gcash_name']
                        ),
                        'account_number' => $this->getSettingValueByName(
                            self::PAYMENT_SETTING_NAMES['gcash_number']
                        ),
                        'bank_name' => null,
                    ],

                    'Maya' => [
                        'account_name' => $this->getSettingValueByName(
                            self::PAYMENT_SETTING_NAMES['maya_name']
                        ),
                        'account_number' => $this->getSettingValueByName(
                            self::PAYMENT_SETTING_NAMES['maya_number']
                        ),
                        'bank_name' => null,
                    ],

                    'Bank Transfer' => [
                        'account_name' => $this->getSettingValueByName(
                            self::PAYMENT_SETTING_NAMES['bank_name']
                        ),
                        'account_number' => $this->getSettingValueByName(
                            self::PAYMENT_SETTING_NAMES['bank_number']
                        ),
                        'bank_name' => 'BPI',
                    ],

                    default => [
                        'account_name' => null,
                        'account_number' => null,
                        'bank_name' => null,
                    ],
                };

                return [
                    'method' => $method,
                    ...$meta,
                    ...$details,
                ];
            })
            ->values()
            ->all();
    }

    public function getAvailablePaymentMethodNamesProperty(): array
    {
        return array_column(
            $this->getAvailablePaymentMethodsProperty(),
            'method'
        );
    }

    public function getPlanMetaProperty(): array
    {
        return self::PLAN_META;
    }

    public function getPaymentIsOnlineProperty(): bool
    {
        return (self::PAYMENT_METHODS[$this->payment_method]['online'] ?? false);
    }

    public function getDefaultCoordinatesProperty(): array
    {
        return self::MAP_DEFAULTS;
    }

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

            return ['valid' => true, 'errors' => []];
        } catch (ValidationException $e) {
            return [
                'valid' => false,
                'errors' => $e->validator->errors()->all(),
            ];
        }
    }

    protected function satelliteDetailsRules(): array
    {
        return [
            'satellite_type' => ['required', Rule::in(self::TIERS)],
            'satellite_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ];
    }

    protected function satelliteDetailsMessages(): array
    {
        return [
            'satellite_type.required' => 'Please select a membership type.',
            'satellite_type.in' => 'Please select a valid membership type.',
            'satellite_name.required' => 'Please enter a satellite name.',
            'address.required' => 'Please enter an address.',
        ];
    }

    protected function accountRules(): array
    {
        $ignoreSelf = $this->isReapply && $this->UserID
            ? (int) $this->UserID
            : null;

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
                ['string', 'confirmed', Password::default()]
            ),
        ];
    }

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
                    fn() => $this->payment_method !== ''
                        && $this->payment_method !== 'Cash'
                ),
            ],
            'uplineCode' => [
                'required',
                'string',
                'max:255',
            ],
            'agree' => ['accepted'],
        ];
    }

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

    protected function validateUplineCode(): bool
    {
        $uplineCode = trim($this->uplineCode);

        if ($uplineCode === '') {
            $this->addError('uplineCode', 'Please enter the upline code.');

            $this->dispatch('swal:error', [
                'title' => 'Upline Required',
                'text' => 'Please enter the upline code.',
            ]);

            return false;
        }

        $upline = User::where('referralCode', $uplineCode)->first();

        if (!$upline) {
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

            return false;
        }

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

        return true;
    }

    protected function assertPaymentMethodAvailable(): bool
    {
        if (!in_array(
            $this->payment_method,
            $this->getAvailablePaymentMethodNamesProperty(),
            true
        )) {
            $this->addError(
                'payment_method',
                'The selected payment method is currently unavailable.'
            );

            $this->dispatch('swal:error', [
                'title' => 'Payment Method Unavailable',
                'text' => 'The selected payment method is currently disabled.',
            ]);

            return false;
        }

        return true;
    }

    #[On('satellite-confirm-submit')]
    public function confirmSubmit(): void
    {
        $this->submit();
    }

    public function validateAndSubmit(): void
    {
        $this->uplineCode = trim($this->uplineCode);
        $this->reference_code = trim($this->reference_code);

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

        if (!$this->assertPaymentMethodAvailable()) {
            return;
        }

        if (!$this->validateUplineCode()) {
            return;
        }

        $this->dispatch(
            $this->isReapply
                ? 'satellite-reapply-confirmation'
                : 'satellite-submit-confirmation'
        );
    }

    public function submit(): void
    {
        if (!$this->open || $this->UserID === null) {
            return;
        }

        try {
            $isReapply = $this->isReapply;
            $member = User::findOrFail($this->UserID);
            $satellite = $this->getSatelliteInformation();
            $amount = $this->getMembershipAmountProperty();

            if ($amount <= 0) {
                throw new \RuntimeException(
                    "The membership amount for '{$this->satellite_type}' is not configured (SettingsID: {$satellite['settingsID']})."
                );
            }

            if (!$this->assertPaymentMethodAvailable()) {
                return;
            }

            $satelliteRoleID = DB::table('roles')
                ->where('rolename', 'Satellite')
                ->where('status', 1)
                ->value('RoleID');

            if (!$satelliteRoleID) {
                throw new \RuntimeException(
                    'Satellite role was not found or is inactive.'
                );
            }

            $processedBy = Auth::user()->UserID
                ?? throw new \RuntimeException(
                    'The authenticated user could not be identified.'
                );

            if (!$this->isReapply && User::where('email', $this->email)->exists()) {
                $this->addError(
                    'email',
                    'This email address is already registered.'
                );
                return;
            }

            $activationType = $this->getPaymentIsOnlineProperty() ? 'O' : 'W';
            $paymentReference = $activationType === 'O'
                ? trim($this->reference_code)
                : null;

            $uplineCode = trim($this->uplineCode);
            $upline = User::where('referralCode', $uplineCode)->first();

            if (!$upline) {
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

            if ((int) $upline->UserID === (int) $this->UserID) {
                $this->addError(
                    'uplineCode',
                    'You cannot use the selected member’s own referral code as the upline code.'
                );

                $this->dispatch('swal:error', [
                    'title' => 'Cannot Use Own Referral Code',
                    'text' => "Referral code '{$uplineCode}' belongs to the selected member.",
                ]);

                return;
            }

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
                $member,
                $satellite,
                $satelliteRoleID,
                $amount,
                $uplineCode,
                $processedBy,
                $activationType,
                $paymentReference
            ) {
                return $this->isReapply
                    ? $this->submitReapply(
                        $member,
                        $satellite,
                        $amount,
                        $uplineCode,
                        $processedBy,
                        $activationType,
                        $paymentReference
                    )
                    : $this->submitNewApplication(
                        $member,
                        $satellite,
                        $satelliteRoleID,
                        $amount,
                        $uplineCode,
                        $processedBy,
                        $activationType,
                        $paymentReference
                    );
            });

            Log::info(
                $isReapply
                    ? 'Satellite REAPPLICATION submitted'
                    : 'Satellite membership application created',
                [
                    'UserID' => $savedUser->UserID,
                    'satellite_type' => $this->satellite_type,
                    'satellite_name' => $this->satellite_name,
                    'amount' => $amount,
                    'membership_validity_months' => $this->getMembershipValidityProperty(),
                    'payment_method' => $this->payment_method,
                    'activation_type' => $activationType,
                    'has_payment_reference' => $paymentReference !== null,
                    'ProcessedBy' => $processedBy,
                ]
            );

            $this->close();

            $this->dispatch('swal:success', [
                'title' => $isReapply
                    ? 'Reapplication Submitted!'
                    : 'Application Submitted!',
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

    private function generateUplineCode(): string
    {
        return strtoupper(substr(
            md5(uniqid((string) mt_rand(), true)),
            0,
            6
        ));
    }

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

    private function submitReapply(
        User $member,
        array $satellite,
        float $amount,
        string $uplineCode,
        int $processedBy,
        string $activationType,
        ?string $paymentReference
    ): User {
        $member->refresh();

        $member->username = $this->username;
        $member->email = $this->email;
        $member->address = $this->address;
        $member->latitude = $this->latitude;
        $member->longitude = $this->longitude;
        $member->uplineCode = $uplineCode;

        if (filled($this->password)) {
            $member->password = Hash::make($this->password);
        }

        $member->save();

        $satelliteRecord = Satellite::where('UserID', $member->UserID)
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

    public function render(): View
    {
        return view(
            'livewire.admin-pages.masterlist.members-modals.satelite-modal'
        );
    }
}
