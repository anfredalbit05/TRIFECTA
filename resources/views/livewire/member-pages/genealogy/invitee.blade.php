<div class="genealogy-explorer"
     x-data="{
         scale: 1,
         isDragging: false,
         startX: 0,
         startY: 0,
         scrollLeft: 0,
         scrollTop: 0
     }">

    <!-- FIXED TOP HEADER -->
    <div class="genealogy-explorer-header">
        <div>
            <h2 class="genealogy-explorer-title">Genealogy Network</h2>
            <p class="genealogy-explorer-subtitle">
                Browse members by generation and explore individual branches.
            </p>
        </div>

        @if(isset($tree['id']) && $tree['id'] !== auth()->id())
            <button
                wire:click="focusUser({{ auth()->id() }})"
                class="genealogy-back-root"
            >
                ↺ Back to My Network
            </button>
        @endif
    </div>

    {{-- View Switcher Toggle Buttons & Zoom Toolbar Combined --}}
    <div class="genealogy-view-switcher px-4 py-3 bg-white border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <button
                wire:click="setViewMode('generation')"
                class="view-btn px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $viewMode === 'generation' ? 'bg-[#0C2C6B] text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100 hover:text-[#0C2C6B]' }}"
            >
                Generation List
            </button>
            <button
                wire:click="setViewMode('tree')"
                class="view-btn px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $viewMode === 'tree' ? 'bg-[#0C2C6B] text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100 hover:text-[#0C2C6B]' }}"
            >
                Visual Tree
            </button>
        </div>

        {{-- Zoom Controls Toolbar --}}
        <div x-show="$wire.viewMode === 'tree'" class="flex items-center gap-2 px-3 py-1 bg-slate-50 border border-slate-200 rounded-full shadow-sm">
            <span class="text-xs font-medium text-slate-500 mr-1">Zoom:</span>
            <button @click="scale = Math.max(0.1, scale - 0.1)" class="w-7 h-7 flex items-center justify-center bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-full border border-slate-200 transition cursor-pointer shadow-xs" type="button" title="Zoom Out">-</button>
            <span class="text-xs font-semibold text-slate-600 px-1 w-10 text-center" x-text="Math.round(scale * 100) + '%'"></span>
            <button @click="scale = Math.min(1.8, scale + 0.1)" class="w-7 h-7 flex items-center justify-center bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-full border border-slate-200 transition cursor-pointer shadow-xs" type="button" title="Zoom In">+</button>
            <button @click="scale = 1" class="text-xs text-blue-600 hover:underline px-1 font-medium cursor-pointer ml-1" type="button">Reset</button>
        </div>
    </div>

    @if(!empty($tree))

        @if($viewMode === 'generation')

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
                            <div class="member-avatar">
                                {{ strtoupper(substr($tree['name'], 0, 1)) }}
                            </div>
                            <div class="member-info">
                                <span class="member-label">Current Member</span>
                                <h4>{{ $tree['name'] }}</h4>
                                <p>Referral: <strong>{{ $tree['referral_code'] }}</strong></p>
                            </div>
                            <span class="member-status {{ strtolower($tree['status']) === 'active' ? 'is-active' : 'is-inactive' }}">
                                {{ $tree['status'] }}
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
                'generation' => 2
            ])

        @else

            {{-- DRAGGABLE & SCALABLE VISUAL TREE VIEW CONTAINER --}}
            <div
                class="visual-tree-container relative overflow-auto p-12 flex flex-col items-center select-none cursor-grab active:cursor-grabbing h-[75vh]"
                @mousedown="isDragging = true; startX = $event.pageX - $el.scrollLeft; startY = $event.pageY - $el.scrollTop;"
                @mouseleave="isDragging = false"
                @mouseup="isDragging = false"
                @mousemove.prevent="
                    if(!isDragging) return;
                    $el.scrollLeft = startX - $event.pageX;
                    $el.scrollTop = startY - $event.pageY;
                "
            >
                <div class="origin-top transition-transform duration-100 ease-out flex flex-col items-center min-w-max my-auto" :style="`transform: scale(${scale});`">
                    @include('livewire.member-pages.genealogy.partials.tree-node', [
                        'node' => $tree,
                        'isRoot' => true
                    ])
                </div>
            </div>

        @endif

    @else

        <div class="genealogy-empty">
            <div class="genealogy-empty-icon">👥</div>
            <h3>No genealogy records yet</h3>
            <p>Members referred by this account will appear here.</p>
        </div>

    @endif

</div>
