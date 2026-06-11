// ── Auto fit-to-one-page A4 saat print (khusus jadwal: data sedikit, sebaiknya 1 hal) ──
        (function() {
            var MARGIN_MM = 10;
            // A4 landscape usable: (297 - 2*10) × (210 - 2*10) = 277mm × 190mm
            // 1mm ≈ 3.7795px at 96dpi
            var usableHeightPx = (210 - 2 * MARGIN_MM) * 3.7795;  // ≈ 718px
            var PRINT_SHRINK_FACTOR = 0.72; // print mode lebih kompak dari screen
            var MIN_APPLY = 0.65;
            var MAX_APPLY = 0.99;

            function injectScale() {
                var el = document.getElementById('printRoot');
                if (!el) return;
                var screenHeight = el.scrollHeight;
                if (!screenHeight) return;

                var estimatedPrintHeight = screenHeight * PRINT_SHRINK_FACTOR;
                var scale = usableHeightPx / estimatedPrintHeight;

                var existing = document.getElementById('jadwalPrintScale');
                if (existing) existing.parentNode.removeChild(existing);

                if (scale >= MAX_APPLY) return;
                if (scale < MIN_APPLY) return;

                var css = '@media print { #printRoot { ' +
                    'transform: scale(' + scale.toFixed(4) + ') !important; ' +
                    'transform-origin: top left !important; ' +
                    'width: ' + (100 / scale).toFixed(2) + '% !important; ' +
                    '} }';
                var style = document.createElement('style');
                style.id = 'jadwalPrintScale';
                style.textContent = css;
                document.head.appendChild(style);
            }

            if (document.readyState === 'complete') {
                injectScale();
            } else {
                window.addEventListener('load', injectScale);
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('[data-history-back]').forEach(function (link) {
                    link.addEventListener('click', function (event) {
                        event.preventDefault();
                        window.history.back();
                    });
                });

                document.querySelectorAll('[data-print-page]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        window.print();
                    });
                });
            });
        })();
