<div class="w-full space-y-8">

    <!-- Header -->
    <div>

        <h2 class="text-2xl font-bold tracking-tight text-slate-900">
            Security
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Update your password to keep your account secure.
        </p>

    </div>


    <!-- Success Message -->
    @if (session()->has('security-success'))

        <div
            class="flex items-center gap-3
            rounded-2xl border
            border-emerald-200
            bg-emerald-50 px-5 py-4
            text-emerald-800">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 shrink-0"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 13l4 4L19 7" />

            </svg>

            <p class="text-sm font-medium">
                {{ session('security-success') }}
            </p>

        </div>

    @endif


    <!-- Security Notice -->
    <div
        class="flex items-start gap-4
        rounded-2xl border
        border-amber-200
        bg-amber-50 p-5">

        <div
            class="flex h-10 w-10
            shrink-0 items-center
            justify-center rounded-xl
            bg-amber-100
            text-amber-700">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 9v2m0 4h.01
                    M5.07 19h13.86
                    c1.54 0 2.5-1.67 1.73-3
                    L13.73 4
                    c-.77-1.33-2.69-1.33-3.46 0
                    L3.34 16
                    c-.77 1.33.19 3 1.73 3z" />

            </svg>

        </div>


        <div>

            <h3 class="font-semibold text-amber-900">
                Keep your account secure
            </h3>

            <p
                class="mt-1 text-sm
                leading-6 text-amber-800">

                Use a strong password with at least
                8 characters. Avoid using personal
                information or passwords you use
                on other websites.

            </p>

        </div>

    </div>


    <!-- Change Password Card -->
    <div
        class="overflow-hidden rounded-2xl
        border border-slate-200
        bg-white shadow-sm">

        <div
            class="border-b border-slate-200
            bg-slate-50 px-6 py-5">

            <h3 class="font-semibold text-slate-900">
                Change Password
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Enter your current password and choose a new password.
            </p>

        </div>


        <form wire:submit="changePassword">

            <div
                class="mx-auto max-w-2xl
                space-y-6 p-6">

              <!-- Current Password -->
<div x-data="{ show: false }">
    <label class="mb-2 block text-sm font-medium text-slate-700">
        Current Password
    </label>
    <div class="relative">
        <input
            :type="show ? 'text' : 'password'"
            wire:model="currentPassword"
            autocomplete="current-password"
            class="w-full rounded-xl border border-slate-300 px-4 py-3 pr-10 text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100">
        <button
            type="button"
            @click="show = !show"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
            <!-- Eye Icon (Show) -->
            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 cursor-pointer">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <!-- Eye Slash Icon (Hide) -->
            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 cursor-pointer" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
            </svg>
        </button>
    </div>
    @error('currentPassword')
        <p class="mt-2 text-xs text-red-600">
            {{ $message }}
        </p>
    @enderror
</div>

<!-- New Password -->
<div x-data="{ show: false }">
    <label class="mb-2 block text-sm font-medium text-slate-700">
        New Password
    </label>
    <div class="relative">
        <input
            :type="show ? 'text' : 'password'"
            wire:model="newPassword"
            autocomplete="new-password"
            class="w-full rounded-xl border border-slate-300 px-4 py-3 pr-10 text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100">
        <button
            type="button"
            @click="show = !show"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
            <!-- Eye Icon (Show) -->
            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 cursor-pointer">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <!-- Eye Slash Icon (Hide) -->
            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 cursor-pointer" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
            </svg>
        </button>
    </div>
    @error('newPassword')
        <p class="mt-2 text-xs text-red-600">
            {{ $message }}
        </p>
    @enderror
</div>

<!-- Confirm Password -->
<div x-data="{ show: false }">
    <label class="mb-2 block text-sm font-medium text-slate-700">
        Confirm New Password
    </label>
    <div class="relative">
        <input
            :type="show ? 'text' : 'password'"
            wire:model="newPasswordConfirmation"
            autocomplete="new-password"
            class="w-full rounded-xl border border-slate-300 px-4 py-3 pr-10 text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100">
        <button
            type="button"
            @click="show = !show"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
            <!-- Eye Icon (Show) -->
            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 cursor-pointer">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <!-- Eye Slash Icon (Hide) -->
            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 cursor-pointer" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
            </svg>
        </button>
    </div>
    @error('newPasswordConfirmation')
        <p class="mt-2 text-xs text-red-600">
            {{ $message }}
        </p>
    @enderror
</div>

            </div>


            <div
                class="flex justify-end
                border-t border-slate-200
                bg-slate-50 px-6 py-5">

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="changePassword"
                    class="rounded-xl bg-[#0C2C6B] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#071a42] disabled:opacity-60">
                    <span wire:loading.remove wire:target="changePassword">
                        Change Password
                    </span>
                    <span wire:loading wire:target="changePassword">
                        Updating...
                    </span>
                </button>

            </div>

        </form>

    </div>

</div>
