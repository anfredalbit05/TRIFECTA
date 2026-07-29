<div class="masterlist-overview">

    <h2 class="masterlist-title text-2xl font-bold text-gray-800 mb-1">
        Dashboard
    </h2>

    <p class="masterlist-description text-gray-500 mb-6">
        Masterlist overview information.
    </p>

    <div class="master-summary-grid grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Total Members Card -->
        <div class="master-summary-card bg-white p-6 rounded-xl shadow-md border border-gray-100 flex flex-col justify-between">
            <h3 class="text-sm font-semibold tracking-wide text-gray-500 uppercase">
                Total Members
            </h3>
            <p class="text-3xl font-extrabold text-[#0C2C6B] mt-2">
                {{ $totalMembers }}
            </p>
        </div>

        {{-- <!-- Active Branches Card -->
        <div class="master-summary-card bg-white p-6 rounded-xl shadow-md border border-gray-100 flex flex-col justify-between">
            <h3 class="text-sm font-semibold tracking-wide text-gray-500 uppercase">
                Active Branches
            </h3>
            <p class="text-3xl font-extrabold text-[#0C2C6B] mt-2">
                {{ $activeBranches }}
            </p>
        </div> --}}

        <!-- Benefits Card -->
        <div class="master-summary-card bg-white p-6 rounded-xl shadow-md border border-gray-100 flex flex-col justify-between">
            <h3 class="text-sm font-semibold tracking-wide text-gray-500 uppercase">
                Benefits
            </h3>
            <p class="text-3xl font-extrabold text-[#0C2C6B] mt-2">
                {{ $totalBenefits }}
            </p>
        </div>

    </div>

</div>
