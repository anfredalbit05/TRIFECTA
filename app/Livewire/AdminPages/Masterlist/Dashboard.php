<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\User;
use App\Models\Branch;
use App\Models\Benefit;
use App\Models\Inquiry;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Dashboard extends Component
{


    public string $summaryFilter = 'all';

    // Excel Export Filters
    public $exportYear = '';
    public $exportMonth = '';
    public $exportStatus = '';

    public bool $skipRender = true;


    public function refreshDashboard()
    {
        // Simply triggers a component re-render with fresh database queries
        session()->flash('message', 'Dashboard data refreshed successfully.');
    }

    public function render()
    {
        $mapUsers = User::where('RoleID', 4)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get([
                'FirstName',
                'LastName',
                'address',
                'longitude',
                'latitude',
            ]);

        return view('components.admin-pages.masterlist.dashboard', [
            'totalMembers' => User::where('RoleID', '!=', 1)->count(),
            'activeBranches' => Branch::where('status', true)->count(),
            'totalBenefits' => Benefit::where('status', true)->count(),
            'totalInquiries' => Inquiry::count(),
            'mapUsers' => $mapUsers,
        ]);
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

        // OFFICIAL GOVERNMENT HEADER
        echo '<div style="text-align: center; font-family: Arial, sans-serif; margin-bottom: 20px;">';
        echo '<p style="margin: 0; font-size: 12px;">Republic of the Philippines</p>';
        echo '<p style="margin: 0; font-size: 14px; font-weight: bold;">Provincial Government of Davao del Norte</p>';
        echo '<p style="margin: 0; font-size: 11px; color: #555;">Government Center, Mankilam, Tagum City</p>';
        echo '<h2 style="margin: 15px 0 5px 0; font-size: 16px; font-weight: bold; text-transform: uppercase;">LIST OF MEMBERS</h2>';
        echo '<p style="margin: 0; font-size: 10px; color: #777;">Generated on: ' . date('F d, Y h:i A') . '</p>';
        echo '</div>';

        // DATA TABLE
        echo '<table border="1" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 11px; width: 100%;">';

        // Header Row Style
        echo '<tr style="background-color: #0C2C6B; color: #FFFFFF; font-weight: bold; text-align: center; height: 25px;">';
        echo '<th style="padding: 5px;">#</th>';
        echo '<th style="padding: 5px;">Full Name</th>';
        echo '<th style="padding: 5px;">Contact Number</th>';
        echo '<th style="padding: 5px;">Gender</th>';
        echo '<th style="padding: 5px;">Birthdate</th>';
        echo '<th style="padding: 5px;">Status</th>';
        echo '<th style="padding: 5px;">Date Created</th>';
        echo '</tr>';

        // Data Rows
        $index = 1;
        foreach ($members as $member) {
            $fullName = trim($member->lastname . ', ' . $member->firstname . ' ' . ($member->middlename ?? ''));
            $birthdateFormatted = $member->birthdate ? date('F d, Y', strtotime($member->birthdate)) : '-';
            $dateCreatedFormatted = $member->created_at ? $member->created_at->format('n/j/Y') : '';

            echo '<tr>';
            echo '<td style="text-align: center; padding: 4px;">' . $index++ . '</td>';
            echo '<td style="padding: 4px; font-weight: bold;">' . htmlspecialchars($fullName) . '</td>';
            echo '<td style="padding: 4px; text-align: center;">' . htmlspecialchars($member->contactno ?? '-') . '</td>';
            echo '<td style="padding: 4px; text-align: center;">' . htmlspecialchars($member->gender ?? '-') . '</td>';
            echo '<td style="padding: 4px; text-align: center;">' . htmlspecialchars($birthdateFormatted) . '</td>';
            echo '<td style="padding: 4px; text-align: center;">' . htmlspecialchars($member->status) . '</td>';
            echo '<td style="padding: 4px; text-align: center;">' . htmlspecialchars($dateCreatedFormatted) . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        echo '</body>';
        echo '</html>';
    }, 200, $headers);
}

 public function resetTable(): void
    {
        $this->summaryFilter = 'all';
        $this->exportYear = '';
        $this->exportMonth = '';
        $this->exportStatus = '';
        $this->resetPage();
        $this->skipRender = false;
    }


}
