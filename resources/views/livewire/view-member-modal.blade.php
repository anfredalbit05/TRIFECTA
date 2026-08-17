{{--
=====================================================================
 VIEW MEMBER MODAL
=====================================================================
 Backing component: App\Livewire\ViewMemberModal
 Opened via the 'open-view-member' event (dispatched with UserID).

 Layout notes:
   - Outer modal box is a flex column capped at max-h-[92vh] so it
     never exceeds the viewport (important on mobile).
   - Header and Footer are `shrink-0` so they always stay visible.
   - Body is `flex-1 overflow-y-auto` — it's the only scrollable
     region, sitting between the fixed header and fixed footer.

 Sections:
   1. Header (avatar + name + close button)
   2. Body
      2a. Personal Information
      2b. Address & Pinpoint Location (Leaflet map)
      2c. Contact Information
      2d. Activation Information
      2e. Beneficiaries
   3. Footer (Certificate & Policy downloads — Active members only)
=====================================================================
--}}

<div>

    @if ($open)

        {{-- ============================= BACKDROP ============================= --}}
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 p-4 backdrop-blur-sm">

            {{-- ============================= MODAL BOX ============================= --}}
            <div class="flex max-h-[92vh] w-full max-w-6xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

                {{-- ======================== 1. HEADER ======================== --}}
                <div class="shrink-0 bg-[#0C2C6B] px-6 py-5 text-white">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-center gap-4">
                            {{-- Avatar --}}
                            {{-- <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20 text-2xl font-bold">
                                {{ strtoupper(substr($first_name, 0, 1)) }}
                            </div> --}}

                            @if (!empty($this->selectedUser->profilepic))
                                <img
                                    src="{{ Str::startsWith($this->selectedUser->profilepic, 'storage/')
                                        ? asset($this->selectedUser->profilepic)
                                        : asset('storage/' . $this->selectedUser->profilepic) }}"
                                    alt="profile-image"
                                    class="w-14 h-14 rounded-full object-cover border border-slate-200 shadow-sm shrink-0"
                                >
                            @else
                                <div class="w-14 h-14 rounded-full bg-[#0C2C6B] text-white flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
                                    {{ strtoupper(
                                        substr($this->selectedUser->firstname, 0, 1) .
                                        substr($this->selectedUser->lastname, 0, 1)
                                    ) }}
                                </div>
                            @endif

                            <div>
                                <h2 class="text-2xl font-bold">
                                    {{ $first_name }} {{ $middle_name }} {{ $surname }}
                                </h2>
                                <p class="text-sm text-blue-100">Member Information</p>
                            </div>
                        </div>

                        <button
                            wire:click="close"
                            class="rounded-lg bg-white/20 px-4 py-2 transition hover:bg-red-500">
                            ✕
                        </button>

                    </div>
                </div>

                {{-- ======================== 2. BODY (scrollable) ======================== --}}
                <div class="flex-1 overflow-y-auto p-6">

                    {{-- ---- 2a. Personal Information ---- --}}
                    <div class="mb-6 rounded-xl border border-slate-200 shadow-sm">
                        <div class="border-b border-slate-200 bg-slate-50 px-5 py-3">
                            <h3 class="font-bold text-slate-800">👤 Personal Information</h3>
                        </div>

                        <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2 lg:grid-cols-3">
                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Surname</p>
                                <p class="mt-1 font-semibold">{{ $surname }}</p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">First Name</p>
                                <p class="mt-1 font-semibold">{{ $first_name }}</p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Middle Name</p>
                                <p class="mt-1 font-semibold">{{ $middle_name ?: 'N/A' }}</p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Gender</p>
                                <p class="mt-1 font-semibold">{{ $gender }}</p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Civil Status</p>
                                <p class="mt-1 font-semibold">{{ $civil_status }}</p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Birthdate</p>
                                <p class="mt-1 font-semibold">
                                    {{ \Carbon\Carbon::parse($birthdate)->format('F d, Y') }}
                                </p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Referral Code</p>
                                <p class="mt-1 font-semibold">{{ $referralCode }}</p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Upline Code</p>
                                <p class="mt-1 font-semibold">{{ $uplineCode }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- ---- 2b. Address & Pinpoint Location ---- --}}
                    <div class="mb-6 rounded-xl border border-slate-200 shadow-sm"
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
                        <div class="border-b border-slate-200 bg-slate-50 px-5 py-3">
                            <h3 class="font-bold text-slate-800">📍 Address & Pinpoint Location</h3>
                        </div>

                        <div class="p-5 space-y-4">
                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400 mb-1">Text Address</p>
                                {{ $address ?: 'No address provided' }}
                            </div>

                            {{-- Leaflet Map Container --}}
                            <div class="overflow-hidden rounded-lg border border-slate-200">
                                <div id="member-map" class="h-100 w-full z-0"></div>
                            </div>
                        </div>
                    </div>

                    {{-- ---- 2c. Contact Information ---- --}}
                    <div class="mb-6 rounded-xl border border-slate-200 shadow-sm">
                        <div class="border-b border-slate-200 bg-slate-50 px-5 py-3">
                            <h3 class="font-bold text-slate-800">📞 Contact Information</h3>
                        </div>

                        <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-3">
                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Mobile</p>
                                <p class="mt-1 font-semibold">{{ $mobile }}</p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Email</p>
                                <p class="mt-1 font-semibold">{{ $email ?: 'N/A' }}</p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Username</p>
                                <p class="mt-1 font-semibold">{{ $username }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- ---- 2d. Activation Information ---- --}}
                    <div class="mb-6 rounded-xl border border-slate-200 shadow-sm">
                        <div class="border-b border-slate-200 bg-slate-50 px-5 py-3">
                            <h3 class="font-bold text-slate-800">✅ Activation Information</h3>
                        </div>

                        <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2">
                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">Activation Status</p>
                                <p class="mt-1 font-semibold">
                                    @if ($activationStatus === 'Approved')
                                        <span class="text-green-600">Approved</span>
                                    @elseif ($activationStatus === 'Rejected')
                                        <span class="text-red-600">Rejected</span>
                                    @elseif (empty($activationStatus))
                                        <span class="text-gray-600">N/A</span>
                                    @else
                                        <span class="text-yellow-600">Pending</span>
                                    @endif
                                </p>
                            </div>

                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs uppercase text-slate-400">{{ $activationLabel ?? 'Date' }}</p>
                                <p class="mt-1 font-semibold">{{ $activationDate ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- ---- 2e. Beneficiaries ---- --}}
                    <div class="rounded-xl border border-slate-200 shadow-sm">
                        <div class="border-b border-slate-200 bg-slate-50 px-5 py-3">
                            <h3 class="font-bold text-slate-800">👨‍👩‍👧 Beneficiaries</h3>
                        </div>

                        <div class="p-5">
                            @forelse ($beneficiaries as $index => $beneficiary)
                                <div class="mb-4 rounded-xl border border-slate-200 p-4">
                                    <div class="mb-4 flex items-center justify-between">
                                        <h4 class="font-bold text-slate-700">
                                            Beneficiary {{ $index + 1 }}
                                        </h4>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                        <div class="rounded-lg bg-slate-50 p-4">
                                            <p class="text-xs uppercase text-slate-400">Full Name</p>
                                            <p class="mt-1 font-semibold">{{ $beneficiary['fullname'] ?: 'N/A' }}</p>
                                        </div>

                                        <div class="rounded-lg bg-slate-50 p-4">
                                            <p class="text-xs uppercase text-slate-400">Birthdate</p>
                                            <p class="mt-1 font-semibold">{{ $beneficiary['birthdate'] ?: 'N/A' }}</p>
                                        </div>

                                        <div class="rounded-lg bg-slate-50 p-4">
                                            <p class="text-xs uppercase text-slate-400">Relationship</p>
                                            <p class="mt-1 font-semibold">{{ $beneficiary['relationship'] ?: 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-lg border border-dashed border-slate-300 p-10 text-center text-slate-500">
                                    No beneficiaries found.
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
                {{-- end 2. BODY --}}

                {{-- ======================== 3. FOOTER ======================== --}}
                @if ($status === 'Active')
                    <div class="shrink-0 flex flex-col gap-3 border-t border-slate-200 px-6 py-4 sm:flex-row sm:justify-end">

                        <a href="{{ route('admin.member.policy.download', $UserID) }}"
                           target="_blank"
                           class="inline-flex items-center justify-center gap-2 rounded-lg border border-[#0C2C6B] px-4 py-2 text-sm font-semibold text-[#0C2C6B] transition hover:bg-[#0C2C6B]/5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download Policy
                        </a>

                        <a href="{{ route('admin.member.certificate.download', $UserID) }}"
                           target="_blank"
                           class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#0C2C6B] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#0C2C6B]/90">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Certificate
                        </a>

                    </div>
                @endif

            </div>
            {{-- end MODAL BOX --}}

        </div>
        {{-- end BACKDROP --}}

    @endif

</div>
