<div class="w-full space-y-8">

    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between text-center sm:text-left">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                Personal Information
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Manage your personal details and profile picture.
            </p>
        </div>
    </div>

    <!-- Success Messages -->
    @if (session()->has('profile-success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-sm font-medium">
                {{ session('profile-success') }}
            </p>
        </div>
    @endif

    @if (session()->has('picture-success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-sm font-medium">
                {{ session('picture-success') }}
            </p>
        </div>
    @endif

    <!-- Profile Picture Card -->
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5 text-center sm:text-left">
            <h3 class="font-semibold text-slate-900">
                Profile Picture
            </h3>
            <p class="mt-1 text-sm text-slate-500">
                Upload a clear profile picture. Maximum file size is 2 MB.
            </p>
        </div>

        <div class="flex flex-col items-center sm:items-start gap-6 px-6 py-6 sm:flex-row sm:items-center text-center sm:text-left">
            <!-- Picture -->
            <div class="h-28 w-28 shrink-0 overflow-hidden rounded-full border-4 border-slate-100 bg-slate-100 shadow-sm mx-auto sm:mx-0">
                @php
                    $profilePath = auth()->user()->profilepic;
                    $defaultAvatar = 'profiles/default-avatar.png';
                @endphp

                @if ($profilePath && $profilePath !== $defaultAvatar)
                    <img src="{{ asset($profilePath) }}" class="h-full w-full object-cover" alt="Profile picture">
                @else
                    <img src="{{ asset($defaultAvatar) }}" class="h-full w-full object-cover" alt="Default profile picture">
                @endif
            </div>

            <!-- Upload Controls -->
            <div class="flex-1 w-full flex flex-col items-center sm:items-start">
               <label for="profilepic" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl px-5 py-3 text-sm font-semibold text-white transition w-full sm:w-auto" style="background-color: #0C2C6B;" onmouseover="this.style.backgroundColor='#071a42'" onmouseout="this.style.backgroundColor='#0C2C6B'">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 12v6m0-6l-3 3m3-3l3 3M12 4v8" />
    </svg>
    Choose New Picture
</label>

                <input id="profilepic" type="file" wire:model="profilepic" accept="image/*" class="hidden">

                <p class="mt-3 text-xs text-slate-500">
                    JPG, PNG, WEBP, or GIF. Maximum size: 2 MB.
                </p>

                @error('profilepic')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                <div wire:loading wire:target="profilepic" class="mt-3 text-sm text-slate-500">
                    Uploading image...
                </div>

                @if ($profilepic)
                    <button type="button" wire:click="updateProfilePicture" wire:loading.attr="disabled" wire:target="updateProfilePicture" class="mt-4 inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60 w-full sm:w-auto">
                        <span wire:loading.remove wire:target="updateProfilePicture">
                            Save Profile Picture
                        </span>
                        <span wire:loading wire:target="updateProfilePicture">
                            Saving...
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Personal Information Card -->
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5 text-center sm:text-left">
            <h3 class="font-semibold text-slate-900">
                Personal Details
            </h3>
            <p class="mt-1 text-sm text-slate-500">
                Keep your personal information accurate and up to date.
            </p>
        </div>

        <form wire:submit="saveProfile">
            <div class="grid gap-5 p-6 md:grid-cols-2 text-left">
                <!-- First Name -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        First Name
                    </label>
                    <input type="text" wire:model="firstname" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100">
                    @error('firstname')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Middle Name -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Middle Name
                    </label>
                    <input type="text" wire:model="middlename" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100">
                    @error('middlename')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Last Name -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Last Name
                    </label>
                    <input type="text" wire:model="lastname" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100">
                    @error('lastname')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Birthdate -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Birthdate
                    </label>
                    <input type="date" wire:model="birthdate" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100">
                    @error('birthdate')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Contact -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Contact Number
                    </label>
                    <input type="text" wire:model="contactno" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100">
                    @error('contactno')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Address
                    </label>
                    <textarea wire:model="address" rows="4" class="w-full resize-none rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-slate-500 focus:ring-4 focus:ring-slate-100"></textarea>
                    @error('address')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-center sm:justify-end border-t border-slate-200 bg-slate-50 px-6 py-5">
              <button type="submit" wire:loading.attr="disabled" wire:target="saveProfile" class="rounded-xl bg-[#0C2C6B] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#071a42] disabled:opacity-60 w-full sm:w-auto">
    <span wire:loading.remove wire:target="saveProfile">
        Save Personal Information
    </span>
    <span wire:loading wire:target="saveProfile">
        Saving...
    </span>
</button>
            </div>
        </form>
    </div>
</div>
