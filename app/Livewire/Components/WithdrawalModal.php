<?php

namespace App\Livewire\Components;

use App\Models\Ledger;
use App\Models\Privilege;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
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
        Log::info('WithdrawalModal: open-admin-withdrawal-modal received.', [
            'admin_id' => Auth::id(),
            'target_user_id' => $userID,
        ]);

        $admin = Auth::user();

        if (!$admin || !$this->adminCanCreateWithdrawal($admin)) {

            Log::warning('WithdrawalModal: admin lacks permission to create withdrawal.', [
                'admin_id' => Auth::id(),
            ]);

            $this->dispatch(
                'withdrawal-error',
                message: 'You do not have permission to create a withdrawal request.'
            );

            return;
        }

        $targetUser = User::where('UserID', $userID)->first();

        if (!$targetUser) {

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
        // The regular dashboard flow always acts on
        // the logged-in user, never admin mode.
        if (!$this->isAdminMode) {
            $this->targetUserID = null;
        }

        Log::info('WithdrawalModal: check-pending-withdrawal event received.', [
            'auth_user_id' => Auth::id(),
            'admin_mode' => $this->isAdminMode,
            'target_user_id' => $this->targetUserID,
        ]);

        $this->refreshBalance();

        if (!$this->isWithdrawalActive()) {

            Log::warning('WithdrawalModal: withdrawals are currently inactive.');

            $this->dispatch(
                'withdrawal-error',
                message: 'Withdrawals are currently unavailable. Please try again later.'
            );

            return;
        }

        $user = $this->resolveActingUser();

        if (!$user) {

            Log::warning('WithdrawalModal: no acting user resolved.');

            $this->dispatch(
                'withdrawal-error',
                message: 'You must be logged in.'
            );

            return;
        }

        Log::info('WithdrawalModal: checking pending withdrawal.', [
            'user_id' => $user->UserID,
        ]);

        $pendingWithdrawal = Withdrawal::where(
            'UserID',
            $user->UserID
        )
            ->where('status', 'Pending')
            ->latest('WithdrawID')
            ->first();

        if ($pendingWithdrawal) {

            Log::info('WithdrawalModal: pending withdrawal found.', [
                'withdraw_id' => $pendingWithdrawal->WithdrawID,
            ]);

            // ==========================================
            // RESOLVE WHO PROCESSED / CREATED THIS
            // WITHDRAWAL REQUEST
            //
            // ProcessedBy stores the UserID of whoever
            // took the action — the member themself on
            // the dashboard, or the admin who created it
            // on the member's behalf via Admin Users.
            // ==========================================

            $processedByUser = $pendingWithdrawal->ProcessedBy
                ? User::where('UserID', $pendingWithdrawal->ProcessedBy)->first()
                : null;

            $processedByName = $processedByUser
                ? trim($processedByUser->firstname . ' ' . $processedByUser->lastname)
                : 'N/A';

            $this->dispatch(
                'pending-withdrawal',
                amount: number_format(
                    (float) $pendingWithdrawal->amount,
                    2
                ),
                method: $pendingWithdrawal->withdrawal_method,
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

        Log::info('WithdrawalModal: no pending withdrawal. Opening modal.');

        $this->dispatch('open-withdraw-modal');

        Log::info('WithdrawalModal: open-withdraw-modal dispatched.');
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

        // Re-check admin permission on every submit,
        // not just when the modal was opened — the
        // component's state can outlive the original
        // check across requests.
        if ($this->isAdminMode) {

            $admin = Auth::user();

            if (!$admin || !$this->adminCanCreateWithdrawal($admin)) {

                $this->dispatch(
                    'withdrawal-error',
                    message: 'You do not have permission to create a withdrawal request.'
                );

                return;
            }
        }

        $user = $this->resolveActingUser();

        if (!$user) {
            $this->dispatch(
                'withdrawal-error',
                message: 'You must be logged in.'
            );

            return;
        }

        $userID = $user->UserID;

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

        // Always re-check the balance right before
        // validating — never trust whatever is sitting
        // in $this->totalAmount from an earlier render.
        $this->refreshBalance();

        $availableBalance = $this->totalAmount;

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

        if ($this->withdrawalMethod === 'Bank Transfer') {
            $rules['bankName'] = [
                'required',
                'string',
                'max:255',
            ];
        }

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

        $this->validate($rules, [
            'withdrawAmount.required' => 'Please enter a withdrawal amount.',
            'withdrawAmount.numeric' => 'Withdrawal amount must be a valid number.',
            'withdrawAmount.min' => "Minimum withdrawal amount is ₱{$minimumWithdrawal}.",
            'withdrawAmount.max' => 'Withdrawal amount exceeds the available balance.',
            'withdrawalMethod.required' => 'Please select a withdrawal method.',
            'bankName.required' => 'Please select a bank.', // <--- Added custom message
            'accountName.required' => 'Please enter the account name.',
            'accountNumber.required' => 'Please enter the account number.',
        ]);
        try {

            $submittedAmount = (float) $this->withdrawAmount;
            $processedBy = Auth::id();

            DB::transaction(function () use ($userID, $processedBy) {

                $type = $this->withdrawalMethod === 'Cash'
                    ? 'W'
                    : 'O';

                // ==========================================
                // CREATE WITHDRAWAL REQUEST ONLY
                // Ledger entries will be created upon approval
                // ==========================================

                Withdrawal::create([
                    'UserID' => $userID,
                    'amount' => (float) $this->withdrawAmount,
                    'type' => $type,
                    'withdrawal_method' => $this->withdrawalMethod,

                    'bank_name' => $this->withdrawalMethod === 'Bank Transfer'
                    ? $this->bankName
                    : null, // <--- Added bank_name mapping

                    'account_name' => $this->withdrawalMethod === 'Cash'
                        ? null
                        : $this->accountName,

                    'account_number' => $this->withdrawalMethod === 'Cash'
                        ? null
                        : $this->accountNumber,

                    'payment_reference' => $this->paymentReference ?: null,

                    'ProcessedBy' => $processedBy,

                    'status' => 'Pending',
                ]);
            });

            $this->reset([
                'withdrawAmount',
                'withdrawalMethod',
                'bankName',
                'accountName',
                'accountNumber',
                'paymentReference',
            ]);

            $this->dispatch(
                'withdrawal-submitted',
                amount: number_format($submittedAmount, 2)
            );

        } catch (\Exception $e) {

            Log::error('Withdrawal submission failed.', [
                'message' => $e->getMessage(),
            ]);

            report($e);

            $this->dispatch(
                'withdrawal-error',
                message: 'Something went wrong while submitting your withdrawal request.'
            );

            return;
        }

        // Admin mode only lives for a single request —
        // clear it so the next open (dashboard or a
        // different admin target) starts clean.
        $this->targetUserID = null;
        $this->isAdminMode = false;

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
    // Mirrors AdminUsers::hasPermission('create').
    // Needed because open-admin-withdrawal-modal is
    // a browser event — without this check, anyone
    // with dev tools could fire it with an arbitrary
    // userID and open the modal for someone else.
    // ==========================================

    private function adminCanCreateWithdrawal(User $admin): bool
    {
        if ((int) $admin->RoleID === 1) {
            return true;
        }

        if ($admin->role && method_exists($admin->role, 'permissions')) {

            $hasPermission = optional($admin->role->permissions)
                ->where('ModuleID', 1)
                ->where('can_create', 1)
                ->isNotEmpty();

            if ($hasPermission) {
                return true;
            }
        }

        return Privilege::where('RoleID', $admin->RoleID)
            ->where('ModuleID', 1)
            ->where('can_create', 1)
            ->exists();
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
                'minimumWithdrawalAmount' =>
                    $this->getMinimumWithdrawalAmount(),

                'isWithdrawalActive' =>
                    $this->isWithdrawalActive(),

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
