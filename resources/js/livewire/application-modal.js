/**
 * Application modal
 * (resources/views/livewire/application-modal.blade.php).
 *
 * Leaflet map and the beneficiary/validation dialogs for the public application form.
 *
 * Extracted from the inline <script> block in the Blade view.
 */

/* Scoped in an IIFE: top-level `let map` here clashes with the same
   declarations in satellite-members-edit.blade.php when both components
   are mounted on one page (e.g. Masterlist). A classic-script redeclaration
   throws a SyntaxError that kills this whole block, silently disabling
   every Livewire.on listener below (beneficiary delete confirm, etc.). */
(() => {
    let map = null;
    let marker = null;

    document.addEventListener('livewire:initialized', () => {

    /* =========================================================
       DESTROY MAP
    ========================================================== */
    Livewire.on('destroy-map', () => {
        if (map) {
            map.remove();
            map = null;
            marker = null;
        }
    });

    /* =========================================================
       MAP OPENED
    ========================================================== */
    Livewire.on('map-opened', (data) => {
        setTimeout(() => {
            const defaultLat = 7.442294461028405;
            const defaultLng = 125.81370503321095;

            const lat = data.latitude ?? defaultLat;
            const lng = data.longitude ?? defaultLng;

            if (map) {
                map.remove();
                map = null;
                marker = null;
            }

            map = L.map('map').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            if (data.latitude !== null && data.longitude !== null) {
                marker = L.marker([lat, lng]).addTo(map);
                map.setView([lat, lng], 16);
            }

            const searchInput = document.getElementById('leaflet-search-input');
            const searchBtn = document.getElementById('leaflet-search-btn');

            function performSearch() {
                if (!searchInput) return;
                const query = searchInput.value.trim();
                if (!query) return;

                fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(results => {
                        if (results && results.length > 0) {
                            const r = results[0];
                            const center = [parseFloat(r.lat), parseFloat(r.lon)];

                            map.setView(center, 16);

                            if (marker) {
                                marker.setLatLng(center);
                            } else {
                                marker = L.marker(center).addTo(map);
                            }

                            Livewire.dispatch('set-coordinates', {
                                lat: r.lat,
                                lng: r.lon
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Location Not Found',
                                text: 'Please try another location.',
                                confirmButtonColor: 'var(--color-primary)'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Geocoding error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Search Error',
                            text: 'Unable to search for this location. Please try again.',
                            confirmButtonColor: 'var(--color-primary)'
                        });
                    });
            }

            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        performSearch();
                    }
                });
            }

            if (searchBtn) {
                searchBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    performSearch();
                });
            }

            map.on('click', function(e) {
                if (marker) {
                    marker.setLatLng(e.latlng);
                } else {
                    marker = L.marker(e.latlng).addTo(map);
                }

                Livewire.dispatch('set-coordinates', {
                    lat: e.latlng.lat,
                    lng: e.latlng.lng
                });
            });

            setTimeout(() => {
                map.invalidateSize();
            }, 100);
        }, 300);
    });

    /* =========================================================
       CONFIRM CLEAR / DELETE BENEFICIARY
    ========================================================== */
    Livewire.on('confirm-delete-beneficiary', (data) => {

        const beneficiaryNumber = Number(data.index ?? 1);
        const isPersisted = !!data.persisted;

        if (!isPersisted) {
            Swal.fire({
                icon: 'warning',
                title: `Clear Beneficiary ${beneficiaryNumber}?`,
                text: `This will clear the entered details for Beneficiary ${beneficiaryNumber}. This beneficiary has not been saved yet.`,
                showCancelButton: true,
                confirmButtonText: 'Clear',
                cancelButtonText: 'Cancel',
                confirmButtonColor: 'var(--color-primary)',
                cancelButtonColor: 'var(--color-accent)',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('delete-beneficiary', {
                        index: beneficiaryNumber - 1
                    });
                }
            });
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: `Delete Beneficiary ${beneficiaryNumber}?`,
            html: `
                <div class="text-center text-slate-600 text-sm leading-[1.6]">
                    <p class="mb-3">
                        This beneficiary is already saved. Deleting will
                        <b class="text-red-600">permanently remove</b>
                        <b class="text-[var(--color-primary)]">Beneficiary ${beneficiaryNumber}</b>
                        from the records right away.
                    </p>
                    <p class="text-[13px] mb-2 text-slate-600">
                        To confirm, type <b class="text-red-600 tracking-[1px]">REMOVE</b> below:
                    </p>
                </div>
            `,
            input: 'text',
            inputPlaceholder: 'Type REMOVE here',
            inputAttributes: {
                autocapitalize: 'characters',
                autocomplete: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Delete Beneficiary',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: 'var(--color-accent)',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            preConfirm: (value) => {
                if (value !== 'REMOVE') {
                    Swal.showValidationMessage(
                    'Please type REMOVE exactly to confirm.');
                    return false;
                }
                return true;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('delete-beneficiary', {
                    index: beneficiaryNumber - 1
                });
            }
        });
    });

    /* =========================================================
       BENEFICIARY REMOVED SUCCESS
    ========================================================== */
    Livewire.on('beneficiary-deleted', (data) => {
        const wasPersisted = !!data.persisted;

        Swal.fire({
            icon: 'success',
            title: wasPersisted ? 'Beneficiary Deleted' : 'Beneficiary Cleared',
            text: wasPersisted ?
                `Beneficiary ${data.index} has been permanently removed.` :
                `Beneficiary ${data.index} fields have been cleared.`,
            confirmButtonColor: 'var(--color-primary)',
            timer: 1800,
            timerProgressBar: true,
            showConfirmButton: false
        });
    });

    /* =========================================================
       VALIDATION ERROR
    ========================================================== */
    Livewire.on('validation-error', (data) => {
        Swal.fire({
            icon: 'error',
            title: data.title || 'Error',
            html: data.html || data.text || 'Please check the required fields.',
            confirmButtonText: 'OK',
            confirmButtonColor: 'var(--color-primary)',
            allowOutsideClick: false
        });
    });

    /* =========================================================
       CONFIRM UPDATE — show a diff of what changed
    ========================================================== */
    Livewire.on('swal-confirm-update', (data) => {
        const changes = (data && data.changes) ? data.changes : [];

        let rowsHtml = '';
        if (changes.length > 0) {
            changes.forEach(c => {
                rowsHtml += `
                    <tr>
                        <td class="px-2.5 py-1.5 font-semibold text-[var(--color-primary)] border-b border-slate-200">${c.field}</td>
                        <td class="px-2.5 py-1.5 text-red-600 line-through border-b border-slate-200">${c.old}</td>
                        <td class="px-2.5 py-1.5 text-[var(--color-gold-darker)] font-semibold border-b border-slate-200">${c.new}</td>
                    </tr>
                `;
            });
        } else {
            rowsHtml =
                `<tr><td colspan="3" class="p-3 text-center text-slate-400">No changes detected.</td></tr>`;
        }

        Swal.fire({
            icon: 'question',
            title: 'Confirm Update',
            width: 560,
            html: `
                <div class="text-left">
                    <p class="mb-2.5 text-slate-600 text-[13px]">
                        Please review the changes below before saving:
                    </p>
                    <div class="max-h-[280px] overflow-y-auto border border-slate-200 rounded-lg">
                        <table class="w-full border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100">
                                    <th class="px-2.5 py-1.5 text-left">Field</th>
                                    <th class="px-2.5 py-1.5 text-left">Before</th>
                                    <th class="px-2.5 py-1.5 text-left">After</th>
                                </tr>
                            </thead>
                            <tbody>${rowsHtml}</tbody>
                        </table>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Save Changes',
            cancelButtonText: 'Cancel',
            confirmButtonColor: 'var(--color-primary)',
            cancelButtonColor: 'var(--color-accent)',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('confirmed-update-member');
            }
        });
    });

    /* =========================================================
       GENERIC TOAST
    ========================================================== */
    Livewire.on('show-toast', (data) => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: data.icon || 'success',
            title: data.title || 'Success',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
        });
    });



window.addEventListener('confirm-duplicate-member', (event) => {
    const matches = event.detail.matches || [];
    const applicant = event.detail.applicant || {};

    const escapeHtml = (value) =>
String(value ?? '').replace(/[&<>"']/g, (char) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
})[char]);

    // NEW APPLICANT: information currently entered in the form.
    const applicantHtml = `
<div class="text-left p-3.5 border-2 border-[var(--color-primary)] rounded-[10px] bg-[var(--color-primary-tint)] mb-4">
    <div class="text-[var(--color-primary)] text-xs font-extrabold tracking-[0.5px] mb-2.5">
        NEW APPLICANT — CURRENT FORM
    </div>

    <div class="font-bold text-[var(--color-primary-dark)]">
        ${escapeHtml(applicant.name)}
    </div>

    <div class="text-[13px] mt-1.5">
        <strong>Contact number:</strong>
        ${escapeHtml(applicant.mobile || 'Not provided')}
    </div>
</div>
    `;

    // EXISTING MEMBERS: records already saved in the database.
    const memberList = matches.map((member, index) => {
const reasons = (member.reasons || []).map(reason => `
    <li class="mt-1">
        ${escapeHtml(reason)}
    </li>
`).join('');

return `
    <div class="text-left p-3.5 mb-2.5 border-2 border-amber-600 rounded-[10px] bg-amber-50">
        <div class="text-amber-700 text-xs font-extrabold tracking-[0.4px] mb-[9px]">
            EXISTING TRIFECTA MEMBER ${index + 1}
        </div>

        <div class="font-bold text-amber-900">
            ${escapeHtml(member.name)}
        </div>

        <div class="text-[13px] mt-1.5">
            <strong>Contact number:</strong>
            ${escapeHtml(member.mobile || 'Not provided')}
        </div>

        <div class="text-xs text-amber-800 font-bold mt-2.5">
            WHY THIS RECORD MATCHED
        </div>

        <ul class="pl-5 mt-[5px] mb-0 text-[13px]">
            ${reasons}
        </ul>
    </div>
`;
    }).join('');

    Swal.fire({
icon: 'warning',
title: 'Duplicate Member Verification',
width: 600,

html: `
    <p class="mb-4 text-sm">
        The applicant may already be registered.
        Compare the new application with the existing member records.
    </p>

    ${applicantHtml}

    <div class="text-left font-extrabold text-[13px] text-amber-800 mb-2.5">
        MATCHING RECORDS IN TRIFECTA
    </div>

    <div class="max-h-[220px] overflow-y-auto p-0.5">
        ${memberList}
    </div>

    <div class="text-left p-3.5 mt-4 rounded-[10px] bg-gray-100">
        <div class="font-extrabold mb-1.5">
            Confirm before proceeding
        </div>

        <p class="text-[13px] mb-3">
            If you have verified that this is intentional,
            type <strong>YES, PROCEED</strong> in the field below.
            Otherwise, cancel and review the application.
        </p>

        <input
            id="duplicate-confirmation-input"
            type="text"
            class="swal2-input w-full! box-border! m-0! text-[15px]!"
            placeholder="YES, PROCEED"
            autocomplete="off"
        />
    </div>
`,

showCancelButton: true,
confirmButtonText: 'Confirm & Continue',
cancelButtonText: 'Cancel & Review',
confirmButtonColor: 'var(--color-primary)',
cancelButtonColor: 'var(--color-accent)',
reverseButtons: true,
focusCancel: true,
allowOutsideClick: false,
allowEscapeKey: false,

preConfirm: () => {
    const input = document
        .getElementById('duplicate-confirmation-input')
        ?.value.trim();

    if (input !== 'YES, PROCEED') {
        Swal.showValidationMessage(
            'Please type YES, PROCEED exactly to continue.'
        );

        return false;
    }

    return true;
},

    }).then((result) => {
if (result.isConfirmed) {
    Livewire.dispatch('confirmed-proceed-duplicate-member');
}
    });
});



    });
})();
