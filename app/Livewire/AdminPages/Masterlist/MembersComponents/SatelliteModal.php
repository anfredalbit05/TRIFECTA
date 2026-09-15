<?php

namespace App\Livewire\AdminPages\Masterlist\MembersComponents;

use App\Models\User;
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
    public bool $open = false;

    public ?int $UserID = null;
    public ?User $member = null;

    public string $satellite_type = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public string $payment_method = '';
    public string $reference_code = '';

    public bool $agree = false;

    #[On('open-satellite-modal')]
    public function openModal($UserID): void
    {
        $member = User::find($UserID);

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
        $this->open = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset([
            'UserID',
            'member',
            'satellite_type',
            'email',
            'password',
            'password_confirmation',
            'payment_method',
            'reference_code',
            'agree',
        ]);

        $this->resetValidation();
    }

    /*
    |--------------------------------------------------------------------------
    | Payment
    |--------------------------------------------------------------------------
    */

    public function updatedPaymentMethod(): void
    {
        if ($this->payment_method === 'Cash') {
            $this->reference_code = '';
        }

        $this->resetValidation([
            'payment_method',
            'reference_code',
        ]);
    }

    protected function paymentIsOnline(): bool
    {
        return in_array(
            $this->payment_method,
            ['GCash', 'Maya', 'Bank Transfer'],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Satellite
    |--------------------------------------------------------------------------
    */

    protected function getSatelliteInformation(): array
    {
        return match ($this->satellite_type) {
            'Bronze' => [
                'SatelliteID' => 1,
                'name' => 'Bronze',
                'setting' => 'satellite_bronze_coverage',
            ],

            'Silver' => [
                'SatelliteID' => 2,
                'name' => 'Silver',
                'setting' => 'satellite_silver_coverage',
            ],

            'Gold' => [
                'SatelliteID' => 3,
                'name' => 'Gold',
                'setting' => 'satellite_gold_coverage',
            ],

            default => throw new \RuntimeException(
                'Invalid satellite membership type.'
            ),
        };
    }

    protected function getSettingValue(string $name): ?string
    {
        return DB::table('settings')
            ->where('name', $name)
            ->value('value');
    }

    public function getMembershipAmountProperty(): float
    {
        if (! in_array(
            $this->satellite_type,
            ['Bronze', 'Silver', 'Gold'],
            true
        )) {
            return 0;
        }

        $satellite = $this->getSatelliteInformation();

        return (float) (
            $this->getSettingValue($satellite['setting']) ?? 0
        );
    }

    public function getMembershipValidityProperty(): int
    {
        return (int) (
            $this->getSettingValue('membership_validity_months') ?? 12
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    */

    public function getGcashEnabledProperty(): bool
    {
        return filter_var(
            $this->getSettingValue('gcash_payment_enabled') ?? false,
            FILTER_VALIDATE_BOOLEAN
        );
    }

    public function getBankPaymentEnabledProperty(): bool
    {
        return filter_var(
            $this->getSettingValue('bank_payment_enabled') ?? false,
            FILTER_VALIDATE_BOOLEAN
        );
    }

    public function getPaymayaEnabledProperty(): bool
    {
        /*
         * If you do not have this setting yet, Maya remains enabled.
         */
        $setting = $this->getSettingValue(
            'paymaya_payment_enabled'
        );

        return $setting === null
            ? true
            : filter_var(
                $setting,
                FILTER_VALIDATE_BOOLEAN
            );
    }

    public function getAvailablePaymentMethodsProperty(): array
    {
        $methods = ['Cash'];

        if ($this->gcashEnabled) {
            $methods[] = 'GCash';
        }

        if ($this->paymayaEnabled) {
            $methods[] = 'Maya';
        }

        if ($this->bankPaymentEnabled) {
            $methods[] = 'Bank Transfer';
        }

        return $methods;
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    protected function paymentRules(): array
    {
        return [
            'payment_method' => [
                'required',
                Rule::in($this->availablePaymentMethods),
            ],

            'reference_code' => [
                'nullable',
                'string',
                'max:255',
                'required_unless:payment_method,Cash',
            ],

            'agree' => [
                'accepted',
            ],
        ];
    }

    protected function paymentMessages(): array
    {
        return [
            'payment_method.required' =>
                'Please select a payment method.',

            'payment_method.in' =>
                'The selected payment method is not currently available.',

            'reference_code.required_unless' =>
                'Please enter the payment reference code.',

            'reference_code.max' =>
                'The payment reference code may not exceed 255 characters.',

            'agree.accepted' =>
                'You must agree to the terms and conditions.',
        ];
    }

    public function validateStep(int $step): bool
    {
        if ($step === 1) {
            $this->validate([
                'satellite_type' => [
                    'required',
                    Rule::in([
                        'Bronze',
                        'Silver',
                        'Gold',
                    ]),
                ],
            ], [
                'satellite_type.required' =>
                    'Please select a membership type.',

                'satellite_type.in' =>
                    'Please select a valid membership type.',
            ]);

            return true;
        }

        if ($step === 2) {
            $this->validate([
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
            ], [
                'email.required' =>
                    'Please enter an email address.',

                'email.email' =>
                    'Please enter a valid email address.',

                'email.unique' =>
                    'This email address is already registered.',

                'password.required' =>
                    'Please enter a password.',

                'password.confirmed' =>
                    'The password confirmation does not match.',
            ]);

            return true;
        }

        if ($step === 3) {
            $this->validate(
                $this->paymentRules(),
                $this->paymentMessages()
            );

            return true;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Submit
    |--------------------------------------------------------------------------
    */

      private function generateUplineCode(): string
    {
        return strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
    }

    public function submit(): void
    {
        $this->validate([
            'UserID' => [
                'required',
                'integer',
                'exists:users,UserID',
            ],

            'satellite_type' => [
                'required',
                Rule::in([
                    'Bronze',
                    'Silver',
                    'Gold',
                ]),
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

            ...$this->paymentRules(),

        ], [
            'UserID.required' =>
                'The selected member is required.',

            'satellite_type.required' =>
                'Please select a membership type.',

            'satellite_type.in' =>
                'Please select a valid membership type.',

            'email.required' =>
                'Please enter an email address.',

            'email.email' =>
                'Please enter a valid email address.',

            'email.unique' =>
                'This email address is already registered.',

            'password.required' =>
                'Please enter a password.',

            'password.confirmed' =>
                'The password confirmation does not match.',

            ...$this->paymentMessages(),
        ]);

        try {
            $member = User::findOrFail($this->UserID);

            $satellite = $this->getSatelliteInformation();

            $amount = $this->membershipAmount;

            if ($amount <= 0) {
                throw new \RuntimeException(
                    'The selected membership amount is not configured.'
                );
            }

            $satelliteRoleID = DB::table('roles')
                ->where('rolename', 'Satellite')
                ->where('status', 1)
                ->value('RoleID');

            if (! $satelliteRoleID) {
                throw new \RuntimeException(
                    'Satellite role was not found or is inactive.'
                );
            }

            $processedBy = Auth::user()?->UserID;

            if (! $processedBy) {
                throw new \RuntimeException(
                    'The authenticated user could not be identified.'
                );
            }

            if (User::where('email', $this->email)->exists()) {
                $this->addError(
                    'email',
                    'This email address is already registered.'
                );

                return;
            }

            $uplineCode =
                $member->referralCode
                ?: $member->uplineCode
                ?: '';

            /*
             * Cash = W (Walk-in)
             * Online = O (Online)
             */
            $activationType =
                $this->payment_method === 'Cash'
                    ? 'W'
                    : 'O';

            /*
             * Cash has no payment reference.
             * Online payments require one.
             */
            $paymentReference =
                $this->payment_method === 'Cash'
                    ? null
                    : trim($this->reference_code);

            $newUser = DB::transaction(function () use (
                $member,
                $satellite,
                $satelliteRoleID,
                $amount,
                $uplineCode,
                $processedBy,
                $activationType,
                $paymentReference
            ) {
                $newUser = new User();

                $newUser->username = $member->username;
                $newUser->password = Hash::make($this->password);

                $newUser->lastname = $member->lastname;
                $newUser->firstname = $member->firstname;
                $newUser->middlename = $member->middlename;
                $newUser->occupation = $member->occupation;
                $newUser->gender = $member->gender;

                $newUser->civilstatus = $member->civilstatus;
                $newUser->birthdate = $member->birthdate;
                $newUser->contactno = $member->contactno;
                $newUser->address = $member->address;
                $newUser->latitude = $member->latitude;
                $newUser->longitude = $member->longitude;

                $newUser->email = $this->email;
                $newUser->referralCode = $this->generateUplineCode();
                $newUser->uplineCode = $member->referralCode;


                $newUser->RoleID = $satelliteRoleID;
                $newUser->SatelliteID = $satellite['SatelliteID'];

                $newUser->status = 'Inactive';

                $newUser->save();

                DB::table('activations')->insert([
                    'UserID' => $newUser->UserID,
                    'amount' => $amount,

                    /*
                     * Cash = W
                     * GCash/Maya/Bank = O
                     */
                    'type' => $activationType,

                    'payment_method' =>
                        $this->payment_method,

                    'payment_reference' =>
                        $paymentReference,

                    'upline_code' =>
                        $uplineCode,

                    'ProcessedBy' =>
                        $processedBy,

                    'status' =>
                        'Pending',

                    'created_at' =>
                        now(),

                    'updated_at' =>
                        now(),
                ]);

                return $newUser;
            });

            Log::info(
                'Satellite membership application created',
                [
                    'original_UserID' =>
                        $member->UserID,

                    'new_UserID' =>
                        $newUser->UserID,

                    'SatelliteID' =>
                        $satellite['SatelliteID'],

                    'satellite_type' =>
                        $this->satellite_type,

                    'amount' =>
                        $amount,

                    'membership_validity_months' =>
                        $this->membershipValidity,

                    'payment_method' =>
                        $this->payment_method,

                    'activation_type' =>
                        $activationType,

                    'has_payment_reference' =>
                        $paymentReference !== null,

                    'ProcessedBy' =>
                        $processedBy,
                ]
            );

            $this->close();

            $this->dispatch('swal:success', [
                'title' =>
                    'Application Submitted!',

                'text' =>
                    'The Satellite membership application has been created successfully and is pending payment verification.',
            ]);

            $this->dispatch('members-refresh');

        } catch (\Throwable $e) {
            report($e);

            Log::error(
                'Satellite application failed',
                [
                    'UserID' =>
                        $this->UserID,

                    'satellite_type' =>
                        $this->satellite_type,

                    'payment_method' =>
                        $this->payment_method,

                    'error' =>
                        $e->getMessage(),
                ]
            );

            $this->dispatch('swal:error', [
                'title' =>
                    'Application Failed',

                'text' =>
                    'Something went wrong while creating the Satellite membership application.',
            ]);
        }
    }

    public function render()
    {
        return view(
            'components.admin-pages.masterlist.members-modals.satelite-modal'
        );
    }
}
