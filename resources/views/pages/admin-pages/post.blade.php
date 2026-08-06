@extends('layouts.member')

@section('title', 'Post')

@section('content')

<div class="dashboard-container"  x-data="{ tab: $persist('oveview').as('overview-tab') }">

    <!-- POST TOP NAVIGATION -->
    <nav class="dashboard-nav">
        <div class="dashboard-nav-title">
            <h1>Post Management</h1>
        </div>
    </nav>

    <!-- CONTENT WRAPPER (Includes both Nav Menu and Cards to keep identical layout width) -->
    <div class="dashboard-layout px-12 pt-4 flex flex-col gap-4">

        <!-- NAVIGATION MENU -->
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
                @click.prevent="tab = 'news'"
                :class="tab === 'news' ? 'active' : ''"
            >
                News & Update
            </a>

            <a
                href="#"
                @click.prevent="tab = 'testimonials'"
                :class="tab === 'testimonials' ? 'active' : ''"
            >
                Testimonials
            </a>
        </div>

        <!-- OVERVIEW -->
     <!-- OVERVIEW -->
        <div
            x-show="tab === 'overview'"
            class="dashboard-main-card full-width-card"
        >
           <livewire:admin-pages.post.overview />
        </div>

        <!-- NEWS -->
        <div
            x-show="tab === 'news'"
            x-cloak
            class="dashboard-main-card full-width-card"
        >
            <livewire:admin-pages.post.create-news-updates />
        </div>

        <!-- TESTIMONIALS -->
        <div
            x-show="tab === 'testimonials'"
            x-cloak
            class="dashboard-main-card full-width-card"
        >
            <livewire:admin-pages.post.manage-testimonials />
        </div>

    </div>

</div>

@endsection
