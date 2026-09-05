import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const dataElement = document.getElementById('admin-dashboard-chart-data');

    if (!dataElement || typeof Chart === 'undefined') {
        return;
    }

    let dashboardData;

    try {
        dashboardData = JSON.parse(dataElement.textContent);
    } catch (error) {
        console.error('Failed to parse admin dashboard chart data.', error);
        return;
    }

    const chartData = dashboardData.chartPendaftaran || {};
    const genderData = dashboardData.genderData || {};
    const classLabels = dashboardData.kelasLabels || [];
    const classCounts = dashboardData.kelasCounts || [];

    Chart.defaults.font.family = "'Plus Jakarta Sans', 'Helvetica', 'Arial', sans-serif";
    Chart.defaults.color = '#64748b';
    Chart.defaults.scale.grid.color = '#e2e8f0';

    const ctxReg = document.getElementById('registrationChart');
    let registrationChart;

    if (ctxReg) {
        const initRegistrationChart = (timeframe) => {
            const dataObj = chartData[timeframe] || { labels: [], data: [] };

            if (registrationChart) {
                registrationChart.destroy();
            }

            registrationChart = new Chart(ctxReg, {
                type: 'line',
                data: {
                    labels: dataObj.labels,
                    datasets: [{
                        label: 'Siswa Baru',
                        data: dataObj.data,
                        borderColor: '#4361ee',
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4361ee',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#334155',
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false,
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            border: { dash: [4, 4] },
                            ticks: { precision: 0 },
                        },
                        x: {
                            grid: { display: false },
                        },
                    },
                },
            });
        };

        initRegistrationChart('6_bulan');

        const timeFilter = document.getElementById('timeFilter');
        timeFilter?.addEventListener('change', (event) => {
            initRegistrationChart(event.target.value);
        });
    }

    const ctxGender = document.getElementById('genderChart');

    if (ctxGender) {
        new Chart(ctxGender, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [genderData.L || 0, genderData.P || 0],
                    backgroundColor: ['#4361ee', '#ec4899'],
                    borderWidth: 0,
                    hoverOffset: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                        },
                    },
                },
            },
        });
    }

    const ctxClass = document.getElementById('classChart');

    if (ctxClass) {
        new Chart(ctxClass, {
            type: 'bar',
            data: {
                labels: classLabels,
                datasets: [{
                    label: 'Jumlah Siswa',
                    data: classCounts,
                    backgroundColor: '#3b82f6',
                    borderRadius: 4,
                    barThickness: 20,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, display: false },
                        grid: { display: false },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
            },
        });
    }
});
