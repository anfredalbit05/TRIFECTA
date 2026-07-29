<div class="profile-upload-section">

    <form wire:submit="save">

        <div class="profile-image-box">

            {{-- Clickable image --}}
            <label
                for="profilepic"
                class="profile-image-label">

                @if ($profilepic)

                    {{-- Preview selected image before saving --}}
                    <img
                        src="{{ $profilepic->temporaryUrl() }}"
                        class="profile-image"
                        alt="New profile picture">

                @elseif (auth()->user()->profilepic)

                    {{-- Existing saved profile image --}}
                    <img
                        src="{{ asset('storage/' . auth()->user()->profilepic) }}"
                        class="profile-image"
                        alt="Profile picture">

                @else

                    {{-- Default image --}}
                    <img
                        src="{{ asset('images/user.png') }}"
                        class="profile-image"
                        alt="Default profile picture">

                @endif

                {{-- Change icon --}}
                <div class="profile-camera-icon">
                    📷
                </div>

            </label>

            {{-- Hidden file selector --}}
            <input
                id="profilepic"
                type="file"
                wire:model="profilepic"
                accept="image/png,image/jpeg,image/jpg,image/webp"
                class="hidden">

        </div>

        @error('profilepic')
            <p class="profile-error">
                {{ $message }}
            </p>
        @enderror

        {{-- Show this only after selecting an image --}}
        @if ($profilepic)

            <button
                type="submit"
                class="save-profile-picture"
                wire:loading.attr="disabled">

                <span wire:loading.remove>
                    Save Profile Picture
                </span>

                <span wire:loading>
                    Uploading...
                </span>

            </button>

        @endif

    </form>

</div>
