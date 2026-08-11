<div>
    @if ($open)
        {{-- ================================================= --}}
        {{-- MODAL BACKDROP --}}
        {{-- ================================================= --}}
        <div
            class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto bg-black/70 p-3 backdrop-blur-sm sm:p-4">

            {{-- ================================================= --}}
            {{-- MODAL CONTAINER --}}
            {{-- ================================================= --}}
            <div
                class="my-auto flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-2xl">

                {{-- ================================================= --}}
                {{-- HEADER --}}
                {{-- ================================================= --}}

                <div
                    class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-5 py-4 shadow-sm">
                    <div>
                        <h2 class="text-lg font-extrabold text-[#0B2A5B] sm:text-xl">
                            Membership Application
                        </h2>
                        <p class="text-xs text-slate-500">
                            True Life Solidaritas • Vitae Auxilium
                        </p>
                    </div>

                    <button wire:click="close" type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 font-bold text-slate-500 transition hover:bg-rose-100 hover:text-rose-600"
                        title="Close">
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
                        <h3
                            class="rounded-xl bg-[#0B2A5B] px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-[#E8B923] shadow-sm sm:text-sm">
                            Applicant Information
                        </h3>

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
                                    Middle Name (Optional)
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

                            {{-- POLICY NUMBER (Edit Mode Only) --}}
                            @if ($isEditMode)
                                <div>
                                    <label class="mb-1 block text-xs font-bold text-slate-700">
                                        Policy Number
                                    </label>
                                    <input wire:model="policy_number" type="text" placeholder="POL-000123"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                                </div>
                            @endif

                            {{-- BIRTHDATE WITH FULL YEAR, MONTH, & DAY PICKER --}}
                            <div wire:key="birthdate-picker-{{ $userId ?? 'new' }}" x-data="{
                                openPicker: false,
                                step: 'year',
                                selectedYear: '2000',
                                selectedMonth: '01',
                                selectedDay: '01',
                                years: Array.from({ length: 80 }, (_, i) => 2026 - i),
                                months: [
                                    { name: 'January', val: '01' }, { name: 'February', val: '02' }, { name: 'March', val: '03' },
                                    { name: 'April', val: '04' }, { name: 'May', val: '05' }, { name: 'June', val: '06' },
                                    { name: 'July', val: '07' }, { name: 'August', val: '08' }, { name: 'September', val: '09' },
                                    { name: 'October', val: '10' }, { name: 'November', val: '11' }, { name: 'December', val: '12' }
                                ],
                                get daysInMonth() {
                                    return Array.from({ length: new Date(this.selectedYear, this.selectedMonth, 0).getDate() }, (_, i) => String(i + 1).padStart(2, '0'));
                                },
                                get formattedDate() {
                                    return `${this.selectedYear}-${this.selectedMonth}-${this.selectedDay}`;
                                },
                                init() {
                                    this.$watch('$wire.birthdate', (value) => {
                                        if (!value) return;

                                        let parts = value.split('-');

                                        if (parts.length === 3) {
                                            this.selectedYear = parts[0];
                                            this.selectedMonth = parts[1];
                                            this.selectedDay = parts[2];
                                        }
                                    });

                                    if (this.$wire.birthdate) {
                                        let parts = this.$wire.birthdate.split('-');

                                        this.selectedYear = parts[0];
                                        this.selectedMonth = parts[1];
                                        this.selectedDay = parts[2];
                                    }
                                },
                                confirmDate() {
                                    let formatted = `${this.selectedYear}-${this.selectedMonth}-${this.selectedDay}`;
                                    @this.set('birthdate', formatted);
                                    this.openPicker = false;
                                    this.step = 'year';
                                }
                            }"
                                class="relative">
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Birthdate *
                                </label>

                                {{-- Trigger Input Box --}}
                                <div @click="openPicker = true"
                                    class="flex w-full cursor-pointer items-center justify-between rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                                    <span x-text="$wire.birthdate || 'Select Date of Birth'"></span>
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>

                                {{-- CUSTOM MATERIAL-STYLE POPUP MODAL --}}
                                <div x-show="openPicker" x-cloak
                                    class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
                                    <div @click.away="openPicker = false"
                                        class="w-full max-w-sm overflow-hidden rounded-2xl bg-white shadow-2xl">

                                        {{-- Header Banner --}}
                                        <div class="bg-[#E8B923] p-5 text-[#0B2A5B]">
                                            <span
                                                class="text-[10px] font-bold uppercase tracking-widest opacity-80">Select
                                                Date of Birth</span>
                                            <div class="mt-1 flex items-center justify-between">
                                                <h2 class="text-xl font-extrabold" x-text="formattedDate"></h2>
                                            </div>
                                            <div class="mt-2 flex gap-2 text-xs font-bold">
                                                <button type="button" @click="step = 'year'"
                                                    :class="step == 'year' ? 'underline opacity-100' : 'opacity-70'"
                                                    class="hover:opacity-100">Year</button>
                                                <span>/</span>
                                                <button type="button" @click="step = 'month'"
                                                    :class="step == 'month' ? 'underline opacity-100' : 'opacity-70'"
                                                    class="hover:opacity-100">Month</button>
                                                <span>/</span>
                                                <button type="button" @click="step = 'day'"
                                                    :class="step == 'day' ? 'underline opacity-100' : 'opacity-70'"
                                                    class="hover:opacity-100">Day</button>
                                            </div>
                                        </div>

                                        {{-- Body Content --}}
                                        <div class="p-4">
                                            {{-- Year Grid --}}
                                            <div x-show="step == 'year'">
                                                <div
                                                    class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                    <span>Select Year</span>
                                                    <button type="button" @click="step = 'month'"
                                                        class="text-xs text-[#0B2A5B] hover:underline">Next: Month
                                                        &rarr;</button>
                                                </div>
                                                <div
                                                    class="grid h-64 grid-cols-3 gap-2 overflow-y-auto pr-1 text-center">
                                                    <template x-for="yr in years" :key="yr">
                                                        <button type="button"
                                                            @click="selectedYear = yr; step = 'month'"
                                                            :class="selectedYear == yr ? 'bg-[#0B2A5B] text-white font-bold' :
                                                                'bg-slate-50 hover:bg-slate-100 text-slate-700'"
                                                            class="rounded-lg py-2.5 text-sm transition"
                                                            x-text="yr"></button>
                                                    </template>
                                                </div>
                                            </div>

                                            {{-- Month Grid --}}
                                            <div x-show="step == 'month'">
                                                <div
                                                    class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                    <button type="button" @click="step = 'year'"
                                                        class="text-xs text-slate-500 hover:underline">&larr;
                                                        Back</button>
                                                    <span>Select Month</span>
                                                    <button type="button" @click="step = 'day'"
                                                        class="text-xs text-[#0B2A5B] hover:underline">Next: Day
                                                        &rarr;</button>
                                                </div>
                                                <div
                                                    class="grid h-64 grid-cols-2 gap-2 overflow-y-auto pr-1 text-center">
                                                    <template x-for="m in months" :key="m.val">
                                                        <button type="button"
                                                            @click="selectedMonth = m.val; step = 'day'"
                                                            :class="selectedMonth == m.val ?
                                                                'bg-[#0B2A5B] text-white font-bold' :
                                                                'bg-slate-50 hover:bg-slate-100 text-slate-700'"
                                                            class="rounded-lg py-3 text-sm transition"
                                                            x-text="m.name"></button>
                                                    </template>
                                                </div>
                                            </div>

                                            {{-- Day Grid --}}
                                            <div x-show="step == 'day'">
                                                <div
                                                    class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                    <button type="button" @click="step = 'month'"
                                                        class="text-xs text-slate-500 hover:underline">&larr;
                                                        Back</button>
                                                    <span>Select Day</span>
                                                    <span></span>
                                                </div>
                                                <div
                                                    class="grid h-64 grid-cols-5 gap-2 overflow-y-auto pr-1 text-center">
                                                    <template x-for="d in daysInMonth" :key="d">
                                                        <button type="button" @click="selectedDay = d"
                                                            :class="selectedDay == d ? 'bg-[#0B2A5B] text-white font-bold' :
                                                                'bg-slate-50 hover:bg-slate-100 text-slate-700'"
                                                            class="rounded-lg py-2.5 text-sm transition"
                                                            x-text="d"></button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Footer Actions --}}
                                        <div class="flex items-center justify-between border-t bg-slate-50 px-5 py-3">
                                            <button type="button" @click="openPicker = false"
                                                class="rounded-lg px-4 py-2 text-xs font-bold text-slate-500 transition hover:bg-slate-200">
                                                Cancel
                                            </button>
                                            <button type="button" @click="confirmDate()"
                                                class="rounded-lg bg-[#0B2A5B] px-6 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#123b7a]">
                                                OK
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- COMPLETE ADDRESS --}}
                            <div class="sm:col-span-2 lg:col-span-3 space-y-2">
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Complete Address *
                                </label>
                                <div class="flex gap-2">
                                    <input wire:model.live="address" type="text"
                                        placeholder="House No., Street, Barangay, City/Municipality, Province"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">

                                    {{-- MAP PIN BUTTON --}}
                                    <button type="button" wire:click="toggleMap"
                                        class="flex items-center gap-1.5 shrink-0 px-4 py-2 bg-[#0B2A5B] hover:bg-[#123b7a] text-white text-xs font-bold rounded-xl shadow-sm transition">
                                        {{ $showMapModal ? 'Close Map' : 'Pin Map' }}
                                    </button>
                                </div>

                                <div x-data x-show="$wire.showMapModal"
                                    class="mt-3 overflow-hidden rounded-xl border border-slate-300 bg-white p-3 shadow-sm space-y-3">

                                    <h4 class="text-sm font-bold text-[#0B2A5B]">
                                        Select Location (Search or Click on Map)
                                    </h4>

                                    {{-- Search Input and Button Container --}}
                                    <div class="flex gap-2">
                                        <input id="leaflet-search-input" type="text"
                                            placeholder="Search location (e.g., Lafilipina)..."
                                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                                        <button id="leaflet-search-btn" type="button"
                                            class="shrink-0 px-4 py-2 bg-[#0B2A5B] hover:bg-[#123b7a] text-white text-xs font-bold rounded-xl shadow-sm transition">
                                            Search
                                        </button>
                                    </div>

                                    <div class="h-[350px] rounded-lg border">
                                        <div id="map" wire:ignore class="h-full w-full"></div>
                                    </div>

                                </div>
                            </div>




                        </div>
                    </div>

                    {{-- ================================================= --}}
                    {{-- BENEFICIARY INFORMATION --}}
                    {{-- ================================================= --}}
                    <div class="space-y-4">
                        <h3
                            class="flex items-center justify-between rounded-xl bg-[#0B2A5B] px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-[#E8B923] shadow-sm sm:text-sm">
                            <span>Beneficiary Information</span>
                            <span class="text-[10px] font-normal uppercase text-slate-200">
                                Up to 3 Dependents
                            </span>
                        </h3>

                        <div class="space-y-4">
                            @for ($i = 0; $i < 3; $i++)
                                <div
                                    class="grid grid-cols-1 gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3 sm:grid-cols-2 sm:p-4 lg:grid-cols-3">
                                    {{-- FULL NAME --}}
                                    <div>
                                        <label class="mb-1 block text-xs font-bold text-slate-700">
                                            Beneficiary {{ $i + 1 }} Full Name
                                        </label>
                                        <input wire:model.live="beneficiaries.{{ $i }}.fullname"
                                            type="text" placeholder="Full name of beneficiary"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                                    </div>

                                    {{-- BENEFICIARY BIRTHDATE WITH CUSTOM YEAR-GRID PICKER --}}
                                    {{-- BENEFICIARY BIRTHDATE WITH FULL YEAR, MONTH, & DAY PICKER --}}
                                    <div wire:key="beneficiary-picker-{{ $i }}-{{ $userId ?? 'new' }}"
                                        x-data="{
                                            birthdate: @entangle('beneficiaries.' . $i . '.birthdate'),

                                            openPicker: false,
                                            step: 'year',

                                            selectedYear: '2000',
                                            selectedMonth: '01',
                                            selectedDay: '01',

                                            years: Array.from({ length: 90 }, (_, i) => 2026 - i),

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
                                                            this.selectedYear,
                                                            this.selectedMonth,
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
                                                this.$watch('birthdate', (value) => {
                                                    if (typeof value !== 'string') return;

                                                    let parts = value.split('-');

                                                    if (parts.length === 3) {
                                                        this.selectedYear = parts[0];
                                                        this.selectedMonth = parts[1];
                                                        this.selectedDay = parts[2];
                                                    }
                                                });

                                                if (typeof this.birthdate === 'string') {
                                                    let parts = this.birthdate.split('-');

                                                    if (parts.length === 3) {
                                                        this.selectedYear = parts[0];
                                                        this.selectedMonth = parts[1];
                                                        this.selectedDay = parts[2];
                                                    }
                                                }
                                            },

                                            confirmDate() {
                                                this.birthdate =
                                                    `${this.selectedYear}-${this.selectedMonth}-${this.selectedDay}`;

                                                this.openPicker = false;
                                                this.step = 'year';
                                            }
                                        }" class="relative">
                                        <label class="mb-1 block text-xs font-bold text-slate-700">
                                            Birthdate
                                        </label>

                                        {{-- Trigger Input Box --}}
                                        <div @click="openPicker = true"
                                            class="flex w-full cursor-pointer items-center justify-between rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                                            <span x-text="birthdate || 'Select Birthdate'"></span>
                                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>

                                        {{-- CUSTOM MATERIAL-STYLE POPUP MODAL --}}
                                        <div x-show="openPicker" x-cloak
                                            class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
                                            <div @click.away="openPicker = false"
                                                class="w-full max-w-sm overflow-hidden rounded-2xl bg-white shadow-2xl">

                                                {{-- Header Banner --}}
                                                <div class="bg-[#E8B923] p-5 text-[#0B2A5B]">
                                                    <span
                                                        class="text-[10px] font-bold uppercase tracking-widest opacity-80">Select
                                                        Beneficiary Birthdate</span>
                                                    <div class="mt-1 flex items-center justify-between">
                                                        <h2 class="text-xl font-extrabold" x-text="formattedDate">
                                                        </h2>
                                                    </div>
                                                    <div class="mt-2 flex gap-2 text-xs font-bold">
                                                        <button type="button" @click="step = 'year'"
                                                            :class="step == 'year' ? 'underline opacity-100' : 'opacity-70'"
                                                            class="hover:opacity-100">Year</button>
                                                        <span>/</span>
                                                        <button type="button" @click="step = 'month'"
                                                            :class="step == 'month' ? 'underline opacity-100' : 'opacity-70'"
                                                            class="hover:opacity-100">Month</button>
                                                        <span>/</span>
                                                        <button type="button" @click="step = 'day'"
                                                            :class="step == 'day' ? 'underline opacity-100' : 'opacity-70'"
                                                            class="hover:opacity-100">Day</button>
                                                    </div>
                                                </div>

                                                {{-- Body Content --}}
                                                <div class="p-4">
                                                    {{-- Year Grid --}}
                                                    <div x-show="step == 'year'">
                                                        <div
                                                            class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                            <span>Select Year</span>
                                                            <button type="button" @click="step = 'month'"
                                                                class="text-xs text-[#0B2A5B] hover:underline">Next:
                                                                Month &rarr;</button>
                                                        </div>
                                                        <div
                                                            class="grid h-64 grid-cols-3 gap-2 overflow-y-auto pr-1 text-center">
                                                            <template x-for="yr in years" :key="yr">
                                                                <button type="button"
                                                                    @click="selectedYear = yr; step = 'month'"
                                                                    :class="selectedYear == yr ?
                                                                        'bg-[#0B2A5B] text-white font-bold' :
                                                                        'bg-slate-50 hover:bg-slate-100 text-slate-700'"
                                                                    class="rounded-lg py-2.5 text-sm transition"
                                                                    x-text="yr"></button>
                                                            </template>
                                                        </div>
                                                    </div>

                                                    {{-- Month Grid --}}
                                                    <div x-show="step == 'month'">
                                                        <div
                                                            class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                            <button type="button" @click="step = 'year'"
                                                                class="text-xs text-slate-500 hover:underline">&larr;
                                                                Back</button>
                                                            <span>Select Month</span>
                                                            <button type="button" @click="step = 'day'"
                                                                class="text-xs text-[#0B2A5B] hover:underline">Next:
                                                                Day &rarr;</button>
                                                        </div>
                                                        <div
                                                            class="grid h-64 grid-cols-2 gap-2 overflow-y-auto pr-1 text-center">
                                                            <template x-for="m in months" :key="m.val">
                                                                <button type="button"
                                                                    @click="selectedMonth = m.val; step = 'day'"
                                                                    :class="selectedMonth == m.val ?
                                                                        'bg-[#0B2A5B] text-white font-bold' :
                                                                        'bg-slate-50 hover:bg-slate-100 text-slate-700'"
                                                                    class="rounded-lg py-3 text-sm transition"
                                                                    x-text="m.name"></button>
                                                            </template>
                                                        </div>
                                                    </div>

                                                    {{-- Day Grid --}}
                                                    <div x-show="step == 'day'">
                                                        <div
                                                            class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                            <button type="button" @click="step = 'month'"
                                                                class="text-xs text-slate-500 hover:underline">&larr;
                                                                Back</button>
                                                            <span>Select Day</span>
                                                            <span></span>
                                                        </div>
                                                        <div
                                                            class="grid h-64 grid-cols-5 gap-2 overflow-y-auto pr-1 text-center">
                                                            <template x-for="d in daysInMonth" :key="d">
                                                                <button type="button" @click="selectedDay = d"
                                                                    :class="selectedDay == d ?
                                                                        'bg-[#0B2A5B] text-white font-bold' :
                                                                        'bg-slate-50 hover:bg-slate-100 text-slate-700'"
                                                                    class="rounded-lg py-2.5 text-sm transition"
                                                                    x-text="d"></button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Footer Actions --}}
                                                <div
                                                    class="flex items-center justify-between border-t bg-slate-50 px-5 py-3">
                                                    <button type="button" @click="openPicker = false"
                                                        class="rounded-lg px-4 py-2 text-xs font-bold text-slate-500 transition hover:bg-slate-200">
                                                        Cancel
                                                    </button>
                                                    <button type="button" @click="confirmDate()"
                                                        class="rounded-lg bg-[#0B2A5B] px-6 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#123b7a]">
                                                        OK
                                                    </button>
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

                    {{-- ================================================= --}}
                    {{-- SECURITY INFORMATION --}}
                    {{-- ================================================= --}}
                    <div class="space-y-4">
                        <h3
                            class="rounded-xl bg-[#0B2A5B] px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-[#E8B923] shadow-sm sm:text-sm">
                            Security Information
                        </h3>

                        <div class="grid grid-cols-1 gap-4 pt-1 sm:grid-cols-2 lg:grid-cols-3">
                            {{-- MOBILE NUMBER --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Mobile Number *
                                </label>
                                <input wire:model.live="mobile" type="tel" placeholder="0917XXXXXXX"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                            </div>

                            {{-- EMAIL --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Email Address (Optional)
                                </label>
                                <input wire:model="email" type="email" placeholder="juan@example.com"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                            </div>

                            {{-- SPACER FOR DESKTOP GRID --}}
                            <div class="hidden lg:block"></div>

                            {{-- USERNAME --}}
                            <div class="lg:col-start-1">
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Username *
                                </label>
                                <input wire:model="username" type="text" placeholder="Juan123"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
                            </div>

                       {{-- PASSWORD --}}
<div x-data="{ show: false }">
    <label class="mb-1 block text-xs font-bold text-slate-700">
        Password *
    </label>
    <div class="relative">
        <input wire:model="password" :type="show ? 'text' : 'password'" placeholder="********"
            class="w-full rounded-xl border border-slate-300 px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
        <button
            type="button"
            @click="show = !show"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
            <!-- Eye Icon (Show) -->
            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 cursor-pointer">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <!-- Eye Slash Icon (Hide) -->
            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 cursor-pointer" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
            </svg>
        </button>
    </div>
</div>

{{-- CONFIRM PASSWORD --}}
<div x-data="{ show: false }">
    <label class="mb-1 block text-xs font-bold text-slate-700">
        Confirm Password *
    </label>
    <div class="relative">
        <input wire:model="password_confirmation" :type="show ? 'text' : 'password'" placeholder="********"
            class="w-full rounded-xl border border-slate-300 px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B2A5B]">
        <button
            type="button"
            @click="show = !show"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
            <!-- Eye Icon (Show) -->
            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 cursor-pointer">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <!-- Eye Slash Icon (Hide) -->
            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 cursor-pointer" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
            </svg>
        </button>
    </div>
</div>
                        </div>
                    </div>


                    {{-- ================================================= --}}
                    {{-- ACCEPTANCE OF TERMS --}}
                    {{-- ================================================= --}}
                    <div class="space-y-3">
                        <h3
                            class="rounded-xl bg-[#0B2A5B] px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-[#E8B923] shadow-sm sm:text-sm">
                            Acceptance of Terms
                        </h3>

                        <div
                            class="max-h-32 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs leading-relaxed text-slate-600 sm:text-sm">
                            <p class="mb-1 font-semibold text-slate-800">
                                Membership Terms & Policy Summary:
                            </p>
                            I acknowledge that the information provided is true, accurate, and correct to the best of my
                            knowledge. I agree to the standard micro-insurance coverage guidelines, claim procedures,
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



                </div>

                {{-- ================================================= --}}
                {{-- FOOTER ACTIONS --}}
                {{-- ================================================= --}}
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
                                    ...
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

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Leaflet Control Geocoder CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Leaflet Control Geocoder JS -->
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script>
    let map = null;
let marker = null;

document.addEventListener('livewire:initialized', () => {

    Livewire.on('destroy-map', () => {
        if (map) {
            map.remove();
            map = null;
            marker = null;
        }
    });

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

            // Initialize Map
            map = L.map('map').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            // Add marker if coordinates exist
            if (data.latitude !== null && data.longitude !== null) {
                marker = L.marker([lat, lng]).addTo(map);
                map.setView([lat, lng], 16);
            }

            // Search input and button elements
            const searchInput = document.getElementById('leaflet-search-input');
            const searchBtn = document.getElementById('leaflet-search-btn');

            function performSearch() {
                if (!searchInput) return;
                const query = searchInput.value.trim();
                if (!query) return;

                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
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

                            // Dispatches ONLY coordinates so the address text input remains untouched
                            Livewire.dispatch('set-coordinates', {
                                lat: r.lat,
                                lng: r.lon
                            });
                        } else {
                            alert('Location not found.');
                        }
                    })
                    .catch(error => console.error('Geocoding error:', error));
            }

            if (searchInput) {
                // Trigger via Enter key
                searchInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        performSearch();
                    }
                });
            }

            if (searchBtn) {
                // Trigger via Button click
                searchBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    performSearch();
                });
            }

            // Map Click Event (Moves pin and updates coordinates only)
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

});
        document.addEventListener('livewire:init', () => {

            // VALIDATION ERROR ALERT
            Livewire.on('validation-error', (event) => {
                // Extract the payload correctly from Livewire 3 event structure
                const data = event.detail ? event.detail : (Array.isArray(event) ? event[0] : event);

                Swal.fire({
                    icon: 'error',
                    title: data.title || 'Error',
                    html: data.html || data.text || 'Please check the required fields.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0B2A5B',
                    allowOutsideClick: false
                });
            });

        });
    </script>
</div>
