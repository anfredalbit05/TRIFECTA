@extends('layouts.member')

@section('title', 'Command Center')

@section('content')

@php
    $user = auth()->user();
    $hasWithdrawals = \App\Models\Withdrawal::where('UserID', $user->UserID)->exists();
@endphp

<div class="ws-page" x-data="{ tab: 'overview' }">

    <header class="ws-head">
        <div>
            <span class="ws-head-kicker">{{ $user->RoleID == 8 ? 'Member Portal' : 'Member Workspace' }}</span>
            <h1>Command Center</h1>
            <p>
                Welcome back, {{ $user->firstname ?? 'Member' }}. Your cover, your community and your money —
                all in one view.
            </p>
        </div>

        <div class="ws-head-side">
            <span class="ws-head-pill">TRIFECTA member</span>
            <span class="ws-head-pill"><strong>{{ date('Y') }}</strong> coverage year</span>
        </div>
    </header>

    <div class="ws-grid">

        <aside class="ws-rail">

            <div class="ws-rail-group">
                <div class="ws-rail-label">Snapshot</div>

                <a href="#" class="ws-rail-link" :class="tab === 'overview' ? 'is-active' : ''"
                   @click.prevent="tab = 'overview'">
                    <span class="ws-rail-icon">⌂</span>
                    <span>My Overview</span>
                </a>

                @if($user->RoleID != 8)
                    <a href="#" class="ws-rail-link" :class="tab === 'analytics' ? 'is-active' : ''"
                       @click.prevent="tab = 'analytics'">
                        <span class="ws-rail-icon">▤</span>
                        <span>Analytics</span>
                    </a>
                @endif
            </div>

            @if($hasWithdrawals)
                <div class="ws-rail-group">
                    <div class="ws-rail-label">Money</div>
                    <a href="#" class="ws-rail-link" :class="tab === 'withdrawals' ? 'is-active' : ''"
                       @click.prevent="tab = 'withdrawals'">
                        <span class="ws-rail-icon">₱</span>
                        <span>Withdrawals</span>
                    </a>
                </div>
            @endif

        </aside>

        <section class="ws-panel">

            <div x-show="tab === 'overview'" x-cloak class="dashboard-card">
                <livewire:share-pages.dashboard.news-updates lazy />
            </div>

            @if($hasWithdrawals)
                <div x-show="tab === 'withdrawals'" x-cloak class="withdrawals-card">
                    <livewire:share-pages.dashboard.withdrawals lazy />
                </div>
            @endif

            @if($user->RoleID != 4)
                <div x-show="tab === 'analytics'" x-cloak class="dashboard-card">
                    <livewire:admin-pages.dashboard.overview-analytics lazy />
                </div>
            @endif

        </section>

    </div>

    {{-- The analytics tab is a lazy Livewire component, so its Alpine factory
         must be registered before Alpine starts. Load the module here on the
         static page instead of inside the lazy component. --}}
    @vite('resources/js/livewire/admin-pages/dashboard/overview.js')

</div>

@endsection
