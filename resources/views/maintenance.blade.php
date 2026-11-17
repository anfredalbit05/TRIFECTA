<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance - TRIFECTA</title>

    @vite(['resources/css/app.css', 'resources/css/maintenance.css'])
</head>
<body class="maintenance-body">

    <div class="maintenance-wrapper">
        <div class="maintenance-card">

            <!-- DECORATIVE BACKGROUND GLOW -->
            <div class="maintenance-glow-top"></div>
            <div class="maintenance-glow-bottom"></div>

            <!-- ANIMATED ICON -->
            <div class="maintenance-icon-container animate-float">
                <div class="maintenance-icon-pulse animate-pulse-subtle"></div>

                <svg xmlns="http://www.w3.org/2000/svg" class="maintenance-icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>

            <!-- TITLE & DESCRIPTION -->
            <div class="maintenance-text">
                <span class="maintenance-badge">
                    System Maintenance
                </span>

                <h1 class="maintenance-title">
                    We'll Be Back Soon!
                </h1>

                <p class="maintenance-description">
                    We are currently performing scheduled maintenance to improve our services and user experience. Please check back shortly.
                </p>
            </div>

            <!-- FOOTER META -->
            <div class="maintenance-footer">
                &copy; {{ date('Y') }} TRIFECTA. All rights reserved.
            </div>

        </div>
    </div>

</body>
</html>
