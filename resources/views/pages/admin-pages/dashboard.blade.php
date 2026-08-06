@extends('layouts.member')

@section('title', 'Dashboard')

@section('content')

<div class="dashboard-container"  x-data="{ tab: $persist('overview').as('dashboard-tab') }">

    <!-- DASHBOARD TOP NAVIGATION -->
    <nav class="dashboard-nav">
        <div class="dashboard-nav-title">
            <h1>
                Dashboard
            </h1>
        </div>
    </nav>

    <!-- CONTENT WRAPPER (Includes both Nav Menu and Cards to keep identical layout width) -->
    <div class="dashboard-layout px-12 pt-4 flex flex-col gap-4">

        <div class="dashboard-nav-menu">
            <a href="#"
               @click.prevent="tab='overview'"
               :class="tab === 'overview' ? 'active' : ''">
                Overview
            </a>

            <a href="#"
               @click.prevent="tab='my-dashboard'"
               :class="tab === 'my-dashboard' ? 'active' : ''">
                My Dashboard
            </a>

            @if(auth()->user()->RoleID == 1)
            <a href="#"
               @click.prevent="tab='all-users'"
               :class="tab === 'all-users' ? 'active' : ''">
                All Users
            </a>
            @endif
        </div>

        <!-- DASHBOARD CONTENT -->
        <!-- OVERVIEW -->
        <div
            x-show="tab === 'overview'"
            x-cloak
            class="dashboard-main-card full-width-card">
            <livewire:admin-pages.dashboard.overview-analytics />
        </div>

        <!-- MY DASHBOARD -->
        <div
            x-show="tab === 'my-dashboard'"
            x-cloak
            class="dashboard-main-card full-width-card">
            <livewire:member-pages.dashboard.news-updates />
        </div>

        <!-- ALL USERS -->
        <div
            x-show="tab === 'all-users'"
            x-cloak
            class="dashboard-main-card full-width-card">
            <livewire:admin-pages.dashboard.all-users />
        </div>

    </div>

</div>

@endsection
