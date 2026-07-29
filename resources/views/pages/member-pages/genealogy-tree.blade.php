@extends('layouts.member')

@section('title', 'Genealogy Tree')

@section('content')

<div class="genealogy-container" x-data="{ tab: 'invitee' }">

    <!-- DASHBOARD TOP NAVIGATION -->
    <nav class="genealogy-nav">
        <div class="genealogy-nav-title">
            <h1>Genealogy</h1>
        </div>
    </nav>

    <!-- NAVIGATION MENU -->
    <div class="genealogy-nav-menu">
        <a href="#"
           @click.prevent="tab = 'invitee'"
           :class="tab === 'invitee' ? 'active' : ''">
            Invitee
        </a>

        <a href="#"
           @click.prevent="tab = 'board-member'"
           :class="tab === 'board-member' ? 'active' : ''">
            Board Member
        </a>
    </div>

    <!-- CONTENT WRAPPER -->
    <div class="genealogy-layout px-10 pt-4">

        <!-- INVITEE TAB -->
        <div x-show="tab === 'invitee'" x-cloak class="tab-card">
            <livewire:member-pages.genealogy.genealogy-tree />
        </div>

        <!-- BOARD MEMBER TAB -->
        <div x-show="tab === 'board-member'" x-cloak class="tab-card">
            @include('livewire.member-pages.genealogy.board-members')
        </div>

    </div>

</div>

@endsection
