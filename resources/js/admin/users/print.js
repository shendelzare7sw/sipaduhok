(function () {
    let currentScale = 1;
    let fitScale = 1;
    let manualZoom = false;

    function getWrapper() {
        return document.querySelector('.print-wrapper');
    }

    function updateZoomLabel(scale) {
        const label = document.getElementById('zoomLevel');
        if (label) {
            label.textContent = `${Math.round(scale * 100)}%`;
        }
    }

    function applyScale(scale) {
        const wrapper = getWrapper();
        if (!wrapper) return;

        currentScale = scale;
        wrapper.style.transform = `scale(${scale})`;
        wrapper.style.transformOrigin = 'top left';
        document.body.style.height = `${Math.ceil((wrapper.scrollHeight * scale) + 60)}px`;
        updateZoomLabel(scale);
    }

    function fit() {
        const wrapper = getWrapper();
        if (!wrapper) return;

        wrapper.style.transform = 'none';
        const viewportWidth = document.documentElement.clientWidth || window.innerWidth;
        const documentWidth = wrapper.scrollWidth;
        fitScale = viewportWidth < documentWidth ? (viewportWidth - 4) / documentWidth : 1;

        if (!manualZoom) {
            applyScale(fitScale);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelector('[data-zoom-in]')?.addEventListener('click', () => {
            manualZoom = true;
            applyScale(Math.min(currentScale + 0.1, 2));
        });

        document.querySelector('[data-zoom-out]')?.addEventListener('click', () => {
            manualZoom = true;
            applyScale(Math.max(currentScale - 0.1, 0.3));
        });

        document.querySelector('[data-zoom-reset]')?.addEventListener('click', () => {
            manualZoom = false;
            fit();
        });

        document.querySelector('[data-print-page]')?.addEventListener('click', () => window.print());

        fit();
    });

    window.addEventListener('load', fit);
    window.addEventListener('resize', () => {
        if (!manualZoom) fit();
    });
    window.addEventListener('beforeprint', () => {
        const wrapper = getWrapper();
        if (wrapper) {
            wrapper.style.transform = 'none';
        }
    });
    window.addEventListener('afterprint', () => {
        if (manualZoom) {
            applyScale(currentScale);
        } else {
            fit();
        }
    });
})();
