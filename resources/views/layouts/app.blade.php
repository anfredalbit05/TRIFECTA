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

    /*
     * Mobile bottom navigation spacing.
     * The navbar is fixed at the bottom, so we reserve space
     * for it to prevent content from being hidden underneath.
     */
    @media (max-width: 1023px) {
        main {
            padding-bottom: calc(100px + env(safe-area-inset-bottom));
        }

        footer {
            padding-bottom: calc(100px + env(safe-area-inset-bottom));
        }
    }
</style>


</head>

<body class="bg-gray-50 text-gray-900">


<!-- Navbar -->
<x-navbar />

<!-- Page Content -->
<main class="min-h-screen pt-16 pb-24 lg:pt-0 lg:pb-0">
    {{ $slot }}
</main>

<!-- Footer -->
<x-footer />


<!-- Livewire Modals -->
<livewire:application-modal />
<livewire:login-modal />
<livewire:context-editor />

@livewireScripts


<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<x-shared.sweet-alert-handler />


<!-- Leaflet -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
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

            mapInstance = L.map('map').setView(
                [defaultLat, defaultLng],
                15
            );

            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                {
                    attribution: '&copy; OpenStreetMap'
                }
            ).addTo(mapInstance);


            // Draggable marker
            let marker = L.marker(
                [defaultLat, defaultLng],
                {
                    draggable: true
                }
            ).addTo(mapInstance);


            marker.on('dragend', function () {

                const latlng = marker.getLatLng();

                Livewire.dispatch(
                    'set-coordinates',
                    {
                        lat: latlng.lat,
                        lng: latlng.lng
                    }
                );

            });


            // Click map to move marker
            mapInstance.on('click', function (e) {

                marker.setLatLng(e.latlng);

                Livewire.dispatch(
                    'set-coordinates',
                    {
                        lat: e.latlng.lat,
                        lng: e.latlng.lng
                    }
                );

            });

        } else {

            if (mapInstance) {
                mapInstance.remove();
                mapInstance = null;
            }

        }
    }


    document.addEventListener(
        'DOMContentLoaded',
        function () {
            initMapIfVisible();
        }
    );
</script>


</body>

</html>
