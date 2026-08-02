@extends('layouts.member')

@section('title', 'Dashboard')

@section('content')

<div class="dashboard-container"  p-6 x-data="{ tab: 'overview' }">


    <!-- DASHBOARD TOP NAVIGATION -->
    <nav class="dashboard-nav">


        <div class="dashboard-nav-title">

            <h1>
                Dashboard
            </h1>

        </div>

    </nav>

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

            <a href="#"
               @click.prevent="tab='all-users'"
               :class="tab === 'all-users' ? 'active' : ''">
                All Users
            </a>


        </div>

    <!-- DASHBOARD CONTENT -->
    <div class="dashboard-layout px-10 pt-4">

        <!-- OVERVIEW -->
        <div
            x-show="tab === 'overview'"
                x-cloak
                class="dashboard-main-card full-width-card">

           <livewire:admin-pages.dashboard.overview-analytics />

        </div>

        <!-- MEMBERS -->
        <div
            x-show="tab === 'my-dashboard'"
            x-cloak
            class="dashboard-main-card full-width-card">

             <livewire:member-pages.dashboard.news-updates />

        </div>

          <!-- MEMBERS -->
        <div
            x-show="tab === 'all-users'"
            x-cloak
            class="dashboard-main-card full-width-card">

             <livewire:admin-pages.dashboard.all-users />

        </div>

    </div>


</div>

@endsection
