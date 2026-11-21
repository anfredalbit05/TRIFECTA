<div
    wire:key="withdrawal-modal-{{ $targetUser->UserID ?? 'self' }}"
    x-data="{
        showWithdrawModal: false,
        balance: @entangle('totalAmount'),
        minimumWithdrawalAmount: {{ (float) $minimumWithdrawalAmount }},

        get amount() {
            return parseFloat(this.balance) || 0;
        },

        get isBelowMinimum() {
            return this.amount > 0 &&
                this.amount < this.minimumWithdrawalAmount;
        },

        formatMoney(value) {
            return new Intl.NumberFormat('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(parseFloat(value) || 0);
        }
    }"

    x-on:open-withdraw-modal.window="showWithdrawModal = true"
    x-on:close-withdraw-modal.window="showWithdrawModal = false"
>
    <div
        x-show="showWithdrawModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 backdrop-blur-sm p-4"
        @click.self="showWithdrawModal = false"
    >
        <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">

            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--color-primary-tint)] text-lg">
                            💳
                        </div>

                        <div>
                            <h3 class="text-base font-bold text-slate-800">
                                @if ($targetUser)
                                    Withdrawal for {{ $targetUser->firstname }} {{ $targetUser->lastname }}
                                @else
                                    Cash Wallet Withdrawal
                                @endif
                            </h3>

                            @if ($targetUser)
                                <span class="text-[10px] font-bold uppercase tracking-wide text-amber-600">
                                    Admin Mode
                                </span>
                            @else
                                <span class="text-xs text-slate-500">
                                    Request a withdrawal from your wallet
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <button
                    type="button"
                    @click="showWithdrawModal = false"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                >
                    &times;
                </button>
            </div>

            <div class="max-h-[75vh] overflow-y-auto px-6 py-5">

                <div class="mb-5 grid grid-cols-2 gap-3">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">
                            Cash Wallet Balance
                        </span>

                        <span
                            class="mt-1 block text-xl font-bold text-[var(--color-primary)]"
                            x-text="'₱' + formatMoney(amount)"
                        ></span>
                    </div>

                    <div class="rounded-xl border border-[var(--color-primary-tint)] bg-[var(--color-primary-tint)] p-4">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-[var(--color-primary)]">
                            Withdrawal Amount
                        </span>

                        <span
                            class="mt-1 block text-xl font-bold text-[var(--color-primary)]"
                            x-text="'₱' + formatMoney(amount)"
                        ></span>
                    </div>
                </div>

                <div class="mb-5 rounded-xl border border-[var(--color-primary-tint)] bg-[var(--color-primary-tint)] px-4 py-3">
                    <div class="mb-1 flex items-center gap-2 text-sm font-bold text-[var(--color-primary-darker)]">
                        Payout Schedule
                    </div>

                    <p class="text-xs leading-5 text-[var(--color-primary-dark)]">
                        Requests are accepted from Friday 5:00 PM to Sunday
                        11:59 PM, and are approved or released every Monday.
                    </p>
                </div>

                <div class="space-y-5">

                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                        <p class="text-xs leading-5 text-amber-800">
                            No amount to type in — this request withdraws your
                            entire Cash Wallet balance of
                            <strong x-text="'₱' + formatMoney(amount)"></strong>.
                            Minimum withdrawal:
                            <strong>₱{{ number_format($minimumWithdrawalAmount, 2) }}</strong>.
                        </p>

                        <p
                            x-show="isBelowMinimum"
                            x-cloak
                            class="mt-1.5 text-xs font-semibold text-red-600"
                        >
                            Your balance is below the minimum withdrawal amount.
                        </p>

                        @error('withdrawAmount')
                            <p class="mt-1.5 text-xs font-semibold text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Withdrawal Method
                        </label>

                        <select
                            wire:model.live="withdrawalMethod"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-[var(--color-primary)] focus:ring-2 focus:ring-[var(--color-primary-tint)]"
                        >
                            <option value="">Select withdrawal method</option>
                            <option value="Cash">Cash</option>
                            <option value="GCash">GCash</option>
                            <option value="Maya">Maya</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>

                        @error('withdrawalMethod')
                            <p class="mt-1.5 text-xs text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    @if ($this->withdrawalMethod && $this->withdrawalMethod !== 'Cash')

                        @if ($this->withdrawalMethod === 'Bank Transfer')
                            <div
                                x-data="{
                                    banks: [
                                        'Al-Amanah Islamic Investment Bank of the Philippines',
                                        'Asia United Bank Corporation (AUB)',
                                        'Bank of Commerce',
                                        'Bank of the Philippine Islands (BPI)',
                                        'BDO Unibank, Inc.',
                                        'BDO Private Bank',
                                        'China Banking Corporation (Chinabank)',
                                        'CIMB Bank Philippines Inc.',
                                        'Citibank Philippines',
                                        'CTBC Bank (Philippines) Corporation',
                                        'Development Bank of the Philippines (DBP)',
                                        'East West Banking Corporation (EastWest Bank)',
                                        'Land Bank of the Philippines (Landbank)',
                                        'Maybank Philippines, Inc.',
                                        'Metropolitan Bank and Trust Company (Metrobank)',
                                        'Philippine Bank of Communications (PBCom)',
                                        'Philippine National Bank (PNB)',
                                        'Philippine Trust Company (Philtrust Bank)',
                                        'Philippine Veterans Bank',
                                        'Rizal Commercial Banking Corporation (RCBC)',
                                        'Security Bank Corporation',
                                        'Union Bank of the Philippines (UnionBank)',
                                        'United Overseas Bank Limited Manila Branch'
                                    ]
                                }"
                            >
                                <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                                    Destination Bank
                                </label>

                                <select
                                    wire:model="bankName"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-[var(--color-primary)] focus:ring-2 focus:ring-[var(--color-primary-tint)]"
                                >
                                    <option value="">Select destination bank</option>

                                    <template x-for="bank in banks" :key="bank">
                                        <option :value="bank" x-text="bank"></option>
                                    </template>
                                </select>

                                @error('bankName')
                                    <p class="mt-1.5 text-xs text-red-500">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        @endif

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                                Account Name
                            </label>

                            <input
                                type="text"
                                wire:model="accountName"
                                placeholder="Enter account holder name"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[var(--color-primary)] focus:ring-2 focus:ring-[var(--color-primary-tint)]"
                            >

                            @error('accountName')
                                <p class="mt-1.5 text-xs text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                                Account Number
                            </label>

                            <input
                                type="text"
                                wire:model="accountNumber"
                                placeholder="Enter account number"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[var(--color-primary)] focus:ring-2 focus:ring-[var(--color-primary-tint)]"
                            >

                            @error('accountNumber')
                                <p class="mt-1.5 text-xs text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    @endif
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">
                <button
                    type="button"
                    @click="showWithdrawModal = false"
                    wire:loading.attr="disabled"
                    class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    @click="confirmWithdrawal()"
                    wire:loading.attr="disabled"
                    wire:target="submitWithdrawal"
                    class="rounded-xl bg-[var(--color-primary)] px-5 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-[var(--color-primary-dark)] disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="submitWithdrawal">
                        Request Now
                    </span>

                    <span wire:loading wire:target="submitWithdrawal">
                        Processing...
                    </span>
                </button>
            </div>
        </div>
    </div>

@script
<script>
    window.confirmWithdrawal = function () {

        // The withdrawal amount is always the full Cash
        // Wallet balance — there is no amount field.
        const amount = parseFloat($wire.totalAmount) || 0;

        // ==========================================
        // INVALID AMOUNT
        // ==========================================

        if (!amount || amount <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Available Balance',
                text: 'Your Cash Wallet has no withdrawable balance.',
                confirmButtonColor: 'var(--color-primary)'
            });

            return;
        }

        // ==========================================
        // MINIMUM WITHDRAWAL
        // ==========================================

        const minimumWithdrawalAmount =
            parseFloat(@js($minimumWithdrawalAmount)) || 0;

        if (amount < minimumWithdrawalAmount) {
            Swal.fire({
                icon: 'warning',
                title: 'Amount Below Minimum',
                text:
                    `Minimum withdrawal amount is ₱${minimumWithdrawalAmount.toLocaleString(
                        'en-PH',
                        {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }
                    )}.`,
                confirmButtonColor: 'var(--color-primary)'
            });

            return;
        }

        // ==========================================
        // PAYMENT METHOD
        // ==========================================

        const withdrawalMethod = $wire.withdrawalMethod;

        if (!withdrawalMethod || withdrawalMethod.trim() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Withdrawal Method Required',
                text: 'Please select a withdrawal method before submitting.',
                confirmButtonColor: 'var(--color-primary)'
            });

            return;
        }

        // ==========================================
        // BANK
        // ==========================================

        if (
            withdrawalMethod === 'Bank Transfer' &&
            (!$wire.bankName || $wire.bankName.trim() === '')
        ) {
            Swal.fire({
                icon: 'warning',
                title: 'Bank Required',
                text: 'Please select a destination bank.',
                confirmButtonColor: 'var(--color-primary)'
            });

            return;
        }

        // ==========================================
        // ACCOUNT DETAILS
        // ==========================================

        if (withdrawalMethod !== 'Cash') {

            if (!$wire.accountName || $wire.accountName.trim() === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Account Name Required',
                    text: 'Please enter the account name.',
                    confirmButtonColor: 'var(--color-primary)'
                });

                return;
            }

            if (!$wire.accountNumber || $wire.accountNumber.trim() === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Account Number Required',
                    text: 'Please enter the account number.',
                    confirmButtonColor: 'var(--color-primary)'
                });

                return;
            }
        }

        // ==========================================
        // CONFIRMATION
        // ==========================================

        const formattedAmount = amount.toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        Swal.fire({
            icon: 'question',
            title: 'Request This Withdrawal?',

            html: `
                <div class="text-left">

                    <p class="mb-3 text-sm text-slate-600">
                        Your entire Cash Wallet balance will be requested:
                    </p>

                    <div class="mb-4 rounded-xl border border-slate-200 bg-slate-50 p-4 text-center">

                        <span class="mb-1 block text-xs text-slate-500">
                            Withdrawal Amount
                        </span>

                        <strong class="text-2xl text-[var(--color-primary)]">
                            ₱${formattedAmount}
                        </strong>

                    </div>

                    <div class="rounded-xl border border-slate-200 p-4">

                        <div class="flex items-center justify-between gap-4">
                            <span class="text-xs text-slate-500">
                                Method
                            </span>

                            <strong class="text-right text-xs text-slate-700">
                                ${withdrawalMethod}
                            </strong>
                        </div>

                    </div>

                </div>
            `,

            showCancelButton: true,

            confirmButtonText: 'Request Now',

            cancelButtonText: 'Cancel',

            confirmButtonColor: 'var(--color-primary)',

            cancelButtonColor: 'var(--color-accent)',

            reverseButtons: true,

            allowOutsideClick: false,

            allowEscapeKey: false

        }).then((result) => {

            if (!result.isConfirmed) {
                return;
            }

            // ==========================================
            // SHOW LOADING
            // ==========================================

            Swal.fire({
                title: 'Submitting Withdrawal...',
                text: 'Please wait.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // ==========================================
            // SUBMIT LIVEWIRE
            // ==========================================

            $wire.submitWithdrawal();
        });
    };


    // ==========================================
    // WITHDRAWAL SUCCESS
    // ==========================================

    window.addEventListener('withdrawal-submitted', (event) => {

        // Close the loading Swal first
        Swal.close();

        Swal.fire({
            icon: 'success',
            title: 'Withdrawal Submitted',
            text:
                `Your withdrawal request of ₱${event.detail.amount} has been submitted.`,
            confirmButtonColor: 'var(--color-primary)'
        }).then(() => {

            window.dispatchEvent(
                new CustomEvent('close-withdraw-modal')
            );

        });
    });


    // ==========================================
    // WITHDRAWAL ERROR
    // ==========================================

    window.addEventListener('withdrawal-error', (event) => {

        // Close the loading Swal
        Swal.close();

        Swal.fire({
            icon: 'error',
            title: 'Withdrawal Failed',
            text: event.detail.message ?? 'Something went wrong.',
            confirmButtonColor: 'var(--color-primary)'
        });
    });


    // ==========================================
    // PENDING WITHDRAWAL
    // ==========================================

    window.addEventListener('pending-withdrawal', (event) => {

        const data = event.detail;

        Swal.fire({
            icon: 'warning',
            title: 'Pending Withdrawal Request',

            html: `
                <div class="space-y-3 text-left text-sm">

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">

                        <div class="flex justify-between">
                            <span class="text-slate-500">
                                Amount
                            </span>

                            <strong class="text-[var(--color-primary)]">
                                ₱${data.amount}
                            </strong>
                        </div>

                        <div class="mt-2 flex justify-between">
                            <span class="text-slate-500">
                                Method
                            </span>

                            <strong>
                                ${data.method}
                            </strong>
                        </div>

                        <div class="mt-2 flex justify-between">
                            <span class="text-slate-500">
                                Processed By
                            </span>

                            <strong>
                                ${data.processedBy ?? 'N/A'}
                            </strong>
                        </div>

                    </div>

                    ${
                        data.method !== 'Cash'
                            ? `
                                <div class="space-y-2 rounded-xl border border-slate-200 bg-slate-50 p-3">

                                    ${
                                        data.method === 'Bank Transfer'
                                            ? `
                                                <div class="flex justify-between gap-4">
                                                    <span class="text-slate-500">
                                                        Bank
                                                    </span>

                                                    <strong class="text-right">
                                                        ${data.bankName ?? '-'}
                                                    </strong>
                                                </div>
                                            `
                                            : ''
                                    }

                                    <div class="flex justify-between gap-4">
                                        <span class="text-slate-500">
                                            Account Name
                                        </span>

                                        <strong class="text-right">
                                            ${data.accountName ?? '-'}
                                        </strong>
                                    </div>

                                    <div class="flex justify-between gap-4">
                                        <span class="text-slate-500">
                                            Account Number
                                        </span>

                                        <strong class="text-right">
                                            ${data.accountNumber ?? '-'}
                                        </strong>
                                    </div>

                                </div>
                            `
                            : `
                                <div class="rounded-xl border border-[var(--color-primary-tint)] bg-[var(--color-primary-tint)] p-3 text-xs text-[var(--color-primary-dark)]">
                                    This withdrawal will be processed through cash payout.
                                </div>
                            `
                    }

                    <div class="space-y-2 border-t border-slate-200 pt-3">

                        <div class="flex justify-between">
                            <span class="text-slate-500">
                                Status
                            </span>

                            <strong class="text-amber-600">
                                Pending
                            </strong>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-slate-500">
                                Requested
                            </span>

                            <strong>
                                ${data.date}
                            </strong>
                        </div>

                    </div>

                </div>
            `,

            confirmButtonText: 'Okay',
            confirmButtonColor: 'var(--color-primary)',
        });

    });
</script>
@endscript
</div>
