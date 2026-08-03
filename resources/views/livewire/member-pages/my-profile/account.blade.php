<div class="w-full space-y-8">

    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">
            Account Information
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Manage your username, email address, and account details.
        </p>
    </div>

    <!-- Success Message -->
    @if (session()->has('account-success'))
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-sm font-medium">
                {{ session('account-success') }}
            </p>
        </div>
    @endif

    <!-- Account Form Card -->
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
            <h3 class="font-semibold text-slate-900">
                Login Details
            </h3>
            <p class="mt-1 text-sm text-slate-500">
                These details are used to access your account.
            </p>
        </div>

        <form wire:submit="save">
            <div class="grid gap-5 p-6 md:grid-cols-2">
                <!-- Username -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Username
                    </label>
                    <input
                        type="text"
                        wire:model="username"
                        autocomplete="username"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100">

                    @error('username')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Email Address
                    </label>
                    <input
                        type="email"
                        wire:model="email"
                        autocomplete="email"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100">

                    @error('email')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end border-t border-slate-200 bg-slate-50 px-6 py-5">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:opacity-60">

                    <span wire:loading.remove wire:target="save">
                        Save Account Information
                    </span>
                    <span wire:loading wire:target="save">
                        Saving...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Read-only Account Information Card -->
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
            <h3 class="font-semibold text-slate-900">
                Account Details
            </h3>
            <p class="mt-1 text-sm text-slate-500">
                Information managed by the system.
            </p>
        </div>

        <div class="grid divide-y divide-slate-200 md:grid-cols-2 md:divide-x md:divide-y-0">
            <!-- Status -->
            <div class="p-6">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                    Account Status
                </p>
                <div class="mt-3">
                    <span @class([
                        'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                        'bg-emerald-100 text-emerald-700' => auth()->user()->status === 'Active',
                        'bg-amber-100 text-amber-700' => auth()->user()->status === 'Inactive',
                        'bg-red-100 text-red-700' => auth()->user()->status === 'Suspended',
                    ])>
                        {{ auth()->user()->status }}
                    </span>
                </div>
            </div>

            <!-- Referral Code -->
            <div class="p-6">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                    Referral Code
                </p>
                <p class="mt-3 break-all font-mono text-sm font-semibold text-slate-800">
                    {{ auth()->user()->referralCode ?? 'Not available' }}
                </p>
            </div>
        </div>

        <!-- Referral QR & Link Section -->
        <div class="border-t border-slate-200 p-6">
            <div class="flex flex-col items-center">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    {!! $qrCode !!}
                </div>

                <p class="mt-4 text-xs text-slate-500 text-center">
                    Scan to register using your referral code.
                </p>

                <div class="mt-4 w-full max-w-md rounded-xl bg-slate-100 px-4 py-3">
                    <p class="break-all text-center text-sm font-medium text-slate-700">
                        {{ $referralUrl }}
                    </p>
                </div>

                <button
                    type="button"
                    onclick="navigator.clipboard.writeText('{{ $referralUrl }}')"
                    class="mt-4 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Copy Referral Link
                </button>
            </div>
        </div>
    </div>

</div>
