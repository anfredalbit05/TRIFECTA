@extends('layouts.member')

@section('title', 'Masterlist')

@section('content')
<div class="masterlist-container" x-data="{ tab: $persist('overview').as('masterlist-tab') }">

    <nav class="masterlist-header">
        <div class="masterlist-header-title">
            <h1>Member Masterlist</h1>
        </div>
    </nav>

    <div class="masterlist-layout">

        <div class="masterlist-nav-menu masterlist-desktop-nav">

            <a href="#" @click.prevent="tab = 'overview'" :class="tab === 'overview' ? 'active' : ''">
                <span class="masterlist-tab-icon">⌂</span>
                <span class="masterlist-tab-label">Overview</span>
            </a>

            @if(auth()->user()->RoleID == 1)
            <a href="#" @click.prevent="tab = 'roles'" :class="tab === 'roles' ? 'active' : ''">
                <span class="masterlist-tab-icon">♙</span>
                <span class="masterlist-tab-label">Roles</span>
            </a>
            @endif

            <a href="#" @click.prevent="tab = 'admin-users'" :class="tab === 'admin-users' ? 'active' : ''">
                <span class="masterlist-tab-icon">♙</span>
                <span class="masterlist-tab-label">Admin Users</span>
            </a>

            <a href="#" @click.prevent="tab = 'members'" :class="tab === 'members' ? 'active' : ''">
                <span class="masterlist-tab-icon">♙</span>
                <span class="masterlist-tab-label">Members</span>
            </a>

            <a href="#" @click.prevent="tab = 'satellite-members'" :class="tab === 'satellite-members' ? 'active' : ''">
                <span class="masterlist-tab-icon">◉</span>
                <span class="masterlist-tab-label">Satellite Members</span>
            </a>

            <a href="#" @click.prevent="tab = 'branches'" :class="tab === 'branches' ? 'active' : ''">
                <span class="masterlist-tab-icon">⌖</span>
                <span class="masterlist-tab-label">Branches</span>
            </a>

            <a href="#" @click.prevent="tab = 'benefits'" :class="tab === 'benefits' ? 'active' : ''">
                <span class="masterlist-tab-icon">♡</span>
                <span class="masterlist-tab-label">Benefits</span>
            </a>

            <a href="#" @click.prevent="tab = 'faqs'" :class="tab === 'faqs' ? 'active' : ''">
                <span class="masterlist-tab-icon">?</span>
                <span class="masterlist-tab-label">FAQs</span>
            </a>

            <a href="#" @click.prevent="tab = 'inquiries'" :class="tab === 'inquiries' ? 'active' : ''">
                <span class="masterlist-tab-icon">☷</span>
                <span class="masterlist-tab-label">Inquiries</span>
            </a>

        </div>

        <div x-show="tab === 'overview'" x-cloak class="masterlist-main-card">
            <livewire:admin-pages.masterlist.dashboard />
        </div>

        <div x-show="tab === 'roles'" x-cloak class="masterlist-main-card">
            <livewire:admin-pages.masterlist.roles />
        </div>

        <div x-show="tab === 'admin-users'" x-cloak class="masterlist-main-card">
            <livewire:admin-pages.masterlist.admin-users />
        </div>

        <div x-show="tab === 'members'" x-cloak class="masterlist-main-card">
            <livewire:members />
        </div>

        <div x-show="tab === 'satellite-members'" x-cloak class="masterlist-main-card">
            <livewire:admin-pages.masterlist.satellite-members />
        </div>

        <div x-show="tab === 'branches'" x-cloak class="masterlist-main-card">
            <livewire:admin-pages.masterlist.branches-list />
        </div>

        <div x-show="tab === 'benefits'" x-cloak class="masterlist-main-card">
            <livewire:admin-pages.masterlist.benefits-list />
        </div>

        <div x-show="tab === 'inquiries'" x-cloak class="masterlist-main-card">
            <livewire:admin-pages.masterlist.inquiries />
        </div>

        <div x-show="tab === 'faqs'" x-cloak class="masterlist-main-card">
            <livewire:admin-pages.masterlist.faq-list />
        </div>

    </div>

    <livewire:admin-pages.masterlist.activation-review-modal />
    <livewire:view-member-modal />
    <livewire:member-pages.my-profile.activation-modal />
    <livewire:admin-pages.masterlist.members-components.satellite-modal />

</div>
@endsection
