/**
 * Member dashboard overview (resources/views/livewire/share-pages/dashboard/overview.blade.php).
 *
 * Friendly dialog when a withdrawal is blocked because the cash wallet is
 * below the minimum withdrawal amount.
 */

window.addEventListener('insufficient-withdrawal-balance', (event) => {
    const data = event.detail;

    Swal.fire({
        icon: 'warning',
        title: 'Insufficient Balance',
        html: `
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-4">
                    Your Cash Wallet balance is not enough to make a withdrawal.
                </p>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-left">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-500">
                            Cash Wallet
                        </span>

                        <strong class="text-[var(--color-primary)]">
                            ₱${data.balance}
                        </strong>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">
                            Minimum Withdrawal
                        </span>

                        <strong class="text-red-600">
                            ₱${data.minimum}
                        </strong>
                    </div>
                </div>

                <p class="text-xs text-gray-500 mt-4">
                    You need at least ₱${data.minimum} in your Cash Wallet to withdraw.
                </p>
            </div>
        `,
        confirmButtonText: 'Okay',
        confirmButtonColor: 'var(--color-primary)',
    });
});
