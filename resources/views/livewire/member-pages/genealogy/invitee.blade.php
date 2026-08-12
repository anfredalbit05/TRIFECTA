<div class="gen-panel"
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
     :class="{ 'gen-panel--fullscreen': isFullscreen }"
>
    <div>
        {{-- Top Action Bar --}}
        <div class="gen-header" :class="{ 'is-hidden': isFullscreen }">
            <div>
                <h2 class="gen-title">Genealogy Tree</h2>
                <p class="gen-subtitle">Visualizing direct referrals and downlines</p>
            </div>

            <div class="gen-actions">

                @if(isset($tree['id']) && $tree['id'] !== auth()->id())
                    <button
                        wire:click="focusUser({{ auth()->id() }})"
                        class="gen-reset-btn"
                    >
                        ↺ Reset to My Node
                    </button>
                @endif
            </div>
        </div>

        {{-- Tree Viewport Container --}}
        <div class="gen-viewport">

            <template x-if="isFullscreen">
                <button @click="toggleFullscreen()" class="gen-exit-fullscreen-btn">
                    Exit Fullscreen ✕
                </button>
            </template>

            @if(!empty($tree))
                {{-- Flex canvas wrapper with automatic margin centering for scrolling bounds --}}
                <div class="gen-canvas-wrap"
                     :style="`transform: translate(${translateX}px, ${translateY}px) scale(${scale});`">
                    @include('livewire.member-pages.genealogy.partials.tree-node', [
                        'node' => $tree,
                        'isRoot' => true
                    ])
                </div>
            @else
                <div class="gen-empty-state">
                    <svg class="gen-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <p class="gen-empty-text">No downline record found for this account.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Floating Bottom Control Toolbar --}}
    <div class="gen-toolbar">
        <div class="gen-toolbar-inner">
            <button @click="zoomIn()" class="gen-toolbar-btn" title="Zoom In">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                </svg>
            </button>
            <button @click="zoomOut()" class="gen-toolbar-btn" title="Zoom Out">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/>
                </svg>
            </button>
        </div>
    </div>
</div>
