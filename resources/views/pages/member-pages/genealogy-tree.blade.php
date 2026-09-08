@extends('layouts.member')

@section('title', 'Genealogy')

@section('content')
<div class="genealogy-container" x-data="{ tab: $persist('invitee').as('genealogy-tree-tab') }">

    <!-- GENEALOGY HEADER -->
    <nav class="genealogy-header">
        <div class="genealogy-header-title">
            <h1>Genealogy</h1>
        </div>
    </nav>

    <!-- PAGE CONTENT -->
    <div class="genealogy-layout">

        <!-- DESKTOP TAB NAVIGATION -->
        <div class="genealogy-desktop-nav">
            <a href="#" @click.prevent="tab = 'invitee'" :class="tab === 'invitee' ? 'active' : ''">
                <span class="genealogy-tab-icon">♧</span>
                <span class="genealogy-tab-label">Invitee</span>
            </a>

            <a href="#" @click.prevent="tab = 'board-member'" :class="tab === 'board-member' ? 'active' : ''">
                <span class="genealogy-tab-icon">♙</span>
                <span class="genealogy-tab-label">Board Member</span>
            </a>
        </div>

        <!-- INVITEE GENEALOGY -->
        <div x-show="tab === 'invitee'" x-cloak x-transition.opacity.duration.200ms class="genealogy-main-card">
            <div class="genealogy-scroll-area">
                <livewire:member-pages.genealogy.genealogy-tree />
            </div>
        </div>

        <!-- BOARD MEMBERS -->
        <div x-show="tab === 'board-member'" x-cloak x-transition.opacity.duration.200ms class="genealogy-main-card">
            <div class="genealogy-scroll-area">
                @include('livewire.member-pages.genealogy.board-members')
            </div>
        </div>

    </div>

    <!-- MOBILE BOTTOM NAVIGATION (teleported to body so position: fixed is reliable) -->
    <template x-teleport="body">
        <nav class="genealogy-mobile-nav">
            <a href="#" @click.prevent="tab = 'invitee'" :class="tab === 'invitee' ? 'active' : ''" aria-label="Invitee">
                <span class="genealogy-mobile-icon">♧</span>
                <span class="genealogy-mobile-label">Invitee</span>
            </a>

            <a href="#" @click.prevent="tab = 'board-member'" :class="tab === 'board-member' ? 'active' : ''" aria-label="Board Member">
                <span class="genealogy-mobile-icon">♙</span>
                <span class="genealogy-mobile-label">Board Member</span>
            </a>
        </nav>
    </template>

</div>
@endsection
