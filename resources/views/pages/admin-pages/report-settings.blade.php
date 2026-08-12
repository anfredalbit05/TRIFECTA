@extends('layouts.member')

@section('title', 'Report & Settings')

@section('content')

<div class="dashboard-container"  x-data="{ tab: $persist('overview').as('report-settings-tab') }">

    <!-- REPORT & SETTINGS NAVIGATION -->
    <nav class="dashboard-nav">
        <div class="dashboard-nav-title">
            <h1>
                Report & Settings
            </h1>
        </div>
    </nav>

    <!-- CONTENT WRAPPER (Includes both Nav Menu and Cards to keep identical layout width) -->
    <div class="dashboard-layout px-12 pt-4 flex flex-col gap-4">

        <div class="dashboard-nav-menu">
            <a
                href="#"
                @click.prevent="tab='overview'"
                :class="tab === 'overview' ? 'active' : ''">
                Overview
            </a>

            @if(auth()->user()->RoleID == 1)
            <a
                href="#"
                @click.prevent="tab='general'"
                :class="tab === 'general' ? 'active' : ''">
                General
            </a>
            @endif
            <a
                href="#"
                @click.prevent="tab='users'"
                :class="tab === 'users' ? 'active' : ''">
                Users Reports
            </a>

            <a
                href="#"
                @click.prevent="tab='reports'"
                :class="tab === 'reports' ? 'active' : ''">
                Ledgers
            </a>
        </div>

        <!-- OVERVIEW -->
        <div
            x-show="tab === 'overview'"
            class="dashboard-main-card full-width-card">
           <livewire:admin-pages.report-settings.overview />
        </div>

        @if(auth()->user()->RoleID == 1)
        <!-- GENERAL -->
        <div
            x-show="tab === 'general'"
            x-cloak
            class="dashboard-main-card full-width-card">
            <livewire:admin-pages.report-settings.general-settings />
        </div>
        @endif
        <!-- USERS -->
        <div
            x-show="tab === 'users'"
            x-cloak
            class="dashboard-main-card full-width-card">
            <livewire:admin-pages.report-settings.user-accounts />
        </div>

            <!-- REPORTS -->
        <div
            x-show="tab === 'reports'"
            x-cloak
            class="dashboard-main-card full-width-card">
            <livewire:admin-pages.report-settings.reports />
        </div>

    </div>

</div>


@endsection
