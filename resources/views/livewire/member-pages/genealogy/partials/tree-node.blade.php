@props(['node', 'isRoot' => false])

<div class="node-wrap">
    {{-- Top Vertical Line connecting from parent above (Desktop only) --}}
    @if(!$isRoot)
        <div class="node-top-line"></div>
    @endif

    {{-- User Card Node --}}
    <div
        @class([
            'node-card',
            'node-card--active' => ($node['status'] ?? '') === 'Active',
        ])
    >
        {{-- Avatar Circle --}}
        <div class="node-avatar">
            {{ strtoupper(substr($node['name'] ?? 'U', 0, 1)) }}
        </div>

        {{-- User Details --}}
        <span class="node-name">
            {{ $node['name'] }}
        </span>

        <span class="node-ref">
            Ref: {{ $node['referral_code'] ?? 'N/A' }}
        </span>
    </div>

    {{-- Downlines (Children) Section --}}
    @if(!empty($node['downlines']))
        {{-- Vertical Stem dropping down from parent card --}}
        <div class="node-children-stem"></div>

        {{-- Responsive Container --}}
        <div class="node-children-row">

            {{-- Optional Desktop Horizontal Bar spanning multiple children --}}
            @if(count($node['downlines']) > 1)
                <div class="node-horizontal-bar"></div>
            @endif

            @foreach($node['downlines'] as $child)
                <div class="node-child">

                    {{-- Desktop Vertical Drop to each child card from the horizontal bar --}}
                    <div class="node-child-line"></div>

                    {{-- Mobile horizontal branch stub pointing to the card --}}
                    <div class="node-mobile-stub"></div>

                    @include('livewire.member-pages.genealogy.partials.tree-node', [
                        'node' => $child,
                        'isRoot' => false
                    ])
                </div>
            @endforeach
        </div>
    @endif
</div>
