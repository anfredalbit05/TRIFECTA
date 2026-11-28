<div>
    @php
        $in = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]';
        $lbl = 'mb-1 block text-xs font-bold text-slate-700';
        $btn = 'shrink-0 rounded-xl bg-[var(--color-primary)] px-3 py-2 text-xs font-bold text-white hover:bg-[var(--color-primary-mid)]';
        $steps = ['Personal & Address', 'Beneficiaries', 'Account & Security'];
        $head = fn ($t, $s) => '<div class="rounded-xl bg-[var(--color-primary)] px-4 py-2.5 shadow-sm"><h3 class="text-xs font-bold uppercase tracking-wider text-white sm:text-sm">' . $t . '</h3><p class="mt-0.5 text-[10px] text-slate-200">' . $s . '</p></div>';
    @endphp

    @if ($open)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto bg-black/70 p-3 backdrop-blur-sm sm:p-4">
            <div class="my-auto flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-2xl">

                {{-- HEADER + STEPPER --}}
                <div class="border-b border-slate-200 bg-white px-5 py-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-extrabold text-[var(--color-primary)] sm:text-xl">
                                {{ $isEditMode ? 'Update Member' : 'Membership Application' }}
                            </h2>
                            <p class="text-xs text-slate-500">Health &middot; Wellness &middot; Protection</p>
                        </div>
                        <button wire:click="close" type="button" title="Close"
                            class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 font-bold text-slate-500 transition hover:bg-rose-100 hover:text-rose-600">&#10005;</button>
                    </div>

                    @unless ($isEditMode)
                    <div class="mt-4 flex items-center gap-2">
                        @foreach ($steps as $i => $label)
                            @php $n = $i + 1; @endphp
                            <div class="flex items-center gap-2 {{ $n < 3 ? 'flex-1' : '' }}">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $step >= $n ? 'bg-[var(--color-primary)] text-white' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $step > $n ? '✓' : $n }}
                                </span>
                                <span class="hidden text-xs font-bold sm:block {{ $step >= $n ? 'text-[var(--color-primary)]' : 'text-slate-400' }}">{{ $label }}</span>
                                @if ($n < 3)
                                    <div class="h-0.5 flex-1 rounded {{ $step > $n ? 'bg-[var(--color-primary)]' : 'bg-slate-200' }}"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @endunless
                </div>

                {{-- BODY (all steps stay in the DOM so the map survives step changes) --}}
                <div x-data x-effect="$wire.step, $el.scrollTop = 0" class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-6">

                    {{-- ========== STEP 1: PERSONAL + ADDRESS ========== --}}
                    <div x-show="$wire.isEditMode || $wire.step === 1" x-cloak class="space-y-7">
                        <div class="space-y-4">
                            {!! $head('Applicant Personal Information', "Please provide the applicant's personal details.") !!}

                            <div class="grid grid-cols-1 gap-4 pt-1 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ([['surname', 'Surname *', 'Dela Cruz'], ['first_name', 'First Name *', 'Juan'], ['middle_name', 'Middle Name (Optional)', 'Santos']] as [$f, $l, $p])
                                    <div>
                                        <label class="{{ $lbl }}">{{ $l }}</label>
                                        <input wire:model.blur="{{ $f }}" type="text"
                                            placeholder="{{ $p }}" class="{{ $in }}">
                                    </div>
                                @endforeach

                                @foreach (['gender' => ['Gender', ['Male', 'Female', 'Other']], 'civil_status' => ['Civil Status', ['Single', 'Married', 'Widowed', 'Separated']]] as $f => [$l, $opts])
                                    <div>
                                        <label class="{{ $lbl }}">{{ $l }}</label>
                                        <select wire:model="{{ $f }}" class="{{ $in }}">
                                            <option value="">Select {{ $l }}</option>
                                            @foreach ($opts as $o)
                                                <option>{{ $o }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach

                                @include('livewire.partials.ymd-picker', ['model' => 'birthdate', 'label' => 'Birthdate *', 'title' => 'Select Date of Birth'])

                                    {{-- OCCUPATION --}}
                                    <div>
                                        <label class="{{ $lbl }}">Occupation</label>
                                        <input wire:model.blur="occupation" type="text"
                                                placeholder="e.g. Teacher, Driver, Engineer" class="{{ $in }}">
                                    </div>

                                    {{-- EMPLOYMENT TYPE --}}
                                    <div>
                                        <label class="{{ $lbl }}">Employment Type</label>

                                        <select wire:model="employment_type_id" class="{{ $in }}">
                                            <option value="">Select Employment Type (Optional)</option>

                                            @foreach ($employmentTypes as $type)
                                                <option value="{{ $type->employment_type_id }}">
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- MEMBER TYPE --}}
                                    @if($isEditMode)
                                    <div>
                                        <label class="{{ $lbl }}">Member Type</label>

                                        <select wire:model="member_type_id" class="{{ $in }}">
                                            <option value="">Select Member Type (Optional)</option>

                                            @foreach ($memberTypes as $type)
                                                <option value="{{ $type->member_type_id }}">
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif

                                    @if(!$isEditMode)
                                    {{-- BRANCH --}}
                                    <div>
                                        <label class="{{ $lbl }}">Branch *</label>

                                        <select wire:model="BranchID" class="{{ $in }}">
                                            <option value="">Select Branch</option>

                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->BranchID }}">
                                                    {{ $branch->branchName }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('BranchID')
                                            <p class="mt-1 text-xs font-semibold text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                    @endif
                            </div>
                        </div>

                        {{-- POLICY (edit mode with approved activation only) --}}
                        @if ($isEditMode && $activationId)
                            <div class="space-y-4">
                                {!! $head('Policy Information', 'Membership policy details and expiration information.') !!}

                                <div class="grid grid-cols-1 gap-4 pt-1 sm:grid-cols-2">

                                {{-- POLICY NUMBER: VIEW ONLY --}}
                                <div>
                                    <label class="{{ $lbl }}">Policy Number</label>

                                    <input
                                        wire:model="policy_number"
                                        type="text"
                                        readonly
                                        class="{{ $in }} cursor-not-allowed bg-slate-100 text-slate-600"
                                    />

                                    @if (filled($issuedPolicyNumber))
                                        <p class="mt-1 text-[10px] font-bold uppercase tracking-wide text-[var(--color-gold-darker)]">
                                            ✓ Issued: {{ $issuedPolicyNumber }}
                                        </p>
                                    @endif
                                </div>

                                {{-- RECEIPT NUMBER: VIEW ONLY --}}
                                <div>
                                    <label class="{{ $lbl }}">Receipt Number</label>

                                    <input
                                        wire:model="receipt_number"
                                        type="text"
                                        readonly
                                        class="{{ $in }} cursor-not-allowed bg-slate-100 text-slate-600"
                                    />

                                    @if (filled($activationControlNumber))
                                        <p class="mt-1 text-[10px] font-bold uppercase tracking-wide text-[var(--color-gold-darker)]">
                                            ✓ Issued: {{ $activationControlNumber }}
                                        </p>
                                    @endif
                                </div>


                                    @foreach (['policy_effectivity_date' => 'Policy Effectivity Date', 'policy_expiration_date' => 'Policy Expiration Date'] as $f => $l)
                                        <div>
                                            <label class="{{ $lbl }}">{{ $l }}</label>
                                            <input wire:model.live="{{ $f }}" type="date" class="{{ $in }}">
                                            @error($f)
                                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endforeach

                                    <div class="sm:col-span-2">
                                        <label class="{{ $lbl }}">Cocogen Policy Number</label>
                                        <input wire:model.live="cocogen_policy_number" type="text" placeholder="12345" class="{{ $in }}">
                                        @error('cocogen_policy_number')
                                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- ADDRESS --}}
                        <div class="space-y-4">
                            {!! $head('Address & Location', "Provide the applicant's complete address and location.") !!}

                            <div class="space-y-2">
                                <label class="{{ $lbl }}">Complete Address *</label>
                                <div class="flex gap-2">
                                    <input wire:model.blur="address" type="text"
                                            placeholder="House No., Street, Barangay, City/Municipality, Province"
                                            class="{{ $in }}">
                                    <button type="button" wire:click="toggleMap" class="{{ $btn }} px-4">
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
                                        <input id="leaflet-search-input" type="text" placeholder="Search location (e.g., Lafilipina)..." class="{{ $in }}">
                                        <button id="leaflet-search-btn" type="button" class="{{ $btn }} px-4">Search</button>
                                    </div>
                                    <div class="h-[350px] overflow-hidden rounded-lg border border-slate-200">
                                        <div id="map" wire:ignore class="h-full w-full"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @unless ($isEditMode)
                    {{-- ========== STEP 2: BENEFICIARIES ========== --}}
                    <div x-show="$wire.step === 2" x-cloak class="space-y-4">
                        <div class="flex items-center justify-between rounded-xl bg-[var(--color-primary)] px-4 py-2.5 shadow-sm">
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-white sm:text-sm">Beneficiary Information</h3>
                                <p class="mt-0.5 text-[10px] text-slate-200">Add the beneficiaries who may receive the applicable benefits.</p>
                            </div>
                            <span class="hidden text-[10px] uppercase text-slate-200 sm:block">Up to 3 Dependents</span>
                        </div>

                        @for ($i = 0; $i < 3; $i++)
                            @php $saved = ! empty($beneficiaryIds[$i] ?? null); @endphp
                            <div wire:key="beneficiary-card-{{ $i }}"
                                class="relative grid grid-cols-1 gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3 pt-12 sm:grid-cols-2 sm:p-4 sm:pt-12 lg:grid-cols-3">
                                <div class="absolute left-4 top-3 flex items-center gap-2">
                                    <span class="flex h-6 min-w-6 items-center justify-center rounded-full bg-[var(--color-primary)] px-2 text-[10px] font-bold text-white">{{ $i + 1 }}</span>
                                    <span class="text-xs font-bold text-slate-600">Beneficiary {{ $i + 1 }}</span>
                                    @if ($saved)
                                        <span class="rounded-full bg-[var(--color-gold-tint)] px-2 py-0.5 text-[9px] font-bold uppercase text-[var(--color-gold-darker)]">Saved</span>
                                    @endif
                                </div>

                                <button type="button" wire:click="confirmDeleteBeneficiary({{ $i }})"
                                    class="absolute right-3 top-3 rounded-lg border border-rose-200 bg-white px-3 py-1.5 text-xs font-bold text-rose-600 shadow-sm transition hover:bg-rose-50">
                                    {{ $saved ? 'Delete' : 'Clear' }}
                                </button>

                                <div>
                                    <label class="{{ $lbl }}">Full Name</label>
                                    <input wire:model.blur="beneficiaries.{{ $i }}.fullname"
                                            type="text" placeholder="Full name of beneficiary" class="{{ $in }}">
                                </div>

                                @include('livewire.partials.ymd-picker', ['model' => "beneficiaries.$i.birthdate", 'label' => 'Birthdate', 'title' => 'Select Beneficiary Birthdate'])

                                <div>
                                    <label class="{{ $lbl }}">Relationship</label>
                                    <input wire:model.blur="beneficiaries.{{ $i }}.relationship"
                                            type="text" placeholder="Father, Mother, Spouse..." class="{{ $in }}">
                                </div>
                            </div>
                        @endfor
                    </div>
                    @endunless

                    {{-- ========== STEP 3: ACCOUNT & SECURITY ========== --}}
                    <div x-show="$wire.isEditMode || $wire.step === 3" x-cloak class="space-y-7">
                        <div class="space-y-4">
                            {!! $head('Account & Security Information', 'Account credentials and contact information.') !!}

                            <div class="grid grid-cols-1 gap-4 pt-1 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ([['mobile', 'Mobile Number', 'tel', '0917XXXXXXX', 'live'], ['email', 'Email Address (Optional)', 'email', 'juan@example.com', 'blur'], ['username', 'Username *', 'text', 'Juan123', 'blur']] as [$f, $l, $t, $p, $m])
                                    <div>
                                        <label class="{{ $lbl }}">{{ $l }}</label>
                                        <input wire:model.{{ $m }}="{{ $f }}" type="{{ $t }}" placeholder="{{ $p }}" class="{{ $in }}">
                                    </div>
                                @endforeach

                                @unless ($isEditMode)
                                    @foreach (['password' => 'Password', 'password_confirmation' => 'Confirm Password'] as $f => $l)
                                        <div x-data="{ show: false }">
                                            <label class="{{ $lbl }}">{{ $l }} *</label>
                                            <div class="relative">
                                                <input wire:model="{{ $f }}" :type="show ? 'text' : 'password'" placeholder="********" class="{{ $in }} pr-14">
                                                <button type="button" @click="show = !show" x-text="show ? 'Hide' : 'Show'"
                                                    class="absolute inset-y-0 right-0 px-3 text-xs font-bold text-slate-500 hover:text-slate-700"></button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endunless
                            </div>
                        </div>

                        @unless ($isEditMode)
                            <div class="space-y-3">
                                {!! $head('Acceptance of Terms', 'Please read before submitting.') !!}

                                <div class="max-h-32 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs leading-relaxed text-slate-600 sm:text-sm">
                                    <p class="mb-1 font-semibold text-slate-800">Membership Terms & Policy Summary:</p>
                                    I acknowledge that the information provided is true, accurate, and correct to the best of my
                                    knowledge. I agree to the standard micro-insurance coverage guidelines, claim procedures,
                                    and privacy policies established by TRIFECTA and partner underwriters.
                                </div>

                                <label class="flex cursor-pointer select-none items-start gap-3 sm:items-center">
                                    <input type="checkbox" wire:model.live="agree"
                                        class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)] sm:mt-0">
                                    <span class="text-xs font-semibold text-slate-700 sm:text-sm">I have read and agree to the terms and conditions.</span>
                                </label>
                            </div>
                        @endunless
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="flex items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 p-4">

                    @unless ($isEditMode)
                        <span class="hidden text-xs text-slate-500 sm:block">
                            Step {{ $step }} of 3 &bull; Please verify all details before submitting.
                        </span>
                    @endunless

                    <div class="ml-auto flex w-full items-center gap-3 sm:w-auto">

                        @if ($isEditMode)

                            <button wire:click="close" type="button"
                                class="w-1/2 rounded-xl border border-slate-300 px-5 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100 sm:w-auto">
                                Cancel
                            </button>

                            <button wire:click="confirmUpdate" type="button"
                                wire:loading.attr="disabled" wire:target="confirmUpdate"
                                class="flex w-1/2 items-center justify-center rounded-xl bg-[var(--color-accent)] px-6 py-2.5 text-xs font-bold text-white shadow-md transition disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto">

                                <span wire:loading.remove wire:target="confirmUpdate">
                                    Update Member
                                </span>

                                <span wire:loading wire:target="confirmUpdate">
                                    Updating...
                                </span>
                            </button>

                        @else

                            @if ($step > 1)
                                <button wire:click="back" type="button"
                                    class="w-1/2 rounded-xl border border-slate-300 px-5 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100 sm:w-auto">
                                    &larr; Back
                                </button>
                            @else
                                <button wire:click="close" type="button"
                                    class="w-1/2 rounded-xl border border-slate-300 px-5 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100 sm:w-auto">
                                    Cancel
                                </button>
                            @endif

                            @if ($step < 3)
                                <button wire:click="next" type="button"
                                    wire:loading.attr="disabled" wire:target="next"
                                    class="w-1/2 rounded-xl bg-[var(--color-accent)] px-6 py-2.5 text-xs font-bold text-white shadow-md transition disabled:opacity-50 sm:w-auto">
                                    Next &rarr;
                                </button>
                            @else
                                <button wire:click="submit" type="button"
                                    wire:loading.attr="disabled" wire:target="submit"
                                    @disabled(! $agree)
                                    class="flex w-1/2 items-center justify-center rounded-xl bg-[var(--color-accent)] px-6 py-2.5 text-xs font-bold text-white shadow-md transition disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto">

                                    <span wire:loading.remove wire:target="submit">
                                        Submit Application
                                    </span>

                                    <span wire:loading wire:target="submit">
                                        Submitting...
                                    </span>
                                </button>
                            @endif

                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    @vite('resources/js/livewire/application-modal.js')
</div>
