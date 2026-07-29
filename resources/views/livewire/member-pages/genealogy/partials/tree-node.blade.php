@props(['node', 'isRoot' => false])

<div class="flex flex-col items-center relative">
    {{-- Top Vertical Line (for non-root nodes connecting from above) --}}
    @if(!$isRoot)
        <div class="w-0.5 h-6 bg-slate-300"></div>
    @endif

    {{-- User Card Node (Non-clickable) --}}
    <div
        @class([
            'group relative flex flex-col items-center p-4 min-w-[160px] bg-white rounded-xl border-2 shadow-sm transition-all duration-200 z-10',
            'border-emerald-500 ring-2 ring-emerald-500/10' => ($node['status'] ?? '') === 'Active',
            'border-slate-200' => ($node['status'] ?? '') !== 'Active',
        ])
    >
        {{-- Avatar Circle --}}
        <div class="w-11 h-11 rounded-full bg-blue-50 border border-blue-100 text-blue-600 font-bold flex items-center justify-center text-base mb-2 shadow-inner">
            {{ strtoupper(substr($node['name'] ?? 'U', 0, 1)) }}
        </div>

        {{-- User Details --}}
        <span class="font-semibold text-xs text-slate-800 text-center line-clamp-1 max-w-[140px] mb-1">
            {{ $node['name'] }}
        </span>

        <span class="text-[11px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200/60">
            Ref: {{ $node['upline_code'] ?? 'N/A' }}
        </span>


    </div>

    {{-- Downlines (Children) Section --}}
    @if(!empty($node['downlines']))
        @php $childCount = count($node['downlines']); @endphp

        {{-- Vertical Stem dropping down from the parent card --}}
        <div class="w-0.5 h-6 bg-slate-300"></div>

        {{-- Dynamic Connector layout matching exact reference design --}}
        <div class="relative flex items-start justify-center">

            {{-- Horizontal Spanning Bar for 2 or more children --}}
            @if($childCount == 2)
                <div class="absolute top-0 left-[25%] right-[25%] h-0.5 bg-slate-300"></div>
            @elseif($childCount >= 3)
                <div class="absolute top-0 left-[15%] right-[15%] h-0.5 bg-slate-300"></div>
            @endif

            {{-- Render each child node cleanly side-by-side with balanced spacing --}}
            @foreach($node['downlines'] as $child)
                <div class="flex flex-col items-center px-4 relative">
                    @include('livewire.member-pages.genealogy.partials.tree-node', ['node' => $child, 'isRoot' => false])
                </div>
            @endforeach
        </div>
    @endif
</div>
