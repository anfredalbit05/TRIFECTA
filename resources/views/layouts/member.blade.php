<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'DAYONGAN')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @livewireStyles
</head>

<body class="bg-gray-100">

        <div class="flex min-h-screen">

            <!-- Sidebar -->
            <x-sidebar />

            <!-- Main Content -->
            <div class="flex-1 lg:ml-72">

                <main class="p-0">
                    @yield('content')
                </main>

            </div>

        </div>

   <livewire:application-modal />

   <!-- Force SweetAlert to always render above Tailwind modals/sidebars -->
    <style>
        .swal2-container {
            z-index: 99999 !important;
        }
    </style>

    @livewireScripts

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
