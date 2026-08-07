@props(['node', 'isRoot' => false])

<div class="flex flex-col items-start sm:items-center relative">
    {{-- Top Vertical Line connecting from parent above (Desktop only) --}}
    @if(!$isRoot)
        <div class="hidden sm:block w-0.5 h-6 bg-slate-300 shrink-0"></div>
    @endif

    {{-- User Card Node --}}
    <div
        @class([
            'group relative flex flex-col items-center p-4 w-[160px] bg-white rounded-xl border-2 shadow-sm transition-all duration-200 z-10 shrink-0 cursor-pointer',
            'border-blue-700 ring-2 ring-blue-700/10' => ($node['status'] ?? '') === 'Active',
            'border-slate-200' => ($node['status'] ?? '') !== 'Active',
        ])
    >
        {{-- Avatar Circle --}}
        <div class="w-11 h-11 rounded-full bg-blue-50 border border-blue-100 text-blue-600 font-bold flex items-center justify-center text-base mb-2 shadow-inner shrink-0">
            {{ strtoupper(substr($node['name'] ?? 'U', 0, 1)) }}
        </div>

        {{-- User Details --}}
        <span class="font-semibold text-xs text-slate-800 text-center truncate w-full mb-1">
            {{ $node['name'] }}
        </span>

        <span class="text-[11px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200/60 truncate max-w-full">
            Ref: {{ $node['referral_code'] ?? 'N/A' }}
        </span>
    </div>

    {{-- Downlines (Children) Section --}}
    @if(!empty($node['downlines']))
        {{-- Vertical Stem dropping down from parent card --}}
        <div class="w-0.5 h-6 bg-slate-300 self-center shrink-0"></div>

        {{-- Responsive Container --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-center pl-4 sm:pl-0 gap-6 sm:gap-10 relative w-full sm:w-auto border-l-2 sm:border-l-0 border-slate-300 sm:border-transparent ml-2 sm:ml-0">

            {{-- Optional Desktop Horizontal Bar spanning multiple children --}}
            @if(count($node['downlines']) > 1)
                <div class="hidden sm:block absolute -top-6 left-[80px] right-[80px] h-0.5 bg-slate-300 pointer-events-none"></div>
            @endif

            @foreach($node['downlines'] as $child)
                <div class="flex flex-col items-start sm:items-center relative">

                    {{-- Desktop Vertical Drop to each child card from the horizontal bar --}}
                    @if(count($node['downlines']) > 1)
                        <div class="hidden sm:block w-0.5 h-6 bg-slate-300 -mt-6 mb-0 shrink-0"></div>
                    @else
                        <div class="hidden sm:block w-0.5 h-6 bg-slate-300 -mt-6 mb-0 shrink-0"></div>
                    @endif

                    {{-- Mobile horizontal branch stub pointing to the card --}}
                    <div class="sm:hidden absolute -left-4 top-1/2 w-4 h-0.5 bg-slate-300 -translate-y-1/2"></div>

                    @include('livewire.member-pages.genealogy.partials.tree-node', [
                        'node' => $child,
                        'isRoot' => false
                    ])
                </div>
            @endforeach
        </div>
    @endif
</div>
