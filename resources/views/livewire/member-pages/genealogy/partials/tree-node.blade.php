@props([
    'node',
    'isRoot' => false,
])

<div
    @class([
        'relative flex w-max flex-col items-center shrink-0',
        'has-children' => !empty($node['downlines']),
        'is-root' => $isRoot,
    ])
>

    {{-- Connection coming from parent --}}
    @if (!$isRoot)
        <div
            class="
                h-[30px]
                w-[2px]
                shrink-0
                bg-slate-300
            "
        ></div>
    @endif


    {{-- =========================
        NODE CARD
    ========================== --}}
    <div
        @class([
            '
                relative
                z-10
                flex
                h-[80px]
                w-[150px]
                shrink-0
                flex-col
                items-center
                justify-center
                rounded-lg
                border-2
                border-blue-300
                bg-white
                px-3
                py-2
                text-center
                shadow-sm
                transition-all
                duration-200
                hover:-translate-y-0.5
                hover:border-[#0C2C6B]
                hover:shadow-md
            ',

            '
                border-blue-600
                ring-2
                ring-blue-100
            ' => ($node['status'] ?? '') === 'Active',
        ])
    >

        {{-- Avatar --}}
        <div
            class="
                mb-1
                flex
                h-[22px]
                w-[22px]
                shrink-0
                items-center
                justify-center
                rounded-full
                bg-blue-50
                text-[9px]
                font-bold
                text-blue-600
            "
        >
            {{ strtoupper(substr($node['name'] ?? 'U', 0, 1)) }}
        </div>


        {{-- Member Name --}}
        <div
            class="
                w-full
                truncate
                text-center
                text-[9px]
                font-semibold
                leading-tight
                text-slate-700
            "
            title="{{ $node['name'] ?? 'Unknown User' }}"
        >
            {{ $node['name'] ?? 'Unknown User' }}
        </div>


        {{-- Referral Code --}}
        <div
            class="
                mt-1
                w-full
                truncate
                text-center
                text-[7px]
                leading-tight
                text-slate-400
            "
        >
            Ref: {{ $node['referral_code'] ?? 'N/A' }}
        </div>

    </div>


    {{-- =========================
        CHILDREN
    ========================== --}}
    @if (!empty($node['downlines']))

        {{-- Parent vertical stem --}}
        <div
            class="
                h-[30px]
                w-[2px]
                shrink-0
                bg-slate-300
            "
        ></div>


        <div
            class="
                relative
                flex
                w-max
                shrink-0
                flex-row
                items-start
                justify-center
            "
        >

            {{-- Horizontal connector --}}
            @if (count($node['downlines']) > 1)
                <div
                    class="
                        absolute
                        left-[75px]
                        right-[75px]
                        top-0
                        z-0
                        h-[2px]
                        bg-slate-300
                    "
                ></div>
            @endif


            @foreach ($node['downlines'] as $child)

                <div
                    class="
                        relative
                        flex
                        min-w-[150px]
                        shrink-0
                        flex-col
                        items-center
                        px-5
                    "
                >

                    {{-- Vertical connector --}}
                    <div
                        class="
                            relative
                            z-[1]
                            h-[30px]
                            w-[2px]
                            shrink-0
                            bg-slate-300
                        "
                    ></div>


                    {{-- Recursive Child --}}
                    @include(
                        'livewire.member-pages.genealogy.partials.tree-node',
                        [
                            'node' => $child,
                            'isRoot' => false,
                        ]
                    )

                </div>

            @endforeach

        </div>

    @endif

</div>
