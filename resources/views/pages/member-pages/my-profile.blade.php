@extends('layouts.member')

@section('title', 'My Profile')

@section('content')

<div
    class="dashboard-container"
    x-data="{ tab: 'profile' }">

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
        </div>

        <!-- ACTIVATE ACCOUNT BUTTON (Pushed to the far right) -->
        @if(auth()->user()->RoleID != 1)
            <div
                x-data="{ hasActivation: @js($hasActivation) }"
                @account-activated.window="hasActivation = true"
                class="ml-auto my-2 sm:my-0 mr-2 sm:mr-2">

                <template x-if="!hasActivation">
                    <button
                        type="button"
                        onclick="Livewire.dispatch('open-activation-modal')"
                        class="activate-account-btn px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-xs sm:text-sm shadow-md transition duration-300 text-center">
                        Activate Account
                    </button>
                </template>
            </div>
        @endif

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

    </div>

</div>

<!-- ACTIVATION MODAL -->
<livewire:member-pages.my-profile.activation-modal />

@endsection
