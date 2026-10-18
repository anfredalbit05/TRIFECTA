<?php

namespace App\Livewire\Components;

use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Support\Modules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class WithdrawalModal extends Component
{
    // ==========================================
    // WITHDRAWAL FORM
    // ==========================================

    public $withdrawAmount = '';

    public $withdrawalMethod = '';

    public $bankName = ''; // <--- Added property

    public $accountName = '';

    public $accountNumber = '';

    public $paymentReference = '';

    // ==========================================
    // WALLET BALANCE
    //
    // Public + entangled in the view so Alpine's
    // "balance" always mirrors the live value,
    // instead of a JS number frozen at whatever
    // it was on the very first x-data init.
    // ==========================================

    public float $totalAmount = 0;

    // ==========================================
    // ADMIN MODE
    //
    // When an admin opens this modal for another
    // user (from AdminUsers), targetUserID holds
    // that user's UserID and isAdminMode is true.
    // Every method below resolves against the
    // "acting" user instead of always using
    // Auth::user().
    // ==========================================

    public ?int $targetUserID = null;

    public bool $isAdminMode = false;

    // ==========================================
    // ADMIN: OPEN MODAL FOR A SPECIFIC USER
    // ==========================================

    #[On('open-admin-withdrawal-modal')]
    public function openForUser(int $userID): void
    {

        $targetUser = User::where('UserID', $userID)->first();

        if (! $targetUser) {

            $this->dispatch(
                'withdrawal-error',
                message: 'User not found.'
            );

            return;
        }

        $this->reset([
            'withdrawAmount',
            'withdrawalMethod',
            'bankName', // <--- Added here
            'accountName',
            'accountNumber',
            'paymentReference',
        ]);

        $this->targetUserID = $targetUser->UserID;
        $this->isAdminMode = true;

        $this->refreshBalance();

        $this->checkPendingWithdrawal();
    }

    // ==========================================
    // CHECK PENDING WITHDRAWAL
    // ==========================================

    #[On('check-pending-withdrawal')]
    public function checkPendingWithdrawal(): void
    {
        if (! $this->isAdminMode) {
            $this->targetUserID = null;
        }

        $this->refreshBalance();

        if (! $this->isWithdrawalActive()) {
            $this->dispatch(
                'withdrawal-error',
                message: 'Withdrawals are currently unavailable. Please try again later.'
            );

            return;
        }

        $user = $this->resolveActingUser();

        if (! $user) {
            $this->dispatch(
                'withdrawal-error',
                message: 'You must be logged in.'
            );

            return;
        }

        $pendingWithdrawal = Withdrawal::where('UserID', $user->UserID)
            ->where('status', 'Pending')
            ->latest('WithdrawID')
            ->first();

        if ($pendingWithdrawal) {
            $processedByUser = $pendingWithdrawal->ProcessedBy
                ? User::where('UserID', $pendingWithdrawal->ProcessedBy)->first()
                : null;

            $processedByName = $processedByUser
                ? trim($processedByUser->firstname.' '.$processedByUser->lastname)
                : 'N/A';

            $this->dispatch(
                'pending-withdrawal',
                amount: number_format((float) $pendingWithdrawal->amount, 2),
                method: $pendingWithdrawal->withdrawal_method,
                bankName: $pendingWithdrawal->bank_name ?? 'N/A',
                accountName: $pendingWithdrawal->account_name ?? 'N/A',
                accountNumber: $pendingWithdrawal->account_number ?? 'N/A',
                reference: $pendingWithdrawal->payment_reference ?? 'N/A',
                type: $pendingWithdrawal->type,
                processedBy: $processedByName,
                date: $pendingWithdrawal->created_at
                    ? $pendingWithdrawal->created_at->format('F d, Y h:i A')
                    : 'N/A'
            );

            return;
        }

        $minimumWithdrawal = $this->getMinimumWithdrawalAmount();

        if ($this->totalAmount < $minimumWithdrawal) {
            $this->dispatch(
                'withdrawal-error',
                message: 'You do not have enough balance to make a withdrawal. Your available balance is ₱'.
                    number_format($this->totalAmount, 2).
                    ', while the minimum withdrawal amount is ₱'.
                    number_format($minimumWithdrawal, 2).
                    '.'
            );

            return;
        }

        $this->dispatch('open-withdraw-modal');
    }

    // ==========================================
    // SUBMIT WITHDRAWAL
    // ==========================================

    public function submitWithdrawal(): void
    {
        // ==========================================
        // CHECK IF WITHDRAWALS ARE ACTIVE
        // ==========================================

        if (! $this->isWithdrawalActive()) {
            $this->dispatch(
                'withdrawal-error',
                message: 'Withdrawals are currently unavailable. Please try again later.'
            );

            return;
        }

        // ==========================================
        // RESOLVE ACTING USER
        // ==========================================

        $user = $this->resolveActingUser();

        if (! $user) {

            $this->dispatch(
                'withdrawal-error',
                message: 'You must be logged in.'
            );

            return;
        }

        $userID = $user->UserID;

        // ==========================================
        // CHECK EXISTING PENDING WITHDRAWAL
        // ==========================================

        $pendingWithdrawal = Withdrawal::where('UserID', $userID)
            ->where('status', 'Pending')
            ->exists();

        if ($pendingWithdrawal) {

            $this->dispatch(
                'withdrawal-error',
                message: 'This user already has a pending withdrawal request.'
            );

            return;
        }

        // ==========================================
        // REFRESH CURRENT BALANCE
        // ==========================================

        $this->refreshBalance();

        $availableBalance = $this->totalAmount;

        $minimumWithdrawal = $this->getMinimumWithdrawalAmount();

        // ==========================================
        // VALIDATION RULES
        //
        // withdrawAmount = REQUESTED AMOUNT
        //
        // Example:
        // User enters ₱1,000
        // Tax = ₱100
        // Net = ₱900
        // ==========================================

        $rules = [

            'withdrawAmount' => [
                'required',
                'numeric',
                'min:'.$minimumWithdrawal,
                'max:'.max(0, $availableBalance),
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

        // ==========================================
        // BANK TRANSFER VALIDATION
        // ==========================================

        if ($this->withdrawalMethod === 'Bank Transfer') {

            $rules['bankName'] = [
                'required',
                'string',
                'max:255',
            ];
        }

        // ==========================================
        // ACCOUNT DETAILS VALIDATION
        //
        // Cash does not require account details.
        // ==========================================

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
        }

        // ==========================================
        // VALIDATE
        // ==========================================

        $this->validate(
            $rules,
            [
                'withdrawAmount.required' => 'Please enter a withdrawal amount.',

                'withdrawAmount.numeric' => 'Withdrawal amount must be a valid number.',

                'withdrawAmount.min' => "Minimum withdrawal amount is ₱{$minimumWithdrawal}.",

                'withdrawAmount.max' => 'Withdrawal amount exceeds the available balance.',

                'withdrawalMethod.required' => 'Please select a withdrawal method.',

                'bankName.required' => 'Please select a bank.',

                'accountName.required' => 'Please enter the account name.',

                'accountNumber.required' => 'Please enter the account number.',
            ]
        );

        // ==========================================
        // CALCULATE AMOUNTS
        // ==========================================

        try {

            /*
            |--------------------------------------------------------------------------
            | REQUESTED AMOUNT
            |--------------------------------------------------------------------------
            |
            | This is exactly what the user entered.
            |
            | Example:
            |
            | User enters ₱1,000
            |
            */

            $requestedAmount = round(
                (float) $this->withdrawAmount,
                2
            );

            /*
            |--------------------------------------------------------------------------
            | TAX RATE
            |--------------------------------------------------------------------------
            |
            | 10%
            |
            */

            /*
            |--------------------------------------------------------------------------
            | TAX RATE
            |--------------------------------------------------------------------------
            | RoleID 5, 6, and 7 are exempt from withdrawal TX.
            |--------------------------------------------------------------------------
            */

            $taxExemptRoles = [5, 6, 7, 9];

            $taxAmount = in_array((int) $user->RoleID, $taxExemptRoles, true)
                ? 0.00
                : round($requestedAmount * 0.10, 2);

            /*
            |--------------------------------------------------------------------------
            | NET AMOUNT
            |--------------------------------------------------------------------------
            | Exempt roles receive the full requested amount.
            |--------------------------------------------------------------------------
            */

            $netAmount = round($requestedAmount - $taxAmount, 2);

            /*
            |--------------------------------------------------------------------------
            | WHO CREATED THE WITHDRAWAL
            |--------------------------------------------------------------------------
            */

            $processedBy = Auth::id();

            // ==========================================
            // DATABASE TRANSACTION
            // ==========================================

            DB::transaction(function () use (
                $userID,
                $processedBy,
                $requestedAmount,
                $taxAmount,
                $netAmount
            ) {

                // ==========================================
                // DETERMINE WITHDRAWAL TYPE
                // ==========================================

                $type = $this->withdrawalMethod === 'Cash'
                    ? 'W'
                    : 'O';

                // ==========================================
                // CREATE WITHDRAWAL
                // ==========================================

                Withdrawal::create([

                    // ======================================
                    // USER
                    // ======================================

                    'UserID' => $userID,

                    // ======================================
                    // REQUESTED AMOUNT
                    //
                    // Example: 1000.00
                    // ======================================

                    'requested_amount' => $requestedAmount,

                    // ======================================
                    // NET AMOUNT
                    //
                    // Example: 900.00
                    // ======================================

                    'amount' => $netAmount,

                    // ======================================
                    // TAX
                    //
                    // Example: 100.00
                    // ======================================

                    'tax_amount' => $taxAmount,

                    // ======================================
                    // WITHDRAWAL TYPE
                    // ======================================

                    'type' => $type,

                    // ======================================
                    // METHOD
                    // ======================================

                    'withdrawal_method' => $this->withdrawalMethod,

                    // ======================================
                    // BANK NAME
                    // ======================================

                    'bank_name' => $this->withdrawalMethod === 'Bank Transfer'
                        ? $this->bankName
                        : null,

                    // ======================================
                    // ACCOUNT NAME
                    // ======================================

                    'account_name' => $this->withdrawalMethod === 'Cash'
                        ? null
                        : $this->accountName,

                    // ======================================
                    // ACCOUNT NUMBER
                    // ======================================

                    'account_number' => $this->withdrawalMethod === 'Cash'
                        ? null
                        : $this->accountNumber,

                    // ======================================
                    // PAYMENT REFERENCE
                    // ======================================

                    'payment_reference' => $this->paymentReference ?: null,

                    // ======================================
                    // PROCESSED BY
                    // ======================================

                    'ProcessedBy' => $processedBy,

                    // ======================================
                    // STATUS
                    // ======================================

                    'status' => 'Pending',
                ]);
            });

            // ==========================================
            // RESET FORM
            // ==========================================

            $this->reset([
                'withdrawAmount',
                'withdrawalMethod',
                'bankName',
                'accountName',
                'accountNumber',
                'paymentReference',
            ]);

            // ==========================================
            // SUCCESS EVENT
            // ==========================================

            $this->dispatch(
                'withdrawal-submitted',
                amount: number_format($requestedAmount, 2),
                taxAmount: number_format($taxAmount, 2),
                netAmount: number_format($netAmount, 2)
            );

        } catch (\Exception $e) {

            // ==========================================
            // LOG ERROR
            // ==========================================

            Log::error('Withdrawal submission failed.', [
                'message' => $e->getMessage(),
                'user_id' => $userID ?? null,
            ]);

            report($e);

            $this->dispatch(
                'withdrawal-error',
                message: 'Something went wrong while submitting your withdrawal request.'
            );

            return;
        }

        // ==========================================
        // CLEAR ADMIN MODE
        // ==========================================

        $this->targetUserID = null;

        $this->isAdminMode = false;

        // ==========================================
        // REFRESH BALANCE
        // ==========================================

        $this->refreshBalance();
    }

    // ==========================================
    // RESOLVE ACTING USER
    //
    // Admin mode -> the selected target user.
    // Otherwise  -> the logged-in user.
    // ==========================================

    private function resolveActingUser(): ?User
    {
        if ($this->isAdminMode && $this->targetUserID) {
            return User::where('UserID', $this->targetUserID)->first();
        }

        return Auth::user();
    }

    // ==========================================
    // REFRESH BALANCE
    //
    // Recomputes $totalAmount for whichever user
    // is currently being acted on. This property
    // is entangled with Alpine's "balance" in the
    // view, so calling this anywhere the acting
    // user might have changed keeps the client-side
    // validation (exceedsBalance, remainingBalance)
    // correct — instead of relying on a value that
    // was only ever set once at first page load.
    // ==========================================

    private function refreshBalance(): void
    {
        $user = $this->resolveActingUser();

        $this->totalAmount = (float) Wallet::where(
            'UserID',
            $user?->UserID
        )
            ->where('status', 'Active')
            ->where('source', '!=', 'PF')
            ->sum('amount');
    }

    // ==========================================
    // ADMIN PERMISSION CHECK
    //
    // Global module system: the caller needs Admin
    // Users (ModuleID 1) can_create. Needed because
    // open-admin-withdrawal-modal is a browser
    // event — without this check, anyone with dev
    // tools could fire it with an arbitrary userID
    // and open the modal for someone else.
    // ==========================================

    private function adminCanCreateWithdrawal(User $admin): bool
    {
        return Modules::can(Modules::ADMIN_USERS, 'create', $admin);
    }

    // ==========================================
    // SETTINGS
    // ==========================================

    public function getMinimumWithdrawalAmount(): float
    {
        return (float) (
            Setting::where(
                'name',
                'minimum_withdraw_amount'
            )->value('value')
            ?? 500
        );
    }

    public function isWithdrawalActive(): bool
    {
        return filter_var(
            Setting::where(
                'name',
                'activate_withdrawal'
            )->value('value'),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    // ==========================================
    // RENDER
    // ==========================================

    public function render()
    {
        $this->refreshBalance();

        return view(
            'livewire.components.withdrawal-modal',
            [
                'minimumWithdrawalAmount' => $this->getMinimumWithdrawalAmount(),

                'isWithdrawalActive' => $this->isWithdrawalActive(),

                // Only set in admin mode — the view
                // uses this to show whose wallet it's
                // creating a withdrawal for.
                'targetUser' => $this->isAdminMode
                    ? $this->resolveActingUser()
                    : null,
            ]
        );
    }
}
