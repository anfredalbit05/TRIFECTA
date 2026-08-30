@props(['node', 'isRoot' => false])

<div class="node-wrap" style="width: 200px; flex-shrink: 0;">

    {{-- Top Vertical Line connecting from parent above --}}
    @if (!$isRoot)
        <div class="node-top-line"></div>
    @endif

    {{-- User Card Node --}}
    <div @class([
        'node-card',
        'node-card--active' => ($node['status'] ?? '') === 'Active',
    ])>

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
    @if (!empty($node['downlines']))

        {{-- Vertical Stem dropping down from parent card --}}
        <div class="node-children-stem"></div>

        {{-- Children Container --}}
        <div class="node-children-row">

            {{-- Horizontal Line --}}
            @if (count($node['downlines']) > 1)
                <div class="node-horizontal-bar"></div>
            @endif

            @foreach ($node['downlines'] as $child)
                <div class="node-child">

                    {{-- Vertical Drop to Child --}}
                    <div class="node-child-line"></div>

                    {{-- Mobile Stub --}}
                    <div class="node-mobile-stub"></div>

                    {{-- Recursive Child --}}
                    @include('livewire.member-pages.genealogy.partials.tree-node', [
                        'node' => $child,
                        'isRoot' => false,
                    ])
                </div>
            @endforeach

        </div>

    @endif

</div>
