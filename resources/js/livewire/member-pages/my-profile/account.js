/**
 * My Profile – Account tab (resources/views/livewire/member-pages/my-profile/account.blade.php).
 */

import { livewireCall } from '../../../shared/livewire.js';

const COMPONENT = 'member-pages.my-profile.account';

function accountForm() {
    return {
        copied: false,
        async confirmSave() {
            livewireCall(COMPONENT, 'save');
        },
    };
}

document.addEventListener('livewire:initialized', () => {
    // Receives the 'changes' array from backend and displays them in SweetAlert
    Livewire.on('prompt-save-confirmation', (event) => {
        // Livewire 3 passes parameters inside an array, handle both v2/v3 safely
        const changes = Array.isArray(event) ? event[0] : event.changes;

        const changesHtml = changes ? changes.join('<br>') : '';

        Swal.fire({
            title: 'Confirm Account Update',
            html: `Are you sure you want to update these details?<br><br><div class="text-left bg-slate-50 p-3 rounded-lg border border-slate-200 text-[13px]">${changesHtml}</div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: 'var(--color-primary)',
            cancelButtonColor: 'var(--color-accent)',
            confirmButtonText: 'Yes, Save Changes',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                livewireCall(COMPONENT, 'executeSave');
            }
        });
    });

    // Triggered when save is completed successfully
    Livewire.on('account-updated', () => {
        Swal.fire({
            title: 'Account Updated!',
            text: 'Your account information has been updated successfully.',
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

window.accountForm = accountForm;
