const chartJsUrl = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';

function loadChartJs() {
    if (window.Chart) {
        return Promise.resolve();
    }

    return new Promise((resolve, reject) => {
        const existing = document.querySelector(`script[src="${chartJsUrl}"]`);
        if (existing) {
            existing.addEventListener('load', resolve, { once: true });
            existing.addEventListener('error', reject, { once: true });
            return;
        }

        const script = document.createElement('script');
        script.src = chartJsUrl;
        script.async = true;
        script.addEventListener('load', resolve, { once: true });
        script.addEventListener('error', reject, { once: true });
        document.head.appendChild(script);
    });
}

function initFlyer(root) {
    const modal = document.getElementById('flyerModal');
    const userId = root.dataset.flyerUserId || 'guest';
    if (!modal || localStorage.getItem(`flyerShown_${userId}`)) return;

    setTimeout(() => {
        modal.classList.add('show');
    }, 1200);

    modal.querySelector('[data-flyer-close]')?.addEventListener('click', () => {
        modal.classList.remove('show');
        localStorage.setItem(`flyerShown_${userId}`, 'true');
    });
}

async function initPerformanceChart(root) {
    if (root.dataset.chartEnabled !== 'true') return;

    const canvas = document.getElementById('performaChart');
    if (!canvas) return;

    const completed = Number(root.dataset.chartCompleted || 0);
    const total = Number(root.dataset.chartTotal || 0);
    const process = Math.max(total - completed, 0);
    const delayed = 0;

    let chartData = [completed, process, delayed];
    let bgColors = ['#10b981', '#f6c23e', '#e74a3b'];
    let chartLabels = ['Lulus', 'Proses', 'Tunda'];
    let tooltipCallback = null;

    if (completed === 0 && process === 0 && delayed === 0) {
        chartData = [1];
        bgColors = ['#e2e8f0'];
        chartLabels = ['Belum ada data'];
        tooltipCallback = () => 'Belum ada data';
    }

    try {
        await loadChartJs();
    } catch (error) {
        console.warn('Chart.js gagal dimuat:', error);
        return;
    }

    new window.Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: chartLabels,
            datasets: [{
                data: chartData,
                backgroundColor: bgColors,
                borderWidth: 0,
                cutout: '75%',
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: tooltipCallback || ((context) => `${context.label}: ${context.raw}`),
                    },
                },
            },
        },
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-sia-dashboard]');
    if (!root) return;

    initFlyer(root);
    initPerformanceChart(root);
});
