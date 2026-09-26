<div>
    @if ($open)

        {{-- =========================================================
             MODAL BACKDROP
        ========================================================== --}}
        <div
            class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto bg-black/70 p-3 backdrop-blur-sm sm:p-4">

            {{-- =========================================================
                 MODAL CONTAINER
            ========================================================== --}}
            <div
                class="my-auto flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-2xl">

                {{-- =====================================================
                     HEADER
                ====================================================== --}}
                <div
                    class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-5 py-4 shadow-sm">
                    <div>
                        <h2 class="text-lg font-extrabold text-[#0B2A5B] sm:text-xl">
                            Membership Application
                        </h2>
                        <p class="text-xs text-slate-500">
                            True Life Solidaritas &bull; Vitae Auxilium
                        </p>
                    </div>
                    <button wire:click="close" type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 font-bold text-slate-500 transition hover:bg-rose-100 hover:text-rose-600"
                        title="Close">
                        &#10005;
                    </button>
                </div>

                {{-- =====================================================
                     FORM BODY
                ====================================================== --}}
                <div class="space-y-7 overflow-y-auto p-4 sm:p-6">

                    {{-- =================================================
                         1. APPLICANT PERSONAL INFORMATION
                    ================================================== --}}
                    <div class="space-y-4">
                        <div class="flex items-center justify-between rounded-xl bg-[#0B2A5B] px-4 py-2.5 shadow-sm">
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-[#E8B923] sm:text-sm">
                                    Applicant Personal Information
                                </h3>
                                <p class="mt-0.5 text-[10px] font-normal normal-case tracking-normal text-slate-200">
                                    Please provide the applicant's personal details.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 pt-1 sm:grid-cols-2 lg:grid-cols-3">

                            {{-- SURNAME --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Surname *
                                </label>
                                <input wire:model.live="surname" type="text" placeholder="Dela Cruz"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                            </div>

                            {{-- FIRST NAME --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    First Name *
                                </label>
                                <input wire:model.live="first_name" type="text" placeholder="Juan"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                            </div>

                            {{-- MIDDLE NAME --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Middle Name
                                    <span class="font-normal text-slate-400">(Optional)</span>
                                </label>
                                <input wire:model.live="middle_name" type="text" placeholder="Santos"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                            </div>

                            {{-- GENDER --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Gender *
                                </label>
                                <select wire:model="gender"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
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
                                <select wire:model="civil_status"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                                    <option value="">Select Status</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Separated">Separated</option>
                                </select>
                            </div>

                            {{-- BIRTHDATE --}}
                            <div wire:key="birthdate-picker-{{ $userId ?? 'new' }}" x-data="{
                                openPicker: false,
                                step: 'year',
                                selectedYear: '2000',
                                selectedMonth: '01',
                                selectedDay: '01',
                                years: Array.from({ length: 100 },
                                    (_, i) => new Date().getFullYear() - i
                                ),
                                months: [
                                    { name: 'January', val: '01' },
                                    { name: 'February', val: '02' },
                                    { name: 'March', val: '03' },
                                    { name: 'April', val: '04' },
                                    { name: 'May', val: '05' },
                                    { name: 'June', val: '06' },
                                    { name: 'July', val: '07' },
                                    { name: 'August', val: '08' },
                                    { name: 'September', val: '09' },
                                    { name: 'October', val: '10' },
                                    { name: 'November', val: '11' },
                                    { name: 'December', val: '12' }
                                ],
                                get daysInMonth() {
                                    return Array.from({
                                            length: new Date(
                                                Number(this.selectedYear),
                                                Number(this.selectedMonth),
                                                0
                                            ).getDate()
                                        },
                                        (_, i) => String(i + 1).padStart(2, '0')
                                    );
                                },
                                get formattedDate() {
                                    return `${this.selectedYear}-${this.selectedMonth}-${this.selectedDay}`;
                                },
                                init() {
                                    this.$watch('$wire.birthdate', (value) => {
                                        if (!value || typeof value !== 'string') {
                                            return;
                                        }
                                        const parts = value.split('-');
                                        if (parts.length === 3) {
                                            this.selectedYear = parts[0];
                                            this.selectedMonth = parts[1];
                                            this.selectedDay = parts[2];
                                        }
                                    });
                                    if (this.$wire.birthdate) {
                                        const parts = this.$wire.birthdate.split('-');
                                        if (parts.length === 3) {
                                            this.selectedYear = parts[0];
                                            this.selectedMonth = parts[1];
                                            this.selectedDay = parts[2];
                                        }
                                    }
                                },
                                selectYear(year) {
                                    this.selectedYear = String(year);
                                    this.step = 'month';
                                },
                                selectMonth(month) {
                                    this.selectedMonth = month;
                                    const maxDays = new Date(
                                        Number(this.selectedYear),
                                        Number(this.selectedMonth),
                                        0
                                    ).getDate();
                                    if (Number(this.selectedDay) > maxDays) {
                                        this.selectedDay = String(maxDays).padStart(2, '0');
                                    }
                                    this.step = 'day';
                                },
                                selectDay(day) {
                                    this.selectedDay = day;
                                },
                                confirmDate() {
                                    const formatted =
                                        `${this.selectedYear}-${this.selectedMonth}-${this.selectedDay}`;
                                    @this.set('birthdate', formatted);
                                    this.openPicker = false;
                                    this.step = 'year';
                                }
                            }"
                                class="relative">
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Birthdate *
                                </label>
                                <button type="button" @click="openPicker = true"
                                    class="flex w-full items-center justify-between rounded-xl border border-slate-300 bg-white px-3 py-2 text-left text-sm text-slate-700 transition hover:border-[#0B2A5B] focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                                    <span x-text="$wire.birthdate || 'Select Date of Birth'"
                                        :class="!$wire.birthdate ? 'text-slate-400' : 'text-slate-700'">
                                    </span>
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </button>

                                <div x-show="openPicker" x-cloak x-transition.opacity
                                    class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
                                    <div @click.stop
                                        class="w-full max-w-sm overflow-hidden rounded-2xl bg-white shadow-2xl">

                                        <div class="bg-[#E8B923] p-5 text-[#0B2A5B]">
                                            <span class="text-[10px] font-bold uppercase tracking-widest opacity-80">
                                                Select Date of Birth
                                            </span>
                                            <h2 class="mt-1 text-xl font-extrabold" x-text="formattedDate">
                                            </h2>
                                            <div class="mt-3 flex gap-3 text-xs font-bold">
                                                <button type="button" @click="step = 'year'"
                                                    :class="step === 'year' ? 'underline opacity-100' : 'opacity-60'">Year</button>
                                                <span>/</span>
                                                <button type="button" @click="step = 'month'"
                                                    :class="step === 'month' ? 'underline opacity-100' : 'opacity-60'">Month</button>
                                                <span>/</span>
                                                <button type="button" @click="step = 'day'"
                                                    :class="step === 'day' ? 'underline opacity-100' : 'opacity-60'">Day</button>
                                            </div>
                                        </div>

                                        <div class="p-4">
                                            <div x-show="step === 'year'">
                                                <div
                                                    class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                    <span>Select Year</span>
                                                    <button type="button" @click="step = 'month'"
                                                        class="text-[#0B2A5B] hover:underline">Next: Month
                                                        &rarr;</button>
                                                </div>
                                                <div
                                                    class="grid h-64 grid-cols-3 gap-2 overflow-y-auto pr-1 text-center">
                                                    <template x-for="yr in years" :key="yr">
                                                        <button type="button" @click="selectYear(yr)"
                                                            :class="String(selectedYear) === String(yr) ?
                                                                'bg-[#0B2A5B] text-white font-bold' :
                                                                'bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                                            class="rounded-lg py-2.5 text-sm transition"
                                                            x-text="yr"></button>
                                                    </template>
                                                </div>
                                            </div>

                                            <div x-show="step === 'month'">
                                                <div
                                                    class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                    <button type="button" @click="step = 'year'"
                                                        class="hover:underline">&larr; Back</button>
                                                    <span>Select Month</span>
                                                    <button type="button" @click="step = 'day'"
                                                        class="text-[#0B2A5B] hover:underline">Next: Day &rarr;</button>
                                                </div>
                                                <div
                                                    class="grid h-64 grid-cols-2 gap-2 overflow-y-auto pr-1 text-center">
                                                    <template x-for="month in months" :key="month.val">
                                                        <button type="button" @click="selectMonth(month.val)"
                                                            :class="selectedMonth === month.val ?
                                                                'bg-[#0B2A5B] text-white font-bold' :
                                                                'bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                                            class="rounded-lg py-3 text-sm transition"
                                                            x-text="month.name"></button>
                                                    </template>
                                                </div>
                                            </div>

                                            <div x-show="step === 'day'">
                                                <div
                                                    class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                    <button type="button" @click="step = 'month'"
                                                        class="hover:underline">&larr; Back</button>
                                                    <span>Select Day</span>
                                                    <span></span>
                                                </div>
                                                <div
                                                    class="grid h-64 grid-cols-5 gap-2 overflow-y-auto pr-1 text-center">
                                                    <template x-for="day in daysInMonth" :key="day">
                                                        <button type="button" @click="selectDay(day)"
                                                            :class="selectedDay === day ? 'bg-[#0B2A5B] text-white font-bold' :
                                                                'bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                                            class="rounded-lg py-2.5 text-sm transition"
                                                            x-text="day"></button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between border-t bg-slate-50 px-5 py-3">
                                            <button type="button" @click="openPicker = false; step = 'year';"
                                                class="rounded-lg px-4 py-2 text-xs font-bold text-slate-500 transition hover:bg-slate-200">Cancel</button>
                                            <button type="button" @click="confirmDate()"
                                                class="rounded-lg bg-[#0B2A5B] px-6 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#123b7a]">OK</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- OCCUPATION --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Occupation
                                </label>
                                <input wire:model.live="occupation" type="text"
                                    placeholder="e.g. Teacher, Driver, Engineer"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                            </div>
                        </div>
                    </div>

                    {{-- =================================================
                         2. POLICY INFORMATION
                    ================================================== --}}
                    @if ($isEditMode)
                        <div class="space-y-4">
                            <div
                                class="flex items-center justify-between rounded-xl bg-[#0B2A5B] px-4 py-2.5 shadow-sm">
                                <div>
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#E8B923] sm:text-sm">
                                        Policy Information
                                    </h3>
                                    <p
                                        class="mt-0.5 text-[10px] font-normal normal-case tracking-normal text-slate-200">
                                        Membership policy details and expiration information.
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 pt-1 sm:grid-cols-2 lg:grid-cols-2">
{{-- POLICY NUMBER --}}
<div>
    <label class="mb-1 block text-xs font-bold text-slate-700">
        Policy Number
    </label>

    @if (filled($policy_number))
        {{-- Already generated/issued — read-only display --}}
        <div
            class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700">
            <span>{{ $policy_number }}</span>

            <span class="text-[10px] font-bold uppercase tracking-wide text-emerald-600">
                ✓ Issued
            </span>
        </div>

    @else
        {{-- No policy number yet — Generate button + editable field --}}
        <div class="flex gap-2">
            <input
                wire:model.live="policy_number"
                type="text"
                placeholder="{{ $this->previewPolicyNumber }}"
                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]"
            >

            <button
                type="button"
                wire:click="generatePolicyNumber"
                title="Generate the next policy number based on the last issued"
                class="shrink-0 rounded-xl bg-[#0B2A5B] px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#123c88]"
            >
                Generate
            </button>
        </div>

        <p class="mt-1 text-[10px] text-slate-400">
            Next available:
            <span class="font-bold text-slate-600">
                {{ $this->previewPolicyNumber }}
            </span>
            — click Generate to use it.
        </p>
    @endif
</div>

                                {{-- RECEIPT NUMBER (acknowledgement receipt, lives on the APPROVED activation) --}}
                                <div>
                                    <label class="mb-1 block text-xs font-bold text-slate-700">
                                        Receipt Number
                                    </label>

                                    @if (filled($activationControlNumber))
                                        {{-- Already issued — read-only display --}}
                                        <div
                                            class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700">
                                            <span>{{ $activationControlNumber }}</span>
                                            <span class="text-[10px] font-bold uppercase tracking-wide text-emerald-600">
                                                ✓ Issued
                                            </span>
                                        </div>
                                    @elseif ($activationId)
                                        {{-- Approved activation without a number — Generate button + editable field.
                                             The placeholder/hint previews the REAL next number, so clicking
                                             Generate fills exactly what the hint shows. --}}
                                        <div class="flex gap-2">
                                            <input wire:model.live="receipt_number" type="text"
                                                placeholder="{{ $this->previewControlNumber }}"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                                            <button type="button" wire:click="generateControlNumber"
                                                title="Generate the next receipt number based on the last issued"
                                                class="shrink-0 rounded-xl bg-[#0B2A5B] px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#123c88]">
                                                Generate
                                            </button>
                                        </div>
                                        <p class="mt-1 text-[10px] text-slate-400">
                                            Next available: <span class="font-bold text-slate-600">{{ $this->previewControlNumber }}</span> — click Generate to use it.
                                        </p>

                                    @else
                                        {{-- No approved activation — nothing to attach a number to --}}
                                        <div
                                            class="w-full rounded-xl border border-dashed border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400">
                                            No approved activation yet
                                        </div>
                                    @endif
                                </div>

                                {{-- POLICY EFFECTIVITY DATE (calendar picker, same as Birthdate) --}}
                                <div wire:key="policy-effectivity-picker-{{ $userId ?? 'new' }}"
                                    x-data="{
                                        openPicker: false,
                                        step: 'year',
                                        selectedYear: '{{ now()->year }}',
                                        selectedMonth: '01',
                                        selectedDay: '01',
                                        years: Array.from({ length: 50 },
                                            (_, i) => new Date().getFullYear() + 25 - i
                                        ),
                                        months: [
                                            { name: 'January', val: '01' },
                                            { name: 'February', val: '02' },
                                            { name: 'March', val: '03' },
                                            { name: 'April', val: '04' },
                                            { name: 'May', val: '05' },
                                            { name: 'June', val: '06' },
                                            { name: 'July', val: '07' },
                                            { name: 'August', val: '08' },
                                            { name: 'September', val: '09' },
                                            { name: 'October', val: '10' },
                                            { name: 'November', val: '11' },
                                            { name: 'December', val: '12' }
                                        ],
                                        get daysInMonth() {
                                            return Array.from({
                                                    length: new Date(
                                                        Number(this.selectedYear),
                                                        Number(this.selectedMonth),
                                                        0
                                                    ).getDate()
                                                },
                                                (_, i) => String(i + 1).padStart(2, '0')
                                            );
                                        },
                                        get formattedDate() {
                                            return `${this.selectedYear}-${this.selectedMonth}-${this.selectedDay}`;
                                        },
                                        init() {
                                            this.$watch('$wire.policy_effectivity_date', (value) => {
                                                if (!value || typeof value !== 'string') {
                                                    return;
                                                }
                                                const parts = value.split('-');
                                                if (parts.length === 3) {
                                                    this.selectedYear = parts[0];
                                                    this.selectedMonth = parts[1];
                                                    this.selectedDay = parts[2];
                                                }
                                            });
                                            if (this.$wire.policy_effectivity_date) {
                                                const parts = this.$wire.policy_effectivity_date.split('-');
                                                if (parts.length === 3) {
                                                    this.selectedYear = parts[0];
                                                    this.selectedMonth = parts[1];
                                                    this.selectedDay = parts[2];
                                                }
                                            }
                                        },
                                        selectYear(year) {
                                            this.selectedYear = String(year);
                                            this.step = 'month';
                                        },
                                        selectMonth(month) {
                                            this.selectedMonth = month;
                                            const maxDays = new Date(
                                                Number(this.selectedYear),
                                                Number(this.selectedMonth),
                                                0
                                            ).getDate();
                                            if (Number(this.selectedDay) > maxDays) {
                                                this.selectedDay = String(maxDays).padStart(2, '0');
                                            }
                                            this.step = 'day';
                                        },
                                        selectDay(day) {
                                            this.selectedDay = day;
                                        },
                                        confirmDate() {
                                            const formatted =
                                                `${this.selectedYear}-${this.selectedMonth}-${this.selectedDay}`;
                                            @this.set('policy_effectivity_date', formatted);
                                            this.openPicker = false;
                                            this.step = 'year';
                                        }
                                    }" class="relative">
                                    <label class="mb-1 block text-xs font-bold text-slate-700">
                                        Policy Effectivity Date
                                    </label>
                                    <button type="button" @click="openPicker = true"
                                        class="flex w-full items-center justify-between rounded-xl border border-slate-300 bg-white px-3 py-2 text-left text-sm text-slate-700 transition hover:border-[#0B2A5B] focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                                        <span x-text="$wire.policy_effectivity_date || 'Select Effectivity Date'"
                                            :class="!$wire.policy_effectivity_date ? 'text-slate-400' : 'text-slate-700'">
                                        </span>
                                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </button>

                                    <div x-show="openPicker" x-cloak x-transition.opacity
                                        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
                                        <div @click.stop
                                            class="w-full max-w-sm overflow-hidden rounded-2xl bg-white shadow-2xl">

                                            <div class="bg-[#E8B923] p-5 text-[#0B2A5B]">
                                                <span
                                                    class="text-[10px] font-bold uppercase tracking-widest opacity-80">
                                                    Select Policy Effectivity Date
                                                </span>
                                                <h2 class="mt-1 text-xl font-extrabold" x-text="formattedDate">
                                                </h2>
                                                <div class="mt-3 flex gap-3 text-xs font-bold">
                                                    <button type="button" @click="step = 'year'"
                                                        :class="step === 'year' ? 'underline opacity-100' : 'opacity-60'">Year</button>
                                                    <span>/</span>
                                                    <button type="button" @click="step = 'month'"
                                                        :class="step === 'month' ? 'underline opacity-100' : 'opacity-60'">Month</button>
                                                    <span>/</span>
                                                    <button type="button" @click="step = 'day'"
                                                        :class="step === 'day' ? 'underline opacity-100' : 'opacity-60'">Day</button>
                                                </div>
                                            </div>

                                            <div class="p-4">
                                                <div x-show="step === 'year'">
                                                    <div
                                                        class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                        <span>Select Year</span>
                                                        <button type="button" @click="step = 'month'"
                                                            class="text-[#0B2A5B] hover:underline">Next: Month
                                                            &rarr;</button>
                                                    </div>
                                                    <div
                                                        class="grid h-64 grid-cols-3 gap-2 overflow-y-auto pr-1 text-center">
                                                        <template x-for="yr in years" :key="yr">
                                                            <button type="button" @click="selectYear(yr)"
                                                                :class="String(selectedYear) === String(yr) ?
                                                                    'bg-[#0B2A5B] text-white font-bold' :
                                                                    'bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                                                class="rounded-lg py-2.5 text-sm transition"
                                                                x-text="yr"></button>
                                                        </template>
                                                    </div>
                                                </div>

                                                <div x-show="step === 'month'">
                                                    <div
                                                        class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                        <button type="button" @click="step = 'year'"
                                                            class="hover:underline">&larr; Back</button>
                                                        <span>Select Month</span>
                                                        <button type="button" @click="step = 'day'"
                                                            class="text-[#0B2A5B] hover:underline">Next: Day
                                                            &rarr;</button>
                                                    </div>
                                                    <div
                                                        class="grid h-64 grid-cols-2 gap-2 overflow-y-auto pr-1 text-center">
                                                        <template x-for="month in months" :key="month.val">
                                                            <button type="button" @click="selectMonth(month.val)"
                                                                :class="selectedMonth === month.val ?
                                                                    'bg-[#0B2A5B] text-white font-bold' :
                                                                    'bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                                                class="rounded-lg py-3 text-sm transition"
                                                                x-text="month.name"></button>
                                                        </template>
                                                    </div>
                                                </div>

                                                <div x-show="step === 'day'">
                                                    <div
                                                        class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                        <button type="button" @click="step = 'month'"
                                                            class="hover:underline">&larr; Back</button>
                                                        <span>Select Day</span>
                                                        <span></span>
                                                    </div>
                                                    <div
                                                        class="grid h-64 grid-cols-5 gap-2 overflow-y-auto pr-1 text-center">
                                                        <template x-for="day in daysInMonth" :key="day">
                                                            <button type="button" @click="selectDay(day)"
                                                                :class="selectedDay === day ?
                                                                    'bg-[#0B2A5B] text-white font-bold' :
                                                                    'bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                                                class="rounded-lg py-2.5 text-sm transition"
                                                                x-text="day"></button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="flex items-center justify-between border-t bg-slate-50 px-5 py-3">
                                                <button type="button" @click="openPicker = false; step = 'year';"
                                                    class="rounded-lg px-4 py-2 text-xs font-bold text-slate-500 transition hover:bg-slate-200">Cancel</button>
                                                <button type="button" @click="confirmDate()"
                                                    class="rounded-lg bg-[#0B2A5B] px-6 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#123b7a]">OK</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                {{-- POLICY EXPIRATION DATE (calendar picker, same as Birthdate) --}}
                                <div wire:key="policy-expiration-picker-{{ $userId ?? 'new' }}"
                                    x-data="{
                                        openPicker: false,
                                        step: 'year',
                                        selectedYear: '{{ now()->year }}',
                                        selectedMonth: '01',
                                        selectedDay: '01',
                                        years: Array.from({ length: 50 },
                                            (_, i) => new Date().getFullYear() + 25 - i
                                        ),
                                        months: [
                                            { name: 'January', val: '01' },
                                            { name: 'February', val: '02' },
                                            { name: 'March', val: '03' },
                                            { name: 'April', val: '04' },
                                            { name: 'May', val: '05' },
                                            { name: 'June', val: '06' },
                                            { name: 'July', val: '07' },
                                            { name: 'August', val: '08' },
                                            { name: 'September', val: '09' },
                                            { name: 'October', val: '10' },
                                            { name: 'November', val: '11' },
                                            { name: 'December', val: '12' }
                                        ],
                                        get daysInMonth() {
                                            return Array.from({
                                                    length: new Date(
                                                        Number(this.selectedYear),
                                                        Number(this.selectedMonth),
                                                        0
                                                    ).getDate()
                                                },
                                                (_, i) => String(i + 1).padStart(2, '0')
                                            );
                                        },
                                        get formattedDate() {
                                            return `${this.selectedYear}-${this.selectedMonth}-${this.selectedDay}`;
                                        },
                                        init() {
                                            this.$watch('$wire.policy_expiration_date', (value) => {
                                                if (!value || typeof value !== 'string') {
                                                    return;
                                                }
                                                const parts = value.split('-');
                                                if (parts.length === 3) {
                                                    this.selectedYear = parts[0];
                                                    this.selectedMonth = parts[1];
                                                    this.selectedDay = parts[2];
                                                }
                                            });
                                            if (this.$wire.policy_expiration_date) {
                                                const parts = this.$wire.policy_expiration_date.split('-');
                                                if (parts.length === 3) {
                                                    this.selectedYear = parts[0];
                                                    this.selectedMonth = parts[1];
                                                    this.selectedDay = parts[2];
                                                }
                                            }
                                        },
                                        selectYear(year) {
                                            this.selectedYear = String(year);
                                            this.step = 'month';
                                        },
                                        selectMonth(month) {
                                            this.selectedMonth = month;
                                            const maxDays = new Date(
                                                Number(this.selectedYear),
                                                Number(this.selectedMonth),
                                                0
                                            ).getDate();
                                            if (Number(this.selectedDay) > maxDays) {
                                                this.selectedDay = String(maxDays).padStart(2, '0');
                                            }
                                            this.step = 'day';
                                        },
                                        selectDay(day) {
                                            this.selectedDay = day;
                                        },
                                        confirmDate() {
                                            const formatted =
                                                `${this.selectedYear}-${this.selectedMonth}-${this.selectedDay}`;
                                            @this.set('policy_expiration_date', formatted);
                                            this.openPicker = false;
                                            this.step = 'year';
                                        }
                                    }" class="relative">
                                    <label class="mb-1 block text-xs font-bold text-slate-700">
                                        Policy Expiration Date
                                    </label>
                                    <button type="button" @click="openPicker = true"
                                        class="flex w-full items-center justify-between rounded-xl border border-slate-300 bg-white px-3 py-2 text-left text-sm text-slate-700 transition hover:border-[#0B2A5B] focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                                        <span x-text="$wire.policy_expiration_date || 'Select Expiration Date'"
                                            :class="!$wire.policy_expiration_date ? 'text-slate-400' : 'text-slate-700'">
                                        </span>
                                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </button>

                                    <div x-show="openPicker" x-cloak x-transition.opacity
                                        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
                                        <div @click.stop
                                            class="w-full max-w-sm overflow-hidden rounded-2xl bg-white shadow-2xl">

                                            <div class="bg-[#E8B923] p-5 text-[#0B2A5B]">
                                                <span
                                                    class="text-[10px] font-bold uppercase tracking-widest opacity-80">
                                                    Select Policy Expiration Date
                                                </span>
                                                <h2 class="mt-1 text-xl font-extrabold" x-text="formattedDate">
                                                </h2>
                                                <div class="mt-3 flex gap-3 text-xs font-bold">
                                                    <button type="button" @click="step = 'year'"
                                                        :class="step === 'year' ? 'underline opacity-100' : 'opacity-60'">Year</button>
                                                    <span>/</span>
                                                    <button type="button" @click="step = 'month'"
                                                        :class="step === 'month' ? 'underline opacity-100' : 'opacity-60'">Month</button>
                                                    <span>/</span>
                                                    <button type="button" @click="step = 'day'"
                                                        :class="step === 'day' ? 'underline opacity-100' : 'opacity-60'">Day</button>
                                                </div>
                                            </div>

                                            <div class="p-4">
                                                <div x-show="step === 'year'">
                                                    <div
                                                        class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                        <span>Select Year</span>
                                                        <button type="button" @click="step = 'month'"
                                                            class="text-[#0B2A5B] hover:underline">Next: Month
                                                            &rarr;</button>
                                                    </div>
                                                    <div
                                                        class="grid h-64 grid-cols-3 gap-2 overflow-y-auto pr-1 text-center">
                                                        <template x-for="yr in years" :key="yr">
                                                            <button type="button" @click="selectYear(yr)"
                                                                :class="String(selectedYear) === String(yr) ?
                                                                    'bg-[#0B2A5B] text-white font-bold' :
                                                                    'bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                                                class="rounded-lg py-2.5 text-sm transition"
                                                                x-text="yr"></button>
                                                        </template>
                                                    </div>
                                                </div>

                                                <div x-show="step === 'month'">
                                                    <div
                                                        class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                        <button type="button" @click="step = 'year'"
                                                            class="hover:underline">&larr; Back</button>
                                                        <span>Select Month</span>
                                                        <button type="button" @click="step = 'day'"
                                                            class="text-[#0B2A5B] hover:underline">Next: Day
                                                            &rarr;</button>
                                                    </div>
                                                    <div
                                                        class="grid h-64 grid-cols-2 gap-2 overflow-y-auto pr-1 text-center">
                                                        <template x-for="month in months" :key="month.val">
                                                            <button type="button" @click="selectMonth(month.val)"
                                                                :class="selectedMonth === month.val ?
                                                                    'bg-[#0B2A5B] text-white font-bold' :
                                                                    'bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                                                class="rounded-lg py-3 text-sm transition"
                                                                x-text="month.name"></button>
                                                        </template>
                                                    </div>
                                                </div>

                                                <div x-show="step === 'day'">
                                                    <div
                                                        class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                        <button type="button" @click="step = 'month'"
                                                            class="hover:underline">&larr; Back</button>
                                                        <span>Select Day</span>
                                                        <span></span>
                                                    </div>
                                                    <div
                                                        class="grid h-64 grid-cols-5 gap-2 overflow-y-auto pr-1 text-center">
                                                        <template x-for="day in daysInMonth" :key="day">
                                                            <button type="button" @click="selectDay(day)"
                                                                :class="selectedDay === day ?
                                                                    'bg-[#0B2A5B] text-white font-bold' :
                                                                    'bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                                                class="rounded-lg py-2.5 text-sm transition"
                                                                x-text="day"></button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="flex items-center justify-between border-t bg-slate-50 px-5 py-3">
                                                <button type="button" @click="openPicker = false; step = 'year';"
                                                    class="rounded-lg px-4 py-2 text-xs font-bold text-slate-500 transition hover:bg-slate-200">Cancel</button>
                                                <button type="button" @click="confirmDate()"
                                                    class="rounded-lg bg-[#0B2A5B] px-6 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#123b7a]">OK</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- =================================================
                         3. ADDRESS & LOCATION
                    ================================================== --}}
                    <div class="space-y-4">
                        <div class="flex items-center justify-between rounded-xl bg-[#0B2A5B] px-4 py-2.5 shadow-sm">
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-[#E8B923] sm:text-sm">
                                    Address & Location
                                </h3>
                                <p class="mt-0.5 text-[10px] font-normal normal-case tracking-normal text-slate-200">
                                    Provide the applicant's complete address and location.
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="mb-1 block text-xs font-bold text-slate-700">
                                Complete Address *
                            </label>
                            <div class="flex gap-2">
                                <input wire:model.live="address" type="text"
                                    placeholder="House No., Street, Barangay, City/Municipality, Province"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                                <button type="button" wire:click="toggleMap"
                                    class="shrink-0 rounded-xl bg-[#0B2A5B] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#123b7a]">
                                    {{ $showMapModal ? 'Close Map' : 'Pin Map' }}
                                </button>
                            </div>

                            <div x-data x-show="$wire.showMapModal" x-cloak
                                class="mt-3 space-y-3 overflow-hidden rounded-xl border border-slate-300 bg-white p-3 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-bold text-[#0B2A5B]">
                                        Select Location
                                    </h4>
                                    <span class="text-[10px] text-slate-400">
                                        Search or click on map
                                    </span>
                                </div>

                                <div class="flex gap-2">
                                    <input id="leaflet-search-input" type="text"
                                        placeholder="Search location (e.g., Lafilipina)..."
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                                    <button id="leaflet-search-btn" type="button"
                                        class="shrink-0 rounded-xl bg-[#0B2A5B] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#123b7a]">
                                        Search
                                    </button>
                                </div>

                                <div class="h-[350px] overflow-hidden rounded-lg border border-slate-200">
                                    <div id="map" wire:ignore class="h-full w-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- =================================================
                         4. BENEFICIARY INFORMATION
                    ================================================== --}}
                    <div class="space-y-4">
                        <div class="flex items-center justify-between rounded-xl bg-[#0B2A5B] px-4 py-2.5 shadow-sm">
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-[#E8B923] sm:text-sm">
                                    Beneficiary Information
                                </h3>
                                <p class="mt-0.5 text-[10px] font-normal normal-case tracking-normal text-slate-200">
                                    Add the beneficiaries who may receive the applicable benefits.
                                </p>
                            </div>
                            <span class="hidden text-[10px] font-normal uppercase text-slate-200 sm:block">
                                Up to 3 Dependents
                            </span>
                        </div>

                        <div class="space-y-4">
                            @for ($i = 0; $i < 3; $i++)
                                @php
                                    $isPersisted = !empty($beneficiaryIds[$i] ?? null);
                                @endphp
                                <div wire:key="beneficiary-card-{{ $i }}"
                                    class="relative grid grid-cols-1 gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3 pt-12 sm:grid-cols-2 sm:p-4 sm:pt-12 lg:grid-cols-3">

                                    {{-- BENEFICIARY HEADER --}}
                                    <div class="absolute left-4 top-3 flex items-center gap-2">
                                        <div
                                            class="flex h-6 min-w-6 items-center justify-center rounded-full bg-[#0B2A5B] px-2 text-[10px] font-bold text-[#E8B923]">
                                            {{ $i + 1 }}
                                        </div>
                                        <span class="text-xs font-bold text-slate-600">
                                            Beneficiary {{ $i + 1 }}
                                        </span>
                                        @if ($isPersisted)
                                            <span
                                                class="rounded-full bg-emerald-100 px-2 py-0.5 text-[9px] font-bold uppercase text-emerald-700">
                                                Saved
                                            </span>
                                        @endif
                                    </div>

                                    {{-- CLEAR / DELETE BENEFICIARY --}}
                                    <button type="button" wire:click="confirmDeleteBeneficiary({{ $i }})"
                                        class="absolute right-3 top-3 flex items-center gap-1.5 rounded-lg border bg-white px-3 py-1.5 text-xs font-bold shadow-sm transition
                                            {{ $isPersisted
                                                ? 'border-rose-200 text-rose-600 hover:bg-rose-50 hover:text-rose-700'
                                                : 'border-amber-200 text-amber-600 hover:bg-amber-50 hover:text-amber-700' }}"
                                        title="{{ $isPersisted ? 'Delete Beneficiary' : 'Clear Beneficiary' }}">
                                        @if ($isPersisted)
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                            <span>Delete</span>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            <span>Clear</span>
                                        @endif
                                    </button>

                                    {{-- FULL NAME --}}
                                    <div>
                                        <label class="mb-1 block text-xs font-bold text-slate-700">
                                            Full Name
                                        </label>
                                        <input wire:model.live="beneficiaries.{{ $i }}.fullname"
                                            type="text" placeholder="Full name of beneficiary"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                                    </div>

                                    {{-- BENEFICIARY BIRTHDATE --}}
                                    <div wire:key="beneficiary-picker-{{ $i }}-{{ $userId ?? 'new' }}"
                                        x-data="{
                                            birthdate: @entangle('beneficiaries.' . $i . '.birthdate').live,
                                            openPicker: false,
                                            step: 'year',
                                            selectedYear: '2000',
                                            selectedMonth: '01',
                                            selectedDay: '01',
                                            years: Array.from({ length: 100 },
                                                (_, index) => new Date().getFullYear() - index
                                            ),
                                            months: [
                                                { name: 'January', val: '01' },
                                                { name: 'February', val: '02' },
                                                { name: 'March', val: '03' },
                                                { name: 'April', val: '04' },
                                                { name: 'May', val: '05' },
                                                { name: 'June', val: '06' },
                                                { name: 'July', val: '07' },
                                                { name: 'August', val: '08' },
                                                { name: 'September', val: '09' },
                                                { name: 'October', val: '10' },
                                                { name: 'November', val: '11' },
                                                { name: 'December', val: '12' }
                                            ],
                                            get daysInMonth() {
                                                const days = new Date(
                                                    Number(this.selectedYear),
                                                    Number(this.selectedMonth),
                                                    0
                                                ).getDate();
                                                return Array.from({ length: days },
                                                    (_, index) => String(index + 1).padStart(2, '0')
                                                );
                                            },
                                            get formattedDate() {
                                                return `${this.selectedYear}-${this.selectedMonth}-${this.selectedDay}`;
                                            },
                                            init() {
                                                if (this.birthdate && typeof this.birthdate === 'string') {
                                                    const parts = this.birthdate.split('-');
                                                    if (parts.length === 3) {
                                                        this.selectedYear = parts[0];
                                                        this.selectedMonth = parts[1];
                                                        this.selectedDay = parts[2];
                                                    }
                                                }
                                                this.$watch('birthdate', value => {
                                                    if (!value || typeof value !== 'string') {
                                                        return;
                                                    }
                                                    const parts = value.split('-');
                                                    if (parts.length === 3) {
                                                        this.selectedYear = parts[0];
                                                        this.selectedMonth = parts[1];
                                                        this.selectedDay = parts[2];
                                                    }
                                                });
                                            },
                                            selectYear(year) {
                                                this.selectedYear = String(year);
                                                this.step = 'month';
                                            },
                                            selectMonth(month) {
                                                this.selectedMonth = month;
                                                const maxDays = new Date(
                                                    Number(this.selectedYear),
                                                    Number(this.selectedMonth),
                                                    0
                                                ).getDate();
                                                if (Number(this.selectedDay) > maxDays) {
                                                    this.selectedDay = String(maxDays).padStart(2, '0');
                                                }
                                                this.step = 'day';
                                            },
                                            selectDay(day) {
                                                this.selectedDay = day;
                                            },
                                            confirmDate() {
                                                this.birthdate = `${this.selectedYear}-${this.selectedMonth}-${this.selectedDay}`;
                                                this.openPicker = false;
                                                this.step = 'year';
                                            }
                                        }" class="relative">
                                        <label class="mb-1 block text-xs font-bold text-slate-700">
                                            Birthdate
                                        </label>
                                        <button type="button" @click="openPicker = true"
                                            class="flex w-full items-center justify-between rounded-xl border border-slate-300 bg-white px-3 py-2 text-left text-sm text-slate-700 transition hover:border-[#0B2A5B] focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                                            <span x-text="birthdate || 'Select Birthdate'"
                                                :class="!birthdate ? 'text-slate-400' : 'text-slate-700'">
                                            </span>
                                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </button>

                                        <div x-show="openPicker" x-cloak x-transition.opacity
                                            class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
                                            <div @click.stop
                                                class="w-full max-w-sm overflow-hidden rounded-2xl bg-white shadow-2xl">
                                                <div class="bg-[#E8B923] p-5 text-[#0B2A5B]">
                                                    <span
                                                        class="text-[10px] font-bold uppercase tracking-widest opacity-80">
                                                        Select Beneficiary Birthdate
                                                    </span>
                                                    <h2 class="mt-1 text-xl font-extrabold" x-text="formattedDate">
                                                    </h2>
                                                    <div class="mt-3 flex gap-3 text-xs font-bold">
                                                        <button type="button" @click="step = 'year'"
                                                            :class="step === 'year' ? 'underline opacity-100' :
                                                                'opacity-60'">Year</button>
                                                        <span>/</span>
                                                        <button type="button" @click="step = 'month'"
                                                            :class="step === 'month' ? 'underline opacity-100' :
                                                                'opacity-60'">Month</button>
                                                        <span>/</span>
                                                        <button type="button" @click="step = 'day'"
                                                            :class="step === 'day' ? 'underline opacity-100' :
                                                                'opacity-60'">Day</button>
                                                    </div>
                                                </div>

                                                <div class="p-4">
                                                    <div x-show="step === 'year'">
                                                        <div
                                                            class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                            <span>Select Year</span>
                                                            <button type="button" @click="step = 'month'"
                                                                class="text-[#0B2A5B] hover:underline">Next: Month
                                                                &rarr;</button>
                                                        </div>
                                                        <div
                                                            class="grid h-64 grid-cols-3 gap-2 overflow-y-auto pr-1 text-center">
                                                            <template x-for="yr in years" :key="yr">
                                                                <button type="button" @click="selectYear(yr)"
                                                                    :class="String(selectedYear) === String(yr) ?
                                                                        'bg-[#0B2A5B] text-white font-bold' :
                                                                        'bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                                                    class="rounded-lg py-2.5 text-sm transition"
                                                                    x-text="yr"></button>
                                                            </template>
                                                        </div>
                                                    </div>

                                                    <div x-show="step === 'month'">
                                                        <div
                                                            class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                            <button type="button" @click="step = 'year'"
                                                                class="hover:underline">&larr; Back</button>
                                                            <span>Select Month</span>
                                                            <button type="button" @click="step = 'day'"
                                                                class="text-[#0B2A5B] hover:underline">Next: Day
                                                                &rarr;</button>
                                                        </div>
                                                        <div
                                                            class="grid h-64 grid-cols-2 gap-2 overflow-y-auto pr-1 text-center">
                                                            <template x-for="month in months" :key="month.val">
                                                                <button type="button" @click="selectMonth(month.val)"
                                                                    :class="selectedMonth === month.val ?
                                                                        'bg-[#0B2A5B] text-white font-bold' :
                                                                        'bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                                                    class="rounded-lg py-3 text-sm transition"
                                                                    x-text="month.name"></button>
                                                            </template>
                                                        </div>
                                                    </div>

                                                    <div x-show="step === 'day'">
                                                        <div
                                                            class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                            <button type="button" @click="step = 'month'"
                                                                class="hover:underline">&larr; Back</button>
                                                            <span>Select Day</span>
                                                            <span></span>
                                                        </div>
                                                        <div
                                                            class="grid h-64 grid-cols-5 gap-2 overflow-y-auto pr-1 text-center">
                                                            <template x-for="day in daysInMonth"
                                                                :key="day">
                                                                <button type="button" @click="selectDay(day)"
                                                                    :class="selectedDay === day ?
                                                                        'bg-[#0B2A5B] text-white font-bold' :
                                                                        'bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                                                    class="rounded-lg py-2.5 text-sm transition"
                                                                    x-text="day"></button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div
                                                    class="flex items-center justify-between border-t bg-slate-50 px-5 py-3">
                                                    <button type="button" @click="openPicker = false; step = 'year';"
                                                        class="rounded-lg px-4 py-2 text-xs font-bold text-slate-500 transition hover:bg-slate-200">Cancel</button>
                                                    <button type="button" @click="confirmDate()"
                                                        class="rounded-lg bg-[#0B2A5B] px-6 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#123b7a]">OK</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- RELATIONSHIP --}}
                                    <div>
                                        <label class="mb-1 block text-xs font-bold text-slate-700">
                                            Relationship
                                        </label>
                                        <input wire:model.live="beneficiaries.{{ $i }}.relationship"
                                            type="text" placeholder="Father, Mother, Spouse..."
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    {{-- =================================================
                         5. ACCOUNT & SECURITY INFORMATION
                    ================================================== --}}
                    <div class="space-y-4">
                        <div class="flex items-center justify-between rounded-xl bg-[#0B2A5B] px-4 py-2.5 shadow-sm">
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-[#E8B923] sm:text-sm">
                                    Account & Security Information
                                </h3>
                                <p class="mt-0.5 text-[10px] font-normal normal-case tracking-normal text-slate-200">
                                    Account credentials and contact information.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 pt-1 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Mobile Number *
                                </label>
                                <input wire:model.live="mobile" type="tel" placeholder="0917XXXXXXX"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Email Address
                                    <span class="font-normal text-slate-400">(Optional)</span>
                                </label>
                                <input wire:model="email" type="email" placeholder="juan@example.com"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Username *
                                </label>
                                <input wire:model="username" type="text" placeholder="Juan123"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                            </div>

                            @if (!$isEditMode)
                                <div x-data="{ show: false }">
                                    <label class="mb-1 block text-xs font-bold text-slate-700">
                                        Password *
                                    </label>
                                    <div class="relative">
                                        <input wire:model="password" :type="show ? 'text' : 'password'"
                                            placeholder="********"
                                            class="w-full rounded-xl border border-slate-300 px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                                        <button type="button" @click="show = !show"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-none">
                                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div x-data="{ show: false }">
                                    <label class="mb-1 block text-xs font-bold text-slate-700">
                                        Confirm Password *
                                    </label>
                                    <div class="relative">
                                        <input wire:model="password_confirmation" :type="show ? 'text' : 'password'"
                                            placeholder="********"
                                            class="w-full rounded-xl border border-slate-300 px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                                        <button type="button" @click="show = !show"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-none">
                                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 00-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- =================================================
                         6. ACCEPTANCE OF TERMS
                    ================================================== --}}
                    @if (!$isEditMode)
                        <div class="space-y-3">
                            <div class="rounded-xl bg-[#0B2A5B] px-4 py-2.5 shadow-sm">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-[#E8B923] sm:text-sm">
                                    Acceptance of Terms
                                </h3>
                            </div>

                            <div
                                class="max-h-32 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs leading-relaxed text-slate-600 sm:text-sm">
                                <p class="mb-1 font-semibold text-slate-800">
                                    Membership Terms & Policy Summary:
                                </p>
                                I acknowledge that the information provided is true, accurate, and correct to the best
                                of my
                                knowledge. I agree to the standard micro-insurance coverage guidelines, claim
                                procedures,
                                and privacy policies established by Dayongan and partner underwriters.
                            </div>

                            <label class="mt-3 flex cursor-pointer select-none items-start gap-3 sm:items-center">
                                <input type="checkbox" wire:model.live="agree"
                                    class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#0B2A5B] focus:ring-[#0B2A5B] sm:mt-0">
                                <span class="text-xs font-semibold text-slate-700 sm:text-sm">
                                    I have read and agree to the terms and conditions.
                                </span>
                            </label>
                        </div>
                    @endif
                </div>

                {{-- =====================================================
                     FOOTER ACTIONS
                ====================================================== --}}
                <div
                    class="flex flex-col items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 p-4 sm:flex-row">
                    <span class="text-center text-xs text-slate-500 sm:text-left">
                        * Please verify all details before submitting.
                    </span>

                    <div class="flex w-full items-center gap-3 sm:w-auto">
                        <button wire:click="close" type="button"
                            class="w-1/2 rounded-xl border border-slate-300 px-5 py-2.5 text-center text-xs font-bold text-slate-600 transition hover:bg-slate-100 sm:w-auto">
                            Cancel
                        </button>

                        <button wire:click="{{ $isEditMode ? 'confirmUpdate' : 'submit' }}"
                            wire:loading.attr="disabled" type="button" @disabled(!$agree && !$isEditMode)
                            class="flex w-1/2 items-center justify-center rounded-xl bg-[#D4A017] px-6 py-2.5 text-xs font-bold text-[#0B2A5B] shadow-md transition hover:bg-[#E8B923] disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto">
                            <span wire:loading.remove wire:target="{{ $isEditMode ? 'confirmUpdate' : 'submit' }}">
                                {{ $isEditMode ? 'Update Member' : 'Submit Application' }}
                            </span>
                            <div wire:loading.flex wire:target="{{ $isEditMode ? 'confirmUpdate' : 'submit' }}"
                                class="items-center justify-center gap-2">
                                <svg class="h-4 w-4 shrink-0 animate-spin text-[#0B2A5B]"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <span class="whitespace-nowrap">
                                    {{ $isEditMode ? 'Updating...' : 'Submitting...' }}
                                </span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    @endif

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script>
        let map = null;
        let marker = null;

        document.addEventListener('livewire:initialized', () => {

            /* =========================================================
               DESTROY MAP
            ========================================================== */
            Livewire.on('destroy-map', () => {
                if (map) {
                    map.remove();
                    map = null;
                    marker = null;
                }
            });

            /* =========================================================
               MAP OPENED
            ========================================================== */
            Livewire.on('map-opened', (data) => {
                setTimeout(() => {
                    const defaultLat = 7.442294461028405;
                    const defaultLng = 125.81370503321095;

                    const lat = data.latitude ?? defaultLat;
                    const lng = data.longitude ?? defaultLng;

                    if (map) {
                        map.remove();
                        map = null;
                        marker = null;
                    }

                    map = L.map('map').setView([lat, lng], 15);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(map);

                    if (data.latitude !== null && data.longitude !== null) {
                        marker = L.marker([lat, lng]).addTo(map);
                        map.setView([lat, lng], 16);
                    }

                    const searchInput = document.getElementById('leaflet-search-input');
                    const searchBtn = document.getElementById('leaflet-search-btn');

                    function performSearch() {
                        if (!searchInput) return;
                        const query = searchInput.value.trim();
                        if (!query) return;

                        fetch(
                                `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(results => {
                                if (results && results.length > 0) {
                                    const r = results[0];
                                    const center = [parseFloat(r.lat), parseFloat(r.lon)];

                                    map.setView(center, 16);

                                    if (marker) {
                                        marker.setLatLng(center);
                                    } else {
                                        marker = L.marker(center).addTo(map);
                                    }

                                    Livewire.dispatch('set-coordinates', {
                                        lat: r.lat,
                                        lng: r.lon
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Location Not Found',
                                        text: 'Please try another location.',
                                        confirmButtonColor: '#0B2A5B'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Geocoding error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Search Error',
                                    text: 'Unable to search for this location. Please try again.',
                                    confirmButtonColor: '#0B2A5B'
                                });
                            });
                    }

                    if (searchInput) {
                        searchInput.addEventListener('keypress', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                performSearch();
                            }
                        });
                    }

                    if (searchBtn) {
                        searchBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            performSearch();
                        });
                    }

                    map.on('click', function(e) {
                        if (marker) {
                            marker.setLatLng(e.latlng);
                        } else {
                            marker = L.marker(e.latlng).addTo(map);
                        }

                        Livewire.dispatch('set-coordinates', {
                            lat: e.latlng.lat,
                            lng: e.latlng.lng
                        });
                    });

                    setTimeout(() => {
                        map.invalidateSize();
                    }, 100);
                }, 300);
            });

            /* =========================================================
               CONFIRM CLEAR / DELETE BENEFICIARY
            ========================================================== */
            Livewire.on('confirm-delete-beneficiary', (data) => {
                const beneficiaryNumber = Number(data.index ?? 1);
                const isPersisted = !!data.persisted;

                if (!isPersisted) {
                    Swal.fire({
                        icon: 'warning',
                        title: `Clear Beneficiary ${beneficiaryNumber}?`,
                        text: `This will clear the entered details for Beneficiary ${beneficiaryNumber}. This beneficiary has not been saved yet.`,
                        showCancelButton: true,
                        confirmButtonText: 'Clear',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#0B2A5B',
                        cancelButtonColor: '#D79B10',
                        reverseButtons: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Livewire.dispatch('delete-beneficiary', {
                                index: beneficiaryNumber - 1
                            });
                        }
                    });
                    return;
                }

                Swal.fire({
                    icon: 'warning',
                    title: `Delete Beneficiary ${beneficiaryNumber}?`,
                    html: `
                        <div style="text-align:center;color:#475569;font-size:14px;line-height:1.6;">
                            <p style="margin-bottom:12px;">
                                This beneficiary is already saved. Deleting will
                                <b style="color:#dc2626;">permanently remove</b>
                                <b style="color:#0B2A5B;">Beneficiary ${beneficiaryNumber}</b>
                                from the records right away.
                            </p>
                            <p style="font-size:13px;margin-bottom:8px;color:#475569;">
                                To confirm, type <b style="color:#dc2626;letter-spacing:1px;">REMOVE</b> below:
                            </p>
                        </div>
                    `,
                    input: 'text',
                    inputPlaceholder: 'Type REMOVE here',
                    inputAttributes: {
                        autocapitalize: 'characters',
                        autocomplete: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Delete Beneficiary',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#D79B10',
                    reverseButtons: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    preConfirm: (value) => {
                        if (value !== 'REMOVE') {
                            Swal.showValidationMessage(
                            'Please type REMOVE exactly to confirm.');
                            return false;
                        }
                        return true;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('delete-beneficiary', {
                            index: beneficiaryNumber - 1
                        });
                    }
                });
            });

            /* =========================================================
               BENEFICIARY REMOVED SUCCESS
            ========================================================== */
            Livewire.on('beneficiary-deleted', (data) => {
                const wasPersisted = !!data.persisted;

                Swal.fire({
                    icon: 'success',
                    title: wasPersisted ? 'Beneficiary Deleted' : 'Beneficiary Cleared',
                    text: wasPersisted ?
                        `Beneficiary ${data.index} has been permanently removed.` :
                        `Beneficiary ${data.index} fields have been cleared.`,
                    confirmButtonColor: '#0B2A5B',
                    timer: 1800,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            });

            /* =========================================================
               VALIDATION ERROR
            ========================================================== */
            Livewire.on('validation-error', (data) => {
                Swal.fire({
                    icon: 'error',
                    title: data.title || 'Error',
                    html: data.html || data.text || 'Please check the required fields.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0B2A5B',
                    allowOutsideClick: false
                });
            });

            /* =========================================================
               CONFIRM UPDATE — show a diff of what changed
            ========================================================== */
            Livewire.on('swal-confirm-update', (data) => {
                const changes = (data && data.changes) ? data.changes : [];

                let rowsHtml = '';
                if (changes.length > 0) {
                    changes.forEach(c => {
                        rowsHtml += `
                            <tr>
                                <td style="padding:6px 10px;font-weight:600;color:#0B2A5B;border-bottom:1px solid #e2e8f0;">${c.field}</td>
                                <td style="padding:6px 10px;color:#dc2626;text-decoration:line-through;border-bottom:1px solid #e2e8f0;">${c.old}</td>
                                <td style="padding:6px 10px;color:#16a34a;font-weight:600;border-bottom:1px solid #e2e8f0;">${c.new}</td>
                            </tr>
                        `;
                    });
                } else {
                    rowsHtml =
                        `<tr><td colspan="3" style="padding:12px;text-align:center;color:#94a3b8;">No changes detected.</td></tr>`;
                }

                Swal.fire({
                    icon: 'question',
                    title: 'Confirm Update',
                    width: 560,
                    html: `
                        <div style="text-align:left;">
                            <p style="margin-bottom:10px;color:#475569;font-size:13px;">
                                Please review the changes below before saving:
                            </p>
                            <div style="max-height:280px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:8px;">
                                <table style="width:100%;border-collapse:collapse;font-size:12px;">
                                    <thead>
                                        <tr style="background:#f1f5f9;">
                                            <th style="padding:6px 10px;text-align:left;">Field</th>
                                            <th style="padding:6px 10px;text-align:left;">Before</th>
                                            <th style="padding:6px 10px;text-align:left;">After</th>
                                        </tr>
                                    </thead>
                                    <tbody>${rowsHtml}</tbody>
                                </table>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Save Changes',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#0B2A5B',
                    cancelButtonColor: '#D79B10',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('confirmed-update-member');
                    }
                });
            });

            /* =========================================================
               GENERIC TOAST
            ========================================================== */
            Livewire.on('show-toast', (data) => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: data.icon || 'success',
                    title: data.title || 'Success',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                });
            });
        });
    </script>
</div>
