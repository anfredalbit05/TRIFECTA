@extends('layouts.member')

@section('title', 'Report & Settings')

@section('content')

    <div class="report-settings-container" x-data="{ tab: $persist('overview').as('report-settings-tab') }">

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
                <a href="#" @click.prevent="tab = 'overview'" :class="tab === 'overview' ? 'active' : ''">
                    <span class="report-settings-tab-icon">
                        ⌂
                    </span>

                    <span class="report-settings-tab-label">
                        Overview
                    </span>
                </a>


                <!-- GENERAL -->
                @if (auth()->user()->RoleID == 1)
                    <a href="#" @click.prevent="tab = 'general'" :class="tab === 'general' ? 'active' : ''">
                        <span class="report-settings-tab-icon">
                            ⚙
                        </span>

                        <span class="report-settings-tab-label">
                            General
                        </span>
                    </a>
                @endif


                <!-- USERS REPORTS -->
                <a href="#" @click.prevent="tab = 'users'" :class="tab === 'users' ? 'active' : ''">
                    <span class="report-settings-tab-icon">
                        ♟
                    </span>

                    <span class="report-settings-tab-label">
                        Users Reports
                    </span>
                </a>


                <!-- LEDGERS -->
                <a href="#" @click.prevent="tab = 'reports'" :class="tab === 'reports' ? 'active' : ''">
                    <span class="report-settings-tab-icon">
                        ▤
                    </span>

                    <span class="report-settings-tab-label">
                        Ledgers
                    </span>
                </a>


                <!-- PANGKABUHAYAN FUND -->
                @if (in_array(auth()->user()->RoleID, [1, 2, 5, 6, 7]))
                    <a href="#" @click.prevent="tab = 'pangkabuhayan'"
                        :class="tab === 'pangkabuhayan' ? 'active' : ''">
                        <span class="report-settings-tab-icon">
                            <svg version="1.1" id="Uploaded to svgrepo.com" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" xml:space="preserve"
                                fill="#000000" transform="rotate(0)">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <style type="text/css">
                                        .linesandangles_een {
                                            fill: #111918;
                                        }
                                    </style>
                                    <path class="linesandangles_een"
                                        d="M16,4C9.373,4,4,9.373,4,16c0,6.627,5.373,12,12,12s12-5.373,12-12C28,9.373,22.627,4,16,4z M16.707,16.707C16.452,16.962,16.155,17,16,17c-0.155,0-0.452-0.038-0.707-0.293C15.038,16.452,15,16.155,15,16 c0-0.155,0.038-0.452,0.293-0.707c0.506-0.506,3.261-2.574,5.983-4.568C19.284,13.444,17.217,16.197,16.707,16.707z M24.118,10.219 c0.987,1.38,1.654,3.004,1.831,4.781h-5.352C21.844,13.342,23.238,11.433,24.118,10.219z M21.781,7.882 c-1.213,0.881-3.123,2.275-4.781,3.521V6.051C18.777,6.228,20.401,6.895,21.781,7.882z M15,6.051v6.886 c-0.523,0.414-0.929,0.75-1.121,0.942c-0.327,0.327-0.553,0.713-0.697,1.121H6.051C6.521,10.283,10.283,6.521,15,6.051z M6.051,17 h7.131c0.144,0.409,0.37,0.794,0.697,1.121s0.713,0.553,1.121,0.697v7.131C10.283,25.479,6.521,21.717,6.051,17z M17,25.949v-7.131 c0.409-0.144,0.794-0.37,1.121-0.697c0.194-0.194,0.53-0.599,0.943-1.121h6.885C25.479,21.717,21.717,25.479,17,25.949z">
                                    </path>
                                </g>
                            </svg>
                        </span>

                        <span class="report-settings-tab-label">
                            Pangkabuhayan
                        </span>
                    </a>
                @endif

            </div>


            <!-- =====================================
                 TAB CONTENT
            ====================================== -->

            <!-- OVERVIEW -->
            <div x-show="tab === 'overview'" x-cloak class="report-settings-main-card">
                <livewire:admin-pages.report-settings.overview />
            </div>


            <!-- GENERAL -->
            @if (auth()->user()->RoleID == 1)
                <div x-show="tab === 'general'" x-cloak class="report-settings-main-card">
                    <livewire:admin-pages.report-settings.general-settings />
                </div>
            @endif


            <!-- USERS REPORTS -->
            <div x-show="tab === 'users'" x-cloak class="report-settings-main-card">
                <livewire:admin-pages.report-settings.user-accounts />
            </div>


            <!-- LEDGERS -->
            <div x-show="tab === 'reports'" x-cloak class="report-settings-main-card">
                <livewire:admin-pages.report-settings.reports />
            </div>

            <!-- PANGKABUHAYAN FUND -->
            @if (in_array(auth()->user()->RoleID, [1, 2, 5, 6, 7]))
                <div x-show="tab === 'pangkabuhayan'" x-cloak class="report-settings-main-card">
                    <livewire:components.spin-fund-report />
                </div>
            @endif

        </div>


        <!-- =========================================
             MOBILE BOTTOM NAVIGATION
        ========================================== -->

        <template x-teleport="body">

            <nav class="report-settings-mobile-nav">

                <!-- OVERVIEW -->
                <a href="#" @click.prevent="tab = 'overview'" :class="tab === 'overview' ? 'active' : ''"
                    aria-label="Overview">
                    <span class="report-settings-mobile-icon">
                        ⌂
                    </span>

                    <span class="report-settings-mobile-label">
                        Overview
                    </span>
                </a>


                <!-- GENERAL -->
                @if (auth()->user()->RoleID == 1)
                    <a href="#" @click.prevent="tab = 'general'" :class="tab === 'general' ? 'active' : ''"
                        aria-label="General">
                        <span class="report-settings-mobile-icon">
                            ⚙
                        </span>

                        <span class="report-settings-mobile-label">
                            General
                        </span>
                    </a>
                @endif


                <!-- USERS -->
                <a href="#" @click.prevent="tab = 'users'" :class="tab === 'users' ? 'active' : ''"
                    aria-label="Users Reports">
                    <span class="report-settings-mobile-icon">
                        ♟
                    </span>

                    <span class="report-settings-mobile-label">
                        Users
                    </span>
                </a>


                <!-- LEDGERS -->
                <a href="#" @click.prevent="tab = 'reports'" :class="tab === 'reports' ? 'active' : ''"
                    aria-label="Ledgers">
                    <span class="report-settings-mobile-icon">
                        ▤
                    </span>

                    <span class="report-settings-mobile-label">
                        Ledgers
                    </span>
                </a>


                <!-- PANGKABUHAYAN -->
                @if (in_array(auth()->user()->RoleID, [1, 2]))
                    <a href="#" @click.prevent="tab = 'pangkabuhayan'"
                        :class="tab === 'pangkabuhayan' ? 'active' : ''" aria-label="Pangkabuhayan">
                        <span class="report-settings-mobile-icon">
                            🎡
                        </span>

                        <span class="report-settings-mobile-label">
                            Pangkabuhayan
                        </span>
                    </a>
                @endif

            </nav>

        </template>

    </div>

@endsection
