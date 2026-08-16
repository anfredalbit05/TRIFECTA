<div class="board-panel">

    <!-- Top Bar -->
    <div class="board-header">
        <div>
            <h2 class="board-title">Board Members Structure</h2>
            <p class="board-subtitle">True Life Leadership &amp; Governance Hierarchy</p>
        </div>
    </div>

    <!-- Tree Structure -->
    <div class="board-tree">
        <div class="board-tree-inner">

            <!-- LEVEL 0: CEO (TOP NODE) -->
            <div class="board-level">
                <div class="board-card board-card--ceo">
                    <img src="{{ asset('images/user.png') }}" alt="CEO Name" class="board-avatar board-avatar--lg board-avatar--purple">
                    <h3 class="board-name">Renante Suaybaguio Robles, MPA, LFA</h3>
                    <span class="board-role-badge board-role-badge--purple">Chief Executive Officer</span>
                </div>
                <!-- Connector down to officers -->
                <div class="board-connector"></div>
            </div>

            <!-- LEVEL 0-1: COO (TOP NODE) -->
            <div class="board-level">
                <div class="board-card board-card--ceo">
                    <img src="{{ asset('images/user.png') }}" alt="COO Name" class="board-avatar board-avatar--lg board-avatar--purple">
                    <h3 class="board-name">Bobby Paulo Dagala</h3>
                    <span class="board-role-badge board-role-badge--purple">Chief Operating Officer</span>
                </div>
                <!-- Connector down to officers -->
                <div class="board-connector"></div>
            </div>

            <!-- LEVEL 1: OFFICERS -->
            {{-- <div class="board-officers-row">

                <!-- Secretary -->
                <div class="board-card board-card--officer board-card--secretary">
                    <img src="{{ asset('images/user.png') }}" alt="Princes Manliguez" class="board-avatar board-avatar--md board-avatar--blue">
                    <h3 class="board-name">Princes Manliguez</h3>
                    <span class="board-role-badge board-role-badge--blue">Secretary</span>
                </div>

                <!-- Auditor -->
                <div class="board-card board-card--officer board-card--auditor">
                    <img src="{{ asset('images/user.png') }}" alt="Dr. Ma. Carnelita Robles" class="board-avatar board-avatar--md board-avatar--amber">
                    <h3 class="board-name">Dr. Maria Carmelita</h3>
                    <span class="board-role-badge board-role-badge--amber">Auditor</span>
                </div>

                <!-- Treasurer -->
                <div class="board-card board-card--officer board-card--treasurer">
                    <img src="{{ asset('images/user.png') }}" alt="Dr. Cherry Lynn Nierras-Dagala" class="board-avatar board-avatar--md board-avatar--emerald">
                    <h3 class="board-name">Dr. Cherry Lynn Nierras-Dagala</h3>
                    <span class="board-role-badge board-role-badge--emerald">Treasurer</span>
                </div>

                <div class="board-card board-card--officer board-card--director">
                    <img src="{{ asset('images/user.png') }}" alt="Dr. Cherry Lynn Nierras-Dagala" class="board-avatar board-avatar--md board-avatar--emerald">
                    <h3 class="board-name">Mr. Alfe Duron</h3>
                    <span class="board-role-badge board-role-badge--emerald">Board of Directors</span>
                </div>

            </div> --}}

            {{-- <!-- CONNECTOR LINE TO BOARD MEMBERS -->
            <div class="board-connector-line">
                <div class="board-connector-line__v"></div>
                <div class="board-connector-line__h"></div>
                <div class="board-connector-line__v"></div>
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

            <div class="board-grid">
                @foreach($boardMembers as $member)
                    <div class="board-grid-card">
                        <img src="{{ asset('images/user.png') }}" alt="{{ $member['name'] }}" class="board-grid-avatar">
                        <h4 class="board-grid-name truncate-text">{{ $member['name'] }}</h4>
                        <span class="board-grid-role">{{ $member['position'] }}</span>
                    </div>
                @endforeach
            </div> --}}

        </div>
    </div>

</div>
