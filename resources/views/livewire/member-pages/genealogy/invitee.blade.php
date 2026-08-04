<div class="w-full bg-slate-50 min-h-[500px] p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm relative flex flex-col justify-between"
     x-data="{
         scale: 1,
         isFullscreen: false,
         zoomIn() { this.scale = Math.min(this.scale + 0.15, 1.6); },
         zoomOut() { this.scale = Math.max(this.scale - 0.15, 0.5); },
         resetZoom() { this.scale = 1; },
         toggleFullscreen() {
             this.isFullscreen = !this.isFullscreen;
             if (this.isFullscreen) {
                 if (this.$el.requestFullscreen) { this.$el.requestFullscreen(); }
             } else {
                 if (document.exitFullscreen) { document.exitFullscreen(); }
             }
         }
     }"
     @fullscreenchange.window="isFullscreen = !!document.fullscreenElement"
     :class="{ 'fixed inset-0 z-50 bg-slate-50 p-6 overflow-y-auto rounded-none border-0': isFullscreen }"
>
    <div>
        {{-- Top Action Bar (Hidden during Fullscreen) --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-200"
             :class="{ 'hidden': isFullscreen }">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Genealogy Tree</h2>
                <p class="text-xs text-slate-500">Visualizing direct referrals and downlines</p>
            </div>

            @if(isset($tree['id']) && $tree['id'] !== auth()->id())
                <button
                    wire:click="focusUser({{ auth()->id() }})"
                    class="px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors border border-blue-200 self-start sm:self-auto"
                >
                    ↺ Reset to My Node
                </button>
            @endif
        </div>

        {{-- Tree Viewport Container --}}
        <div class="w-full overflow-x-auto py-6 pb-20 relative transition-all duration-200">

            {{-- Exit Fullscreen absolute button if active --}}
            <template x-if="isFullscreen">
                <button @click="toggleFullscreen()" class="absolute top-4 right-4 px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-semibold shadow-md z-30">
                    Exit Fullscreen ✕
                </button>
            </template>

            @if(!empty($tree))
                {{-- Tree wrapper scaling dynamically based on Alpine scale state --}}
                <div class="flex justify-center min-w-max mx-auto px-4 transform-gpu origin-top transition-transform duration-150"
                     :style="`transform: scale(${scale});`">
                    @include('livewire.member-pages.genealogy.partials.tree-node', [
                        'node' => $tree,
                        'isRoot' => true
                    ])
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-16 text-slate-400 space-y-2">
                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <p class="text-sm font-medium">No downline record found for this account.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Floating Bottom Control Toolbar --}}
    <div class="sticky bottom-4 left-0 right-0 flex justify-center z-20 pointer-events-none mt-4">
        <div class="pointer-events-auto flex items-center gap-1.5 bg-white/90 backdrop-blur-md p-2 rounded-2xl border border-slate-200 shadow-lg">
            {{-- Search Input --}}
            {{-- <div class="relative flex items-center">
                <input
                    type="text"
                    placeholder="Search downline..."
                    class="w-44 sm:w-56 text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-600 transition-all"
                >
            </div> --}}

            {{-- Search Button --}}
            {{-- <button class="p-2 text-white bg-teal-800 hover:bg-teal-900 rounded-xl transition-colors shadow-sm flex items-center justify-center" title="Search">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button> --}}

            <div class="h-5 w-px bg-slate-200 mx-0.5"></div>

            {{-- Zoom In Button --}}
            <button @click="zoomIn()" class="p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors" title="Zoom In">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                </svg>
            </button>

            {{-- Zoom Out Button --}}
            <button @click="zoomOut()" class="p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors" title="Zoom Out">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/>
                </svg>
            </button>

            {{-- Fullscreen Toggle Button --}}
            {{-- <button @click="toggleFullscreen()" class="p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors" title="Fullscreen">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                </svg>
            </button> --}}
        </div>
    </div>
</div>
