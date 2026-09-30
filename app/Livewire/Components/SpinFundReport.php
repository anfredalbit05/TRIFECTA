<?php

namespace App\Livewire\Components;

use App\Models\SpinDraw;
use App\Services\SpinWheelService;
use App\Support\Modules;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Pangkabuhayan fund report — where the Pangkabuhayan collections went,
 * per Spin the Wheel cycle, with a 👁 view action per draw that lists that
 * cycle's winners (name, prize, claim status). Rendered inside Report &
 * Settings → Pangkabuhayan; purely presentational, so it keeps no state
 * of its own beyond the currently-open draw.
 */
class SpinFundReport extends Component
{
    protected function module(): int
    {
        return Modules::RS_PANGKABUHAYAN;
    }

    public ?int $viewingDrawId = null;

    public string $winnerSearch = '';

    #[Computed]
    public function report(): array
    {
        return app(SpinWheelService::class)->fundReport();
    }

    /** Open the 👁 winners modal for one draw (the row's cycle). */
    public function openWinners(int $drawId): void
    {
        $this->winnerSearch = '';
        $this->viewingDrawId = $drawId;
    }

    public function closeWinners(): void
    {
        $this->viewingDrawId = null;
        $this->winnerSearch = '';
    }

    /**
     * The winners of the draw being viewed, optionally filtered by name,
     * each carrying their paired expense row (relation 'ledger').
     */
    #[Computed]
    public function viewingWinners(): Collection
    {
        if (! $this->viewingDrawId) {
            return collect();
        }

        $draw = SpinDraw::find($this->viewingDrawId);

        if (! $draw) {
            return collect();
        }

        $winners = app(SpinWheelService::class)->winnersOf($draw);

        $search = mb_strtolower(trim($this->winnerSearch));

        if ($search === '') {
            return $winners;
        }

        return $winners->filter(function ($winner) use ($search) {
            $name = mb_strtolower(($winner->user->firstname ?? '').' '
                .($winner->user->lastname ?? '').' '
                .($winner->user->username ?? '').' '
                .($winner->user->referralCode ?? ''));

            return str_contains($name, $search);
        })->values();
    }

    #[Computed]
    public function viewingDraw(): ?SpinDraw
    {
        return $this->viewingDrawId ? SpinDraw::find($this->viewingDrawId) : null;
    }

    /**
     * Download the winners currently shown in the modal as a CSV file
     * (follows the winner search filter, so "export what you see").
     */
    public function exportWinners(): StreamedResponse
    {
        abort_unless($this->can('print'), 403, 'You do not have permission to export this report.');

        $draw = $this->viewingDraw;

        abort_unless($draw !== null, 404, 'Draw not found.');

        $winners = $this->viewingWinners;
        $total = round((float) $winners->sum('prize_amount'), 2);

        $fileName = 'spin-winners_'
            .$draw->draw_date->format('Y-m-d')
            .'_'.now()->format('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response()->streamDownload(function () use ($draw, $winners, $total) {
            // UTF-8 BOM so Excel renders names and the peso sign correctly.
            echo "\xEF\xBB\xBF";

            $csv = fopen('php://output', 'w');

            fputcsv($csv, ['TRUE LIFE — Spin the Wheel Winners']);
            fputcsv($csv, ['Draw date', $draw->draw_date->format('F j, Y')]);
            fputcsv($csv, ['Period covered', $draw->period_start->format('M j')
                .' – '.$draw->period_end->format('M j, Y')]);
            fputcsv($csv, ['Exported', now()->format('M d, Y h:i A')]);
            fputcsv($csv, []);

            fputcsv($csv, [
                '#', 'Winner', 'Referral Code', 'Prize',
                'Amount (PHP)', 'Qualifying Activations', 'Claim Status',
                'Date Drawn', 'Date Claimed', 'PK Ledger #',
            ]);

            foreach ($winners as $winner) {
                fputcsv($csv, [
                    $winner->spin_order,
                    trim(($winner->user->firstname ?? '').' '.($winner->user->lastname ?? ''))
                        ?: ($winner->user->username ?? 'Unknown'),
                    $winner->user->referralCode,
                    $winner->prize_name,
                    number_format((float) $winner->prize_amount, 2, '.', ''),
                    (int) $winner->qualified_invites,
                    ($winner->ledger?->status ?? 'Pending') === 'Completed' ? 'Claimed' : 'Pending claim',
                    optional($winner->created_at)->format('M d, Y h:i A'),
                    ($winner->ledger?->status ?? '') === 'Completed' && $winner->ledger?->updated_at
                        ? $winner->ledger->updated_at->format('M d, Y h:i A')
                        : '',
                    $winner->ledger?->TransactionID,
                ]);
            }

            fputcsv($csv, []);
            fputcsv($csv, [
                'Total', $winners->count().' winner(s)', '', '',
                number_format($total, 2, '.', ''),
            ]);

            fclose($csv);
        }, $fileName, $headers);
    }

    public function render()
    {
        return view('livewire.components.spin-fund-report');
    }
}
