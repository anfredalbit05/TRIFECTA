<div class="genealogy-overview">

    {{-- TOOLBAR --}}
    <div class="genealogy-explorer-header genealogy-print-hide">

        <div>
            <h2 class="genealogy-explorer-title">Overview</h2>
            <p class="genealogy-explorer-subtitle">
                Search any member to view and print their direct downlines.
            </p>
        </div>

        {{-- MEMBER SEARCH --}}
        <div class="relative w-full sm:w-80">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                </svg>
            </span>

            <input
                type="text"
                wire:model.live.debounce.400ms="memberSearch"
                placeholder="Search member (name or referral code)..."
                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-9 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-[#0C2C6B] focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]/20"
            >

            @if (filled($memberSearch))
                <button type="button" wire:click="clearSearch" title="Clear search"
                    class="absolute inset-y-0 right-3 flex items-center text-slate-400 transition hover:text-slate-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif

            @if (mb_strlen(trim($memberSearch)) >= 2)
                <div class="absolute z-30 mt-2 max-h-72 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-xl">
                    @forelse ($this->searchResults as $candidate)
                        <button
                            type="button"
                            wire:click="selectMember({{ $candidate->UserID }})"
                            wire:key="candidate{{ $candidate->UserID }}"
                            class="flex w-full items-center gap-3 border-b border-slate-100 px-4 py-2.5 text-left transition last:border-b-0 hover:bg-slate-50"
                        >
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-600">
                                {{ strtoupper(substr($candidate->firstname, 0, 1)) }}
                            </span>

                            <span class="min-w-0">
                                <span class="block truncate text-sm font-semibold text-slate-700">
                                    {{ $candidate->firstname }} {{ $candidate->lastname }}
                                </span>
                                <span class="block text-[11px] text-slate-400">
                                    {{ $candidate->username }}
                                    @unless (empty($candidate->referralCode))
                                        &middot; Ref: {{ $candidate->referralCode }}
                                    @endunless
                                </span>
                            </span>
                        </button>
                    @empty
                        <p class="px-4 py-3 text-sm text-slate-400">No members found for "{{ $memberSearch }}".</p>
                    @endforelse
                </div>
            @endif
        </div>

        {{-- PRINT --}}
        @if ($this->rootMember)
            <button
                type="button"
                x-data
                @click="window.print()"
                class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-[#0C2C6B] px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#123c88] hover:shadow-md"
                title="Print direct downlines"
            >
                <span class="text-base">&#128424;</span>
                Print
            </button>
        @endif

    </div>


    @if (! $this->rootMember)

        <div class="genealogy-empty">
            <div class="genealogy-empty-icon">&#128269;</div>
            <h3>No member selected</h3>
            <p>Use the search above to pick a member and view their direct downlines.</p>
        </div>

    @else

        @php($root = $this->rootMember)
        @php($downlines = $this->downlines)

        <div class="genealogy-print-area">

            {{-- SELECTED MEMBER --}}
            <section class="generation-section generation-root">

                <div class="generation-heading">
                    <span class="generation-number">01</span>

                    <div>
                        <h3>Direct Downlines</h3>
                        <p>{{ count($downlines) }} approved {{ count($downlines) === 1 ? 'member' : 'members' }}</p>
                    </div>
                </div>

                <div class="root-member-wrapper">
                    <div class="member-card root-member-card">

                        <div class="member-card-top">
                            <div class="member-avatar">{{ strtoupper(substr($root->firstname, 0, 1)) }}</div>

                            <div class="member-info">
                                <span class="member-label">Current Member</span>
                                <h4>{{ trim($root->firstname.' '.$root->lastname) }}</h4>
                                <p>Referral: <strong>{{ $root->referralCode ?: '—' }}</strong></p>
                            </div>

                            <span class="member-status {{ strtolower($root->status) === 'active' ? 'is-active' : 'is-inactive' }}">
                                {{ $root->status }}
                            </span>
                        </div>

                        <div class="member-card-footer">
                            <div>
                                <span>Direct Downlines</span>
                                <strong>{{ count($downlines) }}</strong>
                            </div>
                        </div>

                    </div>
                </div>

            </section>


            {{-- DIRECT DOWNLINES --}}
            @if ($downlines)

                {{-- wire:key recreates this block (and resets the filter) when another member is picked --}}
                <div
                    wire:key="downlines-{{ $root->UserID }}"
                    x-data="{
                        q: '',
                        rows: @js(array_column($downlines, 'search')),
                        ok(i) {
                            const words = this.q.toLowerCase().replace(/,/g, ' ').split(/\s+/).filter(Boolean);
                            return words.every(w => this.rows[i].includes(w));
                        },
                        get shown() {
                            return this.rows.filter((_, i) => this.ok(i)).length;
                        }
                    }"
                >

                    {{-- TABLE SEARCH --}}
                    <div class="genealogy-print-hide mb-3 flex items-center justify-between gap-3">

                        <div class="relative w-full sm:w-80">
                            <input
                                type="text"
                                x-model="q"
                                @keydown.escape="q = ''"
                                placeholder="Filter by first, middle, last name or referral code..."
                                class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-3 pr-9 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-[#0C2C6B] focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]/20"
                            >

                            <button
                                type="button"
                                x-show="q"
                                x-cloak
                                @click="q = ''"
                                title="Clear filter"
                                class="absolute inset-y-0 right-3 flex items-center text-slate-400 transition hover:text-slate-600"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <p class="shrink-0 text-xs text-slate-400" x-show="q" x-cloak>
                            <span x-text="shown"></span> of {{ count($downlines) }}
                        </p>

                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
                        <table class="w-full min-w-[700px] border-collapse text-sm">

                            <thead class="bg-[#0C2C6B] text-white">
                                <tr>
                                    <th class="px-4 py-3 text-left">#</th>
                                    <th class="px-4 py-3 text-left">Member Name</th>
                                    <th class="px-4 py-3 text-left">Referral Code</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-left">Activated</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">
                                @foreach ($downlines as $i => $node)
                                    <tr
                                        wire:key="ov-node{{ $node['id'] }}"
                                        x-show="ok({{ $i }})"
                                        class="transition hover:bg-slate-50"
                                    >

                                        <td class="px-4 py-3 text-slate-500">{{ $i + 1 }}</td>

                                        <td class="px-4 py-3 font-semibold text-slate-700">{{ $node['name'] }}</td>

                                        <td class="px-4 py-3 font-mono text-slate-600">{{ $node['referral_code'] ?: '—' }}</td>

                                        <td class="px-4 py-3">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                                {{ strtolower($node['status']) === 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                                {{ $node['status'] }}
                                            </span>
                                        </td>

                                        <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $node['activated_at'] ?: '—' }}</td>

                                    </tr>
                                @endforeach
                            </tbody>

                        </table>

                        <p x-show="shown === 0" x-cloak class="px-4 py-6 text-center text-sm text-slate-400">
                            No downlines match your filter.
                        </p>
                    </div>

                </div>

            @else

                <div class="genealogy-empty">
                    <div class="genealogy-empty-icon">&#129409;</div>
                    <h3>No direct downlines</h3>
                    <p>{{ trim($root->firstname.' '.$root->lastname) }} has no approved direct downlines yet.</p>
                </div>

            @endif

        </div>

    @endif

</div>
