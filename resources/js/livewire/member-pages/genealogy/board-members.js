/**
 * Genealogy board members (resources/views/livewire/member-pages/genealogy/board-members.blade.php).
 *
 * Alpine component for the pan/zoom board hierarchy chart.
 */

function boardTree() {
    return {
        scale: 0.6,
        minScale: 0.2,
        maxScale: 2,
        zoomStep: 0.1,

        offsetX: 0,
        offsetY: 10,

        dragging: false,
        startX: 0,
        startY: 0,
        lastX: 0,
        lastY: 0,

        isFullscreen: false,
        resizeHandler: null,
        fullscreenHandler: null,

        init() {
            this.$nextTick(() => this.centerChart());

            this.resizeHandler = () => this.centerChart();

            this.fullscreenHandler = () => {
                this.isFullscreen = document.fullscreenElement === this.$refs.boardPanel;

                this.$nextTick(() => this.centerChart());
            };

            window.addEventListener('resize', this.resizeHandler);
            document.addEventListener('fullscreenchange', this.fullscreenHandler);
        },

        destroy() {
            window.removeEventListener('resize', this.resizeHandler);

            document.removeEventListener('fullscreenchange', this.fullscreenHandler);
        },

        centerChart() {
            const viewport = this.$refs.viewport;
            const chart = viewport?.querySelector('.board-hierarchy');

            if (!viewport || !chart) return;

            this.offsetX = Math.max(
                0,
                (viewport.clientWidth - chart.scrollWidth * this.scale) / 2,
            );

            this.offsetY = 10;
        },

        zoomIn() {
            this.setZoom(this.scale + this.zoomStep);
        },

        zoomOut() {
            this.setZoom(this.scale - this.zoomStep);
        },

        setZoom(value, clientX = null, clientY = null) {
            const viewport = this.$refs.viewport;
            if (!viewport) return;

            const oldScale = this.scale;
            const newScale = Math.min(this.maxScale, Math.max(this.minScale, value));

            if (newScale === oldScale) return;

            const rect = viewport.getBoundingClientRect();

            const pointerX = clientX === null ? rect.width / 2 : clientX - rect.left;

            const pointerY = clientY === null ? rect.height / 2 : clientY - rect.top;

            const chartX = (pointerX - this.offsetX) / oldScale;
            const chartY = (pointerY - this.offsetY) / oldScale;

            this.scale = newScale;

            this.offsetX = pointerX - chartX * newScale;
            this.offsetY = pointerY - chartY * newScale;
        },

        wheel(event) {
            const direction = event.deltaY < 0 ? 1 : -1;

            this.setZoom(
                this.scale + direction * this.zoomStep,
                event.clientX,
                event.clientY,
            );
        },

        startDrag(event) {
            if (event.button !== 0) return;

            if (event.target.closest('button, a, input, select, textarea')) {
                return;
            }

            this.dragging = true;
            this.startX = event.clientX;
            this.startY = event.clientY;
            this.lastX = this.offsetX;
            this.lastY = this.offsetY;

            this.$refs.viewport.classList.add('is-dragging');
            this.$refs.viewport.setPointerCapture(event.pointerId);
        },

        drag(event) {
            if (!this.dragging) return;

            this.offsetX = this.lastX + event.clientX - this.startX;
            this.offsetY = this.lastY + event.clientY - this.startY;
        },

        stopDrag() {
            this.dragging = false;
            this.$refs.viewport?.classList.remove('is-dragging');
        },

        reset() {
            this.scale = 0.6;
            this.$nextTick(() => this.centerChart());
        },

        async toggleFullscreen() {
            const panel = this.$refs.boardPanel;

            try {
                if (!document.fullscreenElement) {
                    await panel.requestFullscreen();
                } else if (document.fullscreenElement === panel) {
                    await document.exitFullscreen();
                } else {
                    await document.exitFullscreen();
                    await panel.requestFullscreen();
                }
            } catch (error) {
                console.error('Fullscreen could not be changed:', error);
            }
        },
    };
}

window.boardTree = boardTree;
