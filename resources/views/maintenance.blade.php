<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Inactive</title>

    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-100">

    <div class="flex min-h-screen items-center justify-center p-6">

        <div class="w-full max-w-lg rounded-3xl bg-white p-10 shadow-lg text-center">

            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-amber-100">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-10 w-10 text-amber-600"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 8v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
                </svg>
            </div>

            <h1 class="mt-6 text-3xl font-bold text-slate-900">
                Account Inactive
            </h1>

            <p class="mt-3 text-slate-600">
                Your account is currently inactive. Please wait for activation or contact the administrator.
            </p>

        </div>

    </div>

</body>
</html>
