<?php

namespace App\Livewire\AdminPages\ReportSettings;

use App\Livewire\Concerns\AuthorizesModules;
use App\Models\Ledger;
use App\Support\Modules;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Reports extends Component
{
    use AuthorizesModules;
    use WithPagination;

    protected function module(): int
    {
        return Modules::RS_LEDGERS;
    }

    protected $paginationTheme = 'tailwind';

    /**
     * When true, the printable (unpaginated) report table is rendered into the
     * DOM for printing. Kept false at all other times — rendering every ledger
     * row on every page load made the page multi-megabyte and sluggish.
     */
    public bool $printing = false;

    /**
     * Build the printable report, then tell the browser to print it once the
     * updated DOM has been applied.
     */
    public function preparePrint(): void
    {
        if (! $this->can('print')) {
            $this->dispatch('swal:error', [
                'title' => 'Not Allowed',
                'text' => 'You do not have permission to print ledgers.',
            ]);

            return;
        }

        $this->printing = true;

        $this->dispatch('print-ledgers');
    }

    /**
     * Human-readable labels for the abbreviated ledger Source codes.
     */
    public const SOURCE_LABELS = [
        'MF' => 'Members Fee',
        'DR' => 'Direct Referral',
        'PF' => 'Provident Fund',
        'OV' => 'Overrides',
        'CL' => 'Claims',
        'RF' => 'Refund',
        'WD' => 'Withdraw',
        'TX' => 'Tax',
        'CF' => 'Convenience Fee',
        'PK' => 'Pangkabuhayan',
        'SC' => 'Satellite Commission',
        'OT' => 'Others',
        'DB' => 'Disbursement',
    ];

    /**
     * "Pangkabuhayan (PK)" style label for a Source abbreviation.
     * Falls back to the raw code when it is not in the map.
     */
    public function sourceLabel(?string $source): string
    {
        $code = strtoupper(trim((string) $source));

        return isset(self::SOURCE_LABELS[$code])
            ? self::SOURCE_LABELS[$code].' ('.$code.')'
            : (string) $source;
    }

    public string $search = '';

    public string $dateRange = ''; // e.g. "2026-08-01 to 2026-08-16"

    public function mount()
    {
        $this->dateRange = '';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function parsedDateRange(): array
    {
        if (blank($this->dateRange) || ! str_contains($this->dateRange, ' to ')) {
            return [null, null];
        }

        [$from, $to] = explode(' to ', $this->dateRange, 2);

        return [trim($from) ?: null, trim($to) ?: null];
    }

    public function updatedDateRange()
    {
        $this->resetPage();
    }

    public function clearDateFilter(): void
    {
        $this->dateRange = '';
        $this->resetPage();
        $this->dispatch('ranges-reset');
    }

    public function resetTable(): void
    {
        $this->search = '';
        $this->dateRange = '';
        $this->resetPage();
        $this->dispatch('ranges-reset');
    }

    /**
     * Shared query builder for both the paginated table and the print export,
     * so both always reflect the same search + date filters.
     *
     * Resolves the raw IDs into human-readable names via left joins:
     *  - member_name  : ActivationID → activations.UserID → users (the member
     *                   the ledger entry belongs to, when the activation is
     *                   not null)
     *  - fee_name     : Source 'MF' + RefID → membership_fees.FeeID
     *  - ref_user_name: RefID → users.UserID when RefID holds a plain user ID
     *                   (DB/ DR / OV / PF / CF / WD / TX / PK entries store the
     *                   related user there, so those rows get a name too)
     */
    protected function baseQuery()
    {
        [$dateFrom, $dateTo] = $this->parsedDateRange();

        return Ledger::query()
            // Member behind the activation (only when ActivationID is set)
            ->leftJoin('activations as a', 'a.ActivationID', '=', 'ledgers.ActivationID')
            ->leftJoin('users as au', 'au.UserID', '=', 'a.UserID')
            // Fee name for Membership Fee rows (Source 'MF' + RefID → FeeID)
            ->leftJoin('membership_fees as mf', function ($join) {
                $join->on('mf.FeeID', '=', 'ledgers.RefID')
                    ->where('ledgers.Source', '=', 'MF');
            })
            // RefID may itself carry a UserID — only join when it is numeric,
            // so fee codes like "FEE005" never try to match a user
            ->leftJoin('users as ru', function ($join) {
                $join->on('ru.UserID', '=', 'ledgers.RefID')
                    ->whereRaw("ledgers.RefID REGEXP '^[0-9]+$'");
            })
            ->select('ledgers.*')
            ->addSelect(DB::raw("CONCAT_WS(' ', au.firstname, au.lastname) AS member_name"))
            ->addSelect(DB::raw("CONCAT_WS(' ', ru.firstname, ru.lastname) AS ref_user_name"))
            ->addSelect('mf.FeeName AS fee_name')
            ->when(filled($this->search), function ($query) {
                $search = trim($this->search);
                $query->where(function ($q) use ($search) {
                    $q->where('ledgers.Source', 'like', "%{$search}%")
                        ->orWhere('ledgers.status', 'like', "%{$search}%")
                        ->orWhere('ledgers.ActivationID', 'like', "%{$search}%")
                        ->orWhere('ledgers.RefID', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT_WS(' ', au.firstname, au.lastname) LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("CONCAT_WS(' ', ru.firstname, ru.lastname) LIKE ?", ["%{$search}%"])
                        ->orWhere('mf.FeeName', 'like', "%{$search}%");
                });
            })
            ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('ledgers.created_at', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay(),
                ]);
            });
    }

    public function render()
    {
        [$dateFrom, $dateTo] = $this->parsedDateRange();

        $ledgers = (clone $this->baseQuery())
            ->latest('TransactionID')
            ->paginate(10);

        // Unpaginated set for the printable report, matching the same filters —
        // only queried when the user actually prints (keeps the page light).
        $printLedgers = $this->printing
            ? (clone $this->baseQuery())->latest('TransactionID')->get()
            : collect();

        return view('livewire.admin-pages.report-settings.reports', [
            'ledgers' => $ledgers,
            'printLedgers' => $printLedgers,
            'dateRangeLabel' => $dateFrom && $dateTo
                ? Carbon::parse($dateFrom)->format('M d, Y').' — '.Carbon::parse($dateTo)->format('M d, Y')
                : 'All Time',
        ]);
    }
}
