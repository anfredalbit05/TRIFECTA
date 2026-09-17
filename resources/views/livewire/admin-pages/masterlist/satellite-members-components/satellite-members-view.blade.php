{{--
=====================================================================
 VIEW MEMBER MODAL
=====================================================================
 Backing component: App\Livewire\ViewMemberModal
 Opened via the 'open-view-member' event (dispatched with UserID).

 Layout & color palette matched to the Application Modal:
   - Section headers: navy bar (#0B2A5B) + gold uppercase title (#E8B923)
     + slate-200 subtitle.
   - Field cards: slate-50 background, slate-400 uppercase label,
     slate-700/800 value.
   - Outer modal: rounded-2xl, border-slate-100, shadow-2xl,
     max-h-[90vh] flex column so header/footer stay fixed and only
     the body scrolls.

 Sections:
   1. Header (avatar + name + status + close button)
   2. Body
      2a. Personal Information
      2b. Policy Information
      2c. Address & Pinpoint Location (Leaflet map)
      2d. Contact Information
      2e. Activation Information
      2f. Beneficiaries
   3. Footer (Certificate & Policy downloads — Active members only)
=====================================================================
--}}

<div>

    @if ($open)

        {{-- ============================= BACKDROP ============================= --}}
        <div class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto bg-black/70 p-3 backdrop-blur-sm sm:p-4">

            {{-- ============================= MODAL BOX ============================= --}}
            <div class="my-auto flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-2xl">

                {{-- ======================== 1. HEADER ======================== --}}
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-5 py-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#0B2A5B] text-lg font-extrabold text-[#E8B923]">
                            {{ strtoupper(substr($first_name, 0, 1)) }}
                        </div>
                        <div>
                            <h2 class="text-lg font-extrabold text-[#0B2A5B] sm:text-xl">
                                {{ $first_name }} {{ $middle_name }} {{ $surname }}
                            </h2>
                            <div class="mt-0.5 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                <span>Member Information</span>
                                @if ($status)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold uppercase text-slate-600">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $status === 'Active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ $status }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <button
                        wire:click="close"
                        type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 font-bold text-slate-500 transition hover:bg-rose-100 hover:text-rose-600"
                        title="Close">
                        &#10005;
                    </button>
                </div>

                {{-- ======================== 2. BODY (scrollable) ======================== --}}
                <div class="space-y-7 overflow-y-auto p-4 sm:p-6">

                    {{-- =================================================
                         2c. ADDRESS & PINPOINT LOCATION
                    ================================================== --}}
                    <div
                        class="space-y-4"
                        x-data="{
                            map: null,
                            initMap() {
                                if (this.map) {
                                    this.map.remove();
                                }
                                let lat = {{ $latitude ?? 7.442294461028405 }};
                                let lng = {{ $longitude ?? 125.81370503321095 }};

                                setTimeout(() => {
                                    this.map = L.map('member-map').setView([lat, lng], 15);
                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        maxZoom: 19,
                                        attribution: '&copy; OpenStreetMap'
                                    }).addTo(this.map);
                                    L.marker([lat, lng]).addTo(this.map).bindPopup('Member Location').openPopup();
                                }, 150);
                            }
                        }"
                        x-init="initMap()">
                        <div class="flex items-center justify-between rounded-xl bg-[#0B2A5B] px-4 py-2.5 shadow-sm">
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-[#E8B923] sm:text-sm">
                                    Address & Location
                                </h3>
                                <p class="mt-0.5 text-[10px] font-normal normal-case tracking-normal text-slate-200">
                                    The applicant's complete address and pinpointed location.
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3 pt-1">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase text-slate-400">Complete Address</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $address ?: 'No address provided' }}
                                </p>
                            </div>

                            {{-- Leaflet Map Container --}}
                            <div class="h-[300px] overflow-hidden rounded-xl border border-slate-300">
                                <div id="member-map" class="h-full w-full z-0"></div>
                            </div>
                        </div>
                    </div>

                    {{-- =================================================
                         2d. CONTACT INFORMATION
                    ================================================== --}}
                    <div class="space-y-4">
                        <div class="flex items-center justify-between rounded-xl bg-[#0B2A5B] px-4 py-2.5 shadow-sm">
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-[#E8B923] sm:text-sm">
                                    Account & Contact Information
                                </h3>
                                <p class="mt-0.5 text-[10px] font-normal normal-case tracking-normal text-slate-200">
                                    Account credentials and contact information on record.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 pt-1 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase text-slate-400">Mobile Number</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800">{{ $mobile ?: 'N/A' }}</p>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase text-slate-400">Email Address</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800">{{ $email ?: 'N/A' }}</p>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase text-slate-400">Username</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800">{{ $username ?: 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- =================================================
                         2e. ACTIVATION INFORMATION
                    ================================================== --}}
                    <div class="space-y-4">
                        <div class="flex items-center justify-between rounded-xl bg-[#0B2A5B] px-4 py-2.5 shadow-sm">
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-[#E8B923] sm:text-sm">
                                    Activation Information
                                </h3>
                                <p class="mt-0.5 text-[10px] font-normal normal-case tracking-normal text-slate-200">
                                    Current activation status and relevant date.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 pt-1 sm:grid-cols-2">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase text-slate-400">Activation Status</p>
                                <p class="mt-1 text-sm font-bold">
                                    @if ($activationStatus === 'Approved')
                                        <span class="text-green-600">Approved</span>
                                    @elseif ($activationStatus === 'Rejected')
                                        <span class="text-red-600">Rejected</span>
                                    @elseif (empty($activationStatus))
                                        <span class="text-slate-500">N/A</span>
                                    @else
                                        <span class="text-amber-600">Pending</span>
                                    @endif
                                </p>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase text-slate-400">{{ $activationLabel ?? 'Date' }}</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800">{{ $activationDate ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>



                </div>
                {{-- end 2. BODY --}}

                {{-- ======================== 3. FOOTER ======================== --}}
                <div class="flex flex-col items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 p-4 sm:flex-row">
                    <span class="text-center text-xs text-slate-500 sm:text-left">
                        Viewing full member record.
                    </span>

                    <div class="flex w-full flex-col items-center gap-3 sm:w-auto sm:flex-row">

                        @if ($status === 'Active')
                            <a href="{{ route('admin.member.policy.download', $UserID) }}" target="_blank"
                                class="flex w-full items-center justify-center gap-2 rounded-xl border border-[#0B2A5B] px-5 py-2.5 text-xs font-bold text-[#0B2A5B] shadow-sm transition hover:bg-[#0B2A5B]/5 sm:w-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Download Policy
                            </a>

                            <a href="{{ route('admin.member.certificate.download', $UserID) }}" target="_blank"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#D4A017] px-6 py-2.5 text-xs font-bold text-[#0B2A5B] shadow-md transition hover:bg-[#E8B923] sm:w-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download Certificate
                            </a>
                        @endif

                        <button
                            wire:click="close"
                            type="button"
                            class="w-full rounded-xl border border-slate-300 px-5 py-2.5 text-center text-xs font-bold text-slate-600 transition hover:bg-slate-100 sm:w-auto">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

    @endif

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</div>
