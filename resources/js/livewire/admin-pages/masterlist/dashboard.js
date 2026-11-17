/**
 * Masterlist – Overview / dashboard tab
 * (resources/views/livewire/admin-pages/masterlist/dashboard.blade.php).
 *
 * Renders the member location map. The marker data is read from the
 * `data-users` attribute on `#users-map`, which the Blade view fills with the
 * server-side `$mapUsers` payload.
 */

let myMap = null;

function initMap() {
    const mapContainer = document.getElementById('users-map');
    if (!mapContainer) return;

    // Leaflet is loaded from a CDN at the end of the layout.
    if (typeof L === 'undefined') return;

    // Same container, map already built: nothing to do.
    if (myMap !== null && myMap.getContainer() === mapContainer) return;

    // The tab was re-rendered/replaced: tear the old map down first.
    if (myMap !== null) {
        myMap.remove();
        myMap = null;
    }

    if (mapContainer._leaflet_id) {
        delete mapContainer._leaflet_id;
    }

    setTimeout(() => {
        // Several initMap() calls can queue while the page boots
        // (DOMContentLoaded, livewire:initialized and the morph hooks).
        // Re-check here so a second timer never initialises the same
        // container twice ("Map container is already initialized").
        if (myMap !== null) {
            myMap.remove();
            myMap = null;
        }

        if (mapContainer._leaflet_id) {
            delete mapContainer._leaflet_id;
        }

        const users = JSON.parse(mapContainer.dataset.users || '[]');

        myMap = L.map('users-map').setView([8.4542, 124.6319], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(myMap);

        const bounds = [];

        const personIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/149/149071.png',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40],
        });

        users.forEach((user) => {
            const lat = parseFloat(user.latitude);
            const lng = parseFloat(user.longitude);

            if (!isNaN(lat) && !isNaN(lng)) {
                const marker = L.marker([lat, lng], {
                    icon: personIcon,
                }).addTo(myMap);

                marker.bindPopup(`
                    <div style="font-size: 12px; line-height: 1.4;">
                        Name: <strong>${user.FirstName} ${user.LastName}</strong><br>
                        Address: <strong>${user.address}</strong>
                    </div>
                `);

                bounds.push([lat, lng]);
            }
        });

        if (bounds.length > 0) {
            myMap.fitBounds(bounds, {
                padding: [50, 50],
            });
        }
    }, 100);
}

window.initMap = initMap;

document.addEventListener('DOMContentLoaded', initMap);

document.addEventListener('livewire:initialized', () => {
    initMap();

    Livewire.hook('morph.updated', ({ el }) => {
        if (!el || !el.querySelector) return;

        if (el.id === 'users-map' || el.querySelector('#users-map')) {
            initMap();
        }
    });

    // Lazy tabs are morphed in after the page boots, which does not always
    // emit "morph.updated" for the freshly inserted root element.
    Livewire.hook('morphed', ({ el }) => {
        if (!el || !el.querySelector) return;

        if (el.id === 'users-map' || el.querySelector('#users-map')) {
            initMap();
        }
    });
});
