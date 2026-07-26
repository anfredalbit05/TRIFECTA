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
               @click.prevent="tab='members'"
               :class="tab === 'members' ? 'active' : ''">
                Members
            </a>


        </div>

    <!-- DASHBOARD CONTENT -->
    <div class="dashboard-layout px-10 pt-4">

        <!-- OVERVIEW -->
        <div
            x-show="tab === 'overview'"
                  x-cloak>

            <x-member-pages.dashboard.overview />

        </div>


        <!-- MEMBERS -->
        <div
            x-show="tab === 'members'"
            x-cloak
            class="dashboard-main-card">

            <x-member-pages.dashboard.members />

        </div>

    </div>


</div>

@endsection
