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
            <a href="#"
               @click.prevent="tab = 'overview'"
               :class="tab === 'overview' ? 'active' : ''">
                Overview
            </a>

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
            <livewire:member-pages.dashboard.news-updates />
        </div>

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
