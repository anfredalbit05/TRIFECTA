<div class="overview-container w-full px-4 sm:px-6 lg:px-8 py-6">

   <!-- HEADER SECTION WITH REFRESH BUTTON -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#0C2C6B]">
                Overview
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                View and manage overview information.
            </p>
        </div>

        <!-- REFRESH BUTTON -->
        <button wire:click="refreshOverview" wire:loading.attr="disabled" title="Refresh Overview"
            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl shadow-sm transition cursor-pointer self-start sm:self-auto">
            <span wire:loading.remove wire:target="refreshOverview">🔄</span>
            <span wire:loading wire:target="refreshOverview" class="animate-spin">⏳</span>
            <span>Refresh</span>
        </button>
    </div>

    <!-- SUMMARY METRICS GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">

        <!-- Total Testimonials Card -->
        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold tracking-wide text-gray-500 uppercase">
                    Published Testimonials
                </h3>
                <p class="text-3xl font-extrabold text-[#0C2C6B] mt-2">
                    {{ $totalTestimonials }}
                </p>
            </div>
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-50 text-[#0C2C6B] text-xl">
                💬
            </div>
        </div>

        <!-- Total News & Updates Card -->
        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold tracking-wide text-gray-500 uppercase">
                    Published News & Updates
                </h3>
                <p class="text-3xl font-extrabold text-[#0C2C6B] mt-2">
                    {{ $totalNews }}
                </p>
            </div>
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-50 text-[#0C2C6B] text-xl">
                📰
            </div>
        </div>

    </div>

    <!-- MAIN CONTENT CARD -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-[#0C2C6B] mb-4 text-2xl">
                📊
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Overview Panel Content</h3>
            <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                This space is ready for your overview metrics, analytics, or summary details.
            </p>
        </div>
    </div>

</div>
