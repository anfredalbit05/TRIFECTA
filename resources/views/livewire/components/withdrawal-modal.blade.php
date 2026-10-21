<div
    wire:key="withdrawal-modal-{{ $targetUser->UserID ?? 'self' }}"
    x-data="{
        showWithdrawModal: false,
        balance: @entangle('totalAmount'),
        minimumWithdrawalAmount: {{ (float) $minimumWithdrawalAmount }},
        withdrawAmount: @entangle('withdrawAmount').live,

        get amount() {
            const value = parseFloat(this.withdrawAmount);
            return isNaN(value) ? 0 : value;
        },

        get remainingBalance() {
            return Math.max(0, this.balance - this.amount);
        },

        get isBelowMinimum() {
            return this.withdrawAmount !== '' &&
                this.amount > 0 &&
                this.amount < this.minimumWithdrawalAmount;
        },

        get exceedsBalance() {
            return this.amount > this.balance;
        },

        formatMoney(value) {
            return new Intl.NumberFormat('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value);
        },

        sanitizeAmount() {
            let value = this.withdrawAmount ?? '';

            value = value.replace(/[^0-9.]/g, '');

            const parts = value.split('.');

            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            if (value.includes('.')) {
                const decimalParts = value.split('.');
                value = decimalParts[0] + '.' + decimalParts[1].slice(0, 2);
            }

            this.withdrawAmount = value;
        }
    }"

    x-on:open-withdraw-modal.window="showWithdrawModal = true"
    x-on:close-withdraw-modal.window="showWithdrawModal = false"
>
    <div
        x-show="showWithdrawModal"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 backdrop-blur-sm p-4"
        @click.self="showWithdrawModal = false"
    >
        <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">

            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-lg">
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
                                <span class="text-xs text-slate-400">
                                    Request a withdrawal from your wallet
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <button
                    type="button"
                    @click="showWithdrawModal = false"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                >
                    &times;
                </button>
            </div>

            <div class="max-h-[75vh] overflow-y-auto px-6 py-5">

                <div class="mb-5 grid grid-cols-2 gap-3">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Available Balance
                        </span>

                        <span class="mt-1 block text-xl font-bold text-[#0C2C6B]">
                            ₱{{ number_format($totalAmount, 2) }}
                        </span>
                    </div>

                    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-blue-500">
                            Remaining
                        </span>

                        <span
                            class="mt-1 block text-xl font-bold text-[#0C2C6B]"
                            x-text="'₱' + formatMoney(remainingBalance)"
                        ></span>
                    </div>
                </div>

                <div class="mb-5 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3">
                    <div class="mb-1 flex items-center gap-2 text-sm font-bold text-blue-950">
                        Payout Schedule
                    </div>

                    <p class="text-xs leading-5 text-blue-900">
                        Friday 6:01 PM to Friday 6:00 PM requests are processed
                        and released on the upcoming Monday.
                    </p>
                </div>

                <div class="space-y-5">

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Withdrawal Amount
                        </label>

                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 font-bold text-slate-400">
                                ₱
                            </span>

                            <input
                                type="text"
                                inputmode="decimal"
                                x-model="withdrawAmount"
                                @input="sanitizeAmount()"
                                @keydown="
                                    if (['-', '+', 'e', 'E'].includes($event.key)) {
                                        $event.preventDefault()
                                    }
                                "
                                placeholder="0.00"
                                autocomplete="off"
                                :class="{
                                    'border-red-400 focus:border-red-500 focus:ring-red-100':
                                        isBelowMinimum || exceedsBalance,
                                    'border-slate-200 focus:border-[#0C2C6B] focus:ring-blue-50':
                                        !isBelowMinimum && !exceedsBalance
                                }"
                                class="w-full rounded-xl border py-3 pl-8 pr-4 text-sm font-semibold outline-none focus:ring-2"
                            >
                        </div>

                        <p
                            x-show="isBelowMinimum"
                            x-cloak
                            class="mt-1.5 text-xs font-medium text-red-500"
                        >
                            Minimum withdrawal amount is
                            ₱{{ number_format($minimumWithdrawalAmount, 2) }}.
                        </p>

                        <p
                            x-show="exceedsBalance"
                            x-cloak
                            class="mt-1.5 text-xs font-medium text-red-500"
                        >
                            Withdrawal amount exceeds available balance.
                        </p>

                        @error('withdrawAmount')
                            <p class="mt-1.5 text-xs text-red-500">
                                {{ $message }}
                            </p>
                        @enderror

                        <p
                            x-show="!isBelowMinimum && !exceedsBalance"
                            class="mt-1.5 text-[11px] text-slate-400"
                        >
                            Minimum withdrawal:
                            <strong class="text-slate-600">
                                ₱{{ number_format($minimumWithdrawalAmount, 2) }}
                            </strong>
                        </p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Withdrawal Method
                        </label>

                        <select
                            wire:model.live="withdrawalMethod"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#0C2C6B] focus:ring-2 focus:ring-blue-50"
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
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#0C2C6B] focus:ring-2 focus:ring-blue-50"
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
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#0C2C6B] focus:ring-2 focus:ring-blue-50"
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
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#0C2C6B] focus:ring-2 focus:ring-blue-50"
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
                    class="rounded-xl bg-[#0C2C6B] px-5 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-[#09204f] disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="submitWithdrawal">
                        Submit Request
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

        const amount = parseFloat($wire.withdrawAmount) || 0;
        const balance = parseFloat($wire.totalAmount) || 0;

        // ==========================================
        // INVALID AMOUNT
        // ==========================================

        if (!amount || amount <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Amount',
                text: 'Please enter a valid withdrawal amount first.',
                confirmButtonColor: '#0C2C6B'
            });

            return;
        }

        // ==========================================
        // EXCEEDS BALANCE
        // ==========================================

        if (amount > balance) {
            Swal.fire({
                icon: 'error',
                title: 'Insufficient Balance',
                html: `
                    <div class="text-center">

                        <p class="mb-3 text-sm text-slate-600">
                            The withdrawal amount exceeds your available balance.
                        </p>

                        <div class="space-y-2 rounded-xl border border-red-100 bg-red-50 p-4">

                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">
                                    Available Balance
                                </span>

                                <strong class="text-sm text-[#0C2C6B]">
                                    ₱${balance.toLocaleString('en-PH', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    })}
                                </strong>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">
                                    Withdrawal Amount
                                </span>

                                <strong class="text-sm text-red-600">
                                    ₱${amount.toLocaleString('en-PH', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    })}
                                </strong>
                            </div>

                        </div>

                        <p class="mt-3 text-xs text-slate-500">
                            Please enter an amount that does not exceed your available balance.
                        </p>

                    </div>
                `,
                confirmButtonText: 'Okay',
                confirmButtonColor: '#0C2C6B'
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
                confirmButtonColor: '#0C2C6B'
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
                confirmButtonColor: '#0C2C6B'
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
                confirmButtonColor: '#0C2C6B'
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
                    confirmButtonColor: '#0C2C6B'
                });

                return;
            }

            if (!$wire.accountNumber || $wire.accountNumber.trim() === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Account Number Required',
                    text: 'Please enter the account number.',
                    confirmButtonColor: '#0C2C6B'
                });

                return;
            }
        }

        // ==========================================
        // CONFIRMATION
        // ==========================================

        const formattedAmount = amount.toFixed(2);

        Swal.fire({
            icon: 'warning',
            title: 'Confirm Withdrawal',

            html: `
                <div class="text-left">

                    <p class="mb-3 text-sm text-slate-600">
                        You are about to submit a withdrawal request for:
                    </p>

                    <div class="mb-4 rounded-xl border border-slate-200 bg-slate-50 p-4 text-center">

                        <span class="mb-1 block text-xs text-slate-500">
                            Withdrawal Amount
                        </span>

                        <strong class="text-2xl text-[#0C2C6B]">
                            ₱${formattedAmount}
                        </strong>

                    </div>

                    <p class="mb-2 text-sm text-slate-600">
                        To confirm, type:
                    </p>

                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-center">

                        <strong class="text-amber-700">
                            ${formattedAmount}
                        </strong>

                    </div>

                </div>
            `,

            input: 'text',

            inputPlaceholder: `Type ${formattedAmount}`,

            inputAttributes: {
                autocapitalize: 'off',
                autocomplete: 'off'
            },

            showCancelButton: true,

            confirmButtonText: 'Submit Withdrawal',

            cancelButtonText: 'Cancel',

            confirmButtonColor: '#0C2C6B',

            cancelButtonColor: '#D79B10',

            reverseButtons: true,

            allowOutsideClick: false,

            allowEscapeKey: false,

            preConfirm: (value) => {

                const typedAmount = value.trim();

                if (typedAmount !== formattedAmount) {

                    Swal.showValidationMessage(
                        `Please type exactly: ${formattedAmount}`
                    );

                    return false;
                }

                return true;
            }

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
            confirmButtonColor: '#0C2C6B'
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
            confirmButtonColor: '#0C2C6B'
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

                            <strong class="text-[#0C2C6B]">
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
                                <div class="rounded-xl border border-blue-100 bg-blue-50 p-3 text-xs text-blue-900">
                                    This withdrawal will be processed through cash payout.
                                </div>
                            `
                    }

                    <div class="space-y-2 border-t border-slate-200 pt-3">

                        <div class="flex justify-between">
                            <span class="text-slate-500">
                                Status
                            </span>

                            <strong class="text-yellow-600">
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
            confirmButtonColor: '#0C2C6B',
        });

    });
</script>
@endscript
</div>
