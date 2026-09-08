@extends('layouts.member')

@section('title', 'Masterlist')

@section('content')
<div class="masterlist-container" x-data="{ tab: $persist('overview').as('masterlist-tab') }">

    <!-- MASTERLIST HEADER -->
    <nav class="masterlist-header">
        <div class="masterlist-header-title">
            <h1>Member Masterlist</h1>
        </div>
    </nav>

    <!-- DESKTOP / MOBILE CONTENT -->
    <div class="masterlist-layout">

        <!-- DESKTOP TAB NAVIGATION -->
        <div class="masterlist-nav-menu masterlist-desktop-nav">
            <a href="#" @click.prevent="tab = 'overview'" :class="tab === 'overview' ? 'active' : ''">
                <span class="masterlist-tab-icon">⌂</span>
                <span class="masterlist-tab-label">Overview</span>
            </a>

            <a href="#" @click.prevent="tab = 'admin-users'" :class="tab === 'admin-users' ? 'active' : ''">
                <span class="masterlist-tab-icon">♙</span>
                <span class="masterlist-tab-label">Admin Users</span>
            </a>

            <a href="#" @click.prevent="tab = 'members'" :class="tab === 'members' ? 'active' : ''">
                <span class="masterlist-tab-icon">♙</span>
                <span class="masterlist-tab-label">Members</span>
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

        <!-- TAB CONTENT -->
        <div x-show="tab === 'overview'" class="masterlist-main-card">
            <livewire:admin-pages.masterlist.dashboard />
        </div>

        <div x-show="tab === 'admin-users'" x-cloak class="masterlist-main-card">
            <livewire:admin-pages.masterlist.admin-users />
        </div>

        <div x-show="tab === 'members'" x-cloak class="masterlist-main-card">
            <livewire:members />
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

    <!-- MOBILE BOTTOM NAVIGATION (TELEPORTED TO BODY) -->
    <template x-teleport="body">
        <nav class="masterlist-mobile-nav">
            <a href="#" @click.prevent="tab = 'overview'" :class="tab === 'overview' ? 'active' : ''" aria-label="Overview">
                <span class="masterlist-mobile-icon">⌂</span>
                <span class="masterlist-mobile-label">Overview</span>
            </a>

            <a href="#" @click.prevent="tab = 'admin-users'" :class="tab === 'admin-users' ? 'active' : ''" aria-label="Admin Users">
                <span class="masterlist-mobile-icon">♙</span>
                <span class="masterlist-mobile-label">Admin Users</span>
            </a>

            <a href="#" @click.prevent="tab = 'members'" :class="tab === 'members' ? 'active' : ''" aria-label="Members">
                <span class="masterlist-mobile-icon">♙</span>
                <span class="masterlist-mobile-label">Members</span>
            </a>

            <a href="#" @click.prevent="tab = 'branches'" :class="tab === 'branches' ? 'active' : ''" aria-label="Branches">
                <span class="masterlist-mobile-icon">⌖</span>
                <span class="masterlist-mobile-label">Branches</span>
            </a>

            <a href="#" @click.prevent="tab = 'benefits'" :class="tab === 'benefits' ? 'active' : ''" aria-label="Benefits">
                <span class="masterlist-mobile-icon">♡</span>
                <span class="masterlist-mobile-label">Benefits</span>
            </a>

            <a href="#" @click.prevent="tab = 'faqs'" :class="tab === 'faqs' ? 'active' : ''" aria-label="FAQs">
                <span class="masterlist-mobile-icon">?</span>
                <span class="masterlist-mobile-label">FAQs</span>
            </a>

            <a href="#" @click.prevent="tab = 'inquiries'" :class="tab === 'inquiries' ? 'active' : ''" aria-label="Inquiries">
                <span class="masterlist-mobile-icon">☷</span>
                <span class="masterlist-mobile-label">Inquiries</span>
            </a>
        </nav>
    </template>

    <!-- MODALS -->
    <livewire:admin-pages.masterlist.activation-review-modal />
    <livewire:view-member-modal />

</div>
@endsection
