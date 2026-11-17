/**
 * Application layout scripts.
 *
 * Extracted from resources/views/layouts/app.blade.php.
 * Initialises the Leaflet map (id="map") when present and keeps the
 * Livewire `set-coordinates` event wired to marker drag / map clicks.
 */

let mapInstance = null;

function initMapIfVisible() {
    const mapElement = document.getElementById('map');

    // Default coordinates
    const defaultLat = 7.446819564242066;
    const defaultLng = 125.80932758793946;

    if (mapElement) {
        // Remove previous map instance
        if (mapInstance) {
            mapInstance.remove();
            mapInstance = null;
        }

        mapInstance = L.map('map').setView([defaultLat, defaultLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap',
        }).addTo(mapInstance);

        // Draggable marker
        const marker = L.marker([defaultLat, defaultLng], {
            draggable: true,
        }).addTo(mapInstance);

        marker.on('dragend', function () {
            const latlng = marker.getLatLng();

            Livewire.dispatch('set-coordinates', {
                lat: latlng.lat,
                lng: latlng.lng,
            });
        });

        // Click map to move marker
        mapInstance.on('click', function (e) {
            marker.setLatLng(e.latlng);

            Livewire.dispatch('set-coordinates', {
                lat: e.latlng.lat,
                lng: e.latlng.lng,
            });
        });
    } else {
        if (mapInstance) {
            mapInstance.remove();
            mapInstance = null;
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    initMapIfVisible();
});
