<div>
    @if($open)
        <!-- Modal Backdrop -->
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 p-3 sm:p-4 backdrop-blur-sm overflow-y-auto">

            <!-- Modal Container -->
            <div class="bg-white rounded-2xl w-full max-w-4xl my-auto max-h-[90vh] flex flex-col shadow-2xl border border-slate-100 overflow-hidden">

                <!-- Sticky Header -->
                <div class="flex justify-between items-center px-5 py-4 border-b border-slate-200 sticky top-0 bg-white z-10 shadow-sm">
                    <div>
                        <h2 class="font-extrabold text-lg sm:text-xl text-[#0B2A5B]">
                            Membership Application
                        </h2>
                        <p class="text-xs text-slate-500">Dayongan Solidaritas • Vitae Auxilium</p>
                    </div>

                    <button
                        wire:click="close"
                        type="button"
                        class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-rose-100 hover:text-rose-600 font-bold transition">
                        ✕
                    </button>
                </div>

                <!-- Scrollable Form Body -->
                <div class="p-4 sm:p-6 space-y-6 overflow-y-auto">

                    <!-- APPLICANT INFORMATION -->
                    <div class="space-y-4">
                        <h3 class="bg-[#0B2A5B] text-[#E8B923] px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm tracking-wider uppercase shadow-sm">
                            Applicant Information
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pt-1">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Surname *</label>
                                <input wire:model="surname" type="text" placeholder="Dela Cruz" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">First Name *</label>
                                <input wire:model="first_name" type="text" placeholder="Juan" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Middle Name</label>
                                <input wire:model="middle_name" type="text" placeholder="Santos" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Gender *</label>
                                <select wire:model="gender" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:outline-none bg-white">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Civil Status *</label>
                                <select wire:model="civil_status" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:outline-none bg-white">
                                    <option value="">Select Status</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Separated">Separated</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Birthdate *</label>
                                <input wire:model="birthdate" type="date" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Mobile Number *</label>
                                <input wire:model="mobile" type="tel" placeholder="0917XXXXXXX" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Email Address</label>
                                <input wire:model="email" type="email" placeholder="juan@example.com" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:outline-none">
                            </div>

                            <div class="sm:col-span-2 lg:col-span-3">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Complete Address *</label>
                                <input wire:model="address" type="text" placeholder="House No., Street, Barangay, City/Municipality, Province" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- BENEFICIARY INFORMATION -->
                    <div class="space-y-4">
                        <h3 class="bg-[#0B2A5B] text-[#E8B923] px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm tracking-wider uppercase shadow-sm flex items-center justify-between">
                            <span>Beneficiary Information</span>
                            <span class="text-[10px] font-normal text-slate-200 uppercase">Up to 3 Dependents</span>
                        </h3>

                        <div class="space-y-4">
                            @for($i = 0; $i < 3; $i++)
                                <div class="p-3 sm:p-4 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Beneficiary {{ $i + 1 }} Full Name
                                        </label>
                                        <input
                                            wire:model="beneficiaries.{{ $i }}.name"
                                            type="text"
                                            placeholder="Full name of beneficiary"
                                            class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:outline-none bg-white">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Beneficiary {{ $i + 1 }} Birthdate
                                        </label>
                                        <input
                                            wire:model="beneficiaries.{{ $i }}.birthdate"
                                            type="date"
                                            class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#0B2A5B] focus:outline-none bg-white">
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- ACCEPTANCE OF TERMS -->
                    <div class="space-y-3">
                        <h3 class="bg-[#0B2A5B] text-[#E8B923] px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm tracking-wider uppercase shadow-sm">
                            Acceptance of Terms
                        </h3>

                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs sm:text-sm text-slate-600 leading-relaxed max-h-32 overflow-y-auto">
                            <p class="font-semibold text-slate-800 mb-1">Membership Terms & Policy Summary:</p>
                            I acknowledge that the information provided is true, accurate, and correct to the best of my knowledge. I agree to the standard micro-insurance coverage guidelines, claim procedures, and privacy policies established by Dayongan and partner underwriters.
                        </div>

                        <label class="flex items-start sm:items-center gap-3 mt-3 cursor-pointer select-none">
                            <input type="checkbox" wire:model="agree" class="mt-0.5 sm:mt-0 w-4 h-4 rounded text-[#0B2A5B] focus:ring-[#0B2A5B] border-slate-300">
                            <span class="text-xs sm:text-sm font-semibold text-slate-700">
                                I have read and agree to the terms and conditions.
                            </span>
                        </label>
                    </div>

                </div>

                <!-- Sticky Footer Actions -->
                <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <span class="text-xs text-slate-500 text-center sm:text-left">
                        * Please verify all details before submitting.
                    </span>

                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <button
                            wire:click="close"
                            type="button"
                            class="w-1/2 sm:w-auto px-5 py-2.5 border border-slate-300 rounded-xl text-slate-600 text-xs font-bold hover:bg-slate-100 transition text-center">
                            Cancel
                        </button>

                        <button
                            wire:click="submit"
                            type="button"
                            class="w-1/2 sm:w-auto px-6 py-2.5 bg-[#D4A017] hover:bg-[#E8B923] text-[#0B2A5B] font-bold text-xs rounded-xl shadow-md transition text-center">
                            Submit Application
                        </button>
                    </div>
                </div>

            </div>

        </div>
    @endif
</div>
