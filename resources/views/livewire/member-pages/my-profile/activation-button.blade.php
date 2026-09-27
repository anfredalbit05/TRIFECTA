<div>
    @if(auth()->user()->RoleID == 4)
        <div class="ml-auto my-2 sm:my-0 mr-2 sm:mr-2">
            @if($activationStatus === null)

                <button
                    type="button"
                    onclick="Livewire.dispatch('open-activation-modal')"
                    class="activate-account-btn px-4 py-2 bg-[#0C2C6B] text-white rounded-xl font-bold text-xs sm:text-sm shadow-md transition duration-300">
                    Activate Account
                </button>

            @elseif($activationStatus === 'Pending')

                <button
                    disabled
                    class="px-4 py-2 bg-yellow-500 text-white rounded-xl font-bold text-xs sm:text-sm cursor-not-allowed">
                    Waiting for Review
                </button>

            @elseif($activationStatus === 'Approved')

                <button
                    disabled
                    class="px-4 py-2 bg-[#0C2C6B] text-white rounded-xl font-bold text-xs sm:text-sm cursor-not-allowed flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    Approved
                </button>

            @elseif($activationStatus === 'Rejected')

                <button
                    type="button"
                    onclick="Livewire.dispatch('open-rejected-activation')"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-xs sm:text-sm">
                    Resubmit Activation
                </button>

            @endif
        </div>
    @endif
</div>
