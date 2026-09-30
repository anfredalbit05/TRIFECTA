@extends('layouts.member')

@section('title', 'Masterlist')

@section('content')
@php
    /* Tab visibility follows the global module system: a tab appears only
       when the viewer holds can_read on its module (see App\Support\Modules). */
    $moduleTabIds = [
        'admin-users' => \App\Support\Modules::ADMIN_USERS,
        'members' => \App\Support\Modules::MEMBERS,
        'satellite-members' => \App\Support\Modules::SATELLITE_MEMBERS,
        'branches' => \App\Support\Modules::BRANCHES,
        'benefits' => \App\Support\Modules::BENEFITS,
        'faqs' => \App\Support\Modules::FAQS,
        'inquiries' => \App\Support\Modules::INQUIRIES,
    ];
    $allowedTabs = ['overview', ...collect($moduleTabIds)
        ->filter(fn ($moduleId) => \App\Support\Modules::can($moduleId, 'read'))
        ->keys()
        ->all()];
@endphp
<div class="masterlist-container"
     x-data="{ tab: $persist('overview').as('masterlist-tab'), allowedTabs: {{ json_encode($allowedTabs) }} }"
     x-init="if (! allowedTabs.includes(tab)) tab = 'overview'">

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

            @if(in_array('admin-users', $allowedTabs))
            <a href="#" @click.prevent="tab = 'admin-users'" :class="tab === 'admin-users' ? 'active' : ''">
                <span class="masterlist-tab-icon">♙</span>
                <span class="masterlist-tab-label">Admin Users</span>
            </a>
            @endif

            @if(in_array('members', $allowedTabs))
            <a href="#" @click.prevent="tab = 'members'" :class="tab === 'members' ? 'active' : ''">
                <span class="masterlist-tab-icon">♙</span>
                <span class="masterlist-tab-label">Members</span>
            </a>
            @endif

            @if(in_array('satellite-members', $allowedTabs))
            <a href="#" @click.prevent="tab = 'satellite-members'" :class="tab === 'satellite-members' ? 'active' : ''">
                <span class="masterlist-tab-icon">◉</span>
                <span class="masterlist-tab-label">Satellite Members</span>
            </a>
            @endif

            @if(in_array('branches', $allowedTabs))
            <a href="#" @click.prevent="tab = 'branches'" :class="tab === 'branches' ? 'active' : ''">
                <span class="masterlist-tab-icon">⌖</span>
                <span class="masterlist-tab-label">Branches</span>
            </a>
            @endif

            @if(in_array('benefits', $allowedTabs))
            <a href="#" @click.prevent="tab = 'benefits'" :class="tab === 'benefits' ? 'active' : ''">
                <span class="masterlist-tab-icon">♡</span>
                <span class="masterlist-tab-label">Benefits</span>
            </a>
            @endif

            @if(in_array('faqs', $allowedTabs))
            <a href="#" @click.prevent="tab = 'faqs'" :class="tab === 'faqs' ? 'active' : ''">
                <span class="masterlist-tab-icon">?</span>
                <span class="masterlist-tab-label">FAQs</span>
            </a>
            @endif

            @if(in_array('inquiries', $allowedTabs))
            <a href="#" @click.prevent="tab = 'inquiries'" :class="tab === 'inquiries' ? 'active' : ''">
                <span class="masterlist-tab-icon">☷</span>
                <span class="masterlist-tab-label">Inquiries</span>
            </a>
            @endif

        </div>

        <div x-show="tab === 'overview'" x-cloak class="masterlist-main-card">
            <livewire:admin-pages.masterlist.dashboard />
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
