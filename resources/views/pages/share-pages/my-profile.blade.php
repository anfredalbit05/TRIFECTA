@extends('layouts.member')

@section('title', 'My Profile')

@section('content')

<div
    class="my-profile-container"
    x-data="{ tab: $persist('profile').as('my-profile-tab') }"
    @account-activated.window="$wire.$refresh()"
>
    <nav class="my-profile-header">
        <div class="my-profile-header-title">
            <h1>My Profile</h1>
        </div>
    </nav>

    <main class="my-profile-layout">

        <div class="my-profile-nav-menu">
            <div class="my-profile-tabs-links">

                <a href="#" @click.prevent="tab='profile'" :class="{active:tab==='profile'}">
                    <span class="my-profile-tab-icon">♙</span>
                    <span>Profile</span>
                </a>

                <a href="#" @click.prevent="tab='account'" :class="{active:tab==='account'}">
                    <span class="my-profile-tab-icon">◉</span>
                    <span>Account</span>
                </a>

                <a href="#" @click.prevent="tab='security'" :class="{active:tab==='security'}">
                    <span class="my-profile-tab-icon">🛡</span>
                    <span>Security</span>
                </a>

                <a href="#" @click.prevent="tab='activity'" :class="{active:tab==='activity'}">
                    <span class="my-profile-tab-icon">◷</span>
                    <span>Activity</span>
                </a>

                <a href="#" @click.prevent="tab='certificate'" :class="{active:tab==='certificate'}">
                    <span class="my-profile-tab-icon">▣</span>
                    <span>Certificate</span>
                </a>

            </div>

            <div class="my-profile-tabs-activation">
                <livewire:member-pages.my-profile.activation-button />
            </div>
        </div>

        <div x-show="tab==='profile'" x-cloak class="my-profile-main-card">
            <livewire:member-pages.my-profile.profile />
        </div>

        <div x-show="tab==='account'" x-cloak class="my-profile-main-card">
            <livewire:member-pages.my-profile.account />
        </div>

        <div x-show="tab==='security'" x-cloak class="my-profile-main-card">
            <livewire:member-pages.my-profile.security />
        </div>

        <div x-show="tab==='activity'" x-cloak class="my-profile-main-card">
            <livewire:member-pages.my-profile.activity />
        </div>

        <div x-show="tab==='certificate'" x-cloak class="my-profile-main-card">
            <livewire:member-pages.my-profile.certificate />
        </div>

    </main>


    <livewire:member-pages.my-profile.activation-modal />

</div>

@endsection
