@extends('layouts.member')

@section('title', 'Dashboard')

@section('content')

<div class="dashboard-container" x-data="{ tab: 'overview' }">

    <!-- DASHBOARD TOP NAVIGATION -->
    <nav class="dashboard-nav">
        <div class="dashboard-nav-title">
            <h1>Dashboard</h1>
        </div>
    </nav>

    <!-- CONTENT WRAPPER (Includes both Nav Menu and Tabs to keep identical width matching genealogy) -->
    <div class="dashboard-layout px-12 pt-4 flex flex-col gap-4">

        <!-- NAVIGATION MENU -->
        <div class="dashboard-nav-menu">

             @if(auth()->user()->RoleID == 1)
              <a href="#"
               @click.prevent="tab='analytics'"
               :class="tab === 'analytics' ? 'active' : ''">
                Overview
            </a>
            @endif

            <a href="#"
               @click.prevent="tab = 'overview'"
               :class="tab === 'overview' ? 'active' : ''">
                My Dashboard
            </a>


             @if(auth()->user()->RoleID == 1)
            <a href="#"
               @click.prevent="tab='admin-users'"
               :class="tab === 'admin-users' ? 'active' : ''">
                All Users
            </a>
            @endif


            {{-- <a href="#"
               @click.prevent="tab = 'my-dashboard'"
               :class="tab === 'my-dashboard' ? 'active' : ''">
                My Dashboard
            </a>


            <a href="#"
               @click.prevent="tab = 'all-users'"
               :class="tab === 'all-users' ? 'active' : ''">
                All Users
            </a> --}}

        </div>

        <!-- OVERVIEW TAB -->
        <div x-show="tab === 'overview'" x-cloak class="tab-card">
            <livewire:share-pages.dashboard.news-updates />
        </div>

          <div
            x-show="tab === 'analytics'"
            x-cloak
            class="dashboard-main-card full-width-card">
            <livewire:admin-pages.dashboard.overview-analytics />
        </div>

{{--
        <!-- ALL USERS -->
        <div
            x-show="tab === 'all-users'"
            x-cloak
            class="dashboard-main-card full-width-card">
            <livewire:admin-pages.dashboard.admin-users />
        </div> --}}

        {{-- <!-- MY DASHBOARD TAB -->
        <div x-show="tab === 'my-dashboard'" x-cloak class="tab-card">
            <!-- Add content here -->
        </div>

        <!-- ALL USERS TAB -->
        <div x-show="tab === 'all-users'" x-cloak class="tab-card">
            <!-- Add content here -->
        </div> --}}

    </div>

</div>

@endsection
