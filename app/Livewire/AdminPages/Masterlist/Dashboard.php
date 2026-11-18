<?php

namespace App\Livewire\AdminPages\Masterlist;

use App\Models\User;
use App\Models\Branch;
use App\Models\Benefit;
use App\Models\Inquiry;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Dashboard extends Component
{
    use HasTabSkeleton;
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
        $mapUsers = User::where('RoleID', 8)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['FirstName', 'LastName', 'address', 'longitude', 'latitude']);

        // Build filtered query for the print table matching export filters
        $query = User::query()->where('RoleID', 8);

        if (!empty($this->exportYear)) {
            $query->whereYear('created_at', $this->exportYear);
        }

        if (!empty($this->exportMonth)) {
            $query->whereMonth('created_at', $this->exportMonth);
        }

        if (!empty($this->exportStatus)) {
            if (strtolower($this->exportStatus) === 'active') {
                $query->whereRaw('LOWER(status) = ?', ['active']);
            } else {
                $query->whereRaw('LOWER(status) != ?', ['active']);
            }
        }

        // Inside render() method:
        $printMembers = $query->with('beneficiaries')->latest()->get();

        return view('livewire.admin-pages.masterlist.dashboard', [
            'totalMembers' => User::where('RoleID', 8)->count(),
            'activeBranches' => Branch::where('status', true)->count(),
            'totalBenefits' => Benefit::where('status', true)->count(),
            'totalInquiries' => Inquiry::count(),
            'mapUsers' => $mapUsers,
            'printMembers' => $printMembers, // <--- Crucial for the print table
        ]);
    }

    public function printReport()
    {
        return redirect()->route('admin.reports.print-members', [
            'year' => $this->exportYear,
            'month' => $this->exportMonth,
            'status' => $this->exportStatus,
        ]);
    }

    public function getSummaryStatsProperty(): array
    {
        $query = User::query()->where('RoleID', 8);

        // 1. Apply Report Filters (Year & Month) if selected
        if (!empty($this->exportYear)) {
            $query->whereYear('created_at', $this->exportYear);
        }

        if (!empty($this->exportMonth)) {
            $query->whereMonth('created_at', $this->exportMonth);
        }

        // 2. Apply Status Filter if selected
        if (!empty($this->exportStatus)) {
            $query->whereRaw('LOWER(status) = ?', [strtolower($this->exportStatus)]);
        }

        // 3. Fallback to summaryFilter time frame if no specific export year/month are chosen
        if (empty($this->exportYear) && empty($this->exportMonth)) {
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
        $query = User::query()->where('RoleID', 4)->with('beneficiaries');

        if (!empty($this->exportYear)) {
            $query->whereYear('created_at', $this->exportYear);
        }

        if (!empty($this->exportMonth)) {
            $query->whereMonth('created_at', $this->exportMonth);
        }

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
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response()->stream(function () use ($members) {
            echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head><meta http-equiv="content-type" content="text/html; charset=UTF-8"></head>';
            echo '<body>';

            // HEADER
            // (Excel export can't read CSS vars — palette hexes are inlined.)
            echo '<div style="text-align: center; font-family: Arial, sans-serif; margin-bottom: 25px; border-bottom: 2px solid #073F80; padding-bottom: 15px;">';
            echo '<h1 style="margin: 0; font-size: 28px; font-weight: bold; color: #073F80; letter-spacing: 3px; font-family: Georgia, serif;">TRIFECTA</h1>';
            echo '<p style="margin: 4px 0; font-size: 12px; font-weight: bold; color: #309425; letter-spacing: 4px; text-transform: uppercase;">&mdash; S O L I D A R I T A S &mdash;</p>';
            echo '<h2 style="margin: 15px 0 5px 0; font-size: 16px; font-weight: bold; text-transform: uppercase; color: #333;">LIST OF MEMBERS & BENEFICIARIES</h2>';
            echo '</div>';

            // DATA TABLE
            echo '<table border="1" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 11px; width: 100%;">';
            echo '<tr style="background-color: #0B5FC0; color: #FFFFFF; font-weight: bold; text-align: center; height: 25px;">';
            echo '<th style="padding: 5px;">#</th>';
            echo '<th style="padding: 5px;">Full Name</th>';
            echo '<th style="padding: 5px;">Birthdate</th>';
            echo '<th style="padding: 5px;">Gender</th>';
            echo '<th style="padding: 5px;">Civil Status</th>';
            echo '<th style="padding: 5px;">Contact Number</th>';
            echo '<th style="padding: 5px;">Beneficiaries</th>';
            echo '</tr>';

            $index = 1;
            foreach ($members as $member) {
                $fullName = trim($member->lastname . ', ' . $member->firstname . ' ' . ($member->middlename ?? ''));
                $birthdateFormatted = $member->birthdate ? date('F d, Y', strtotime($member->birthdate)) : '-';

                // Format beneficiaries list for Excel cell
                // Format beneficiaries list for Excel cell
                $beneficiariesList = '-';
                if ($member->beneficiaries && $member->beneficiaries->count() > 0) {
                    $beneficiariesFormatted = [];
                    foreach ($member->beneficiaries as $ben) {
                        $benName = $ben->fullname ?? 'N/A';
                        $benRel = !empty($ben->relationship) ? ' (' . $ben->relationship . ')' : '';
                        $beneficiariesFormatted[] = $benName . $benRel;
                    }
                    $beneficiariesList = implode('<br>', $beneficiariesFormatted);
                }

                echo '<tr>';
                echo '<td style="text-align: center; padding: 4px; vertical-align: top;">' . $index++ . '</td>';
                echo '<td style="padding: 4px; font-weight: bold; vertical-align: top;">' . htmlspecialchars($fullName) . '</td>';
                echo '<td style="padding: 4px; text-align: center; vertical-align: top;">' . htmlspecialchars($birthdateFormatted) . '</td>';
                echo '<td style="padding: 4px; text-align: center; vertical-align: top;">' . htmlspecialchars($member->gender ?? '-') . '</td>';
                echo '<td style="padding: 4px; text-align: center; vertical-align: top;">' . htmlspecialchars($member->civilstatus ?? '-') . '</td>';
                echo '<td style="padding: 4px; text-align: center; vertical-align: top;">' . htmlspecialchars($member->contactno ?? '-') . '</td>';
                echo '<td style="padding: 4px; vertical-align: top;">' . $beneficiariesList . '</td>';
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
