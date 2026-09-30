@extends('layouts.member')

@section('title', 'Post')

@section('content')

<div class="post-container"
     x-data="{ tab: $persist('overview').as('post-tab') }">

    <nav class="post-header">
        <div class="post-header-title">
            <h1>Post Management</h1>
        </div>
    </nav>

    <div class="post-layout">

        <div class="post-nav-menu post-desktop-nav">

            <a href="#"
               @click.prevent="tab='overview'"
               :class="{ active: tab==='overview' }">
                <span class="post-tab-icon">⌂</span>
                <span>Overview</span>
            </a>

            @if(\App\Support\Modules::can(\App\Support\Modules::POST_NEWS, 'read'))
            <a href="#"
               @click.prevent="tab='news'"
               :class="{ active: tab==='news' }">
                <span class="post-tab-icon">▣</span>
                <span>News & Update</span>
            </a>
            @endif

            @if(\App\Support\Modules::can(\App\Support\Modules::POST_TESTIMONIALS, 'read'))
            <a href="#"
               @click.prevent="tab='testimonials'"
               :class="{ active: tab==='testimonials' }">
                <span class="post-tab-icon">♡</span>
                <span>Testimonials</span>
            </a>
            @endif

        </div>

        <div x-show="tab==='overview'" x-cloak class="post-main-card">
            <livewire:admin-pages.post.overview />
        </div>

        <div x-show="tab==='news'" x-cloak class="post-main-card">
            <livewire:admin-pages.post.create-news-updates />
        </div>

        <div x-show="tab==='testimonials'" x-cloak class="post-main-card">
            <livewire:admin-pages.post.manage-testimonials />
        </div>

    </div>

</div>

@endsection
