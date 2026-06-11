const NATIVE_W = 1058;
        let zoomLevel  = 1; // multiplier di atas baseScale

        function baseScale() {
            // Hitung skala agar container fit ke lebar layar
            const avail = window.innerWidth - 20; // 10px sisi kiri + kanan
            return avail < NATIVE_W ? avail / NATIVE_W : 1;
        }

        function applyScale() {
            const el  = document.getElementById('pageContainer');
            const lbl = document.getElementById('zoomLabel');
            if (!el) return;

            // CSS zoom mempengaruhi layout (berbeda dari transform scale)
            // → overflow/scrollbar wrapper muncul otomatis saat zoom in
            const scale = Math.round(baseScale() * zoomLevel * 1000) / 1000;
            el.style.zoom = scale;

            if (lbl) lbl.textContent = Math.round(scale * 100) + '%';
        }

        function zoomIn()    { zoomLevel = Math.min(+(zoomLevel + 0.15).toFixed(2), 4);   applyScale(); }
        function zoomOut()   { zoomLevel = Math.max(+(zoomLevel - 0.15).toFixed(2), 0.1); applyScale(); }
        function fitScreen() { zoomLevel = 1; applyScale(); }

        window.addEventListener('load',   applyScale);
        window.addEventListener('resize', () => { zoomLevel = 1; applyScale(); });

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-zoom-action]').forEach(function (button) {
                button.addEventListener('click', function () {
                    if (this.dataset.zoomAction === 'in') zoomIn();
                    if (this.dataset.zoomAction === 'out') zoomOut();
                    if (this.dataset.zoomAction === 'fit') fitScreen();
                });
            });

            document.querySelectorAll('[data-print-page]').forEach(function (button) {
                button.addEventListener('click', function () {
                    window.print();
                });
            });
        });
