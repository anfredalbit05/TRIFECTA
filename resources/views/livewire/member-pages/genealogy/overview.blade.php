<div class="genealogy-overview">

    {{-- PRINT STYLES (scoped; !important beats Tailwind utilities like "flex") --}}
    <style>
        @media print {
            @page { margin: 10mm; }

            .genealogy-overview .genealogy-print-hide { display: none !important; }

            .genealogy-overview .ov-wrap {
                overflow: visible !important;
                border: 0 !important;
                border-radius: 0 !important;
            }

            .genealogy-overview .ov-table { min-width: 0 !important; font-size: 11px; }
            .genealogy-overview .ov-table th,
            .genealogy-overview .ov-table td { padding: 3px 8px !important; }
            .genealogy-overview .ov-table thead { display: table-header-group; }
            .genealogy-overview .ov-table tr { break-inside: avoid; page-break-inside: avoid; }
            .genealogy-overview .ov-table span { padding-top: 0 !important; padding-bottom: 0 !important; }

            /* keep colors (header bar, badges) in the printout */
            .genealogy-overview * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

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
                        <p>
                            {{ count($downlines) }} approved {{ count($downlines) === 1 ? 'member' : 'members' }}
                            @if ($this->dateLabel)
                                &middot; Activated {{ $this->dateLabel }}
                            @endif
                        </p>
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


            {{-- Keyed by member only: the date picker survives range changes; rows carry their own search text --}}
            <div
                wire:key="downlines-{{ $root->UserID }}"
                x-data="{
                    q: '',
                    ok(s) {
                        const words = this.q.toLowerCase().replace(/,/g, ' ').split(/\s+/).filter(Boolean);
                        return words.every(w => s.includes(w));
                    },
                    get shown() {
                        const rows = this.$refs.body ? Array.from(this.$refs.body.rows) : [];
                        return rows.filter(r => this.ok(r.dataset.s)).length;
                    }
                }"
            >

                {{-- FILTER ROW: name filter, then date range --}}
                <div class="genealogy-print-hide mb-3 flex flex-wrap items-center gap-3">

                    {{-- NAME / REFERRAL FILTER --}}
                    <div class="relative w-full sm:w-80">
                        <input
                            type="text"
                            x-model="q"
                            @keydown.escape="q = ''"
                            placeholder="Filter name or referral code..."
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

                    {{-- DATE RANGE (activation date) --}}
                    <div
                        wire:ignore
                        class="relative w-full sm:w-72"
                        x-init="
                            const picker = flatpickr($refs.range, {
                                mode: 'range',
                                dateFormat: 'Y-m-d',
                                altInput: true,
                                altFormat: 'M d, Y',
                                maxDate: 'today',
                                defaultDate: @js($dateRange ? explode(' to ', $dateRange) : []),

                                onChange(dates, str, inst) {
                                    if (dates.length === 2) {
                                        $wire.set('dateRange', inst.formatDate(dates[0], 'Y-m-d') + ' to ' + inst.formatDate(dates[1], 'Y-m-d'));
                                    } else if (dates.length === 0) {
                                        $wire.set('dateRange', '');
                                    }
                                },

                                onClose(dates, str, inst) {
                                    if (dates.length === 1) {
                                        const day = inst.formatDate(dates[0], 'Y-m-d');
                                        inst.setDate([dates[0], dates[0]], false);
                                        $wire.set('dateRange', day + ' to ' + day);
                                    }
                                }
                            });

                            $wire.on('ranges-reset', () => picker.clear());
                        "
                    >
                        <span class="pointer-events-none absolute inset-y-0 left-3 z-10 flex items-center text-slate-400">&#128197;</span>

                        <input
                            x-ref="range"
                            type="text"
                            readonly
                            placeholder="Filter by activation date"
                            class="date-range-input w-full cursor-pointer rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-3 text-center text-sm font-medium text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]"
                        >
                    </div>

                    @if ($dateRange)
                        <button
                            type="button"
                            wire:click="clearDateFilter"
                            title="Clear date filter"
                            class="inline-flex cursor-pointer items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-500 shadow-sm transition hover:border-red-200 hover:bg-red-50 hover:text-red-500"
                        >
                            &#10005;
                        </button>
                    @endif

                    <p class="text-xs text-slate-400" x-show="q" x-cloak>
                        <span x-text="shown"></span> of {{ count($downlines) }}
                    </p>

                </div>


                {{-- DIRECT DOWNLINES --}}
                @if ($downlines)

                    <div class="ov-wrap overflow-x-auto rounded-xl border border-slate-200 bg-white">
                        <table class="ov-table w-full min-w-[700px] border-collapse text-sm">

                            <thead class="bg-[#0C2C6B] text-white">
                                <tr>
                                    <th class="px-4 py-3 text-left">#</th>
                                    <th class="px-4 py-3 text-left">Member Name</th>
                                    <th class="px-4 py-3 text-left">Referral Code</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-left">Activated</th>
                                </tr>
                            </thead>

                            <tbody x-ref="body" class="divide-y divide-slate-100">
                                @foreach ($downlines as $i => $node)
                                    <tr
                                        wire:key="ov-node{{ $node['id'] }}"
                                        data-s="{{ $node['search'] }}"
                                        x-show="ok($el.dataset.s)"
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

                        <p x-show="q && shown === 0" x-cloak class="px-4 py-6 text-center text-sm text-slate-400">
                            No downlines match your filter.
                        </p>
                    </div>

                @else

                    <div class="genealogy-empty">
                        <div class="genealogy-empty-icon">&#129409;</div>
                        <h3>No direct downlines</h3>
                        <p>
                            {{ trim($root->firstname.' '.$root->lastname) }} has no approved direct downlines
                            {{ $this->dateLabel ? 'activated between '.$this->dateLabel : 'yet' }}.
                        </p>
                    </div>

                @endif

            </div>

        </div>

    @endif

</div>
