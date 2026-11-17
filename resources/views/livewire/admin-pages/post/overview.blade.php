<div class="overview-container w-full px-4 sm:px-6 lg:px-8 py-6">

   <!-- HEADER SECTION WITH REFRESH BUTTON -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[var(--color-primary)]">
                Overview
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                View and manage overview information.
            </p>
        </div>

        {{-- REFRESH BUTTON --}}
        <button
            type="button"
            wire:click="refreshOverview"
            wire:loading.attr="disabled"
            title="Refresh Overview"
            class="refresh-btn"
        >
            {{-- NORMAL STATE --}}
            <span
                wire:loading.remove
                wire:target="refreshOverview"
                class="refresh-icon"
            >
                <svg
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 4v5h5M20 20v-5h-5M5.5 9A7 7 0 0 1 18 6.5L20 9M18.5 15A7 7 0 0 1 6 17.5L4 15"
                    />
                </svg>
            </span>

            {{-- LOADING STATE --}}
            <span
                wire:loading
                wire:target="refreshOverview"
                class="refresh-loading"
            >
                <svg
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <circle
                        cx="12"
                        cy="12"
                        r="9"
                        stroke-width="3"
                        class="refresh-circle"
                    />
                    <path
                        d="M21 12a9 9 0 0 1-9 9"
                        stroke-width="3"
                        stroke-linecap="round"
                    />
                </svg>
            </span>

            <span>Refresh</span>
        </button>
    </div>

   <!-- SUMMARY METRICS GRID (Dark Glassmorphism with var(--color-primary) Base, Watermark Icons Only) -->
@php
    $cards = [
        ['Published Testimonials', $totalTestimonials, '💬', 'hover:shadow-[0_18px_40px_-12px_var(--color-primary)]'],
        ['Published News & Updates', $totalNews, '📰', 'hover:shadow-[0_18px_40px_-12px_var(--color-primary)]'],
    ];
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
    @foreach ($cards as $card)
        <div class="relative overflow-hidden rounded-2xl p-6 backdrop-blur-xl bg-gradient-to-br from-[var(--color-primary)] via-[var(--color-primary-mid)] to-[var(--color-primary-mid)] border border-white/10 {{ $card[3] }} shadow-xl transition-all duration-300 hover:-translate-y-1.5 group text-white">

            <!-- Large Background Overlay Icon Watermark -->
            <div class="absolute -right-3 -bottom-3 text-7xl text-white opacity-10 transition-transform duration-500 group-hover:scale-125 group-hover:opacity-20 pointer-events-none select-none brightness-0 invert">
                {{ $card[2] }}
            </div>

            <!-- Content Container -->
            <div class="relative z-10">
                <h3 class="text-sm font-semibold tracking-wide text-white uppercase">
                    {{ $card[0] }}
                </h3>
                <p class="text-3xl font-extrabold text-white mt-2 tracking-tight drop-shadow-sm">
                    {{ number_format($card[1]) }}
                </p>
            </div>

        </div>
    @endforeach
</div>

    <!-- MAIN CONTENT CARD -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
        <div class="text-center py-12">
            <h3 class="text-lg font-semibold text-gray-800">Overview Panel Content</h3>
            <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                This space is ready for your overview metrics, analytics, or summary details.
            </p>
        </div>
    </div>

</div>
