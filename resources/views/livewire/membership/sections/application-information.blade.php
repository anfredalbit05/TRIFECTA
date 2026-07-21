<div>

    <h3 class="bg-brand-navy text-white px-4 py-2 rounded font-bold">
        APPLICANT INFORMATION
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">

        <!-- Surname -->
        <div>
            <label class="block text-sm font-bold mb-1">
                Surname
            </label>

            <input
                wire:model="surname"
                type="text"
                placeholder="Enter surname"
                class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
        </div>

        <!-- Middle Name -->
        <div>
            <label class="block text-sm font-bold mb-1">
                Middle Name
            </label>

            <input
                wire:model="middle_name"
                type="text"
                placeholder="Enter middle name"
                class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
        </div>

        <!-- First Name -->
        <div>
            <label class="block text-sm font-bold mb-1">
                First Name
            </label>

            <input
                wire:model="first_name"
                type="text"
                placeholder="Enter first name"
                class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
        </div>

        <!-- Birthdate -->
        <div>
            <label class="block text-sm font-bold mb-1">
                Birthdate
            </label>

            <input
                wire:model="birthdate"
                type="date"
                class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
        </div>

        <!-- Address -->
        <div class="md:col-span-2">
            <label class="block text-sm font-bold mb-1">
                Address
            </label>

            <input
                wire:model="address"
                type="text"
                placeholder="Complete address"
                class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
        </div>

        <!-- Mobile Number -->
        <div>
            <label class="block text-sm font-bold mb-1">
                Mobile Number
            </label>

            <input
                wire:model="mobile"
                type="text"
                placeholder="09XXXXXXXXX"
                class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-bold mb-1">
                Email Address
            </label>

            <input
                wire:model="email"
                type="email"
                placeholder="example@email.com"
                class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-brand-gold focus:border-brand-gold">
        </div>

    </div>

</div>
