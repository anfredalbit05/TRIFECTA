@php
    use Illuminate\Support\Str;
@endphp

<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8"
    x-data="profileForm({
        firstname: @js($firstname),
        lastname: @js($lastname),
        middlename: @js($middlename),
        birthdate: @js($birthdate),
        contactno: @js($contactno),
        address: @js($address),
        civilStatus: @js($civilStatus),
        sex: @js($sex),
        emergencyName: @js($emergencyName),
        emergencyRelationship: @js($emergencyRelationship),
        emergencyContactno: @js($emergencyContactno),
        emergencyAddress: @js($emergencyAddress)
    })"
>
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between text-center sm:text-left">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                Personal Information & ID Card
            </h2>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Manage your personal details, profile picture, emergency contacts, signature, and ID card.
            </p>
        </div>
    </div>

    <!-- Success Messages -->
    @if (session()->has('profile-success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 sm:px-5 py-4 text-emerald-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-xs sm:text-sm font-medium">{{ session('profile-success') }}</p>
        </div>
    @endif

    @if (session()->has('picture-success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 sm:px-5 py-4 text-emerald-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-xs sm:text-sm font-medium">{{ session('picture-success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 sm:gap-8">
        <!-- Left Side: Profile Picture, Form Fields -->
        <div class="space-y-6 sm:space-y-8">
            <!-- Profile Picture Card -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-4 sm:px-6 py-4 sm:py-5 text-center sm:text-left">
                    <h3 class="font-semibold text-sm sm:text-base text-slate-900">Profile Picture</h3>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500">Upload a clear profile picture. Maximum file size is 2 MB.</p>
                </div>

                <div class="flex flex-col items-center gap-6 px-4 sm:px-6 py-6 sm:flex-row sm:items-center text-center sm:text-left">
                    <div class="h-28 w-28 shrink-0 overflow-hidden rounded-full border-4 border-slate-100 bg-slate-100 shadow-sm mx-auto sm:mx-0">
                        @if ($profilepic)
                            <img src="{{ $profilepic->temporaryUrl() }}" class="h-full w-full object-cover" alt="New profile picture preview">
                        @else
                            @php
                                $profilePic = auth()->user()->profilepic;
                            @endphp

                            @if (!$profilePic || $profilePic === 'images/profiles/default-avatar.png')
                                <img src="{{ asset('images/profiles/default-avatar.png') }}" class="h-full w-full object-cover" alt="Default profile picture">
                            @else
                                <img src="{{ Storage::url($profilePic) }}" class="h-full w-full object-cover" alt="Profile picture">
                            @endif
                        @endif
                    </div>

                    <div class="flex-1 w-full flex flex-col items-center sm:items-start">
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                            <label for="profilepic"
                                class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl px-4 sm:px-5 py-3 text-xs sm:text-sm font-semibold text-white transition w-full sm:w-auto bg-[#0C2C6B] hover:bg-[#071a42] text-center whitespace-nowrap">
                                Choose New Picture
                            </label>
                            <input id="profilepic" type="file" wire:model="profilepic" accept="image/*" class="hidden">

                            @if (!$profilepic && auth()->user()->profilepic && auth()->user()->profilepic !== 'images/profiles/default-avatar.png')
                                <button type="button" wire:click="removeProfilePicture"
                                    wire:confirm="Are you sure you want to remove your profile picture?"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl px-4 sm:px-5 py-3 text-white sm:text-sm font-semibold bg-[#D79B10] transition w-full sm:w-auto text-center whitespace-nowrap cursor-pointer">
                                    Remove Picture
                                </button>
                            @endif
                        </div>

                        @error('profilepic')
                            <p class="mt-2 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        @if ($profilepic)
                            <button type="button" wire:click="updateProfilePicture"
                                class="mt-3 inline-flex items-center justify-center rounded-xl bg-[#D79B10] px-4 sm:px-5 py-3 text-xs sm:text-sm font-semibold text-white transition hover:bg-[#B8830D] w-full sm:w-auto text-center whitespace-nowrap cursor-pointer">
                                Save Profile Picture
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Personal Information Form Card -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-4 sm:px-6 py-4 sm:py-5 text-center sm:text-left">
                    <h3 class="font-semibold text-sm sm:text-base text-slate-900">Personal Details & Emergency Contact</h3>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500">Update your account info and emergency contact details for the ID back side.</p>
                </div>

                <form @submit.prevent="confirmSave">
                    <div class="grid gap-4 sm:gap-5 p-4 sm:p-6 md:grid-cols-2 text-left">
                        <div>
                            <label class="mb-2 block text-xs sm:text-sm font-medium text-slate-700">First Name</label>
                            <input type="text" wire:model="firstname"
                                class="w-full rounded-xl border border-slate-300 px-3.5 sm:px-4 py-2.5 sm:py-3 text-xs sm:text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100">
                            @error('firstname')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-xs sm:text-sm font-medium text-slate-700">Middle Name</label>
                            <input type="text" wire:model="middlename"
                                class="w-full rounded-xl border border-slate-300 px-3.5 sm:px-4 py-2.5 sm:py-3 text-xs sm:text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100">
                            @error('middlename')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-xs sm:text-sm font-medium text-slate-700">Last Name</label>
                            <input type="text" wire:model="lastname"
                                class="w-full rounded-xl border border-slate-300 px-3.5 sm:px-4 py-2.5 sm:py-3 text-xs sm:text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100">
                            @error('lastname')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-xs sm:text-sm font-medium text-slate-700">Birthdate</label>
                            <input type="date" wire:model="birthdate"
                                class="w-full rounded-xl border border-slate-300 px-3.5 sm:px-4 py-2.5 sm:py-3 text-xs sm:text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100">
                            @error('birthdate')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-xs sm:text-sm font-medium text-slate-700">Civil Status</label>
                            <select wire:model.live="civilStatus"
                                class="w-full rounded-xl border border-slate-300 px-3.5 sm:px-4 py-2.5 sm:py-3 text-xs sm:text-sm outline-none bg-white">
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Divorced">Divorced</option>
                            </select>
                            @error('civilStatus')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-xs sm:text-sm font-medium text-slate-700">Sex</label>
                            <select wire:model.live="sex"
                                class="w-full rounded-xl border border-slate-300 px-3.5 sm:px-4 py-2.5 sm:py-3 text-xs sm:text-sm outline-none bg-white">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            @error('sex')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-xs sm:text-sm font-medium text-slate-700">Contact Number</label>
                            <input type="text" wire:model="contactno"
                                class="w-full rounded-xl border border-slate-300 px-3.5 sm:px-4 py-2.5 sm:py-3 text-xs sm:text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100">
                            @error('contactno')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-2 block text-xs sm:text-sm font-medium text-slate-700">Address</label>
                            <textarea wire:model="address" rows="2"
                                class="w-full resize-none rounded-xl border border-slate-300 px-3.5 sm:px-4 py-2.5 sm:py-3 text-xs sm:text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100"></textarea>
                            @error('address')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Emergency Contact Details Section -->
                        <div class="md:col-span-2 pt-4 border-t border-slate-200">
                            <h4 class="font-semibold text-slate-900 text-xs sm:text-sm mb-1">In Case of Emergency (ICE) Details</h4>
                            <p class="text-xs text-slate-500 mb-4">This will sync directly to the back of your ID card preview.</p>
                        </div>

                        <div>
                            <label class="mb-2 block text-xs sm:text-sm font-medium text-slate-700">Emergency Contact Name</label>
                            <input type="text" wire:model="emergencyName"
                                class="w-full rounded-xl border border-slate-300 px-3.5 sm:px-4 py-2.5 sm:py-3 text-xs sm:text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100"
                                placeholder="Full name">
                            @error('emergencyName')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-xs sm:text-sm font-medium text-slate-700">Relationship</label>
                            <input type="text" wire:model="emergencyRelationship"
                                class="w-full rounded-xl border border-slate-300 px-3.5 sm:px-4 py-2.5 sm:py-3 text-xs sm:text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100"
                                placeholder="e.g. Spouse, Parent">
                            @error('emergencyRelationship')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-xs sm:text-sm font-medium text-slate-700">Emergency Contact No.</label>
                            <input type="text" wire:model="emergencyContactno"
                                class="w-full rounded-xl border border-slate-300 px-3.5 sm:px-4 py-2.5 sm:py-3 text-xs sm:text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100"
                                placeholder="Mobile number">
                            @error('emergencyContactno')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-xs sm:text-sm font-medium text-slate-700">Emergency Address</label>
                            <input type="text" wire:model="emergencyAddress"
                                class="w-full rounded-xl border border-slate-300 px-3.5 sm:px-4 py-2.5 sm:py-3 text-xs sm:text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100"
                                placeholder="Address">
                            @error('emergencyAddress')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-center sm:justify-end border-t border-slate-200 bg-slate-50 px-4 sm:px-6 py-4 sm:py-5">
                        <button type="submit" wire:loading.attr="disabled" wire:target="saveProfile"
                            class="rounded-xl bg-[#0C2C6B] px-6 py-3 text-xs sm:text-sm font-semibold text-white transition hover:bg-[#071a42] disabled:opacity-60 w-full sm:w-auto cursor-pointer">
                            <span wire:loading.remove wire:target="saveProfile">Save Personal Information</span>
                            <span wire:loading wire:target="saveProfile">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Side: Signature Pad & Live ID Preview Card Section -->
        <div class="space-y-6 sm:space-y-8">
            <!-- Signature Pad Card -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm p-4 sm:p-6 text-left"
                x-data="signaturePadSetup()" x-init="initPad()">
                <h3 class="font-semibold text-sm sm:text-base text-slate-900 mb-1">Signature</h3>
                <p class="text-xs text-slate-500 mb-4">Draw your signature inside the box below to display on your ID front preview.</p>

                <div class="border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 p-2 relative w-full">
                    <canvas x-ref="signatureCanvas"
                        class="w-full h-32 sm:h-36 bg-white rounded cursor-crosshair touch-none"></canvas>
                </div>

                <div class="flex items-center justify-between mt-3 gap-2">
                    <button type="button" @click="clearPad"
                        class="px-4 py-2 text-xs font-semibold bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg cursor-pointer">
                        🧹 Clear
                    </button>
                    <button type="button" @click="savePad"
                        class="px-5 py-2 text-xs font-semibold bg-[#0C2C6B] hover:bg-[#071a42] text-white rounded-lg cursor-pointer">
                        Save Signature
                    </button>
                </div>
            </div>

            <!-- Live ID Preview Card Section -->
            <div
                class="bg-slate-50 p-4 sm:p-6 rounded-2xl border border-slate-200 flex flex-col items-center justify-between shadow-inner h-fit overflow-x-hidden"
                >
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4 flex items-center gap-1">
                    👁️ Live ID Preview
                </h4>

                <div class="w-full flex justify-center overflow-x-auto py-2">
                    <!-- FRONT SIDE -->
                    <div id="id-card-front"
                        class="w-full min-w-[340px] sm:max-w-[420px] aspect-[1.58/1] bg-white rounded-xl shadow-lg border border-slate-300 p-3 sm:p-4 flex flex-col justify-between relative overflow-hidden text-xs shrink-0">
                        <div class="flex justify-between items-center border-b pb-2">
                            <div class="font-bold text-[#0C2C6B] text-xs sm:text-sm">True Life Dayongan</div>
                            <div class="text-[9px] sm:text-[10px] font-semibold px-2 py-0.5 bg-blue-100 text-blue-800 rounded">
                                PRIMARY MEMBER
                            </div>
                        </div>

                      <div class="flex gap-2 sm:gap-3 my-auto items-center">
                            <div class="w-16 sm:w-20 h-20 sm:h-24 border border-slate-300 bg-slate-100 flex items-center justify-center text-slate-400 text-center text-[9px] shrink-0 overflow-hidden rounded">
                                @if ($profilepic && is_object($profilepic))
                                    <img src="{{ $profilepic->temporaryUrl() }}" class="h-full w-full object-cover">
                                @elseif ($profilePic && $profilePic !== 'images/profiles/default-avatar.png')
                                    <img src="{{ Str::startsWith($profilePic, 'storage/') ? asset($profilePic) : asset('storage/' . $profilePic) }}"
                                        class="h-full w-full object-cover">
                                @else
                                    PHOTO
                                @endif
                            </div>

                            <div class="flex-1 space-y-0.5 text-[9px] sm:text-[10px] text-slate-800 overflow-hidden">
                                <p><strong>ID No.:</strong> UL-{{ str_pad(auth()->id(), 5, '0', STR_PAD_LEFT) }}</p>
                                <p><strong>FULL NAME:</strong>
                                    {{ strtoupper(trim("$firstname $middlename $lastname") ?: 'NAME HERE') }}</p>
                                <p><strong>ADDRESS:</strong> {{ strtoupper($address ?: 'N/A') }}</p>
                                <p><strong>SEX:</strong> {{ strtoupper($sex) }} &nbsp;&nbsp;&nbsp;
                                    <strong>STATUS:</strong> {{ strtoupper($civilStatus) }}
                                </p>
                                <p><strong>BIRTH DATE:</strong>
                                    {{ $birthdate ? \Carbon\Carbon::parse($birthdate)->format('F d, Y') : 'N/A' }}</p>
                                <p><strong>DATE JOINED:</strong>
                                    {{ auth()->user()->created_at?->format('F d, Y') ?? 'N/A' }}</p>
                            </div>

                            <div class="w-14 h-14 sm:w-16 sm:h-16 shrink-0 ml-auto [&>svg]:w-full [&>svg]:h-full">
                                {!! $qrCode !!}
                            </div>
                        </div>

                        <div class="border-t pt-1 flex justify-between items-end">
                            <div class="w-1/3">
                                <p class="text-[8px] sm:text-[9px] font-bold text-slate-500 uppercase">BENEFICIARIES:</p>
                                @forelse($beneficiaries->take(3) as $b)
                                    <p class="text-[8px] sm:text-[9px] truncate text-slate-700">• {{ $b->fullname }}</p>
                                @empty
                                    <p class="text-[8px] sm:text-[9px] text-slate-400">No beneficiaries added</p>
                                @endforelse
                            </div>

                            <div class="flex flex-col items-center justify-end mx-1">
                                <div class="w-20 sm:w-24 h-7 sm:h-8 flex items-center justify-center border-b border-slate-400">
                                    @if ($signature)
                                        <img src="{{ asset('storage/' . $signature) }}" class="max-h-6 sm:max-h-7 object-contain">
                                    @else
                                        <span class="text-[7px] text-slate-400 italic">No Signature</span>
                                    @endif
                                </div>
                                <span class="text-[5px] sm:text-[6px] text-slate-500 tracking-tighter mt-0.5">SIGNATURE</span>
                            </div>


                        </div>
                    </div>

                    <!-- BACK SIDE -->
                    <div id="id-card-back"
                        class="hidden w-full min-w-[340px] sm:max-w-[420px] aspect-[1.58/1] bg-white rounded-xl shadow-lg border border-slate-300 p-3 sm:p-4 flex flex-col justify-between relative overflow-hidden text-[9px] sm:text-[10px] shrink-0">
                        <div>
                            <h5 class="text-center font-bold text-slate-900 tracking-wide text-[10px] sm:text-[11px] mb-1">
                                TRUE LIFE DAYONGAN TERM AND TERMINATION
                            </h5>
                            <p class="text-[7px] sm:text-[8px] font-semibold text-slate-700 mb-1">TRUE LIFE DAYONGAN may terminate this MEMBERSHIP, for the following grounds:</p>
                            <ul class="list-disc list-inside text-[7px] sm:text-[8px] text-slate-600 space-y-0.5">
                                <li>Fraud or misrepresentation</li>
                                <li>Misuse of True Life Dayongan name and materials</li>
                                <li>Violation of company rules or applicable laws</li>
                            </ul>
                            <p class="text-[6px] sm:text-[7px] text-slate-500 mt-1">Termination shall be without prejudice to any liabilities incurred prior to termination.</p>
                        </div>

                        <div class="border-t pt-1 mt-1 flex justify-between items-end">
                            <div class="w-3/5 space-y-0.5">
                                <p class="font-bold text-[8px] sm:text-[9px] text-slate-800">IN CASE OF EMERGENCY:</p>
                                <p class="truncate"><strong>NAME:</strong> {{ strtoupper($emergencyName ?: '____________________') }}</p>
                                <p class="truncate"><strong>ADDRESS:</strong> {{ strtoupper($emergencyAddress ?: '____________________') }}</p>
                                <p class="truncate"><strong>RELATIONSHIP:</strong> {{ strtoupper($emergencyRelationship ?: '____________________') }}</p>
                                <p class="truncate"><strong>CONTACT NO.:</strong> {{ strtoupper($emergencyContactno ?: '____________________') }}</p>
                            </div>
                            <div class="text-center w-2/5 flex flex-col items-center">
                                <div class="font-serif italic text-xs sm:text-sm tracking-tighter text-slate-800">Sey</div>
                                <div class="w-full border-t border-slate-400 mt-0.5"></div>
                                <p class="text-[6px] sm:text-[7px] font-bold text-slate-700 leading-none mt-0.5">REYMARK P. BAUTISTA</p>
                                <p class="text-[5px] sm:text-[6px] text-slate-500">GENERAL MANAGER</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 mt-4">
                    <button type="button" id="btn-front" onclick="switchIdTab('front')"
                        class="px-4 py-1 text-xs font-semibold rounded shadow cursor-pointer bg-[#0C2C6B] text-white">Front</button>
                    <button type="button" id="btn-back" onclick="switchIdTab('back')"
                        class="px-4 py-1 text-xs font-semibold rounded shadow cursor-pointer bg-slate-200 text-slate-700">Back</button>
                </div>

                <div class="flex flex-col sm:flex-row gap-2 w-full mt-4">
                    <button type="button"
                        onclick="window.location.href='{{ route('member.id-card.pdf') }}'"
                        class="flex-1 rounded-xl bg-slate-200 py-3 text-xs font-semibold cursor-pointer">
                        📥 Download ID (PDF)
                    </button>
                    {{-- <button type="button" wire:click="requestPhysicalId"
                        class="flex-1 rounded-xl bg-[#0C2C6B] py-3 text-xs font-semibold text-white shadow transition hover:bg-[#071a42] cursor-pointer">
                        Request Physical ID
                    </button> --}}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>


<script>
 function profileForm(initialValues) {
        return {
            initial: initialValues,

            async confirmSave() {
                const current = {
                    'First Name': await @this.get('firstname'),
                    'Last Name': await @this.get('lastname'),
                    'Middle Name': await @this.get('middlename'),
                    'Birthdate': await @this.get('birthdate'),
                    'Contact No': await @this.get('contactno'),
                    'Address': await @this.get('address'),
                    'Civil Status': await @this.get('civilStatus'),
                    'Sex': await @this.get('sex'),
                    'Emergency Name': await @this.get('emergencyName'),
                    'Emergency Relationship': await @this.get('emergencyRelationship'),
                    'Emergency Contact No': await @this.get('emergencyContactno'),
                    'Emergency Address': await @this.get('emergencyAddress'),
                };

                const keysMap = {
                    'First Name': 'firstname',
                    'Last Name': 'lastname',
                    'Middle Name': 'middlename',
                    'Birthdate': 'birthdate',
                    'Contact No': 'contactno',
                    'Address': 'address',
                    'Civil Status': 'civilStatus',
                    'Sex': 'sex',
                    'Emergency Name': 'emergencyName',
                    'Emergency Relationship': 'emergencyRelationship',
                    'Emergency Contact No': 'emergencyContactno',
                    'Emergency Address': 'emergencyAddress',
                };

                let changes = [];
                for (const [label, key] of Object.entries(keysMap)) {
                    const oldVal = String(this.initial[key] || '').trim();
                    const newVal = String(current[label] || '').trim();
                    if (oldVal !== newVal) {
                        changes.push(`<li><b>${label}:</b> "${oldVal || 'None'}" ➔ "${newVal || 'None'}"</li>`);
                    }
                }

                // If NOTHING changed, show an info modal and STOP here
                if (changes.length === 0) {
                    Swal.fire({
                        title: 'No Changes Detected',
                        text: 'You haven’t modified any profile details.',
                        icon: 'info',
                        confirmButtonColor: '#0C2C6B',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                    return; // Exit function so it doesn't try to save
                }

                // If changes WERE found, show confirmation modal
                let htmlContent = `
                    <div style="text-align: left; max-height: 220px; overflow-y: auto; font-size: 13px;" class="mt-2 space-y-1">
                        <p class="font-semibold text-slate-800 mb-2">Detected Modifications (${changes.length}):</p>
                        <ul style="list-style-type: disc; padding-left: 20px;" class="space-y-1 text-slate-600">
                            ${changes.join('')}
                        </ul>
                    </div>
                `;

                Swal.fire({
                    title: 'Confirm Profile Update',
                    html: htmlContent,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0C2C6B',
                    cancelButtonColor: '#D79B10',
                    confirmButtonText: 'Yes, Save Changes',
                    cancelButtonText: 'Cancel',
                    width: '520px',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('saveProfile');
                    }
                });
            }
        }
    }

    document.addEventListener('livewire:initialized', () => {
    // Listener for Profile Details Update
    Livewire.on('profile-details-updated', () => {
        Swal.fire({
            title: 'Updated Successfully!',
            text: 'Your personal and emergency contact information has been updated.',
            icon: 'success',
            confirmButtonColor: '#0C2C6B',
            confirmButtonText: 'OK',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.reload();
            }
        });
    });

    // Listener for Profile Picture Update (Add this block)
    Livewire.on('picture-updated', () => {
        Swal.fire({
            title: 'Picture Updated!',
            text: 'Your profile picture has been updated successfully.',
            icon: 'success',
            confirmButtonColor: '#0C2C6B',
            confirmButtonText: 'OK',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.reload();
            }
        });
    });
});


    function switchIdTab(target) {
        const frontEl = document.getElementById('id-card-front');
        const backEl = document.getElementById('id-card-back');
        const btnFront = document.getElementById('btn-front');
        const btnBack = document.getElementById('btn-back');

        if (target === 'front') {
            frontEl.classList.remove('hidden');
            backEl.classList.add('hidden');
            btnFront.className = "px-4 py-1 text-xs font-semibold rounded shadow cursor-pointer bg-[#0C2C6B] text-white";
            btnBack.className = "px-4 py-1 text-xs font-semibold rounded shadow cursor-pointer bg-slate-200 text-slate-700";
        } else {
            backEl.classList.remove('hidden');
            frontEl.classList.add('hidden');
            btnBack.className = "px-4 py-1 text-xs font-semibold rounded shadow cursor-pointer bg-[#0C2C6B] text-white";
            btnFront.className = "px-4 py-1 text-xs font-semibold rounded shadow cursor-pointer bg-slate-200 text-slate-700";
        }
    }

    function signaturePadSetup() {
        return {
            pad: null,
            initPad() {
                const canvas = this.$refs.signatureCanvas;
                canvas.width = canvas.offsetWidth;
                canvas.height = canvas.offsetHeight;
                this.pad = new SignaturePad(canvas);

                @if ($signature)
                    this.pad.fromDataURL("{{ Storage::url($signature) }}");
                @endif
            },
            clearPad() {
                this.pad.clear();
                @this.call('removeSignature');
            },
            savePad() {
                if (this.pad.isEmpty()) {
                    Swal.fire({
                        title: 'No Signature',
                        text: 'Please provide a signature first.',
                        icon: 'warning',
                        confirmButtonColor: '#0C2C6B'
                    });
                    return;
                }

                const dataUrl = this.pad.toDataURL();

                Swal.fire({
                    title: 'Save Signature?',
                    text: 'Your signature will be saved to your profile.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0B2A5B',
                    cancelButtonColor: '#D79B10',
                    confirmButtonText: 'Yes, Save it',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('saveSignature', dataUrl);
                    }
                });
            }
        }
    }

     document.addEventListener('alpine:init', () => {

        Alpine.data('idPdfDownload', () => ({
            async downloadPdf() {

                const pdfTemplate = document.getElementById('pdf-membership-card');

                if (!pdfTemplate) {
                    console.error(
                        'idPdfDownload: #pdf-membership-card was not found.'
                    );

                    Swal.fire({
                        title: 'Download Failed',
                        text: 'PDF card template was not found.',
                        icon: 'error',
                        confirmButtonColor: '#0C2C6B'
                    });

                    return;
                }

                if (typeof html2pdf === 'undefined') {
                    console.error(
                        'idPdfDownload: html2pdf library did not load.'
                    );

                    Swal.fire({
                        title: 'Download Failed',
                        text: 'PDF generator did not load. Please refresh the page and try again.',
                        icon: 'error',
                        confirmButtonColor: '#0C2C6B'
                    });

                    return;
                }

                let container = null;

                try {

                    /*
                    |--------------------------------------------------------------------------
                    | CREATE PDF CONTAINER
                    |--------------------------------------------------------------------------
                    */

                    container = document.createElement('div');

                    container.id = 'pdf-download-container';

                    container.style.position = 'fixed';
                    container.style.left = '-100000px';
                    container.style.top = '0';
                    container.style.width = '760px';
                    container.style.background = '#ffffff';
                    container.style.padding = '10px';
                    container.style.margin = '0';
                    container.style.zIndex = '-9999';

                    /*
                    |--------------------------------------------------------------------------
                    | CLONE PLAIN HTML CARD
                    |--------------------------------------------------------------------------
                    |
                    | IMPORTANT:
                    | We clone #pdf-membership-card instead of the Tailwind
                    | #id-card-front / #id-card-back.
                    |
                    | Your PDF template uses normal HEX/RGBA colors only.
                    |
                    */

                    const clone = pdfTemplate.cloneNode(true);

                    /*
                    |--------------------------------------------------------------------------
                    | REMOVE HIDDEN ATTRIBUTE / DISPLAY NONE
                    |--------------------------------------------------------------------------
                    */

                    clone.removeAttribute('hidden');

                    clone.style.display = 'block';
                    clone.style.visibility = 'visible';
                    clone.style.position = 'static';
                    clone.style.width = '100%';
                    clone.style.background = '#ffffff';

                    /*
                    |--------------------------------------------------------------------------
                    | MAKE SURE ALL ELEMENTS ARE VISIBLE
                    |--------------------------------------------------------------------------
                    */

                    clone.querySelectorAll('*').forEach(element => {

                        element.style.removeProperty('display');

                        element.style.removeProperty('visibility');

                    });

                    /*
                    |--------------------------------------------------------------------------
                    | APPEND TO BODY
                    |--------------------------------------------------------------------------
                    */

                    container.appendChild(clone);

                    document.body.appendChild(container);

                    /*
                    |--------------------------------------------------------------------------
                    | WAIT FOR IMAGES
                    |--------------------------------------------------------------------------
                    */

                    await this.waitForImages(clone);

                    /*
                    |--------------------------------------------------------------------------
                    | PDF OPTIONS
                    |--------------------------------------------------------------------------
                    */

                    const options = {
                        margin: 10,

                        filename: 'member-id-card.pdf',

                        image: {
                            type: 'jpeg',
                            quality: 0.98
                        },

                        html2canvas: {
                            scale: 2,

                            useCORS: true,

                            allowTaint: false,

                            backgroundColor: '#ffffff',

                            logging: false,

                            imageTimeout: 15000
                        },

                        jsPDF: {
                            unit: 'mm',
                            format: 'a4',
                            orientation: 'portrait'
                        },

                        pagebreak: {
                            mode: ['css', 'legacy']
                        }
                    };

                    /*
                    |--------------------------------------------------------------------------
                    | GENERATE PDF
                    |--------------------------------------------------------------------------
                    */

                    await html2pdf()
                        .set(options)
                        .from(clone)
                        .save();

                    console.log(
                        'idPdfDownload: PDF generated successfully.'
                    );

                } catch (error) {

                    console.error(
                        'idPdfDownload: PDF generation failed',
                        error
                    );

                    Swal.fire({
                        title: 'Download Failed',
                        text: 'Something went wrong while generating your ID card PDF.',
                        icon: 'error',
                        confirmButtonColor: '#0C2C6B'
                    });

                } finally {

                    /*
                    |--------------------------------------------------------------------------
                    | CLEANUP
                    |--------------------------------------------------------------------------
                    */

                    if (container && container.parentNode) {
                        container.remove();
                    }

                }
            },

            /*
            |--------------------------------------------------------------------------
            | WAIT FOR ALL IMAGES
            |--------------------------------------------------------------------------
            */

            waitForImages(root) {

                const images = Array.from(
                    root.querySelectorAll('img')
                );

                if (images.length === 0) {
                    return Promise.resolve();
                }

                return Promise.all(
                    images.map(img => {

                        if (img.complete) {
                            return Promise.resolve();
                        }

                        return new Promise(resolve => {

                            img.addEventListener(
                                'load',
                                resolve,
                                { once: true }
                            );

                            img.addEventListener(
                                'error',
                                resolve,
                                { once: true }
                            );

                        });

                    })
                );
            }
        }));

    });
</script>
