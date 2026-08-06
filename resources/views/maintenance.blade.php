<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance - True Life Dayongan</title>

    @vite(['resources/css/app.css'])

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .animate-float {
            animation: float 3.5s ease-in-out infinite;
        }
        @keyframes pulse-subtle {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        .animate-pulse-subtle {
            animation: pulse-subtle 2.5s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-slate-100 to-slate-200 text-slate-800 antialiased selection:bg-[#D79B10] selection:text-white min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">

    <div class="w-full max-w-lg mx-auto transform transition-all duration-500">
        <div class="relative rounded-3xl bg-white/80 backdrop-blur-xl p-8 sm:p-12 shadow-2xl border border-white/60 text-center overflow-hidden">

            <!-- Decorative Background Glow -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-[#0C2C6B]/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-[#D79B10]/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Animated Icon Container -->
            <div class="relative mx-auto flex h-24 w-24 sm:h-28 sm:w-28 items-center justify-center rounded-3xl bg-gradient-to-tr from-amber-50 to-amber-100/80 border border-amber-200/60 animate-float shadow-lg shadow-amber-500/10 mb-8">
                <div class="absolute inset-0 rounded-3xl bg-[#D79B10]/5 animate-pulse-subtle"></div>
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-12 w-12 sm:h-14 sm:w-14 text-[#D79B10] relative z-10"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="1.8"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>

            <!-- Title & Description -->
            <div class="space-y-3 relative z-10">
                <span class="inline-block px-3.5 py-1 text-xs font-bold uppercase tracking-widest text-[#0C2C6B] bg-blue-50 border border-blue-100 rounded-full">
                    System Maintenance
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-[#0C2C6B] tracking-tight">
                    We'll Be Back Soon!
                </h1>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-md mx-auto">
                    We are currently performing scheduled maintenance to improve our services and user experience. Please check back shortly.
                </p>
            </div>

            <!-- Footer Meta -->
            <div class="mt-8 pt-6 border-t border-slate-100 text-xs text-slate-400 font-medium relative z-10">
                &copy; {{ date('Y') }} True Life Dayongan. All rights reserved.
            </div>

        </div>
    </div>

</body>
</html>
