@extends('layouts.member')

@section('title', 'Dashboard')

@section('content')

@php
    $hasWithdrawals = \App\Models\Withdrawal::where(
        'UserID',
        auth()->user()->UserID
    )->exists();
@endphp

<div class="dashboard-page" x-data="{ tab: 'overview' }">

    <!-- DASHBOARD HEADER -->
    <nav class="dashboard-header">
        <div class="dashboard-header-title">
            <h1>Dashboard</h1>
        </div>
    </nav>

    <!-- DASHBOARD CONTENT -->
    <div class="dashboard-content">

        <!-- DESKTOP NAVIGATION -->
        <div class="dashboard-tabs dashboard-desktop-tabs">
            @if(auth()->user()->RoleID != 4)
                <a href="#" @click.prevent="tab = 'analytics'" :class="tab === 'analytics' ? 'active' : ''">
                    <span class="dashboard-tab-icon">▦</span>
                    <span>Overview</span>
                </a>
            @endif

            <a href="#" @click.prevent="tab = 'overview'" :class="tab === 'overview' ? 'active' : ''">
                <span class="dashboard-tab-icon">⌂</span>
                <span>My Dashboard</span>
            </a>

            @if($hasWithdrawals)

                <a
                    href="#"
                    @click.prevent="tab = 'withdrawals'"
                    :class="tab === 'withdrawals' ? 'active' : ''"
                >
                    <span class="dashboard-tab-icon">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            width="20"
                            height="20"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M3 7.5A2.5 2.5 0 0 1 5.5 5h13A2.5 2.5 0 0 1 21 7.5v9a2.5 2.5 0 0 1-2.5 2.5h-13A2.5 2.5 0 0 1 3 16.5v-9Z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M3 9h18"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M16 13h2"
                            />
                        </svg>
                    </span>

                    <span>My Withdrawals</span>
                </a>

            @endif

        </div>

        <!-- TAB CONTENT -->

        <!-- MY DASHBOARD -->
        <div x-show="tab === 'overview'" x-cloak class="dashboard-card">
            <livewire:share-pages.dashboard.news-updates />
        </div>

                <!-- MY WITHDRAWALS -->
        <div x-show="tab === 'withdrawals'" x-cloak class="withdrawals-card">
            <livewire:share-pages.dashboard.withdrawals />
        </div>

        <!-- ANALYTICS -->
        @if(auth()->user()->RoleID != 4)
            <div x-show="tab === 'analytics'" x-cloak class="dashboard-card">
                <livewire:admin-pages.dashboard.overview-analytics />
            </div>
        @endif

    </div>

    <!-- MOBILE BOTTOM NAVIGATION (separate from desktop nav to avoid scrolling/overflow issues) -->
    <template x-teleport="body">
        <nav class="dashboard-mobile-nav">
            @if(auth()->user()->RoleID != 4)
                <a href="#" @click.prevent="tab = 'analytics'" :class="tab === 'analytics' ? 'active' : ''">
                    <span class="dashboard-mobile-icon">▦</span>
                    <span class="dashboard-mobile-label">Overview</span>
                </a>
            @endif

            <a href="#" @click.prevent="tab = 'overview'" :class="tab === 'overview' ? 'active' : ''">
                <span class="dashboard-mobile-icon">⌂</span>
                <span class="dashboard-mobile-label">My Dashboard</span>
            </a>
        </nav>
    </template>

</div>
@endsection
