function handlePrintClick() { window.print(); }

function handleHistoryBack(event) {
            event.preventDefault();
            if (document.referrer && window.history.length > 1) {
                window.history.back();
            } else {
                window.close();
            }
        }

(function() {
        var DOC_WIDTH = 900;
        var currentScale = 1;
        var fitScale = 1;
        var manualZoom = false;

        function applyScale(scale) {
            var wrapper = document.querySelector('.rapor-wrapper');
            if (!wrapper) return;
            currentScale = scale;
            wrapper.style.transform = 'scale(' + scale + ')';
            wrapper.style.transformOrigin = 'top left';
            wrapper.style.marginLeft = '0';
            wrapper.style.marginRight = '0';
            document.body.style.height = Math.ceil(wrapper.scrollHeight * scale) + 'px';
            var label = document.getElementById('zoomLevel');
            if (label) label.textContent = Math.round(scale * 100) + '%';
        }

        function fitToScreen() {
            var wrapper = document.querySelector('.rapor-wrapper');
            if (!wrapper) return;

            var vw = Math.min(window.innerWidth, document.documentElement.clientWidth);

            if (vw < DOC_WIDTH) {
                fitScale = vw / DOC_WIDTH;
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
