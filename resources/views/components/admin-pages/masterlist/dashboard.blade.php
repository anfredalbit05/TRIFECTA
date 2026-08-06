<div class="masterlist-overview w-full px-4 sm:px-6 lg:px-8 py-6">

    <!-- HEADER WITH REFRESH BUTTON -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-2">
        <div>
            <h2 class="masterlist-title text-2xl font-bold text-gray-800">
                Dashboard
            </h2>
            <p class="masterlist-description text-gray-500">
                Masterlist overview information.
            </p>
        </div>

        <!-- REFRESH BUTTON -->
        <button wire:click="refreshDashboard" wire:loading.attr="disabled" title="Refresh Dashboard"
            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl shadow-sm transition cursor-pointer self-start sm:self-auto">
            <span wire:loading.remove wire:target="refreshDashboard">🔄</span>
            <span wire:loading wire:target="refreshDashboard" class="animate-spin">⏳</span>
            <span>Refresh</span>
        </button>
    </div>

    <!-- SUMMARY GRID -->
    <div class="master-summary-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-4">

        <!-- Total Members Card -->
        <div class="master-summary-card bg-white p-6 rounded-xl shadow-md border border-gray-100 flex flex-col justify-between">
            <h3 class="text-sm font-semibold tracking-wide text-gray-500 uppercase">
                Total Members
            </h3>
            <p class="text-3xl font-extrabold text-[#0C2C6B] mt-2">
                {{ $totalMembers }}
            </p>
        </div>

        <!-- Active Branches Card -->
        <div class="master-summary-card bg-white p-6 rounded-xl shadow-md border border-gray-100 flex flex-col justify-between">
            <h3 class="text-sm font-semibold tracking-wide text-gray-500 uppercase">
                Active Branches
            </h3>
            <p class="text-3xl font-extrabold text-[#0C2C6B] mt-2">
                {{ $activeBranches }}
            </p>
        </div>

        <!-- Benefits Card -->
        <div class="master-summary-card bg-white p-6 rounded-xl shadow-md border border-gray-100 flex flex-col justify-between">
            <h3 class="text-sm font-semibold tracking-wide text-gray-500 uppercase">
                Benefits
            </h3>
            <p class="text-3xl font-extrabold text-[#0C2C6B] mt-2">
                {{ $totalBenefits }}
            </p>
        </div>

        <!-- Inquiries Card -->
        <div class="master-summary-card bg-white p-6 rounded-xl shadow-md border border-gray-100 flex flex-col justify-between">
            <h3 class="text-sm font-semibold tracking-wide text-gray-500 uppercase">
                Inquiries
            </h3>
            <p class="text-3xl font-extrabold text-[#0C2C6B] mt-2">
                {{ $totalInquiries }}
            </p>
        </div>

    </div>

    {{-- <!-- SUMMARY STATISTICS & EXCEL EXPORT / PRINT FILTERS -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Summary Overview</h3>
                <p class="text-xs text-slate-500">Filter stats overview or select specific options below to export and print reports.</p>
            </div>

            <div class="flex items-center gap-3">
                <!-- TIME FRAME FILTER -->
                <select wire:model.live="summaryFilter" class="text-xs border border-slate-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-slate-50 font-semibold text-slate-700">
                    <option value="all">Overview: All Time</option>
                    <option value="year">Overview: This Year</option>
                    <option value="month">Overview: This Month</option>
                    <option value="week">Overview: This Week</option>
                    <option value="today">Overview: Today</option>
                </select>
            </div>
        </div>

        <!-- CARDS GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                <p class="text-xs font-medium text-slate-500">Total Members</p>
                <p class="text-xl font-bold text-slate-800 mt-1">{{ $this->summaryStats['total'] }}</p>
            </div>
            <div class="bg-blue-50/50 rounded-lg p-3 border border-blue-100">
                <p class="text-xs font-medium text-blue-600">Active Members</p>
                <p class="text-xl font-bold text-[#0C2C6B] mt-1">{{ $this->summaryStats['active'] }}</p>
            </div>
            <div class="bg-slate-100 rounded-lg p-3 border border-slate-200">
                <p class="text-xs font-medium text-slate-600">Inactive Members</p>
                <p class="text-xl font-bold text-slate-700 mt-1">{{ $this->summaryStats['inactive'] }}</p>
            </div>
        </div>

        <!-- EXCEL EXPORT & PRINT BAR WITH ICON BUTTONS -->
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <span class="text-xs font-bold text-slate-700">Report Filters:</span>

                <!-- YEAR SELECTOR (2020 to Current Year 2026) -->
                <select wire:model.live="exportYear" class="text-xs border border-slate-200 rounded-lg px-2.5 py-2 bg-white font-medium text-slate-700 shadow-sm">
                    <option value="">All Years</option>
                    @for ($year = 2020; $year <= date('Y'); $year++)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>

                <!-- MONTH SELECTOR -->
                <select wire:model.live="exportMonth" class="text-xs border border-slate-200 rounded-lg px-2.5 py-2 bg-white font-medium text-slate-700 shadow-sm">
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
                <select wire:model.live="exportStatus" class="text-xs border border-slate-200 rounded-lg px-2.5 py-2 bg-white font-medium text-slate-700 shadow-sm">
                    <option value="">All Statuses</option>
                    <option value="Active">Active Only</option>
                    <option value="Inactive">Inactive Only</option>
                </select>
            </div>

            <!-- ICON BUTTONS CONTAINER -->
            <div class="flex items-center gap-2 w-full md:w-auto justify-end">

                <!-- EXCEL EXPORT ICON BUTTON -->
                <button wire:click="exportExcel" wire:loading.attr="disabled" title="Export to Excel (.xls)"
                    class="w-10 h-10 inline-flex items-center justify-center bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 hover:text-emerald-600 rounded-xl shadow-sm transition cursor-pointer">
                    <span wire:loading.remove wire:target="exportExcel" class="text-base">📥</span>
                    <span wire:loading wire:target="exportExcel" class="animate-spin text-xs">⏳</span>
                </button>

                <!-- PRINT ICON BUTTON -->
                <button onclick="window.print()" title="Print Report"
                    class="w-10 h-10 inline-flex items-center justify-center bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 hover:text-blue-600 rounded-xl shadow-sm transition cursor-pointer">
                    <span class="text-base">🖨️</span>
                </button>

            </div>
        </div>
    </div> --}}

    <!-- MAP CONTAINER -->
    <div class="mt-8 bg-white p-6 rounded-xl shadow-md border border-gray-100">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            Member Locations
        </h3>
        <div id="users-map" class="w-full h-[500px] rounded-xl"></div>
    </div>


    <!-- PRINT STYLING & HEADERS -->
<div id="printable-report" class="hidden print:block font-sans mb-6">
    <div style="text-align: center; margin-bottom: 20px;">
        <p style="margin: 0; font-size: 12px;">Republic of the Philippines</p>
        <p style="margin: 0; font-size: 14px; font-weight: bold;">Provincial Government of Davao del Norte</p>
        <p style="margin: 0; font-size: 11px; color: #555;">Government Center, Mankilam, Tagum City</p>
        <h2 style="margin: 15px 0 5px 0; font-size: 16px; font-weight: bold; text-transform: uppercase;">LIST OF MEMBERS</h2>
        <p style="margin: 0; font-size: 10px; color: #777;">Generated on: {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</div>

<style>
    @media print {
        @page {
            size: auto;
            margin: 0; /* Setting margins to zero forces browsers to hide headers and footers */
        }

        body * {
            visibility: hidden;
        }
        #printable-report, #printable-report *,
        .member-table, .member-table * {
            visibility: visible;
        }
        #printable-report {
            display: block !important;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }
        .member-table {
            position: absolute;
            top: 120px;
            left: 0;
            width: 100%;
            border: none !important;
            box-shadow: none !important;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1.5px solid #000 !important;
            padding: 6px !important;
            font-size: 11px;
        }
        th {
            background-color: #0C2C6B !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
        }
        /* Hide UI components like filters, search boxes, and actions column when printing */
        .members-container > div:not(.member-table),
        .members-header,
        .members-description,
        .relative.max-w-sm,
        button,
        td:last-child,
        th:last-child {
            display: none !important;
        }
    }
</style>

</div>



<script>
    let myMap = null; // Store map instance globally within script scope

    function initMap() {
        const mapContainer = document.getElementById('users-map');
        if (!mapContainer) return;

        // If a map instance already exists on this container, remove it properly
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
                        <div>
                            Name:<strong> ${user.FirstName} ${user.LastName}</strong> <br>
                            Address:<strong> ${user.address}</strong><br>
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

    // Initialize map on initial page load
    document.addEventListener('DOMContentLoaded', initMap);

    // Re-initialize map whenever Livewire finishes updating/refreshing the component
    document.addEventListener('livewire:initialized', () => {
        initMap();
        Livewire.hook('morph.updated', ({ el, component }) => {
            if (document.getElementById('users-map')) {
                initMap();
            }
        });
    });
</script>
