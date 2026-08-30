<div class="board-panel">

    <!-- FIXED TOP HEADER -->
    <div class="board-header">
        <div>
            <h2 class="board-title">Board Members Structure</h2>
            <p class="board-subtitle">
                True Life Leadership &amp; Governance Hierarchy
            </p>
        </div>
    </div>


    <!-- SCROLLABLE BOARD AREA -->
    <div class="board-tree">

        <div class="board-tree-inner">

            <!-- =========================
                 LEVEL 0: CEO
            ========================== -->
            <div class="board-level">

                <div
                    class="board-card board-card--ceo"
                    style="
                        border-top-color: #0C2C6B;
                        border-color: #0C2C6B;
                    "
                >
                    <img
                        src="{{ asset('images/user.png') }}"
                        alt="CEO Name"
                        class="board-avatar board-avatar--lg"
                    >

                    <h3 class="board-name">
                        Renante Suaybaguio Robles, MPA, LFA
                    </h3>

                    <span
                        class="board-role-badge"
                        style="
                            background-color: rgba(12, 44, 107, 0.1);
                            color: #0C2C6B;
                        "
                    >
                        Chief Executive Officer
                    </span>

                </div>

                <!-- Connector -->
                <div class="board-connector"></div>

            </div>


            <!-- =========================
                 LEVEL 1: COO
            ========================== -->
            <div class="board-level">

                <div
                    class="board-card board-card--ceo"
                    style="
                        border-top-color: #0C2C6B;
                        border-color: #0C2C6B;
                    "
                >
                    <img
                        src="{{ asset('images/user.png') }}"
                        alt="COO Name"
                        class="board-avatar board-avatar--lg"
                    >

                    <h3 class="board-name">
                        Bobby Paulo Dagala
                    </h3>

                    <span
                        class="board-role-badge"
                        style="
                            background-color: rgba(12, 44, 107, 0.1);
                            color: #0C2C6B;
                        "
                    >
                        Chief Operating Officer
                    </span>

                </div>

                <!-- Connector -->
                <div class="board-connector"></div>

            </div>


            <!-- =========================
                 CONNECTOR TO MEMBERS
            ========================== -->
            <div class="board-connector-line">

                <div class="board-connector-line__v"></div>

                <div class="board-connector-line__h"></div>

                <div class="board-connector-line__v"></div>

            </div>


            <!-- =========================
                 LEVEL 2: BOARD MEMBERS
            ========================== -->

            @php
                $boardMembers = [
                    [
                        'name' => 'Jeorge Leemart Alfeche',
                        'position' => 'Board Member',
                        'image' => 'images/BOD/Member1.png',
                    ],
                    [
                        'name' => 'Benito Igadna',
                        'position' => 'Board Member',
                        'image' => 'images/BOD/Member2.jpg',
                    ],
                    [
                        'name' => 'Pierre Hordista',
                        'position' => 'Board Member',
                        'image' => 'images/BOD/Member3.png',
                    ],
                    [
                        'name' => 'Daisy Maghuyop Colegado',
                        'position' => 'Board Member',
                        'image' => 'images/BOD/Member4.png',
                    ],
                    [
                        'name' => 'Mrs. Cheryl Eliot, RN',
                        'position' => 'Board Member',
                        'image' => 'images/BOD/Member5.png',
                    ],
                    [
                        'name' => 'Bernard Laurente',
                        'position' => 'Board Member',
                        'image' => 'images/BOD/Member6.png',
                    ],
                    [
                        'name' => 'Alfe Duron',
                        'position' => 'Board Member',
                        'image' => 'images/BOD/Member7.png',
                    ],
                    [
                        'name' => 'Cherry Lynn Nierras-Dagala, MD',
                        'position' => 'Board Member',
                        'image' => 'images/BOD/Member8.png',
                    ],
                ];
            @endphp


            <div class="board-grid">

                @foreach ($boardMembers as $member)

                    <div class="board-grid-card">

                        <img
                            src="{{ asset($member['image'] ?? 'images/user.png') }}"
                            alt="{{ $member['name'] }}"
                            class="board-grid-avatar"
                        >

                        <h4 class="board-grid-name truncate-text">
                            {{ $member['name'] }}
                        </h4>

                        <span class="board-grid-role">
                            {{ $member['position'] }}
                        </span>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

</div>
