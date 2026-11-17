/**
 * Transaction List page (resources/views/pages/admin-pages/transaction-list.blade.php).
 *
 * Approve / reject dialogs shared by the Withdrawal and Activations tabs.
 * The tab components dispatch browser events, so this handler is registered
 * once on `livewire:init` and resolves the right component by name when the
 * dialog is confirmed.
 */

document.addEventListener('livewire:init', () => {
    const callTxn = (name, method, ...args) => {
        const el = [...document.querySelectorAll('[wire\\:id]')].find(
            (el) => JSON.parse(el.getAttribute('wire:snapshot') || '{}').memo?.name === name,
        );

        // Livewire.find() returns the component's $wire object directly.
        const wire = el ? window.Livewire.find(el.getAttribute('wire:id')) : null;

        if (!wire) {
            Swal.fire({
                icon: 'error',
                title: 'Tab Not Loaded',
                text:
                    'Open the ' +
                    (name.includes('withdrawal') ? 'Withdrawal' : 'Activations') +
                    ' tab first, then try again.',
                confirmButtonColor: 'var(--color-primary)',
            });
            return;
        }

        wire.call(method, ...args);
    };

    // ---------- ACTIVATIONS: APPROVE (typed APPROVE) ----------
    window.Livewire.on('txn-approve-activation-prompt', (raw) => {
        const d = Array.isArray(raw) ? raw[0] : raw;

        SwalFlow.confirm(
            {
                title: 'Approve Activation?',
                html:
                    'You are about to approve the activation of ' +
                    '<strong class="text-[var(--color-primary)]">' +
                    (d.memberName || 'this member') +
                    '</strong>' +
                    ' for <strong class="text-[var(--color-primary)]">₱' +
                    (d.amount || '0.00') +
                    '</strong>.' +
                    ' This activates the member account, writes wallets/ledgers and sends emails.' +
                    '<br><br>Type <strong>APPROVE</strong> to confirm.',
                icon: 'question',
                requireConfirmWord: 'APPROVE',
                inputPlaceholder: 'APPROVE',
                confirmText: 'Yes, approve!',
                cancelText: 'Cancel',
                loadingTitle: 'Approving activation...',
                loadingText: 'Updating records and sending emails.',
            },
            () => callTxn('admin-pages.transaction-list.activations', 'approveActivation', d.activationId),
        );
    });

    // ---------- WITHDRAWALS: APPROVE (typed APPROVE) ----------
    // The review modal STAYS OPEN behind the typed dialog — it is only
    // dismissed after the typed APPROVE is confirmed and processing
    // succeeds (the tab component broadcasts withdrawal-processed, and
    // the modal closes itself then; the success alert follows).
    window.Livewire.on('txn-approve-withdrawal-prompt', (raw) => {
        const d = Array.isArray(raw) ? raw[0] : raw;

        SwalFlow.confirm(
            {
                title: 'Approve & Release Withdrawal?',
                html:
                    'You are about to release ' +
                    '<strong class="text-[var(--color-primary)]">₱' +
                    (d.amount || '0.00') +
                    '</strong>' +
                    ' to <strong class="text-[var(--color-primary)]">' +
                    (d.memberName || 'this member') +
                    '</strong>.' +
                    ' Wallet and ledger records will be written immediately.' +
                    '<br><br>Type <strong>APPROVE</strong> to confirm.',
                icon: 'question',
                requireConfirmWord: 'APPROVE',
                inputPlaceholder: 'APPROVE',
                confirmText: 'Yes, approve!',
                cancelText: 'Cancel',
                loadingTitle: 'Approving withdrawal...',
                loadingText: 'Releasing funds and updating records.',
            },
            () => callTxn('admin-pages.transaction-list.withdrawals', 'approveWithdrawal', d.withdrawalId),
        );
    });

    // Activation review-modal confirmations (approve/reject/resubmit)
    // are handled globally by the shared sweet-alert-handler — the modals
    // dispatch txn-modal-* events so they work on every page.

    // ---------- WITHDRAWALS: REJECT (typed REJECT) ----------
    // Same rule as approve: the review modal stays open behind the
    // typed dialog and closes itself after the rejection is processed.
    window.Livewire.on('txn-reject-withdrawal-prompt', (raw) => {
        const d = Array.isArray(raw) ? raw[0] : raw;

        SwalFlow.confirm(
            {
                title: 'Reject Withdrawal?',
                html:
                    'This withdrawal request will be rejected. This action cannot be undone.' +
                    '<br><br>Type <strong>REJECT</strong> to confirm.',
                icon: 'warning',
                requireConfirmWord: 'REJECT',
                inputPlaceholder: 'REJECT',
                confirmText: 'Yes, reject it!',
                cancelText: 'Cancel',
                loadingTitle: 'Rejecting withdrawal...',
                loadingText: 'Updating records.',
            },
            () => callTxn('admin-pages.transaction-list.withdrawals', 'rejectWithdrawal', d.withdrawalId),
        );
    });
});
