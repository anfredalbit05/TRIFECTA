{{--
=====================================================================
 ACCOUNT INFORMATION PAGE
=====================================================================
 Styling lives in profile.css (section 10 — reuses shared classes
 from the card / form / alert sections defined earlier in that
 file). This file only references semantic classes.
 Sections:
   1. Header
   2. Login details form (username / email)
   3. Account details (status + referral code)
   4. Referral QR & link
   5. Toast notification
   6. Scripts
=====================================================================
--}}

<div class="account-page" x-data="accountForm()">

    {{-- ============================= 1. HEADER ============================= --}}
    <div>
        <h2 class="profile-header-title">Account Information</h2>
        <p class="profile-header-subtitle">
            Manage your username, email address, and account details.
        </p>
    </div>

    {{-- ==================== 2. LOGIN DETAILS FORM ==================== --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Login Details</h3>
            <p class="card-subtitle">These details are used to access your account.</p>
        </div>

        {{-- Intercept submit with SweetAlert confirmation --}}
        <form @submit.prevent="confirmSave">
            <div class="form-grid">
                {{-- Username --}}
                <div class="form-field">
                    <label class="form-label">Username</label>
                    <input type="text" wire:model="username" autocomplete="username" class="form-input">
                    @error('username')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="form-field">
                    <label class="form-label">Email Address</label>
                    <input type="email" wire:model="email" autocomplete="email" class="form-input">
                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" wire:loading.attr="disabled" wire:target="save" class="btn-submit">
                    <span wire:loading.remove wire:target="save">Save Account Information</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </form>
    </div>

    @if(auth()->user()->RoleID != 9)
    {{-- ================= 3. READ-ONLY ACCOUNT DETAILS ================= --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Account Details</h3>
            <p class="card-subtitle">Information managed by the system.</p>
        </div>

        <div class="grid divide-y divide-slate-200 md:grid-cols-3 md:divide-x md:divide-y-0">
    {{-- Status --}}
    <div class="account-detail-cell">
        <p class="account-detail-label">Account Status</p>
        <div class="mt-3">
            @if (auth()->user()->RoleID == 1)
                <select wire:model="status" class="status-select">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            @else
                <span @class([
                    'status-badge',
                    'status-badge--active' => auth()->user()->status === 'Active',
                    'status-badge--inactive' => auth()->user()->status === 'Inactive',
                    'status-badge--suspended' => auth()->user()->status === 'Suspended',
                ])>
                    {{ auth()->user()->status }}
                </span>
            @endif
        </div>
    </div>

    {{-- Referral Code --}}
    <div class="account-detail-cell">
        <p class="account-detail-label">Referral Code</p>
        <p class="account-detail-value">{{ auth()->user()->referralCode ?? 'Not available' }}</p>
    </div>

    {{-- Policy Number --}}
    <div class="account-detail-cell">
        <p class="account-detail-label">Policy Number</p>
        <p class="account-detail-value">{{ auth()->user()->policy_number ?? 'Not available' }}</p>
    </div>
</div>

        {{-- ==================== 4. REFERRAL QR & LINK ==================== --}}
        <div class="referral-section">
            <div class="referral-wrap">
                <div class="qr-frame">
                    {!! $qrCode !!}
                </div>

                <p class="referral-note">Scan to register using your referral code.</p>

                <div class="referral-box">
                    <p class="referral-text">{{ $referralUrl }}</p>
                </div>

                <button
                    type="button"
                    @click="
                        navigator.clipboard.writeText('{{ $referralUrl }}');
                        copied = true;
                        setTimeout(() => copied = false, 3000);
                    "
                    class="btn-copy">
                    Copy Referral Link
                </button>
            </div>
        </div>
    </div>

    @endif


    {{-- ==================== 5. TOAST NOTIFICATION ==================== --}}
    <div
        x-show="copied"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        style="display: none;"
        class="toast">
        <svg xmlns="http://www.w3.org/2000/svg" class="alert-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <p class="alert-text">Referral link copied to clipboard!</p>
    </div>

</div>

{{-- ============================= 6. SCRIPTS ============================= --}}
<script>
    function accountForm() {
        return {
            copied: false,
            async confirmSave() {
                @this.call('save');
            }
        }
    }

    document.addEventListener('livewire:initialized', () => {
        // Receives the 'changes' array from backend and displays them in SweetAlert
        Livewire.on('prompt-save-confirmation', (event) => {
            // Livewire 3 passes parameters inside an array, handle both v2/v3 safely
            const changes = Array.isArray(event) ? event[0] : event.changes;

            const changesHtml = changes ? changes.join('<br>') : '';

            Swal.fire({
                title: 'Confirm Account Update',
                html: `Are you sure you want to update these details?<br><br><div style="text-align: left; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 13px;">${changesHtml}</div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0C2C6B',
                cancelButtonColor: '#D79B10',
                confirmButtonText: 'Yes, Save Changes',
                cancelButtonText: 'Cancel',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('executeSave');
                }
            });
        });

        // Triggered when save is completed successfully
        Livewire.on('account-updated', () => {
            Swal.fire({
                title: 'Account Updated!',
                text: 'Your account information has been updated successfully.',
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
</script>
