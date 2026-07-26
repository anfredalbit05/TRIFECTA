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
        });
    </script>

</body>
</html>
