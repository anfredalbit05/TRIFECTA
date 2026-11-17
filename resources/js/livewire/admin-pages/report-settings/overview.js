/**
 * Report & Settings – overview
 * (resources/views/livewire/admin-pages/report-settings/overview.blade.php).
 *
 * Alpine `monthlyChart` component for the "Monthly Income Analytics" bar chart
 * and the per-card print helper used by the print buttons.
 *
 * The labels/values are read from `data-chart-labels` / `data-chart-values`
 * on the chart card, which the Blade view fills from `$monthlyIncome`.
 */

// Register before Alpine starts when the module loads with the page (the
// report-settings page loads it eagerly because the overview tab is lazy).
// Canvas cannot resolve CSS var() directly, so read the global palette value.
const cssColor = (name, fallback) => {
    const value = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
    return value || fallback;
};

function registerMonthlyChart() {
    Alpine.data('monthlyChart', () => ({
        chartInstance: null,

        init() {
            this.renderChart(
                JSON.parse(this.$el.dataset.chartLabels || '[]'),
                JSON.parse(this.$el.dataset.chartValues || '[]'),
            );
        },

        renderChart(labels, data) {
            const ctx = document.getElementById('monthlyIncomeChart');
            if (!ctx) return;

            if (this.chartInstance) {
                this.chartInstance.destroy();
            }

            this.chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Total Income (₱)',
                            data: data,
                            backgroundColor: cssColor('--brand-primary', '#0B5FC0'),
                            borderRadius: 8,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return '₱' + value.toLocaleString();
                                },
                            },
                        },
                    },
                },
            });
        },

        updateChart(detail) {
            this.renderChart(detail.labels, detail.data);
        },
    }));
}

if (window.Alpine) {
    registerMonthlyChart();
} else {
    document.addEventListener('alpine:init', registerMonthlyChart);
}

// ---- PER-CARD PRINT ----
function printCard(section) {
    document.body.setAttribute('data-print-target', section);
    window.print();
}

window.printCard = printCard;

window.addEventListener('afterprint', () => {
    document.body.removeAttribute('data-print-target');
});
