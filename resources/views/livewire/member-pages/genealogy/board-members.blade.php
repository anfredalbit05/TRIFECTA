@php
    $boardMembers = [
        ['name' => 'Jeorge Leemart Alfeche', 'image' => 'images/BOD/Member1.png'],
        ['name' => 'Benito Igadna', 'image' => 'images/BOD/Member2.jpg'],
        ['name' => 'Alfe Duron', 'image' => 'images/BOD/Member7.png'],
        ['name' => 'Cresanto Lemosniro', 'image' => 'images/BOD/Member10.jpg'],
        ['name' => 'Regie Relampagos Guiñares', 'image' => 'images/BOD/Member17.jpg'],
        ['name' => 'Rene Cycil Mabanglo', 'image' => 'images/BOD/Member18.jpg'],
        ['name' => 'Cherry Lynn Nierras-Dagala, MD', 'image' => 'images/BOD/Member8.png'],
        ['name' => 'Venus D. Bartal', 'image' => 'images/BOD/Member9.jpg'],
        ['name' => 'Fatima Lemosniro', 'image' => 'images/BOD/Member11.jpg'],
        ['name' => 'MA. Carmelita Dalisay Robles', 'image' => 'images/BOD/Member12.jpg'],
        ['name' => 'Mary Jane Estaca Samillano', 'image' => 'images/BOD/Member13.jpg'],
        ['name' => 'Mechelle Engbino Sefuentes', 'image' => 'images/BOD/Member14.jpg'],
        ['name' => 'Nova Tajale', 'image' => 'images/BOD/Member15.jpg'],
        ['name' => 'Precious Gwen Dela Cruz Dela Torre', 'image' => 'images/BOD/Member16.jpg'],
        ['name' => 'Vivelyn Dela Cruz Pereda', 'image' => 'images/BOD/Member19.jpg'],
    ];
@endphp

<div class="board-panel" x-data="boardTree()" x-ref="boardPanel">

    <div class="board-header">
        <div>
            <h2 class="board-title">Board Members Structure</h2>
            <p class="board-subtitle">
                TRIFECTA Leadership &amp; Governance Hierarchy
            </p>
        </div>

        <div class="tree-tools">
            <button type="button" @click="zoomOut()" aria-label="Zoom out" title="Zoom out">−</button>

            <span class="zoom-percentage" x-text="Math.round(scale * 100) + '%'"></span>

            <button type="button" @click="zoomIn()" aria-label="Zoom in" title="Zoom in">+</button>

            <button type="button" @click="reset()" title="Reset view" aria-label="Reset view">↺</button>

            <button
                type="button"
                @click="toggleFullscreen()"
                :title="isFullscreen ? 'Exit fullscreen' : 'Enter fullscreen'"
                :aria-label="isFullscreen ? 'Exit fullscreen' : 'Enter fullscreen'"
                x-text="isFullscreen ? '⊗' : '⛶'"
            ></button>
        </div>
    </div>

    <div
        class="tree-viewport"
        x-ref="viewport"
        @wheel.prevent="wheel($event)"
        @pointerdown="startDrag($event)"
        @pointermove="drag($event)"
        @pointerup="stopDrag()"
        @pointercancel="stopDrag()"
        @lostpointercapture="stopDrag()"
    >
        <div
            class="tree-stage"
            :style="`transform: translate(${offsetX}px, ${offsetY}px) scale(${scale});`"
        >
            <div class="board-hierarchy">

                {{-- CEO --}}
                <div class="hierarchy-level">
                    <div class="leader-card ceo">
                        <div class="leader-circle">
                            <img
                                src="{{ asset('images/ceo.jpg') }}"
                                alt="Renante Suaybaguio Robles"
                            >
                        </div>

                        <div class="leader-info">
                            <strong>Renante Suaybaguio Robles, MPA, LFA</strong>
                            <span>Chief Executive Officer</span>
                        </div>
                    </div>
                </div>

                <div class="hierarchy-connector"></div>

                {{-- COO --}}
                <div class="hierarchy-level">
                    <div class="leader-card coo">
                        <div class="leader-circle">
                            <img
                                src="{{ asset('images/coo.jpg') }}"
                                alt="Bobby Paulo Dagala"
                            >
                        </div>

                        <div class="leader-info">
                            <strong>Bobby Paulo Dagala</strong>
                            <span>Chief Operating Officer</span>
                        </div>
                    </div>
                </div>

                <div class="hierarchy-connector board-main-connector"></div>

                {{-- Board members: five per row --}}
                <div class="board-members-rows">
                    @foreach (array_chunk($boardMembers, 5) as $rowIndex => $row)
                        <div class="board-member-row" wire:key="board-row-{{ $rowIndex }}">
                            @foreach ($row as $i => $member)
                                <div
                                    class="board-member-item"
                                    wire:key="board-member-{{ ($rowIndex * 5) + $i }}"
                                >
                                    <div class="member-branch"></div>

                                    <div class="member-circle">
                                        <img
                                            src="{{ asset($member['image']) }}"
                                            alt="{{ $member['name'] }}"
                                            loading="lazy"
                                        >
                                    </div>

                                    <div class="member-card">
                                        <strong>{{ $member['name'] }}</strong>
                                        <span>Board Member</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

            </div>
        </div>

        @vite('resources/js/livewire/member-pages/genealogy/board-members.js')
    </div>
</div>

