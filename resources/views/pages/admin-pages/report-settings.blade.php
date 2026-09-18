@extends('layouts.member')

@section('title', 'Report & Settings')

@section('content')

<div class="report-settings-container"
     x-data="{ tab: $persist('overview').as('report-settings-tab') }">

    <nav class="report-settings-header">
        <div class="report-settings-header-title">
            <h1>Report & Settings</h1>
        </div>
    </nav>

    <div class="report-settings-layout">

        <div class="report-settings-desktop-nav">

            <a href="#"
               @click.prevent="tab='overview'"
               :class="{ active: tab==='overview' }">
                <span class="report-settings-tab-icon">⌂</span>
                <span>Overview</span>
            </a>

            @if(auth()->user()->RoleID == 1)
                <a href="#"
                   @click.prevent="tab='general'"
                   :class="{ active: tab==='general' }">
                    <span class="report-settings-tab-icon">⚙</span>
                    <span>General</span>
                </a>
            @endif

            <a href="#"
               @click.prevent="tab='users'"
               :class="{ active: tab==='users' }">
                <span class="report-settings-tab-icon">♟</span>
                <span>Users Reports</span>
            </a>

            <a href="#"
               @click.prevent="tab='reports'"
               :class="{ active: tab==='reports' }">
                <span class="report-settings-tab-icon">▤</span>
                <span>Ledgers</span>
            </a>

            @if(in_array(auth()->user()->RoleID, [1,2,5,6,7]))
                <a href="#"
                   @click.prevent="tab='pangkabuhayan'"
                   :class="{ active: tab==='pangkabuhayan' }">
                    <span class="report-settings-tab-icon">🎡</span>
                    <span>Pangkabuhayan</span>
                </a>
            @endif

        </div>

        <div x-show="tab==='overview'" x-cloak class="report-settings-main-card">
            <livewire:admin-pages.report-settings.overview />
        </div>

        @if(auth()->user()->RoleID == 1)
            <div x-show="tab==='general'" x-cloak class="report-settings-main-card">
                <livewire:admin-pages.report-settings.general-settings />
            </div>
        @endif

        <div x-show="tab==='users'" x-cloak class="report-settings-main-card">
            <livewire:admin-pages.report-settings.user-accounts />
        </div>

        <div x-show="tab==='reports'" x-cloak class="report-settings-main-card">
            <livewire:admin-pages.report-settings.reports />
        </div>

        @if(in_array(auth()->user()->RoleID, [1,2,5,6,7]))
            <div x-show="tab==='pangkabuhayan'" x-cloak class="report-settings-main-card">
                <livewire:components.spin-fund-report />
            </div>
        @endif

    </div>

</div>

@endsection
