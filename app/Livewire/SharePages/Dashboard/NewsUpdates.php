<?php

namespace App\Livewire\SharePages\Dashboard;

use App\Models\NewsUpdate;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Concerns\HasTabSkeleton;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Withdrawal;
use App\Services\WithdrawalWindowService;

class NewsUpdates extends Component
{
    use HasTabSkeleton;
    use WithFileUploads;

    public $title = '';
    public $details = '';
    public $category = 'General';
    public $image = null;

    // ==========================================
    // REFRESH DASHBOARD
    // ==========================================

    public function refreshDashboard(): void
    {
        session()->flash(
            'success',
            'Dashboard data refreshed successfully.'
        );
    }

    // ==========================================
    // CREATE POST
    // ==========================================

    public function createPost(): void
    {
        $this->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'details' => [
                'required',
                'string',
            ],

            'category' => [
                'required',
                'string',
                'max:100',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ], [
            'image.image' => 'The selected file must be an image.',
            'image.mimes' => 'Only JPG, JPEG, PNG, and WEBP images are allowed.',
            'image.max' => 'The image must not exceed 5MB.',
        ]);

        $user = Auth::user();

        if (!$user instanceof User) {
            $this->addError(
                'title',
                'You must be logged in to create a post.'
            );

            return;
        }

        $imagePath = null;

        if ($this->image) {
            $imagePath = $this->image->store(
                'news-images',
                'public'
            );
        }

        NewsUpdate::create([
            'title' => $this->title,
            'details' => $this->details,
            'image' => $imagePath,
            'category' => $this->category,
            'status' => 'Published',
            'UserID' => $user->UserID,
        ]);

        $this->reset([
            'title',
            'details',
            'image',
        ]);

        $this->category = 'General';

        session()->flash(
            'success',
            'News update published successfully.'
        );
    }

    // ==========================================
    // DELETE POST
    // ==========================================

    public function deletePost($id): void
    {
        $user = Auth::user();

        if (!$user || (int) $user->RoleID !== 1) {
            return;
        }

        $news = NewsUpdate::find($id);

        if (!$news) {
            return;
        }

        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        session()->flash(
            'success',
            'News update deleted successfully.'
        );
    }

public function openWithdrawalModal(): void
{
    $user = Auth::user();

    if (!$user) {
        return;
    }

    // Requests are only accepted Friday 5:01 PM to Sunday 12:00 AM.
    $withdrawalWindow = app(WithdrawalWindowService::class);

    if (! $withdrawalWindow->isOpen()) {
        $this->dispatch('swal:error', [
            'title' => 'Withdrawal Window Closed',
            'text' => $withdrawalWindow->message(),
        ]);

        return;
    }

    $totalAmount = Wallet::where('UserID', $user->UserID)
        ->where('status', 'Active')
        ->where('source', '!=', 'PF')
        ->sum('amount');

    $minimumWithdrawal = $this->getMinimumWithdrawalAmount();

    if ((float) $totalAmount < $minimumWithdrawal) {
        $this->dispatch(
            'insufficient-withdrawal-balance',
            balance: number_format((float) $totalAmount, 2),
            minimum: number_format($minimumWithdrawal, 2)
        );

        return;
    }

    $this->dispatch('check-pending-withdrawal');
}

public function getMinimumWithdrawalAmount(): float
{
    return (float) (
        Setting::where('name', 'minimum_withdraw_amount')
            ->value('value')
        ?? 500
    );
}

    // ==========================================
    // CHECK WITHDRAWAL STATUS
    // ==========================================

    public function isWithdrawalActive(): bool
    {
        return filter_var(
            Setting::where('name', 'activate_withdrawal')
                ->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    // ==========================================
    // WITHDRAWAL REQUEST WINDOW
    // Friday 5:01 PM to Sunday 12:00 AM.
    // See App\Services\WithdrawalWindowService.
    // ==========================================

    public function withdrawalWindow(): array
    {
        return app(WithdrawalWindowService::class)->status();
    }

    // ==========================================
    // CHECK KABUHAYAN SPIN THE WHEEL STATUS
    // ==========================================

    public function isKabuhayanSpinActive(): bool
    {
        return filter_var(
            Setting::where('name', 'activate_spin_pangkabuhayan')
                ->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    // ==========================================
    // RENDER
    // ==========================================

    public function render()
    {
        // ------------------------------------------
        // NEWS UPDATES
        // ------------------------------------------

        $newsUpdates = NewsUpdate::with('user')
            ->where('status', 'Published')
            ->latest()
            ->get();

        // ------------------------------------------
        // CURRENT USER
        // ------------------------------------------

        $user = Auth::user();

        $userID = $user?->UserID;

        // ------------------------------------------
        // DIVISION FUNDS
        // ------------------------------------------

        $divisionFeeMap = [
            1 => 'FEE011',
            4 => 'FEE012',
        ];

        $divisionFundAmount = 0;

        if (isset($divisionFeeMap[(int) $user->RoleID])) {

            $divisionFundAmount = \App\Models\Ledger::where(
                'RefID',
                $divisionFeeMap[(int) $user->RoleID]
            )
                ->where('Source', 'MF')
                ->where('status', 'Completed')
                ->sum('amount');
        }

        // ------------------------------------------
        // TOTAL WITHDRAWALS
        // ------------------------------------------

        $totalWithdrawals = Withdrawal::where('UserID', $userID)
            ->where('status', 'Approved')
            ->sum('requested_amount');

        // ------------------------------------------
        // CASH WALLET
        // ------------------------------------------

        $totalAmount = Wallet::where('UserID', $userID)
            ->where('status', 'Active')
            ->where('source', '!=', 'PF')
            ->sum('amount');

        // ------------------------------------------
        // PROVIDENT FUND
        // ------------------------------------------

        $providentFundAmount = Wallet::where('UserID', $userID)
            ->where('status', 'Active')
            ->where('source', 'PF')
            ->sum('amount');

        // ------------------------------------------
        // TOTAL EARNINGS
        // ------------------------------------------

        $totalEarnings = $totalAmount + $providentFundAmount;

        // ------------------------------------------
        // WITHDRAWAL STATUS
        // ------------------------------------------

        $isWithdrawalActive = $this->isWithdrawalActive();



        // ------------------------------------------
        // KABUHAYAN SPIN THE WHEEL STATUS
        // ------------------------------------------

        $isKabuhayanSpinActive = $this->isKabuhayanSpinActive();
        // ------------------------------------------
        // RETURN VIEW
        // ------------------------------------------

        return view(
            'livewire.share-pages.dashboard.overview',
            [
                'newsUpdates' => $newsUpdates,

                'totalAmount' => $totalAmount,

                'providentFundAmount' => $providentFundAmount,

                'totalEarnings' => $totalEarnings,

                'totalWithdrawals' => $totalWithdrawals,

                'isWithdrawalActive' => $isWithdrawalActive,

                'withdrawalWindow' => $this->withdrawalWindow(),

                'isKabuhayanSpinActive' => $isKabuhayanSpinActive,

                'divisionFundAmount' => $divisionFundAmount,
            ]
        );
    }
}
