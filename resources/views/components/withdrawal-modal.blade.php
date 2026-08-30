@props([
    'totalAmount' => 0,
    'minimumWithdrawalAmount' => 500,
])

<div
    x-data="{
        showWithdrawModal: false,

        balance: {{ (float) $totalAmount }},

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
                value = decimalParts[0] + '.' + decimalParts[1].slice(0, 2);
            }

            this.withdrawAmount = value;
        }
    }"

    x-on:open-withdraw-modal.window="showWithdrawModal = true"
    x-on:close-withdraw-modal.window="showWithdrawModal = false"
>
     <!-- WITHDRAWAL MODAL -->
    <div x-show="showWithdrawModal"
        style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        x-transition.opacity
        @click.self="showWithdrawModal = false">
        <div
            class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden border border-gray-100">

            <!-- Modal Header -->
            <div
                class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-slate-50 sticky top-0 z-10">
                <h3 class="font-bold text-gray-800 text-lg">
                    Cash Wallet Withdrawal
                </h3>

                <button type="button" @click="showWithdrawModal = false"
                    class="text-gray-400 hover:text-gray-600 font-bold text-xl cursor-pointer">
                    &times;
                </button>
            </div>


            <!-- Modal Body -->
            <div class="p-6 space-y-4">

                <!-- Available Balance -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex items-center justify-between">

                    <div>
                        <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            Available Cash Wallet
                        </span>

                        <span class="text-lg font-bold text-[#0C2C6B]">
                            ₱{{ number_format($totalAmount, 2) }}
                        </span>
                    </div>

                    <div
                        class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-900 text-base font-bold">
                        💳
                    </div>

                </div>


                <!-- Schedule Info -->
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-xs text-blue-900 space-y-2">

                    <div class="font-bold flex items-center gap-1.5 text-blue-950 text-sm">
                        <span>ℹ️</span>
                        Payout Schedule &amp; Cut-off Rules
                    </div>

                    <p>
                        • <strong>Friday 6:01 PM to Friday 6:00 PM:</strong>
                        Requests processed and released on the upcoming Monday.
                    </p>

                </div>


<div class="space-y-1.5">

    <label class="block text-xs font-semibold text-gray-700">
        Enter Withdrawal Amount
    </label>

    <!-- INPUT -->
    <div class="relative">

        <span
            class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold pointer-events-none">
            ₱
        </span>

        <input
            type="text"
            inputmode="decimal"
            x-model="withdrawAmount"
            @input="sanitizeAmount()"
            @keydown="if (['-', '+', 'e', 'E'].includes($event.key)) $event.preventDefault()"
            placeholder="0.00"
            autocomplete="off"

            :class="{
                'border-red-500 focus:border-red-500': isBelowMinimum || exceedsBalance,
                'border-gray-200 focus:border-[#0C2C6B]': !isBelowMinimum && !exceedsBalance
            }"

            class="w-full pl-8 pr-4 py-2 border rounded-xl focus:outline-none text-sm font-semibold"
        />

    </div>


    <!-- BELOW MINIMUM WARNING -->
    <p
        x-show="isBelowMinimum"
        x-cloak
        class="text-xs text-red-500 font-medium"
    >
        ⚠ Minimum withdrawal amount is ₱{{ number_format($minimumWithdrawalAmount, 2) }}.
    </p>


    <!-- EXCEEDS BALANCE WARNING -->
    <p
        x-show="exceedsBalance"
        x-cloak
        class="text-xs text-red-500 font-medium"
    >
        ⚠ Withdrawal amount exceeds your available balance.
    </p>


    <!-- REMAINING BALANCE -->
    <div
        class="flex items-center justify-between mt-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg">

        <span class="text-xs text-gray-500">
            Remaining Balance
        </span>

        <span
            class="text-sm font-bold text-[#0C2C6B]"
            x-text="'₱' + formatMoney(remainingBalance)">
        </span>

    </div>


    <!-- LIVEWIRE VALIDATION ERROR -->
    @error('withdrawAmount')
        <p class="text-xs text-red-500">
            {{ $message }}
        </p>
    @enderror


    <!-- DEFAULT HINT -->
    <p
        x-show="!isBelowMinimum && !exceedsBalance"
        class="text-[11px] text-gray-500"
    >
        Minimum withdrawal amount is
        <strong>₱{{ number_format($minimumWithdrawalAmount, 2) }}</strong>
    </p>

</div>

                <!-- Withdrawal Method -->
                <div class="space-y-1.5">

                    <label class="block text-xs font-semibold text-gray-700">
                        Withdrawal Method
                    </label>

                    <select wire:model.live="withdrawalMethod"
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0C2C6B] text-sm">
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


                <!-- ONLINE ACCOUNT DETAILS -->
                @if ($this->withdrawalMethod && $this->withdrawalMethod !== 'Cash')
                    <!-- Account Name -->
                    <div class="space-y-1.5">

                        <label class="block text-xs font-semibold text-gray-700">
                            Account Name
                        </label>

                        <input type="text" wire:model="accountName" placeholder="Enter account holder name"
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0C2C6B] text-sm" />

                        @error('accountName')
                            <p class="text-xs text-red-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    <!-- Account Number -->
                    <div class="space-y-1.5">

                        <label class="block text-xs font-semibold text-gray-700">
                            Account Number
                        </label>

                        <input type="text" wire:model="accountNumber" placeholder="Enter account number"
                            class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0C2C6B] text-sm" />

                        @error('accountNumber')
                            <p class="text-xs text-red-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>
                @endif

            </div>


            <!-- Modal Footer -->
            <div
                class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50 sticky bottom-0">

                <button @click="showWithdrawModal = false" type="button" wire:loading.attr="disabled"
                    class="px-4 py-2 border border-gray-200 text-gray-600 hover:bg-gray-100 text-xs font-semibold rounded-xl transition cursor-pointer">
                    Cancel
                </button>


                <button
                    type="button"
                    @click="window.confirmWithdrawal()"
                    wire:loading.attr="disabled"
                    wire:target="submitWithdrawal"
                    class="px-4 py-2 bg-[#0C2C6B] hover:bg-[#09204f] text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer disabled:opacity-50">

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
</div>
