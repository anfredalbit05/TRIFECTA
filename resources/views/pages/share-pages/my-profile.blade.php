@extends('layouts.member')

@section('title', 'My Profile')

@section('content')

{{--
=====================================================================
 MY PROFILE — TAB SHELL
=====================================================================
 New utility groups on this page (tab bar layout, link wrapping,
 activation button placement) live in profile.css section 14 as
 .profile-tabs-shell / .profile-tabs-bar / .profile-tabs-links /
 .profile-tabs-activation — applied alongside the existing global
 .dashboard-* classes, not replacing them.
 Sections:
   1. Page navigation header
   2. Tabs bar (tab links + activation button)
   3. Tab panels (Profile / Account / Security / Activity / Certificate)
   4. Activation modal
=====================================================================
--}}

<div
    class="dashboard-container"
    x-data="{ tab: $persist('profile').as('my-profile-tab') }"
    @account-activated.window="$wire.$refresh()">

    {{-- ==================== 1. PAGE NAVIGATION HEADER ==================== --}}
    <nav class="dashboard-nav">
        <div class="dashboard-nav-title">
            <h1>My Profile</h1>
        </div>
    </nav>

    {{-- Content wrapper (keeps nav menu and cards at identical layout width) --}}
    <div class="dashboard-layout profile-tabs-shell">

        {{-- ============ 2. TABS BAR (LINKS + ACTIVATION BUTTON) ============ --}}
        <div class="dashboard-nav-menu profile-tabs-bar">

            <div class="profile-tabs-links">
                <a href="#" @click.prevent="tab = 'profile'" :class="tab === 'profile' ? 'active' : ''">
                    Profile
                </a>

                <a href="#" @click.prevent="tab = 'account'" :class="tab === 'account' ? 'active' : ''">
                    Account
                </a>

                <a href="#" @click.prevent="tab = 'security'" :class="tab === 'security' ? 'active' : ''">
                    Security
                </a>

                {{-- @if(auth()->user()->RoleID == 4) --}}
                    <a href="#" @click.prevent="tab = 'activity'" :class="tab === 'activity' ? 'active' : ''">
                        Activity
                    </a>

                    <a href="#" @click.prevent="tab = 'certificate'" :class="tab === 'certificate' ? 'active' : ''">
                        Certificate
                    </a>
                {{-- @endif --}}
            </div>

            <div class="profile-tabs-activation">
                <livewire:member-pages.my-profile.activation-button />
            </div>

        </div>

        {{-- ==================== 3. TAB PANELS ==================== --}}

        {{-- Profile --}}
        <div x-show="tab === 'profile'" class="dashboard-main-card full-width-card">
            <livewire:member-pages.my-profile.profile />
        </div>

        {{-- Account --}}
        <div x-show="tab === 'account'" x-cloak class="dashboard-main-card full-width-card">
            <livewire:member-pages.my-profile.account />
        </div>

        {{-- Security --}}
        <div x-show="tab === 'security'" x-cloak class="dashboard-main-card full-width-card">
            <livewire:member-pages.my-profile.security />
        </div>

        {{-- @if(auth()->user()->RoleID == 4) --}}
            {{-- Activity --}}
            <div x-show="tab === 'activity'" x-cloak class="dashboard-main-card full-width-card">
                <livewire:member-pages.my-profile.activity />
            </div>

            {{-- Certificate --}}
            <div x-show="tab === 'certificate'" x-cloak class="dashboard-main-card full-width-card">
                <livewire:member-pages.my-profile.certificate />
            </div>
        {{-- @endif --}}

    </div>

    {{-- ==================== 4. ACTIVATION MODAL ==================== --}}
    <livewire:member-pages.my-profile.activation-modal />
</div>

@endsection
