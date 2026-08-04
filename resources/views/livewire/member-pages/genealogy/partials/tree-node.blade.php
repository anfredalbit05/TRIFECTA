@props(['node', 'isRoot' => false])

<div class="flex flex-col items-center relative">
    {{-- Top Vertical Line connecting from parent above --}}
    @if(!$isRoot)
        <div class="w-0.5 h-6 bg-slate-300"></div>
    @endif

    {{-- User Card Node --}}
    <div
        @class([
            'group relative flex flex-col items-center p-4 min-w-[150px] sm:min-w-[160px] bg-white rounded-xl border-2 shadow-sm transition-all duration-200 z-10',
            'border-blue-700 ring-2 ring-blue-700/10' => ($node['status'] ?? '') === 'Active',
            'border-slate-200' => ($node['status'] ?? '') !== 'Active',
        ])
    >
        {{-- Avatar Circle --}}
        <div class="w-11 h-11 rounded-full bg-blue-50 border border-blue-100 text-blue-600 font-bold flex items-center justify-center text-base mb-2 shadow-inner">
            {{ strtoupper(substr($node['name'] ?? 'U', 0, 1)) }}
        </div>

        {{-- User Details --}}
        <span class="font-semibold text-xs text-slate-800 text-center line-clamp-1 max-w-[130px] sm:max-w-[140px] mb-1">
            {{ $node['name'] }}
        </span>

        <span class="text-[11px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200/60">
            Ref: {{ $node['referral_code'] ?? 'N/A' }}
        </span>
    </div>

    {{-- Downlines (Children) Section --}}
    @if(!empty($node['downlines']))
        {{-- Vertical Stem dropping down from parent card --}}
        <div class="w-0.5 h-6 bg-slate-300"></div>

        {{-- Children Container --}}
        <div class="flex flex-col sm:flex-row items-center sm:items-start justify-center gap-6 sm:gap-4 relative">
            @foreach($node['downlines'] as $child)
                <div class="flex flex-col items-center relative sm:px-2">

                    {{-- Mobile Vertical Line --}}
                    @if(!$loop->first)
                        <div class="sm:hidden w-0.5 h-6 bg-slate-300"></div>
                    @endif

                    {{-- Desktop Branch Connector Structure --}}
                    @if(count($node['downlines']) > 1)
                        <div class="hidden sm:block absolute -top-6 left-0 right-0 h-6">
                            {{-- Horizontal connector spanning across children via top-border --}}
                            <div class="absolute top-0 h-3 border-t-2 border-slate-300
                                @if($loop->first) left-1/2 right-0
                                @elseif($loop->last) left-0 right-1/2
                                @else left-0 right-0 @endif">
                            </div>
                            {{-- Center vertical tick connecting down to the child card --}}
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-0.5 h-6 bg-slate-300"></div>
                        </div>
                    @else
                        {{-- Single Child Straight Vertical Line --}}
                        <div class="hidden sm:block w-0.5 h-6 bg-slate-300 -mt-6 mb-0"></div>
                    @endif

                    @include('livewire.member-pages.genealogy.partials.tree-node', ['node' => $child, 'isRoot' => false])
                </div>
            @endforeach
        </div>
    @endif
</div>
