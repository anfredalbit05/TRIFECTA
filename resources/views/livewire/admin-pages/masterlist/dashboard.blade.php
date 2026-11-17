<div class="space-y-6">

    <!-- HEADER WITH CONTROLS -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-[var(--color-primary)]">
                Dashboard Overview
            </h2>
            <p class="text-xs sm:text-sm text-slate-500">
                Masterlist overview information, regional distributions, and system statistics.
            </p>
        </div>

        {{-- REFRESH --}}
        <button
            type="button"
            wire:click="refreshDashboard"
            wire:loading.attr="disabled"
            title="Refresh Dashboard"
            class="refresh-btn"
        >
            {{-- NORMAL STATE --}}
            <span
                wire:loading.remove
                wire:target="refreshDashboard"
                class="refresh-icon"
            >
                <svg
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 4v5h5M20 20v-5h-5M5.5 9A7 7 0 0 1 18 6.5L20 9M18.5 15A7 7 0 0 1 6 17.5L4 15"
                    />
                </svg>
            </span>

            {{-- LOADING STATE --}}
            <span
                wire:loading
                wire:target="refreshDashboard"
                class="refresh-loading"
            >
                <svg
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <circle
                        cx="12"
                        cy="12"
                        r="9"
                        stroke-width="3"
                        class="refresh-circle"
                    />
                    <path
                        d="M21 12a9 9 0 0 1-9 9"
                        stroke-width="3"
                        stroke-linecap="round"
                    />
                </svg>
            </span>

            <span>Refresh</span>
        </button>
    </div>

    <!-- KPI CARDS (Dark Glassmorphism with var(--color-primary) Base, Watermark Icons Only) -->
    @php
        $cards = [
            ['Total Members', $totalMembers, '👥', 'hover:shadow-[0_18px_40px_-12px_var(--color-primary)]'],
            ['Active Branches', $activeBranches, '🏢', 'hover:shadow-[0_18px_40px_-12px_var(--color-primary)]'],
            ['Benefits', $totalBenefits, '🎁', 'hover:shadow-[0_18px_40px_-12px_var(--color-primary)]'],
            ['Inquiries', $totalInquiries, '📩', 'hover:shadow-[0_18px_40px_-12px_var(--color-primary)]'],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($cards as $card)
            <div
                class="relative overflow-hidden rounded-2xl p-4 sm:p-5 backdrop-blur-xl bg-gradient-to-br from-[var(--color-primary)] via-[var(--color-primary-mid)] to-[var(--color-primary-mid)] border border-white/10 {{ $card[3] }} shadow-xl transition-all duration-300 hover:-translate-y-1.5 group text-white">

                <!-- Large Background Overlay Icon Watermark -->
                <div
                    class="absolute -right-3 -bottom-3 text-7xl text-white opacity-10 transition-transform duration-500 group-hover:scale-125 group-hover:opacity-20 pointer-events-none select-none brightness-0 invert">
                    {{ $card[2] }}
                </div>

                <!-- Content Container -->
                <div class="relative z-10">
                    <p class="text-[10px] sm:text-xs uppercase tracking-wider text-white font-semibold">
                        {{ $card[0] }}
                    </p>
                    <h3 class="text-xl sm:text-2xl font-bold mt-1 text-white tracking-tight drop-shadow-sm">
                        {{ number_format($card[1]) }}
                    </h3>
                </div>

            </div>
        @endforeach
    </div>

    <!-- SUMMARY STATISTICS & EXCEL EXPORT / PRINT SECTION -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm space-y-6">
        <!-- Top Filter Bar -->
        <div
            class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider">Summary Overview of
                    Members</h3>
                <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Filter stats overview or select specific options
                    below to export and print reports.</p>
            </div>
            <div class="w-full sm:w-auto">
                <select wire:model.live="summaryFilter"
                    class="w-full sm:w-auto text-xs border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] bg-slate-50 font-semibold text-slate-700 shadow-sm cursor-pointer">
                    <option value="all">Overview: All Time</option>
                    <option value="year">Overview: This Year</option>
                    <option value="month">Overview: This Month</option>
                    <option value="week">Overview: This Week</option>
                    <option value="today">Overview: Today</option>
                </select>
            </div>
        </div>

        <!-- STATS CARDS GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 flex flex-col justify-between">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Members</p>
                <p class="text-xl sm:text-2xl font-bold text-slate-800 mt-2">
                    {{ number_format($this->summaryStats['total']) }}</p>
            </div>
            <div class="bg-[var(--color-primary-tint)]/50 rounded-xl p-4 border border-[var(--color-primary-tint)] flex flex-col justify-between">
                <p class="text-xs font-semibold text-[var(--color-primary)] uppercase tracking-wider">Active Members</p>
                <p class="text-xl sm:text-2xl font-bold text-[var(--color-primary)] mt-2">
                    {{ number_format($this->summaryStats['active']) }}</p>
            </div>
            <div class="bg-slate-100/70 rounded-xl p-4 border border-slate-200 flex flex-col justify-between">
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Inactive Members</p>
                <p class="text-xl sm:text-2xl font-bold text-slate-700 mt-2">
                    {{ number_format($this->summaryStats['inactive']) }}</p>
            </div>
        </div>

        <!-- REPORT FILTERS & EXPORT TOOLBAR -->
        <div
            class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col lg:flex-row items-center justify-between gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 w-full lg:w-auto">
                <!-- YEAR SELECTOR -->
                <select wire:model.live="exportYear"
                    class="w-full text-xs border border-slate-200 rounded-xl px-3 py-2 bg-white font-medium text-slate-700 shadow-sm cursor-pointer">
                    <option value="">All Years</option>
                    @for ($year = 2020; $year <= date('Y'); $year++)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>

                <!-- MONTH SELECTOR -->
                <select wire:model.live="exportMonth"
                    class="w-full text-xs border border-slate-200 rounded-xl px-3 py-2 bg-white font-medium text-slate-700 shadow-sm cursor-pointer">
                    <option value="">All Months</option>
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>

                <!-- STATUS SELECTOR -->
                <select wire:model.live="exportStatus"
                    class="w-full text-xs border border-slate-200 rounded-xl px-3 py-2 bg-white font-medium text-slate-700 shadow-sm cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="Active">Active Only</option>
                    <option value="Inactive">Inactive Only</option>
                </select>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="flex items-center gap-2 w-full lg:w-auto justify-end">
                <!-- EXCEL EXPORT BUTTON -->
                <button wire:click="exportExcel" wire:loading.attr="disabled" title="Export to Excel (.xls)"
                    class="flex-1 sm:flex-none h-10 px-4 inline-flex items-center justify-center gap-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 hover:text-[var(--color-primary)] rounded-xl shadow-sm transition text-xs font-semibold cursor-pointer">
                    <span wire:loading.remove wire:target="exportExcel" class="text-base">📥</span>
                    <span wire:loading wire:target="exportExcel" class="animate-spin text-xs">⏳</span>
                    <span>Export Excel</span>
                </button>
                <!-- PRINT REPORT BUTTON -->
                <button onclick="window.print()" title="Print Report"
                    class="flex-1 sm:flex-none h-10 px-4 inline-flex items-center justify-center gap-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 hover:text-[var(--color-primary)] rounded-xl shadow-sm transition text-xs font-semibold cursor-pointer">
                    <span class="text-base">🖨️</span>
                    <span>Print Report</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MAP CONTAINER -->
    <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100">
        <h3 class="font-bold text-[var(--color-primary)] text-base sm:text-lg mb-4">
            Member Locations
        </h3>
        <div id="users-map" data-users="{{ collect($mapUsers)->toJson() }}" class="w-full h-[350px] sm:h-[450px] lg:h-[500px] rounded-xl border border-slate-200"></div>
    </div>

    <!-- PRINTABLE REPORT TEMPLATE & TABLE (Hidden on screen, visible only when printing) -->
    <div id="printable-report" class="hidden print:block font-sans mb-4">
        <div class="text-center font-sans mb-5 pb-3 border-b-2 border-[var(--color-primary-dark)]">
            <h1 class="m-0 text-2xl font-bold text-[var(--color-primary-dark)] font-serif">TRIFECTA</h1>
            <p class="my-1 text-[11px] font-bold text-[var(--color-accent-dark)] uppercase tracking-wider">&mdash; S O L I D A R I T A S
                &mdash;</p>
            <p class="my-0.5 text-[8px] text-[var(--color-primary-dark)] uppercase tracking-wider">&mdash; Vitae Auxilium &mdash;</p>
            <h2 class="mt-2.5 mb-1 text-sm uppercase text-slate-800 font-bold">LIST OF MEMBERS & BENEFICIARIES</h2>
        </div>
    </div>

    <div class="member-table hidden print:block overflow-x-auto">
        <table class="w-full border-collapse font-sans text-[10px]">
            <thead>
                <tr class="bg-[var(--color-primary)] text-white font-bold text-center h-[22px]">
                    <th class="border border-slate-900 p-1">#</th>
                    <th class="border border-slate-900 p-1">Full Name</th>
                    <th class="border border-slate-900 p-1">Birthdate</th>
                    <th class="border border-slate-900 p-1">Gender</th>
                    <th class="border border-slate-900 p-1">Civil Status</th>
                    <th class="border border-slate-900 p-1">Contact Number</th>
                    <th class="border border-slate-900 p-1">Beneficiaries</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($printMembers as $index => $member)
                    @php
                        $fullName = trim(
                            ($member->lastname ?? '') .
                                ', ' .
                                ($member->firstname ?? '') .
                                ' ' .
                                ($member->middlename ?? ''),
                        );
                        $birthdateFormatted = $member->birthdate ? date('F d, Y', strtotime($member->birthdate)) : '-';
                    @endphp
                    <tr>
                        <td class="border border-slate-900 p-1 text-center align-top">{{ $index + 1 }}</td>
                        <td class="border border-slate-900 p-1 font-bold align-top">{{ $fullName }}</td>
                        <td class="border border-slate-900 p-1 text-center align-top">{{ $birthdateFormatted }}</td>
                        <td class="border border-slate-900 p-1 text-center align-top">{{ $member->gender ?? '-' }}
                        </td>
                        <td class="border border-slate-900 p-1 text-center align-top">
                            {{ $member->civilstatus ?? '-' }}</td>
                        <td class="border border-slate-900 p-1 text-center align-top">{{ $member->contactno ?? '-' }}
                        </td>
                        <td class="border border-slate-900 p-1 align-top">
                            @if ($member->beneficiaries && $member->beneficiaries->count() > 0)
                                <ul class="list-disc pl-3 m-0">
                                    @foreach ($member->beneficiaries as $beneficiary)
                                        <li>
                                            <strong>{{ $beneficiary->fullname }}</strong>
                                            @if ($beneficiary->relationship)
                                                ({{ $beneficiary->relationship }})
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-slate-500 italic">None</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="border border-slate-900 p-2.5 text-center">No members found for the
                            selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
