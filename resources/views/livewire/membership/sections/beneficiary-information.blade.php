<div>

    <h3 class="bg-brand-navy text-white px-4 py-2 rounded font-bold">
        ACCEPTANCE OF TERMS
    </h3>

    <div class="mt-4">

        <label class="block text-sm font-bold mb-2">
            Terms and Conditions
        </label>

        <textarea
            readonly
            class="w-full h-48 border border-gray-300 rounded-lg p-4 bg-gray-50 resize-none">

I acknowledge that the information provided in this application is true, complete, and correct to the best of my knowledge.

I understand that any false, misleading, or incomplete information may result in the rejection of my application or the cancellation of my membership.

By submitting this application, I voluntarily agree to comply with the rules, regulations, policies, and future amendments established by Dayongan Solidaritas.

I also authorize Dayongan Solidaritas to collect, verify, store, and process my personal information solely for membership administration and organizational purposes in accordance with applicable data privacy laws.

        </textarea>

    </div>

    <div class="mt-5">

        <label class="flex items-start gap-3 cursor-pointer">

            <input
                type="checkbox"
                wire:model="agree"
                class="mt-1 h-5 w-5 rounded border-gray-300 text-brand-gold focus:ring-brand-gold">

            <span class="text-sm">
                I have carefully read and understood the Terms and Conditions.
                I voluntarily agree to all the provisions stated above.
            </span>

        </label>

        @error('agree')
            <p class="text-red-500 text-sm mt-2">
                {{ $message }}
            </p>
        @enderror

    </div>

</div>
