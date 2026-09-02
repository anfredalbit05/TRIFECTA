<div
    wire:key="withdrawal-modal-{{ $targetUser->UserID ?? 'self' }}"
    x-data="{
        showWithdrawModal: false,

    balance: @entangle('totalAmount'),

    minimumWithdrawalAmount: {{ (float) $minimumWithdrawalAmount }},

    withdrawAmount: @entangle('withdrawAmount').live,

    get amount() {
        let value = parseFloat(this.withdrawAmount);

        return isNaN(value) ? 0 : value;
    },

    get remainingBalance() {
        return Math.max(0, this.balance - this.amount);
    },

    get isBelowMinimum() {
        return this.withdrawAmount !== ''
            && this.amount > 0
            && this.amount < this.minimumWithdrawalAmount;
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

        let parts = value.split('.');

        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }

        if (value.includes('.')) {
            let decimalParts = value.split('.');

            value = decimalParts[0]
                + '.'
                + decimalParts[1].slice(0, 2);
        }

        this.withdrawAmount = value;
    }
}"

x-on:open-withdraw-modal.window="
    console.log('WithdrawalModal: open-withdraw-modal received');
    showWithdrawModal = true;
"

x-on:close-withdraw-modal.window="
    console.log('WithdrawalModal: close-withdraw-modal received');
    showWithdrawModal = false;
"
>

{{-- ==========================================
     WITHDRAWAL MODAL
========================================== --}}
<div
    x-show="showWithdrawModal"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    x-transition.opacity
    @click.self="showWithdrawModal = false"
>

    <div
        class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden border border-gray-100"
    >

        {{-- ==========================================
             MODAL HEADER
        ========================================== --}}
        <div
            class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-slate-50"
        >

            <div>

                <h3 class="font-bold text-gray-800 text-lg">
                    @if ($targetUser)
                        Withdrawal for {{ $targetUser->firstname }} {{ $targetUser->lastname }}
                    @else
                        Cash Wallet Withdrawal
                    @endif
                </h3>

                {{-- ADMIN MODE BADGE --}}
                @if ($targetUser)

                    <span
                        class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-md bg-amber-100 text-amber-700 text-[10px] font-bold uppercase tracking-wide"
                    >
                        🛡️ Admin Mode
                    </span>

                @endif

            </div>

            <button
                type="button"
                @click="showWithdrawModal = false"
                class="text-gray-400 hover:text-gray-600 font-bold text-xl cursor-pointer"
            >
                &times;
            </button>

        </div>


        {{-- ==========================================
             MODAL BODY
        ========================================== --}}
        <div class="p-6 space-y-4">

            {{-- AVAILABLE BALANCE --}}
            <div
                class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex items-center justify-between"
            >

                <div>

                    <span
                        class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider"
                    >
                        @if ($targetUser)
                            {{ $targetUser->firstname }}'s Cash Wallet
                        @else
                            Available Cash Wallet
                        @endif
                    </span>

                    <span
                        class="text-lg font-bold text-[#0C2C6B]"
                    >
                        ₱{{ number_format($totalAmount, 2) }}
                    </span>

                </div>

                <div
                    class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-900 text-base font-bold"
                >
                    💳
                </div>

            </div>


            {{-- PAYOUT SCHEDULE --}}
            <div
                class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-xs text-blue-900 space-y-2"
            >

                <div
                    class="font-bold flex items-center gap-1.5 text-blue-950 text-sm"
                >
                    <span>ℹ️</span>

                    Payout Schedule & Cut-off Rules
                </div>

                <p>
                    • <strong>Friday 6:01 PM to Friday 6:00 PM:</strong>

                    Requests processed and released on the upcoming Monday.
                </p>

            </div>


            {{-- ==========================================
                 WITHDRAWAL AMOUNT
            ========================================== --}}
            <div class="space-y-1.5">

                <label
                    class="block text-xs font-semibold text-gray-700"
                >
                    Enter Withdrawal Amount
                </label>


                <div class="relative">

                    <span
                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold pointer-events-none"
                    >
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
                            'border-red-500 focus:border-red-500':
                                isBelowMinimum || exceedsBalance,

                            'border-gray-200 focus:border-[#0C2C6B]':
                                !isBelowMinimum && !exceedsBalance
                        }"

                        class="w-full pl-8 pr-4 py-2 border rounded-xl focus:outline-none text-sm font-semibold"
                    />

                </div>


                {{-- BELOW MINIMUM --}}
                <p
                    x-show="isBelowMinimum"
                    x-cloak
                    class="text-xs text-red-500 font-medium"
                >
                    ⚠ Minimum withdrawal amount is
                    ₱{{ number_format($minimumWithdrawalAmount, 2) }}.
                </p>


                {{-- EXCEEDS BALANCE --}}
                <p
                    x-show="exceedsBalance"
                    x-cloak
                    class="text-xs text-red-500 font-medium"
                >
                    ⚠ Withdrawal amount exceeds
                    @if ($targetUser)
                        this user's
                    @else
                        your
                    @endif
                    available balance.
                </p>


                {{-- REMAINING BALANCE --}}
                <div
                    class="flex items-center justify-between mt-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg"
                >

                    <span class="text-xs text-gray-500">
                        Remaining Balance
                    </span>

                    <span
                        class="text-sm font-bold text-[#0C2C6B]"
                        x-text="'₱' + formatMoney(remainingBalance)"
                    >
                    </span>

                </div>


                {{-- LIVEWIRE ERROR --}}
                @error('withdrawAmount')

                    <p class="text-xs text-red-500">
                        {{ $message }}
                    </p>

                @enderror


                {{-- DEFAULT HINT --}}
                <p
                    x-show="!isBelowMinimum && !exceedsBalance"
                    class="text-[11px] text-gray-500"
                >
                    Minimum withdrawal amount is

                    <strong>
                        ₱{{ number_format($minimumWithdrawalAmount, 2) }}
                    </strong>

                </p>

            </div>


            {{-- ==========================================
                 WITHDRAWAL METHOD
            ========================================== --}}
            <div class="space-y-1.5">

                <label
                    class="block text-xs font-semibold text-gray-700"
                >
                    Withdrawal Method
                </label>


                <select
                    wire:model.live="withdrawalMethod"

                    class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0C2C6B] text-sm"
                >

                    <option value="">
                        Select withdrawal method
                    </option>

                    <option value="Cash">
                        Cash
                    </option>

                    <option value="GCash">
                        GCash
                    </option>

                    <option value="Maya">
                        Maya
                    </option>

                    <option value="Bank Transfer">
                        Bank Transfer
                    </option>

                </select>


                @error('withdrawalMethod')

                    <p class="text-xs text-red-500">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- ==========================================
                ONLINE ACCOUNT DETAILS
            ========================================== --}}
            @if (
                $this->withdrawalMethod
                && $this->withdrawalMethod !== 'Cash'
            )

                {{-- BANK SELECTION (ONLY FOR BANK TRANSFER) --}}
                @if ($this->withdrawalMethod === 'Bank Transfer')
                    <div class="space-y-1.5" x-data="{
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
                    }">
                        <label class="block text-xs font-semibold text-gray-700">
                            Select Bank
                        </label>

                        <select
                            wire:model="bankName"
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0C2C6B] text-sm bg-white"
                        >
                            <option value="">Select destination bank</option>
                            <template x-for="bank in banks" :key="bank">
                                <option :value="bank" x-text="bank"></option>
                            </template>
                        </select>

                        @error('bankName')
                            <p class="text-xs text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                @endif

                {{-- ACCOUNT NAME --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-gray-700">
                        Account Name *
                    </label>

                    <input
                        type="text"
                        wire:model="accountName"
                        placeholder="Enter account holder name"
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0C2C6B] text-sm"
                    />

                    @error('accountName')
                        <p class="text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- ACCOUNT NUMBER --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-gray-700">
                        Account Number *
                    </label>

                    <input
                        type="text"
                        wire:model="accountNumber"
                        placeholder="Enter account number"
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0C2C6B] text-sm"
                    />

                    @error('accountNumber')
                        <p class="text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            @endif

        </div>


        {{-- ==========================================
             MODAL FOOTER
        ========================================== --}}
        <div
            class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50"
        >

            {{-- CANCEL --}}
            <button
                type="button"

                @click="showWithdrawModal = false"

                wire:loading.attr="disabled"

                class="px-4 py-2 border border-gray-200 text-gray-600 hover:bg-gray-100 text-xs font-semibold rounded-xl transition cursor-pointer"
            >
                Cancel
            </button>


            {{-- SUBMIT --}}
            <button
                type="button"

                @click="confirmWithdrawal()"

                wire:loading.attr="disabled"

                wire:target="submitWithdrawal"

                class="px-4 py-2 bg-[#0C2C6B] hover:bg-[#09204f] text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer disabled:opacity-50"
            >

                <span
                    wire:loading.remove
                    wire:target="submitWithdrawal"
                >
                    Submit Request
                </span>


                <span
                    wire:loading
                    wire:target="submitWithdrawal"
                >
                    Processing...
                </span>

            </button>

        </div>

    </div>

</div>


{{-- ==========================================
     JAVASCRIPT
========================================== --}}
@script
<script>

    // ==========================================
    // CONFIRM WITHDRAWAL WITH VALIDATION
    // ==========================================

    window.confirmWithdrawal = function () {

        // ==========================================
        // VALIDATE WITHDRAWAL AMOUNT
        // ==========================================

        const amount = parseFloat($wire.withdrawAmount);

        if (!amount || amount <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Amount',
                text: 'Please enter a valid withdrawal amount first.',
                confirmButtonColor: '#0C2C6B',
            });

            return;
        }

        // ==========================================
        // VALIDATE WITHDRAWAL METHOD
        // ==========================================

        const withdrawalMethod = $wire.withdrawalMethod;

        if (!withdrawalMethod || withdrawalMethod.trim() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Withdrawal Method Required',
                text: 'Please select a withdrawal method before submitting.',
                confirmButtonColor: '#0C2C6B',
            });

            return;
        }

        // ==========================================
        // VALIDATE BANK NAME
        // ==========================================

        if (
            withdrawalMethod === 'Bank Transfer' &&
            (!$wire.bankName || $wire.bankName.trim() === '')
        ) {
            Swal.fire({
                icon: 'warning',
                title: 'Bank Required',
                text: 'Please select a destination bank.',
                confirmButtonColor: '#0C2C6B',
            });

            return;
        }

        // ==========================================
        // VALIDATE ACCOUNT DETAILS
        // Only required for non-cash withdrawals
        // ==========================================

        if (withdrawalMethod !== 'Cash') {

            if (
                !$wire.accountName ||
                $wire.accountName.trim() === ''
            ) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Account Name Required',
                    text: 'Please enter the account name.',
                    confirmButtonColor: '#0C2C6B',
                });

                return;
            }

            if (
                !$wire.accountNumber ||
                $wire.accountNumber.trim() === ''
            ) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Account Number Required',
                    text: 'Please enter the account number.',
                    confirmButtonColor: '#0C2C6B',
                });

                return;
            }
        }

        // ==========================================
        // FORMAT AMOUNT
        // ==========================================

        const formattedAmount = amount.toFixed(2);

        // ==========================================
        // CONFIRM WITHDRAWAL
        // ==========================================

        Swal.fire({
            icon: 'warning',
            title: 'Confirm Withdrawal',

            html: `
                <div class="text-left">

                    <p class="mb-3 text-sm text-slate-600">
                        You are about to submit a withdrawal request for:
                    </p>

                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-4 text-center">

                        <span class="block text-xs text-slate-500 mb-1">
                            Withdrawal Amount
                        </span>

                        <strong class="text-2xl text-[#0C2C6B]">
                            ₱${formattedAmount}
                        </strong>

                    </div>

                    <p class="text-sm text-slate-600 mb-2">
                        To confirm, type:
                    </p>

                    <div class="bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 text-center">

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

            if (result.isConfirmed) {

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

                $wire.submitWithdrawal();
            }

        });

    };


    // ==========================================
    // PENDING WITHDRAWAL
    // ==========================================

    window.addEventListener(
        'pending-withdrawal',
        (event) => {

            console.log(
                'WithdrawalModal: pending withdrawal received.',
                event.detail
            );

            const data = event.detail;

            Swal.fire({
                icon: 'warning',

                title: 'Pending Withdrawal Request',

html: `
    <div class="text-left space-y-3 text-sm">

        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
            <div class="flex justify-between items-center">
                <span class="text-gray-500">Amount</span>
                <strong class="text-[#0C2C6B]">
                    ₱${data.amount}
                </strong>
            </div>

            <div class="flex justify-between items-center mt-2">
                <span class="text-gray-500">Method</span>
                <strong>
                    ${data.method}
                </strong>
            </div>

            <div class="flex justify-between items-center mt-2">
                <span class="text-gray-500">Processed By</span>
                <strong>
                    ${data.processedBy ?? 'N/A'}
                </strong>
            </div>
        </div>

        ${
            data.method !== 'Cash'
                ? `
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 space-y-2">
                        ${data.method === 'Bank Transfer' ? `
                            <div class="flex justify-between">
                                <span class="text-gray-500">Bank Name</span>
                                <strong>${data.bankName ?? '-'}</strong>
                            </div>
                        ` : ''}

                        <div class="flex justify-between">
                            <span class="text-gray-500">Account Name</span>
                            <strong>${data.accountName ?? '-'}</strong>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Account Number</span>
                            <strong>${data.accountNumber ?? '-'}</strong>
                        </div>
                    </div>
                `
                : `
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 text-xs text-blue-900">
                        This withdrawal will be processed through cash payout.
                    </div>
                `
        }

        <div class="border-t border-gray-200 pt-3 space-y-2">

            <div class="flex justify-between">
                <span class="text-gray-500">Status</span>
                <strong class="text-yellow-600">
                    Pending
                </strong>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Requested</span>
                <strong>
                    ${data.date}
                </strong>
            </div>

        </div>

    </div>
`,

                confirmButtonText: 'Okay',

                confirmButtonColor: '#0C2C6B'
            });

        }
    );


    // ==========================================
    // WITHDRAWAL SUBMITTED
    // ==========================================

    window.addEventListener(
        'withdrawal-submitted',
        (event) => {

            console.log(
                'WithdrawalModal: withdrawal submitted.',
                event.detail
            );

            Swal.fire({
                icon: 'success',

                title: 'Withdrawal Submitted',

                text: `Your withdrawal request of ₱${event.detail.amount} has been submitted.`,

                confirmButtonColor: '#0C2C6B'

            }).then(() => {

                window.dispatchEvent(
                    new CustomEvent(
                        'close-withdraw-modal'
                    )
                );

            });

        }
    );

</script>
@endscript


</div>
