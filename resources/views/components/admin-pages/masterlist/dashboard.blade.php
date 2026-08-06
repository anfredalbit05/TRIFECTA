<div class="space-y-6">

    <!-- HEADER WITH CONTROLS -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#0C2C6B]">
                Dashboard Overview
            </h2>
            <p class="text-xs sm:text-sm text-slate-500">
                Masterlist overview information, regional distributions, and system statistics.
            </p>
        </div>

        <!-- REFRESH BUTTON -->
        <button wire:click="refreshDashboard" wire:loading.attr="disabled" title="Refresh Dashboard"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs sm:text-sm font-semibold rounded-xl shadow-sm transition cursor-pointer self-start sm:self-auto">
            <span wire:loading.remove wire:target="refreshDashboard">🔄</span>
            <span wire:loading wire:target="refreshDashboard" class="animate-spin">⏳</span>
            <span>Refresh</span>
        </button>
    </div>

    <!-- KPI CARDS -->
    @php
        $cards = [
            ['Total Members', $totalMembers, '👥', 'bg-blue-600'],
            ['Active Branches', $activeBranches, '🏢', 'bg-purple-600'],
            ['Benefits', $totalBenefits, '🎁', 'bg-pink-600'],
            ['Inquiries', $totalInquiries, '📩', 'bg-red-600'],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($cards as $card)
            <div class="rounded-xl text-white p-4 sm:p-5 shadow-lg transition-transform hover:-translate-y-1 {{ $card[3] }}">
                <div class="text-2xl sm:text-3xl mb-2">
                    {{ $card[2] }}
                </div>
                <p class="text-[10px] sm:text-xs uppercase tracking-wider opacity-80 font-medium">
                    {{ $card[0] }}
                </p>
                <h3 class="text-xl sm:text-2xl font-bold mt-1">
                    {{ number_format($card[1]) }}
                </h3>
            </div>
        @endforeach
    </div>

    <!-- SUMMARY STATISTICS & EXCEL EXPORT / PRINT SECTION -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm space-y-6">
        <!-- Top Filter Bar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider">Summary Overview of Members</h3>
                <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Filter stats overview or select specific options below to export and print reports.</p>
            </div>
            <div class="w-full sm:w-auto">
                <select wire:model.live="summaryFilter"
                    class="w-full sm:w-auto text-xs border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-slate-50 font-semibold text-slate-700 shadow-sm cursor-pointer">
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
                <p class="text-xl sm:text-2xl font-bold text-slate-800 mt-2">{{ number_format($this->summaryStats['total']) }}</p>
            </div>
            <div class="bg-blue-50/50 rounded-xl p-4 border border-blue-100 flex flex-col justify-between">
                <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Active Members</p>
                <p class="text-xl sm:text-2xl font-bold text-[#0C2C6B] mt-2">{{ number_format($this->summaryStats['active']) }}</p>
            </div>
            <div class="bg-slate-100/70 rounded-xl p-4 border border-slate-200 flex flex-col justify-between">
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Inactive Members</p>
                <p class="text-xl sm:text-2xl font-bold text-slate-700 mt-2">{{ number_format($this->summaryStats['inactive']) }}</p>
            </div>
        </div>

        <!-- REPORT FILTERS & EXPORT TOOLBAR -->
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col lg:flex-row items-center justify-between gap-4">
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
                    class="flex-1 sm:flex-none h-10 px-4 inline-flex items-center justify-center gap-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 hover:text-emerald-600 rounded-xl shadow-sm transition text-xs font-semibold cursor-pointer">
                    <span wire:loading.remove wire:target="exportExcel" class="text-base">📥</span>
                    <span wire:loading wire:target="exportExcel" class="animate-spin text-xs">⏳</span>
                    <span>Export Excel</span>
                </button>
                <!-- PRINT REPORT BUTTON -->
                <button onclick="window.print()" title="Print Report"
                    class="flex-1 sm:flex-none h-10 px-4 inline-flex items-center justify-center gap-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 hover:text-blue-600 rounded-xl shadow-sm transition text-xs font-semibold cursor-pointer">
                    <span class="text-base">🖨️</span>
                    <span>Print Report</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MAP CONTAINER -->
    <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100">
        <h3 class="font-bold text-[#0C2C6B] text-base sm:text-lg mb-4">
            Member Locations
        </h3>
        <div id="users-map" class="w-full h-[350px] sm:h-[450px] lg:h-[500px] rounded-xl border border-slate-200"></div>
    </div>

    <!-- PRINTABLE REPORT TEMPLATE & TABLE (Hidden on screen, visible only when printing) -->
    <div id="printable-report" class="hidden print:block font-sans mb-4">
        <div class="text-center font-sans mb-5 pb-3 border-b-2 border-[#1b365d]">
            <h1 class="m-0 text-2xl font-bold text-[#1b365d] font-serif">TRUE LIFE DAYONGAN</h1>
            <p class="my-1 text-[11px] font-bold text-[#c5a059] uppercase tracking-wider">&mdash; S O L I D A R I T A S &mdash;</p>
            <p class="my-0.5 text-[8px] text-[#1b365d] uppercase tracking-wider">&mdash; Vitae Auxilium &mdash;</p>
            <h2 class="mt-2.5 mb-1 text-sm uppercase text-slate-800 font-bold">LIST OF MEMBERS</h2>
        </div>
    </div>

    <div class="member-table hidden print:block overflow-x-auto">
        <table class="w-full border-collapse font-sans text-[10px]">
            <thead>
                <tr class="bg-[#0C2C6B] text-white font-bold text-center h-[22px]">
                    <th class="border border-slate-900 p-1">#</th>
                    <th class="border border-slate-900 p-1">Full Name</th>
                    <th class="border border-slate-900 p-1">Birthdate</th>
                    <th class="border border-slate-900 p-1">Gender</th>
                    <th class="border border-slate-900 p-1">Civil Status</th>
                    <th class="border border-slate-900 p-1">Contact Number</th>
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
                        <td class="border border-slate-900 p-1 text-center">{{ $index + 1 }}</td>
                        <td class="border border-slate-900 p-1 font-bold">{{ $fullName }}</td>
                        <td class="border border-slate-900 p-1 text-center">{{ $birthdateFormatted }}</td>
                        <td class="border border-slate-900 p-1 text-center">{{ $member->gender ?? '-' }}</td>
                        <td class="border border-slate-900 p-1 text-center">{{ $member->civilstatus ?? '-' }}</td>
                        <td class="border border-slate-900 p-1 text-center">{{ $member->contactno ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="border border-slate-900 p-2.5 text-center">No members found for the selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <style>
        @media print {
            @page {
                size: auto;
                margin: 8mm;
            }

            body * {
                visibility: hidden !important;
            }

            #printable-report,
            #printable-report *,
            .member-table,
            .member-table * {
                visibility: visible !important;
            }

            #printable-report {
                display: block !important;
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
            }

            .member-table {
                display: block !important;
                position: absolute !important;
                left: 0 !important;
                top: 110px !important;
                width: 100% !important;
            }

            table {
                border-collapse: collapse !important;
                width: 100% !important;
            }

            th,
            td {
                border: 1px solid #000 !important;
                padding: 4px !important;
            }

            th {
                background-color: #0C2C6B !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</div>

<!-- MAP INITIALIZATION SCRIPT -->
<script>
    let myMap = null;

    function initMap() {
        const mapContainer = document.getElementById('users-map');
        if (!mapContainer) return;

        if (myMap !== null) {
            myMap.remove();
            myMap = null;
        }

        setTimeout(() => {
            const users = @json($mapUsers);

            myMap = L.map('users-map').setView([8.4542, 124.6319], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(myMap);

            const bounds = [];

            const personIcon = L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/149/149071.png',
                iconSize: [40, 40],
                iconAnchor: [20, 40],
                popupAnchor: [0, -40]
            });

            users.forEach(user => {
                const lat = parseFloat(user.latitude);
                const lng = parseFloat(user.longitude);

                if (!isNaN(lat) && !isNaN(lng)) {
                    const marker = L.marker([lat, lng], {
                        icon: personIcon
                    }).addTo(myMap);

                    marker.bindPopup(`
                        <div style="font-size: 12px; line-height: 1.4;">
                            Name: <strong>${user.FirstName} ${user.LastName}</strong><br>
                            Address: <strong>${user.address}</strong>
                        </div>
                    `);

                    bounds.push([lat, lng]);
                }
            });

            if (bounds.length > 0) {
                myMap.fitBounds(bounds, {
                    padding: [50, 50]
                });
            }
        }, 100);
    }

    document.addEventListener('DOMContentLoaded', initMap);

    document.addEventListener('livewire:initialized', () => {
        initMap();
        Livewire.hook('morph.updated', ({
            el,
            component
        }) => {
            if (document.getElementById('users-map')) {
                initMap();
            }
        });
    });
</script>
