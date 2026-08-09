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
            <svg xmlns="http://www.w3.org/2000/svg" class="alert-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="alert-text">{{ session('profile-success') }}</p>
        </div>
    @endif

    @if (session()->has('picture-success'))
        <div class="alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" class="alert-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="alert-text">{{ session('picture-success') }}</p>
        </div>
    @endif

    <div class="profile-grid">

        {{-- =================== LEFT COLUMN =================== --}}
        <div class="profile-col">

            {{-- --------------- 3. PROFILE PICTURE CARD --------------- --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profile Picture</h3>
                    <p class="card-subtitle">Upload a clear profile picture. Maximum file size is 2 MB.</p>
                </div>

                <div class="avatar-section">
                    <div class="avatar-frame">
                        @if ($profilepic)
                            <img src="{{ $profilepic->temporaryUrl() }}" class="avatar-img" alt="New profile picture preview">
                        @else
                            @php
                                $profilePic = auth()->user()->profilepic;
                            @endphp

                            @if (!$profilePic || $profilePic === 'images/profiles/default-avatar.png')
                                <img src="{{ asset('images/profiles/default-avatar.png') }}" class="avatar-img" alt="Default profile picture">
                            @else
                                <img src="{{ Storage::url($profilePic) }}" class="avatar-img" alt="Profile picture">
                            @endif
                        @endif
                    </div>

                    <div class="avatar-actions">
                        <div class="avatar-buttons-row">
                            <label for="profilepic" class="btn-primary">
                                Choose New Picture
                            </label>
                            <input id="profilepic" type="file" wire:model="profilepic" accept="image/*" class="hidden">

                            @if (!$profilepic && auth()->user()->profilepic && auth()->user()->profilepic !== 'images/profiles/default-avatar.png')
                                <button type="button" wire:click="removeProfilePicture"
                                    wire:confirm="Are you sure you want to remove your profile picture?"
                                    class="btn-warning">
                                    Remove Picture
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

            {{-- ------ 4. PERSONAL DETAILS + EMERGENCY CONTACT FORM ------ --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Personal Details & Emergency Contact</h3>
                    <p class="card-subtitle">Update your account info and emergency contact details for the ID back side.</p>
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

                        {{-- Emergency Contact Details Section --}}
                        <div class="form-section-divider">
                            <h4 class="form-section-title">In Case of Emergency (ICE) Details</h4>
                            <p class="form-section-desc">This will sync directly to the back of your ID card preview.</p>
                        </div>

                        <div class="form-field">
                            <label class="form-label">Emergency Contact Name</label>
                            <input type="text" wire:model="emergencyName" class="form-input" placeholder="Full name">
                            @error('emergencyName')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Relationship</label>
                            <input type="text" wire:model="emergencyRelationship" class="form-input" placeholder="e.g. Spouse, Parent">
                            @error('emergencyRelationship')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Emergency Contact No.</label>
                            <input type="text" wire:model="emergencyContactno" class="form-input" placeholder="Mobile number">
                            @error('emergencyContactno')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label class="form-label">Emergency Address</label>
                            <input type="text" wire:model="emergencyAddress" class="form-input" placeholder="Address">
                            @error('emergencyAddress')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" wire:loading.attr="disabled" wire:target="saveProfile" class="btn-submit">
                            <span wire:loading.remove wire:target="saveProfile">Save Personal Information</span>
                            <span wire:loading wire:target="saveProfile">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- =================== RIGHT COLUMN =================== --}}
        <div class="profile-col">

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
                                            <td class="table-cell-primary">{{ $beneficiary->fullname ?? 'N/A' }}</td>
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

            {{-- --------------- 6. SIGNATURE PAD --------------- --}}
            <div class="signature-card" x-data="signaturePadSetup()" x-init="initPad()">
                <h3 class="signature-title">Signature</h3>
                <p class="signature-desc">Draw your signature inside the box below to display on your ID front preview.</p>

                <div class="signature-pad-frame">
                    <canvas x-ref="signatureCanvas" class="signature-canvas"></canvas>
                </div>

                <div class="signature-actions">
                    <button type="button" @click="clearPad" class="btn-clear">
                        🧹 Clear
                    </button>
                    <button type="button" @click="savePad" class="btn-save">
                        Save Signature
                    </button>
                </div>
            </div>

            {{-- --------------- 7. LIVE ID PREVIEW --------------- --}}
            <div class="id-preview-card">
                <h4 class="id-preview-title">👁️ Live ID Preview</h4>

                <div class="id-card-viewport">

                    {{-- FRONT SIDE --}}
                    <div id="id-card-front" class="id-card id-card-front">
                        <div class="id-card-top">
                            <div class="id-org-name">True Life Dayongan</div>
                            <div class="id-badge">PRIMARY MEMBER</div>
                        </div>

                        <div class="id-card-mid">
                            <div class="id-photo-box">
                                @if ($profilepic && is_object($profilepic))
                                    <img src="{{ $profilepic->temporaryUrl() }}" class="avatar-img">
                                @elseif ($profilePic && $profilePic !== 'images/profiles/default-avatar.png')
                                    <img src="{{ Str::startsWith($profilePic, 'storage/') ? asset($profilePic) : asset('storage/' . $profilePic) }}" class="avatar-img">
                                @else
                                    PHOTO
                                @endif
                            </div>

                            <div class="id-details">
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

                            <div class="id-qr">
                                {!! $qrCode !!}
                            </div>
                        </div>

                        <div class="id-card-bottom">
                            <div class="id-beneficiaries">
                                <p class="id-beneficiaries-title">BENEFICIARIES:</p>
                                @forelse($beneficiaries->take(3) as $b)
                                    <p class="id-beneficiary-line">• {{ $b->fullname }}</p>
                                @empty
                                    <p class="id-beneficiary-empty">No beneficiaries added</p>
                                @endforelse
                            </div>

                            <div class="id-signature-block">
                                <div class="id-signature-line">
                                    @if ($signature)
                                        <img src="{{ asset('storage/' . $signature) }}" class="id-signature-img">
                                    @else
                                        <span class="id-signature-empty">No Signature</span>
                                    @endif
                                </div>
                                <span class="id-signature-label">SIGNATURE</span>
                            </div>
                        </div>
                    </div>

                    {{-- BACK SIDE --}}
                    <div id="id-card-back" class="id-card id-card-back hidden">
                        <div>
                            <h5 class="id-back-title">TRUE LIFE DAYONGAN TERM AND TERMINATION</h5>
                            <p class="id-back-text">TRUE LIFE DAYONGAN may terminate this MEMBERSHIP, for the following grounds:</p>
                            <ul class="id-back-list">
                                <li>Fraud or misrepresentation</li>
                                <li>Misuse of True Life Dayongan name and materials</li>
                                <li>Violation of company rules or applicable laws</li>
                            </ul>
                            <p class="id-back-note">Termination shall be without prejudice to any liabilities incurred prior to termination.</p>
                        </div>

                        <div class="id-back-bottom">
                            <div class="id-ice-block">
                                <p class="id-ice-title">IN CASE OF EMERGENCY:</p>
                                <p class="id-ice-line"><strong>NAME:</strong> {{ strtoupper($emergencyName ?: '____________________') }}</p>
                                <p class="id-ice-line"><strong>ADDRESS:</strong> {{ strtoupper($emergencyAddress ?: '____________________') }}</p>
                                <p class="id-ice-line"><strong>RELATIONSHIP:</strong> {{ strtoupper($emergencyRelationship ?: '____________________') }}</p>
                                <p class="id-ice-line"><strong>CONTACT NO.:</strong> {{ strtoupper($emergencyContactno ?: '____________________') }}</p>
                            </div>
                            <div class="id-signatory-block">
                                <div class="id-signatory-script">Sey</div>
                                <div class="id-signatory-divider"></div>
                                <p class="id-signatory-name">REYMARK P. BAUTISTA</p>
                                <p class="id-signatory-role">GENERAL MANAGER</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="id-tabs">
                    <button type="button" id="btn-front" onclick="switchIdTab('front')" class="id-tab id-tab--active">Front</button>
                    <button type="button" id="btn-back" onclick="switchIdTab('back')" class="id-tab id-tab--inactive">Back</button>
                </div>

                <div class="id-actions">
                    <button type="button" onclick="window.location.href='{{ route('member.id-card.pdf') }}'" class="btn-download">
                        📥 Download ID (PDF)
                    </button>
                    {{-- <button type="button" wire:click="requestPhysicalId" class="btn-primary flex-1">
                        Request Physical ID
                    </button> --}}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================= 8. SCRIPTS ============================= --}}
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

        // Listener for Profile Picture Update
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
            btnFront.className = 'id-tab id-tab--active';
            btnBack.className = 'id-tab id-tab--inactive';
        } else {
            backEl.classList.remove('hidden');
            frontEl.classList.add('hidden');
            btnBack.className = 'id-tab id-tab--active';
            btnFront.className = 'id-tab id-tab--inactive';
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
                    console.error('idPdfDownload: #pdf-membership-card was not found.');

                    Swal.fire({
                        title: 'Download Failed',
                        text: 'PDF card template was not found.',
                        icon: 'error',
                        confirmButtonColor: '#0C2C6B'
                    });

                    return;
                }

                if (typeof html2pdf === 'undefined') {
                    console.error('idPdfDownload: html2pdf library did not load.');

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
                    // ---------------------------------------------------------
                    // Create PDF container
                    // ---------------------------------------------------------
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

                    // ---------------------------------------------------------
                    // Clone plain-HTML card (uses hex/rgba colors only, no
                    // Tailwind classes, so html2canvas renders it reliably)
                    // ---------------------------------------------------------
                    const clone = pdfTemplate.cloneNode(true);

                    clone.removeAttribute('hidden');
                    clone.style.display = 'block';
                    clone.style.visibility = 'visible';
                    clone.style.position = 'static';
                    clone.style.width = '100%';
                    clone.style.background = '#ffffff';

                    clone.querySelectorAll('*').forEach(element => {
                        element.style.removeProperty('display');
                        element.style.removeProperty('visibility');
                    });

                    container.appendChild(clone);
                    document.body.appendChild(container);

                    // ---------------------------------------------------------
                    // Wait for images, then generate the PDF
                    // ---------------------------------------------------------
                    await this.waitForImages(clone);

                    const options = {
                        margin: 10,
                        filename: 'member-id-card.pdf',
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: {
                            scale: 2,
                            useCORS: true,
                            allowTaint: false,
                            backgroundColor: '#ffffff',
                            logging: false,
                            imageTimeout: 15000
                        },
                        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                        pagebreak: { mode: ['css', 'legacy'] }
                    };

                    await html2pdf().set(options).from(clone).save();

                    console.log('idPdfDownload: PDF generated successfully.');

                } catch (error) {
                    console.error('idPdfDownload: PDF generation failed', error);

                    Swal.fire({
                        title: 'Download Failed',
                        text: 'Something went wrong while generating your ID card PDF.',
                        icon: 'error',
                        confirmButtonColor: '#0C2C6B'
                    });

                } finally {
                    if (container && container.parentNode) {
                        container.remove();
                    }
                }
            },

            // Waits for all <img> tags inside `root` to finish loading.
            waitForImages(root) {
                const images = Array.from(root.querySelectorAll('img'));

                if (images.length === 0) {
                    return Promise.resolve();
                }

                return Promise.all(
                    images.map(img => {
                        if (img.complete) {
                            return Promise.resolve();
                        }

                        return new Promise(resolve => {
                            img.addEventListener('load', resolve, { once: true });
                            img.addEventListener('error', resolve, { once: true });
                        });
                    })
                );
            }
        }));

    });
</script>
