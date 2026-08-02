<div class="masterlist-overview w-full px-4 sm:px-6 lg:px-8 py-6">

    <h2 class="masterlist-title text-2xl font-bold text-gray-800 mb-1">
        Dashboard
    </h2>

    <p class="masterlist-description text-gray-500 mb-6">
        Masterlist overview information.
    </p>

    <div class="master-summary-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

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

    <div class="mt-8 bg-white p-6 rounded-xl shadow-md border border-gray-100">

    <h3 class="text-lg font-bold text-gray-800 mb-4">
        Member Locations
    </h3>

    <div id="users-map" class="w-full h-[500px] rounded-xl"></div>

</div>

</div>
<script>
    document.addEventListener('livewire:initialized', () => {

        setTimeout(() => {

            const users = @json($mapUsers);

            const map = L.map('users-map').setView([8.4542, 124.6319], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const bounds = [];

            const personIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/149/149071.png',

            iconSize: [40, 40],       // width, height
            iconAnchor: [20, 40],     // point of the icon that corresponds to marker's location
            popupAnchor: [0, -40]     // popup position
        });

            users.forEach(user => {

                const lat = parseFloat(user.latitude);
                const lng = parseFloat(user.longitude);

                const marker = L.marker([lat, lng], {
                    icon: personIcon
                }).addTo(map);

                marker.bindPopup(`
                    <div>
                        Name:<strong> ${user.FirstName} ${user.LastName}</strong> <br>
                        Address:<strong> ${user.address}</strong><br>
                    </div>
                `);

                bounds.push([lat, lng]);
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds, {
                    padding: [50, 50]
                });
            }

        }, 300);

    });
</script>

