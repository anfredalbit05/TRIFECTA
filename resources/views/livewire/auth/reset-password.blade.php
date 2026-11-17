<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scheme-light">
    <head>
        @include('partials.head')

    </head>
    <body class="min-h-screen bg-slate-100 bg-[linear-gradient(160deg,#f1f5f9_0%,#e2e8f0_100%)] text-slate-800 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center gap-4 p-4 sm:p-6">
            <div class="w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">

                {{-- Brand Header --}}
                <div class="border-b-[3px] border-[var(--color-gold)] bg-[var(--color-primary)] px-6 py-7 text-center text-white">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-[var(--color-gold)] text-xl font-black text-[var(--color-primary)] shadow-lg">
                        D
                    </div>
                    <h1 class="text-xl font-black">Set New Password</h1>
                    <p class="mt-1 text-xs text-white">Choose a new password for your TRIFECTA member portal</p>
                </div>

                <div class="p-6 sm:p-8">
                    {{-- Success / status message --}}
                    @if (session('status'))
                        <div class="mb-4 rounded-xl border border-[var(--color-gold-tint-border)] bg-[var(--color-gold-tint)] px-4 py-3 text-xs font-semibold text-[var(--color-gold-darker)]">
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

                    @if (!request()->has('email'))
                        {{-- Token link opened without an email address --}}
                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-4 text-xs font-medium text-amber-800">
                            This reset link is missing your email address. Please
                            <a href="{{ route('home') }}" class="font-bold underline">go back to the home page</a>
                            and request a new reset link so you can sign in again.
                        </div>
                    @else
                        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-4">
                            @csrf

                            <input type="hidden" name="token" value="{{ request()->route('token') }}">

                            {{-- Email --}}
                            <div>
                                <label for="reset-email" class="mb-1 block text-xs font-bold text-slate-700">Account Email</label>
                                <input
                                    id="reset-email"
                                    name="email"
                                    type="email"
                                    value="{{ request('email') }}"
                                    readonly
                                    class="w-full border border-slate-300 rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-500 focus:outline-none"
                                >
                            </div>

                            {{-- New Password (with visibility toggle) --}}
                            <div>
                                <label for="reset-password" class="mb-1 block text-xs font-bold text-slate-700">New Password</label>
                                <div class="relative">
                                    <input
                                        id="reset-password"
                                        name="password"
                                        type="password"
                                        required
                                        autocomplete="new-password"
                                        placeholder="••••••••"
                                        class="w-full border border-slate-300 rounded-xl bg-white px-4 py-3 pr-10 text-sm text-slate-800 focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] focus:outline-none transition"
                                    >
                                    <button type="button" class="pw-toggle absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer"
                                        data-target="reset-password" tabindex="-1" aria-label="Show or hide password">
                                        <svg data-ico="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <svg data-ico="hide" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    @if (app()->isProduction())
                                        At least 12 characters, with uppercase &amp; lowercase letters, numbers, and symbols.
                                    @else
                                        Minimum of 8 characters.
                                    @endif
                                </p>
                            </div>

                            {{-- Confirm New Password (with visibility toggle) --}}
                            <div>
                                <label for="reset-password-confirm" class="mb-1 block text-xs font-bold text-slate-700">Confirm New Password</label>
                                <div class="relative">
                                    <input
                                        id="reset-password-confirm"
                                        name="password_confirmation"
                                        type="password"
                                        required
                                        autocomplete="new-password"
                                        placeholder="••••••••"
                                        class="w-full border border-slate-300 rounded-xl bg-white px-4 py-3 pr-10 text-sm text-slate-800 focus:ring-2 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] focus:outline-none transition"
                                    >
                                    <button type="button" class="pw-toggle absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer"
                                        data-target="reset-password-confirm" tabindex="-1" aria-label="Show or hide password">
                                        <svg data-ico="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <svg data-ico="hide" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="mt-2 w-full cursor-pointer rounded-xl bg-[var(--color-gold)] py-3.5 text-sm font-extrabold text-[var(--color-primary)] shadow-lg transition hover:bg-[var(--color-gold-light)]"
                            >
                                Reset Password
                            </button>
                        </form>

                        <p class="mt-5 text-center text-xs text-slate-500">
                            Remembered your password?
                            <a href="{{ route('home') }}" class="font-bold text-[var(--color-primary)] hover:underline">Return to sign in</a>
                        </p>
                    @endif
                </div>
            </div>

            <p class="text-[11px] text-slate-400">Health &middot; Wellness &middot; Protection</p>
        </div>

        @vite('resources/js/livewire/auth/reset-password.js')
    </body>
</html>
