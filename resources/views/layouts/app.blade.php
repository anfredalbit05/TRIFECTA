<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Dayongan')
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



    <!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('livewire:init', () => {

        // TOAST NOTIFICATIONS
        Livewire.on('toast', (event) => {
            const data = Array.isArray(event) ? event[0] : event;

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: data.type,
                title: data.message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        });

        // GENERAL CONFIRMATION
        Livewire.on('swal:confirm', (event) => {
            const data = Array.isArray(event) ? event[0] : event;

            Swal.fire({
                title: data.title || 'Are you sure?',
                text: data.text || 'Do you want to proceed?',
                icon: data.icon || 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed!',
                confirmButtonColor: '#0B2A5B',
                cancelButtonColor: '#D79B10',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    Livewire.dispatch(data.method || data.nextMethod);
                }
            });
        });

        // APPLICATION SUBMISSION
        Livewire.on('swal-confirm-submission', () => {
            Swal.fire({
                title: 'Save Application?',
                text: 'Are you sure you want to submit this application?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, submit it!',
                confirmButtonColor: '#0B2A5B',
                cancelButtonColor: '#D79B10'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Submitting...',
                        text: 'Please wait.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    Livewire.dispatch('confirmed-save-application');
                }
            });
        });

        // CLOSE LOADING
        Livewire.on('swal-close', () => {
            Swal.close();
        });

        // SUCCESS ALERT WITH OK BUTTON
        Livewire.on('swal:success', (event) => {
            const data = Array.isArray(event) ? event[0] : event;

            Swal.close();

            Swal.fire({
                icon: 'success',
                title: data.title || 'Success!',
                text: data.text || 'Operation completed successfully.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#0B2A5B',
                showConfirmButton: true,
                allowOutsideClick: false
            });
        });



    });
</script>

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
