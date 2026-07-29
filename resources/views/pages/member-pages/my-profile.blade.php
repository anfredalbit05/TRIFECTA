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

@if(!$hasActivation)
<button
    type="button"
    onclick="Livewire.dispatch('open-activation-modal', { UserID: {{ auth()->user()->UserID }} })"
    class="activate-account-btn
           px-5
           py-2
           mb-2
           ml-253
           bg-green-600
           text-white
           rounded-lg
           font-semibold
           hover:bg-green-700
           transition
           duration-300">

    Activate Account

</button>

        @endif


    <!-- PROFILE NAVIGATION MENU -->
    <div class="dashboard-nav-menu">

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

    </div>

</div>

<!-- ACTIVATION MODAL -->
<livewire:member-pages.my-profile.activation-modal />

@endsection
