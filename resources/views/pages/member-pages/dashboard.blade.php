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
                Dashboard
            </a>

        </div>

    <!-- DASHBOARD CONTENT -->
    <div class="dashboard-layout px-12 pt-4">

        <!-- OVERVIEW -->
        <div
            x-show="tab === 'overview'"
                  x-cloak>

            <livewire:member-pages.dashboard.newsupdates />

        </div>


    </div>


</div>

@endsection
