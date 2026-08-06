<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;


class Members extends Component
{
    use WithPagination;

    public string $search = '';

    public string $summaryFilter = 'all';

    // Excel Export Filters
    public $exportYear = '';
    public $exportMonth = '';
    public $exportStatus = '';

    public bool $skipRender = true;

    protected $paginationTheme = 'tailwind';

    /*
    |--------------------------------------------------------------------------
    | Permission Checkers
    |--------------------------------------------------------------------------
    */

    protected function hasPermission($action): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Super Admin override (RoleID 1)
        if ((int) $user->RoleID === 1) {
            return true;
        }

        $column = 'can_' . $action;

        // Safely check role permissions or fallback to direct query
        if ($user->role && method_exists($user->role, 'permissions')) {
            return optional($user->role->permissions)
                ->where('ModuleID', 2)
                ->where($column, 1)
                ->isNotEmpty();
        }

        // Fallback direct check against privileges table
        return \App\Models\Privilege::where('RoleID', $user->RoleID)
            ->where('ModuleID', 2)
            ->where($column, 1)
            ->exists();
    }

    public function getCanCreateProperty(): bool
    {
        return $this->hasPermission('create'); // checks can_create
    }

    public function getCanReadProperty(): bool
    {
        return $this->hasPermission('read'); // checks can_read
    }

    public function getCanUpdateProperty(): bool
    {
        return $this->hasPermission('update'); // checks can_update
    }

    public function getCanDeleteProperty(): bool
    {
        return $this->hasPermission('delete'); // checks can_delete
    }

    public function getSummaryStatsProperty(): array
    {
        $query = User::query()->where('RoleID', 4);

        if ($this->summaryFilter === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($this->summaryFilter === 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($this->summaryFilter === 'month') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        } elseif ($this->summaryFilter === 'year') {
            $query->whereYear('created_at', now()->year);
        }

        $total = (clone $query)->count();
        $active = (clone $query)->whereRaw('LOWER(status) = ?', ['active'])->count();
        $inactive = (clone $query)->whereRaw('LOWER(status) != ?', ['active'])->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Export to Excel CSV Method
    |--------------------------------------------------------------------------
    */
    public function exportExcel(): StreamedResponse
    {
        $query = User::query()->where('RoleID', 4);

        // Filter by Year if selected
        if (!empty($this->exportYear)) {
            $query->whereYear('created_at', $this->exportYear);
        }

        // Filter by Month if selected
        if (!empty($this->exportMonth)) {
            $query->whereMonth('created_at', $this->exportMonth);
        }

        // Filter by Status if selected
        if (!empty($this->exportStatus)) {
            if (strtolower($this->exportStatus) === 'active') {
                $query->whereRaw('LOWER(status) = ?', ['active']);
            } else {
                $query->whereRaw('LOWER(status) != ?', ['active']);
            }
        }

        $members = $query->latest()->get();

        $fileName = 'members_report_' . date('Y-m-d_H-i-s') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response()->stream(function () use ($members) {
            echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta http-equiv="content-type" content="text/html; charset=UTF-8"></head>';
            echo '<body>';
            echo '<table border="1">';

            // Header Row Style
            echo '<tr style="background-color: #0C2C6B; color: #FFFFFF; font-weight: bold;">';
            echo '<th>ID</th>';
            echo '<th>First Name</th>';
            echo '<th>Middle Name</th>';
            echo '<th>Last Name</th>';
            echo '<th>Username</th>';
            echo '<th>Contact Number</th>';
            echo '<th>Gender</th>';
            echo '<th>Birthdate</th>';
            echo '<th>Status</th>';
            echo '<th>Date Registered</th>';
            echo '</tr>';

            // Data Rows
            foreach ($members as $member) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($member->UserID) . '</td>';
                echo '<td>' . htmlspecialchars($member->firstname) . '</td>';
                echo '<td>' . htmlspecialchars($member->middlename) . '</td>';
                echo '<td>' . htmlspecialchars($member->lastname) . '</td>';
                echo '<td>' . htmlspecialchars($member->username) . '</td>';
                echo '<td>' . htmlspecialchars($member->contactno) . '</td>';
                echo '<td>' . htmlspecialchars($member->gender) . '</td>';
                echo '<td>' . htmlspecialchars($member->birthdate) . '</td>';
                echo '<td>' . htmlspecialchars($member->status) . '</td>';
                echo '<td>' . htmlspecialchars($member->created_at) . '</td>';
                echo '</tr>';
            }

            echo '</table>';
            echo '</body>';
            echo '</html>';
        }, 200, $headers);
    }

    /*
    |--------------------------------------------------------------------------
    | Refresh Members Table
    |--------------------------------------------------------------------------
    */
    #[On('members-refresh')]
    public function refreshMembers(): void
    {
        $this->skipRender = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Render Members
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $members = User::query()
            ->where('RoleID', 4)
            ->withExists([
                'activations as has_pending_activation' => function ($query) {
                    $query->where('status', 'Pending');
                }
            ])
            ->when(filled($this->search), function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                        ->orWhere('middlename', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('components.admin-pages.masterlist.members', [
            'members' => $members,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Open Activation Review Modal
    |--------------------------------------------------------------------------
    */

    public function openReview($id): void
    {
        if (!$this->canUpdate) {
            return;
        }

        Log::info('Activation review clicked', ['UserID' => $id, 'reviewed_by' => Auth::id()]);

        $member = User::find($id);

        if (!$member) {
            Log::warning('Review failed: member not found.', ['UserID' => $id]);
            return;
        }

        $this->dispatch('open-activation-review', UserID: $member->UserID);
    }

    /*
    |--------------------------------------------------------------------------
    | Activate / Deactivate Confirmation
    |--------------------------------------------------------------------------
    */

    public function confirmStatus($id): void
    {
        if (!$this->canUpdate) {
            return;
        }

        Log::info('Status button clicked', ['UserID' => $id]);

        $member = User::find($id);

        if (!$member) {
            Log::warning('Status update failed: member not found.', ['UserID' => $id]);
            return;
        }

        $action = strtolower($member->status) === 'active' ? 'deactivate' : 'activate';

        $this->dispatch('confirm-status', id: $member->UserID, action: $action);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Member Status
    |--------------------------------------------------------------------------
    */

    public function updateStatus($id): void
    {
        if (!$this->canUpdate) {
            return;
        }

        Log::info('Updating member status', ['UserID' => $id, 'updated_by' => Auth::id()]);

        $member = User::find($id);

        if (!$member) {
            Log::warning('Status update failed: member not found.', ['UserID' => $id]);
            return;
        }

         $isActivating = strtolower($member->status) !== 'active';

        $member->status = strtolower($member->status) === 'active' ? 'Inactive' : 'Active';
        $member->save();

        Log::info('Member status updated successfully', ['UserID' => $member->UserID, 'new_status' => $member->status]);

         $this->dispatch('swal:success', [
        'title' => 'Success!',
        'text' => $isActivating
            ? 'Member has been activated successfully.'
            : 'Member has been deactivated successfully.',
        ]);

        $this->dispatch('members-refresh');
    }

    /*
    |--------------------------------------------------------------------------
    | Open Activation Modal
    |--------------------------------------------------------------------------
    */

    public function openActivation($id): void
    {
        if (!$this->canUpdate) {
            return;
        }

        Log::info('Admin activation clicked', ['target_UserID' => $id, 'processed_by' => Auth::id()]);

        $member = User::find($id);

        if (!$member) {
            Log::warning('Activation failed. Member not found.', ['target_UserID' => $id]);
            return;
        }

        $this->dispatch('open-activation-modal', UserID: $member->UserID);
    }

    /*
    |--------------------------------------------------------------------------
    | Open Update Member Modal
    |--------------------------------------------------------------------------
    */

    public function editMember($id): void
    {
        if (!$this->canUpdate) {
            return;
        }

        Log::info('Edit member clicked', ['UserID' => $id]);

        $member = User::find($id);

        if (!$member) {
            Log::warning('Edit member not found.', ['UserID' => $id]);
            return;
        }

        $this->dispatch('open-update-member', UserID: $member->UserID);
    }

    public function viewMember($id): void
    {
        if (!$this->canRead) {
            return;
        }

        $member = User::find($id);

        if (! $member) {
            return;
        }

        $this->dispatch('open-view-member', UserID: $member->UserID);
    }

    public function handleActivationBroadcast($event): void
    {
        // This method will automatically trigger in real-time
        // whenever an activation event is broadcasted on the channel.

        // You can optionally target or just refresh the component:
        $this->skipRender = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Reset / Refresh Table Action
    |--------------------------------------------------------------------------
    */
    public function resetTable(): void
    {
        $this->search = '';
        $this->summaryFilter = 'all';
        $this->exportYear = '';
        $this->exportMonth = '';
        $this->exportStatus = '';
        $this->resetPage();
        $this->skipRender = false;
    }
}
