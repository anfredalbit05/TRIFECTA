/**
 * Masterlist - Satellite application modal
 * (resources/views/livewire/admin-pages/masterlist/members-modals/satelite-modal.blade.php).
 *
 * Leaflet map and the satellite-member application dialogs (validation, submit, resubmit).
 *
 * Extracted from the inline <script> block in the Blade view.
 */

let satelliteMap = null;
let satelliteMarker = null;

document.addEventListener('livewire:initialized', () => {

    Livewire.on('satellite-destroy-map', () => {
        if (satelliteMap) {
            satelliteMap.remove();
            satelliteMap = null;
            satelliteMarker = null;
        }
    });

    Livewire.on('satellite-map-opened', (data) => {
        setTimeout(() => {
            const lat = data.latitude ?? 7.442294461028405;
            const lng = data.longitude ?? 125.81370503321095;

            if (satelliteMap) {
                satelliteMap.remove();
                satelliteMap = null;
                satelliteMarker = null;
            }

            satelliteMap = L.map('satellite-map').setView([lat, lng], 15);

            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                { attribution: '&copy; OpenStreetMap' }
            ).addTo(satelliteMap);

            if (data.latitude != null && data.longitude != null) {
                satelliteMarker = L.marker([lat, lng]).addTo(satelliteMap);
                satelliteMap.setView([lat, lng], 16);
            }

            const searchInput = document.getElementById('satellite-leaflet-search-input');
            const searchBtn = document.getElementById('satellite-leaflet-search-btn');

            const setPin = (lat, lng) => {
                const center = [parseFloat(lat), parseFloat(lng)];

                satelliteMap.setView(center, 16);

                if (satelliteMarker) {
                    satelliteMarker.setLatLng(center);
                } else {
                    satelliteMarker = L.marker(center).addTo(satelliteMap);
                }

                Livewire.dispatch('satellite-set-coordinates', {
                    lat: center[0],
                    lng: center[1]
                });
            };

            const performSearch = () => {
                if (!searchInput) return;

                const query = searchInput.value.trim();
                if (!query) return;

                fetch(
                    `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`
                )
                    .then(response => response.json())
                    .then(results => {
                        if (results?.length) {
                            setPin(results[0].lat, results[0].lon);
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Location Not Found',
                                text: 'Please try another location.',
                                confirmButtonColor: 'var(--color-primary)'
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Search Error',
                            text: 'Unable to search for this location. Please try again.',
                            confirmButtonColor: 'var(--color-primary)'
                        });
                    });
            };

            searchInput?.addEventListener('keypress', e => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    performSearch();
                }
            });

            searchBtn?.addEventListener('click', e => {
                e.preventDefault();
                performSearch();
            });

            satelliteMap.on('click', e => {
                setPin(e.latlng.lat, e.latlng.lng);
            });

            setTimeout(() => satelliteMap?.invalidateSize(), 100);
        }, 300);
    });

    Alpine.data('membershipWizard', () => ({
        step: 1,

        async nextStep() {
            if (this.step >= 3) return;

            try {
                const result = await this.$wire.validateStep(this.step);

                if (!result.valid) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Please Check Your Information',
                        html: `
                            <div style="text-align:left;">
                                <ul style="margin:0;padding-left:20px;">
                                    ${result.errors.map(error => `<li>${error}</li>`).join('')}
                                </ul>
                            </div>
                        `,
                        confirmButtonText: 'OK',
                        confirmButtonColor: 'var(--color-primary)'
                    });

                    this.scrollTop();
                    return;
                }

                this.step++;
                this.scrollTop();
            } catch (error) {
                console.error(error);

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Unable to validate the form. Please try again.',
                    confirmButtonColor: 'var(--color-primary)'
                });
            }
        },

        previousStep() {
            if (this.step > 1) {
                this.step--;
                this.scrollTop();
            }
        },

        scrollTop() {
            this.$nextTick(() => {
                const body = document.getElementById('membership-wizard-body');

                body?.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    }));

    /* VALIDATION ERROR */
    Livewire.on('satellite-validation-error', event => {
        const errors = event.errors ?? [];

        if (!errors.length) return;

        Swal.fire({
            icon: 'warning',
            title: 'Please Check Your Information',
            html: `
                <div style="text-align:left;">
                    <ul style="margin:0;padding-left:22px;line-height:1.8;">
                        ${errors.map(error => `<li>${error}</li>`).join('')}
                    </ul>
                </div>
            `,
            confirmButtonText: 'OK',
            confirmButtonColor: 'var(--color-primary)'
        });
    });

    /* NEW APPLICATION CONFIRMATION */
    Livewire.on('satellite-submit-confirmation', () => {
        Swal.fire({
            icon: 'question',
            title: 'Confirm Application Submission',
            html: `
                <div style="text-align:left;line-height:1.7;">
                    <p>Please review all information carefully before submitting your application.</p>

                    <p style="margin-top:12px;">
                        Once submitted, the application will be processed.
                        Make sure all membership, account, payment, and
                        upline information is correct.
                    </p>

                    <div style="margin-top:18px;padding:14px;background:#F3F4F6;border-radius:8px;">
                        <label
                            for="confirm-submit-text"
                            style="display:block;font-weight:600;margin-bottom:8px;"
                        >
                            Type
                            <strong style="color:var(--color-primary);">
                                SUBMIT APPLICATION
                            </strong>
                            to confirm:
                        </label>

                        <input
                            id="confirm-submit-text"
                            type="text"
                            class="swal2-input"
                            autocomplete="off"
                            style="width:100%;margin:0;box-sizing:border-box;color:var(--color-primary);"
                        >

                        <small
                            id="confirm-submit-hint"
                            style="display:block;margin-top:8px;color:var(--color-primary);"
                        >
                            The confirmation button will be enabled once
                            the phrase is entered correctly.
                        </small>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Yes, Submit Application',
            cancelButtonText: 'Review Again',
            confirmButtonColor: 'var(--color-primary)',
            cancelButtonColor: 'var(--color-accent)',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            focusConfirm: false,

            didOpen: () => {
                const input = document.getElementById('confirm-submit-text');
                const confirmButton = Swal.getConfirmButton();
                const hint = document.getElementById('confirm-submit-hint');

                confirmButton.disabled = true;
                confirmButton.style.opacity = '0.5';
                input.focus();

                input.addEventListener('input', () => {
                    const valid = input.value.trim() === 'SUBMIT APPLICATION';

                    confirmButton.disabled = !valid;
                    confirmButton.style.opacity = valid ? '1' : '0.5';

                    hint.textContent = valid
                        ? 'Confirmation phrase accepted.'
                        : 'Please type exactly: SUBMIT APPLICATION';

                    hint.style.color = valid ? 'var(--color-primary)' : '#6B7280';
                });
            }
        }).then(result => {
            if (result.isConfirmed) {
                Livewire.dispatch('satellite-confirm-submit');
            }
        });
    });

    /* REAPPLICATION CONFIRMATION */
    Livewire.on('satellite-reapply-confirmation', () => {
        Swal.fire({
            icon: 'question',
            title: 'Confirm Reapplication Submission',
            html: `
                <div style="text-align:left;line-height:1.7;">
                    <p>
                        This satellite account was previously <strong>rejected</strong>.
                        Resubmitting will <strong>update the SAME satellite user</strong>
                        and file a fresh activation for approval.
                    </p>

                    <p style="margin-top:12px;">
                        Make sure all membership, account, payment, and upline
                        information is correct before resubmitting.
                    </p>

                    <div style="margin-top:18px;padding:14px;background:#F3F4F6;border-radius:8px;">
                        <label
                            for="confirm-reapply-text"
                            style="display:block;font-weight:600;margin-bottom:8px;"
                        >
                            Type
                            <strong style="color:var(--color-primary);">
                                RESUBMIT APPLICATION
                            </strong>
                            to confirm:
                        </label>

                        <input
                            id="confirm-reapply-text"
                            type="text"
                            class="swal2-input"
                            autocomplete="off"
                            style="width:100%;margin:0;box-sizing:border-box;color:var(--color-primary);"
                        >

                        <small
                            id="confirm-reapply-hint"
                            style="display:block;margin-top:8px;color:var(--color-primary);"
                        >
                            The confirmation button will be enabled once
                            the phrase is entered correctly.
                        </small>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Yes, Resubmit Application',
            cancelButtonText: 'Review Again',
            confirmButtonColor: 'var(--color-primary)',
            cancelButtonColor: 'var(--color-accent)',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            focusConfirm: false,

            didOpen: () => {
                const input = document.getElementById('confirm-reapply-text');
                const confirmButton = Swal.getConfirmButton();
                const hint = document.getElementById('confirm-reapply-hint');

                confirmButton.disabled = true;
                confirmButton.style.opacity = '0.5';
                input.focus();

                input.addEventListener('input', () => {
                    const valid = input.value.trim() === 'RESUBMIT APPLICATION';

                    confirmButton.disabled = !valid;
                    confirmButton.style.opacity = valid ? '1' : '0.5';

                    hint.textContent = valid
                        ? 'Confirmation phrase accepted.'
                        : 'Please type exactly: RESUBMIT APPLICATION';

                    hint.style.color = valid ? 'var(--color-primary)' : '#6B7280';
                });
            }
        }).then(result => {
            if (result.isConfirmed) {
                Livewire.dispatch('satellite-confirm-submit');
            }
        });
    });
});
