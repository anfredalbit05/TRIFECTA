/**
 * My Profile – Personal Information & ID Card tab
 * (resources/views/livewire/member-pages/my-profile/profile.blade.php).
 *
 * Contains the Alpine factories (`profileForm`, `signaturePadSetup`), the ID
 * card tab switcher and the Livewire listeners for the success dialogs.
 */

import { livewireCall, livewireGet } from '../../../shared/livewire.js';

const COMPONENT = 'member-pages.my-profile.profile';

function profileForm(initialValues) {
    return {
        initial: initialValues,

        async confirmSave() {
            const current = {
                'First Name': await livewireGet(COMPONENT, 'firstname'),
                'Last Name': await livewireGet(COMPONENT, 'lastname'),
                'Middle Name': await livewireGet(COMPONENT, 'middlename'),
                Birthdate: await livewireGet(COMPONENT, 'birthdate'),
                'Contact No': await livewireGet(COMPONENT, 'contactno'),
                Address: await livewireGet(COMPONENT, 'address'),
                'Civil Status': await livewireGet(COMPONENT, 'civilStatus'),
                Sex: await livewireGet(COMPONENT, 'sex'),
                'Emergency Name': await livewireGet(COMPONENT, 'emergencyName'),
                'Emergency Relationship': await livewireGet(COMPONENT, 'emergencyRelationship'),
                'Emergency Contact No': await livewireGet(COMPONENT, 'emergencyContactno'),
                'Emergency Address': await livewireGet(COMPONENT, 'emergencyAddress'),
            };

            const keysMap = {
                'First Name': 'firstname',
                'Last Name': 'lastname',
                'Middle Name': 'middlename',
                Birthdate: 'birthdate',
                'Contact No': 'contactno',
                Address: 'address',
                'Civil Status': 'civilStatus',
                Sex: 'sex',
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
                    changes.push(
                        `<li><b>${label}:</b> "${oldVal || 'None'}" ➔ "${newVal || 'None'}"</li>`,
                    );
                }
            }

            // If NOTHING changed, show an info modal and STOP here
            if (changes.length === 0) {
                Swal.fire({
                    title: 'No Changes Detected',
                    text: 'You haven’t modified any profile details.',
                    icon: 'info',
                    confirmButtonColor: 'var(--color-primary)',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                });
                return; // Exit function so it doesn't try to save
            }

            // If changes WERE found, show confirmation modal
            let htmlContent = `
                <div class="mt-2 space-y-1 text-left text-[13px] max-h-[220px] overflow-y-auto">
                    <p class="font-semibold text-slate-800 mb-2">Detected Modifications (${changes.length}):</p>
                    <ul class="space-y-1 text-slate-600 list-disc pl-5">
                        ${changes.join('')}
                    </ul>
                </div>
            `;

            Swal.fire({
                title: 'Confirm Profile Update',
                html: htmlContent,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: 'var(--color-primary)',
                cancelButtonColor: 'var(--color-accent)',
                confirmButtonText: 'Yes, Save Changes',
                cancelButtonText: 'Cancel',
                width: '520px',
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    livewireCall(COMPONENT, 'saveProfile');
                }
            });
        },
    };
}

document.addEventListener('livewire:initialized', () => {
    // Listener for Profile Details Update
    Livewire.on('profile-details-updated', () => {
        Swal.fire({
            title: 'Updated Successfully!',
            text: 'Your personal and emergency contact information has been updated.',
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

    // Listener for Profile Picture Update
    Livewire.on('picture-updated', () => {
        Swal.fire({
            title: 'Picture Updated!',
            text: 'Your profile picture has been updated successfully.',
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

    Livewire.on('signature-saved', ({ url }) => {
        const canvas = document.querySelector('[x-ref="signatureCanvas"]');

        if (!canvas) return;

        requestAnimationFrame(() => {
            const width = canvas.offsetWidth;
            const height = canvas.offsetHeight;

            if (!width || !height) return;

            canvas.width = width;
            canvas.height = height;

            const pad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)',
            });

            pad.clear();

            if (url) {
                pad.fromDataURL(url);
            }

            Swal.fire({
                title: 'Signature Saved!',
                text: 'Your signature has been saved successfully.',
                icon: 'success',
                confirmButtonColor: 'var(--color-primary)',
                confirmButtonText: 'OK',
            });
        });
    });

    // =========================================================
    // SIGNATURE DELETED SUCCESSFULLY
    // =========================================================
    Livewire.on('signature-deleted', () => {
        Swal.fire({
            title: 'Signature Deleted!',
            text: 'Your saved signature has been permanently deleted.',
            icon: 'success',
            confirmButtonColor: 'var(--color-primary)',
            confirmButtonText: 'OK',
            allowOutsideClick: false,
            allowEscapeKey: false,
        });
    });

    // =========================================================
    // NO SIGNATURE TO DELETE
    // =========================================================
    Livewire.on('signature-delete-no-signature', () => {
        Swal.fire({
            title: 'No Signature Found',
            text: 'There is no saved signature to delete.',
            icon: 'info',
            confirmButtonColor: 'var(--color-primary)',
            confirmButtonText: 'OK',
            allowOutsideClick: false,
            allowEscapeKey: false,
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

function signaturePadSetup(savedSignatureUrl = null) {
    return {
        pad: null,
        savedSignatureUrl,

        initPad() {
            requestAnimationFrame(() => {
                const canvas = this.$refs.signatureCanvas;

                if (!canvas) return;

                const width = canvas.offsetWidth;
                const height = canvas.offsetHeight;

                if (!width || !height) return;

                canvas.width = width;
                canvas.height = height;

                this.pad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)',
                });

                // Initialize empty canvas
                this.pad.clear();

                // Restore existing saved signature
                if (this.savedSignatureUrl) {
                    this.pad.fromDataURL(this.savedSignatureUrl);
                }
            });
        },

        restoreSignature(url) {
            requestAnimationFrame(() => {
                const canvas = this.$refs.signatureCanvas;

                if (!canvas) return;

                const width = canvas.offsetWidth;
                const height = canvas.offsetHeight;

                if (!width || !height) return;

                canvas.width = width;
                canvas.height = height;

                this.pad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)',
                });

                this.pad.clear();

                if (url) {
                    this.pad.fromDataURL(url);
                }
            });
        },

        // =========================================================
        // CLEAR ONLY THE CANVAS
        // DOES NOT DELETE THE SAVED SIGNATURE
        // =========================================================
        clearPad() {
            if (!this.pad) return;

            this.pad.clear();
        },

        // =========================================================
        // DELETE SAVED SIGNATURE
        // =========================================================
        deleteSignature() {
            if (!this.savedSignatureUrl) {
                Swal.fire({
                    title: 'No Signature Found',
                    text: 'There is no saved signature to delete.',
                    icon: 'info',
                    confirmButtonColor: 'var(--color-primary)',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                });

                return;
            }

            Swal.fire({
                title: 'Delete Signature?',
                text: 'Are you sure you want to permanently delete your saved signature?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Delete It',
                cancelButtonText: 'Cancel',
                allowOutsideClick: false,
                allowEscapeKey: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    livewireCall(COMPONENT, 'removeSignature');
                }
            });
        },

        // =========================================================
        // SAVE SIGNATURE
        // =========================================================
        savePad() {
            if (!this.pad || this.pad.isEmpty()) {
                Swal.fire({
                    title: 'No Signature',
                    text: 'Please provide a signature first.',
                    icon: 'warning',
                    confirmButtonColor: 'var(--color-primary)',
                    confirmButtonText: 'OK',
                });

                return;
            }

            const dataUrl = (() => {
                const src = this.pad.canvas;

                const exportCanvas = document.createElement('canvas');

                exportCanvas.width = src.width;
                exportCanvas.height = src.height;

                const ctx = exportCanvas.getContext('2d');

                ctx.fillStyle = '#ffffff';

                ctx.fillRect(0, 0, exportCanvas.width, exportCanvas.height);

                ctx.drawImage(src, 0, 0);

                return exportCanvas.toDataURL('image/jpeg', 1.0);
            })();

            // =====================================================
            // SAVE CONFIRMATION
            // =====================================================
            Swal.fire({
                title: 'Save Signature?',
                text: 'Your signature will be saved to your profile.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: 'var(--color-primary)',
                cancelButtonColor: 'var(--color-accent)',
                confirmButtonText: 'Yes, Save It',
                cancelButtonText: 'Cancel',
                allowOutsideClick: false,
                allowEscapeKey: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    livewireCall(COMPONENT, 'saveSignature', dataUrl);
                }
            });
        },
    };
}

window.profileForm = profileForm;
window.switchIdTab = switchIdTab;
window.signaturePadSetup = signaturePadSetup;
