<?php

namespace App\Livewire\SharePages\Dashboard;

use App\Models\NewsUpdate;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Ledger;
use App\Models\Withdrawal;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class NewsUpdates extends Component
{
    use WithFileUploads;

    public $title = '';
    public $details = '';
    public $category = 'General';
    public $image = null;

    // ==========================================
    // WITHDRAWAL FORM
    // ==========================================
    public $withdrawAmount = '';
    public $withdrawalMethod = '';
    public $accountName = '';
    public $accountNumber = '';
    public $paymentReference = '';


    public function refreshDashboard(): void
    {
        session()->flash('success', 'Dashboard data refreshed successfully.');
    }

    public function createPost(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'details' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'image.image' => 'The selected file must be an image.',
            'image.mimes' => 'Only JPG, JPEG, PNG, and WEBP images are allowed.',
            'image.max' => 'The image must not exceed 5MB.',
        ]);

        $user = Auth::user();

        if (!$user instanceof User) {
            $this->addError('title', 'You must be logged in to create a post.');
            return;
        }

        $imagePath = null;

        if ($this->image) {
            $imagePath = $this->image->store('news-images', 'public');
        }

        NewsUpdate::create([
            'title' => $this->title,
            'details' => $this->details,
            'image' => $imagePath,
            'category' => $this->category,
            'status' => 'Published',
            'UserID' => $user->UserID,
        ]);

        $this->reset(['title', 'details', 'image']);
        $this->category = 'General';

        session()->flash('success', 'News update published successfully.');
    }

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

        session()->flash('success', 'News update deleted successfully.');
    }


    public function checkPendingWithdrawal(): void
    {

        if (!$this->isWithdrawalActive()) {

        $this->dispatch(
            'withdrawal-error',
            message: 'Withdrawals are currently unavailable. Please try again later.'
        );

        return;
    }

        $user = Auth::user();

        if (!$user) {
            session()->flash('error', 'You must be logged in.');
            return;
        }

        $pendingWithdrawal = Withdrawal::where('UserID', $user->UserID)
            ->where('status', 'Pending')
            ->latest('WithdrawID')
            ->first();

        // User has a pending withdrawal
        if ($pendingWithdrawal) {

            $this->dispatch(
                'pending-withdrawal',
                amount: number_format((float) $pendingWithdrawal->amount, 2),
                method: $pendingWithdrawal->withdrawal_method,
                accountName: $pendingWithdrawal->account_name ?? 'N/A',
                accountNumber: $pendingWithdrawal->account_number ?? 'N/A',
                reference: $pendingWithdrawal->payment_reference ?? 'N/A',
                type: $pendingWithdrawal->type,
                date: $pendingWithdrawal->created_at
                    ? $pendingWithdrawal->created_at->format('F d, Y h:i A')
                    : 'N/A'
            );

            return;
        }

        // No pending withdrawal - tell Alpine to open the modal
        $this->dispatch('open-withdraw-modal');
    }

    private function getMinimumWithdrawalAmount(): float
    {
        return (float) Setting::where('name', 'minimum_withdraw_amount')
            ->value('value') ?? 500;
    }

    private function isWithdrawalActive(): bool
    {
        return filter_var(
            Setting::where('name', 'activate_withdrawal')->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    // ==========================================
    // SUBMIT WITHDRAWAL
    // ==========================================
    public function submitWithdrawal(): void
    {

        if (!$this->isWithdrawalActive()) {

        $this->dispatch(
            'withdrawal-error',
            message: 'Withdrawals are currently unavailable. Please try again later.'
        );

        return;
    }

        $user = Auth::user();

        if (!$user) {
            session()->flash('error', 'You must be logged in.');
            return;
        }

        $userID = $user->UserID;

        // Check if the user already has a pending withdrawal
        $pendingWithdrawal = Withdrawal::where('UserID', $userID)
            ->where('status', 'Pending')
            ->latest('WithdrawID')
            ->first();

        if ($pendingWithdrawal) {

            $this->dispatch(
                'pending-withdrawal',
                amount: number_format((float) $pendingWithdrawal->amount, 2),
                method: $pendingWithdrawal->withdrawal_method,
                accountName: $pendingWithdrawal->account_name ?? 'N/A',
                accountNumber: $pendingWithdrawal->account_number ?? 'N/A',
                reference: $pendingWithdrawal->payment_reference ?? 'N/A',
                type: $pendingWithdrawal->type,
                date: $pendingWithdrawal->created_at
                    ? $pendingWithdrawal->created_at->format('F d, Y h:i A')
                    : 'N/A'
            );

            return;
        }

        // Available Cash Wallet balance
        $availableBalance = Wallet::where('UserID', $userID)
            ->where('status', 'Active')
            ->where('source', '!=', 'PF')
            ->sum('amount');


        // ==========================================
        // VALIDATION RULES
        // ==========================================

        $minimumWithdrawal = $this->getMinimumWithdrawalAmount();

        $rules = [
            'withdrawAmount' => [
                'required',
                'numeric',
                'min:' . $minimumWithdrawal,
                'max:' . max(0, $availableBalance),
            ],

            'withdrawalMethod' => [
                'required',
                'in:Cash,GCash,Maya,Bank Transfer',
            ],

            'paymentReference' => [
                'nullable',
                'string',
                'max:100',
            ],
        ];


        // Online methods require account details
        if ($this->withdrawalMethod !== 'Cash') {

            $rules['accountName'] = [
                'required',
                'string',
                'max:255',
            ];

            $rules['accountNumber'] = [
                'required',
                'string',
                'max:255',
            ];

        } else {

            $rules['accountName'] = [
                'nullable',
                'string',
                'max:255',
            ];

            $rules['accountNumber'] = [
                'nullable',
                'string',
                'max:255',
            ];
        }


        $this->validate($rules, [
            'withdrawAmount.required' => 'Please enter a withdrawal amount.',
            'withdrawAmount.numeric' => 'Withdrawal amount must be a valid number.',
            'withdrawAmount.min' => 'Minimum withdrawal amount is ₱500.00.',
            'withdrawAmount.max' => 'Withdrawal amount exceeds your available balance.',

            'withdrawalMethod.required' => 'Please select a withdrawal method.',

            'accountName.required' => 'Please enter the account name.',
            'accountNumber.required' => 'Please enter the account number.',
        ]);


        try {

            $submittedAmount = (float) $this->withdrawAmount;

            DB::transaction(function () use ($userID) {

                // ==========================================
                // TYPE
                // W = CASH
                // O = GCASH / MAYA / BANK TRANSFER
                // ==========================================
                $type = $this->withdrawalMethod === 'Cash'
                    ? 'W'
                    : 'O';


                // ==========================================
                // CREATE WITHDRAWAL
                // ==========================================
                $withdrawal = Withdrawal::create([

                    'UserID' => $userID,

                    'amount' => $this->withdrawAmount,

                    'type' => $type,

                    'withdrawal_method' => $this->withdrawalMethod,

                    'account_name' =>
                        $this->withdrawalMethod === 'Cash'
                            ? null
                            : $this->accountName,

                    'account_number' =>
                        $this->withdrawalMethod === 'Cash'
                            ? null
                            : $this->accountNumber,

                    'payment_reference' =>
                        $this->paymentReference ?: null,

                    // Not processed yet
                    'ProcessedBy' => Auth::user()->UserID,

                    'status' => 'Pending',
                ]);


                // ==========================================
                // CREATE LEDGER RECORD
                // ==========================================
                Ledger::create([

                    'RefID' => $userID,

                    'WithdrawID' => $withdrawal->WithdrawID,

                    'amount' => - $this->withdrawAmount,

                    'Source' => 'WD',

                    'ActivationID' => null,

                    'status' => 'Pending',
                ]);
            });


            // ==========================================
            // RESET FORM
            // ==========================================
            $this->reset([
                'withdrawAmount',
                'withdrawalMethod',
                'accountName',
                'accountNumber',
                'paymentReference',
            ]);


              // SweetAlert success event
            $this->dispatch(
                'withdrawal-submitted',
                amount: number_format($submittedAmount, 2)
            );

        } catch (\Exception $e) {

            report($e);

            $this->dispatch(
            'withdrawal-error',
            message: 'Something went wrong while submitting your withdrawal request.'
        );
        }
    }


    // ==========================================
    // RENDER
    // ==========================================
    public function render()
    {
        $newsUpdates = NewsUpdate::with('user')
            ->where('status', 'Published')
            ->latest()
            ->get();

        $user = Auth::user();

        $userID = $user?->UserID;

        // Cash Wallet
        $totalAmount = Wallet::where('UserID', $userID)
            ->where('status', 'Active')
            ->where('source', '!=', 'PF')
            ->sum('amount');

        // Provident Fund
        $providentFundAmount = Wallet::where('UserID', $userID)
            ->where('status', 'Active')
            ->where('source', 'PF')
            ->sum('amount');

        // Combined Earnings
        $totalEarnings = $totalAmount + $providentFundAmount;

        $minimumWithdrawalAmount = $this->getMinimumWithdrawalAmount();

        $isWithdrawalActive = $this->isWithdrawalActive();

        return view('livewire.share-pages.dashboard.overview', [
            'newsUpdates' => $newsUpdates,
            'totalAmount' => $totalAmount,
            'providentFundAmount' => $providentFundAmount,
            'totalEarnings' => $totalEarnings,
            'minimumWithdrawalAmount' => $minimumWithdrawalAmount,
            'isWithdrawalActive' => $isWithdrawalActive,
        ]);
    }
}
