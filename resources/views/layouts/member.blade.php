<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'DAYONGAN')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

    <linkrel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <!-- Include html2canvas-pro to support oklch -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas-pro@1.5.0/dist/html2canvas-pro.min.js"></script>
<!-- Include html2pdf -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include html2pdf library -->

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

            // UPDATE MEMBER
            Livewire.on('swal-confirm-update', () => {

                Swal.fire({
                    title: 'Update Member?',
                    text: 'Are you sure you want to update this member?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update it!',
                    confirmButtonColor: '#0B2A5B',
                    cancelButtonColor: '#D79B10'
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

            // ACTIVATE ACCOUNT
            Livewire.on('swal:confirm', (event) => {

                const data = Array.isArray(event) ? event[0] : event;

                Swal.fire({
                    title: data.title,
                    text: data.text,
                    icon: data.icon,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, proceed!',
                    confirmButtonColor: '#0B2A5B',
                    cancelButtonColor: '#D79B10',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {

                        Swal.fire({
                            title: 'Processing Activation...',
                            text: 'Please wait while we process your request.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        Livewire.dispatch(data.method);
                    }
                });
            });

            // CLOSE LOADING
            Livewire.on('swal-close', () => {
                Swal.close();
            });

            // SUCCESS ALERT
            Livewire.on('swal:success', (event) => {

                const data = Array.isArray(event) ? event[0] : event;

                Swal.fire({
                    icon: 'success',
                    title: data.title,
                    text: data.text,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0B2A5B',
                    showConfirmButton: true
                });

            });
        });
    </script>


</body>

</html>
