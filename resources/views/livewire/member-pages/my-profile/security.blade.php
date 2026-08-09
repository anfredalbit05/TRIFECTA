{{--
=====================================================================
 SECURITY / CHANGE PASSWORD PAGE
=====================================================================
 Styling lives in profile.css (section 11 — reuses shared classes
 from earlier sections plus tone-* helpers applied dynamically via
 Alpine :class bindings for the password-strength colors).
 Sections:
   1. Header
   2. Success message
   3. Security notice
   4. Change password form (current / new / confirm)
   5. Scripts
=====================================================================
--}}

<div class="account-page" x-data="securityForm()">

    {{-- ============================= 1. HEADER ============================= --}}
    <div>
        <h2 class="profile-header-title">Security</h2>
        <p class="profile-header-subtitle">Update your password to keep your account secure.</p>
    </div>

    {{-- ========================= 2. SUCCESS MESSAGE ========================= --}}
    @if (session()->has('security-success'))
        <div class="alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" class="alert-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="alert-text">{{ session('security-success') }}</p>
        </div>
    @endif

    {{-- ========================= 3. SECURITY NOTICE ========================= --}}
    <div class="notice-warning">
        <div class="notice-icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="notice-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01 M5.07 19h13.86 c1.54 0 2.5-1.67 1.73-3 L13.73 4 c-.77-1.33-2.69-1.33-3.46 0 L3.34 16 c-.77 1.33.19 3 1.73 3z" />
            </svg>
        </div>
        <div>
            <h3 class="notice-title">Keep your account secure</h3>
            <p class="notice-text">
                Use a strong password with at least 8 characters. Avoid using personal information or passwords you use on other websites.
            </p>
        </div>
    </div>

    {{-- ====================== 4. CHANGE PASSWORD FORM ====================== --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Change Password</h3>
            <p class="card-subtitle">Enter your current password and choose a new password.</p>
        </div>

        <form @submit.prevent="confirmPasswordChange">
            <div class="password-form-body">

                {{-- Current Password --}}
                <div x-data="{ show: false }">
                    <label class="form-label">Current Password</label>
                    <div class="password-input-wrap">
                        <input
                            :type="show ? 'text' : 'password'"
                            wire:model="currentPassword"
                            autocomplete="current-password"
                            class="password-input">
                        <button type="button" @click="show = !show" class="password-toggle">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="password-toggle-icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="password-toggle-icon" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    @error('currentPassword')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New Password --}}
                <div x-data="{
                    password: '',
                    show: false,
                    get hasMinLength() { return this.password.length >= 8; },
                    get hasUppercase() { return /[A-Z]/.test(this.password); },
                    get hasLowercase() { return /[a-z]/.test(this.password); },
                    get hasNumber() { return /[0-9]/.test(this.password); },
                    get hasSpecial() { return /[^A-Za-z0-9]/.test(this.password); },
                    get score() {
                        let s = 0;
                        if (this.hasMinLength) s++;
                        if (this.hasUppercase) s++;
                        if (this.hasLowercase) s++;
                        if (this.hasNumber) s++;
                        if (this.hasSpecial) s++;
                        return s;
                    },
                    get strengthText() {
                        if (this.score <= 2) return 'Weak';
                        if (this.score <= 4) return 'Medium';
                        return 'Strong';
                    },
                    get tone() {
                        if (this.score <= 2) return 'weak';
                        if (this.score <= 4) return 'medium';
                        return 'strong';
                    }
                }">
                    {{-- Premium Border Container --}}
                    <div class="password-strength-box"
                         :class="{
                             'tone-weak-border': tone === 'weak',
                             'tone-medium-border': tone === 'medium',
                             'tone-strong-border': tone === 'strong'
                         }">

                        {{-- Floating Border Label --}}
                        <label class="password-float-label"
                               :class="{
                                   'tone-weak-text': tone === 'weak',
                                   'tone-medium-text': tone === 'medium',
                                   'tone-strong-text': tone === 'strong'
                               }">
                            New Password
                        </label>

                        <div class="password-input-row">
                            <input
                                :type="show ? 'text' : 'password'"
                                x-model="password"
                                @input="$wire.set('newPassword', password)"
                                autocomplete="new-password"
                                class="password-input-transparent">

                            <button type="button" @click="show = !show" class="password-toggle">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="password-toggle-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="password-toggle-icon" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Password Strength & Checklist --}}
                    <div class="password-meter">
                        <div class="password-meter-row">
                            <span class="password-meter-label">Password Strength</span>
                            <span class="password-meter-value"
                                  :class="{
                                      'tone-weak-text': tone === 'weak',
                                      'tone-medium-text': tone === 'medium',
                                      'tone-strong-text': tone === 'strong'
                                  }"
                                  x-text="strengthText"></span>
                        </div>

                        <div class="password-meter-track">
                            <div class="password-meter-fill"
                                 :class="{
                                     'tone-weak-bg': tone === 'weak',
                                     'tone-medium-bg': tone === 'medium',
                                     'tone-strong-bg': tone === 'strong'
                                 }"
                                 :style="`width: ${(score / 5) * 100}%`"></div>
                        </div>

                        <div class="password-checklist">
                            <div class="password-checklist-item" :class="{ 'password-checklist-item--met': hasMinLength }">
                                <span><span x-show="hasMinLength">✔</span><span x-show="!hasMinLength">○</span></span>
                                <span class="truncate">8 Chars</span>
                            </div>
                            <div class="password-checklist-item" :class="{ 'password-checklist-item--met': hasUppercase }">
                                <span><span x-show="hasUppercase">✔</span><span x-show="!hasUppercase">○</span></span>
                                <span>A-Z</span>
                            </div>
                            <div class="password-checklist-item" :class="{ 'password-checklist-item--met': hasLowercase }">
                                <span><span x-show="hasLowercase">✔</span><span x-show="!hasLowercase">○</span></span>
                                <span>a-z</span>
                            </div>
                            <div class="password-checklist-item" :class="{ 'password-checklist-item--met': hasNumber }">
                                <span><span x-show="hasNumber">✔</span><span x-show="!hasNumber">○</span></span>
                                <span>123</span>
                            </div>
                            <div class="password-checklist-item" :class="{ 'password-checklist-item--met': hasSpecial }">
                                <span><span x-show="hasSpecial">✔</span><span x-show="!hasSpecial">○</span></span>
                                <span>@#$</span>
                            </div>
                        </div>
                    </div>

                    @error('newPassword')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div x-data="{ show: false }">
                    {{-- Premium Border Container --}}
                    <div class="password-static-box">
                        <label class="password-static-label">Confirm New Password</label>

                        <div class="password-input-row">
                            <input
                                :type="show ? 'text' : 'password'"
                                wire:model="newPasswordConfirmation"
                                autocomplete="new-password"
                                class="password-input-transparent">

                            <button type="button" @click="show = !show" class="password-toggle">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="password-toggle-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="password-toggle-icon" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    @error('newPasswordConfirmation')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <div class="card-footer">
                <button type="submit" wire:loading.attr="disabled" wire:target="changePassword" class="btn-submit">
                    <span wire:loading.remove wire:target="changePassword">Change Password</span>
                    <span wire:loading wire:target="changePassword">Updating...</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================= 5. SCRIPTS ============================= --}}
<script>
    function securityForm() {
        return {
            async confirmPasswordChange() {
                @this.call('changePassword');
            }
        }
    }

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('prompt-password-confirmation', () => {
            Swal.fire({
                title: 'Confirm Password Update',
                text: 'Are you sure you want to change your account password?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0C2C6B',
                cancelButtonColor: '#D79B10',
                confirmButtonText: 'Yes, Change Password',
                cancelButtonText: 'Cancel',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('executePasswordChange');
                }
            });
        });

        Livewire.on('password-updated', () => {
            Swal.fire({
                title: 'Password Updated!',
                text: 'Your password has been changed successfully.',
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
