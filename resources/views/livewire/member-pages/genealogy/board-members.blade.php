<div class="board-panel" x-data="boardTree()">

    <div class="board-header">
        <div>
            <h2 class="board-title">Board Members Structure</h2>
            <p class="board-subtitle">True Life Leadership &amp; Governance Hierarchy</p>
        </div>

        <div class="tree-tools">
            <button type="button" @click="zoomOut()">−</button>
            <span x-text="Math.round(scale * 100) + '%'"></span>
            <button type="button" @click="zoomIn()">+</button>
            <button type="button" @click="reset()">↺</button>
        </div>
    </div>

    <div class="tree-viewport"
         @wheel.prevent="wheel($event)"
         @mousedown="startPan($event)"
         @mousemove="pan($event)"
         @mouseup="stopPan()"
         @mouseleave="stopPan()">

        <div class="tree-stage"
             :style="`transform:translate(${x}px,${y}px) scale(${scale})`">

            <svg class="tree-lines" viewBox="0 0 1800 850">
                <path d="M900 145 V285"/>
                <path d="M900 405 V475"/>
                <path d="M150 475 H1650"/>
                <path d="M150 475 V530"/>
                <path d="M365 475 V530"/>
                <path d="M580 475 V530"/>
                <path d="M795 475 V530"/>
                <path d="M1010 475 V530"/>
                <path d="M1225 475 V530"/>
                <path d="M1440 475 V530"/>
                <path d="M1650 475 V530"/>
            </svg>

            <div class="leader ceo">
                <div class="leader-circle">
                    <img src="{{ asset('images/user.png') }}" alt="Renante Suaybaguio Robles">
                </div>

                <div class="leader-info">
                    <strong>Renante Suaybaguio Robles, MPA, LFA</strong>
                    <span>Chief Executive Officer</span>
                </div>
            </div>

            <div class="leader coo">
                <div class="leader-circle coo-circle">
                    <img src="{{ asset('images/user.png') }}" alt="Bobby Paulo Dagala">
                </div>

                <div class="leader-info">
                    <strong>Bobby Paulo Dagala</strong>
                    <span>Chief Operating Officer</span>
                </div>
            </div>

            @php
                $boardMembers = [
                    ['name' => 'Jeorge Leemart Alfeche', 'image' => 'images/BOD/Member1.png'],
                    ['name' => 'Benito Igadna', 'image' => 'images/BOD/Member2.jpg'],
                    ['name' => 'Pierre Hordista', 'image' => 'images/BOD/Member3.png'],
                    ['name' => 'Daisy Maghuyop Colegado', 'image' => 'images/BOD/Member4.png'],
                    ['name' => 'Mrs. Cheryl Eliot, RN', 'image' => 'images/BOD/Member5.png'],
                    ['name' => 'Bernard Laurente', 'image' => 'images/BOD/Member6.png'],
                    ['name' => 'Alfe Duron', 'image' => 'images/BOD/Member7.png'],
                    ['name' => 'Cherry Lynn Nierras-Dagala, MD', 'image' => 'images/BOD/Member8.png'],
                ];
            @endphp

            @foreach($boardMembers as $i => $member)
                <div class="member member-{{ $i + 1 }}">
                    <div class="member-circle">
                        <img src="{{ asset($member['image']) }}" alt="{{ $member['name'] }}">
                    </div>

                    <div class="member-card">
                        <strong>{{ $member['name'] }}</strong>
                        <span>Board Member</span>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>

<script>
function boardTree() {
    return {
        scale: .65,
        x: 0,
        y: 15,
        dragging: false,
        startX: 0,
        startY: 0,
        startPanX: 0,
        startPanY: 0,

        zoomIn() {
            if (window.innerWidth <= 768) return;
            this.scale = Math.min(1.4, this.scale + .08);
        },

        zoomOut() {
            if (window.innerWidth <= 768) return;
            this.scale = Math.max(.35, this.scale - .08);
        },

        reset() {
            if (window.innerWidth <= 768) return;
            this.scale = .65;
            this.x = 0;
            this.y = 15;
        },

        wheel(e) {
            if (window.innerWidth <= 768) return;

            this.scale = Math.max(
                .35,
                Math.min(1.4, this.scale + (e.deltaY > 0 ? -.05 : .05))
            );
        },

        startPan(e) {
            if (window.innerWidth <= 768 || e.button !== 0) return;

            this.dragging = true;
            this.startX = e.clientX;
            this.startY = e.clientY;
            this.startPanX = this.x;
            this.startPanY = this.y;
        },

        pan(e) {
            if (window.innerWidth <= 768 || !this.dragging) return;

            this.x = this.startPanX + e.clientX - this.startX;
            this.y = this.startPanY + e.clientY - this.startY;
        },

        stopPan() {
            this.dragging = false;
        }
    }
}
</script>
