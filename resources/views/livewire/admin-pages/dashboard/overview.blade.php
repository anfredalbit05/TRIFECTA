<div class="space-y-6">

    <!-- HEADER WITH CONTROLS -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#0C2C6B]">
                Overview Analytics
            </h2>
            <p class="text-sm text-gray-500">
                System performance and statistics overview for <span class="font-semibold text-gray-700" x-text="selectedYear">{{ $selectedYear }}</span>
            </p>
        </div>

        <!-- ACTIONS: YEAR FILTER & REFRESH -->
        <div class="flex items-center gap-3">
            <!-- YEAR DROPDOWN -->
                <select wire:model.live="selectedYear"
                    class="bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] cursor-pointer">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>

            <!-- REFRESH BUTTON -->
            <button wire:click="refreshAnalytics" wire:loading.attr="disabled" title="Refresh Analytics"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl shadow-sm transition cursor-pointer">
                <span wire:loading.remove wire:target="refreshAnalytics">🔄</span>
                <span wire:loading wire:target="refreshAnalytics" class="animate-spin">⏳</span>
                <span class="hidden sm:inline">Refresh</span>
            </button>
        </div>
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
                        <p class="text-sm text-gray-400 py-4 text-center">No recent members found for this year.</p>
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
                    <p class="text-sm text-gray-400 py-4 text-center">No news updates available for this year.</p>
                @endforelse
            </div>
        </div>

        <!-- TOP REFERRERS -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6" x-data="{ modalOpen: false, activeImage: '', activeName: '', activeAddress: '' }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-[#0C2C6B] text-lg">
                    Top Members (Most Referrals)
                </h3>
                <span class="text-xs font-semibold bg-blue-50 text-[#0C2C6B] px-2.5 py-1 rounded-lg">
                    Leaderboard
                </span>
            </div>

            <!-- Scrollable container -->
            <div class="space-y-3 max-h-[380px] overflow-y-auto pr-1">
                @forelse($topReferrers as $index => $referrer)
                    @php
                        $rankColors = [
                            0 => 'bg-amber-100 text-amber-700 border-amber-200',
                            1 => 'bg-slate-100 text-slate-700 border-slate-200',
                            2 => 'bg-orange-100 text-orange-700 border-orange-200'
                        ];
                        $badgeStyle = $rankColors[$index] ?? 'bg-gray-50 text-gray-500 border-gray-100';
                    @endphp

                    <div class="flex items-center justify-between p-3.5 bg-gray-50/70 hover:bg-gray-50 border border-gray-100/80 rounded-xl transition-all">
                        <div class="flex items-center gap-3.5">
                            <div class="w-7 h-7 flex items-center justify-center rounded-lg text-xs font-bold border {{ $badgeStyle }}">
                                #{{ $index + 1 }}
                            </div>
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-[#0C2C6B]/10 flex items-center justify-center shrink-0 border border-gray-200">
                                @if(!empty($referrer->profilepic))
                                    <img src="{{ asset('storage/' . $referrer->profilepic) }}"
                                         alt="{{ $referrer->firstname }}"
                                         class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition-opacity"
                                         @click="modalOpen = true; activeImage = '{{ asset('storage/' . $referrer->profilepic) }}'; activeName = '{{ $referrer->firstname }} {{ $referrer->lastname }}'; activeAddress = '{{ $referrer->address }}'">
                                @else
                                    <span class="text-[#0C2C6B] font-bold text-sm tracking-wide">
                                        {{ strtoupper(substr($referrer->firstname, 0, 1)) }}{{ strtoupper(substr($referrer->lastname, 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800 leading-tight">
                                    {{ $referrer->firstname }} {{ $referrer->lastname }}
                                </p>
                                <p class="text-xs text-gray-400 font-mono mt-1 flex items-center gap-1">
                                    <span>Code:</span>
                                    <span class="text-[#0C2C6B] font-semibold bg-white px-1.5 py-0.5 rounded border border-gray-200">
                                        {{ $referrer->referralCode }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1 font-extrabold text-[#0C2C6B] bg-blue-50/80 border border-blue-100 px-3 py-1.5 rounded-xl text-sm shadow-sm">
                                {{ $referrer->total_invites }}
                                <span class="text-xs font-medium text-blue-600/70">Invites</span>
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <p class="text-3xl mb-2">🏆</p>
                        <p class="text-sm text-gray-400">No referral records found for this year.</p>
                    </div>
                @endforelse
            </div>

            <!-- IMAGE PREVIEW MODAL -->
            <div x-show="modalOpen"
                 x-transition.opacity
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
                 style="display: none;"
                 @click.self="modalOpen = false">
                <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-center relative shadow-2xl" @click.stop>
                    <button @click="modalOpen = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 font-bold text-lg cursor-pointer">
                        &times;
                    </button>
                    <h4 class="font-bold text-[#0C2C6B] text-base mb-4" x-text="activeName"></h4>
                    <div class="w-48 h-48 mx-auto rounded-full overflow-hidden border-4 border-gray-100 shadow-inner mb-4">
                        <img :src="activeImage" alt="Profile View" class="w-full h-full object-cover">
                    </div>
                    <p class="text-xs text-gray-500 mb-4 flex items-center justify-center gap-1">
                        📍 <span x-text="activeAddress"></span>
                    </p>
                    <button @click="modalOpen = false" class="w-full bg-[#0C2C6B] text-white py-2 rounded-xl text-sm font-semibold hover:bg-[#081f4f] transition-colors cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- CHARTS SECTION -->
    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6">
            <h3 class="font-bold text-[#0C2C6B] text-lg mb-4">
                Monthly Registrations
            </h3>
            <div class="relative h-64 sm:h-80 w-full">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

</div>

<!-- CHART INITIALIZATION WITH LIVEWIRE RE-RENDER SUPPORT -->
<script>
    let monthlyChartInstance = null;

    function renderMonthlyChart() {
        const ctx = document.getElementById('monthlyChart');
        if (!ctx) return;

        const monthlyLabels = @json($monthlyUsers->pluck('month'));
        const monthlyData = @json($monthlyUsers->pluck('total'));

        if (monthlyChartInstance) {
            monthlyChartInstance.destroy();
        }

        monthlyChartInstance = new Chart(ctx, {
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
    }

    document.addEventListener('DOMContentLoaded', renderMonthlyChart);
    document.addEventListener('livewire:initialized', () => {
        renderMonthlyChart();
        Livewire.hook('morph.updated', ({ el, component }) => {
            renderMonthlyChart();
        });
    });
</script>
