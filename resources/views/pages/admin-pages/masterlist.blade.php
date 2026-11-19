@extends('layouts.member')

@section('title', 'Member Registry')

@push('styles')
    @vite('resources/css/admin/print/masterlist.css')
@endpush

@section('content')

@php
    /* Unread inquiries badge on the Inquiries tab (refreshed by the tab via
       the inquiries-updated browser event after an inquiry is opened). */
    $unreadInquiriesCount = \App\Models\Inquiry::whereNull('date_read')->count();
@endphp

<div class="ws-page"
     x-data="{
         tab: $persist('overview').as('masterlist-tab'),
         unreadInquiries: {{ $unreadInquiriesCount }},
     }"
     x-init="window.addEventListener('inquiries-updated', (e) => {
         unreadInquiries = e.detail ? (e.detail.count ?? unreadInquiries) : unreadInquiries;
     });">

    <header class="ws-head">
        <div>
            <span class="ws-head-kicker">People · Records</span>
            <h1>Member Registry</h1>
            <p>Every member, satellite, branch and membership record in one reviewed workspace.</p>
        </div>

        <div class="ws-head-side">
            <span class="ws-head-pill">
                <strong x-text="unreadInquiries">{{ $unreadInquiriesCount }}</strong> unread inquiries
            </span>
        </div>
    </header>

    <div class="ws-grid">

        <aside class="ws-rail">

            <div class="ws-rail-group">
                <div class="ws-rail-label">Snapshot</div>
                <a href="#" class="ws-rail-link" :class="tab === 'overview' ? 'is-active' : ''"
                   @click.prevent="tab = 'overview'">
                    <span class="ws-rail-icon">⌂</span>
                    <span>Overview</span>
                </a>
            </div>

            <div class="ws-rail-group">
                <div class="ws-rail-label">People</div>

                <a href="#" class="ws-rail-link" :class="tab === 'members' ? 'is-active' : ''"
                   @click.prevent="tab = 'members'">
                    <span class="ws-rail-icon">♙</span>
                    <span>Members</span>
                </a>

                {{-- <a href="#" class="ws-rail-link" :class="tab === 'satellite-members' ? 'is-active' : ''"
                   @click.prevent="tab = 'satellite-members'">
                    <span class="ws-rail-icon">◉</span>
                    <span>Satellite Members</span>
                </a> --}}

                <a href="#" class="ws-rail-link" :class="tab === 'admin-users' ? 'is-active' : ''"
                   @click.prevent="tab = 'admin-users'">
                    <span class="ws-rail-icon">⚙</span>
                    <span>Admin Users</span>
                </a>

                @if(auth()->user()->RoleID == 1)
                    <a href="#" class="ws-rail-link" :class="tab === 'roles' ? 'is-active' : ''"
                       @click.prevent="tab = 'roles'">
                        <span class="ws-rail-icon">♜</span>
                        <span>Roles</span>
                    </a>
                @endif
            </div>

            <div class="ws-rail-group">
                <div class="ws-rail-label">Operations</div>

                <a href="#" class="ws-rail-link" :class="tab === 'benefits' ? 'is-active' : ''"
                   @click.prevent="tab = 'benefits'">
                    <span class="ws-rail-icon">♡</span>
                    <span>Benefits</span>
                </a>

                <a href="#" class="ws-rail-link" :class="tab === 'faqs' ? 'is-active' : ''"
                   @click.prevent="tab = 'faqs'">
                    <span class="ws-rail-icon">?</span>
                    <span>FAQs</span>
                </a>

                <a href="#" class="ws-rail-link" :class="tab === 'inquiries' ? 'is-active' : ''"
                   @click.prevent="tab = 'inquiries'">
                    <span class="ws-rail-icon">☷</span>
                    <span>Inquiries</span>
                    <span class="ws-rail-badge" x-show="unreadInquiries > 0"
                          x-text="unreadInquiries" x-cloak></span>
                </a>
            </div>

        </aside>

        <section class="ws-panel">

            <template x-if="tab === 'overview'">
                <div class="masterlist-main-card">
                    <livewire:admin-pages.masterlist.dashboard lazy />
                </div>
            </template>

            <template x-if="tab === 'roles'">
                <div class="masterlist-main-card">
                    <livewire:admin-pages.masterlist.roles lazy />
                </div>
            </template>

            <template x-if="tab === 'admin-users'">
                <div class="masterlist-main-card">
                    <livewire:admin-pages.masterlist.admin-users lazy />
                </div>
            </template>

            <template x-if="tab === 'members'">
                <div class="masterlist-main-card">
                    <livewire:members lazy />
                </div>
            </template>

            {{-- <template x-if="tab === 'satellite-members'">
                <div class="masterlist-main-card">
                    <livewire:admin-pages.masterlist.satellite-members lazy />
                </div>
            </template> --}}

            <template x-if="tab === 'benefits'">
                <div class="masterlist-main-card">
                    <livewire:admin-pages.masterlist.benefits-list lazy />
                </div>
            </template>

            <template x-if="tab === 'inquiries'">
                <div class="masterlist-main-card">
                    <livewire:admin-pages.masterlist.inquiries lazy />
                </div>
            </template>

            <template x-if="tab === 'faqs'">
                <div class="masterlist-main-card">
                    <livewire:admin-pages.masterlist.faq-list lazy />
                </div>
            </template>

        </section>

    </div>

    {{-- Loaded here (page level) because a lazy component's module script does
         not execute when Livewire morphs the component in. --}}
    @vite('resources/js/livewire/admin-pages/masterlist/dashboard.js')

    <livewire:admin-pages.masterlist.activation-review-modal />
    <livewire:view-member-modal />
    <livewire:member-pages.my-profile.activation-modal />
    <livewire:admin-pages.masterlist.members-components.satellite-modal />

</div>
@endsection
