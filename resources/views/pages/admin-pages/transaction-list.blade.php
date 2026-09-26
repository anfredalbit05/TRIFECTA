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

    .txn-pill-approved { background: #15803d; }
    .txn-pill-pending { background: #D79B10; }
    .txn-pill-rejected { background: #b91c1c; }

    /* ---------- Responsive tab nav: scroll instead of wrapping/overflowing -- */
    .report-settings-desktop-nav {
        max-width: 100%;
        overflow-x: auto;
        scrollbar-width: thin;
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

<div class="report-settings-container"
     x-data="{ tab: $persist('overview').as('transaction-list-tab') }">

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
            </a>

            <a href="#"
               @click.prevent="tab='activations'"
               :class="{ active: tab==='activations' }">
                <span class="report-settings-tab-icon">✦</span>
                <span>Activations</span>
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

    </div>

</div>

@endsection
