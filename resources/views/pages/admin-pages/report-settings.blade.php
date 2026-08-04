@extends('layouts.member')

@section('title', 'Report & Settings')

@section('content')

<div class="dashboard-container" x-data="{ tab: 'overview' }">



    <!-- REPORT & SETTINGS NAVIGATION -->
    <nav class="dashboard-nav">


        <div class="dashboard-nav-title">

            <h1>
                Report & Settings
            </h1>

        </div>
    </nav>

        <div class="dashboard-nav-menu">
            <a
                href="#"
                @click.prevent="tab='overview'"
                :class="tab === 'overview' ? 'active' : ''">
                Overview
            </a>



            <a
                href="#"
                @click.prevent="tab='general'"
                :class="tab === 'general' ? 'active' : ''">
                General
            </a>



            <a
                href="#"
                @click.prevent="tab='users'"
                :class="tab === 'users' ? 'active' : ''">
                Users
            </a>



            <a
                href="#"
                @click.prevent="tab='reports'"
                :class="tab === 'reports' ? 'active' : ''">
                Reports
            </a>


        </div>


    <!-- CONTENT -->

    <div class="dashboard-layout px-12 pt-4">

        <!-- OVERVIEW -->

        <div
            x-show="tab === 'overview'"
            class="dashboard-main-card full-width-card">


            <x-admin-pages.report-settings.overview />


        </div>

<!-- GENERAL -->
<div
    x-show="tab === 'general'"
    x-cloak
    class="dashboard-main-card full-width-card">

    @livewire('admin-pages.report-settings.general-settings')

</div>







        <!-- USERS -->

        <div
            x-show="tab === 'users'"
            x-cloak
            class="dashboard-main-card full-width-card">


            <x-admin-pages.report-settings.users-accounts />


        </div>







        <!-- REPORTS -->

        <div
            x-show="tab === 'reports'"
            x-cloak
            class="dashboard-main-card full-width-card">


            <x-admin-pages.report-settings.reports />


        </div>




    </div>



</div>

@endsection
