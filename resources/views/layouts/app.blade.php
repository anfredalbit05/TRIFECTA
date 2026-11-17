<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'TRIFECTA')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    @livewireStyles

</head>

<body class="tf-public">

    <x-navbar />

    <main class="tf-public-main">
        {{ $slot }}
    </main>

    <x-footer />

    <livewire:application-modal />
    <livewire:login-modal />
    <livewire:context-editor />

    @stack('scripts')
    @livewireScripts

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @vite('resources/js/layouts/app.js')

</body>

</html>
