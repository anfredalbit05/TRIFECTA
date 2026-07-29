<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Dayongan')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

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
            // Toast Listener
            Livewire.on('toast', (event) => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: event.type,
                    title: event.message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            });

            // Confirmation Alert Listener
            Livewire.on('swal:confirm', (event) => {
                const data = Array.isArray(event) ? event[0] : event;

                Swal.fire({
                    title: data.title || 'Are you sure?',
                    text: data.text || 'Do you want to proceed?',
                    icon: data.icon || 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0B2A5B',
                    cancelButtonColor: '#D79B10',
                    confirmButtonText: 'Yes, proceed!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch(data.nextMethod);
                    }
                });
            });
        });
    </script>

</body>
</html>
