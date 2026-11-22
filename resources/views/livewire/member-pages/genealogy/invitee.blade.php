@php
    $btn = 'flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-base font-bold text-slate-700 transition hover:bg-slate-100';
@endphp

{{-- Zoom / pan state lives in the `treeViewport` Alpine store (resources/js/livewire/member-pages/genealogy/invitee.js) --}}
<div class="genealogy-explorer">

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
                    <button type="button" @click="$store.treeViewport.zoomOut()" title="Zoom out" aria-label="Zoom out" class="{{ $btn }}">−</button>
                    <span class="min-w-[44px] text-center text-xs font-semibold text-slate-600" x-text="Math.round($store.treeViewport.scale * 100) + '%'"></span>
                    <button type="button" @click="$store.treeViewport.zoomIn()" title="Zoom in" aria-label="Zoom in" class="{{ $btn }}">+</button>
                    <button type="button" @click="$store.treeViewport.fitTree()" title="Reset view" aria-label="Reset view" class="{{ $btn }}">↺</button>
                    <button type="button" @click="$store.treeViewport.toggleFullscreen()" title="Enter fullscreen" aria-label="Enter fullscreen" class="{{ $btn }}">⛶</button>
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
                data-tree-panel
                class="relative w-full select-none overflow-hidden bg-slate-50"
                :class="$store.treeViewport.isFullscreen ? 'h-screen' : 'h-[75vh]'"
                x-init="$nextTick(() => { $store.treeViewport.fitTree(); setTimeout(() => $store.treeViewport.fitTree(), 300); })"
            >
                {{-- Controls: visible only in browser fullscreen (toolbar is hidden then) --}}
                <div x-show="$store.treeViewport.isFullscreen" x-cloak
                    class="absolute right-5 top-5 z-50 flex items-center gap-2 rounded-xl border border-slate-200 bg-white/95 px-3 py-2 shadow-lg backdrop-blur">
                    <button type="button" @click="$store.treeViewport.zoomOut()" title="Zoom out" aria-label="Zoom out" class="{{ $btn }}">−</button>
                    <span class="min-w-[44px] text-center text-xs font-semibold text-slate-600" x-text="Math.round($store.treeViewport.scale * 100) + '%'"></span>
                    <button type="button" @click="$store.treeViewport.zoomIn()" title="Zoom in" aria-label="Zoom in" class="{{ $btn }}">+</button>
                    <button type="button" @click="$store.treeViewport.fitTree()" title="Reset view" aria-label="Reset view" class="{{ $btn }}">↺</button>
                    <button type="button" @click="$store.treeViewport.toggleFullscreen()" title="Exit fullscreen" aria-label="Exit fullscreen" class="{{ $btn }}">⊗</button>
                </div>

                <div
                    data-tree-viewport
                    class="relative h-full w-full cursor-grab overflow-hidden active:cursor-grabbing"
                    @wheel.prevent="$store.treeViewport.wheel($event)"
                    @mousedown.prevent="$store.treeViewport.startDrag($event)"
                    @mousemove.window="$store.treeViewport.drag($event)"
                    @mouseup.window="$store.treeViewport.stopDrag()"
                >
                    <div class="absolute left-0 top-0 w-max origin-top-left will-change-transform"
                        data-tree-stage
                        x-effect="$store.treeViewport.applyTransform()">
                        <div data-tree-tree class="flex w-max flex-col items-center justify-start p-10">
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
