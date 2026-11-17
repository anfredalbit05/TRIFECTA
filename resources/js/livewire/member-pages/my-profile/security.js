/**
 * My Profile – Security tab (resources/views/livewire/member-pages/my-profile/security.blade.php).
 */

import { livewireCall } from '../../../shared/livewire.js';

const COMPONENT = 'member-pages.my-profile.security';

function securityForm() {
    return {
        async confirmPasswordChange() {
            livewireCall(COMPONENT, 'changePassword');
        },
    };
}

document.addEventListener('livewire:initialized', () => {
    Livewire.on('prompt-password-confirmation', () => {
        Swal.fire({
            title: 'Confirm Password Update',
            text: 'Are you sure you want to change your account password?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: 'var(--color-primary)',
            cancelButtonColor: 'var(--color-accent)',
            confirmButtonText: 'Yes, Change Password',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                livewireCall(COMPONENT, 'executePasswordChange');
            }
        });
    });

    Livewire.on('password-updated', () => {
        Swal.fire({
            title: 'Password Updated!',
            text: 'Your password has been changed successfully.',
            icon: 'success',
            confirmButtonColor: 'var(--color-primary)',
            confirmButtonText: 'OK',
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.reload();
            }
        });
    });
});

window.securityForm = securityForm;
