<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'TrueLife - Solidaritas')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @livewireStyles

    <!-- SweetAlert2 High Z-Index Fix -->
    <style>
        .swal2-container {
            z-index: 99999 !important;
        }
    </style>
</head>


<body class="bg-gray-50 text-gray-900">

    <x-navbar />

    <main>
        {{ $slot }}
    </main>

    <x-footer />

    <!-- Livewire Modal -->
    <livewire:application-modal />
    <livewire:login-modal />
    <livewire:context-editor />

    @livewireScripts


    <!-- SweetAlert2 (single shared, reusable handler) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-shared.sweet-alert-handler />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        let mapInstance = null;

        function initMapIfVisible() {
            const mapElement = document.getElementById('map');

            // Your default coordinates
            const defaultLat = 7.446819564242066;
            const defaultLng = 125.80932758793946;

            if (mapElement) {
                // Fix: Clean up old map instance if it exists to prevent "already initialized" crash
                if (mapInstance) {
                    mapInstance.remove();
                    mapInstance = null;
                }

                mapInstance = L.map('map').setView([defaultLat, defaultLng], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap'
                }).addTo(mapInstance);

                // Add a draggable marker initialized at your default coordinates
                let marker = L.marker([defaultLat, defaultLng], {
                    draggable: true
                }).addTo(mapInstance);

                marker.on('dragend', function(e) {
                    const latlng = marker.getLatLng();
                    Livewire.dispatch('set-coordinates', {
                        lat: latlng.lat,
                        lng: latlng.lng
                    });
                });

                mapInstance.on('click', function(e) {
                    marker.setLatLng(e.latlng);
                    Livewire.dispatch('set-coordinates', {
                        lat: e.latlng.lat,
                        lng: e.latlng.lng
                    });
                });
            } else {
                if (mapInstance) {
                    mapInstance.remove();
                    mapInstance = null;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initMapIfVisible();
        });
    </script>

</body>

</html>
