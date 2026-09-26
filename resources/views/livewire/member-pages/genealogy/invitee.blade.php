<div
    class="genealogy-explorer"
    x-data="{
        fullscreen: false,

        openFullscreen() {
            this.fullscreen = true;

            this.$nextTick(() => {
                if (this.$refs.fullscreenTreeComponent) {
                    this.$refs.fullscreenTreeComponent.fitTree();
                }
            });
        }
    }"
>

    {{-- =========================================
        FIXED TOP HEADER
    ========================================= --}}
    <div class="genealogy-explorer-header">

        <div>
            <h2 class="genealogy-explorer-title">
                Genealogy Network
            </h2>

            <p class="genealogy-explorer-subtitle">
                Browse members by generation and explore individual branches.
            </p>
        </div>

        {{-- NAME SEARCH FILTER --}}
        <div class="relative w-full sm:w-72">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                </svg>
            </span>

            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search member name..."
                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-9 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-[#0C2C6B] focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]/20"
            >

            @if (filled($search))
                <button
                    type="button"
                    wire:click="clearSearch"
                    title="Clear search"
                    class="absolute inset-y-0 right-3 flex items-center text-slate-400 transition hover:text-slate-600"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>

        @if (isset($tree['id']) && $tree['id'] !== auth()->id())
            <button
                wire:click="focusUser({{ auth()->id() }})"
                class="genealogy-back-root"
            >
                ↺ Back to My Network
            </button>
        @endif

    </div>


    {{-- =========================================
        VIEW SWITCHER
    ========================================= --}}
    <div
        class="
            genealogy-view-switcher
            flex
            flex-wrap
            items-center
            justify-between
            gap-3
            border-b
            border-slate-200
            bg-white
            px-4
            py-3
        "
    >

        {{-- VIEW BUTTONS --}}
        <div class="flex items-center gap-3">

            {{-- Generation --}}
            <button
                wire:click="setViewMode('generation')"
                class="
                    view-btn
                    rounded-lg
                    px-4
                    py-2
                    text-sm
                    font-semibold
                    transition-all
                    {{
                        $viewMode === 'generation'
                            ? 'bg-[#0C2C6B] text-white shadow-sm'
                            : 'text-gray-500 hover:bg-gray-100 hover:text-[#0C2C6B]'
                    }}
                "
            >
                Generation List
            </button>


            {{-- Visual Tree --}}
            <button
                wire:click="setViewMode('tree')"
                class="
                    view-btn
                    rounded-lg
                    px-4
                    py-2
                    text-sm
                    font-semibold
                    transition-all
                    {{
                        $viewMode === 'tree'
                            ? 'bg-[#0C2C6B] text-white shadow-sm'
                            : 'text-gray-500 hover:bg-gray-100 hover:text-[#0C2C6B]'
                    }}
                "
            >
                Visual Tree
            </button>

        </div>


        {{-- FULLSCREEN BUTTON --}}
        <div
            x-show="$wire.viewMode === 'tree'"
            x-cloak
        >

            <button
                type="button"
                @click="openFullscreen()"
                class="
                    inline-flex
                    items-center
                    gap-2
                    rounded-lg
                    bg-[#0C2C6B]
                    px-4
                    py-2
                    text-xs
                    font-semibold
                    text-white
                    shadow-sm
                    transition
                    hover:bg-[#123c88]
                    hover:shadow-md
                "
            >
                <span class="text-base">⛶</span>

                Fullscreen
            </button>

        </div>

    </div>


    {{-- =========================================
        CONTENT
    ========================================= --}}
    @if (!empty($tree))

        {{-- =====================================
            GENERATION VIEW
        ====================================== --}}
        @if ($viewMode === 'generation')

            {{-- ROOT MEMBER --}}
            <section class="generation-section generation-root">

                <div class="generation-heading">

                    <span class="generation-number">
                        01
                    </span>

                    <div>
                        <h3>
                            Root Member
                        </h3>

                        <p>
                            Currently selected genealogy account
                        </p>
                    </div>

                </div>


                <div class="root-member-wrapper">

                    <div class="member-card root-member-card">

                        <div class="member-card-top">

                            <div class="member-avatar">
                                {{ strtoupper(substr($tree['name'] ?? 'U', 0, 1)) }}
                            </div>


                            <div class="member-info">

                                <span class="member-label">
                                    Current Member
                                </span>

                                <h4>
                                    {{ $tree['name'] ?? 'Unknown User' }}
                                </h4>

                                <p>
                                    Referral:

                                    <strong>
                                        {{ $tree['referral_code'] ?? 'N/A' }}
                                    </strong>
                                </p>

                            </div>


                            <span
                                class="
                                    member-status
                                    {{
                                        strtolower($tree['status'] ?? '') === 'active'
                                            ? 'is-active'
                                            : 'is-inactive'
                                    }}
                                "
                            >
                                {{ $tree['status'] ?? 'Unknown' }}
                            </span>

                        </div>


                        <div class="member-card-footer">

                            <div>

                                <span>
                                    Direct Downlines
                                </span>

                                <strong>
                                    {{ count($tree['downlines'] ?? []) }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </section>


            {{-- GENERATION LIST --}}
            @include(
                'livewire.member-pages.genealogy.partials.generation-list',
                [
                    'nodes' => $tree['downlines'] ?? [],
                    'generation' => 2,
                ]
            )


        {{-- =====================================
            VISUAL TREE VIEW
        ====================================== --}}
        @else

            <div
                class="
                    relative
                    h-[75vh]
                    w-full
                    overflow-hidden
                    bg-slate-50
                    select-none
                "

                x-data="{
                    scale: 1,
                    panX: 0,
                    panY: 0,

                    isDragging: false,

                    startX: 0,
                    startY: 0,

                    startPanX: 0,
                    startPanY: 0,

                    fitTree() {
                        this.$nextTick(() => {

                            const viewport = this.$refs.viewport;
                            const tree = this.$refs.tree;

                            if (!viewport || !tree) return;

                            const padding = 120;

                            const treeWidth = tree.offsetWidth;
                            const treeHeight = tree.offsetHeight;

                            const availableWidth =
                                viewport.clientWidth - padding;

                            const availableHeight =
                                viewport.clientHeight - padding;

                            const scaleX =
                                availableWidth / treeWidth;

                            const scaleY =
                                availableHeight / treeHeight;

                            this.scale = Math.min(
                                1,
                                Math.max(
                                    0.15,
                                    Math.min(scaleX, scaleY)
                                )
                            );

                            this.panX = 0;
                            this.panY = 0;

                        });
                    },

                    zoomIn() {
                        this.scale = Math.min(
                            1.8,
                            this.scale + 0.1
                        );
                    },

                    zoomOut() {
                        this.scale = Math.max(
                            0.1,
                            this.scale - 0.1
                        );
                    },

                    startDrag(event) {

                        this.isDragging = true;

                        this.startX = event.clientX;
                        this.startY = event.clientY;

                        this.startPanX = this.panX;
                        this.startPanY = this.panY;
                    },

                    drag(event) {

                        if (!this.isDragging) return;

                        this.panX =
                            this.startPanX +
                            (event.clientX - this.startX);

                        this.panY =
                            this.startPanY +
                            (event.clientY - this.startY);
                    },

                    stopDrag() {
                        this.isDragging = false;
                    }
                }"

                x-init="
                    $nextTick(() => {
                        fitTree();

                        setTimeout(() => {
                            fitTree();
                        }, 300);
                    })
                "
            >


                {{-- =============================
                    TREE CONTROLS
                ============================== --}}
                <div
                    class="
                        absolute
                        right-5
                        top-5
                        z-50
                        flex
                        items-center
                        gap-2
                        rounded-xl
                        border
                        border-slate-200
                        bg-white/95
                        px-3
                        py-2
                        shadow-lg
                        backdrop-blur
                    "
                >

                    {{-- Zoom Out --}}
                    <button
                        type="button"
                        @click="zoomOut()"
                        class="
                            flex
                            h-8
                            w-8
                            items-center
                            justify-center
                            rounded-lg
                            border
                            border-slate-200
                            bg-white
                            text-base
                            font-bold
                            text-slate-700
                            transition
                            hover:bg-slate-100
                        "
                    >
                        −
                    </button>


                    {{-- Percentage --}}
                    <div
                        class="
                            min-w-[52px]
                            text-center
                            text-xs
                            font-semibold
                            text-slate-600
                        "
                        x-text="Math.round(scale * 100) + '%'"
                    ></div>


                    {{-- Zoom In --}}
                    <button
                        type="button"
                        @click="zoomIn()"
                        class="
                            flex
                            h-8
                            w-8
                            items-center
                            justify-center
                            rounded-lg
                            border
                            border-slate-200
                            bg-white
                            text-base
                            font-bold
                            text-slate-700
                            transition
                            hover:bg-slate-100
                        "
                    >
                        +
                    </button>


                    {{-- Fit --}}
                    <button
                        type="button"
                        @click="fitTree()"
                        class="
                            ml-1
                            rounded-lg
                            bg-[#0C2C6B]
                            px-3
                            py-1.5
                            text-xs
                            font-semibold
                            text-white
                            transition
                            hover:bg-[#08245a]
                        "
                    >
                        Fit
                    </button>

                </div>


                {{-- =============================
                    TREE VIEWPORT
                ============================== --}}
                <div
                    x-ref="viewport"

                    class="
                        relative
                        h-full
                        w-full
                        overflow-hidden
                        cursor-grab
                        active:cursor-grabbing
                    "

                    @mousedown.prevent="startDrag($event)"
                    @mousemove.window="drag($event)"
                    @mouseup.window="stopDrag()"
                    @mouseleave.window="stopDrag()"
                >

                    {{-- DRAGGABLE TREE --}}
                    <div
                        class="
                            absolute
                            left-1/2
                            top-1/2
                            w-max
                            origin-center
                            will-change-transform
                        "

                        :class="{
                            'transition-transform duration-200 ease-out':
                                !isDragging
                        }"

                        :style="`
                            transform:
                                translate(
                                    calc(-50% + ${panX}px),
                                    calc(-50% + ${panY}px)
                                )
                                scale(${scale});
                        `"
                    >

                        <div
                            x-ref="tree"

                            class="
                                flex
                                w-max
                                flex-col
                                items-center
                                justify-start
                                p-10
                            "
                        >

                            @include(
                                'livewire.member-pages.genealogy.partials.tree-node',
                                [
                                    'node' => $tree,
                                    'isRoot' => true,
                                ]
                            )

                        </div>

                    </div>

                </div>

            </div>

        @endif


    {{-- =========================================
        EMPTY STATE
    ========================================= --}}
    @elseif (filled($search))

        <div class="genealogy-empty">

            <div class="genealogy-empty-icon">
                🔍
            </div>

            <h3>
                No members match "{{ $search }}"
            </h3>

            <p>
                Try a different name or clear the search to see the full network.
            </p>

        </div>

    @else

        <div class="genealogy-empty">

            <div class="genealogy-empty-icon">
                👥
            </div>

            <h3>
                No genealogy records yet
            </h3>

            <p>
                Members referred by this account will appear here.
            </p>

        </div>

    @endif


    {{-- =========================================
        FULLSCREEN MODAL
    ========================================= --}}
    <div
        x-show="fullscreen"
        x-cloak

        x-ref="fullscreenTreeComponent"

        class="
            fixed
            inset-0
            z-[9999]
            bg-slate-950/70
            backdrop-blur-sm
        "

        @keydown.escape.window="fullscreen = false"
    >

        {{-- Modal --}}
        <div
            class="
                absolute
                inset-4
                flex
                flex-col
                overflow-hidden
                rounded-2xl
                bg-white
                shadow-2xl
            "

            x-data="{
                scale: 1,
                panX: 0,
                panY: 0,

                isDragging: false,

                startX: 0,
                startY: 0,

                startPanX: 0,
                startPanY: 0,

                fitTree() {
                    this.$nextTick(() => {

                        const viewport =
                            this.$refs.fsViewport;

                        const tree =
                            this.$refs.fsTree;

                        if (!viewport || !tree) return;

                        const padding = 150;

                        const scaleX =
                            (viewport.clientWidth - padding)
                            / tree.offsetWidth;

                        const scaleY =
                            (viewport.clientHeight - padding)
                            / tree.offsetHeight;

                        this.scale = Math.min(
                            1.5,
                            Math.max(
                                0.15,
                                Math.min(scaleX, scaleY)
                            )
                        );

                        this.panX = 0;
                        this.panY = 0;

                    });
                },

                zoomIn() {
                    this.scale = Math.min(
                        2,
                        this.scale + 0.1
                    );
                },

                zoomOut() {
                    this.scale = Math.max(
                        0.1,
                        this.scale - 0.1
                    );
                },

                startDrag(event) {

                    this.isDragging = true;

                    this.startX = event.clientX;
                    this.startY = event.clientY;

                    this.startPanX = this.panX;
                    this.startPanY = this.panY;
                },

                drag(event) {

                    if (!this.isDragging) return;

                    this.panX =
                        this.startPanX +
                        (event.clientX - this.startX);

                    this.panY =
                        this.startPanY +
                        (event.clientY - this.startY);
                },

                stopDrag() {
                    this.isDragging = false;
                }
            }"

            x-init="
                $nextTick(() => {
                    fitTree();
                })
            "
        >


            {{-- FULLSCREEN HEADER --}}
            <div
                class="
                    flex
                    shrink-0
                    items-center
                    justify-between
                    border-b
                    border-slate-200
                    px-6
                    py-4
                "
            >

                <div>

                    <h2
                        class="
                            text-lg
                            font-bold
                            text-[#0C2C6B]
                        "
                    >
                        Genealogy Network
                    </h2>

                    <p
                        class="
                            text-sm
                            text-slate-500
                        "
                    >
                        Fullscreen Visual Tree
                    </p>

                </div>


                <div class="flex items-center gap-3">

                    {{-- Controls --}}
                    <div
                        class="
                            flex
                            items-center
                            gap-2
                            rounded-xl
                            border
                            border-slate-200
                            bg-slate-50
                            px-3
                            py-2
                        "
                    >

                        <button
                            type="button"
                            @click="zoomOut()"
                            class="
                                flex
                                h-8
                                w-8
                                items-center
                                justify-center
                                rounded-lg
                                border
                                bg-white
                                font-bold
                                hover:bg-slate-100
                            "
                        >
                            −
                        </button>


                        <span
                            class="
                                w-12
                                text-center
                                text-xs
                                font-semibold
                                text-slate-600
                            "
                            x-text="Math.round(scale * 100) + '%'"
                        ></span>


                        <button
                            type="button"
                            @click="zoomIn()"
                            class="
                                flex
                                h-8
                                w-8
                                items-center
                                justify-center
                                rounded-lg
                                border
                                bg-white
                                font-bold
                                hover:bg-slate-100
                            "
                        >
                            +
                        </button>


                        <button
                            type="button"
                            @click="fitTree()"
                            class="
                                rounded-lg
                                bg-[#0C2C6B]
                                px-3
                                py-2
                                text-xs
                                font-semibold
                                text-white
                            "
                        >
                            Fit
                        </button>

                    </div>


                    {{-- Close --}}
                    <button
                        type="button"
                        @click="fullscreen = false"

                        class="
                            flex
                            h-10
                            w-10
                            items-center
                            justify-center
                            rounded-xl
                            border
                            border-slate-200
                            bg-white
                            text-lg
                            font-bold
                            text-slate-600
                            transition
                            hover:bg-red-50
                            hover:text-red-600
                        "
                    >
                        ✕
                    </button>

                </div>

            </div>


            {{-- FULLSCREEN VIEWPORT --}}
            <div
                x-ref="fsViewport"

                class="
                    relative
                    flex-1
                    overflow-hidden
                    bg-slate-50
                    cursor-grab
                    active:cursor-grabbing
                "

                @mousedown.prevent="startDrag($event)"
                @mousemove.window="drag($event)"
                @mouseup.window="stopDrag()"
                @mouseleave.window="stopDrag()"
            >

                {{-- TREE --}}
                <div
                    class="
                        absolute
                        left-1/2
                        top-1/2
                        w-max
                        origin-center
                        will-change-transform
                    "

                    :class="{
                        'transition-transform duration-200 ease-out':
                            !isDragging
                    }"

                    :style="`
                        transform:
                            translate(
                                calc(-50% + ${panX}px),
                                calc(-50% + ${panY}px)
                            )
                            scale(${scale});
                    `"
                >

                    <div
                        x-ref="fsTree"

                        class="
                            flex
                            w-max
                            flex-col
                            items-center
                            justify-start
                            p-10
                        "
                    >

                        @include(
                            'livewire.member-pages.genealogy.partials.tree-node',
                            [
                                'node' => $tree,
                                'isRoot' => true,
                            ]
                        )

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
