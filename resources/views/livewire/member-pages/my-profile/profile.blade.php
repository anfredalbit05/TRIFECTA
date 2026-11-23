@php
    use Illuminate\Support\Str;
@endphp

{{--
=====================================================================
 PERSONAL INFORMATION & ID CARD PAGE
=====================================================================
 Styling lives in profile.css (imported in your Tailwind build).
 This file only references semantic classes — no utility soup.
 Sections:
   1. Header
   2. Success alerts
   3. Profile picture card
   4. Personal details + emergency contact form
   5. Registered beneficiaries (read-only)
   6. Signature pad
   7. Live ID preview (front / back) + actions
   8. Scripts (Alpine components + PDF export)
=====================================================================
--}}

<div class="profile-page" x-data="profileForm({
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
})">

    {{-- ============================= 1. HEADER ============================= --}}
    <div class="profile-header">
        <div>
            <h2 class="profile-header-title">Personal Information & ID Card</h2>
            <p class="profile-header-subtitle">
                Manage your personal details, profile picture, emergency contacts, signature, and ID card.
            </p>
        </div>
    </div>

    {{-- ========================= 2. SUCCESS ALERTS ========================= --}}
    @if (session()->has('profile-success'))
        <div class="alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" class="alert-icon" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="alert-text">{{ session('profile-success') }}</p>
        </div>
    @endif

    @if (session()->has('picture-success'))
        <div class="alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" class="alert-icon" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="alert-text">{{ session('picture-success') }}</p>
        </div>
    @endif

    <div class="profile-grid">

        {{-- =================== LEFT COLUMN =================== --}}
        <div class="profile-col">

            {{-- ------ 4. PERSONAL DETAILS + EMERGENCY CONTACT FORM ------ --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Personal Details & Emergency Contact</h3>
                    <p class="card-subtitle">Update your account info and emergency contact details for the ID back
                        side.</p>
                </div>

                <form @submit.prevent="confirmSave">
                    <div class="form-grid">

                        <div class="form-field">
                            <label class="form-label">First Name</label>
                            <input type="text" wire:model="firstname" class="form-input">
                            @error('firstname')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Middle Name</label>
                            <input type="text" wire:model="middlename" class="form-input">
                            @error('middlename')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Last Name</label>
                            <input type="text" wire:model="lastname" class="form-input">
                            @error('lastname')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Birthdate</label>
                            <input type="date" wire:model="birthdate" class="form-input">
                            @error('birthdate')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Civil Status</label>
                            <select wire:model.live="civilStatus" class="form-select">
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Divorced">Divorced</option>
                            </select>
                            @error('civilStatus')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Sex</label>
                            <select wire:model.live="sex" class="form-select">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            @error('sex')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Contact Number</label>
                            <input type="text" wire:model="contactno" class="form-input">
                            @error('contactno')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field form-field-full">
                            <label class="form-label">Address</label>
                            <textarea wire:model="address" rows="2" class="form-textarea"></textarea>
                            @error('address')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        @if (auth()->user()->RoleID != 8)
                            {{-- Emergency Contact Details Section --}}
                            <div class="form-section-divider">
                                <h4 class="form-section-title">In Case of Emergency (ICE) Details</h4>
                                <p class="form-section-desc">This will sync directly to the back of your ID card
                                    preview.</p>
                            </div>

                            <div class="form-field">
                                <label class="form-label">Emergency Contact Name</label>
                                <input type="text" wire:model="emergencyName" class="form-input"
                                    placeholder="Full name">
                                @error('emergencyName')
                                    <p class="field-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-field">
                                <label class="form-label">Relationship</label>
                                <input type="text" wire:model="emergencyRelationship" class="form-input"
                                    placeholder="e.g. Spouse, Parent">
                                @error('emergencyRelationship')
                                    <p class="field-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-field">
                                <label class="form-label">Emergency Contact No.</label>
                                <input type="text" wire:model="emergencyContactno" class="form-input"
                                    placeholder="Mobile number">
                                @error('emergencyContactno')
                                    <p class="field-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-field">
                                <label class="form-label">Emergency Address</label>
                                <input type="text" wire:model="emergencyAddress" class="form-input"
                                    placeholder="Address">
                                @error('emergencyAddress')
                                    <p class="field-error">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <button type="submit" wire:loading.attr="disabled" wire:target="saveProfile"
                            class="btn-submit">
                            <span wire:loading.remove wire:target="saveProfile">Save Personal Information</span>
                            <span wire:loading wire:target="saveProfile">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- =================== RIGHT COLUMN =================== --}}
        <div class="profile-col">

             @if (auth()->user()->RoleID != 8)
            {{-- --------------- 3. PROFILE PICTURE CARD --------------- --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profile Picture</h3>
                    <p class="card-subtitle">Upload a clear profile picture. Maximum file size is 2 MB.</p>
                </div>

                <div class="avatar-section">
                    <div class="avatar-frame">
                        @if ($profilepic)
                            <img src="{{ $profilepic->temporaryUrl() }}" class="avatar-img"
                                alt="New profile picture preview">
                        @else
                            @php
                                $profilePic = auth()->user()->profilepic;
                            @endphp

                            @if (!$profilePic || $profilePic === 'images/profiles/default-avatar.png')
                                <img src="{{ asset('images/profiles/default-avatar.png') }}" class="avatar-img"
                                    alt="Default profile picture">
                            @else
                                <img
                                    src="{{ route('profile.picture', [
                                        'v' => auth()->id() . '-' . md5(auth()->user()->profilepic ?? 'default'),
                                    ]) }}"
                                    class="avatar-img"
                                    alt="Profile picture"
                                >
                            @endif
                        @endif
                    </div>

                    <div class="avatar-actions">
                        <div class="avatar-buttons-row">
                            <label for="profilepic" class="btn-primary">
                                Choose Profile Picture
                            </label>
                            <input id="profilepic" type="file" wire:model="profilepic" accept="image/jpeg,image/jpg"
                                class="hidden">

                            @if (!$profilepic && auth()->user()->profilepic && auth()->user()->profilepic !== 'images/profiles/default-avatar.png')
                            <button type="button"
                                wire:click="removeProfilePicture"
                                wire:confirm="Are you sure you want to remove your profile picture?"
                                class="btn-danger">
                                Remove Profile Picture
                            </button>
                            @endif
                        </div>

                        @error('profilepic')
                            <p class="field-error">{{ $message }}</p>
                        @enderror

                        @if ($profilepic)
                            <button type="button" wire:click="updateProfilePicture" class="btn-secondary mt-3">
                                Save Profile Picture
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- //Hello --}}
            @if (auth()->user()->RoleID == 8)
                {{-- --------------- 5. REGISTERED BENEFICIARIES (READ-ONLY) --------------- --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Registered Beneficiaries</h3>
                    </div>

                    <div class="card-body">
                        @if (count($beneficiaries) > 0)
                            <div class="table-wrap">
                                <table class="table">
                                    <thead>
                                        <tr class="table-head-row">
                                            <th class="table-head-cell">Full Name</th>
                                            <th class="table-head-cell">Birthdate</th>
                                            <th class="table-head-cell">Relationship</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-body">
                                        @foreach ($beneficiaries as $beneficiary)
                                            <tr>
                                                <td class="table-cell-primary">{{ $beneficiary->fullname ?? 'N/A' }}
                                                </td>
                                                <td class="table-cell">
                                                    {{ $beneficiary->birthdate ? \Carbon\Carbon::parse($beneficiary->birthdate)->format('M d, Y') : 'N/A' }}
                                                </td>
                                                <td class="table-cell">{{ $beneficiary->relationship ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="table-empty">No beneficiaries registered.</p>
                        @endif
                    </div>
                </div>
            @endif


            {{-- --------------- 6. SIGNATURE PAD --------------- --}}
            {{-- <div class="signature-card" x-data="signaturePadSetup($el.dataset.signatureUrl || null)"
             data-signature-url="{{ $signature ? Storage::url($signature) : '' }}" x-init="initPad()">
                <h3 class="signature-title">Signature</h3>

                <p class="signature-desc">
                    Draw your signature inside the box below to display on your ID front preview.
                </p>

                <div class="signature-pad-frame">
                    <canvas x-ref="signatureCanvas" class="signature-canvas"></canvas>
                </div>

                <div class="signature-actions">

                DELETE
                <button type="button"
                    @click="deleteSignature"
                    class="btn-warning">
                    Delete Signature
                </button>

                CLEAR + SAVE
                <div class="signature-actions-right">

                    <button type="button"
                        @click="clearPad"
                        class="btn-clear">
                        🧹 Clear
                    </button>

                    <button type="button"
                        @click="savePad"
                        class="btn-save">
                        Save Signature
                    </button>

                </div>

            </div>
            </div> --}}


            @if (auth()->user()->RoleID == 8)
                {{-- --------------- 7. LIVE ID PREVIEW --------------- --}}
            <div class="id-preview-card">
                <h4 class="id-preview-title">👁️ Live ID Preview</h4>

                <div class="id-card-viewport">

                    {{-- FRONT SIDE --}}
                    <div id="id-card-front" class="id-card-side id-card-front-new">

                        <img src="{{ asset('images/certificate/trifecta-id.jpg') }}"
                            class="id-template-img"
                            alt="TRIFECTA Membership ID Front">

                        {{-- Member Photo --}}
                        {{-- <div class="idn-photo">
                            @if ($profilepic && is_object($profilepic))
                                <img src="{{ $profilepic->temporaryUrl() }}" alt="Member Photo">
                            @elseif (auth()->user()->profilepic &&
                                auth()->user()->profilepic !== 'images/profiles/default-avatar.png')
                                <img src="{{ asset('storage/' . auth()->user()->profilepic) }}"
                                    alt="Member Photo">
                            @endif
                        </div> --}}

                        {{-- Dynamic Member Information — sits on the printed
                             labels of the template, one value per line. --}}
                        <div class="idn-field idn-name">
                            {{ strtoupper(trim("$firstname " . ($middlename ? substr(trim($middlename), 0, 1) . '. ' : '') . $lastname)) }}
                        </div>

                        <div class="idn-field idn-membership">
                            {{ $membershipNo }}
                        </div>

                        <div class="idn-field idn-issue">
                            {{ $dateOfIssue }}
                        </div>

                        <div class="idn-field idn-valid">
                            {{ $validUntil }}
                        </div>

                        {{-- Referral QR — encodes the referral link, so
                             scanning it opens the join page; clicking it
                             opens the same link in a new tab. --}}
                        <a class="idn-qr" href="{{ $referralUrl }}" target="_blank" rel="noopener"
                            title="Open your referral / join link">
                            {!! $qrCode !!}
                        </a>

                    </div>

                    {{-- BACK SIDE --}}
                    <div id="id-card-back" class="id-card-side id-card-back-new hidden">

                        <img src="{{ asset('images/certificate/trifecta-id.png') }}"
                            class="id-template-img"
                            alt="TRIFECTA Membership ID Back">

                    </div>

                </div>

                {{-- FRONT / BACK TABS --}}
                {{-- <div class="id-tabs">
                    <button type="button"
                            id="btn-front"
                            onclick="switchIdTab('front')"
                            class="id-tab id-tab--active">
                        Front
                    </button>

                    <button type="button"
                            id="btn-back"
                            onclick="switchIdTab('back')"
                            class="id-tab id-tab--inactive">
                        Back
                    </button>
                </div> --}}

                {{-- ACTIONS --}}
                <div class="id-actions flex justify-center items-center gap-4 flex-wrap">

                    <a href="{{ route('member.id-card.download') }}"
                    class="btn-submit">
                        Download ID
                    </a>

                    {{-- <button type="button"
                            wire:click="requestPhysicalId"
                            class="btn-download text-center">
                        Request Physical ID
                    </button> --}}

                </div>
            </div>

            @endif
        </div>
    </div>

</div>
