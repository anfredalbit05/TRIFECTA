@extends('layouts.member')

@section('title', 'Masterlist')

@section('content')

<div class="dashboard-container"
     x-data="{ tab: $persist('overview').as('masterlist-tab') }">

    <!-- MASTERLIST NAVIGATION -->
    <nav class="dashboard-nav">
        <div class="dashboard-nav-title">
            <h1>Member Masterlist</h1>
        </div>
    </nav>

    <div class="dashboard-nav-menu">
        <a
            href="#"
            @click.prevent="tab = 'overview'"
            :class="tab === 'overview' ? 'active' : ''"
        >
            Overview
        </a>

        <a
            href="#"
            @click.prevent="tab = 'members'"
            :class="tab === 'members' ? 'active' : ''"
        >
            Members
        </a>

        <a
            href="#"
            @click.prevent="tab = 'branches'"
            :class="tab === 'branches' ? 'active' : ''"
        >
            Branches
        </a>

        <a
            href="#"
            @click.prevent="tab = 'benefits'"
            :class="tab === 'benefits' ? 'active' : ''"
        >
            Benefits
        </a>

        <a
            href="#"
            @click.prevent="tab = 'inquiries'"
            :class="tab === 'inquiries' ? 'active' : ''"
        >
            Inquiries
        </a>


        <a
            href="#"
            @click.prevent="tab = 'faqs'"
            :class="tab === 'faqs' ? 'active' : ''"
        >
            FAQs
        </a>

    </div>

    <div class="dashboard-layout px-12 pt-4">

        <!-- OVERVIEW -->
        <div
            x-show="tab === 'overview'"
            class="dashboard-main-card full-width-card"
        >
             <livewire:admin-pages.masterlist.dashboard />
        </div>

        <!-- MEMBERS -->
        <div
            x-show="tab === 'members'"
            x-cloak
            class="dashboard-main-card full-width-card"
        >
            <livewire:members />
        </div>

        <!-- BRANCHES -->
        <div
            x-show="tab === 'branches'"
            x-cloak
            class="dashboard-main-card full-width-card"
        >
             <livewire:admin-pages.masterlist.branches-list />
        </div>

        <!-- BENEFITS -->
        <div
            x-show="tab === 'benefits'"
            x-cloak
            class="dashboard-main-card full-width-card"
        >
            <livewire:admin-pages.masterlist.benefits-list />
        </div>

        <!-- INQUIRIES -->
        <div
            x-show="tab === 'inquiries'"
            x-cloak
            class="dashboard-main-card full-width-card"
        >
             <livewire:admin-pages.masterlist.inquiries />
        </div>

          <!-- FAQs -->
        <div
            x-show="tab === 'faqs'"
            x-cloak
            class="dashboard-main-card full-width-card"
        >
             <livewire:admin-pages.masterlist.faq-list />
        </div>


    </div>

    <livewire:admin-pages.masterlist.activation-review-modal />
    <livewire:view-member-modal />

</div>

@endsection
