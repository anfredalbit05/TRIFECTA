/**
 * Admin dashboard – overview
 * (resources/views/livewire/admin-pages/dashboard/overview.blade.php).
 *
 * Alpine `monthlyChart` component for the "Monthly Registrations" bar chart.
 * The labels/values are read from `data-chart-labels` / `data-chart-values`
 * on the chart card, which the Blade view fills from `$monthlyUsers`.
 */

// Canvas cannot resolve CSS var() directly, so read the global palette value.
const cssColor = (name, fallback) => {
    const value = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
    return value || fallback;
};

document.addEventListener('alpine:init', () => {
    Alpine.data('monthlyChart', () => ({
        init() {
            this.renderChart(
                JSON.parse(this.$el.dataset.chartLabels || '[]'),
                JSON.parse(this.$el.dataset.chartValues || '[]'),
            );
        },

        renderChart(labels, data) {
            const canvas = document.getElementById('monthlyChartCanvas');
            if (!canvas) return;

            // Copy the values out of any Alpine/Livewire reactive proxy before
            // handing them to Chart.js (deep proxying recurses inside Chart).
            const labelsCopy = Array.from(labels || []);
            const valuesCopy = Array.from(data || []);

            // Keep the instance on the element (not in Alpine data): storing it
            // in reactive state makes Chart.js traverse Alpine proxies, which
            // overflows the stack when the chart updates.
            const chart = this.$el._monthlyChart;

            // Update the existing chart in place. Destroying it right after the
            // first paint can leave Chart.js drawing to a cancelled context
            // (surfaces as a cross-origin "Script error." in the console).
            if (chart) {
                chart.data.labels = labelsCopy;
                chart.data.datasets[0].data = valuesCopy;
                chart.update();
                return;
            }

            this.$el._monthlyChart = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: labelsCopy,
                    datasets: [
                        {
                            label: 'New Members',
                            data: valuesCopy,
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
                                stepSize: 1,
                                precision: 0,
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
});
