@extends('layouts.member')

@section('title', 'Transaction List')

@section('content')

{{--
    TEMPORARY: Transaction List styles embedded in the blade for now (plain
    CSS — no Tailwind @apply, which only works in Vite-built css files).
    Scoped under .txn-* so nothing here can clash with the global bundles.
    Move into resources/css when the page design settles.
--}}
<style>
    /* ---------- Search + filter ---------- */
    .txn-search-input {
        width: 100%;
        max-width: 420px;
        flex: 1 1 260px;
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        padding: 0.625rem 1rem;
        font-size: 0.875rem;
        color: #334155;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .06);
        outline: none;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .txn-search-input::placeholder { color: #94a3b8; }

    .txn-search-input:focus {
        border-color: #0C2C6B;
        box-shadow: 0 0 0 3px rgba(12, 44, 107, .18);
    }

    .txn-filter-select {
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        padding: 0.625rem 0.75rem;
        font-size: 0.875rem;
        color: #334155;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .06);
        outline: none;
        cursor: pointer;
    }

    .txn-filter-select:focus {
        border-color: #0C2C6B;
        box-shadow: 0 0 0 3px rgba(12, 44, 107, .18);
    }

    /* ---------- Table ---------- */
    .txn-table-wrap {
        overflow-x: auto;
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .06);
    }

    .txn-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .txn-table thead tr {
        background: #0C2C6B;
        color: #fff;
        text-align: left;
    }

    .txn-table th {
        padding: 0.75rem 1rem;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        white-space: nowrap;
    }

    .txn-table td {
        padding: 0.75rem 1rem;
        border-top: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }

    .txn-table tbody tr:hover { background: #f8fafc; }

    .txn-amount { font-weight: 600; white-space: nowrap; }

    .txn-empty {
        padding: 1.75rem 1rem;
        text-align: center;
        color: #64748b;
        font-size: 0.875rem;
    }

    /* ---------- Status pills ---------- */
    .txn-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 9999px;
        padding: 0.25rem 0.75rem;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #fff;
        white-space: nowrap;
    }

    .txn-pill-approved { background: #0C2C6B; }
    .txn-pill-pending { background: #D79B10; }
    .txn-pill-rejected { background: #b91c1c; }

    /* ---------- Responsive tab nav: scroll instead of wrapping/overflowing -- */
    .report-settings-desktop-nav {
        max-width: 100%;
        overflow-x: auto;
        scrollbar-width: thin;
    }

    /* ---------- Pending-count badge on the Withdrawal/Activations tabs -- */
    .txn-tab-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 9999px;
        background: #D79B10;
        color: #fff;
        font-size: 0.62rem;
        font-weight: 700;
        line-height: 1;
        white-space: nowrap;
    }

    /* ---------- Row action buttons (approve / reject) ---------- */
    .txn-actions {
        white-space: nowrap;
    }

    .txn-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        margin-right: 0.35rem;
        border: 1px solid transparent;
        border-radius: 0.5rem;
        font-size: 0.85rem;
        font-weight: 700;
        line-height: 1;
        cursor: pointer;
        transition: background .15s ease, color .15s ease, border-color .15s ease;
    }

    .txn-action-approve {
        background: #0C2C6B;
        color: #fff;
    }

    .txn-action-approve:hover {
        background: #0B2A5B;
    }

    .txn-action-reject {
        background: #fff;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .txn-action-reject:hover {
        background: #b91c1c;
        color: #fff;
        border-color: #b91c1c;
    }

    .txn-actions-none {
        color: #cbd5e1;
    }

    .txn-action-review {
        background: #fff;
        color: #0C2C6B;
        border-color: #cbd5e1;
    }

    .txn-action-resubmit {
        background: #fff;
        color: #0C2C6B;
        border-color: #cbd5e1;
    }

    .txn-action-resubmit:hover {
        background: #0C2C6B;
        color: #fff;
    }

    .txn-action-review:hover {
        background: #0C2C6B;
        color: #fff;
        border-color: #0C2C6B;
    }

    /* ---------- TrueLife / Satellite member-type pills ---------- */
    .txn-type-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 9999px;
        padding: 0.25rem 0.6rem;
        font-size: 0.62rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .txn-type-truelife {
        background: rgba(12, 44, 107, .08);
        color: #0C2C6B;
    }

    .txn-type-satellite {
        background: rgba(215, 155, 16, .12);
        color: #B8830D;
    }

    @media (max-width: 1023px) {
        .txn-action-btn {
            width: 34px;
            height: 34px;
        }
    }

    /* ---------- Small screens: tighten paddings, stack toolbar ---------- */
    @media (max-width: 640px) {
        .report-settings-desktop-nav {
            gap: 0.5rem;
            padding: 0 0.75rem;
        }

        .members-header .masterlist-btn {
            width: 100%;
        }

        .txn-search-input {
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
        }
    }

    /* ---------- Mobile/tablet: rows collapse into labelled cards.
       1023px matches the app's mobile-layout breakpoint (bottom nav). ---------- */
    @media (max-width: 1023px) {
        .txn-table thead { display: none; }

        .txn-table,
        .txn-table tbody,
        .txn-table tr,
        .txn-table td {
            display: block;
            width: 100%;
        }

        .txn-table tr {
            border-top: 1px solid #e2e8f0;
            padding: 0.4rem 0;
        }

        .txn-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            border: none;
            padding: 0.3rem 1rem;
        }

        .txn-table td::before {
            content: attr(data-label);
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #0C2C6B;
        }
    }
</style>

@php
    /* Initial pending counts for the tab badges (server-rendered so they
       appear immediately; the tab components keep them updated via the
       txn-badges-updated browser event after every approve/reject). */
    $pendingWithdrawalsCount = \App\Models\Withdrawal::where('status', 'Pending')->count();
    $pendingActivationsCount = \App\Models\Activation::where('status', 'Pending')->count();
@endphp

<div class="report-settings-container"
     x-data="{
         tab: $persist('overview').as('transaction-list-tab'),
         pendingWithdrawals: {{ $pendingWithdrawalsCount }},
         pendingActivations: {{ $pendingActivationsCount }},
     }"
     x-init="window.addEventListener('txn-badges-updated', (e) => {
         pendingActivations = e.detail ? (e.detail.activations ?? pendingActivations) : pendingActivations;
         pendingWithdrawals = e.detail ? (e.detail.withdrawals ?? pendingWithdrawals) : pendingWithdrawals;
     });
     // Clamp any stale persisted tab (e.g. corrupted localStorage or a tab
     // that no longer exists) so the page never renders without a panel.
     if (! ['overview','withdrawals','activations','disbursements'].includes(tab)) {
         tab = 'overview';
     }">

    <nav class="report-settings-header">
        <div class="report-settings-header-title">
            <h1>Transaction List</h1>
        </div>
    </nav>

    <div class="report-settings-layout">

        <div class="report-settings-desktop-nav">

            <a href="#"
               @click.prevent="tab='overview'"
               :class="{ active: tab==='overview' }">
                <span class="report-settings-tab-icon">⌂</span>
                <span>Overview</span>
            </a>

            <a href="#"
               @click.prevent="tab='withdrawals'"
               :class="{ active: tab==='withdrawals' }">
                <span class="report-settings-tab-icon">↧</span>
                <span>Withdrawal</span>
                <span class="txn-tab-badge" x-show="pendingWithdrawals > 0"
                      x-text="pendingWithdrawals" x-cloak></span>
            </a>

            <a href="#"
               @click.prevent="tab='activations'"
               :class="{ active: tab==='activations' }">
                <span class="report-settings-tab-icon">✦</span>
                <span>Activations</span>
                <span class="txn-tab-badge" x-show="pendingActivations > 0"
                      x-text="pendingActivations" x-cloak></span>
            </a>

            <a href="#"
               @click.prevent="tab='disbursements'"
               :class="{ active: tab==='disbursements' }">
                <span class="report-settings-tab-icon">₱</span>
                <span>Disbursement</span>
            </a>

        </div>

        <div x-show="tab==='overview'" x-cloak class="report-settings-main-card">
            <livewire:admin-pages.transaction-list.overview />
        </div>

        <div x-show="tab==='withdrawals'" x-cloak class="report-settings-main-card">
            <livewire:admin-pages.transaction-list.withdrawals />
        </div>

        <div x-show="tab==='activations'" x-cloak class="report-settings-main-card">
            <livewire:admin-pages.transaction-list.activations />
        </div>

        <div x-show="tab==='disbursements'" x-cloak class="report-settings-main-card">
            <livewire:admin-pages.transaction-list.disbursements />
        </div>

    </div>

</div>

{{--
    REVIEW MODALS — used by the Activations tab row actions. A regular
    TrueLife member activation opens the members-page review modal; a
    satellite member activation opens the satellite review modal. Both are
    the same components the Masterlist pages use (they listen for their
    open events globally), so approve/reject/resubmit inside them behaves
    identically and refreshes the tab rows + badges via members-refresh /
    txn-badges-updated.
--}}
<livewire:admin-pages.masterlist.activation-review-modal />
<livewire:admin-pages.masterlist.satellite-members-components.satellite-members-activation-review />

{{-- Activation FORM modals — opened by the Activations tab RESUBMIT action:
     TrueLife members get the regular ActivationModal in reactivate mode
     (pre-filled from the rejected request, submit "ReActivate Account"),
     satellite members get the reapply wizard. Both dispatch
     members-refresh + txn-badges-updated after submitting, so the tab rows
     and page badges stay in sync. --}}
<livewire:member-pages.my-profile.activation-modal />
<livewire:admin-pages.masterlist.members-components.satellite-modal />

{{-- Withdrawal review modal — the ONLY route to approve/reject a withdrawal:
     the row eye opens this modal first (full details), and its Approve /
     Reject buttons fire the typed dialogs below. --}}
<livewire:admin-pages.transaction-list.withdrawal-review-modal />

{{--
    APPROVE / REJECT DIALOGS (shared SwalFlow)
    Fired from the Withdrawal / Activations tab components via browser
    events (confirmApprove*/confirmReject* gates in PHP dispatch them).
    Approve requires the typed word APPROVE; withdrawal reject requires
    REJECT; activation reject requires remarks. Handled here (plain
    livewire:init script, same pattern as the shared sweet-alert-handler)
    so the dialogs work regardless of which tab component dispatched.
--}}
<script>
    document.addEventListener('livewire:init', () => {
        const callTxn = (name, method, ...args) => {
            const el = [...document.querySelectorAll('[wire\\:id]')]
                .find((el) => JSON.parse(el.getAttribute('wire:snapshot') || '{}').memo?.name === name);

            // Livewire.find() returns the component's $wire object directly.
            const wire = el ? window.Livewire.find(el.getAttribute('wire:id')) : null;

            if (! wire) {
                Swal.fire({
                    icon: 'error',
                    title: 'Tab Not Loaded',
                    text: 'Open the ' + (name.includes('withdrawal') ? 'Withdrawal' : 'Activations') + ' tab first, then try again.',
                    confirmButtonColor: '#0B2A5B',
                });
                return;
            }

            wire.call(method, ...args);
        };

        // ---------- ACTIVATIONS: APPROVE (typed APPROVE) ----------
        window.Livewire.on('txn-approve-activation-prompt', (raw) => {
            const d = Array.isArray(raw) ? raw[0] : raw;

            SwalFlow.confirm({
                title: 'Approve Activation?',
                html: 'You are about to approve the activation of '
                    + '<strong style="color: #0B2A5B;">' + (d.memberName || 'this member') + '</strong>'
                    + ' for <strong style="color: #0B2A5B;">₱' + (d.amount || '0.00') + '</strong>.'
                    + ' This activates the member account, writes wallets/ledgers and sends emails.'
                    + '<br><br>Type <strong>APPROVE</strong> to confirm.',
                icon: 'question',
                requireConfirmWord: 'APPROVE',
                inputPlaceholder: 'APPROVE',
                confirmText: 'Yes, approve!',
                cancelText: 'Cancel',
                loadingTitle: 'Approving activation...',
                loadingText: 'Updating records and sending emails.',
            }, () => callTxn('admin-pages.transaction-list.activations', 'approveActivation', d.activationId));
        });

        // ---------- WITHDRAWALS: APPROVE (typed APPROVE) ----------
        // The review modal STAYS OPEN behind the typed dialog — it is only
        // dismissed after the typed APPROVE is confirmed and processing
        // succeeds (the tab component broadcasts withdrawal-processed, and
        // the modal closes itself then; the success alert follows).
        window.Livewire.on('txn-approve-withdrawal-prompt', (raw) => {
            const d = Array.isArray(raw) ? raw[0] : raw;

            SwalFlow.confirm({
                title: 'Approve & Release Withdrawal?',
                html: 'You are about to release '
                    + '<strong style="color: #0B2A5B;">₱' + (d.amount || '0.00') + '</strong>'
                    + ' to <strong style="color: #0B2A5B;">' + (d.memberName || 'this member') + '</strong>.'
                    + ' Wallet and ledger records will be written immediately.'
                    + '<br><br>Type <strong>APPROVE</strong> to confirm.',
                icon: 'question',
                requireConfirmWord: 'APPROVE',
                inputPlaceholder: 'APPROVE',
                confirmText: 'Yes, approve!',
                cancelText: 'Cancel',
                loadingTitle: 'Approving withdrawal...',
                loadingText: 'Releasing funds and updating records.',
            }, () => callTxn('admin-pages.transaction-list.withdrawals', 'approveWithdrawal', d.withdrawalId));
        });

        // Activation review-modal confirmations (approve/reject/resubmit)
        // are handled globally by the shared sweet-alert-handler — the modals
        // dispatch txn-modal-* events so they work on every page.

        // ---------- WITHDRAWALS: REJECT (typed REJECT) ----------
        // Same rule as approve: the review modal stays open behind the
        // typed dialog and closes itself after the rejection is processed.
        window.Livewire.on('txn-reject-withdrawal-prompt', (raw) => {
            const d = Array.isArray(raw) ? raw[0] : raw;

            SwalFlow.confirm({
                title: 'Reject Withdrawal?',
                html: 'This withdrawal request will be rejected. This action cannot be undone.'
                    + '<br><br>Type <strong>REJECT</strong> to confirm.',
                icon: 'warning',
                requireConfirmWord: 'REJECT',
                inputPlaceholder: 'REJECT',
                confirmText: 'Yes, reject it!',
                cancelText: 'Cancel',
                loadingTitle: 'Rejecting withdrawal...',
                loadingText: 'Updating records.',
            }, () => callTxn('admin-pages.transaction-list.withdrawals', 'rejectWithdrawal', d.withdrawalId));
        });
    });
</script>

@endsection
