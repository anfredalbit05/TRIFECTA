<?php

namespace App\Livewire\AdminPages\TransactionList;

use App\Livewire\Concerns\AuthorizesModules;
use App\Livewire\Concerns\HasTabSkeleton;
use App\Support\Modules;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CocogenMembersInsuranceExport;

class CocogeMembersInsurance extends Component
{
    use HasTabSkeleton;
    use AuthorizesModules;
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';

    /**
     * Empty = all civil statuses.
     */
    public string $statusFilter = '';

    public bool $showBeneficiaryModal = false;

    public ?int $selectedInsuranceUserId = null;

    public string $selectedMemberName = '';

    public array $availableBeneficiaries = [];

    public ?int $selectedBeneficiaryId = null;

    public bool $showOccupationModal = false;

    public ?int $selectedOccupationUserId = null;

    public string $selectedOccupationMemberName = '';

    public string $occupationInput = '';

    protected function module(): int
    {
        return Modules::TXN_DISBURSEMENTS;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function resetTable(): void
    {
        $this->search = '';
        $this->statusFilter = '';

        $this->resetPage();
    }

    /**
     * Download the currently filtered records as an Excel file.
     */
    public function exportExcel()
    {
        // if (! $this->canRead) {
        //     $this->dispatch('swal:error', [
        //         'title' => 'Not Allowed',
        //         'message' => "You don't have permission to export membership insurance records.",
        //     ]);

        //     return;
        // }

        return Excel::download(
            new CocogenMembersInsuranceExport(
                $this->search,
                $this->statusFilter
            ),
            'cocogen-members-insurance-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Open the beneficiary selection modal for an insurance member.
     */
    public function openBeneficiaryModal(int $userId): void
    {
        $insurance = DB::table('cocogen_members_insurance')
            ->where('UserID', $userId)
            ->first();

        if (! $insurance) {
            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'message' => 'Insurance member record was not found.',
            ]);

            return;
        }

        $this->selectedInsuranceUserId = $userId;
        $this->selectedMemberName = $insurance->member_name;

        $beneficiaries = DB::table('beneficiaries')
            ->where('UserID', $userId)
            ->where('status', 1)
            ->orderBy('fullname')
            ->get();

        $this->availableBeneficiaries = $beneficiaries
            ->map(fn ($beneficiary) => [
                'BeneficiaryID' => $beneficiary->BeneficiaryID,
                'fullname' => $beneficiary->fullname,
                'birthdate' => $beneficiary->birthdate,
                'relationship' => $beneficiary->relationship,
                'status' => $beneficiary->status,
            ])
            ->toArray();

        // Automatically check the previously saved beneficiary.
        $savedName = trim($insurance->beneficiary_name ?? '');

        $existingBeneficiary = $beneficiaries->first(
            fn ($beneficiary) => trim($beneficiary->fullname) === $savedName
        );

        $this->selectedBeneficiaryId = $existingBeneficiary
            ? (int) $existingBeneficiary->BeneficiaryID
            : null;

        $this->resetErrorBag();

        $this->showBeneficiaryModal = true;
    }

    public function toggleBeneficiary(int $beneficiaryId): void
    {
        $exists = DB::table('beneficiaries')
            ->where('BeneficiaryID', $beneficiaryId)
            ->where('UserID', $this->selectedInsuranceUserId)
            ->where('status', 1)
            ->exists();

        if (! $exists) {
            return;
        }

        // Clicking the same checkbox again unchecks it.
        // Selecting another automatically replaces the previous one.
        $this->selectedBeneficiaryId =
            $this->selectedBeneficiaryId === $beneficiaryId
                ? null
                : $beneficiaryId;

        $this->resetErrorBag('selectedBeneficiaryId');
    }

    /**
     * Save selected beneficiaries to the insurance record.
     */
    public function saveBeneficiaries(): void
    {
        if (! $this->selectedInsuranceUserId) {
            return;
        }

        $userId = $this->selectedInsuranceUserId;

        $beneficiaryName = null;

        if ($this->selectedBeneficiaryId !== null) {

            $beneficiary = DB::table('beneficiaries')
                ->where('BeneficiaryID', $this->selectedBeneficiaryId)
                ->where('UserID', $userId)
                ->where('status', 1)
                ->first();

            if (! $beneficiary) {
                $this->addError(
                    'selectedBeneficiaryId',
                    'The selected beneficiary is invalid.'
                );

                return;
            }

            $beneficiaryName = $beneficiary->fullname;
        }

        DB::table('cocogen_members_insurance')
            ->where('UserID', $userId)
            ->update([
                'beneficiary_name' => $beneficiaryName,
                'updated_at' => now(),
            ]);

        $this->showBeneficiaryModal = false;

        $this->dispatch('swal:success', [
            'title' => 'Updated',
            'message' => 'Beneficiary has been updated successfully.',
        ]);
    }

    /**
     * Close the beneficiary modal.
     */
    public function closeBeneficiaryModal(): void
    {
        $this->showBeneficiaryModal = false;
        $this->resetErrorBag('selectedBeneficiaryIds');
    }

    /**
     * Open occupation modal.
     */
    public function openOccupationModal(int $userId): void
    {
        $insurance = DB::table('cocogen_members_insurance')
            ->where('UserID', $userId)
            ->first();

        if (! $insurance) {
            $this->dispatch('swal:error', [
                'title' => 'Not Found',
                'message' => 'Insurance member record was not found.',
            ]);

            return;
        }

        $this->selectedOccupationUserId = $userId;
        $this->selectedOccupationMemberName = $insurance->member_name;
        $this->occupationInput = $insurance->occupation ?? '';

        $this->resetErrorBag('occupationInput');

        $this->showOccupationModal = true;
    }

    /**
     * Save occupation.
     */
    public function saveOccupation(): void
    {
        $this->validate([
            'occupationInput' => ['nullable', 'string', 'max:255'],
        ]);

        if (! $this->selectedOccupationUserId) {
            return;
        }

        DB::table('cocogen_members_insurance')
            ->where('UserID', $this->selectedOccupationUserId)
            ->update([
                'occupation' => filled($this->occupationInput)
                    ? trim($this->occupationInput)
                    : null,
                'updated_at' => now(),
            ]);

        $this->showOccupationModal = false;

        $this->dispatch('swal:success', [
            'title' => 'Updated',
            'message' => 'Occupation has been updated successfully.',
        ]);
    }

    /**
     * Close occupation modal.
     */
    public function closeOccupationModal(): void
    {
        $this->showOccupationModal = false;
        $this->resetErrorBag('occupationInput');
    }

    public function render()
    {
        $query = DB::table('cocogen_members_insurance');

        $query->when(filled($this->search), function ($query) {
            $term = '%' . trim($this->search) . '%';

            $query->where(function ($q) use ($term) {
                $q->where('member_name', 'like', $term)
                    ->orWhere('beneficiary_name', 'like', $term)
                    ->orWhere('address', 'like', $term)
                    ->orWhere('contact_number', 'like', $term)
                    ->orWhere('plan', 'like', $term);
            });
        });

        $query->when(
            $this->statusFilter !== '',
            fn ($query) => $query->where(
                'civil_status',
                $this->statusFilter
            )
        );

        $fees = $query
            ->orderBy('CocogenID', 'desc')
            ->paginate(15);

        $civilStatuses = DB::table('cocogen_members_insurance')
            ->whereNotNull('civil_status')
            ->where('civil_status', '!=', '')
            ->distinct()
            ->orderBy('civil_status')
            ->pluck('civil_status');

        return view(
            'livewire.admin-pages.transaction-list.cocogen-members-insurance',
            [
                'fees' => $fees,
                'civilStatuses' => $civilStatuses,
            ]
        );
    }
}
