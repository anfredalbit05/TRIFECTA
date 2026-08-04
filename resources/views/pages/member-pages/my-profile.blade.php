@extends('layouts.member')

@section('title', 'My Profile')

@section('content')

<div
    class="dashboard-container"
    x-data="{ tab: 'profile' }"
    @account-activated.window="$wire.$refresh()">

    <!-- MY PROFILE NAVIGATION -->
    <nav class="dashboard-nav">
        <div class="dashboard-nav-title">
            <h1>
                My Profile
            </h1>
        </div>
    </nav>

    <!-- CONTENT WRAPPER (Includes both Nav Menu and Cards to keep identical layout width) -->
    <div class="dashboard-layout px-12 pt-4 flex flex-col gap-4">

        <!-- PROFILE NAVIGATION MENU WITH ACTIVATE BUTTON ON FAR RIGHT -->
        <div class="dashboard-nav-menu flex flex-col md:flex-row md:items-center justify-between gap-4">

            <!-- WRAPPED LINKS CONTAINER -->
            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="#"
                    @click.prevent="tab = 'profile'"
                    :class="tab === 'profile' ? 'active' : ''">
                    Profile
                </a>

                <a
                    href="#"
                    @click.prevent="tab = 'account'"
                    :class="tab === 'account' ? 'active' : ''">
                    Account
                </a>

                <a
                    href="#"
                    @click.prevent="tab = 'security'"
                    :class="tab === 'security' ? 'active' : ''">
                    Security
                </a>

                {{-- @if(auth()->user()->RoleID == 4) --}}
                    <a
                        href="#"
                        @click.prevent="tab = 'activity'"
                        :class="tab === 'activity' ? 'active' : ''">
                        Activity
                    </a>

                    <a
                        href="#"
                        @click.prevent="tab = 'certificate'"
                        :class="tab === 'certificate' ? 'active' : ''">
                        Certificate
                    </a>
                {{-- @endif --}}
            </div>

            <!-- ACTIVATION BUTTON CONTAINER -->
            <div class="flex items-center shrink-0">
                <livewire:member-pages.my-profile.activation-button />
            </div>

        </div>

        <!-- PAGE CONTENT -->
        <!-- PROFILE TAB -->
        <div
            x-show="tab === 'profile'"
            class="dashboard-main-card full-width-card">
            <livewire:member-pages.my-profile.profile />
        </div>

        <!-- ACCOUNT TAB -->
        <div
            x-show="tab === 'account'"
            x-cloak
            class="dashboard-main-card full-width-card">
            <livewire:member-pages.my-profile.account />
        </div>

        <!-- SECURITY TAB -->
        <div
            x-show="tab === 'security'"
            x-cloak
            class="dashboard-main-card full-width-card">
            <livewire:member-pages.my-profile.security />
        </div>

        {{-- @if(auth()->user()->RoleID == 4) --}}
            <!-- ACTIVITY TAB -->
            <div
                x-show="tab === 'activity'"
                x-cloak
                class="dashboard-main-card full-width-card">
                <livewire:member-pages.my-profile.activity />
            </div>

            <!-- CERTIFICATE TAB -->
            <div
                x-show="tab === 'certificate'"
                x-cloak
                class="dashboard-main-card full-width-card">
                <livewire:member-pages.my-profile.certificate />
            </div>
        {{-- @endif --}}

    </div>

    <!-- ACTIVATION MODAL -->
    <livewire:member-pages.my-profile.activation-modal />
</div>

@endsection
