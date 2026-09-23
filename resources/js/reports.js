import { Chart } from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const terlarisEl = document.getElementById('chart-terlaris-data');
    const sepiEl = document.getElementById('chart-sepi-data');

    if (!terlarisEl || !sepiEl) {
        return;
    }

    const terlaris = JSON.parse(terlarisEl.textContent);
    const sepi = JSON.parse(sepiEl.textContent);

    function buildChart(canvasId, data, barColor) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: data.map((d) => d.product_name),
                datasets: [
                    {
                        label: 'Unit Terjual',
                        data: data.map((d) => d.total_qty),
                        backgroundColor: barColor,
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    }

    buildChart('chartTerlaris', terlaris, '#3f6b52');
    buildChart('chartSepi', sepi, '#a3453f');
});
