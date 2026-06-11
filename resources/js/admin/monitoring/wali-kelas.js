/* Monitoring page behavior extracted from the former shared loader. */
document.addEventListener('DOMContentLoaded', function () {
    const query = window.matchMedia('(min-width: 768px)');
    const syncMonitoringDetails = function () {
        document.querySelectorAll('.monitoring-page .desktop-detail-cell .mobile-details').forEach(function (detail) {
            if (query.matches) {
                detail.setAttribute('open', 'open');
            } else {
                detail.removeAttribute('open');
            }
        });
    };

    syncMonitoringDetails();

    if (typeof query.addEventListener === 'function') {
        query.addEventListener('change', syncMonitoringDetails);
    } else if (typeof query.addListener === 'function') {
        query.addListener(syncMonitoringDetails);
    }

    document.querySelectorAll('[data-monitoring-auto-submit]').forEach(function (field) {
        field.addEventListener('change', function () {
            field.form?.submit();
        });
    });

    document.querySelectorAll('[data-monitoring-progress]').forEach(function (bar) {
        const rawValue = Number.parseFloat(bar.dataset.monitoringProgress || '0');
        const progress = Number.isFinite(rawValue) ? Math.max(0, Math.min(rawValue, 100)) : 0;

        bar.style.width = progress + '%';
        bar.setAttribute('role', 'progressbar');
        bar.setAttribute('aria-valuemin', '0');
        bar.setAttribute('aria-valuemax', '100');
        bar.setAttribute('aria-valuenow', String(progress));
    });
});
