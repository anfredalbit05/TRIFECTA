<div>
    @if($open)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 p-2 sm:p-4 backdrop-blur-sm overflow-y-auto">
            <!-- MODAL CONTAINER -->
            <div class="bg-white rounded-2xl w-full max-w-4xl my-auto max-h-[92vh] sm:max-h-[90vh] flex flex-col shadow-2xl border border-slate-100 overflow-hidden">

                <!-- =====================================================
                    HEADER
                ====================================================== -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 px-4 sm:px-6 py-4 border-b border-slate-200 sticky top-0 bg-white z-10 shadow-sm">
                    <div class="w-full sm:w-auto">
                        <h2 class="font-extrabold text-base sm:text-xl text-[#0B2A5B]">
                            Account Activation
                        </h2>
                        <p class="mt-0.5 text-[11px] sm:text-xs text-slate-500">
                            TRUELIFE • Membership Activation
                        </p>
                        <div class="mt-3 flex items-center gap-2.5 rounded-xl border border-blue-100 bg-blue-50 px-3 py-2">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#0B2A5B] text-xs font-bold text-[#E8B923]">
                                {{ strtoupper(substr($selectedUserRecord?->firstname ?? 'M', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                    Activating Member
                                </p>
                                <p class="text-xs sm:text-sm font-extrabold text-[#0B2A5B] truncate">
                                    {{ trim(($selectedUserRecord?->firstname ?? '') . ' ' . ($selectedUserRecord?->middlename ?? '') . ' ' . ($selectedUserRecord?->lastname ?? '')) ?: 'Member not selected' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <button
                        wire:click="closeModal"
                        type="button"
                        class="absolute top-4 right-4 sm:relative sm:top-0 sm:right-0 w-9 h-9 shrink-0 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-rose-100 hover:text-rose-600 font-bold transition cursor-pointer">
                        ✕
                    </button>
                </div>

                <!-- =====================================================
                    MODAL BODY
                ====================================================== -->
                <div class="p-4 sm:p-6 space-y-6 overflow-y-auto">

                    <!-- =================================================
                        REMARKS
                    ================================================== -->
                    @if(!empty($rejectionRemarks))
                        <section class="space-y-4">
                            <h3 class="bg-[#0B2A5B] text-[#E8B923] px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm tracking-wider uppercase shadow-sm">
                                Reason For Rejections
                            </h3>
                            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                                <p class="text-xs sm:text-sm text-red-600">
                                    {{ $rejectionRemarks }}
                                </p>
                            </div>
                        </section>
                    @endif

                    <!-- =================================================
                        BENEFICIARY INFORMATION
                    ================================================== -->
                    <section class="space-y-4">
                        <h3 class="bg-[#0B2A5B] text-[#E8B923] px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm tracking-wider uppercase shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <span>Beneficiary Information</span>
                            <span class="text-[10px] font-normal text-slate-200 uppercase">Maximum 3 Dependents</span>
                        </h3>

                        <div class="space-y-4">
                            @foreach($beneficiaries as $index => $beneficiary)
                                <div class="p-3 sm:p-4 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- FULL NAME -->
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Beneficiary {{ $index + 1 }} Full Name
                                        </label>
                                        <input
                                            wire:model="beneficiaries.{{ $index }}.fullname"
                                            type="text"
                                            placeholder="Full name of beneficiary"
                                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:outline-none bg-white">
                                        @error("beneficiaries.$index.fullname")
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- BIRTHDATE -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Birthdate
                                        </label>
                                        <input
                                            wire:model="beneficiaries.{{ $index }}.birthdate"
                                            type="date"
                                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:outline-none bg-white">
                                        @error("beneficiaries.$index.birthdate")
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- RELATIONSHIP -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Relationship
                                        </label>
                                        <input
                                            wire:model="beneficiaries.{{ $index }}.relationship"
                                            type="text"
                                            placeholder="Mother, Father, Spouse..."
                                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:outline-none bg-white">
                                        @error("beneficiaries.$index.relationship")
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <!-- =================================================
                        PAYMENT INFORMATION
                    ================================================== -->
                    <section class="space-y-5">
                        <h3 class="bg-[#0B2A5B] text-[#E8B923] px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm tracking-wider uppercase shadow-sm">
                            Payment Information
                        </h3>

                        <!-- PAYMENT METHOD SELECTION -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-3">
                                Select Payment Method *
                            </label>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <!-- CASH -->
                                <label class="flex items-center gap-3 cursor-pointer p-3.5 bg-white border border-slate-300 rounded-xl hover:border-[#0B2A5B] hover:bg-slate-50 transition">
                                    <input
                                        type="radio"
                                        wire:model.live="payment_method"
                                        value="Cash"
                                        class="w-4 h-4 text-[#0B2A5B] shrink-0">
                                    <div>
                                        <span class="block text-sm font-bold text-slate-700">Cash</span>
                                        <span class="block text-[11px] text-slate-500">Walk-in Payment</span>
                                    </div>
                                </label>

                                <!-- GCASH -->
                                <label
                                    class="flex items-center gap-3 p-3.5 rounded-xl border transition
                                        {{ $gcashEnabled
                                            ? 'cursor-pointer bg-white border-slate-300 hover:border-[#0B2A5B] hover:bg-slate-50'
                                            : 'cursor-not-allowed bg-slate-100 border-slate-200 opacity-60' }}"
                                >
                                    <input
                                        type="radio"
                                        wire:model.live="payment_method"
                                        value="GCash"
                                        @disabled(!$gcashEnabled)
                                        class="w-4 h-4 text-[#0B2A5B] shrink-0">

                                    <div>
                                        <span class="block text-sm font-bold text-slate-700">
                                            GCash
                                            @if(!$gcashEnabled)
                                                <span class="text-[10px] text-red-500">(Unavailable)</span>
                                            @endif
                                        </span>

                                        <span class="block text-[11px] text-slate-500">
                                            {{ $gcashEnabled ? 'Online Payment' : 'Currently unavailable' }}
                                        </span>
                                    </div>
                                </label>
                                <!-- BANK TRANSFER -->
                                <label
                                    class="flex items-center gap-3 p-3.5 rounded-xl border transition
                                        {{ $bankEnabled
                                            ? 'cursor-pointer bg-white border-slate-300 hover:border-[#0B2A5B] hover:bg-slate-50'
                                            : 'cursor-not-allowed bg-slate-100 border-slate-200 opacity-60' }}"
                                >
                                    <input
                                        type="radio"
                                        wire:model.live="payment_method"
                                        value="Bank Transfer"
                                        @disabled(!$bankEnabled)
                                        class="w-4 h-4 text-[#0B2A5B] shrink-0">

                                    <div>
                                        <span class="block text-sm font-bold text-slate-700">
                                            Bank Transfer
                                            @if(!$bankEnabled)
                                                <span class="text-[10px] text-red-500">(Unavailable)</span>
                                            @endif
                                        </span>

                                        <span class="block text-[11px] text-slate-500">
                                            {{ $bankEnabled ? 'Online Payment' : 'Currently unavailable' }}
                                        </span>
                                    </div>
                                </label>
                            </div>

                            @error('payment_method')
                                <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ACTIVATION AMOUNT AND REFERRAL CODE -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- ACTIVATION AMOUNT -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Activation Amount
                                </label>
                                <input
                                    wire:model="amount"
                                    readonly
                                    type="text"
                                    class="w-full bg-slate-100 border border-slate-300 rounded-xl px-3 py-2.5 text-sm text-slate-700">
                            </div>

                            <!-- REFERRAL CODE -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Upline Code *
                                </label>
                                <input
    wire:model="upline_code"
    type="text"
    placeholder="Enter upline code"
    @readonly($selectedUserRecord?->status === 'Active')
    class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm
        focus:ring-2 focus:ring-[#0B2A5B] focus:outline-none
        {{ $selectedUserRecord?->status === 'Active'
            ? 'bg-slate-100 text-slate-600 cursor-not-allowed'
            : 'bg-white' }}">
                                @error('upline_code')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- CASH PAYMENT MESSAGE -->
                        @if($payment_method === 'Cash')
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                                <p class="text-sm font-bold text-amber-800">Cash Payment — Walk-in</p>
                                <p class="mt-1 text-xs text-amber-700">Please pay the ₱1,999 activation fee at the authorized office or designated payment location.</p>
                                <p class="mt-2 text-[11px] text-amber-600">Your activation will be recorded as Walk-in (W).</p>
                            </div>
                        @endif

                        <!-- ONLINE PAYMENT REFERENCE -->
                        @if($payment_method === 'GCash' || $payment_method === 'Bank Transfer')
                            <div class="space-y-3 rounded-xl border border-blue-200 bg-blue-50 p-4">
                                <div>
                                    <p class="text-sm font-bold text-blue-800">Online Payment</p>
                                    <p class="mt-1 text-xs text-blue-700">Complete your payment first, then enter the transaction reference number below.</p>
                                </div>

                        {{-- GCASH DETAILS --}}
                        @if($payment_method === 'GCash')
                            <div class="rounded-lg border border-blue-200 bg-white p-3 text-sm space-y-3">

                                <!-- GCASH ACCOUNT -->
                                <div>
                                    <p class="mb-1">
                                        <span class="font-bold text-slate-700">Send to (GCash Name):</span>
                                        {{ $gcashName }}
                                    </p>

                                    <p>
                                        <span class="font-bold text-slate-700">GCash Number:</span>
                                        {{ $gcashNumber }}
                                    </p>
                                </div>

                                <!-- PAYMENT BREAKDOWN -->
                                <div class="border-t border-slate-200 pt-3">

                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                                        Payment Breakdown
                                    </p>

                                    <div class="space-y-1.5 text-sm">

                                        <!-- MEMBERSHIP FEE -->
                                        <div class="flex justify-between gap-4">
                                            <span class="text-slate-600">
                                                Membership Fee
                                            </span>

                                            <span class="font-semibold text-slate-700">
                                                ₱1,999.00
                                            </span>
                                        </div>

                                        @if($gcashConvenienceFeeEnabled)

                                            <!-- GCASH CF -->
                                            <div class="flex justify-between gap-4">
                                                <span class="text-slate-600">
                                                    GCash Convenience Fee (1%)
                                                </span>

                                                <span class="font-semibold text-slate-700">
                                                    ₱19.99
                                                </span>
                                            </div>

                                            <!-- TOTAL -->
                                            <div class="flex justify-between gap-4 border-t border-slate-200 pt-2 mt-2">
                                                <span class="font-extrabold text-[#0B2A5B]">
                                                    Total Amount to Pay
                                                </span>

                                                <span class="font-extrabold text-[#0B2A5B]">
                                                    ₱2,018.99
                                                </span>
                                            </div>

                                        @else

                                            <!-- TOTAL WITHOUT CF -->
                                            <div class="flex justify-between gap-4 border-t border-slate-200 pt-2 mt-2">
                                                <span class="font-extrabold text-[#0B2A5B]">
                                                    Total Amount to Pay
                                                </span>

                                                <span class="font-extrabold text-[#0B2A5B]">
                                                    ₱1,999.00
                                                </span>
                                            </div>

                                        @endif

                                    </div>
                                </div>

                                @if($gcashConvenienceFeeEnabled)
                                    <div class="rounded-lg bg-amber-50 border border-amber-200 px-3 py-2">
                                        <p class="text-[11px] text-amber-700">
                                            A 1% GCash convenience fee (₱19.99) is included in the total payment.
                                        </p>
                                    </div>
                                @endif

                            </div>
                        @endif

                                {{-- BANK DETAILS --}}
                                @if($payment_method === 'Bank Transfer')
                                    <div class="rounded-lg border border-blue-200 bg-white p-3 text-sm">
                                        <p class="mb-1"><span class="font-bold text-slate-700">Bank:</span> {{ $bankName }}</p>
                                        <p class="mb-1"><span class="font-bold text-slate-700">Account Name:</span> {{ $bankAccountName }}</p>
                                        <p><span class="font-bold text-slate-700">Account Number:</span> {{ $bankAccountNumber }}</p>
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">
                                        Payment Reference Code *
                                    </label>
                                    <input
                                        wire:model="reference_code"
                                        type="text"
                                        placeholder="Enter GCash or bank transaction reference"
                                        class="w-full bg-white border border-blue-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:outline-none">
                                    @error('reference_code')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <p class="text-[11px] text-blue-600">Your activation will be recorded as Online (O).</p>
                            </div>
                        @endif
                    </section>

                    <!-- =================================================
                        CONFIRMATION
                    ================================================== -->
                    <section class="space-y-3">
                        <h3 class="bg-[#0B2A5B] text-[#E8B923] px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm tracking-wider uppercase shadow-sm">
                            Confirmation
                        </h3>
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs sm:text-sm text-slate-600">
                            Please verify all beneficiary information and payment details before activating your account.
                        </div>
                    </section>
                </div>

                <!-- =====================================================
                    FOOTER
                ====================================================== -->
                <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <span class="text-xs text-slate-500 text-center sm:text-left">
                        * Activation fee is fixed at {{ $amount }}
                    </span>

                    <div class="flex flex-col-reverse sm:flex-row gap-2.5 w-full sm:w-auto">
                        <!-- CANCEL BUTTON -->
                        <button
                            wire:click="closeModal"
                            type="button"
                            class="w-full sm:w-auto px-5 py-2.5 border0 bg-[#D79B10] text-white rounded-xl shadow-md transition cursor-pointer">
                            Cancel
                        </button>

                        <!-- ACTIVATE BUTTON -->
                        <button
                            type="button"
                            wire:click="confirmActivate"
                            wire:loading.attr="disabled"
                            class="w-full sm:w-auto px-6 py-2.5 bg-[#0B2A5B] hover:bg-blue-800 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center cursor-pointer">
                            <span wire:loading.remove wire:target="confirmActivate">
                                {{ $isReactivation ? 'Reactivate Account' : 'Activate Account' }}
                            </span>
                            <span wire:loading wire:target="confirmActivate">
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif

<script>
    window.addEventListener('swal:confirm', event => {
        Swal.fire({
            title: event.detail[0].title,
            text: event.detail[0].text,
            icon: event.detail[0].icon,
            showCancelButton: true,
            confirmButtonColor: '#0B2A5B',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, proceed!',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing Activation...',
                    text: 'Please wait while we process your request.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                Livewire.dispatch(event.detail[0].method);
            }
        });
    });

    window.addEventListener('swal:loading', event => {
        Swal.fire({
            title: event.detail[0].title,
            text: event.detail[0].text,
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });

     window.addEventListener('swal:success', event => {
        Swal.fire({
            icon: 'success',
            title: event.detail[0].title,
            text: event.detail[0].text,
            confirmButtonText: 'OK',
            confirmButtonColor: '#0B2A5B',
            allowOutsideClick: false
        }).then(() => {
            Livewire.dispatch('members-refresh');
        });
    });

    window.addEventListener('swal:close', event => {
        Swal.close();
    });
</script>
</div>
