/**
 * Masterlist - Satellite member edit modal
 * (resources/views/livewire/admin-pages/masterlist/satellite-members-components/satellite-members-edit.blade.php).
 *
 * Leaflet map + Livewire event handlers for the satellite member edit form.
 *
 * Extracted from the inline <script> block in the Blade view.
 */

/* Scoped in an IIFE: top-level `let map` here clashes with the same
   declarations in application-modal.blade.php when both components are
   mounted on one page (e.g. Masterlist). A classic-script redeclaration
   throws a SyntaxError that kills this whole block, silently disabling
   every Livewire.on listener below. */
(() => {
    let map = null;
    let marker = null;

    document.addEventListener('livewire:initialized', () => {

    Livewire.on('destroy-map', () => {
        if (map) {
            map.remove();
            map = null;
            marker = null;
        }
    });

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

            const performSearch = () => {
                if (!searchInput) return;

                const query = searchInput.value.trim();
                if (!query) return;

                fetch(
                    `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`
                )
                    .then(response => response.json())
                    .then(results => {
                        if (!results?.length) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Location Not Found',
                                text: 'Please try another location.',
                                confirmButtonColor: 'var(--color-primary)'
                            });
                            return;
                        }

                        const result = results[0];
                        const center = [
                            parseFloat(result.lat),
                            parseFloat(result.lon)
                        ];

                        map.setView(center, 16);

                        if (marker) {
                            marker.setLatLng(center);
                        } else {
                            marker = L.marker(center).addTo(map);
                        }

                        Livewire.dispatch('set-coordinates', {
                            lat: result.lat,
                            lng: result.lon
                        });
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

            map.on('click', e => {
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

            setTimeout(() => map.invalidateSize(), 100);
        }, 300);
    });

    /* VALIDATION ERROR */
    Livewire.on('validation-error', data => {
        Swal.fire({
            icon: 'error',
            title: data.title || 'Error',
            html: data.html || data.text || 'Please check the required fields.',
            confirmButtonText: 'OK',
            confirmButtonColor: 'var(--color-primary)',
            allowOutsideClick: false
        });
    });

    /* CONFIRM UPDATE */
    Livewire.on('swal-confirm-update', data => {
        const changes = data?.changes || [];

        let rowsHtml = '';

        if (changes.length) {
            changes.forEach(change => {
                rowsHtml += `
                    <tr>
                        <td class="px-2.5 py-1.5 font-semibold text-[var(--color-primary)] border-b border-slate-200">
                            ${change.field}
                        </td>
                        <td class="px-2.5 py-1.5 text-red-600 line-through border-b border-slate-200">
                            ${change.old}
                        </td>
                        <td class="px-2.5 py-1.5 text-[var(--color-gold-darker)] font-semibold border-b border-slate-200">
                            ${change.new}
                        </td>
                    </tr>
                `;
            });
        } else {
            rowsHtml = `
                <tr>
                    <td colspan="3" class="p-3 text-center text-slate-400">
                        No changes detected.
                    </td>
                </tr>
            `;
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
            reverseButtons: true
        }).then(result => {
            if (result.isConfirmed) {
                Livewire.dispatch('confirmed-update-member');
            }
        });
    });

    /* GENERIC TOAST */
    Livewire.on('show-toast', data => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: data.icon || 'success',
            title: data.title || 'Success',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });
    });
    });
})();
