<div>
    @if ($open)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto bg-black/70 p-3 backdrop-blur-sm sm:p-4">
            <div class="my-auto flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-2xl">

                {{-- HEADER --}}
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-5 py-4 shadow-sm">
                    <div>
                        <h2 class="text-lg font-extrabold text-[var(--color-primary)] sm:text-xl">Edit Satellite Member</h2>
                        <p class="text-xs text-slate-500">Health &middot; Wellness &middot; Protection</p>
                    </div>

                    <button wire:click="close" type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 font-bold text-slate-500 transition hover:bg-rose-100 hover:text-rose-600"
                        title="Close">
                        &#10005;
                    </button>
                </div>

                {{-- FORM BODY --}}
                <div class="space-y-7 overflow-y-auto p-4 sm:p-6">

                    {{-- PERSONAL INFORMATION --}}
                    <div class="space-y-4">
                        <div class="rounded-xl bg-[var(--color-primary)] px-4 py-2.5 shadow-sm">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-white sm:text-sm">
                                Member Personal Information
                            </h3>
                            <p class="mt-0.5 text-[10px] text-slate-200">
                                Update the member's personal details.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 pt-1 sm:grid-cols-2 lg:grid-cols-3">

                            {{-- SURNAME --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">Surname *</label>
                                <input wire:model.live="surname" type="text" placeholder="Dela Cruz"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]">
                            </div>

                            {{-- FIRST NAME --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">First Name *</label>
                                <input wire:model.live="first_name" type="text" placeholder="Juan"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]">
                            </div>

                            {{-- MIDDLE NAME --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Middle Name <span class="font-normal text-slate-500">(Optional)</span>
                                </label>
                                <input wire:model.live="middle_name" type="text" placeholder="Santos"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]">
                            </div>

                            {{-- GENDER --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">Gender *</label>
                                <select wire:model="gender"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            {{-- CIVIL STATUS --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">Civil Status *</label>
                                <select wire:model="civil_status"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]">
                                    <option value="">Select Status</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Separated">Separated</option>
                                </select>
                            </div>

                            {{-- BIRTHDATE --}}
                            <div wire:key="birthdate-picker-{{ $userId }}"
                                x-data="{
                                    openPicker: false,
                                    step: 'year',
                                    selectedYear: '2000',
                                    selectedMonth: '01',
                                    selectedDay: '01',
                                    years: Array.from({ length: 100 }, (_, i) => new Date().getFullYear() - i),
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
                                            length: new Date(Number(this.selectedYear), Number(this.selectedMonth), 0).getDate()
                                        }, (_, i) => String(i + 1).padStart(2, '0'));
                                    },
                                    get formattedDate() {
                                        return `${this.selectedYear}-${this.selectedMonth}-${this.selectedDay}`;
                                    },
                                    init() {
                                        const value = this.$wire.birthdate;
                                        if (value) {
                                            const parts = value.split('-');
                                            if (parts.length === 3) {
                                                this.selectedYear = parts[0];
                                                this.selectedMonth = parts[1];
                                                this.selectedDay = parts[2];
                                            }
                                        }

                                        this.$watch('$wire.birthdate', value => {
                                            if (!value || typeof value !== 'string') return;
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
                                        this.$wire.set(
                                            'birthdate',
                                            `${this.selectedYear}-${this.selectedMonth}-${this.selectedDay}`
                                        );
                                        this.openPicker = false;
                                        this.step = 'year';
                                    }
                                }"
                                class="relative">

                                <label class="mb-1 block text-xs font-bold text-slate-700">Birthdate *</label>

                                <button type="button" @click="openPicker = true"
                                    class="flex w-full items-center justify-between rounded-xl border border-slate-300 bg-white px-3 py-2 text-left text-sm text-slate-700 transition hover:border-[var(--color-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]">
                                    <span x-text="$wire.birthdate || 'Select Date of Birth'"
                                        :class="!$wire.birthdate ? 'text-slate-500' : 'text-slate-700'"></span>

                                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2v12a2 2 0 002 2z" />
                                    </svg>
                                </button>

                                <div x-show="openPicker" x-cloak x-transition.opacity
                                    class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">

                                    <div @click.stop class="w-full max-w-sm overflow-hidden rounded-2xl bg-white shadow-2xl">

                                        <div class="bg-[var(--color-accent)] p-5 text-white">
                                            <span class="text-[10px] font-bold uppercase tracking-widest opacity-80">
                                                Select Date of Birth
                                            </span>

                                            <h2 class="mt-1 text-xl font-extrabold" x-text="formattedDate"></h2>

                                            <div class="mt-3 flex gap-3 text-xs font-bold">
                                                <button type="button" @click="step = 'year'"
                                                    :class="step === 'year' ? 'underline opacity-100' : 'opacity-60'">
                                                    Year
                                                </button>
                                                <span>/</span>
                                                <button type="button" @click="step = 'month'"
                                                    :class="step === 'month' ? 'underline opacity-100' : 'opacity-60'">
                                                    Month
                                                </button>
                                                <span>/</span>
                                                <button type="button" @click="step = 'day'"
                                                    :class="step === 'day' ? 'underline opacity-100' : 'opacity-60'">
                                                    Day
                                                </button>
                                            </div>
                                        </div>

                                        <div class="p-4">

                                            <div x-show="step === 'year'">
                                                <div class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                    <span>Select Year</span>
                                                    <button type="button" @click="step = 'month'"
                                                        class="text-[var(--color-primary)] hover:underline">
                                                        Next: Month &rarr;
                                                    </button>
                                                </div>

                                                <div class="grid h-64 grid-cols-3 gap-2 overflow-y-auto pr-1 text-center">
                                                    <template x-for="yr in years" :key="yr">
                                                        <button type="button" @click="selectYear(yr)"
                                                            :class="String(selectedYear) === String(yr)
                                                                ? 'bg-[var(--color-primary)] text-white font-bold'
                                                                : 'bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                                            class="rounded-lg py-2.5 text-sm transition"
                                                            x-text="yr">
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>

                                            <div x-show="step === 'month'">
                                                <div class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                    <button type="button" @click="step = 'year'"
                                                        class="hover:underline">
                                                        &larr; Back
                                                    </button>

                                                    <span>Select Month</span>

                                                    <button type="button" @click="step = 'day'"
                                                        class="text-[var(--color-primary)] hover:underline">
                                                        Next: Day &rarr;
                                                    </button>
                                                </div>

                                                <div class="grid h-64 grid-cols-2 gap-2 overflow-y-auto pr-1 text-center">
                                                    <template x-for="month in months" :key="month.val">
                                                        <button type="button" @click="selectMonth(month.val)"
                                                            :class="selectedMonth === month.val
                                                                ? 'bg-[var(--color-primary)] text-white font-bold'
                                                                : 'bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                                            class="rounded-lg py-3 text-sm transition"
                                                            x-text="month.name">
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>

                                            <div x-show="step === 'day'">
                                                <div class="mb-3 flex items-center justify-between border-b pb-2 text-xs font-bold uppercase text-slate-500">
                                                    <button type="button" @click="step = 'month'"
                                                        class="hover:underline">
                                                        &larr; Back
                                                    </button>

                                                    <span>Select Day</span>
                                                    <span></span>
                                                </div>

                                                <div class="grid h-64 grid-cols-5 gap-2 overflow-y-auto pr-1 text-center">
                                                    <template x-for="day in daysInMonth" :key="day">
                                                        <button type="button" @click="selectDay(day)"
                                                            :class="selectedDay === day
                                                                ? 'bg-[var(--color-primary)] text-white font-bold'
                                                                : 'bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                                            class="rounded-lg py-2.5 text-sm transition"
                                                            x-text="day">
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="flex items-center justify-between border-t bg-slate-50 px-5 py-3">
                                            <button type="button"
                                                @click="openPicker = false; step = 'year'"
                                                class="rounded-lg px-4 py-2 text-xs font-bold text-slate-500 transition hover:bg-slate-200">
                                                Cancel
                                            </button>

                                            <button type="button" @click="confirmDate()"
                                                class="rounded-lg bg-[var(--color-primary)] px-6 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[var(--color-primary-mid)]">
                                                OK
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- OCCUPATION --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">Occupation</label>
                                <input wire:model.live="occupation" type="text"
                                    placeholder="e.g. Teacher, Driver, Engineer"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]">
                            </div>
                        </div>
                    </div>

                    {{-- ADDRESS & LOCATION --}}
                    <div class="space-y-4">
                        <div class="rounded-xl bg-[var(--color-primary)] px-4 py-2.5 shadow-sm">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-white sm:text-sm">
                                Address & Location
                            </h3>
                            <p class="mt-0.5 text-[10px] text-slate-200">
                                Update the member's complete address and location.
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label class="mb-1 block text-xs font-bold text-slate-700">
                                Complete Address *
                            </label>

                            <div class="flex gap-2">
                                <input wire:model.live="address" type="text"
                                    placeholder="House No., Street, Barangay, City/Municipality, Province"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]">

                                <button type="button" wire:click="toggleMap"
                                    class="shrink-0 rounded-xl bg-[var(--color-primary)] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[var(--color-primary-mid)]">
                                    {{ $showMapModal ? 'Close Map' : 'Pin Map' }}
                                </button>
                            </div>

                            <div x-data x-show="$wire.showMapModal" x-cloak
                                class="mt-3 space-y-3 overflow-hidden rounded-xl border border-slate-300 bg-white p-3 shadow-sm">

                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-bold text-[var(--color-primary)]">Select Location</h4>
                                    <span class="text-[10px] text-slate-500">Search or click on map</span>
                                </div>

                                <div class="flex gap-2">
                                    <input id="leaflet-search-input" type="text"
                                        placeholder="Search location..."
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]">

                                    <button id="leaflet-search-btn" type="button"
                                        class="shrink-0 rounded-xl bg-[var(--color-primary)] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[var(--color-primary-mid)]">
                                        Search
                                    </button>
                                </div>

                                <div class="h-[350px] overflow-hidden rounded-lg border border-slate-200">
                                    <div id="map" wire:ignore class="h-full w-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ACCOUNT INFORMATION --}}
                    <div class="space-y-4">
                        <div class="rounded-xl bg-[var(--color-primary)] px-4 py-2.5 shadow-sm">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-white sm:text-sm">
                                Account Information
                            </h3>
                            <p class="mt-0.5 text-[10px] text-slate-200">
                                Update the member's contact and login information.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 pt-1 sm:grid-cols-2 lg:grid-cols-3">

                            {{-- MOBILE --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Mobile Number *
                                </label>
                                <input wire:model.live="mobile" type="tel" placeholder="0917XXXXXXX"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]">
                            </div>

                            {{-- EMAIL --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                    Email Address <span class="font-normal text-slate-500">(Optional)</span>
                                </label>
                                <input wire:model="email" type="email" placeholder="juan@example.com"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]">
                            </div>

                            {{-- USERNAME --}}
                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-700">Username *</label>
                                <input wire:model="username" type="text" placeholder="Juan123"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="flex flex-col items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 p-4 sm:flex-row">
                    <span class="text-center text-xs text-slate-500 sm:text-left">
                        * Please verify all details before updating.
                    </span>

                    <div class="flex w-full items-center gap-3 sm:w-auto">
                        <button wire:click="close" type="button"
                            class="w-1/2 rounded-xl border border-slate-300 px-5 py-2.5 text-center text-xs font-bold text-slate-600 transition hover:bg-slate-100 sm:w-auto">
                            Cancel
                        </button>

                        <button wire:click="confirmUpdate"
                            wire:loading.attr="disabled"
                            type="button"
                            class="flex w-1/2 items-center justify-center rounded-xl bg-[var(--color-accent)] px-6 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-[var(--color-accent)] disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto">

                            <span wire:loading.remove wire:target="confirmUpdate">
                                Update Member
                            </span>

                            <div wire:loading.flex wire:target="confirmUpdate"
                                class="items-center justify-center gap-2">
                                <svg class="h-4 w-4 shrink-0 animate-spin text-[var(--color-primary)]"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <span>Updating...</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- LEAFLET --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css">

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    @vite('resources/js/livewire/admin-pages/masterlist/satellite-members-components/satellite-members-edit.js')
</div>
