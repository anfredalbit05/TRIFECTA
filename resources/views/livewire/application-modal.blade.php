<div>
    @if($open)
        {{-- ================================================= --}}
        {{-- MODAL BACKDROP --}}
        {{-- ================================================= --}}
        <div class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto bg-black/70 p-3 backdrop-blur-sm sm:p-4">

            {{-- ================================================= --}}
            {{-- MODAL CONTAINER --}}
            {{-- ================================================= --}}
            <div class="my-auto flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-2xl">

                {{-- ================================================= --}}
                {{-- HEADER --}}
                {{-- ================================================= --}}
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-5 py-4 shadow-sm">
                    <div>
                        <h2 class="text-lg font-extrabold text-[#0B2A5B] sm:text-xl">
                            Membership Application
                        </h2>
                        <p class="text-xs text-slate-500">
                            Dayongan Solidaritas • Vitae Auxilium
                        </p>
                    </div>

                    <button
                        wire:click="close"
                        type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 font-bold text-slate-500 transition hover:bg-rose-100 hover:text-rose-600"
                        title="Close"
                    >
                        ✕
                    </button>
                </div>

                {{-- ================================================= --}}
                {{-- FORM BODY --}}
                {{-- ================================================= --}}
                <div class="space-y-6 overflow-y-auto p-4 sm:p-6">

                    {{-- ================================================= --}}
                    {{-- APPLICANT INFORMATION --}}
                    {{-- ================================================= --}}
                    <div class="space-y-4">
                        <h3 class="rounded-xl bg-[#0B2A5B] px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-[#E8B923] shadow-sm sm:text-sm">
                            Applicant Information
                        </h3>

                        <div class="grid grid-cols-1 gap-4 pt-1 sm:grid-cols-2 lg:grid-cols-3">
                            {{-- SURNAME --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Surname *
                                </label>
                                <input
                                    wire:model="surname"
                                    type="text"
                                    placeholder="Dela Cruz"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                                >
                            </div>

                            {{-- FIRST NAME --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    First Name *
                                </label>
                                <input
                                    wire:model="first_name"
                                    type="text"
                                    placeholder="Juan"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                                >
                            </div>

                            {{-- MIDDLE NAME --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Middle Name (Optional)
                                </label>
                                <input
                                    wire:model="middle_name"
                                    type="text"
                                    placeholder="Santos"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                                >
                            </div>

                            {{-- GENDER --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Gender *
                                </label>
                                <select
                                    wire:model="gender"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                                >
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            {{-- CIVIL STATUS --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Civil Status *
                                </label>
                                <select
                                    wire:model="civil_status"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                                >
                                    <option value="">Select Status</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Separated">Separated</option>
                                </select>
                            </div>

                            {{-- BIRTHDATE --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Birthdate *
                                </label>
                                <input
                                    wire:model="birthdate"
                                    type="date"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                                >
                            </div>

                            {{-- COMPLETE ADDRESS --}}
                            <div class="sm:col-span-2 lg:col-span-3">
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Complete Address *
                                </label>
                                <input
                                    wire:model="address"
                                    type="text"
                                    placeholder="House No., Street, Barangay, City/Municipality, Province"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                                >
                            </div>
                        </div>
                    </div>

                    {{-- ================================================= --}}
                    {{-- BENEFICIARY INFORMATION --}}
                    {{-- ================================================= --}}
                    <div class="space-y-4">
                        <h3 class="flex items-center justify-between rounded-xl bg-[#0B2A5B] px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-[#E8B923] shadow-sm sm:text-sm">
                            <span>Beneficiary Information</span>
                            <span class="text-[10px] font-normal uppercase text-slate-200">
                                Up to 3 Dependents
                            </span>
                        </h3>

                        <div class="space-y-4">
                            @for($i = 0; $i < 3; $i++)
                                <div class="grid grid-cols-1 gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3 sm:grid-cols-2 sm:p-4 lg:grid-cols-3">
                                    {{-- FULL NAME --}}
                                    <div>
                                        <label class="mb-1 block text-xs font-bold text-slate-700">
                                            Beneficiary {{ $i + 1 }} Full Name
                                        </label>
                                        <input
                                            wire:model="beneficiaries.{{ $i }}.fullname"
                                            type="text"
                                            placeholder="Full name of beneficiary"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                                        >
                                    </div>

                                    {{-- BIRTHDATE --}}
                                    <div>
                                        <label class="mb-1 block text-xs font-bold text-slate-700">
                                            Birthdate
                                        </label>
                                        <input
                                            wire:model="beneficiaries.{{ $i }}.birthdate"
                                            type="date"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                                        >
                                    </div>

                                    {{-- RELATIONSHIP --}}
                                    <div>
                                        <label class="mb-1 block text-xs font-bold text-slate-700">
                                            Relationship
                                        </label>
                                        <input
                                            wire:model="beneficiaries.{{ $i }}.relationship"
                                            type="text"
                                            placeholder="Father, Mother, Spouse..."
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                                        >
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    {{-- ================================================= --}}
                    {{-- SECURITY INFORMATION --}}
                    {{-- ================================================= --}}
                    <div class="space-y-4">
                        <h3 class="rounded-xl bg-[#0B2A5B] px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-[#E8B923] shadow-sm sm:text-sm">
                            Security Information
                        </h3>

                        <div class="grid grid-cols-1 gap-4 pt-1 sm:grid-cols-2 lg:grid-cols-3">
                            {{-- MOBILE NUMBER --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Mobile Number *
                                </label>
                                <input
                                    wire:model="mobile"
                                    type="tel"
                                    placeholder="0917XXXXXXX"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                                >
                            </div>

                            {{-- EMAIL --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Email Address (Optional)
                                </label>
                                <input
                                    wire:model="email"
                                    type="email"
                                    placeholder="juan@example.com"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                                >
                            </div>

                            {{-- SPACER FOR DESKTOP GRID --}}
                            <div class="hidden lg:block"></div>

                            {{-- USERNAME --}}
                            <div class="lg:col-start-1">
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Username *
                                </label>
                                <input
                                    wire:model="username"
                                    type="text"
                                    placeholder="Juan123"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                                >
                            </div>

                            {{-- PASSWORD --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Password *
                                </label>
                                <input
                                    wire:model="password"
                                    type="password"
                                    placeholder="********"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                                >
                            </div>

                            {{-- CONFIRM PASSWORD --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Confirm Password *
                                </label>
                                <input
                                    wire:model="password_confirmation"
                                    type="password"
                                    placeholder="********"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
                                >
                            </div>
                        </div>
                    </div>

                    {{-- ================================================= --}}
                    {{-- ACCEPTANCE OF TERMS --}}
                    {{-- ================================================= --}}
                    <div class="space-y-3">
                        <h3 class="rounded-xl bg-[#0B2A5B] px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-[#E8B923] shadow-sm sm:text-sm">
                            Acceptance of Terms
                        </h3>

                        <div class="max-h-32 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs leading-relaxed text-slate-600 sm:text-sm">
                            <p class="mb-1 font-semibold text-slate-800">
                                Membership Terms & Policy Summary:
                            </p>
                            I acknowledge that the information provided is true, accurate, and correct to the best of my knowledge. I agree to the standard micro-insurance coverage guidelines, claim procedures, and privacy policies established by Dayongan and partner underwriters.
                        </div>

                        <label class="mt-3 flex cursor-pointer select-none items-start gap-3 sm:items-center">
                            <input
                                type="checkbox"
                                wire:model="agree"
                                class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#0B2A5B] focus:ring-[#0B2A5B] sm:mt-0"
                            >
                            <span class="text-xs font-semibold text-slate-700 sm:text-sm">
                                I have read and agree to the terms and conditions.
                            </span>
                        </label>
                    </div>

                </div>

                {{-- ================================================= --}}
                {{-- FOOTER ACTIONS --}}
                {{-- ================================================= --}}
                <div class="flex flex-col items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 p-4 sm:flex-row">
                    <span class="text-center text-xs text-slate-500 sm:text-left">
                        * Please verify all details before submitting.
                    </span>

                    <div class="flex w-full items-center gap-3 sm:w-auto">
                        <button
                            wire:click="close"
                            type="button"
                            class="w-1/2 rounded-xl border border-slate-300 px-5 py-2.5 text-center text-xs font-bold text-slate-600 transition hover:bg-slate-100 sm:w-auto"
                        >
                            Cancel
                        </button>

                        {{-- SUBMIT BUTTON --}}
                        <button
                            wire:click="submit"
                            wire:loading.attr="disabled"
                            type="button"
                            class="flex w-1/2 items-center justify-center rounded-xl bg-[#D4A017] px-6 py-2.5 text-xs font-bold text-[#0B2A5B] shadow-md transition hover:bg-[#E8B923] disabled:cursor-not-allowed disabled:opacity-75 sm:w-auto"
                        >
                            {{-- NORMAL STATE --}}
                            <span wire:loading.remove wire:target="submit">
                                Submit Application
                            </span>

                            {{-- LOADING STATE --}}
                            <div wire:loading.flex wire:target="submit" class="items-center justify-center gap-2">
                                <svg class="h-4 w-4 shrink-0 animate-spin text-[#0B2A5B]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="whitespace-nowrap">Submitting...</span>
                            </div>
                        </button>
                    </div>
                </div>

            </div>

        </div>
    @endif
</div>
