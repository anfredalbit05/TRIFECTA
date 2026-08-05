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

    <!-- MAP CONTAINER -->
    <div class="mt-8 bg-white p-6 rounded-xl shadow-md border border-gray-100">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            Member Locations
        </h3>
        <div id="users-map" class="w-full h-[500px] rounded-xl"></div>
    </div>

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
