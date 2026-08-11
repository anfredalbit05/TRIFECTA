<div class="w-full bg-slate-50 min-h-[500px] p-6 rounded-2xl border border-slate-200/80 shadow-sm">

    <!-- Top Bar -->
    <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-200">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Board Members Structure</h2>
            <p class="text-xs text-slate-500">True Life Leadership & Governance Hierarchy</p>
        </div>
    </div>

    <!-- Tree Structure -->
    <div class="w-full overflow-x-auto py-4">
        <div class="inline-flex min-w-full flex-col items-center justify-center">

            <!-- LEVEL 0: CEO (TOP NODE) -->
            <div class="flex flex-col items-center mb-6">
                <div class="bg-white p-4 rounded-xl shadow-sm border-t-4 border-purple-600 w-56 text-center transition-all hover:-translate-y-1 hover:shadow-md">
                    <img src="{{ asset('images/user.png') }}" alt="CEO Name" class="w-16 h-16 rounded-full mx-auto border-2 border-purple-100 mb-2 object-cover">
                    <h3 class="font-bold text-slate-800 text-sm">Renante Suaybaguio Robles, MPA, LFA</h3>
                    <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold text-purple-700 bg-purple-50 rounded-full mt-1">Chief Executive Officer</span>
                </div>
                <!-- Connector down to officers -->
                <div class="w-0.5 h-6 bg-slate-300 mt-2"></div>
            </div>

                 <!-- LEVEL 0-1: COO (TOP NODE) -->
            <div class="flex flex-col items-center mb-6">
                <div class="bg-white p-4 rounded-xl shadow-sm border-t-4 border-purple-600 w-56 text-center transition-all hover:-translate-y-1 hover:shadow-md">
                    <img src="{{ asset('images/user.png') }}" alt="CEO Name" class="w-16 h-16 rounded-full mx-auto border-2 border-purple-100 mb-2 object-cover">
                    <h3 class="font-bold text-slate-800 text-sm">Bobby Paulo Dagala</h3>
                    <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold text-purple-700 bg-purple-50 rounded-full mt-1">Chief Executive Officer</span>
                </div>
                <!-- Connector down to officers -->
                <div class="w-0.5 h-6 bg-slate-300 mt-2"></div>
            </div>

            <!-- LEVEL 1: OFFICERS -->
            <div class="flex flex-wrap items-center justify-center gap-6 sm:gap-10 mb-8 relative">

                <!-- Secretary -->
                <div class="bg-white p-4 rounded-xl shadow-sm border-t-4 border-blue-600 w-52 text-center transition-all hover:-translate-y-1 hover:shadow-md">
                    <img src="{{ asset('images/user.png') }}" alt="Princes Manliguez" class="w-14 h-14 rounded-full mx-auto border-2 border-blue-100 mb-2 object-cover">
                    <h3 class="font-bold text-slate-800 text-sm">Princes Manliguez</h3>
                    <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold text-blue-700 bg-blue-50 rounded-full mt-1">Secretary</span>
                </div>



                <!-- Auditor -->
                <div class="bg-white p-4 rounded-xl shadow-sm border-t-4 border-amber-600 w-52 text-center transition-all hover:-translate-y-1 hover:shadow-md">
                    <img src="{{ asset('images/user.png') }}" alt="Dr. Ma. Carnelita Robles" class="w-14 h-14 rounded-full mx-auto border-2 border-amber-100 mb-2 object-cover">
                    <h3 class="font-bold text-slate-800 text-sm">Dr. Maria Carmelita</h3>
                    <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold text-amber-700 bg-amber-50 rounded-full mt-1">Auditor</span>
                </div>

                        <!-- Treasurer -->
                <div class="bg-white p-4 rounded-xl shadow-sm border-t-4 border-emerald-600 w-52 text-center transition-all hover:-translate-y-1 hover:shadow-md">
                    <img src="{{ asset('images/user.png') }}" alt="Dr. Cherry Lynn Nierras-Dagala" class="w-14 h-14 rounded-full mx-auto border-2 border-emerald-100 mb-2 object-cover">
                    <h3 class="font-bold text-slate-800 text-sm">Dr. Cherry Lynn Nierras-Dagala</h3>
                    <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700 bg-emerald-50 rounded-full mt-1">Treasurer</span>
                </div>

                  <div class="bg-white p-4 rounded-xl shadow-sm border-t-4 border-emerald-600 w-52 text-center transition-all hover:-translate-y-1 hover:shadow-md">
                    <img src="{{ asset('images/user.png') }}" alt="Dr. Cherry Lynn Nierras-Dagala" class="w-14 h-14 rounded-full mx-auto border-2 border-emerald-100 mb-2 object-cover">
                    <h3 class="font-bold text-slate-800 text-sm">Mr. Alfe Duron</h3>
                    <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700 bg-emerald-50 rounded-full mt-1">Board of Directors</span>
                </div>

            </div>

            {{-- <!-- CONNECTOR LINE TO BOARD MEMBERS -->
            <div class="flex flex-col items-center mb-8 w-full max-w-3xl">
                <div class="w-0.5 h-6 bg-slate-300"></div>
                <div class="h-0.5 bg-slate-300 w-full"></div>
                <div class="w-0.5 h-6 bg-slate-300"></div>
            </div> --}}

            {{-- <!-- LEVEL 2: BOARD MEMBERS GRID -->
            @php
                $boardMembers = [
                    ['name' => 'Mr. Alfe Duron', 'position' => 'Board Member'],
                    ['name' => 'Ms. Ma. Daisy Colegado', 'position' => 'Board Member'],
                    ['name' => 'Mr. Bernard Laurente', 'position' => 'Board Member'],
                    ['name' => 'Mr. Pierre Hordista', 'position' => 'Board Member'],
                    ['name' => 'Mrs. Cheryl Eliot, RN', 'position' => 'Board Member'],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 w-full max-w-6xl">
                @foreach($boardMembers as $member)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200/80 text-center flex flex-col items-center hover:border-blue-400 hover:shadow-md transition-all">
                        <img src="{{ asset('images/user.png') }}" alt="{{ $member['name'] }}" class="w-12 h-12 rounded-full border border-slate-200 mb-2 object-cover">
                        <h4 class="font-bold text-slate-800 text-xs line-clamp-1">{{ $member['name'] }}</h4>
                        <span class="text-[11px] text-slate-500 font-medium mt-0.5">{{ $member['position'] }}</span>
                    </div>
                @endforeach
            </div> --}}

        </div>
    </div>

</div>
