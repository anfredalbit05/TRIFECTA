<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="color-scheme: light;">
    <head>
        @include('partials.head')

        <style>
            #forgot-email::placeholder {
                color: #94a3b8;
                opacity: 1;
            }
        </style>
    </head>
    <body class="min-h-screen bg-slate-100 text-slate-800 antialiased" style="background: linear-gradient(160deg, #f1f5f9 0%, #e2e8f0 100%);">
        <div class="flex min-h-screen flex-col items-center justify-center gap-4 p-4 sm:p-6">
            <div class="w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">

                {{-- Brand Header --}}
                <div class="bg-[#0B2A5B] px-6 py-7 text-center text-white">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#D4A017] text-xl font-black text-[#0B2A5B] shadow-lg">
                        D
                    </div>
                    <h1 class="text-xl font-black">Forgot Password</h1>
                    <p class="mt-1 text-xs text-slate-300">Enter your account email to receive a reset link</p>
                </div>

                <div class="p-6 sm:p-8">
                    {{-- Status message after a reset link was sent --}}
                    @if (session('status'))
                        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">
                            ✅ {{ session('status') }}
                        </div>
                    @endif

                    {{-- Validation errors --}}
                    @if ($errors->any())
                        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3">
                            <p class="text-xs font-bold text-rose-600 mb-1">Please fix the following:</p>
                            <ul class="list-disc pl-4 text-xs text-rose-600 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-4">
                        @csrf

                        <div>
                            <label for="forgot-email" class="mb-1 block text-xs font-bold text-slate-700">Account Email Address</label>
                            <input
                                id="forgot-email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="name@example.com"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 focus:ring-2 focus:ring-[#0B2A5B] focus:border-[#0B2A5B] focus:outline-none transition"
                            >
                        </div>

                        <button
                            type="submit"
                            class="mt-2 w-full cursor-pointer rounded-xl bg-[#0B2A5B] py-3.5 text-sm font-bold text-white shadow-lg transition hover:bg-[#071D3F]"
                        >
                            Send Password Reset Link
                        </button>
                    </form>

                    <p class="mt-5 text-center text-xs text-slate-400">
                        Remembered your password?
                        <a href="{{ route('home') }}" class="font-bold text-[#0B2A5B] hover:underline">Return to sign in</a>
                    </p>
                </div>
            </div>

            <p class="text-[11px] text-slate-400">TrueLife Solidaritas • Vitae Auxilium</p>
        </div>
    </body>
</html>
