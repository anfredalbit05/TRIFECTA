/**
 * Genealogy Visual Tree viewport (resources/views/livewire/member-pages/genealogy/invitee.blade.php).
 *
 * The state lives in an Alpine store instead of an `x-data` object on the
 * Livewire component, because Livewire re-renders this component on every
 * search / view change and the markup's local Alpine scope can be recreated
 * without its methods. Store state and magics keep resolving either way.
 */

function treeViewport() {
    return {
        scale: 1,
        minScale: 0.02,
        maxScale: 2,
        zoomStep: 0.1,

        offsetX: 0,
        offsetY: 0,

        isDragging: false,
        isFullscreen: false,

        startX: 0,
        startY: 0,
        lastX: 0,
        lastY: 0,

        fsHandler: null,

        rootEl() {
            return document.querySelector('.genealogy-explorer');
        },

        panelEl() {
            return this.rootEl()?.querySelector('[data-tree-panel]') ?? null;
        },

        viewportEl() {
            return this.rootEl()?.querySelector('[data-tree-viewport]') ?? null;
        },

        treeEl() {
            return this.rootEl()?.querySelector('[data-tree-tree]') ?? null;
        },

        stageEl() {
            return this.rootEl()?.querySelector('[data-tree-stage]') ?? null;
        },

        /** Keeps the stage transform in sync — runs from `x-effect` on the stage. */
        applyTransform() {
            const stage = this.stageEl();

            if (!stage) return;

            stage.style.transform = `translate(${this.offsetX}px, ${this.offsetY}px) scale(${this.scale})`;
        },

        init() {
            if (this.fsHandler) return;

            this.fsHandler = () => {
                this.isFullscreen = document.fullscreenElement === this.panelEl();
                setTimeout(() => this.fitTree(), 150);
            };

            document.addEventListener('fullscreenchange', this.fsHandler);
        },

        destroy() {
            if (!this.fsHandler) return;

            document.removeEventListener('fullscreenchange', this.fsHandler);
            this.fsHandler = null;
        },

        async toggleFullscreen() {
            const panel = this.panelEl();

            if (!panel) return;

            try {
                if (!document.fullscreenElement) {
                    await panel.requestFullscreen();
                } else {
                    await document.exitFullscreen();
                }
            } catch (error) {
                console.error('Fullscreen could not be changed:', error);
            }
        },

        fitTree() {
            setTimeout(() => {
                const viewport = this.viewportEl();
                const tree = this.treeEl();

                if (!viewport || !tree || !tree.offsetWidth || !tree.offsetHeight) {
                    return;
                }

                const pad = 60;
                const scaleX = (viewport.clientWidth - pad) / tree.offsetWidth;
                const scaleY = (viewport.clientHeight - pad) / tree.offsetHeight;

                this.scale = Math.min(1, Math.max(this.minScale, Math.min(scaleX, scaleY)));
                this.offsetX = (viewport.clientWidth - tree.offsetWidth * this.scale) / 2;
                this.offsetY = Math.max(10, (viewport.clientHeight - tree.offsetHeight * this.scale) / 2);
            }, 0);
        },

        zoomIn() {
            this.setZoom(this.scale + this.zoomStep);
        },

        zoomOut() {
            this.setZoom(this.scale - this.zoomStep);
        },

        setZoom(value, clientX = null, clientY = null) {
            const viewport = this.viewportEl();

            if (!viewport) return;

            const oldScale = this.scale;
            const newScale = Math.min(this.maxScale, Math.max(this.minScale, value));

            if (newScale === oldScale) return;

            const rect = viewport.getBoundingClientRect();

            const pointerX = clientX === null ? rect.width / 2 : clientX - rect.left;
            const pointerY = clientY === null ? rect.height / 2 : clientY - rect.top;

            const treeX = (pointerX - this.offsetX) / oldScale;
            const treeY = (pointerY - this.offsetY) / oldScale;

            this.scale = newScale;

            this.offsetX = pointerX - treeX * newScale;
            this.offsetY = pointerY - treeY * newScale;
        },

        wheel(event) {
            const direction = event.deltaY < 0 ? 1 : -1;

            this.setZoom(this.scale + direction * this.zoomStep, event.clientX, event.clientY);
        },

        startDrag(event) {
            if (event.button !== 0) return;

            this.isDragging = true;
            this.startX = event.clientX;
            this.startY = event.clientY;
            this.lastX = this.offsetX;
            this.lastY = this.offsetY;
        },

        drag(event) {
            if (!this.isDragging) return;

            this.offsetX = this.lastX + event.clientX - this.startX;
            this.offsetY = this.lastY + event.clientY - this.startY;
        },

        stopDrag() {
            this.isDragging = false;
        },
    };
}

function registerTreeViewport() {
    if (!window.Alpine) return;

    const store = treeViewport();

    window.Alpine.store('treeViewport', store);

    store.init();
}

document.addEventListener('alpine:init', registerTreeViewport);

// The script may load after Alpine has already booted (Vite modules are deferred).
if (window.Alpine) {
    registerTreeViewport();
}
