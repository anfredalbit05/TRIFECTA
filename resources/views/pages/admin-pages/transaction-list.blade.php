@extends('layouts.member')

@section('title', 'Payment Desk')

@push('styles')
    @vite('resources/css/admin/print/transaction-list.css')
@endpush

@section('content')

@php
    /* Initial pending counts for the tab badges (server-rendered so they
       appear immediately; the tab components keep them updated via the
       txn-badges-updated browser event after every approve/reject). */
    $pendingWithdrawalsCount = \App\Models\Withdrawal::where('status', 'Pending')->count();
    $pendingActivationsCount = \App\Models\Activation::where('status', 'Pending')->count();
@endphp

<div class="ws-page"
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

    <header class="ws-head">
        <div>
            <span class="ws-head-kicker">Money · Queues</span>
            <h1>Payment Desk</h1>
            <p>Review every withdrawal, activation and disbursement request before money or cover moves.</p>
        </div>

        <div class="ws-head-side">
            <span class="ws-head-pill"><strong x-text="pendingWithdrawals">{{ $pendingWithdrawalsCount }}</strong> withdrawals</span>
            <span class="ws-head-pill"><strong x-text="pendingActivations">{{ $pendingActivationsCount }}</strong> activations</span>
        </div>
    </header>

    <div class="ws-grid">

        <aside class="ws-rail">

            <div class="ws-rail-group">
                <div class="ws-rail-label">Snapshot</div>
                <a href="#" class="ws-rail-link" :class="tab === 'overview' ? 'is-active' : ''"
                   @click.prevent="tab = 'overview'">
                    <span class="ws-rail-icon">⌂</span>
                    <span>Overview</span>
                </a>
            </div>

            <div class="ws-rail-group">
                <div class="ws-rail-label">Queues</div>

                <a href="#" class="ws-rail-link" :class="tab === 'withdrawals' ? 'is-active' : ''"
                   @click.prevent="tab = 'withdrawals'">
                    <span class="ws-rail-icon">↧</span>
                    <span>Withdrawal</span>
                    <span class="ws-rail-badge" x-show="pendingWithdrawals > 0"
                          x-text="pendingWithdrawals" x-cloak></span>
                </a>

                <a href="#" class="ws-rail-link" :class="tab === 'activations' ? 'is-active' : ''"
                   @click.prevent="tab = 'activations'">
                    <span class="ws-rail-icon">✦</span>
                    <span>Activations</span>
                    <span class="ws-rail-badge" x-show="pendingActivations > 0"
                          x-text="pendingActivations" x-cloak></span>
                </a>

                @if(auth()->user()->RoleID == 1)
                    <a href="#" class="ws-rail-link" :class="tab === 'disbursements' ? 'is-active' : ''"
                       @click.prevent="tab = 'disbursements'">
                        <span class="ws-rail-icon">₱</span>
                        <span>Disbursement</span>
                    </a>
                @endif
            </div>

        </aside>

        <section class="ws-panel">

            <template x-if="tab === 'overview'">
                <div class="report-settings-main-card">
                    <livewire:admin-pages.transaction-list.overview lazy />
                </div>
            </template>

            <template x-if="tab === 'withdrawals'">
                <div class="report-settings-main-card">
                    <livewire:admin-pages.transaction-list.withdrawals lazy />
                </div>
            </template>

            <template x-if="tab === 'activations'">
                <div class="report-settings-main-card">
                    <livewire:admin-pages.transaction-list.activations lazy />
                </div>
            </template>

            @if(auth()->user()->RoleID == 1)
                <template x-if="tab === 'disbursements'">
                    <div class="report-settings-main-card">
                        <livewire:admin-pages.transaction-list.disbursements lazy />
                    </div>
                </template>
            @endif

        </section>

    </div>

</div>

{{--
    REVIEW MODALS — used by the Activations tab row actions. A regular
    TRIFECTA member activation opens the members-page review modal; a
    satellite member activation opens the satellite review modal. Both are
    the same components the Masterlist pages use (they listen for their
    open events globally), so approve/reject/resubmit inside them behaves
    identically and refreshes the tab rows + badges via members-refresh /
    txn-badges-updated.
--}}
<livewire:admin-pages.masterlist.activation-review-modal />
<livewire:admin-pages.masterlist.satellite-members-components.satellite-members-activation-review />

{{-- Activation FORM modals — opened by the Activations tab RESUBMIT action:
     TRIFECTA members get the regular ActivationModal in reactivate mode
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
@vite('resources/js/pages/admin-pages/transaction-list.js')

@endsection
