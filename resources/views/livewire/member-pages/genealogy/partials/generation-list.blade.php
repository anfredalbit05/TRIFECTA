@if(!empty($nodes))

    <section class="generation-section">

        <div class="generation-heading">

            <span class="generation-number">
                {{ str_pad($generation, 2, '0', STR_PAD_LEFT) }}
            </span>

            <div>
                <h3>Generation {{ $generation }}</h3>

                <p>
                    {{ count($nodes) }}
                    {{ count($nodes) === 1 ? 'member' : 'members' }}
                </p>
            </div>

        </div>

        <div class="generation-grid">

            @foreach($nodes as $node)

                <article class="member-card">

                    <div class="member-card-top">

                        <div class="member-avatar">
                            {{ strtoupper(substr($node['name'], 0, 1)) }}
                        </div>

                        <div class="member-info">

                            <span class="member-label">
                                Generation {{ $generation }}
                            </span>

                            <h4>
                                {{ $node['name'] }}
                            </h4>

                            <p>
                                Referral:
                                <strong>
                                    {{ $node['referral_code'] }}
                                </strong>
                            </p>

                        </div>

                        <span
                            class="member-status
                            {{ strtolower($node['status']) === 'active'
                                ? 'is-active'
                                : 'is-inactive' }}"
                        >
                            {{ $node['status'] }}
                        </span>

                    </div>

                    <div class="member-card-footer">

                        <div>
                            <span>Downlines</span>

                            <strong>
                                {{ count($node['downlines'] ?? []) }}
                            </strong>
                        </div>

                        @if(!empty($node['downlines']))

                            <button
                                wire:click="focusUser({{ $node['id'] }})"
                                class="member-explore-btn"
                            >
                                Explore Branch
                                <span>→</span>
                            </button>

                        @endif

                    </div>

                </article>

            @endforeach

        </div>

        @php
            $nextGenerationNodes = [];

            foreach ($nodes as $node) {
                foreach ($node['downlines'] ?? [] as $child) {
                    $nextGenerationNodes[] = $child;
                }
            }
        @endphp

        @if(!empty($nextGenerationNodes))

            @include(
                'livewire.member-pages.genealogy.partials.generation-list',
                [
                    'nodes' => $nextGenerationNodes,
                    'generation' => $generation + 1
                ]
            )

        @endif

    </section>

@endif
