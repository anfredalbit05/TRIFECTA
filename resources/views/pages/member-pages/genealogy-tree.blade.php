@extends('layouts.member')

@section('title', 'Genealogy Network')

@push('styles')
    @vite('resources/css/member/print/genealogy.css')
@endpush

@push('scripts')
    {{-- Loaded with the page: Livewire-rendered markup never executes injected scripts --}}
    @vite([
        'resources/js/livewire/member-pages/genealogy/board-members.js',
        'resources/js/livewire/member-pages/genealogy/invitee.js',
    ])
@endpush

@section('content')

<div class="ws-page" x-data="{ tab: $persist('invitee').as('genealogy-tree-tab') }">

    <header class="ws-head">
        <div>
            <span class="ws-head-kicker">Community · Network</span>
            <h1>Genealogy Network</h1>
            <p>See how your community grows — your invitees, your leaders and the branches they belong to.</p>
        </div>

        <div class="ws-head-side">
            <span class="ws-head-pill">Live network view</span>
        </div>
    </header>

    <div class="ws-grid">

        <aside class="ws-rail">

            <div class="ws-rail-group">
                <div class="ws-rail-label">Network</div>



                <a href="#" class="ws-rail-link" :class="tab === 'invitee' ? 'is-active' : ''"
                   @click.prevent="tab = 'invitee'">
                    <span class="ws-rail-icon">♧</span>
                    <span>Invitee</span>
                </a>

                @if (auth()->check() && (int) auth()->user()->RoleID != 8)
                    {{-- Overview is a leadership tool — not shown to plain Members --}}
                    <a href="#" class="ws-rail-link" :class="tab === 'overview' ? 'is-active' : ''"
                       @click.prevent="tab = 'overview'">
                        <span class="ws-rail-icon">♢</span>
                        <span>Overview</span>
                    </a>
                @endif

                {{-- <a href="#" class="ws-rail-link" :class="tab === 'board-member' ? 'is-active' : ''"
                   @click.prevent="tab = 'board-member'">
                    <span class="ws-rail-icon">♙</span>
                    <span>Board Member</span>
                </a> --}}
            </div>

        </aside>

        <section class="ws-panel">



            <div x-show="tab === 'invitee'"
                 x-cloak
                 x-transition.opacity.duration.200ms
                 class="genealogy-main-card">

                <div class="genealogy-scroll-area">
                    <livewire:member-pages.genealogy.genealogy-tree lazy />
                </div>

            </div>

              @if (auth()->check() && (int) auth()->user()->RoleID != 8)
                <div x-show="tab === 'overview'"
                     x-cloak
                     x-transition.opacity.duration.200ms
                     class="genealogy-main-card">

                    <div class="genealogy-scroll-area">
                        <livewire:member-pages.genealogy.overview lazy />
                    </div>

                </div>
            @endif

            {{-- <div x-show="tab === 'board-member'"
                 x-cloak
                 x-transition.opacity.duration.200ms
                 class="genealogy-main-card">

                <div class="genealogy-scroll-area">
                    @include('livewire.member-pages.genealogy.board-members')
                </div>

            </div> --}}

        </section>

    </div>

</div>

@endsection
