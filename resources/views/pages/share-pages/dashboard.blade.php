@extends('layouts.member')

@section('title', 'Dashboard')

@section('content')

@php
    $user = auth()->user();
    $hasWithdrawals = \App\Models\Withdrawal::where('UserID', $user->UserID)->exists();
@endphp

<div class="dashboard-page" x-data="{ tab: 'overview' }">

    <nav class="dashboard-header">
        <div class="dashboard-header-title">
            <h1>Dashboard</h1>
        </div>
    </nav>

    <main class="dashboard-content">

        <div class="dashboard-tabs">

            @if($user->RoleID != 4)
                <a href="#"
                   @click.prevent="tab='analytics'"
                   :class="{active:tab==='analytics'}">
                    <span class="dashboard-tab-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M4 19V5M4 19h16M8 16v-5M12 16V8M16 16v-3M20 16V6"/>
                        </svg>
                    </span>
                    <span>Overview</span>
                </a>
            @endif

            <a href="#"
               @click.prevent="tab='overview'"
               :class="{active:tab==='overview'}">
                <span class="dashboard-tab-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M3 10.5L12 3l9 7.5M5 9.5V21h14V9.5M9 21v-6h6v6"/>
                    </svg>
                </span>
                <span>My Dashboard</span>
            </a>

            @if($hasWithdrawals)
                <a href="#"
                   @click.prevent="tab='withdrawals'"
                   :class="{active:tab==='withdrawals'}">
                    <span class="dashboard-tab-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M3 7.5A2.5 2.5 0 0 1 5.5 5h13A2.5 2.5 0 0 1 21 7.5v9a2.5 2.5 0 0 1-2.5 2.5h-13A2.5 2.5 0 0 1 3 16.5v-9Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M3 9h18M16 13h2"/>
                        </svg>
                    </span>
                    <span>My Withdrawals</span>
                </a>
            @endif

        </div>

        <div x-show="tab==='overview'" x-cloak class="dashboard-card">
            <livewire:share-pages.dashboard.news-updates />
        </div>

        @if($hasWithdrawals)
            <div x-show="tab==='withdrawals'" x-cloak class="withdrawals-card">
                <livewire:share-pages.dashboard.withdrawals />
            </div>
        @endif

        @if($user->RoleID != 4)
            <div x-show="tab==='analytics'" x-cloak class="dashboard-card">
                <livewire:admin-pages.dashboard.overview-analytics />
            </div>
        @endif

    </main>

</div>

@endsection
