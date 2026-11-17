@extends('layouts.member')

@section('title', 'Publishing Studio')

@section('content')

<div class="ws-page" x-data="{ tab: $persist('overview').as('post-tab') }">

    <header class="ws-head">
        <div>
            <span class="ws-head-kicker">Content · Publishing</span>
            <h1>Publishing Studio</h1>
            <p>Shape the public face of TRIFECTA — announcements, stories and the words members read first.</p>
        </div>

        <div class="ws-head-side">
            <span class="ws-head-pill"><strong>3</strong> publishing areas</span>
        </div>
    </header>

    <div class="ws-grid">

        <aside class="ws-rail">

            <div class="ws-rail-group">
                <div class="ws-rail-label">Studio</div>
                <a href="#" class="ws-rail-link" :class="tab === 'overview' ? 'is-active' : ''"
                   @click.prevent="tab = 'overview'">
                    <span class="ws-rail-icon">⌂</span>
                    <span>Overview</span>
                </a>
            </div>

            <div class="ws-rail-group">
                <div class="ws-rail-label">Publishing</div>
                <a href="#" class="ws-rail-link" :class="tab === 'news' ? 'is-active' : ''"
                   @click.prevent="tab = 'news'">
                    <span class="ws-rail-icon">▣</span>
                    <span>Announcements</span>
                </a>

                <a href="#" class="ws-rail-link" :class="tab === 'testimonials' ? 'is-active' : ''"
                   @click.prevent="tab = 'testimonials'">
                    <span class="ws-rail-icon">♡</span>
                    <span>Testimonials</span>
                </a>
            </div>

        </aside>

        <section class="ws-panel">

            <div x-show="tab === 'overview'" x-cloak class="post-main-card">
                <livewire:admin-pages.post.overview lazy />
            </div>

            <div x-show="tab === 'news'" x-cloak class="post-main-card">
                <livewire:admin-pages.post.create-news-updates lazy />
            </div>

            <div x-show="tab === 'testimonials'" x-cloak class="post-main-card">
                <livewire:admin-pages.post.manage-testimonials lazy />
            </div>

        </section>

    </div>

</div>

@endsection
