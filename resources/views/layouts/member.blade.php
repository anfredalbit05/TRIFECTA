<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'TRIFECTA')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    {{-- Page-scoped stylesheets pushed by individual pages --}}
    @stack('styles')

    @livewireStyles
</head>

<body class="tf-app">

    {{-- Aurora wash behind every authenticated screen --}}
    <div class="tf-app-canvas" aria-hidden="true"></div>

    <x-sidebar />

    <div class="tf-app-main lg:ml-72">
        <main class="min-w-0 p-0">
            @yield('content')
        </main>
    </div>

    <livewire:application-modal />

    @stack('scripts')
    @livewireScripts

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

</body>

</html>
