/**
 * User Accounts settings tab (resources/views/livewire/admin-pages/report-settings/users-accounts.blade.php).
 *
 * Print helper for the per-user financial summary.
 */

function printUserFinancials() {
    document.body.setAttribute('data-print-target', 'user-financials');
    window.print();
}

window.printUserFinancials = printUserFinancials;

window.addEventListener('afterprint', () => {
    document.body.removeAttribute('data-print-target');
});
