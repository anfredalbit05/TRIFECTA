@php
    $btn = 'flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-base font-bold text-slate-700 transition hover:bg-slate-100';
@endphp

<div
    class="genealogy-explorer"
    x-data="{
        scale: 1, minScale: 0.02, maxScale: 2, zoomStep: 0.1,
        offsetX: 0, offsetY: 0,
        isDragging: false, isFullscreen: false,
        startX: 0, startY: 0, lastX: 0, lastY: 0,
        fsHandler: null,

        init() {
            this.fsHandler = () => {
                this.isFullscreen = document.fullscreenElement === this.$refs.panel;
                setTimeout(() => this.fitTree(), 150);
            };
            document.addEventListener('fullscreenchange', this.fsHandler);
        },

        destroy() { document.removeEventListener('fullscreenchange', this.fsHandler); },

        async toggleFullscreen() {
            const panel = this.$refs.panel;
            if (!panel) return;
            try {
                if (!document.fullscreenElement) await panel.requestFullscreen();
                else await document.exitFullscreen();
            } catch (e) { console.error('Fullscreen could not be changed:', e); }
        },

        fitTree() {
            this.$nextTick(() => {
                const vp = this.$refs.viewport, tree = this.$refs.tree;
                if (!vp || !tree || !tree.offsetWidth || !tree.offsetHeight) return;
                const pad = 60;
                const sx = (vp.clientWidth - pad) / tree.offsetWidth;
                const sy = (vp.clientHeight - pad) / tree.offsetHeight;
                this.scale = Math.min(1, Math.max(this.minScale, Math.min(sx, sy)));
                this.offsetX = (vp.clientWidth - tree.offsetWidth * this.scale) / 2;
                this.offsetY = Math.max(10, (vp.clientHeight - tree.offsetHeight * this.scale) / 2);
            });
        },

        zoomIn() { this.setZoom(this.scale + this.zoomStep); },
        zoomOut() { this.setZoom(this.scale - this.zoomStep); },

        setZoom(value, clientX = null, clientY = null) {
            const vp = this.$refs.viewport;
            if (!vp) return;
            const oldScale = this.scale;
            const newScale = Math.min(this.maxScale, Math.max(this.minScale, value));
            if (newScale === oldScale) return;
            const rect = vp.getBoundingClientRect();
            const px = clientX === null ? rect.width / 2 : clientX - rect.left;
            const py = clientY === null ? rect.height / 2 : clientY - rect.top;
            const treeX = (px - this.offsetX) / oldScale;
            const treeY = (py - this.offsetY) / oldScale;
            this.scale = newScale;
            this.offsetX = px - treeX * newScale;
            this.offsetY = py - treeY * newScale;
        },

        wheel(e) {
            this.setZoom(this.scale + (e.deltaY < 0 ? 1 : -1) * this.zoomStep, e.clientX, e.clientY);
        },

        startDrag(e) {
            if (e.button !== 0) return;
            this.isDragging = true;
            this.startX = e.clientX; this.startY = e.clientY;
            this.lastX = this.offsetX; this.lastY = this.offsetY;
        },

        drag(e) {
            if (!this.isDragging) return;
            this.offsetX = this.lastX + e.clientX - this.startX;
            this.offsetY = this.lastY + e.clientY - this.startY;
        },

        stopDrag() { this.isDragging = false; }
    }"
>

    {{-- HEADER --}}
    <div class="genealogy-explorer-header">
        <div>
            <h2 class="genealogy-explorer-title">Genealogy Network</h2>
            <p class="genealogy-explorer-subtitle">Browse members by generation and explore individual branches.</p>
        </div>

        {{-- SEARCH --}}
        <div class="relative w-full sm:w-72">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" /></svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search member name..."
                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-9 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-[var(--color-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]/20">
            @if (filled($search))
                <button type="button" wire:click="clearSearch" title="Clear search" class="absolute inset-y-0 right-3 flex items-center text-slate-400 transition hover:text-slate-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            @endif
        </div>

        @if (isset($tree['id']) && $tree['id'] !== auth()->id())
            <button wire:click="focusUser({{ auth()->id() }})" class="genealogy-back-root">↺ Back to My Network</button>
        @endif
    </div>

    {{-- VIEW SWITCHER --}}
    <div class="genealogy-view-switcher flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 py-3">

        <div class="flex items-center gap-3">
            <button wire:click="setViewMode('generation')"
                class="view-btn rounded-lg px-4 py-2 text-sm font-semibold transition-all {{ $viewMode === 'generation' ? 'bg-[var(--color-primary)] text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100 hover:text-[var(--color-primary)]' }}">
                Generation List
            </button>
            <button wire:click="setViewMode('tree')"
                class="view-btn rounded-lg px-4 py-2 text-sm font-semibold transition-all {{ $viewMode === 'tree' ? 'bg-[var(--color-primary)] text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100 hover:text-[var(--color-primary)]' }}">
                Visual Tree
            </button>
        </div>

        {{-- TREE ACTIONS: Print (Excel) + zoom controls --}}
        <div x-show="$wire.viewMode === 'tree'" x-cloak class="flex flex-wrap items-center gap-2">
            @if (! empty($tree))
                <button type="button" wire:click="exportTreeExcel" wire:loading.attr="disabled" wire:target="exportTreeExcel"
                    class="inline-flex items-center gap-2 rounded-lg border border-[var(--color-primary)] bg-white px-4 py-2 text-xs font-semibold text-[var(--color-primary)] shadow-sm transition hover:bg-slate-50 disabled:opacity-50">
                    <span wire:loading.remove wire:target="exportTreeExcel">&#128424; Print (Excel)</span>
                    <span wire:loading wire:target="exportTreeExcel">Preparing...</span>
                </button>

                <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-2 py-1.5 shadow-sm">
                    <button type="button" @click="zoomOut()" title="Zoom out" aria-label="Zoom out" class="{{ $btn }}">−</button>
                    <span class="min-w-[44px] text-center text-xs font-semibold text-slate-600" x-text="Math.round(($data.scale ?? 1) * 100) + '%'"></span>
                    <button type="button" @click="zoomIn()" title="Zoom in" aria-label="Zoom in" class="{{ $btn }}">+</button>
                    <button type="button" @click="fitTree()" title="Reset view" aria-label="Reset view" class="{{ $btn }}">↺</button>
                    <button type="button" @click="toggleFullscreen()" title="Enter fullscreen" aria-label="Enter fullscreen" class="{{ $btn }}">⛶</button>
                </div>
            @endif
        </div>
    </div>

    {{-- CONTENT --}}
    @if (! empty($tree))

        @if ($viewMode === 'generation')

            {{-- ROOT MEMBER --}}
            <section class="generation-section generation-root">
                <div class="generation-heading">
                    <span class="generation-number">01</span>
                    <div>
                        <h3>Root Member</h3>
                        <p>Currently selected genealogy account</p>
                    </div>
                </div>

                <div class="root-member-wrapper">
                    <div class="member-card root-member-card">
                        <div class="member-card-top">
                            <div class="member-avatar">{{ strtoupper(substr($tree['name'] ?? 'U', 0, 1)) }}</div>

                            <div class="member-info">
                                <span class="member-label">Current Member</span>
                                <h4>{{ $tree['name'] ?? 'Unknown User' }}</h4>
                                <p>Referral: <strong>{{ $tree['referral_code'] ?? 'N/A' }}</strong></p>
                            </div>

                            <span class="member-status {{ strtolower($tree['status'] ?? '') === 'active' ? 'is-active' : 'is-inactive' }}">
                                {{ $tree['status'] ?? 'Unknown' }}
                            </span>
                        </div>

                        <div class="member-card-footer">
                            <div>
                                <span>Direct Downlines</span>
                                <strong>{{ count($tree['downlines'] ?? []) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            @include('livewire.member-pages.genealogy.partials.generation-list', [
                'nodes' => $tree['downlines'] ?? [],
                'generation' => 2,
            ])

        @else

            {{-- VISUAL TREE --}}
            <div
                x-ref="panel"
                class="relative w-full select-none overflow-hidden bg-slate-50"
                :class="isFullscreen ? 'h-screen' : 'h-[75vh]'"
                x-init="$nextTick(() => { fitTree(); setTimeout(() => fitTree(), 300); })"
            >
                {{-- Controls: visible only in browser fullscreen (toolbar is hidden then) --}}
                <div x-show="isFullscreen" x-cloak
                    class="absolute right-5 top-5 z-50 flex items-center gap-2 rounded-xl border border-slate-200 bg-white/95 px-3 py-2 shadow-lg backdrop-blur">
                    <button type="button" @click="zoomOut()" title="Zoom out" aria-label="Zoom out" class="{{ $btn }}">−</button>
                    <span class="min-w-[44px] text-center text-xs font-semibold text-slate-600" x-text="Math.round(($data.scale ?? 1) * 100) + '%'"></span>
                    <button type="button" @click="zoomIn()" title="Zoom in" aria-label="Zoom in" class="{{ $btn }}">+</button>
                    <button type="button" @click="fitTree()" title="Reset view" aria-label="Reset view" class="{{ $btn }}">↺</button>
                    <button type="button" @click="toggleFullscreen()" title="Exit fullscreen" aria-label="Exit fullscreen" class="{{ $btn }}">⊗</button>
                </div>

                <div
                    x-ref="viewport"
                    class="relative h-full w-full cursor-grab overflow-hidden active:cursor-grabbing"
                    @wheel.prevent="wheel($event)"
                    @mousedown.prevent="startDrag($event)"
                    @mousemove.window="drag($event)"
                    @mouseup.window="stopDrag()"
                >
                    <div class="absolute left-0 top-0 w-max origin-top-left will-change-transform"
                        :style="`transform: translate(${offsetX}px, ${offsetY}px) scale(${scale});`">
                        <div x-ref="tree" class="flex w-max flex-col items-center justify-start p-10">
                            @include('livewire.member-pages.genealogy.partials.tree-node', [
                                'node' => $tree,
                                'isRoot' => true,
                            ])
                        </div>
                    </div>
                </div>
            </div>

        @endif

    {{-- EMPTY STATES --}}
    @elseif (filled($search))

        <div class="genealogy-empty">
            <div class="genealogy-empty-icon">🔍</div>
            <h3>No members match "{{ $search }}"</h3>
            <p>Try a different name or clear the search to see the full network.</p>
        </div>

    @else

        <div class="genealogy-empty">
            <div class="genealogy-empty-icon">👥</div>
            <h3>No genealogy records yet</h3>
            <p>Members referred by this account will appear here.</p>
        </div>

    @endif

</div>
