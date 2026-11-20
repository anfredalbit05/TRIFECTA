<?php

namespace App\Livewire;

use App\Models\Activation;
use App\Models\Beneficiary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\EmploymentType;
use App\Models\MemberType;

class ApplicationModal extends Component
{
    private const BLANK = ['fullname' => '', 'birthdate' => '', 'relationship' => ''];

    private const LABELS = [
        'surname' => 'Surname', 'first_name' => 'First Name', 'middle_name' => 'Middle Name',
        'occupation' => 'Occupation', 'gender' => 'Gender', 'civil_status' => 'Civil Status',
        'employment_type_id' => 'Employment Type',
        'member_type_id' => 'Member Type',
        'birthdate' => 'Birthdate', 'address' => 'Address', 'mobile' => 'Mobile Number',
        'email' => 'Email', 'username' => 'Username', 'policy_number' => 'Policy Number',
        'policy_effectivity_date' => 'Policy Effectivity Date',
        'policy_expiration_date' => 'Policy Expiration Date',
        'receipt_number' => 'Receipt Number', 'cocogen_policy_number' => 'Cocogen Policy Number',
    ];

    private const BEN_RULES = [
        'beneficiaries.*.fullname' => 'required_with:beneficiaries.*.birthdate|nullable|string|max:150',
        'beneficiaries.*.birthdate' => 'required_with:beneficiaries.*.fullname|nullable|date',
        'beneficiaries.*.relationship' => 'nullable|string|max:50',
    ];

    public bool $open = false;

    public int $step = 1;

    public bool $showMapModal = false;

    public bool $isEditMode = false;

    public bool $agree = false;

    public ?float $latitude = null;

    public ?float $longitude = null;

    public ?int $userId = null;

    public ?int $activationId = null;

    public $surname = '', $first_name = '', $middle_name = '', $occupation = '', $gender = '',
        $civil_status = '', $birthdate = '', $address = '', $upline = '';

    public $mobile = '', $email = '', $username = '', $password = '', $password_confirmation = '';

    public $policy_number = null, $issuedPolicyNumber = null, $policy_effectivity_date = null,
        $policy_expiration_date = null, $receipt_number = null, $cocogen_policy_number = null,
        $activationControlNumber = null;

    public array $beneficiaries = [self::BLANK, self::BLANK, self::BLANK];

    /** DB id per beneficiary slot (null = not saved yet → "Clear", else "Delete"). */
    public array $beneficiaryIds = [null, null, null];

    /** Snapshot of the record when opened for edit, used for the "what changed" diff. */
    public array $originalData = [];

    public $employment_type_id = null;

    public $member_type_id = null;

    #[Computed]
    public function previewPolicyNumber(): string
    {
        return Activation::nextPolicyNumber();
    }

    #[Computed]
    public function previewControlNumber(): string
    {
        return Activation::nextReceiptNumber();
    }

    public function generatePolicyNumber(): void
    {
        $this->policy_number = $this->previewPolicyNumber;
    }

    public function generateControlNumber(): void
    {
        if ($this->activationId) {
            $this->receipt_number = Activation::nextReceiptNumber();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Open / Close / Steps
    |--------------------------------------------------------------------------
    */
    #[On('open-application-modal')]
    public function open($upline = null)
    {
        $this->resetValidation();
        $this->open = true;

        if ($upline) {
            $this->upline = $upline;
        }
    }

    #[On('open-update-member')]
    public function openEdit($UserID): void
    {
        $this->fillUserData(User::with('beneficiaries')->findOrFail($UserID));
        $this->userId = $UserID;
        $this->isEditMode = true;
        $this->open = true;
    }

    public function close()
    {
        $this->dispatch('destroy-map');
        $this->resetValidation();
        $this->reset(array_diff(array_keys($this->all()), ['upline']));
    }

    public function next(): void
    {
        if ($this->step === 1 && ! $this->check($this->rules1())) {
            return;
        }

        if ($this->step === 2 && ! $this->validateBeneficiaries()) {
            return;
        }

        $this->step = min(3, $this->step + 1);
    }

    public function back(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    /*
    |--------------------------------------------------------------------------
    | Real-time formatting
    |--------------------------------------------------------------------------
    */
    public function updated($property, $value)
    {
        if ($property === 'mobile') {
            $v = (string) $value;

            if (trim($v) === '') {
                $this->mobile = '';

                return;
            }

            if (str_starts_with($v, '+63')) {
                $v = '09'.substr($v, 3);
            } elseif (str_starts_with($v, '63') && strlen($v) === 12) {
                $v = '0'.substr($v, 2);
            }

            $v = preg_replace('/\D/', '', $v);

            if ($v !== '' && $v[0] !== '0') {
                $v = '0'.$v;
            }

            if (strlen($v) > 1 && $v[1] !== '9') {
                $v = '09'.substr($v, 2);
            }

            $this->mobile = substr($v, 0, 11);

            return;
        }

        if (! is_string($value)) {
            return;
        }

        if (in_array($property, [
            'surname',
            'first_name',
            'middle_name',
            'occupation',
            'address',
        ])) {
            $this->{$property} = $this->formatName($value);

        } elseif (preg_match('/^beneficiaries\.(\d+)\.(fullname|relationship)$/', $property, $m)) {
            $this->beneficiaries[$m[1]][$m[2]] = $this->formatName($value);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    private function rules1(): array
    {
        return [
            'surname' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'gender' => 'nullable|string',
            'civil_status' => 'nullable|string',
            'birthdate' => 'required|date',
            'occupation' => 'nullable|string|max:255',

            'employment_type_id' => 'nullable|exists:employment_types,employment_type_id',
            'member_type_id' => 'nullable|exists:member_types,member_type_id',

            'address' => 'required|string|max:500',
        ] + ($this->isEditMode ? [
            // Policy and receipt numbers are already issued.
            // Only the policy dates and Cocogen number are editable.
            'policy_effectivity_date' => 'nullable|date',
            'policy_expiration_date' => 'nullable|date',
            'cocogen_policy_number' => 'nullable|string|max:255',
        ] : []);
    }


    private function rules3(): array
    {
        return [
            'mobile' => 'nullable|digits:11',
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($this->userId, 'UserID')],
            'username' => ['required', 'string', 'max:100',
                Rule::unique('users', 'username')->ignore($this->userId, 'UserID')],
        ] + ($this->isEditMode ? [] : ['password' => 'required|min:8|confirmed', 'agree' => 'accepted']);
    }

    private function normalize(): void
    {
        foreach (['policy_number', 'policy_effectivity_date', 'policy_expiration_date',
            'receipt_number', 'cocogen_policy_number'] as $f) {
            $this->{$f} = blank($this->{$f}) ? null : trim($this->{$f});
        }
    }

    /** Validate; on failure show the SweetAlert error list and return false. */
    private function check(array $rules, bool $policy = false): bool
    {
        $this->normalize();

        try {
            $this->validate($rules);

            if ($policy) {
                $this->validateManualPolicyNumber();
                $this->validateManualReceiptNumber();
            }

            return true;
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->errors());

            $items = collect($e->validator->errors()->all())
                ->map(fn ($m) => "<li style='margin-bottom:6px'>{$m}</li>")->implode('');

            $this->dispatch('validation-error', title: 'Missing or Invalid Fields',
                html: "<ul style='text-align:left; list-style-type:disc; padding-left:20px;'>{$items}</ul>");

            return false;
        }
    }

    private function validateBeneficiaries(): bool
    {
        $fields = ['fullname' => 'Full Name', 'birthdate' => 'Birthdate', 'relationship' => 'Relationship'];

        foreach ($this->beneficiaries as $i => $b) {
            $b = array_map(fn ($v) => trim((string) $v), $b);

            if (implode('', $b) === '') {
                continue;
            }

            foreach ($fields as $f => $label) {
                if (($b[$f] ?? '') === '') {
                    $n = $i + 1;
                    $this->dispatch('validation-error', title: "Beneficiary {$n} {$label} Required",
                        html: "<p><b>{$label} is required for Beneficiary {$n}.</b></p>");

                    return false;
                }
            }
        }

        return true;
    }

    private function validateManualPolicyNumber(): void
    {
        if (blank($this->policy_number)) {
            return;
        }

        $number = strtoupper(trim($this->policy_number));
        $fail = fn ($m) => throw ValidationException::withMessages(['policy_number' => $m]);

        if (! preg_match('/^TRI-(\d+)-TS(\d{4})-(\d+)$/', $number, $m)) {
            $fail('Invalid policy number format. Expected format: TRI-00557-TS2026-557.');
        }

        [, $padded, $year, $seq] = array_map('intval', $m);

        if ($year !== now()->year) {
            $fail('The policy number must use the current year '.now()->year.'.');
        }

        if ($padded !== $seq) {
            $fail('The policy number sequence is invalid. The numbers must match.');
        }

        // Number may live in users OR activations — check both.
        $inUsers = User::where('policy_number', $number)
            ->when($this->userId, fn ($q) => $q->where('UserID', '!=', $this->userId));
        $inActivations = Activation::where('policy_number', $number)
            ->when($this->activationId, fn ($q) => $q->where('ActivationID', '!=', $this->activationId));

        if ($inUsers->exists() || $inActivations->exists()) {
            $fail("Policy number {$number} already exists.");
        }

        // Backlog numbers below the next one are fine; jumping ahead is not.
        $next = max(Activation::FIRST_POLICY_SEQUENCE, Activation::latestPolicySequence() + 1);

        if ($seq > $next) {
            $fail("Policy number {$number} cannot be used because it jumps ahead of the latest sequence. "
                .'The next available policy number is '.sprintf('TRI-%05d-TS%d-%d', $next, $year, $next).'.');
        }

        $this->policy_number = sprintf('TRI-%05d-TS%d-%d', $seq, $year, $seq);
    }

    private function validateManualReceiptNumber(): void
    {
        if (blank($this->receipt_number)) {
            return;
        }

        $receipt = trim($this->receipt_number);
        $fail = fn ($m) => throw ValidationException::withMessages(['receipt_number' => $m]);

        if (! preg_match('/^\d{6}$/', $receipt)) {
            $fail('Receipt number must contain exactly 6 digits.');
        }

        $duplicate = Activation::where('receipt_number', $receipt)
            ->when($this->activationId, fn ($q) => $q->where('ActivationID', '!=', $this->activationId));

        if ($duplicate->exists()) {
            $fail("Receipt number {$receipt} already exists.");
        }

        $next = (int) Activation::nextReceiptNumber();

        if ((int) $receipt > $next) {
            $fail("Receipt number {$receipt} cannot be used because it jumps ahead of the latest sequence. "
                .'The next available receipt number is '.str_pad($next, 6, '0', STR_PAD_LEFT)
                .'. Please use Generate or enter a backlog number.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Beneficiary Clear / Delete
    |--------------------------------------------------------------------------
    */
    public function confirmDeleteBeneficiary($index)
    {
        if (array_key_exists($index, $this->beneficiaries)) {
            $this->dispatch('confirm-delete-beneficiary', index: $index + 1,
                persisted: ! empty($this->beneficiaryIds[$index] ?? null));
        }
    }

    #[On('delete-beneficiary')]
    public function deleteBeneficiary($index)
    {
        if (! isset($this->beneficiaries[$index])) {
            return;
        }

        $id = $this->beneficiaryIds[$index] ?? null;

        if ($id) {
            Beneficiary::whereKey($id)->where('UserID', $this->userId)->delete();
            $this->beneficiaryIds[$index] = null;
        }

        $this->beneficiaries[$index] = self::BLANK;
        $this->dispatch('beneficiary-deleted', index: $index + 1, persisted: (bool) $id);
    }

    private function saveBeneficiaries(int $userId): void
    {
        foreach ($this->beneficiaries as $b) {
            if (filled($b['fullname']) && filled($b['birthdate'])) {
                Beneficiary::create([
                    'UserID' => $userId,
                    'fullname' => trim($b['fullname']),
                    'birthdate' => $b['birthdate'],
                    'relationship' => trim($b['relationship'] ?? '') ?: null,
                    'status' => true,
                ]);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | New Application
    |--------------------------------------------------------------------------
    */
    public function submit()
    {
        if (! $this->validateBeneficiaries()
            || ! $this->check($this->rules1() + self::BEN_RULES + $this->rules3())) {
            return;
        }

        $matches = $this->findExistingMemberMatches();

        if ($matches) {
            $this->dispatch('confirm-duplicate-member', matches: $matches, applicant: [
                'name' => trim(implode(' ', array_filter([$this->first_name, $this->middle_name, $this->surname]))),
                'mobile' => $this->mobile,
            ]);

            return;
        }

        $this->dispatch('swal-confirm-submission');
    }

    #[On('confirmed-proceed-duplicate-member')]
    public function proceedWithDuplicateMember(): void
    {
        $this->dispatch('swal-confirm-submission');
    }

    #[On('confirmed-save-application')]
    public function saveApplication(): void
    {
        try {
            $user = DB::transaction(function () {
                do {
                    $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
                } while (User::where('referralCode', $code)->exists());

                $user = User::create([
                    'username' => $this->username,
                    'password' => bcrypt($this->password),
                    'referralCode' => $code,
                    'uplineCode' => $this->upline,
                    'lastname' => $this->surname,
                    'firstname' => $this->first_name,
                    'middlename' => $this->middle_name,
                    'gender' => filled($this->gender) ? $this->gender : null,
                    'civilstatus' => filled($this->civil_status) ? $this->civil_status : null,
                    'birthdate' => $this->birthdate,
                    'occupation' => $this->occupation,
                    'employment_type_id' => $this->employment_type_id ?: null,
                    'member_type_id' => $this->member_type_id ?: null,
                    'contactno' => filled($this->mobile) ? $this->mobile : null,
                    'email' => filled($this->email) ? $this->email : null,
                    'address' => $this->address,
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'RoleID' => 8,
                    'status' => 'Inactive',
                ]);

                $this->saveBeneficiaries($user->UserID);

                return $user;
            });
        } catch (\Throwable $e) {
            Log::error('APPLICATION DATABASE FAILED', [
                'message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile(),
            ]);

            $this->dispatch('swal-close');
            $this->dispatch('validation-error', title: 'Error',
                html: 'Something went wrong: '.htmlspecialchars($e->getMessage()));

            return;
        }

        $this->finish('Success!', 'Application submitted successfully.');
    }

    private function finish(string $title, string $text): void
    {
        $this->dispatch('swal-close');
        $this->dispatch('show-success-after-close', title: $title, text: $text);
        $this->dispatch('show-toast', title: $text, icon: 'success');
        $this->dispatch('refresh-members');
        $this->close();
    }

    /*
    |--------------------------------------------------------------------------
    | Update Member
    |--------------------------------------------------------------------------
    */

    public function confirmUpdate()
    {
        if (! $this->check($this->rules1() + $this->rules3())) {
            return;
        }

        $changes = $this->getChangedFields();

        if (! $changes) {
            $this->dispatch(
                'validation-error',
                title: 'No Changes',
                html: 'There are no changes to save.'
            );

            return;
        }

        $this->dispatch('swal-confirm-update', changes: $changes);
    }


    private function getChangedFields(): array
    {
        $changes = [];

        foreach (self::LABELS as $f => $label) {
            $old = (string) ($this->originalData[$f] ?? '');
            $new = (string) $this->{$f};

            if ($old !== $new) {
                $changes[] = ['field' => $label, 'old' => $old ?: '—', 'new' => $new ?: '—'];
            }
        }
        return $changes;
    }


    #[On('confirmed-update-member')]
    public function updateMember(): void
    {
        if (! $this->check($this->rules1() + self::BEN_RULES + $this->rules3())) {
            return;
        }

        try {
            DB::transaction(function () {
                $user = User::findOrFail($this->userId);

                $user->update([
                    'lastname' => $this->surname,
                    'firstname' => $this->first_name,
                    'middlename' => $this->middle_name,
                    'gender' => filled($this->gender) ? $this->gender : null,
                    'civilstatus' => filled($this->civil_status) ? $this->civil_status : null,
                    'birthdate' => $this->birthdate,
                    'occupation' => $this->occupation,
                    'employment_type_id' => $this->employment_type_id ?: null,
                    'member_type_id' => $this->member_type_id ?: null,
                    'contactno' => filled($this->mobile) ? $this->mobile : null,
                    'email' => filled($this->email) ? $this->email : null,
                    'username' => $this->username,
                    'address' => $this->address,
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                ]);

                // Update only editable policy information.
                // Never modify the already-issued policy or receipt numbers.
                if ($this->activationId) {
                    Activation::where('ActivationID', $this->activationId)
                        ->update([
                            'cocogen_policy_number' => $this->cocogen_policy_number,
                            'policy_effectivity_date' => $this->policy_effectivity_date,
                            'policy_expiration_date' => $this->policy_expiration_date,
                        ]);
                }

            });
        } catch (\Throwable $e) {
            Log::error('Update failed', [
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('swal-close');
            $this->dispatch('validation-error',
                title: 'Error',
                html: 'Failed to update member.'
            );

            return;
        }

        $this->finish('Updated!', 'Member updated successfully.');
    }


    private function fillUserData(User $u): void
    {
        $date = fn ($d) => $d ? Carbon::parse($d)->format('Y-m-d') : null;

        $a = Activation::where('UserID', $u->UserID)->where('status', 'Approved')
            ->latest('ActivationID')->first();

        $this->fill([
            'surname' => $u->lastname ?? '',
            'first_name' => $u->firstname ?? '',
            'middle_name' => $u->middlename ?? '',
            'occupation' => $u->occupation ?? '',
            'employment_type_id' => $u->employment_type_id,
            'member_type_id' => $u->member_type_id,
            'gender' => $u->gender ?? '',
            'civil_status' => $u->civilstatus ?? '',
            'birthdate' => $date($u->birthdate) ?? '',
            'address' => $u->address ?? '',
            'latitude' => $u->latitude,
            'longitude' => $u->longitude,
            'mobile' => $u->contactno ?? '',
            'email' => $u->email ?? '',
            'username' => $u->username ?? '',
            'activationId' => $a?->ActivationID,
            'activationControlNumber' => $a?->receipt_number,
            'receipt_number' => $a?->receipt_number,
            'issuedPolicyNumber' => $a?->policy_number,
            'policy_number' => $a?->policy_number ?: null,
            'cocogen_policy_number' => $a?->cocogen_policy_number,
            'policy_effectivity_date' => $date($a?->policy_effectivity_date),
            'policy_expiration_date' => $date($a?->policy_expiration_date),
            'beneficiaries' => array_fill(0, 3, self::BLANK),
            'beneficiaryIds' => [null, null, null],
        ]);

        foreach ($u->beneficiaries->take(3)->values() as $i => $b) {
            $this->beneficiaryIds[$i] = $b->getKey();
            $this->beneficiaries[$i] = [
                'fullname' => $b->fullname ?? '',
                'birthdate' => $date($b->birthdate) ?? '',
                'relationship' => $b->relationship ?? '',
            ];
        }

        $this->originalData = $this->only(array_keys(self::LABELS)) + ['beneficiaries' => $this->beneficiaries];
    }

    /*
    |--------------------------------------------------------------------------
    | Duplicate member check (name or contact number)
    |--------------------------------------------------------------------------
    */
    private function findExistingMemberMatches(): array
    {
        $n = fn ($v) => mb_strtolower(trim($v ?? ''));
        $d = fn ($v) => preg_replace('/\D/', '', $v ?? '');
        [$first, $middle, $last, $mobile] = [$n($this->first_name), $n($this->middle_name), $n($this->surname), $d($this->mobile)];

        return User::get(['UserID', 'firstname', 'middlename', 'lastname', 'contactno'])
            ->map(function ($u) use ($n, $d, $first, $middle, $last, $mobile) {
                $sameName = $first === $n($u->firstname) && $last === $n($u->lastname);
                $sameContact = $mobile !== '' && $mobile === $d($u->contactno);

                if (! $sameName && ! $sameContact) {
                    return null;
                }

                $reasons = [];

                if ($sameName) {
                    $reasons[] = $middle === $n($u->middlename) ? 'Exact full name match' : 'First name and last name match';
                }

                if ($sameContact) {
                    $reasons[] = 'Contact number already registered';
                }

                return [
                    'name' => trim(implode(' ', array_filter([$u->firstname, $u->middlename, $u->lastname]))),
                    'mobile' => $u->contactno,
                    'reasons' => $reasons,
                ];
            })
            ->filter()
            ->unique(fn ($m) => mb_strtolower($m['name']).'|'.$d($m['mobile']))
            ->values()->take(10)->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Map
    |--------------------------------------------------------------------------
    */
    public function toggleMap(): void
    {
        $this->showMapModal = ! $this->showMapModal;

        if ($this->showMapModal) {
            $this->dispatch('map-opened', latitude: $this->latitude, longitude: $this->longitude);
        }
    }

    #[On('set-coordinates')]
    public function setCoordinates($lat, $lng, $address = null): void
    {
        $this->latitude = (float) $lat;
        $this->longitude = (float) $lng;

        if (! empty($address)) {
            $this->address = ucwords(strtolower($address));
        }
    }

    private function formatName(string $value): string
    {
        $value = mb_convert_case(mb_strtolower(trim($value)), MB_CASE_TITLE, 'UTF-8');

        // Preserve repeated-character uppercase abbreviations (AA, AAA, etc.)
        $value = preg_replace_callback('/\b([A-Z])\1{1,}\b/i', function ($matches) {
            return strtoupper($matches[0]);
        }, $value);

        // Preserve Roman numeral suffixes (II, III, IV, V, VI, etc.)
        $value = preg_replace_callback(
            '/\b(I|II|III|IV|V|VI|VII|VIII|IX|X)\b/i',
            fn ($matches) => strtoupper($matches[0]),
            $value
        );

        return $value;
    }

    public function render()
    {
        return view('livewire.application-modal', [
            'employmentTypes' => EmploymentType::orderBy('name')->get(),
            'memberTypes' => MemberType::orderBy('name')->get(),
        ]);
    }
}
