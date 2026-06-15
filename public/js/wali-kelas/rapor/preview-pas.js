function handlePrintClick() {
            window.print();
        }

function handleHistoryBack(event) {
            event.preventDefault();
            if (document.referrer && window.history.length > 1) {
                window.history.back();
            } else {
                window.close();
            }
        }

(function() {
        var currentScale = 1;
        var fitScale = 1;
        var manualZoom = false;

        function getViewportWidth() {
            return (window.visualViewport ? window.visualViewport.width : null)
                || document.documentElement.clientWidth
                || window.innerWidth;
        }

        function applyScale(scale) {
            var wrapper = document.querySelector('.rapor-wrapper');
            if (!wrapper) return;
            currentScale = scale;
            wrapper.style.transform = 'scale(' + scale + ')';
            wrapper.style.transformOrigin = 'top left';
            document.body.style.height = Math.ceil(wrapper.scrollHeight * scale) + 'px';
            var label = document.getElementById('zoomLevel');
            if (label) label.textContent = Math.round(scale * 100) + '%';
        }

        function fitToScreen() {
            var wrapper = document.querySelector('.rapor-wrapper');
            if (!wrapper) return;

            wrapper.style.transform = 'none';
            wrapper.style.transformOrigin = '';

            var vw = getViewportWidth();
            var docWidth = wrapper.scrollWidth;

            if (vw < docWidth) {
                fitScale = (vw - 2) / docWidth;
            } else {
                fitScale = 1;
            }

            if (!manualZoom) {
                applyScale(fitScale);
            }
        }

        window.zoomIn = function() {
            manualZoom = true;
            applyScale(Math.min(currentScale + 0.1, 2));
        };
        window.zoomOut = function() {
            manualZoom = true;
            applyScale(Math.max(currentScale - 0.1, 0.3));
        };
        window.zoomReset = function() {
            manualZoom = false;
            fitToScreen();
        };

        window.addEventListener('load', fitToScreen);
        window.addEventListener('resize', function() {
            if (!manualZoom) fitToScreen();
        });
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', function() {
                if (!manualZoom) fitToScreen();
            });
        }

        window.addEventListener('beforeprint', function() {
            var wrapper = document.querySelector('.rapor-wrapper');
            if (wrapper) { wrapper.style.transform = 'none'; wrapper.style.width = ''; }
        });
        window.addEventListener('afterprint', function() {
            if (manualZoom) { applyScale(currentScale); } else { fitToScreen(); }
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-history-back]').forEach(function (link) {
                link.addEventListener('click', handleHistoryBack);
            });

            document.querySelectorAll('[data-print-page]').forEach(function (button) {
                button.addEventListener('click', handlePrintClick);
            });

            document.querySelectorAll('[data-zoom-action]').forEach(function (button) {
                button.addEventListener('click', function () {
                    if (this.dataset.zoomAction === 'in') window.zoomIn();
                    if (this.dataset.zoomAction === 'out') window.zoomOut();
                    if (this.dataset.zoomAction === 'reset') window.zoomReset();
                });
            });
        });
    })();
