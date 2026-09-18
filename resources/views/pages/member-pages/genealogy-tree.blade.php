@extends('layouts.member')

@section('title', 'Genealogy')

@section('content')

<div class="genealogy-container"
     x-data="{ tab: $persist('invitee').as('genealogy-tree-tab') }">

    <nav class="genealogy-header">
        <div class="genealogy-header-title">
            <h1>Genealogy</h1>
        </div>
    </nav>

    <main class="genealogy-layout">

        <div class="genealogy-desktop-nav">

            <a href="#"
               @click.prevent="tab='invitee'"
               :class="{active:tab==='invitee'}">
                <span class="genealogy-tab-icon">♧</span>
                <span>Invitee</span>
            </a>

            <a href="#"
               @click.prevent="tab='board-member'"
               :class="{active:tab==='board-member'}">
                <span class="genealogy-tab-icon">♙</span>
                <span>Board Member</span>
            </a>

        </div>

        <div x-show="tab==='invitee'"
             x-cloak
             x-transition.opacity.duration.200ms
             class="genealogy-main-card">

            <div class="genealogy-scroll-area">
                <livewire:member-pages.genealogy.genealogy-tree />
            </div>

        </div>

        <div x-show="tab==='board-member'"
             x-cloak
             x-transition.opacity.duration.200ms
             class="genealogy-main-card">

            <div class="genealogy-scroll-area">
                @include('livewire.member-pages.genealogy.board-members')
            </div>

        </div>

    </main>

</div>

@endsection
