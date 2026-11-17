/**
 * Spin fund report component (resources/views/livewire/components/spin-fund-report.blade.php).
 *
 * Print helpers. `data-print-target` scopes the print stylesheet to the
 * section that was requested; it is cleared again after printing.
 */

function printDrawWinners() {
    document.body.setAttribute('data-print-target', 'draw-winners');
    window.print();
}

function printFundReport() {
    document.body.setAttribute('data-print-target', 'spin-fund-report');
    window.print();
}

window.printDrawWinners = printDrawWinners;
window.printFundReport = printFundReport;

window.addEventListener('afterprint', () => {
    document.body.removeAttribute('data-print-target');
});
