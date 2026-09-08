@extends('layouts.member')

@section('title', 'Report & Settings')

@section('content')

<div
    class="report-settings-container"
    x-data="{ tab: $persist('overview').as('report-settings-tab') }"
>

    <!-- =========================================
         REPORT & SETTINGS HEADER
    ========================================== -->

    <nav class="report-settings-header">

        <div class="report-settings-header-title">
            <h1>Report & Settings</h1>
        </div>

    </nav>


    <!-- =========================================
         MAIN CONTENT
    ========================================== -->

    <div class="report-settings-layout">

        <!-- =====================================
             DESKTOP TAB NAVIGATION
        ====================================== -->

        <div class="report-settings-nav-menu report-settings-desktop-nav">

            <!-- OVERVIEW -->
            <a
                href="#"
                @click.prevent="tab = 'overview'"
                :class="tab === 'overview' ? 'active' : ''"
            >
                <span class="report-settings-tab-icon">
                    ⌂
                </span>

                <span class="report-settings-tab-label">
                    Overview
                </span>
            </a>


            <!-- GENERAL -->
            @if(auth()->user()->RoleID == 1)

                <a
                    href="#"
                    @click.prevent="tab = 'general'"
                    :class="tab === 'general' ? 'active' : ''"
                >
                    <span class="report-settings-tab-icon">
                        ⚙
                    </span>

                    <span class="report-settings-tab-label">
                        General
                    </span>
                </a>

            @endif


            <!-- USERS REPORTS -->
            <a
                href="#"
                @click.prevent="tab = 'users'"
                :class="tab === 'users' ? 'active' : ''"
            >
                <span class="report-settings-tab-icon">
                    ♟
                </span>

                <span class="report-settings-tab-label">
                    Users Reports
                </span>
            </a>


            <!-- LEDGERS -->
            <a
                href="#"
                @click.prevent="tab = 'reports'"
                :class="tab === 'reports' ? 'active' : ''"
            >
                <span class="report-settings-tab-icon">
                    ▤
                </span>

                <span class="report-settings-tab-label">
                    Ledgers
                </span>
            </a>

        </div>


        <!-- =====================================
             TAB CONTENT
        ====================================== -->

        <!-- OVERVIEW -->
        <div
            x-show="tab === 'overview'"
            x-cloak
            class="report-settings-main-card"
        >
            <livewire:admin-pages.report-settings.overview />
        </div>


        <!-- GENERAL -->
        @if(auth()->user()->RoleID == 1)

            <div
                x-show="tab === 'general'"
                x-cloak
                class="report-settings-main-card"
            >
                <livewire:admin-pages.report-settings.general-settings />
            </div>

        @endif


        <!-- USERS REPORTS -->
        <div
            x-show="tab === 'users'"
            x-cloak
            class="report-settings-main-card"
        >
            <livewire:admin-pages.report-settings.user-accounts />
        </div>


        <!-- LEDGERS -->
        <div
            x-show="tab === 'reports'"
            x-cloak
            class="report-settings-main-card"
        >
            <livewire:admin-pages.report-settings.reports />
        </div>

    </div>


    <!-- =========================================
         MOBILE BOTTOM NAVIGATION
    ========================================== -->

    <template x-teleport="body">

        <nav class="report-settings-mobile-nav">

            <!-- OVERVIEW -->
            <a
                href="#"
                @click.prevent="tab = 'overview'"
                :class="tab === 'overview' ? 'active' : ''"
                aria-label="Overview"
            >
                <span class="report-settings-mobile-icon">
                    ⌂
                </span>

                <span class="report-settings-mobile-label">
                    Overview
                </span>
            </a>


            <!-- GENERAL -->
            @if(auth()->user()->RoleID == 1)

                <a
                    href="#"
                    @click.prevent="tab = 'general'"
                    :class="tab === 'general' ? 'active' : ''"
                    aria-label="General"
                >
                    <span class="report-settings-mobile-icon">
                        ⚙
                    </span>

                    <span class="report-settings-mobile-label">
                        General
                    </span>
                </a>

            @endif


            <!-- USERS -->
            <a
                href="#"
                @click.prevent="tab = 'users'"
                :class="tab === 'users' ? 'active' : ''"
                aria-label="Users Reports"
            >
                <span class="report-settings-mobile-icon">
                    ♟
                </span>

                <span class="report-settings-mobile-label">
                    Users
                </span>
            </a>


            <!-- LEDGERS -->
            <a
                href="#"
                @click.prevent="tab = 'reports'"
                :class="tab === 'reports' ? 'active' : ''"
                aria-label="Ledgers"
            >
                <span class="report-settings-mobile-icon">
                    ▤
                </span>

                <span class="report-settings-mobile-label">
                    Ledgers
                </span>
            </a>

        </nav>

    </template>

</div>

@endsection
