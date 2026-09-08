@extends('layouts.member')

@section('title', 'Dashboard')

@section('content')
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
        </div>

        <!-- TAB CONTENT -->

        <!-- MY DASHBOARD -->
        <div x-show="tab === 'overview'" x-cloak class="dashboard-card">
            <livewire:share-pages.dashboard.news-updates />
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
