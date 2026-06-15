function handlePrintClick() { window.print(); }

function handleHistoryBack(event) {
    event.preventDefault();
    window.history.back();
}

(function() {
        const DOC_WIDTH = 900;

        function fitToScreen() {
            const wrapper = document.querySelector('.rapor-wrapper');
            if (!wrapper) return;
            const vw = Math.min(window.innerWidth, document.documentElement.clientWidth);
            if (vw < DOC_WIDTH) {
                const scale = vw / DOC_WIDTH;
                wrapper.style.transform = 'scale(' + scale + ')';
                wrapper.style.transformOrigin = 'top left';
                wrapper.style.marginLeft = '0';
                wrapper.style.marginRight = '0';
                document.body.style.height = Math.ceil(wrapper.scrollHeight * scale) + 'px';
            } else {
                wrapper.style.transform = '';
                wrapper.style.transformOrigin = '';
                document.body.style.height = '';
            }
        }

        window.addEventListener('load', fitToScreen);
        window.addEventListener('resize', fitToScreen);

        window.addEventListener('beforeprint', function() {
            const wrapper = document.querySelector('.rapor-wrapper');
            if (wrapper) { wrapper.style.transform = 'none'; wrapper.style.width = ''; }
        });
        window.addEventListener('afterprint', fitToScreen);

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-history-back]').forEach(function (link) {
                link.addEventListener('click', handleHistoryBack);
            });

            document.querySelectorAll('[data-print-page]').forEach(function (button) {
                button.addEventListener('click', handlePrintClick);
            });
        });
    })();
