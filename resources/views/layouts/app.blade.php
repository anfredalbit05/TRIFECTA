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
        @yield('content')
    </main>

    <x-footer />

    <!-- Livewire Modal -->
    <livewire:application-modal />

      <!-- Livewire Modal -->
    <livewire:login-modal />

    @livewireScripts

</body>
</html>
