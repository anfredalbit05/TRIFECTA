@extends('layouts.member')

@section('title', 'Post')

@section('content')

<div
    class="post-container"
    x-data="{ tab: $persist('overview').as('post-tab') }"
>

    <!-- =========================================
         POST HEADER
    ========================================== -->

    <nav class="post-header">

        <div class="post-header-title">
            <h1>Post Management</h1>
        </div>

    </nav>


    <!-- =========================================
         MAIN CONTENT
    ========================================== -->

    <div class="post-layout">

        <!-- =====================================
             DESKTOP TAB NAVIGATION
        ====================================== -->

        <div class="post-nav-menu post-desktop-nav">

            <!-- OVERVIEW -->
            <a
                href="#"
                @click.prevent="tab = 'overview'"
                :class="tab === 'overview' ? 'active' : ''"
            >
                <span class="post-tab-icon">⌂</span>
                <span class="post-tab-label">Overview</span>
            </a>


            <!-- NEWS & UPDATE -->
            <a
                href="#"
                @click.prevent="tab = 'news'"
                :class="tab === 'news' ? 'active' : ''"
            >
                <span class="post-tab-icon">▣</span>
                <span class="post-tab-label">News & Update</span>
            </a>


            <!-- TESTIMONIALS -->
            <a
                href="#"
                @click.prevent="tab = 'testimonials'"
                :class="tab === 'testimonials' ? 'active' : ''"
            >
                <span class="post-tab-icon">♡</span>
                <span class="post-tab-label">Testimonials</span>
            </a>

        </div>


        <!-- =====================================
             TAB CONTENT
        ====================================== -->

        <!-- OVERVIEW -->
        <div
            x-show="tab === 'overview'"
            x-cloak
            class="post-main-card"
        >
            <livewire:admin-pages.post.overview />
        </div>


        <!-- NEWS -->
        <div
            x-show="tab === 'news'"
            x-cloak
            class="post-main-card"
        >
            <livewire:admin-pages.post.create-news-updates />
        </div>


        <!-- TESTIMONIALS -->
        <div
            x-show="tab === 'testimonials'"
            x-cloak
            class="post-main-card"
        >
            <livewire:admin-pages.post.manage-testimonials />
        </div>


    </div>


    <!-- =========================================
         MOBILE BOTTOM NAVIGATION
    ========================================== -->

    <template x-teleport="body">

        <nav class="post-mobile-nav">

            <!-- OVERVIEW -->
            <a
                href="#"
                @click.prevent="tab = 'overview'"
                :class="tab === 'overview' ? 'active' : ''"
                aria-label="Overview"
            >
                <span class="post-mobile-icon">
                    ⌂
                </span>

                <span class="post-mobile-label">
                    Overview
                </span>
            </a>


            <!-- NEWS -->
            <a
                href="#"
                @click.prevent="tab = 'news'"
                :class="tab === 'news' ? 'active' : ''"
                aria-label="News & Update"
            >
                <span class="post-mobile-icon">
                    ▣
                </span>

                <span class="post-mobile-label">
                    News
                </span>
            </a>


            <!-- TESTIMONIALS -->
            <a
                href="#"
                @click.prevent="tab = 'testimonials'"
                :class="tab === 'testimonials' ? 'active' : ''"
                aria-label="Testimonials"
            >
                <span class="post-mobile-icon">
                    ♡
                </span>

                <span class="post-mobile-label">
                    Testimonials
                </span>
            </a>

        </nav>

    </template>

</div>

@endsection
