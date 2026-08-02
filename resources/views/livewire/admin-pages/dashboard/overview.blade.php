<div class="space-y-6">

    <!-- HEADER -->
    <div>
        <h2 class="text-2xl font-bold text-[#0C2C6B]">
            Overview Analytics
        </h2>
        <p class="text-sm text-gray-500">
            System performance and statistics overview
        </p>
    </div>

    <!-- KPI CARDS -->
    @php
        $cards = [
            ['Members', $totalMembers, '👥', 'bg-blue-600'],
            ['Active', $activeMembers, '✅', 'bg-green-600'],
            ['Branches', $totalBranches, '🏢', 'bg-purple-600'],
            ['Benefits', $totalBenefits, '🎁', 'bg-pink-600'],
            ['Beneficiaries', $totalBeneficiaries, '💳', 'bg-orange-600'],
            ['Inquiries', $totalInquiry, '📩', 'bg-red-600'],
        ];
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4">
        @foreach($cards as $card)
            <div class="rounded-xl text-white p-5 shadow-lg transition-transform hover:-translate-y-1 {{ $card[3] }}">
                <div class="text-3xl mb-2">
                    {{ $card[2] }}
                </div>
                <p class="text-xs uppercase tracking-wider opacity-80 font-medium">
                    {{ $card[0] }}
                </p>
                <h3 class="text-2xl font-bold mt-1">
                    {{ number_format($card[1]) }}
                </h3>
            </div>
        @endforeach
    </div>

    <!-- SECOND ROW -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- RECENT MEMBERS -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-[#0C2C6B] text-lg mb-4 flex items-center gap-2">
                    Recent Members
                </h3>
                <div class="divide-y divide-gray-100">
                    @forelse($recentMembers as $member)
                        <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{ $member->firstname }} {{ $member->lastname }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $member->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $member->status ? 'bg-green-50 text-green-600' : 'bg-gray-50 text-gray-500' }}">
                                {{ $member->status ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 py-4 text-center">No recent members found.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- INQUIRY ANALYTICS -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-[#0C2C6B] text-lg mb-4">
                    Inquiry Analytics
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm font-medium text-gray-600">Unread Inquiries</span>
                        <span class="font-bold text-red-600 bg-red-50 px-3 py-1 rounded-lg text-sm">
                            {{ $unreadInquiry }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm font-medium text-gray-600">Read Inquiries</span>
                        <span class="font-bold text-green-600 bg-green-50 px-3 py-1 rounded-lg text-sm">
                            {{ $readInquiry }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- BENEFITS DISTRIBUTION -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-[#0C2C6B] text-lg mb-4">
                Benefits Distribution
            </h3>
            <div class="divide-y divide-gray-100 max-h-[250px] overflow-y-auto pr-1">
                @forelse($benefitStats as $benefit)
                    <div class="flex justify-between items-center py-2.5 text-sm">
                        <span class="text-gray-600 truncate pr-2">{{ $benefit->name }}</span>
                        <span class="font-bold text-[#0C2C6B] bg-blue-50 px-2.5 py-0.5 rounded-md text-xs">
                            {{ $benefit->beneficiaries_count }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 py-4 text-center">No benefit stats available.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- BRANCH TABLE -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 overflow-hidden">
        <h3 class="font-bold text-[#0C2C6B] text-lg mb-4">
            Branch Performance
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="pb-3 pl-2">Branch Name</th>
                        <th class="pb-3 pr-2 text-right">Total Users</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse($branchStats as $branch)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3.5 pl-2 font-medium text-gray-800">
                                {{ $branch->branchname }}
                            </td>
                            <td class="py-3.5 pr-2 text-right font-semibold text-[#0C2C6B]">
                                {{ $branch->user_count }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center py-4 text-gray-400 text-sm">No branch data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- NEWS & DEMOGRAPHICS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- LATEST NEWS -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6">
            <h3 class="font-bold text-[#0C2C6B] text-lg mb-4">
                Latest News
            </h3>
            <div class="divide-y divide-gray-100">
                @forelse($recentNews as $news)
                    <div class="py-3 first:pt-0 last:pb-0">
                        <p class="font-semibold text-gray-800 text-sm hover:text-[#0C2C6B] transition-colors cursor-pointer">
                            {{ $news->title }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $news->created_at->diffForHumans() }}
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 py-4 text-center">No news updates available.</p>
                @endforelse
            </div>
        </div>

        <!-- GENDER DEMOGRAPHICS -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6">
            <h3 class="font-bold text-[#0C2C6B] text-lg mb-4">
                Member Demographics
            </h3>
            <div class="space-y-3">
                @forelse($genderStats as $gender)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm font-medium text-gray-700">
                            {{ $gender->gender }}
                        </span>
                        <span class="font-bold text-[#0C2C6B] bg-white px-3 py-1 rounded-lg shadow-2xs text-sm">
                            {{ $gender->total }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 py-4 text-center">No demographic data available.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- CHARTS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- MONTHLY REGISTRATIONS -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6">
            <h3 class="font-bold text-[#0C2C6B] text-lg mb-4">
                Monthly Registrations
            </h3>
            <div class="relative h-64 sm:h-80 w-full">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- GENDER CHART -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6">
            <h3 class="font-bold text-[#0C2C6B] text-lg mb-4">
                Gender Distribution (Male, Female, Other)
            </h3>
            <div class="relative h-64 sm:h-80 w-full">
                <canvas id="genderChart"></canvas>
            </div>
        </div>

    </div>


</div>

<!-- UNIFIED CHART INITIALIZATION -->
<script>
    document.addEventListener('livewire:initialized', () => {

        // 1. Monthly Registrations Bar Chart (Steps of 1, 2, 3...)
        const monthlyLabels = @json($monthlyUsers->pluck('month'));
        const monthlyData = @json($monthlyUsers->pluck('total'));

        new Chart(document.getElementById('monthlyChart'), {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'New Members',
                    data: monthlyData,
                    backgroundColor: '#0C2C6B',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        }
                    }
                }
            }
        });

        // 2. Gender Distribution Doughnut Chart (Male, Female, Other)
        const genderLabels = @json($genderStats->pluck('gender'));
        const genderData = @json($genderStats->pluck('total'));

        new Chart(document.getElementById('genderChart'), {
            type: 'doughnut',
            data: {
                labels: genderLabels,
                datasets: [{
                    data: genderData,
                    backgroundColor: [
                        '#0C2C6B', // Male (Blue)
                        '#D79B10', // Female (Pink)
                        '#475569'  // Other (Green)
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // 3. Branch Performance Line Chart
        const branchLabels = @json($branchStats->pluck('branchname'));
        const branchData = @json($branchStats->pluck('user_count'));

        new Chart(document.getElementById('branchChart'), {
            type: 'line',
            data: {
                labels: branchLabels,
                datasets: [{
                    label: 'Users',
                    data: branchData,
                    borderColor: '#7c3aed',
                    backgroundColor: '#7c3aed33',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        }
                    }
                }
            }
        });

    });
</script>
