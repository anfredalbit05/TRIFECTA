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

    <!-- PROFILE NAVIGATION MENU WITH ACTIVATE BUTTON ON FAR RIGHT -->
    <div class="dashboard-nav-menu flex flex-wrap items-center justify-between">

        <div class="flex items-center space-x-2">
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

              @if(auth()->user()->RoleID != 1)
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
            @endif
        </div>

            <!-- Replace your old button block with this: -->
        <livewire:member-pages.my-profile.activation-button />

    </div>

    <!-- PAGE CONTENT -->
    <div class="dashboard-layout px-10 pt-4">

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

         @if(auth()->user()->RoleID != 1)
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
        @endif

    </div>
<!-- ACTIVATION MODAL -->
<livewire:member-pages.my-profile.activation-modal />
</div>



@endsection
