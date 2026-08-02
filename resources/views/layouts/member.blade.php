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

    <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
document.addEventListener('livewire:init', () => {

    // SUBMIT APPLICATION
    Livewire.on('swal-confirm-submission', () => {

        Swal.fire({
            title: 'Save Application?',
            text: 'Are you sure you want to submit this application?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, submit it!',
            confirmButtonColor: '#0B2A5B',
            cancelButtonColor: '#d33'
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

    // UPDATE MEMBER
    Livewire.on('swal-confirm-update', () => {

        Swal.fire({
            title: 'Update Member?',
            text: 'Are you sure you want to update this member?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, update it!',
            confirmButtonColor: '#0B2A5B',
            cancelButtonColor: '#d33'
        }).then((result) => {

            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Updating...',
                    text: 'Please wait.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                Livewire.dispatch('confirmed-update-member');
            }

        });

    });

    // CLOSE LOADING
    Livewire.on('swal-close', () => {
        Swal.close();
    });

    // SUCCESS ALERT
    Livewire.on('show-success-after-close', (event) => {

        const data = Array.isArray(event) ? event[0] : event;

        Swal.fire({
            icon: 'success',
            title: data.title,
            text: data.text,
            confirmButtonColor: '#0B2A5B'
        });

    });

});
</script>


</body>
</html>
