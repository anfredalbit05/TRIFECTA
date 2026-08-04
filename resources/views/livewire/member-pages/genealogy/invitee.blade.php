<div class="w-full bg-slate-50 min-h-[500px] p-6 rounded-2xl border border-slate-200/80 shadow-sm"
     x-data="{
         scale: 0.75,
         isFullscreen: false,
         panning: false,
         startX: 0,
         startY: 0,
         translateX: 0,
         translateY: 0,

         zoomIn() {
             this.scale = Math.min(this.scale + 0.15, 1.6);
         },
         zoomOut() {
             this.scale = Math.max(this.scale - 0.15, 0.4);
         },
         resetZoom() {
             this.scale = 0.75;
             this.translateX = 0;
             this.translateY = 0;
         },

         startPan(e) {
             if (e.target.closest('button') || e.target.closest('input')) return;
             this.panning = true;
             // Support both mouse and touch events
             const clientX = e.touches ? e.touches[0].clientX : e.clientX;
             const clientY = e.touches ? e.touches[0].clientY : e.clientY;
             this.startX = clientX - this.translateX;
             this.startY = clientY - this.translateY;
         },
         pan(e) {
             if (!this.panning) return;
             const clientX = e.touches ? e.touches[0].clientX : e.clientX;
             const clientY = e.touches ? e.touches[0].clientY : e.clientY;
             this.translateX = clientX - this.startX;
             this.translateY = clientY - this.startY;
         },
         endPan() {
             this.panning = false;
         },

         toggleFullscreen() {
             this.isFullscreen = !this.isFullscreen;
             if (this.isFullscreen) {
                 if (this.$el.requestFullscreen) { this.$el.requestFullscreen(); }
             } else {
                 if (document.exitFullscreen) { document.exitFullscreen(); }
             }
         }
     }"
     @mousedown="startPan"
     @mousemove="pan"
     @mouseup="endPan"
     @mouseleave="endPan"
     @touchstart="startPan"
     @touchmove="pan"
     @touchend="endPan"
     @fullscreenchange.window="isFullscreen = !!document.fullscreenElement"
     :class="{ 'fixed inset-0 z-50 bg-slate-50 p-4 sm:p-6 overflow-y-auto rounded-none border-0': isFullscreen }"
>
    <div>
        {{-- Top Action Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-200"
             :class="{ 'hidden': isFullscreen }">
            <div >
                <h2 class="text-lg font-bold text-slate-800">Genealogy Tree</h2>
                <p class="text-xs text-slate-500">Visualizing direct referrals and downlines</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">


                @if(isset($tree['id']) && $tree['id'] !== auth()->id())
                    <button
                        wire:click="focusUser({{ auth()->id() }})"
                        class="px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors border border-blue-200"
                    >
                        ↺ Reset to My Node
                    </button>
                @endif
            </div>
        </div>

     {{-- Tree Viewport Container --}}
        <div class="w-full overflow-hidden py-12 px-4 sm:px-12 relative transition-all duration-200 min-h-[480px] touch-none">

            <template x-if="isFullscreen">
                <button @click="toggleFullscreen()" class="absolute top-4 right-4 px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-semibold shadow-md z-30">
                    Exit Fullscreen ✕
                </button>
            </template>

            @if(!empty($tree))
                {{-- Flex canvas wrapper with automatic margin centering for scrolling bounds --}}
                <div class="min-w-max min-h-max flex items-center justify-center mx-auto my-auto transform-gpu origin-center transition-transform duration-75 ease-out"
                     :style="`transform: translate(${translateX}px, ${translateY}px) scale(${scale});`">
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
            <button @click="zoomIn()" class="p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors" title="Zoom In">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                </svg>
            </button>
            <button @click="zoomOut()" class="p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors" title="Zoom Out">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/>
                </svg>
            </button>
        </div>
    </div>
</div>
