<?php

namespace App\Livewire\Components;

use App\Models\Beneficiary;
use App\Models\BeneficiaryHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class BeneficiaryModal extends Component
{
    public const MAX = 3;
    public const TZ  = 'Asia/Manila';

    public bool $open = false;
    public bool $confirmed = false;
    public ?int $userId = null;
    public string $memberName = '';
    public array $rows = [];

    /**
     * "Today" in Manila time. Used by BOTH the validation rule and the
     * date input's max attribute so they always agree.
     */
    public function today(): string
    {
        return Carbon::now(self::TZ)->toDateString();
    }

    #[On('open-beneficiary-modal')]
    public function openModal(int $UserID): void
    {
        $member = User::where('UserID', $UserID)->where('RoleID', 8)->first();

        if (! $member) {
            $this->dispatch('swal:error', ['title' => 'Not Found', 'text' => 'Member not found.']);
            return;
        }

        $this->userId = $member->UserID;
        $this->memberName = trim("{$member->firstname} {$member->lastname}");
        $this->confirmed = false;

        $this->rows = Beneficiary::where('UserID', $member->UserID)
            ->where('status', 1)
            ->orderBy('BeneficiaryID')
            ->limit(self::MAX)
            ->get()
            ->map(fn ($b) => [
                'id'           => $b->BeneficiaryID,
                'fullname'     => $b->fullname,
                'birthdate'    => optional($b->birthdate)->format('Y-m-d'),
                'relationship' => $b->relationship,
                'remove'       => false,
            ])
            ->all();

        while (count($this->rows) < self::MAX) {
            $this->rows[] = ['id' => null, 'fullname' => '', 'birthdate' => '', 'relationship' => '', 'remove' => false];
        }

        $this->resetErrorBag();
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
        $this->reset(['userId', 'memberName', 'rows', 'confirmed']);
    }

    /**
     * Validate only rows that are filled in and not being removed.
     */
    private function validateRows(): void
    {
        $today = $this->today();

        foreach ($this->rows as $i => $row) {
            if ($row['remove'] || (! $row['id'] && blank($row['fullname']))) {
                continue;
            }

            $this->validate([
                "rows.$i.fullname"     => 'required|string|max:150',
                "rows.$i.birthdate"    => [
                    'required',
                    'date_format:Y-m-d',
                    'after_or_equal:1900-01-01',
                    'before_or_equal:'.$today,
                ],
                "rows.$i.relationship" => 'nullable|string|max:50',
            ], [
                "rows.$i.birthdate.date_format"     => 'Beneficiary '.($i + 1).' birthdate is not a valid date.',
                "rows.$i.birthdate.after_or_equal"  => 'Beneficiary '.($i + 1).' birthdate is too far in the past.',
                "rows.$i.birthdate.before_or_equal" => 'Beneficiary '.($i + 1).' birthdate cannot be after '.Carbon::parse($today)->format('M d, Y').'.',
            ], [
                "rows.$i.fullname"     => 'beneficiary '.($i + 1).' full name',
                "rows.$i.birthdate"    => 'beneficiary '.($i + 1).' birthdate',
                "rows.$i.relationship" => 'beneficiary '.($i + 1).' relationship',
            ]);
        }
    }

    private function fmtDate(?string $date): ?string
    {
        return filled($date) ? Carbon::parse($date)->format('M d, Y') : null;
    }

    /**
     * Compare the form rows against the database and return a
     * human-readable list of what is about to change.
     */
    private function computeChanges(): array
    {
        $existing = Beneficiary::where('UserID', $this->userId)
            ->where('status', 1)
            ->get()
            ->keyBy('BeneficiaryID');

        $changes = [];

        foreach ($this->rows as $i => $row) {
            $label    = 'Beneficiary '.($i + 1);
            $fullname = trim((string) $row['fullname']);
            $rel      = filled($row['relationship']) ? trim($row['relationship']) : null;

            // ---------- EXISTING ----------
            if ($row['id']) {
                $b = $existing->get($row['id']);

                if (! $b) {
                    continue;
                }

                $old = [
                    'Full name'    => $b->fullname,
                    'Birthdate'    => $this->fmtDate(optional($b->birthdate)->format('Y-m-d')),
                    'Relationship' => $b->relationship,
                ];

                if ($row['remove']) {
                    $changes[] = [
                        'type'   => 'Removed',
                        'label'  => $label,
                        'fields' => collect($old)->map(fn ($v, $k) => ['field' => $k, 'old' => $v, 'new' => null])->values()->all(),
                    ];
                    continue;
                }

                $new = [
                    'Full name'    => $fullname,
                    'Birthdate'    => $this->fmtDate($row['birthdate']),
                    'Relationship' => $rel,
                ];

                $fields = [];
                foreach ($old as $k => $v) {
                    if ($v !== $new[$k]) {
                        $fields[] = ['field' => $k, 'old' => $v, 'new' => $new[$k]];
                    }
                }

                if ($fields) {
                    $changes[] = ['type' => 'Updated', 'label' => $label, 'fields' => $fields];
                }

                continue;
            }

            // ---------- NEW ----------
            if ($fullname === '' || $row['remove']) {
                continue;
            }

            $changes[] = [
                'type'   => 'Added',
                'label'  => $label,
                'fields' => [
                    ['field' => 'Full name',    'old' => null, 'new' => $fullname],
                    ['field' => 'Birthdate',    'old' => null, 'new' => $this->fmtDate($row['birthdate'])],
                    ['field' => 'Relationship', 'old' => null, 'new' => $rel],
                ],
            ];
        }

        return $changes;
    }

    /**
     * Step 1: validate, build the change summary, and ask the browser
     * to show the SweetAlert confirmation.
     */
    public function reviewChanges(): void
    {
        if (! $this->confirmed) {
            return;
        }

        $this->validateRows();

        $changes = $this->computeChanges();

        if (empty($changes)) {
            $this->dispatch('swal:info', [
                'title' => 'No Changes',
                'text'  => 'Nothing was changed.',
            ]);

            return;
        }

        $this->dispatch(
            'beneficiary-confirm-save',
            name: $this->memberName,
            changes: $changes,
        );
    }

    /**
     * Step 2: called from the SweetAlert confirm button.
     */
    public function save(): void
    {
        if (! $this->confirmed) {
            return;
        }

        $this->validateRows();

        try {
            $changes = 0;

            DB::transaction(function () use (&$changes) {
                foreach ($this->rows as $row) {
                    $fullname = trim((string) $row['fullname']);

                    // ---------- EXISTING ROW ----------
                    if ($row['id']) {
                        $b = Beneficiary::where('BeneficiaryID', $row['id'])
                            ->where('UserID', $this->userId)
                            ->lockForUpdate()
                            ->first();

                        if (! $b) {
                            continue;
                        }

                        $old = [
                            'fullname'     => $b->fullname,
                            'birthdate'    => optional($b->birthdate)->format('Y-m-d'),
                            'relationship' => $b->relationship,
                            'status'       => (int) $b->status,
                        ];

                        if ($row['remove']) {
                            $b->update(['status' => 0]);
                            $this->log($b->BeneficiaryID, 'Removed', $old, array_merge($old, ['status' => 0]));
                            $changes++;
                            continue;
                        }

                        $new = [
                            'fullname'     => $fullname,
                            'birthdate'    => $row['birthdate'],
                            'relationship' => filled($row['relationship']) ? trim($row['relationship']) : null,
                            'status'       => 1,
                        ];

                        if ($old !== $new) {
                            $b->update($new);
                            $this->log($b->BeneficiaryID, 'Updated', $old, $new);
                            $changes++;
                        }

                        continue;
                    }

                    // ---------- NEW ROW ----------
                    if ($fullname === '' || $row['remove']) {
                        continue;
                    }

                    $new = [
                        'fullname'     => $fullname,
                        'birthdate'    => $row['birthdate'],
                        'relationship' => filled($row['relationship']) ? trim($row['relationship']) : null,
                        'status'       => 1,
                    ];

                    $b = Beneficiary::create($new + ['UserID' => $this->userId]);
                    $this->log($b->BeneficiaryID, 'Created', null, $new);
                    $changes++;
                }
            });

            $name = $this->memberName;
            $this->close();

            $this->dispatch('swal:success', [
                'title' => $changes ? 'Beneficiaries Updated' : 'No Changes',
                'text'  => $changes
                    ? "{$name}'s beneficiaries were saved."
                    : 'Nothing was changed.',
            ]);
        } catch (\Throwable $e) {
            report($e);
            Log::error('Beneficiary save failed: '.$e->getMessage());

            $this->dispatch('swal:error', [
                'title' => 'Error',
                'text'  => 'Something went wrong while saving beneficiaries.',
            ]);
        }
    }

    private function log(int $beneficiaryId, string $action, ?array $old, array $new): void
    {
        BeneficiaryHistory::create([
            'BeneficiaryID'    => $beneficiaryId,
            'UserID'           => $this->userId,
            'action'           => $action,

            'old_fullname'     => $old['fullname'] ?? null,
            'old_birthdate'    => $old['birthdate'] ?? null,
            'old_relationship' => $old['relationship'] ?? null,
            'old_status'       => $old['status'] ?? null,

            'new_fullname'     => $new['fullname'],
            'new_birthdate'    => $new['birthdate'],
            'new_relationship' => $new['relationship'],
            'new_status'       => $new['status'],

            'changed_by'       => Auth::id(),
        ]);
    }

    public function render()
    {
        return view('livewire.components.beneficiary-modal');
    }
}
